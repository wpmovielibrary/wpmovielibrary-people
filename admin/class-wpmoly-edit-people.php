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

	class WPMOLY_Edit_People extends WPMOLYP_Module {

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

			$this->metadata = $wpmolyp->metadata;
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 9 );

			// Bulk/quick edit
			add_filter( 'bulk_post_updated_messages', __CLASS__ . '::people_bulk_updated_messages', 10, 2 );

			/*add_action( 'quick_edit_custom_box', __CLASS__ . '::quick_edit_movies', 10, 2 );
			add_action( 'bulk_edit_custom_box', __CLASS__ . '::bulk_edit_movies', 10, 2 );
			add_filter( 'post_row_actions', __CLASS__ . '::expand_quick_edit_link', 10, 2 );

			// Post List Table
			add_filter( 'manage_movie_posts_columns', __CLASS__ . '::movies_columns_head' );
			add_action( 'manage_movie_posts_custom_column', __CLASS__ . '::movies_columns_content', 10, 2 );
			add_filter( 'manage_edit-movie_sortable_columns', __CLASS__ . '::movies_sortable_columns', 10, 1 );
			add_action( 'pre_get_posts', __CLASS__ . '::movies_sortable_columns_order', 10, 1 );

			// Media
			add_action( 'the_posts', __CLASS__ . '::the_posts_hijack', 10, 2 );
			add_action( 'ajax_query_attachments_args', __CLASS__ . '::load_images_dummy_query_args', 10, 1 );*/

			// Metabox
			add_filter( 'wpmoly_filter_metaboxes', array( $this, 'add_meta_box' ), 10 );

			/*if ( 1 == wpmoly_o( 'convert-enable' ) ) {
				add_action( 'admin_footer-edit.php', __CLASS__ . '::bulk_admin_footer', 10 );
				add_action( 'load-post.php', __CLASS__ . '::convert_post_type', 10 );
				add_action( 'load-edit.php', __CLASS__ . '::bulk_convert_post_type', 10 );
				add_action( 'add_meta_boxes', __CLASS__ . '::add_meta_box', 10 );
			}*/

			// Post edit
			add_filter( 'post_updated_messages', __CLASS__ . '::people_updated_messages', 10, 1 );
			/*add_action( 'save_post_movie', __CLASS__ . '::save_movie', 10, 4 );
			add_action( 'wp_insert_post_empty_content', __CLASS__ . '::filter_empty_content', 10, 2 );
			add_action( 'wp_insert_post_data', __CLASS__ . '::filter_empty_title', 10, 2 );

			// Callbacks
			add_action( 'wp_ajax_wpmoly_save_meta', __CLASS__ . '::save_meta_callback' );
			add_action( 'wp_ajax_wpmoly_empty_meta', __CLASS__ . '::empty_meta_callback' );*/
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                        Scripts & Styles
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Enqueue required media scripts and styles
		 * 
		 * @since    2.0
		 * 
		 * @param    string    $hook_suffix The current admin page.
		 */
		public function admin_enqueue_scripts( $hook_suffix ) {

			if ( ( 'post.php' != $hook_suffix && 'post-new.php' != $hook_suffix ) || 'people' != get_post_type() )
				return false;

			wp_enqueue_media();
			wp_enqueue_script( 'media' );

			wp_register_script( 'select2-sortable-js', ReduxFramework::$_url . 'assets/js/vendor/select2.sortable.min.js', array( 'jquery' ), WPMOLY_VERSION, true );
			wp_register_script( 'select2-js', ReduxFramework::$_url . 'assets/js/vendor/select2/select2.min.js', array( 'jquery', 'select2-sortable-js' ), WPMOLY_VERSION, true );
			wp_enqueue_script( 'field-select-js', ReduxFramework::$_url . 'inc/fields/select/field_select.min.js', array( 'jquery', 'select2-js' ), WPMOLY_VERSION, true );
			wp_enqueue_style( 'select2-css', ReduxFramework::$_url . 'assets/js/vendor/select2/select2.css', array(), WPMOLY_VERSION, 'all' );
			wp_enqueue_style( 'redux-field-select-css', ReduxFramework::$_url . 'inc/fields/select/field_select.css', WPMOLY_VERSION, true );
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
		public static function people_updated_messages( $messages ) {

			global $post;
			$post_ID = $post->ID;

			$new_messages = array(
				'people' => array(
					1  => sprintf( __( 'People updated. <a href="%s">View people</a>', 'wpmovielibrary-people' ), esc_url( get_permalink( $post_ID ) ) ),
					2  => __( 'Custom field updated.', 'wpmovielibrary-people' ) ,
					3  => __( 'Custom field deleted.', 'wpmovielibrary-people' ),
					4  => __( 'People updated.', 'wpmovielibrary-people' ),
					5  => isset( $_GET['revision'] ) ? sprintf( __( 'People restored to revision from %s', 'wpmovielibrary-people' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
					6  => sprintf( __( 'People published. <a href="%s">View people</a>', 'wpmovielibrary-people' ), esc_url( get_permalink( $post_ID ) ) ),
					7  => __( 'People saved.' ),
					8  => sprintf( __( 'People submitted. <a target="_blank" href="%s">Preview people</a>', 'wpmovielibrary-people' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
					9  => sprintf( __( 'People scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview people</a>', 'wpmovielibrary-people' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
					10 => sprintf( __( 'People draft updated. <a target="_blank" href="%s">Preview people</a>', 'wpmovielibrary-people' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
					11 => __( 'Successfully converted to people.', 'wpmovielibrary-people' )
				)
			);

			$messages = array_merge( $messages, $new_messages );

			return $messages;
		}

		/**
		 * Add message support for people in Post Editor bulk edit.
		 * 
		 * @since    2.1.4
		 * 
		 * @param    array    $messages Default Post bulk edit messages
		 * 
		 * @return   array    Updated Post bulk edit messages
		 */
		public static function people_bulk_updated_messages( $bulk_messages, $bulk_counts ) {

			$new_messages = array(
				'people' => array(
					'updated'   => _n( '%s people updated.', '%s people updated.', $bulk_counts['updated'], 'wpmovielibrary-people' ),
					'locked'    => _n( '%s people not updated, somebody is editing it.', '%s people not updated, somebody is editing them.', $bulk_counts['locked'], 'wpmovielibrary-people' ),
					'deleted'   => _n( '%s people permanently deleted.', '%s people permanently deleted.', $bulk_counts['deleted'], 'wpmovielibrary-people' ),
					'trashed'   => _n( '%s people moved to the Trash.', '%s people moved to the Trash.', $bulk_counts['trashed'], 'wpmovielibrary-people' ),
					'untrashed' => _n( '%s people restored from the Trash.', '%s people restored from the Trash.', $bulk_counts['untrashed'], 'wpmovielibrary-people' ),
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

		/**
		 * Add a custom column to Movies WP_List_Table list.
		 * Insert a simple 'Poster' column to Movies list table to display
		 * movies' poster set as featured image if available.
		 * 
		 * @since    1.0
		 * 
		 * @param    array    $defaults Default WP_List_Table header columns
		 * 
		 * @return   array    Default columns with new poster column
		 */
		public static function movies_columns_head( $defaults ) {

			$title = array_search( 'title', array_keys( $defaults ) );
			$comments = array_search( 'comments', array_keys( $defaults ) ) - 1;

			$defaults = array_merge(
				array_slice( $defaults, 0, $title, true ),
				array( 'wpmoly-poster' => __( 'Poster', 'wpmovielibrary-people' ) ),
				array_slice( $defaults, $title, $comments, true ),
				array( 'wpmoly-release_date' => sprintf( '<span class="wpmolicon icon-date" title="%s"></span>', __( 'Year', 'wpmovielibrary-people' ) ) ),
				array( 'wpmoly-status'       => sprintf( '<span class="wpmolicon icon-status" title="%s"></span>', __( 'Status', 'wpmovielibrary-people' ) ) ),
				array( 'wpmoly-media'        => sprintf( '<span class="wpmolicon icon-video" title="%s"></span>', __( 'Media', 'wpmovielibrary-people' ) ) ),
				array( 'wpmoly-rating'       => __( 'Rating', 'wpmovielibrary-people' ) ),
				array_slice( $defaults, $comments, count( $defaults ), true )
			);

			unset( $defaults['author'] );
			return $defaults;
		}

		/**
		 * Add a custom column to Movies WP_List_Table list.
		 * Insert movies' poster set as featured image if available.
		 * 
		 * @since    1.0
		 * 
		 * @param    string   $column_name The column name
		 * @param    int      $post_id current movie's post ID
		 */
		public static function movies_columns_content( $column_name, $post_id ) {

			/*$_column_name = str_replace( 'wpmoly-', '', $column_name );
			switch ( $column_name ) {
				case 'wpmoly-poster':
					$html = get_the_post_thumbnail( $post_id, 'thumbnail' );
					break;
				case 'wpmoly-release_date':
					$meta = wpmoly_get_movie_meta( $post_id, 'release_date' );
					$html = apply_filters( 'wpmoly_format_movie_release_date', $meta, 'Y' );
					break;
				case 'wpmoly-status':
					$meta = call_user_func_array( 'wpmoly_get_movie_meta', array( 'post_id' => $post_id, 'meta' => $_column_name ) );
					$html = apply_filters( 'wpmoly_format_movie_status', $meta, $format = 'html', $icon = true );
					break;
				case 'wpmoly-media':
					$meta = call_user_func_array( 'wpmoly_get_movie_meta', array( 'post_id' => $post_id, 'meta' => $_column_name ) );
					$html = apply_filters( 'wpmoly_format_movie_media', $meta, $format = 'html', $icon = true );
					break;
				case 'wpmoly-rating':
					$meta = wpmoly_get_movie_rating( $post_id );
					$html = apply_filters( 'wpmoly_movie_rating_stars', $meta, $post_id, $base = 5 );
					break;
				default:
					$html = '';
					break;
			}

			echo $html;*/
		}

		/**
		 * Add a custom column to Movies WP_List_Table list.
		 * Insert movies' poster set as featured image if available.
		 * 
		 * @since    2.0
		 * 
		 * @param    array    $column_name The column name
		 * 
		 * @return   array    $columns Updated the column name
		 */
		public static function movies_sortable_columns( $columns ) {

			$columns['wpmoly-release_date'] = 'wpmoly-release_date';
			$columns['wpmoly-status']       = 'wpmoly-status';
			$columns['wpmoly-media']        = 'wpmoly-media';
			$columns['wpmoly-rating']       = 'wpmoly-rating';

			return $columns;
		}

		/**
		 * 
		 * 
		 * @since    2.0
		 * 
		 * @param    object    $wp_query Current WP_Query instance
		 */
		public static function movies_sortable_columns_order( $wp_query ) {

			if ( ! is_admin() )
			    return false;

			/*$orderby = $wp_query->get( 'orderby' );
			$allowed = array( 'wpmoly-release_date', 'wpmoly-release_date', 'wpmoly-status', 'wpmoly-media', 'wpmoly-rating' );
			if ( in_array( $orderby, $allowed ) ) {
				$key = str_replace( 'wpmoly-', '_wpmoly_movie_', $orderby );
				$wp_query->set( 'meta_key', $key );
				$wp_query->set( 'orderby', 'meta_value_num' );
			}*/
		}


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
				'people' => array(
					'wpmoly-people' => array(
						'title'         => __( 'WordPress Movie Library', 'wpmovielibrary' ),
						'callback'      => 'WPMOLY_Edit_People::metabox',
						'screen'        => 'people',
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

								'details' => array(
									'title'    => __( 'Details', 'wpmovielibrary' ),
									'icon'     => 'wpmolicon icon-details',
									'callback' => 'WPMOLY_Edit_People::render_details_panel'
								),

								'images' => array(
									'title'    => __( 'Images', 'wpmovielibrary' ),
									'icon'     => 'wpmolicon icon-images-alt',
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

			$rating   = wpmoly_get_movie_rating( $post_id );
			$metadata = wpmoly_get_movie_meta( $post_id );
			$metadata = wpmoly_filter_empty_array( $metadata );

			$preview  = array();
			$empty    = (bool) ( isset( $metadata['_empty'] ) && 1 == $metadata['_empty'] );

			if ( $empty )
				$preview = array(
					'title'          => '<span class="lipsum">Lorem ipsum dolor</span>',
					'original_title' => '<span class="lipsum">Lorem ipsum dolor sit amet</span>',
					'genres'         => '<span class="lipsum">Lorem, ipsum, dolor, sit, amet</span>',
					'release_date'   => '<span class="lipsum">2014</span>',
					'rating'         => '<span class="lipsum">0-0</span>',
					'overview'       => '<span class="lipsum">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut mattis fermentum eros, et rhoncus enim cursus vitae. Nullam interdum mi feugiat, tempor turpis ac, viverra lorem. Nunc placerat sapien ut vehicula iaculis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lacinia augue pharetra orci porta, nec posuere lectus accumsan. Mauris porttitor posuere lacus, sit amet auctor nibh congue eu.</span>',
					'director'       => '<span class="lipsum">Lorem ipsum</span>',
					'cast'           => '<span class="lipsum">Lorem, ipsum, dolor, sit, amet, consectetur, adipiscing, elit, mattis, fermentum, eros, rhoncus, cursus, vitae</span>',
					
				);
			else
				foreach ( $metadata as $slug => $meta )
					$preview[ $slug ] = call_user_func( 'apply_filters', "wpmoly_format_movie_{$slug}", $meta );

			$attributes = array(
				'empty'     => $empty,
				'thumbnail' => get_the_post_thumbnail( $post->ID, 'medium' ),
				'rating'    => apply_filters( 'wpmoly_movie_rating_stars', $rating, $post->ID, $base = 5 ),
				'preview'   => $preview
			);

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
			$metadata  = wpmoly_get_movie_meta( $post_id );
			$metadata  = wpmoly_filter_empty_array( $metadata );

			$attributes = array(
				'languages' => $languages,
				'metas'     => $metas,
				'metadata'  => $metadata
			);

			$panel = self::render_admin_template( 'metabox/panels/panel-meta.php', $attributes );

			return $panel;
		}

		/**
		 * Movie Metabox Details Panel.
		 * 
		 * Display a Metabox panel to edit movie details.
		 * 
		 * @since    2.0
		 * 
		 * @param    int    Current Post ID
		 * 
		 * @return   string    Panel HTML Markup
		 */
		private static function render_details_panel( $post_id ) {

			$details = WPMOLY_Settings::get_supported_movie_details();
			$class   = new ReduxFramework();

			foreach ( $details as $slug => $detail ) {

				if ( 'custom' == $detail['panel'] ) {
					unset( $details[ $slug ] );
					continue;
				}

				$field_name = $detail['type'];
				$class_name = "ReduxFramework_{$field_name}";
				$value      = call_user_func_array( 'wpmoly_get_movie_meta', array( 'post_id' => $post_id, 'meta' => $slug ) );

				if ( ! class_exists( $class_name ) )
					require_once WPMOLY_PATH . "includes/framework/redux/ReduxCore/inc/fields/{$field_name}/field_{$field_name}.php";

				$field = new $class_name( $detail, $value, $class );

				ob_start();
				$field->render();
				$html = ob_get_contents();
				ob_end_clean();

				$details[ $slug ]['html'] = $html;
			}

			$attributes = array( 'details' => $details );

			$panel = self::render_admin_template( 'metabox/panels/panel-details.php', $attributes );

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

			global $wp_version;

			$attributes = array(
				'nonce'   => wpmoly_nonce_field( 'upload-movie-image', $referer = false ),
				'images'  => WPMOLY_Media::get_movie_imported_images(),
				'version' => ( version_compare( $wp_version, '4.0', '>=' ) ? 4 : 0 )
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

			global $wp_version;

			$attributes = array(
				'posters' => WPMOLY_Media::get_movie_imported_posters(),
				'version' => ( version_compare( $wp_version, '4.0', '>=' ) ? 4 : 0 )
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
		 * Save movie details.
		 * 
		 * @since    1.0
		 * 
		 * @param    int      $post_id ID of the current Post
		 * @param    array    $details Movie details: media, status, rating
		 * 
		 * @return   int|object    WP_Error object is anything went
		 *                                  wrong, true else
		 */
		public static function save_movie_details( $post_id, $details ) {

			$post = get_post( $post_id );
			if ( ! $post || 'movie' != get_post_type( $post ) )
				return new WP_Error( 'invalid_post', __( 'Error: submitted post is not a movie.', 'wpmovielibrary-people' ) );

			$details    = self::validate_details( $details );
			$supported  = WPMOLY_Settings::get_supported_movie_details();

			if ( ! is_array( $details ) )
				return new WP_Error( 'invalid_details', __( 'Error: the submitted movie details are invalid.', 'wpmovielibrary-people' ) );

			foreach ( $details as $slug => $detail )
				update_post_meta( $post_id, "_wpmoly_movie_{$slug}", $detail );

			WPMOLY_Cache::clean_transient( 'clean', $force = true );

			return $post_id;
		}

		/**
		 * Save movie metadata.
		 * 
		 * @since    1.3
		 * 
		 * @param    int      $post_id ID of the current Post
		 * @param    array    $details Movie details: media, status, rating
		 * 
		 * @return   int|object    WP_Error object is anything went wrong, true else
		 */
		public static function save_movie_meta( $post_id, $movie_meta, $clean = true ) {

			$post = get_post( $post_id );
			if ( ! $post || 'movie' != get_post_type( $post ) )
				return new WP_Error( 'invalid_post', __( 'Error: submitted post is not a movie.', 'wpmovielibrary-people' ) );

			$movie_meta = self::validate_meta( $movie_meta );
			unset( $movie_meta['post_id'] );

			foreach ( $movie_meta as $slug => $meta )
				$update = update_post_meta( $post_id, "_wpmoly_movie_{$slug}", $meta );

			if ( false !== $clean )
				WPMOLY_Cache::clean_transient( 'clean', $force = true );

			return $post_id;
		}

		/**
		 * Filter the Movie Metadata submitted when saving a post to
		 * avoid storing unexpected data to the database.
		 * 
		 * The Metabox array makes a distinction between pure metadata
		 * and crew data, so we filter them separately. If the data slug
		 * is valid, the value is escaped and added to the return array.
		 * 
		 * @since    1.0
		 * 
		 * @param    array    $data The Movie Metadata to filter
		 * 
		 * @return   array    The filtered Metadata
		 */
		private static function validate_meta( $data ) {

			if ( ! is_array( $data ) || empty( $data ) || ! isset( $data['tmdb_id'] ) )
				return $data;

			$data = wpmoly_filter_empty_array( $data );
			$data = wpmoly_filter_undimension_array( $data );

			$supported = WPMOLY_Settings::get_supported_movie_meta();
			$keys = array_keys( $supported );
			$movie_tmdb_id = esc_attr( $data['tmdb_id'] );
			$movie_post_id = ( isset( $data['post_id'] ) && '' != $data['post_id'] ? esc_attr( $data['post_id'] ) : null );
			$movie_poster = ( isset( $data['poster'] ) && '' != $data['poster'] ? esc_attr( $data['poster'] ) : null );
			$movie_meta = array();

			foreach ( $data as $slug => $_meta ) {
				if ( in_array( $slug, $keys ) ) {
					$filter = ( isset( $supported[ $slug ]['filter'] ) && function_exists( $supported[ $slug ]['filter'] ) ? $supported[ $slug ]['filter'] : 'esc_html' );
					$args   = ( isset( $supported[ $slug ]['filter_args'] ) && ! is_null( $supported[ $slug ]['filter_args'] ) ? $supported[ $slug ]['filter_args'] : null );
					$movie_meta[ $slug ] = call_user_func( $filter, $_meta, $args );
				}
			}

			$_data = array_merge(
				array(
					'tmdb_id' => $movie_tmdb_id,
					'post_id' => $movie_post_id,
					'poster'  => $movie_poster
				),
				$movie_meta
			);

			return $_data;
		}

		/**
		 * Filter the Movie Details submitted when saving a post to
		 * avoid storing unexpected data to the database.
		 * 
		 * @since    2.1
		 * 
		 * @param    array    $data The Movie Details to filter
		 * 
		 * @return   array    The filtered Details
		 */
		private static function validate_details( $data ) {

			if ( ! is_array( $data ) || empty( $data ) )
				return $data;

			$data = wpmoly_filter_empty_array( $data );

			$supported = WPMOLY_Settings::get_supported_movie_details();
			$movie_details = array();

			foreach ( $supported as $slug => $detail ) {

				if ( isset( $data[ $slug ] ) ) {

					$_detail = $data[ $slug ];
					if ( is_array( $_detail ) && 1 == $detail['multi'] ) {

						$_d = array();
						foreach ( $_detail as $d )
							if ( in_array( $d, array_keys( $detail['options'] ) ) )
								$_d[] = $d;

						$movie_details[ $slug ] = $_d;
					}
					else if ( in_array( $_detail, array_keys( $detail['options'] ) ) ) {
						$movie_details[ $slug ] = $_detail;
					}
				}
				else {
					$movie_details[ $slug ] = null;
				}
			}

			return $movie_details;
		}

		/**
		 * Remove movie meta and taxonomies.
		 * 
		 * @since    1.2
		 * 
		 * @param    int      $post_id ID of the current Post
		 * 
		 * @return   boolean  Always return true
		 */
		public static function empty_movie_meta( $post_id ) {

			wp_delete_object_term_relationships( $post_id, array( 'collection', 'genre', 'actor' ) );
			delete_post_meta( $post_id, '_wpmoly_movie_data' );

			return true;
		}

		/**
		 * Save TMDb fetched data.
		 * 
		 * Uses the 'save_post_movie' action hook to save the movie metadata
		 * as a postmeta. This method is used in regular post creation as
		 * well as in movie import. If no $movie_meta is passed, we're 
		 * most likely creating a new movie, use $_REQUEST to get the data.
		 * 
		 * Saves the movie details as well.
		 *
		 * @since    1.0
		 * 
		 * @param    int        $post_ID ID of the current Post
		 * @param    object     $post Post Object of the current Post
		 * @param    boolean    $queue Queued movie?
		 * @param    array      $movie_meta Movie Metadata to save with the post
		 * 
		 * @return   int|WP_Error
		 */
		public static function save_movie( $post_ID, $post, $queue = false, $movie_meta = null ) {

			if ( ! current_user_can( 'edit_post', $post_ID ) )
				return new WP_Error( __( 'You are not allowed to edit posts.', 'wpmovielibrary-people' ) );

			if ( ! $post = get_post( $post_ID ) || 'movie' != get_post_type( $post ) )
				return new WP_Error( sprintf( __( 'Posts with #%s is invalid or is not a movie.', 'wpmovielibrary-people' ), $post_ID ) );

			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
				return $post_ID;

			$errors = new WP_Error();

			if ( ! is_null( $movie_meta ) && count( $movie_meta ) ) {

				// Save TMDb data
				self::save_movie_meta( $post_ID, $movie_meta );

				// Set poster as featured image
				if ( wpmoly_o( 'poster-featured' ) && ! $queue ) {
					$upload = WPMOLY_Media::set_image_as_featured( $movie_meta['poster'], $post_ID, $movie_meta['tmdb_id'], $movie_meta['title'] );
					if ( is_wp_error( $upload ) )
						$errors->add( $upload->get_error_code(), $upload->get_error_message() );
					else
						update_post_meta( $post_ID, '_thumbnail_id', $upload );
				}

				// Switch status from import draft to published
				if ( 'import-draft' == get_post_status( $post_ID ) && ! $queue ) {
					$update = wp_update_post(
						array(
							'ID' => $post_ID,
							'post_name'   => sanitize_title_with_dashes( $movie_meta['title'] ),
							'post_status' => 'publish',
							'post_title'  => $movie_meta['title'],
							'post_date'   => current_time( 'mysql' )
						),
						$wp_error = true
					);
					if ( is_wp_error( $update ) )
						$errors->add( $update->get_error_code(), $update->get_error_message() );
				}

				// Autofilling Actors
				if ( wpmoly_o( 'enable-actor' ) && wpmoly_o( 'actor-autocomplete' ) ) {
					$limit = intval( wpmoly_o( 'actor-limit' ) );
					$actors = explode( ',', $movie_meta['cast'] );
					if ( $limit )
						$actors = array_slice( $actors, 0, $limit );
					$actors = wp_set_object_terms( $post_ID, $actors, 'actor', false );
				}

				// Autofilling Genres
				if ( wpmoly_o( 'enable-genre' ) && wpmoly_o( 'genre-autocomplete' ) ) {
					$genres = explode( ',', $movie_meta['genres'] );
					$genres = wp_set_object_terms( $post_ID, $genres, 'genre', false );
				}

				// Autofilling Collections
				if ( wpmoly_o( 'enable-collection' ) && wpmoly_o( 'collection-autocomplete' ) ) {
					$collections = explode( ',', $movie_meta['director'] );
					$collections = wp_set_object_terms( $post_ID, $collections, 'collection', false );
				}
			}
			else if ( isset( $_REQUEST['meta'] ) && '' != $_REQUEST['meta'] ) {

				self::save_movie_meta( $post_ID, $_POST['meta'] );
			}

			if ( isset( $_REQUEST['wpmoly_details'] ) && ! is_null( $_REQUEST['wpmoly_details'] ) ) {

				if ( isset( $_REQUEST['is_quickedit'] ) || isset( $_REQUEST['is_bulkedit'] ) )
					wpmoly_check_admin_referer( 'quickedit-movie-details' );

				$wpmoly_details = $_REQUEST['wpmoly_details'];
				if ( true === $_REQUEST['is_bulkedit'] ) {
					foreach ( $_REQUEST['post'] as $post_id ) {
						self::save_movie_details( $post_id, $wpmoly_details );
					}
				} else {
					self::save_movie_details( $post_ID, $wpmoly_details );
				}
			}

			WPMOLY_Cache::clean_transient( 'clean', $force = true );

			return ( ! empty( $errors->errors ) ? $errors : $post_ID );
		}

		/**
		 * If a movie's post is considered "empty" and post_title is
		 * empty, bypass WordPress empty content safety to avoid losing
		 * imported metadata. 'wp_insert_post_data' filter will later
		 * update the post_title to the correct movie title.
		 * 
		 * @since    2.0
		 * 
		 * @param    bool     $maybe_empty Whether the post should be considered "empty".
		 * @param    array    $postarr     Array of post data.
		 * 
		 * @return   boolean
		 */
		public static function filter_empty_content( $maybe_empty, $postarr ) {

			if ( ! isset( $postarr['post_type'] ) || 'movie' != $postarr['post_type'] )
				return $maybe_empty;

			if ( '' == trim( $postarr['post_title'] ) )
				return false;
		}

		/**
		 * Filter slashed post data just before it is inserted into the
		 * database. If an empty movie title is detected, and metadata
		 * contains a title, use it for post_title; if no movie title
		 * can be found, just use (no title) for post_title.
		 * 
		 * @since    2.0
		 * 
		 * @param    array    $data    An array of slashed post data.
		 * @param    array    $postarr An array of sanitized, but otherwise unmodified post data.
		 * 
		 * @return   array    Updated $data
		 */
		public static function filter_empty_title( $data, $postarr ) {

			if ( '' != $data['post_title'] || ! isset( $data['post_type'] ) || 'movie' != $data['post_type'] || in_array( $data['post_status'], array( 'import-queued', 'import-draft' ) ) )
				return $data;

			$no_title   = __( '(no title)' );
			$post_title = $no_title;
			if ( isset( $postarr['meta']['title'] ) && trim( $postarr['meta']['title'] ) )
				$post_title = $postarr['meta']['title'];

			if ( $post_title != $no_title && ! in_array( $data['post_status'], array( 'draft', 'pending', 'auto-draft' ) ) )
				$data['post_name'] = sanitize_title( $post_title );

			$data['post_title'] = $post_title;

			return $data;
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