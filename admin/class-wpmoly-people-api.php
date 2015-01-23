<?php
/**
 * TMDb Class extension to support People.
 * 
 * @package   WPMovieLibrary-People
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( class_exists( 'TMDb' ) && ! class_exists( 'WPMOLYP_Api' ) ) :

	/**
	 * Extends Class for WPML Api Class
	 * 
	 * @since    1.0
	 */
	class WPMOLYP_Api extends TMDb
	{

		/**
		 * Default constructor
		 */
		public function __construct() {

			if ( ! is_admin() )
				return false;

			$this->api_key  = wpmoly_o( 'api-key' );
			$this->scheme   = wpmoly_o( 'api-scheme' );
			$this->internal = wpmoly_o( 'api-internal' );

			if ( '' == $this->api_key )
				$this->internal = true;
		}

		/**
		 * Search a person by querystring
		 *
		 * @param    string    $text Query to search after in the TMDb database
		 * @param    int       $page Number of the page with results (default first page)
		 * @param    bool      $adult Whether of not to include adult movies in the results (default FALSE)
		 * 
		 * @return   string    TMDb result array
		 */
		public function searchPerson( $query, $page = 1, $adult = false ) {

			$params = array(
				'query' => $query,
				'page' => (int) $page,
				'include_adult' => (bool) $adult,
			);

			return $this->_makeCall( 'search/person', $params );
		}

		/**
		 * Retrieve all basic information for a particular person
		 *
		 * @param    int    $id TMDb person-id
		 * 
		 * @return   string TMDb result array
		 */
		public function getPerson( $id ) {

			return $this->_makeCall( 'person/' . $id );
		}

		/**
		 * Retrieve all cast and crew information for a particular person
		 *
		 * @param    int       $id TMDb person-id
		 * @param    string    $lang Filter the result with a language (ISO 3166-1) other than default
		 * 
		 * @return   string    TMDb result array
		 */
		public function getPersonCredits( $id, $lang = null ) {

			$params = array(
				'language' => $lang
			);

			return $this->_makeCall( 'person/' . $id . '/movie_credits', $params );
		}

		/**
		 * Retrieve all images for a particular person
		 *
		 * @param    int    $id TMDb person-id
		 * 
		 * @return          TMDb result array
		 */
		public function getPersonImages( $id ) {

			return $this->_makeCall( 'person/' . $id . '/images' );
		}

		/**
		 * Retrieve all tagged images for a particular person
		 *
		 * @param    int       $id TMDb person-id
		 * @param    string    $lang Filter the result with a language (ISO 3166-1) other than default
		 * 
		 * @return             TMDb result array
		 */
		public function getPersonTaggedImages( $id, $lang = null ) {

			$params = array(
				'language' => $lang
			);

			return $this->_makeCall( 'person/' . $id . '/tagged_images', $params );
		}
	}

endif;