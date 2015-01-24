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
				'singular_name'      => __( 'Person', 'wpmovielibrary' ),
				'add_new'            => __( 'Add New', 'wpmovielibrary' ),
				'add_new_item'       => __( 'Add New Person', 'wpmovielibrary' ),
				'edit_item'          => __( 'Edit Person', 'wpmovielibrary' ),
				'new_item'           => __( 'New Person', 'wpmovielibrary' ),
				'all_items'          => __( 'All People', 'wpmovielibrary' ),
				'view_item'          => __( 'View Person', 'wpmovielibrary' ),
				'search_items'       => __( 'Search People', 'wpmovielibrary' ),
				'not_found'          => __( 'No movies found', 'wpmovielibrary' ),
				'not_found_in_trash' => __( 'No movies found in Trash', 'wpmovielibrary' ),
				'parent_item_colon'  => '',
				'menu_name'          => __( 'People', 'wpmovielibrary' )
			);

			$slug = 'person';
			/*if ( '1' == wpmoly_o( 'rewrite-enable' ) ) {
				$rewrite = wpmoly_o( 'rewrite-person' );
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

			register_post_type( 'person', $args );
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                              Methods
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Return various Person Post Meta.
		 *
		 * @since    1.0
		 * 
		 * @param    int       $post_id Person Post ID
		 * @param    string    $meta Meta to return
		 *
		 * @return   array|string    WPMOLY Person Meta if available, empty string else.
		 */
		public static function get_person_meta( $post_id = null, $meta = null ) {

			if ( is_null( $post_id ) )
				$post_id =  get_the_ID();

			if ( ! $post = get_post( $post_id ) || 'person' != get_post_type( $post_id ) )
				return false;

			global $wpmolyp;

			if ( in_array( $meta, array( 'meta', 'data' ) ) ) {

				$value = array();
				foreach ( array_keys( $wpmolyp->metadata ) as $meta )
					$value[ $meta ] = get_post_meta( $post_id, "_wpmoly_person_{$meta}", true );

				return $value;
			}

			if ( ! in_array( $meta, array_keys( $wpmolyp->metadata ) ) )
				return null;

			$value = get_post_meta( $post_id, "_wpmoly_person_{$meta}", true );

			return $value;
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