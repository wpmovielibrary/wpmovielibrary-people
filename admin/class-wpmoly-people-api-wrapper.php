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
	class WPMOLYP_TMDb {

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

		}

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
			$person['photos'] = $this->get_images( $id );
			/*$person['images'] = $this->get_photos( $id, $lang );
			$person['credit'] = $this->get_credits( $id, $lang );*/

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
		public function search_person( $query ) {

			$person = $this->api->searchPerson( $query );

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

			$defaults = array_flip( array( 'adult', 'also_known_as', 'biography', 'birthday', 'deathday', 'homepage', 'id', 'imdb_id', 'name', 'place_of_birth' ) );

			$person = $this->api->getPerson( $id, $lang );
			$person = array_intersect_key( $person, $defaults );

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

			$defaults = array_flip( array( 'aspect_ratio', 'file_path', 'height', 'width' ) );

			$images = $this->api->getPersonImages( $id, $lang );
			if ( ! isset( $images['profiles'] ) || empty( $images['profiles'] ) )
				return array();

			$images = $images['profiles'];
			foreach ( $images as $i => $image )
				$images[ $i ] = array_intersect_key( $image, $defaults );

			$images = apply_filters( 'wpmoly_jsonify_movie_images', $images );

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