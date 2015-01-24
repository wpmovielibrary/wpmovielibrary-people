<?php
/**
 * WPML_TMDb Class extension.
 * 
 * @package   WPMovieLibrary-People
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( class_exists( 'WPMOLY_TMDb' ) && ! class_exists( 'WPMOLYP_TMDb' ) ) :

	/**
	 * Extends Class for WPML Api Wrapper Class
	 * 
	 * @since    1.0
	 */
	class WPMOLYP_TMDb extends WPMOLY_TMDb {

		/**
		 * API Instance
		 *
		 * @since    1.0
		 * @var      class
		 */
		protected $api = null;

		/**
		 * Default constructor
		 */
		public function __construct() {

			if ( ! is_admin() )
				return false;

			$this->api = new WPMOLYP_Api();

			$this->register_hook_callbacks();

		}

		/**
		 * Register callbacks for actions and filters
		 * 
		 * @since    1.0
		 */
		public function register_hook_callbacks() {

			add_action( 'admin_init', array( $this, 'init' ) );

			add_action( 'wp_ajax_wpmoly_search_people', array( $this, 'search_people_callback' ), 10 );
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Callbacks
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Search people callback
		 *
		 * @since    1.0
		 */
		public function search_people_callback() {

			wpmoly_check_ajax_referer( 'search-people' );

			$post_id  = ( isset( $_POST['post_id'] ) && '' != $_POST['post_id'] ? intval( $_POST['post_id'] ) : null );
			$lang     = ( isset( $_POST['lang'] ) && '' != $_POST['lang'] ? esc_html( $_POST['lang'] ) : wpmoly_o( 'api-language' ) );
			$query    = ( isset( $_POST['query'] ) && '' != $_POST['query'] ? esc_html( $_POST['query'] ) : null );

			if ( is_null( $query ) )
				wp_send_json_error( new WP_Error( 'empty', __( 'Empty search query.', 'wpmovielibrary-people' ) ) );

			if ( preg_match( '/\d/i', $query, $m ) )
				$response = $this->get_people( $query, $lang );
			else
				$response = $this->search_person( $query, $lang );

			if ( is_wp_error( $response ) )
				wp_send_json_error( $response );

			wp_send_json_success( $response );
		}

		/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
		 *
		 *                             Methods
		 * 
		 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

		/**
		 * Get all detail about a specific person
		 * 
		 * @since    1.0
		 * 
		 * @param    int       $id Person TMDb ID
		 * @param    string    $lang Filter the result with a language
		 * 
		 * @return   array     TMDb result
		 */
		public function get_people( $id, $lang = null ) {

			$person = $this->get_person( $id, $lang );
			/*$person['photos'] = $this->get_images( $id );
			$person['images'] = $this->get_photos( $id, $lang );*/
			$person['credits'] = $this->get_credits( $id, $lang );

			return $person;
		}

		/**
		 * Find a specific person
		 * 
		 * @since    1.0
		 * 
		 * @param    string    $query Person name or ID
		 * 
		 * @return   array     TMDb result
		 */
		public function search_person( $query, $lang = null ) {

			$person = $this->api->searchPerson( $query );

			if ( 1 == $person['total_results'] && isset( $person['results'][0]['id'] ) ) {

				$id = intval( $person['results'][0]['id'] );
				return $this->get_people( $id, $lang );
			}

			return $person;
		}

		/**
		 * Get a person's data
		 * 
		 * @since    1.0
		 * 
		 * @param    int       $id Person TMDb ID
		 * @param    string    $lang Filter the result with a language
		 * 
		 * @return   array     TMDb result
		 */
		public function get_person( $id, $lang = null ) {

			$defaults = array_flip( array( 'adult', 'also_known_as', 'biography', 'birthday', 'deathday', 'homepage', 'id', 'imdb_id', 'name', 'place_of_birth', 'profile_path' ) );

			$person = $this->api->getPerson( $id, $lang );
			$person = array_intersect_key( $person, $defaults );

			$person['alias']      = implode( ', ', $person['also_known_as'] );
			$person['tmdb_id']    = intval( $person['id'] );
			$person['birthplace'] = $person['place_of_birth'];
			unset( $person['place_of_birth'], $person['id'], $person['also_known_as'] );

			$person = array_map( 'esc_html', $person );
			$person = array(
				'meta' => $person
			);


			return $person;
		}

		/**
		 * Get a person's credits
		 * 
		 * @since    1.0
		 * 
		 * @param    int       $id Person TMDb ID
		 * @param    string    $lang Filter the result with a language
		 * 
		 * @return   array     TMDb result
		 */
		public function get_credits( $id, $lang = null ) {

			$credits = $this->api->getPersonCredits( $id, $lang );

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
		 * Get a person's images
		 * 
		 * @since    1.0
		 * 
		 * @param    int       $id Person TMDb ID
		 * @param    string    $lang Filter the result with a language
		 * 
		 * @return   array     TMDb result
		 */
		public function get_images( $id, $lang = null ) {

			global $post;

			$defaults = array_flip( array( 'aspect_ratio', 'file_path', 'height', 'width' ) );

			$images = $this->api->getPersonImages( $id, $lang );
			if ( ! isset( $images['profiles'] ) || empty( $images['profiles'] ) )
				return array();

			$images = $images['profiles'];
			foreach ( $images as $i => $image )
				$images[ $i ] = array_intersect_key( $image, $defaults );

			$images = apply_filters( 'wpmoly_jsonify_movie_images', $images, $post, 'poster' );

			return $images;
		}

		/**
		 * Get a person's photos
		 * 
		 * @since    1.0
		 * 
		 * @param    int       $id Person TMDb ID
		 * @param    string    $lang Filter the result with a language
		 * 
		 * @return   array     TMDb result
		 */
		public function get_photos( $id, $lang = null ) {

			$photos = $this->api->getPersonTaggedImages( $id, $lang );

			return $photos;
		}

		private function filter_data( $data ) {

			return $data;
		}

	}

endif;