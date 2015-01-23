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

if ( ! class_exists( 'WPMOLYP_People' ) ) :

	/**
	* Plugin class
	*
	* @package WPMovieLibrary-People
	* @author  Charlie MERLAND <charlie@caercam.org>
	*/
	class WPMOLYP_People extends WPMOLYP_Module {

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
		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_action( 'init', array( $this, 'register_post_type' ), 10 );

			// Enqueue scripts and styles
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                     Scripts/Styles and Utils
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Register and enqueue public-facing style sheet.
		 *
		 * @since    1.0
		 */
		public function enqueue_styles() {

			wp_enqueue_style( WPMOLYP_SLUG . '-css', WPMOLYP_URL . '/assets/css/public/wpmoly-people.css', array(), WPMOLYP_VERSION );
		}

		/**
		 * Register and enqueue public-facing style sheet.
		 *
		 * @since    1.0
		 */
		public function enqueue_scripts() {

			wp_enqueue_script( WPMOLYP_SLUG . '-js', WPMOLYP_URL . '/assets/js/public/wpmoly-people.js', array( 'jquery' ), WPMOLYP_VERSION, true );
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                     Post Type Registration
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Register a 'movie' custom post type and 'import-draft' post status
		 *
		 * @since    1.0
		 */
		public function register_post_type() {

			$labels = array(
				'name'               => __( 'People', 'wpmovielibrary' ),
				'singular_name'      => __( 'People', 'wpmovielibrary' ),
				'add_new'            => __( 'Add New', 'wpmovielibrary' ),
				'add_new_item'       => __( 'Add New People', 'wpmovielibrary' ),
				'edit_item'          => __( 'Edit People', 'wpmovielibrary' ),
				'new_item'           => __( 'New People', 'wpmovielibrary' ),
				'all_items'          => __( 'All People', 'wpmovielibrary' ),
				'view_item'          => __( 'View People', 'wpmovielibrary' ),
				'search_items'       => __( 'Search People', 'wpmovielibrary' ),
				'not_found'          => __( 'No movies found', 'wpmovielibrary' ),
				'not_found_in_trash' => __( 'No movies found in Trash', 'wpmovielibrary' ),
				'parent_item_colon'  => '',
				'menu_name'          => __( 'People', 'wpmovielibrary' )
			);

			$slug = 'people';
			/*if ( '1' == wpmoly_o( 'rewrite-enable' ) ) {
				$rewrite = wpmoly_o( 'rewrite-people' );
				if ( '' != $slug )
					$slug = $rewrite;
			}*/

			$args = array(
				'labels'             => $labels,
				'rewrite'            => array(
					'slug'       => $slug
				),
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'has_archive'        => true,
				'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments' ),
				'menu_position'      => 5,
				'menu_icon'          => null
			);

			register_post_type( 'people', $args );
		}

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
		public function activate( $network_wide ) {}

		/**
		 * Fired when the plugin is deactivated.
		 *
		 * @since    1.0
		 */
		public function deactivate() {}

	}
endif;