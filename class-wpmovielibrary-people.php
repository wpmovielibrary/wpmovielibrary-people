<?php
/**
 * WPMovieLibrary-People
 *
 * @package   WPMovieLibrary-People
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 Charlie MERLAND
 */

if ( ! class_exists( 'WPMovieLibrary_People' ) ) :

	/**
	* Plugin class
	*
	* @package WPMovieLibrary-People
	* @author  Charlie MERLAND <charlie@caercam.org>
	*/
	class WPMovieLibrary_People extends WPMOLYP_Module {

		protected $modules;

		/**
		 * Initialize the plugin by setting localization and loading public scripts
		 * and styles.
		 *
		 * @since     1.0
		 */
		public function __construct() {

			$this->init();
		}

		/**
		 * Initializes variables
		 *
		 * @since    1.0
		 */
		public function init() {

			$this->register_hook_callbacks();

			$this->modules = array(
				'WPMOLYP_People'    => WPMOLYP_People::get_instance()
			);

			$this->metadata = array(
				'tmdb_id' => array(
					'title'       => __( 'TMDb ID', 'wpmovielibrary' ),
					'type'        => 'hidden',
					'filter'      => 'intval',
					'filter_args' => null,
					'size'        => 'hidden',
					'group'       => 'meta',
					'rewrite'     => array( 'tmdb' => __( 'tmdb', 'wpmovielibrary' ) )
				),
				'name' => array(
					'title'       => __( 'Name', 'wpmovielibrary' ),
					'type'        => 'text',
					'filter'      => 'esc_html',
					'filter_args' => null,
					'size'        => 'half',
					'rewrite'     => array( 'name' => __( 'name', 'wpmovielibrary' ) )
				),
				'also_known_as' => array(
					'title'       => __( 'Alias', 'wpmovielibrary' ),
					'type'        => 'text',
					'filter'      => 'esc_html',
					'filter_args' => null,
					'size'        => 'half',
					'rewrite'     => array( 'alias' => __( 'alias', 'wpmovielibrary' ) )
				),
				'birthday' => array(
					'title'       => __( 'Birthday', 'wpmovielibrary' ),
					'type'        => 'text',
					'filter'      => 'esc_html',
					'filter_args' => null,
					'size'        => 'half',
					'rewrite'     => array( 'birthday' => __( 'birthday', 'wpmovielibrary' ) )
				),
				'place_of_birth' => array(
					'title'       => __( 'Place of birth', 'wpmovielibrary' ),
					'type'        => 'text',
					'filter'      => 'esc_html',
					'filter_args' => null,
					'size'        => 'half',
					'rewrite'     => array( 'placeofbirth' => __( '', 'wpmovielibrary' ) )
				),
				'biography' => array(
					'title'       => __( 'Biography', 'wpmovielibrary' ),
					'type'        => 'textarea',
					'filter'      => 'wp_kses',
					'filter_args' => array( 'b' => array(), 'i' => array(), 'em' => array(), 'strong' => array(), 'sup' => array(), 'sub' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(), 'br' => array(), 'span' => array() ),
					'size'        => 'full',
					'rewrite'     => null
				),
				'deathday' => array(
					'title'       => __( 'Deathday', 'wpmovielibrary' ),
					'type'        => 'text',
					'filter'      => 'esc_html',
					'filter_args' => null,
					'size'        => 'half',
					'rewrite'     => array( 'deathday' => __( 'deathday', 'wpmovielibrary' ) )
				),
				'imdb_id' => array(
					'title'       => __( 'IMDb ID', 'wpmovielibrary' ),
					'type'        => 'text',
					'filter'      => 'esc_html',
					'filter_args' => null,
					'size'        => 'half',
					'rewrite'     => array( 'imdb' => __( 'imdb', 'wpmovielibrary' ) )
				),
				'homepage' => array(
					'title'       => __( 'Homepage', 'wpmovielibrary' ),
					'type'        => 'text',
					'filter'      => 'esc_html',
					'filter_args' => null,
					'size'        => 'half',
					'rewrite'     => null
				),
				'adult' => array(
					'title'       => __( 'Adult', 'wpmovielibrary' ),
					'type'        => 'text',
					'filter'      => 'esc_html',
					'filter_args' => null,
					'size'        => 'half',
					'rewrite'     => array( 'adult' => __( 'adult', 'wpmovielibrary' ) )
				)
			);
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_action( 'plugins_loaded', 'wpmolyp_l10n' );

			add_action( 'activated_plugin', __CLASS__ . '::require_wpmoly_first' );

		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                     Plugin  Activate/Deactivate
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Fired when the plugin is activated.
		 *
		 * @since    1.0
		 *
		 * @param    boolean    $network_wide    True if WPMU superadmin uses
		 *                                       "Network Activate" action, false if
		 *                                       WPMU is disabled or plugin is
		 *                                       activated on an individual blog.
		 */
		public function activate( $network_wide ) {

			global $wpdb;

			if ( function_exists( 'is_multisite' ) && is_multisite() ) {
				if ( $network_wide ) {
					$blogs = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

					foreach ( $blogs as $blog ) {
						switch_to_blog( $blog );
						$this->single_activate( $network_wide );
					}

					restore_current_blog();
				} else {
					$this->single_activate( $network_wide );
				}
			} else {
				$this->single_activate( $network_wide );
			}

		}

		/**
		 * Fired when the plugin is deactivated.
		 * 
		 * When deactivatin/uninstalling WPMOLY, adopt different behaviors depending
		 * on user options. Movies and Taxonomies can be kept as they are,
		 * converted to WordPress standars or removed. Default is conserve on
		 * deactivation, convert on uninstall.
		 *
		 * @since    1.0
		 */
		public function deactivate() {

			foreach ( $this->modules as $module )
				$module->deactivate();
		}

		/**
		 * Runs activation code on a new WPMS site when it's created
		 *
		 * @since    1.0
		 *
		 * @param    int    $blog_id
		 */
		public function activate_new_site( $blog_id ) {
			switch_to_blog( $blog_id );
			$this->single_activate( true );
			restore_current_blog();
		}

		/**
		 * Prepares a single blog to use the plugin
		 *
		 * @since    1.0
		 *
		 * @param    bool    $network_wide
		 */
		protected function single_activate( $network_wide ) {

			self::require_wpmoly_first();

			foreach ( $this->modules as $module )
				$module->activate( $network_wide );

			flush_rewrite_rules();
		}

		/**
		 * Make sure the plugin is load after WPMovieLibrary and not
		 * before, which would result in errors and missing files.
		 *
		 * @since    1.0
		 */
		public static function require_wpmoly_first() {

			$this_plugin_path = plugin_dir_path( __FILE__ );
			$this_plugin      = basename( $this_plugin_path ) . '/wpmoly-people.php';
			$active_plugins   = get_option( 'active_plugins' );
			$this_plugin_key  = array_search( $this_plugin, $active_plugins );
			$wpmoly_plugin_key  = array_search( 'wpmovielibrary/wpmovielibrary.php', $active_plugins );

			if ( $this_plugin_key < $wpmoly_plugin_key ) {

				unset( $active_plugins[ $this_plugin_key ] );
				$active_plugins = array_merge(
					array_slice( $active_plugins, 0, $wpmoly_plugin_key ),
					array( $this_plugin ),
					array_slice( $active_plugins, $wpmoly_plugin_key )
				);

				update_option( 'active_plugins', $active_plugins );
			}
		}

	}
endif;