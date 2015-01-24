<?php
/**
 * WPMovieLibrary People functions.
 * 
 * 
 * @since     2.0
 * 
 * @package   WPMovieLibrary-People
 * @author    Charlie MERLAND <charlie.merland@gmail.com>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

/** * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *                            People Meta
 * 
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/**
 * Return People's stored TMDb data.
 *
 * @since    1.0
 * 
 * @param    int    Person Post ID
 *
 * @return   array|string    WPMOLY Person TMDb data if stored, empty string else.
 */
function wpmoly_get_person_meta( $post_id = null, $meta = 'data' ) {
	return WPMOLYP_People::get_person_meta( $post_id, $meta );
}