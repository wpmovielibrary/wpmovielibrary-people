<?php
/**
 * WPMovieLibrary Config People definition
 *
 * @package   WPMovieLibrary-People
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 Charlie MERLAND
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) )
	wp_die();

$wpmoly_people_meta = array(
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
	'biography' => array(
		'title'       => __( 'Biography', 'wpmovielibrary' ),
		'type'        => 'textarea',
		'filter'      => 'wp_kses',
		'filter_args' => array( 'b' => array(), 'i' => array(), 'em' => array(), 'strong' => array(), 'sup' => array(), 'sub' => array(), 'ul' => array(), 'ol' => array(), 'li' => array(), 'br' => array(), 'span' => array() ),
		'size'        => 'full',
		'rewrite'     => null
	),
	'place_of_birth' => array(
		'title'       => __( 'Place of birth', 'wpmovielibrary' ),
		'type'        => 'text',
		'filter'      => 'esc_html',
		'filter_args' => null,
		'size'        => 'half',
		'rewrite'     => array( 'placeofbirth' => __( '', 'wpmovielibrary' ) )
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
