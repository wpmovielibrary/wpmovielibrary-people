<?php
/**
 * WPMovieLibrary People Admin Class
 *
 * @package   WPMovieLibrary
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 Charlie MERLAND
 */

if ( ! class_exists( 'WPMovieLibrary_People_Admin' ) ) :

	/**
	* Plugin Admin class.
	*
	* @package WPMovieLibrary_Admin
	* @author  Charlie MERLAND <charlie@caercam.org>
	*/
	class WPMovieLibrary_People_Admin extends WPMOLYP_Module {

		/**
		 * Slug of the plugin screen.
		 *
		 * @since    1.0
		 * @var      array
		 */
		protected $screen_hooks = null;
		protected $hidden_pages = null;

		/**
		 * Plugin Settings.
		 *
		 * @since    1.0
		 * @var      array
		 */
		protected $settings;
		protected static $default_settings;

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

			$this->modules = array(
				//'WPMOLYP_TMDb'       => WPMOLYP_TMDb::get_instance(),
				'WPMOLY_Edit_People' => WPMOLY_Edit_People::get_instance(),
			);

			/*$this->screen_hooks = array(
				'edit'     => 'post.php',
				'new'      => 'post-new.php',
				'movie'    => 'movie',
				'movies'   => 'edit.php',
				'widgets'  => 'widgets.php',
				'settings' => sprintf( '%s_page_wpmovielibrary-settings', strtolower( __( 'Movies', 'wpmovielibrary' ) ) )
			);

			$this->hidden_pages = array();*/

		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_action( 'init', array( $this, 'init' ) );

			// Add the options page and menu item.
			/*add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
			add_action( 'admin_head', array( $this, 'admin_head' ) );

			// highlight the proper top level menu
			add_action( 'parent_file', array( $this, 'admin_menu_highlight' ) );*/

			// Load admin style sheet and JavaScript.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

			add_filter( 'plugin_action_links_' . WPMOLY_PLUGIN, array( $this, 'plugin_action_links' ), 10, 1 );
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		}

		/**
		 * Add new links to the Plugins Page
		 *
		 * @since    1.0
		 * 
		 * @param    array    $links Current links list
		 * 
		 * @return   array    $links Updated links list
		 */
		public function plugin_action_links( $links ) {

			$new_links = array(
				sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=wpmovielibrary-settings' ), __( 'Settings', 'wpmovielibrary' ) )
			);

			$links = array_merge( $new_links, $links );

			return $links;
		}

		/**
		 * Add new links to the Plugin's row meta list
		 *
		 * @since    1.0
		 * 
		 * @param    mixed    $links Plugin Row Meta
		 * @param    mixed    $file  Plugin Base file
		 * 
		 * @return   array    $links Updated links list
		 */
		public function plugin_row_meta( $links, $file ) {

			if ( $file != WPMOLY_PLUGIN )
				return $links;

			$row_meta = array(
				'docs'    => '<a href="' . esc_url( 'http://wpmovielibrary.com/documentation/' ) . '" title="' . esc_attr( __( 'View WPMovieLibrary Documentation', 'wpmovielibrary' ) ) . '">' . __( 'Documentation', 'wpmovielibrary' ) . '</a>',
				'apidocs' => '<a href="' . esc_url( 'https://wordpress.org/support/plugin/wpmovielibrary/' ) . '" title="' . esc_attr( __( 'Visit WPMovieLibrary Support Forum', 'wpmovielibrary' ) ) . '">' . __( 'Support', 'wpmovielibrary' ) . '</a>',
			);

			$links = array_merge( $links, $row_meta );

			return $links;
		}

		/**
		 * Register and enqueue admin-specific style sheet.
		 *
		 * @since    1.0
		 * 
		 * @param    string    $hook_suffix The current admin page.
		 */
		public function enqueue_admin_styles( $hook_suffix ) {

			wp_enqueue_style( WPMOLYP_SLUG . '-admin-css', WPMOLYP_URL . '/assets/css/admin/wpmoly-people.css', array(), WPMOLYP_VERSION );
		}

		/**
		 * Register and enqueue admin-specific JavaScript.
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $hook_suffix The current admin page.
		 */
		public function enqueue_admin_scripts( $hook_suffix ) {

			wp_enqueue_script( WPMOLYP_SLUG . '-admin-js', WPMOLYP_URL . '/assets/js/admin/wpmoly-people.js', array( 'jquery' ), WPMOLYP_VERSION, true );
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
