<?php
/**
 * WPMovieLibrary-People
 *
 * Add People support to WPMovieLibrary
 *
 * @package   WPMovieLibrary-People
 * @author    Charlie MERLAND <charlie@caercam.org>
 * @license   GPL-3.0
 * @link      http://www.caercam.org/
 * @copyright 2014 CaerCam.org
 *
 * @wordpress-plugin
 * Plugin Name: WPMovieLibrary-People
 * Plugin URI:  http://wpmovielibrary.com/extensions/wpmovielibrary-people/
 * Description: Add People support to WPMovieLibrary
 * Version:     1.0
 * Author:      Charlie MERLAND
 * Author URI:  http://www.caercam.org/
 * Text Domain: wpmovielibrary-people
 * License:     GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/wpmovielibrary/wpmovielibrary-people
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WPMOLYP_NAME',                    'WPMovieLibrary-People' );
define( 'WPMOLYP_VERSION',                 '1.0' );
define( 'WPMOLYP_SLUG',                    'wpmoly-people' );
define( 'WPMOLYP_URL',                     plugins_url( basename( __DIR__ ) ) );
define( 'WPMOLYP_PATH',                    plugin_dir_path( __FILE__ ) );
define( 'WPMOLYP_REQUIRED_PHP_VERSION',    '5.4' );
define( 'WPMOLYP_REQUIRED_WP_VERSION',     '4.0' );
define( 'WPMOLYP_REQUIRED_WPMOLY_VERSION', '2.1' );

/**
 * Determine whether WPMOLY is active or not.
 *
 * @since    1.0
 *
 * @return   boolean
 */
if ( ! function_exists( 'is_wpmoly_active' ) ) :
	function is_wpmoly_active() {

		return defined( 'WPMOLYVERSION' );
	}
endif;

/**
 * Checks if the system requirements are met
 * 
 * @since    1.0
 * 
 * @return   bool    True if system requirements are met, false if not
 */
function wpmolyp_requirements_met() {

	global $wp_version;

	if ( version_compare( PHP_VERSION, WPMOLYP_REQUIRED_PHP_VERSION, '<=' ) )
		return false;

	if ( version_compare( $wp_version, WPMOLYP_REQUIRED_WP_VERSION, '<=' ) )
		return false;

	return true;
}

/**
 * Prints an error that the system requirements weren't met.
 * 
 * @since    1.0
 */
function wpmolyp_requirements_error() {

	global $wp_version;

	require_once WPMOLYP_PATH . '/views/requirements-error.php';
}

/**
 * Prints an error that the system requirements weren't met.
 * 
 * @since    1.0.1
 */
function wpmolyp_l10n() {

	$domain = 'wpmovielibrary-people';
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	load_textdomain( $domain, WPMOLYP_PATH . 'languages/' . $domain . '-' . $locale . '.mo' );
	load_plugin_textdomain( $domain, FALSE, basename( __DIR__ ) . '/languages/' );
}

/*
 * Check requirements and load main class
 * The main program needs to be in a separate file that only gets loaded if the
 * plugin requirements are met. Otherwise older PHP installations could crash
 * when trying to parse it.
 */
if ( wpmolyp_requirements_met() ) {

	// Core
	require_once( WPMOLYP_PATH . 'includes/classes/class-module.php' );
	require_once( WPMOLYP_PATH . 'class-wpmovielibrary-people.php' );

	// Public
	require_once( WPMOLYP_PATH . 'public/class-wpmoly-people.php' );

	if ( class_exists( 'WPMovieLibrary_People' ) ) {
		$GLOBALS['wpmolyp'] = new WPMovieLibrary_People();
		register_activation_hook(   __FILE__, array( $GLOBALS['wpmolyp'], 'activate' ) );
		register_deactivation_hook( __FILE__, array( $GLOBALS['wpmolyp'], 'deactivate' ) );
	}

	WPMovieLibrary_People::require_wpmoly_first();

	if ( is_admin() ) {
		require_once( WPMOLYP_PATH . 'admin/class-wpmoly-people-api.php' );
		require_once( WPMOLYP_PATH . 'admin/class-wpmoly-people-api-wrapper.php' );
		require_once( WPMOLYP_PATH . 'admin/class-wpmoly-people-admin.php' );
		require_once( WPMOLYP_PATH . 'admin/class-wpmoly-edit-people.php' );

		add_action( 'plugins_loaded', array( 'WPMovieLibrary_People_Admin', 'get_instance' ) );
	}
}
else {
	add_action( 'init', 'wpmolyp_l10n' );
	add_action( 'admin_notices', 'wpmolyp_requirements_error' );
}
