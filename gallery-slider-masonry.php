<?php
/*
Plugin Name: Masonry Gallery Slider
Description: Create masonry galleries with sliders
Version: 1.0.0
Author: Catapult Themes
Author URI: https://catapultthemes.com/
Text Domain: ctmgs
Domain Path: /languages
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ctmgs_load_plugin_textdomain() {
    load_plugin_textdomain( 'ctmgs', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'ctmgs_load_plugin_textdomain' );

/**
 * Define constants
 **/
if ( ! defined( 'CTMGS_PLUGIN_URL' ) ) {
	define( 'CTMGS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'CTMGS_PLUGIN_VERSION' ) ) {
	define( 'CTMGS_PLUGIN_VERSION', '1.0.0' );
}

if ( is_admin() ) {
	require_once dirname( __FILE__ ) . '/admin/admin-settings.php';
	require_once dirname( __FILE__ ) . '/admin/class-ctmgs-admin.php';
	require_once dirname( __FILE__ ) . '/admin/gallery-settings.php';
}

require_once dirname( __FILE__ ) . '/inc/functions.php';
require_once dirname( __FILE__ ) . '/inc/functions-gallery.php';
require_once dirname( __FILE__ ) . '/inc/functions-slider.php';