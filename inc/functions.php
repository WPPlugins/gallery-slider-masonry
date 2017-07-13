<?php
/**
 * Functions
 */

/**
 * Enqueue scripts and styles.
 */
function ctmgs_scripts() {
	if( ctmgs_is_lightbox_slider_enabled() ) {
		wp_enqueue_style( 'owl-style', CTMGS_PLUGIN_URL . 'assets/css/owl.carousel.min.css' );
		wp_enqueue_script( 'owl-script', CTMGS_PLUGIN_URL . 'assets/js/owl.carousel.min.js', array( 'jquery' ) );
	}
	wp_enqueue_script( 'jquery-masonry', '', array( 'jquery' ) );
	wp_enqueue_style( 'ctmgs-style', CTMGS_PLUGIN_URL . 'assets/css/style.css' );
}
add_action( 'wp_enqueue_scripts', 'ctmgs_scripts' );