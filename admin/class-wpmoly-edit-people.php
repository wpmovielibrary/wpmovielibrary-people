<?php
/**
 * WPMovieLibrary Edit People Class extension.
 *
 * @package   WPMovieLibrary-People
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! class_exists( 'WPMOLY_Edit_People' ) ) :

	class WPMOLY_Edit_People extends WPMovieLibrary_People_Admin {

		/**
		 * People Metadata
		 *
		 * @since    2.1.4
		 * @var      array
		 */
		protected $metadata = array();

		/**
		 * Constructor
		 *
		 * @since    1.0
		 */
		public function __construct() {

			if ( ! is_admin() )
				return false;

			$this->init();

			$this->register_hook_callbacks();
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {

			global $wpmolyp;

			$this->wpmolyp  = $wpmolyp;
			$this->metadata = $wpmolyp->metadata;
			$this->api      = new WPMOLYP_TMDb();
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_action( 'admin_enqueue_scripts', array( $this, 'pre_admin_enqueue_scripts' ), 9 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 15 );

			// Bulk/quick edit
			add_filter( 'bulk_post_updated_messages', array( $this, 'person_bulk_updated_messages' ), 10, 2 );

			// Metabox
			add_filter( 'wpmoly_filter_metaboxes', array( $this, 'add_meta_box' ), 10 );

			// Post edit
			add_filter( 'post_updated_messages', array( $this, 'person_updated_messages' ), 10, 1 );
			add_action( 'save_post_person', array( $this, 'save_person' ), 10, 3 );

			add_action( 'admin_footer', __CLASS__ . '::footer_scripts' );
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                        Scripts & Styles
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Enqueue required media scripts and styles
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $hook_suffix The current admin page.
		 */
		public function pre_admin_enqueue_scripts( $hook_suffix ) {

			if ( ( 'post.php' != $hook_suffix && 'post-new.php' != $hook_suffix ) || 'person' != get_post_type() )
				return false;

			wp_enqueue_media();
			wp_enqueue_script( 'media' );

			wp_register_script( 'select2-sortable-js', ReduxFramework::$_url . 'assets/js/vendor/select2.sortable.min.js', array( 'jquery' ), WPMOLY_VERSION, true );
			wp_register_script( 'select2-js', ReduxFramework::$_url . 'assets/js/vendor/select2/select2.min.js', array( 'jquery', 'select2-sortable-js' ), WPMOLY_VERSION, true );
			wp_enqueue_script( 'field-select-js', ReduxFramework::$_url . 'inc/fields/select/field_select.min.js', array( 'jquery', 'select2-js' ), WPMOLY_VERSION, true );
			wp_enqueue_style( 'select2-css', ReduxFramework::$_url . 'assets/js/vendor/select2/select2.css', array(), WPMOLY_VERSION, 'all' );
			wp_enqueue_style( 'redux-field-select-css', ReduxFramework::$_url . 'inc/fields/select/field_select.css', WPMOLY_VERSION, true );
		}

		/**
		 * Enqueue required media scripts and styles
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $hook_suffix The current admin page.
		 */
		public function admin_enqueue_scripts( $hook_suffix ) {

			if ( ( 'post.php' != $hook_suffix && 'post-new.php' != $hook_suffix ) || 'person' != get_post_type() )
				return false;

			wp_enqueue_script( WPMOLYP_SLUG . '-people-editor-models-js', WPMOLYP_URL . '/assets/js/admin/wpmoly-people-editor-models.js', array( 'jquery' ), WPMOLYP_VERSION, true );
			wp_enqueue_script( WPMOLYP_SLUG . '-people-editor-views-js', WPMOLYP_URL . '/assets/js/admin/wpmoly-people-editor-views.js', array( 'jquery' ), WPMOLYP_VERSION, true );
		}

		/**
		 * Add Backbone Templates to footer.
		 * 
		 * @since    1.0
		 */
		public static function footer_scripts() {

?>
		<script type="text/template" id="wpmoly-filmography-cast-template">
								<% _.each( movies, function( movie ) { %>
									<div>
										<input type="hidden" name="wpmoly[credits][cast][<%= movie.id %>][tmdb_id]" value="<%= movie.id %>" /><span><%= movie.id %></span> - 
										<input type="hidden" name="wpmoly[credits][cast][<%= movie.id %>][title]" value="<%= movie.title %>" /><span><%= movie.title %></span> - 
										<input type="hidden" name="wpmoly[credits][cast][<%= movie.id %>][original_title]" value="<%= movie.original_title %>" /><span><%= movie.original_title %></span> - 
										<input type="hidden" name="wpmoly[credits][cast][<%= movie.id %>][character]" value="<%= movie.character %>" /><span><%= movie.character %></span> - 
										<input type="hidden" name="wpmoly[credits][cast][<%= movie.id %>][job]" value="<%= movie.job %>" /><span><%= movie.job %></span> - 
										<input type="hidden" name="wpmoly[credits][cast][<%= movie.id %>][poster_path]" value="<%= movie.poster_path %>" /><span><%= movie.poster_path %></span> - 
										<input type="hidden" name="wpmoly[credits][cast][<%= movie.id %>][release_date]" value="<%= movie.release_date %>" /><span><%= movie.release_date %></span>
									</div>

								<% }); %>
		</script>
<?php
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                          Callbacks
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                       Updated Messages
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Add message support for movies in Post Editor.
		 * 
		 * @since    2.1.4
		 * 
		 * @param    array    $messages Default Post update messages
		 * 
		 * @return   array    Updated Post update messages
		 */
		public function person_updated_messages( $messages ) {

			global $post;
			$post_ID = $post->ID;

			$new_messages = array(
				'person' => array(
					1  => sprintf( __( 'Person updated. <a href="%s">View person</a>', 'wpmovielibrary-people' ), esc_url( get_permalink( $post_ID ) ) ),
					2  => __( 'Custom field updated.', 'wpmovielibrary-people' ) ,
					3  => __( 'Custom field deleted.', 'wpmovielibrary-people' ),
					4  => __( 'Person updated.', 'wpmovielibrary-people' ),
					5  => isset( $_GET['revision'] ) ? sprintf( __( 'Person restored to revision from %s', 'wpmovielibrary-people' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
					6  => sprintf( __( 'Person published. <a href="%s">View person</a>', 'wpmovielibrary-people' ), esc_url( get_permalink( $post_ID ) ) ),
					7  => __( 'Person saved.' ),
					8  => sprintf( __( 'Person submitted. <a target="_blank" href="%s">Preview person</a>', 'wpmovielibrary-people' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
					9  => sprintf( __( 'Person scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview people</a>', 'wpmovielibrary-people' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
					10 => sprintf( __( 'Person draft updated. <a target="_blank" href="%s">Preview person</a>', 'wpmovielibrary-people' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
				)
			);

			$messages = array_merge( $messages, $new_messages );

			return $messages;
		}

		/**
		 * Add message support for person in Post Editor bulk edit.
		 * 
		 * @since    2.1.4
		 * 
		 * @param    array    $messages Default Post bulk edit messages
		 * 
		 * @return   array    Updated Post bulk edit messages
		 */
		public function person_bulk_updated_messages( $bulk_messages, $bulk_counts ) {

			$new_messages = array(
				'person' => array(
					'updated'   => _n( '%s person updated.', '%s people updated.', $bulk_counts['updated'], 'wpmovielibrary-people' ),
					'locked'    => _n( '%s person not updated, somebody is editing it.', '%s people not updated, somebody is editing them.', $bulk_counts['locked'], 'wpmovielibrary-people' ),
					'deleted'   => _n( '%s person permanently deleted.', '%s people permanently deleted.', $bulk_counts['deleted'], 'wpmovielibrary-people' ),
					'trashed'   => _n( '%s person moved to the Trash.', '%s people moved to the Trash.', $bulk_counts['trashed'], 'wpmovielibrary-people' ),
					'untrashed' => _n( '%s person restored from the Trash.', '%s people restored from the Trash.', $bulk_counts['untrashed'], 'wpmovielibrary-people' ),
				)
			);

			$messages = array_merge( $bulk_messages, $new_messages );

			return $messages;
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                     "All Movies" WP List Table
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		


		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Metabox
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Register Metabox to WPMovieLibrary
		 * 
		 * @since    1.0
		 * 
		 * @param    object    $post Current Post object
		 * @param    array     $args Metabox parameters
		 */
		public function add_meta_box( $metaboxes ) {

			$new_metaboxes = array(
				'person' => array(
					'wpmoly-people' => array(
						'title'         => __( 'WordPress Movie Library', 'wpmovielibrary' ),
						'callback'      => 'WPMOLY_Edit_People::metabox',
						'screen'        => 'person',
						'context'       => 'normal',
						'priority'      => 'high',
						'callback_args' => array(
							'panels' => array(
								'preview' => array(
									'title'    => __( 'Preview', 'wpmovielibrary' ),
									'icon'     => 'wpmolicon icon-actor-alt',
									'callback' => 'WPMOLY_Edit_People::render_preview_panel'
								),

								'meta' => array(
									'title'    => __( 'Metadata', 'wpmovielibrary' ),
									'icon'     => 'wpmolicon icon-meta',
									'callback' => 'WPMOLY_Edit_People::render_meta_panel'
								),

								'filmography' => array(
									'title'    => __( 'Filmography', 'wpmovielibrary' ),
									'icon'     => 'wpmolicon icon-list',
									'callback' => 'WPMOLY_Edit_People::render_filmography_panel'
								),

								'images' => array(
									'title'    => __( 'Portrait', 'wpmovielibrary' ),
									'icon'     => 'wpmolicon icon-hat',
									'callback' => 'WPMOLY_Edit_People::render_images_panel'
								),

								'photos' => array(
									'title'    => __( 'Images', 'wpmovielibrary' ),
									'icon'     => 'wpmolicon icon-images-alt-2',
									'callback' => 'WPMOLY_Edit_People::render_images_panel'
								)
							)
						)
					)
				)
			);

			$metaboxes = array_merge( $metaboxes, $new_metaboxes );

			return $metaboxes;
		}

		/**
		 * Movie Metabox content callback.
		 * 
		 * @since    1.0
		 * 
		 * @param    object    $post Current Post object
		 * @param    array     $args Metabox parameters
		 */
		public static function metabox( $post, $args = array() ) {

			$defaults = array(
				'panels' => array()
			);
			$args = wp_parse_args( $args['args'], $defaults );

			$tabs   = array();
			$panels = array();

			foreach ( $args['panels'] as $id => $panel ) {

				if ( ! is_callable( $panel['callback'] ) )
					continue;

				$is_active = ( ( 'preview' == $id && ! $empty ) || ( 'meta' == $id && $empty ) );
				//$is_active = ( 'meta' == $id );
				$tabs[ $id ] = array(
					'title'  => $panel['title'],
					'icon'   => $panel['icon'],
					'active' => $is_active ? ' active' : ''
				);
				$panels[ $id ] = array( 
					'active'  => $is_active ? ' active' : '',
					'content' => call_user_func_array( $panel['callback'], array( $post->ID ) )
				);
			}

			$attributes = array(
				'tabs'   => $tabs,
				'panels' => $panels
			);

			echo self::render_admin_template( 'metabox/metabox.php', $attributes );
		}

		/**
		 * Movie Metabox Preview Panel.
		 * 
		 * Display a Metabox panel to preview metadata.
		 * 
		 * @since    2.0
		 * 
		 * @param    int    Current Post ID
		 * 
		 * @return   string    Panel HTML Markup
		 */
		private static function render_preview_panel( $post_id ) {

			// TODO: filter default thumbnail
			$thumbnail = get_the_post_thumbnail( $post_id, 'medium' );
			if ( '' == $thumbnail )
				$thumbnail = '<img src="https://image.tmdb.org/t/p/w185/jdRmHrG0TWXGhs4tO6TJNSoL25T.jpg" alt="" />';
				//$thumbnail = '<img src="' . WPMOLYP_URL . '/assets/img/no-profile.jpg" alt="" />';

			$preview = array(
				'name'       => wpmoly_get_person_meta( $post_id, 'name' ),
				'age'        => '45',
				'jobs'       => 'Actor, Producer',
				'birthplace' => 'Uvalde - Texas - USA',
				'biography'  => 'Matthew David McConaughey (born November 4, 1969) is an American actor. After a series of minor roles in the early 1990s, McConaughey gained notice for his breakout role in Dazed and Confused (1993). It was in this role that he first conceived the idea of his catch-phrase "Well alright, alright." He then appeared in films such as A Time to Kill, Contact, U-571, Tiptoes, Sahara, and We Are Marshall. McConaughey is best known more recently for his performances as a leading man in the romantic comedies The Wedding Planner, How to Lose a Guy in 10 Days, Failure to Launch, Ghosts of Girlfriends Past and Fool\'s Gold.<br />Description above from the Wikipedia article Matthew McConaughey, licensed under CC-BY-SA, full list of contributors on Wikipedia.'
			);

			$attributes = compact( 'thumbnail', 'preview' );
			
			$panel = self::render_admin_template( 'metabox/panels/panel-preview.php', $attributes );

			return $panel;
		}

		/**
		 * Movie Metabox Meta Panel.
		 * 
		 * Display a Metabox panel to download movie metadata.
		 * 
		 * @since    2.0
		 * 
		 * @param    int    Current Post ID
		 * 
		 * @return   string    Panel HTML Markup
		 */
		private static function render_meta_panel( $post_id ) {

			global $wpmolyp;

			$metas     = $wpmolyp->metadata;
			$languages = WPMOLY_Settings::get_supported_languages();
			$metadata  = wpmoly_get_person_meta( $post_id, 'meta' );

			$attributes = array(
				'languages' => $languages,
				'metas'     => $metas,
				'metadata'  => $metadata
			);

			$panel = self::render_admin_template( 'metabox/panels/panel-meta.php', $attributes );

			return $panel;
		}

		/**
		 * People Metabox Filmography Panel.
		 * 
		 * @since    1.0
		 * 
		 * @param    int    Current Post ID
		 * 
		 * @return   string    Panel HTML Markup
		 */
		private static function render_filmography_panel( $post_id ) {

			$attributes = array();

			$panel = self::render_admin_template( 'metabox/panels/panel-filmography.php', $attributes );

			return $panel;
		}

		/**
		 * Movie Images Metabox Panel.
		 * 
		 * Display a Metabox panel to download movie images.
		 * 
		 * @since    2.0
		 * 
		 * @param    int    Current Post ID
		 * 
		 * @return   string    Panel HTML Markup
		 */
		private static function render_images_panel( $post_id ) {

			$attributes = array(
				'nonce'   => wpmoly_nonce_field( 'upload-movie-image', $referer = false ),
				'images'  => array()
			);

			$panel = self::render_admin_template( 'metabox/panels/panel-images.php', $attributes  );

			return $panel;
		}

		/**
		 * Movie Posters Metabox Panel.
		 * 
		 * Display a Metabox panel to download movie posters.
		 * 
		 * @since    2.0
		 * 
		 * @param    int    Current Post ID
		 * 
		 * @return   string    Panel HTML Markup
		 */
		private static function render_posters_panel( $post_id ) {

			$attributes = array(
				'posters' => array()
			);

			$panel = self::render_admin_template( 'metabox/panels/panel-posters.php', $attributes  );

			return $panel;
		}


		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Save data
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Save person metadata.
		 * 
		 * @since    1.3
		 * 
		 * @param    int      $post_id ID of the current Post
		 * @param    array    $meta Person meta
		 * 
		 * @return   int|object    WP_Error object is anything went wrong, true else
		 */
		protected function save_person_meta( $post_id, $meta, $clean = true ) {

			$post = get_post( $post_id );
			if ( ! $post || 'person' != get_post_type( $post ) )
				return new WP_Error( 'invalid_post', __( 'Error: submitted post is not a person.', 'wpmovielibrary-people' ) );

			$meta = $this->validate_meta( $meta );
			foreach ( $meta as $slug => $meta )
				$update = update_post_meta( $post_id, "_wpmoly_person_{$slug}", $meta );

			if ( false !== $clean )
				WPMOLY_Cache::clean_transient( 'clean', $force = true );

			return $post_id;
		}

		protected function save_person_filmography( $post_id, $credits ) {

			$post = get_post( $post_id );
			if ( ! $post || 'person' != get_post_type( $post ) )
				return new WP_Error( 'invalid_post', __( 'Error: submitted post is not a person.', 'wpmovielibrary-people' ) );

			$credits = $this->validate_credits( $credits );
			print_r( $credits ); die();
		}

		/**
		 * Filter the Person Metadata submitted when saving a post to
		 * avoid storing unexpected data to the database.
		 * 
		 * @since    1.0
		 * 
		 * @param    array    $data The Person Metadata to filter
		 * 
		 * @return   array    The filtered Metadata
		 */
		private function validate_meta( $data ) {

			$valid = array_keys( $this->metadata );

			foreach ( $data as $slug => $meta ) {
				if ( in_array( $slug, $valid ) ) {
					$filter = ( isset( $this->metadata[ $slug ]['filter'] ) && function_exists( $this->metadata[ $slug ]['filter'] ) ? $this->metadata[ $slug ]['filter'] : 'esc_html' );
					$args   = ( isset( $this->metadata[ $slug ]['filter_args'] ) && ! is_null( $this->metadata[ $slug ]['filter_args'] ) ? $this->metadata[ $slug ]['filter_args'] : null );
					$data[ $slug ] = call_user_func( $filter, $meta, $args );
				}
			}

			return $data;
		}

		private function validate_credits( $credits ) {

			$defaults = array(
				'cast' => array(),
				'crew' => array()
			);
			$credits = wp_parse_args( $credits, $defaults );

			$default_cast = array_flip( array( 'adult', 'character', 'credit_id', 'id', 'original_title', 'poster_path', 'release_date', 'title' ) );
			$default_crew = array_flip( array( 'adult', 'credit_id', 'department', 'id', 'job', 'original_title', 'poster_path', 'release_date', 'title' ) );

			if ( isset( $credits['cast'] ) && ! empty( $credits['cast'] ) ) {
				foreach ( $credits['cast'] as $i => $credit ) {
					$_credit = array_intersect_key( $credit, $default_cast );
					$credits['cast'][ $i ] = array_map( 'esc_html', $_credit );
				}
			}

			if ( isset( $credits['crew'] ) && ! empty( $credits['crew'] ) ) {
				foreach ( $credits['crew'] as $i => $credit ) {
					$_credit = array_intersect_key( $credit, $default_crew );
					$credits['crew'][ $i ] = array_map( 'esc_html', $_credit );
				}
			}

			return $credits;
		}

		/**
		 * Remove person meta.
		 * 
		 * @since    1.2
		 * 
		 * @param    int      $post_id ID of the current Post
		 * 
		 * @return   boolean  Always return true
		 */
		public static function empty_person_meta( $post_id ) {

			delete_post_meta( $post_id, '_wpmoly_person_data' );

			return true;
		}

		/**
		 * Save TMDb fetched data.
		 *
		 * @since    1.0
		 * 
		 * @param    int        $post_id ID of the current Post
		 * @param    object     $post Post Object of the current Post
		 * 
		 * @return   int|WP_Error
		 */
		public function save_person( $post_id, $post ) {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return new WP_Error( __( 'You are not allowed to edit posts.', 'wpmovielibrary-people' ) );

			if ( ! $post = get_post( $post_id ) || 'people' != get_post_type( $post ) )
				return new WP_Error( sprintf( __( 'Posts with #%s is invalid or is not a person.', 'wpmovielibrary-people' ), $post_id ) );

			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
				return $post_id;

			print_r( $_POST['wpmoly'] ); die();

			if ( isset( $_POST['wpmoly']['credits'] ) ) {
				$credits = $_POST['wpmoly']['credits'];
				$this->save_person_filmography( $post_id, $credits );
			}

			if ( isset( $_POST['wpmoly']['meta'] ) ) {
				$meta = $_POST['wpmoly']['meta'];
				$this->save_person_meta( $post_id, $meta );
			}

			return $post_id;
		}

		/**
		 * Prepares sites to use the plugin during single or network-wide activation
		 *
		 * @since    1.0
		 *
		 * @param    bool    $network_wide
		 */
		public function activate( $network_wide ) {}

		/**
		 * Rolls back activation procedures when de-activating the plugin
		 *
		 * @since    1.0
		 */
		public function deactivate() {}
		
	}
	
endif;