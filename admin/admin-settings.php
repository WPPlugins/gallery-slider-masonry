<?php
/*
 * Functions and data for the admin
 * Includes our settings
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns an array of settings for the Gallery tab
 *
 * @since 1.0.0
 * @return Array
 */
if( ! function_exists( 'ctmgs_gallery_page_settings' ) ) {
	function ctmgs_gallery_page_settings() {
		$settings = array(
			'link_to' => array(
				'id'		=> 'link_to',
				'label'		=> __( 'Link To', 'ctmgs' ),
				'callback'	=> 'select_callback',
				'choices'	=> array(
					'post'	=> __( 'Attachment Page', 'ctmgs' ),
					'file'	=> __( 'Media File', 'ctmgs' ),
					'none'	=> __( 'None', 'ctmgs' )
				),
				'description' => __( 'This setting will be overridden if you have the Lightbox Slider enabled', 'ctmgs' ),
				'page'		=> 'ctmgs_gallery',
				'section'	=> 'ctmgs_gallery_settings',
			),
			'columns' => array(
				'id'		=> 'columns',
				'label'		=> __( 'Columns', 'ctmgs' ),
				'callback'	=> 'select_callback',
				'choices'	=> array(
					'2'		=> '2',
					'3'		=> '3',
					'4'		=> '4',
					'5'		=> '5',
					'6'		=> '6',
					'7'		=> '7',
					'8'		=> '8',
					'9'		=> '9',
				),
				'description' => __( 'Set default number of columns for Masonry Gallery', 'ctmgs' ),
				'page'		=> 'ctmgs_gallery',
				'section'	=> 'ctmgs_gallery_settings',
			),
			'size' => array(
				'id'		=> 'size',
				'label'		=> __( 'Size', 'ctmgs' ),
				'callback'	=> 'select_callback',
				'choices'	=> array(
					'none'			=> __( 'No Default', 'ctmgs' ),
					'thumbnail'		=> __( 'Thumbnail', 'ctmgs' ),
					'medium'		=> __( 'Medium', 'ctmgs' ),
					'large'			=> __( 'Large', 'ctmgs' ),
					'full'			=> __( 'Full', 'ctmgs' ),
				),
				'description'	=> __( 'Unlike the Link To and Columns settings above, a default value cannot be attributed to the Size setting from the admin side (i.e. when you create a new gallery). Instead, if you specify a value here, all galleries in your site will use this value for gallery image sizes.', 'ctmgs' ),
				'page'		=> 'ctmgs_gallery',
				'section'	=> 'ctmgs_gallery_settings',
			),
			'landscape' => array(
				'id'		=> 'landscape',
				'label'		=> __( 'Landscape / Portrait', 'ctmgs' ),
				'callback'	=> 'checkbox_callback',
				'description'	=> __( 'Enable this to set landscape and portrait images to different widths. Works best with lots of images and several columns. For the masonry gallery only.', 'ctmgs' ),
				'page'		=> 'ctmgs_gallery',
				'section'	=> 'ctmgs_gallery_settings',
			),
			'existing_gallery_format' => array(
				'id'		=> 'existing_gallery_format',
				'label'		=> __( 'Existing Gallery Format', 'ctmgs' ),
				'callback'	=> 'select_callback',
				'choices'	=> array(
					'default'	=> __( 'Default', 'ctmgs' ),
					'masonry'	=> __( 'Masonry', 'ctmgs' ),
					'slider'	=> __( 'Slider', 'ctmgs' )
				),
				'description' => __( 'This setting will apply to existing galleries created before you activated this plugin. Default will mean that existing galleries are not updated; otherwise, choosing Masonry or Slider will convert all existing galleries to either masonry or slider layouts.', 'ctmgs' ),
				'page'		=> 'ctmgs_gallery',
				'section'	=> 'ctmgs_gallery_settings',
			),
		);
		
		return $settings;
	}

}

/**
 * Returns an array of settings for the Gallery Slider tab
 *
 * @since 1.0.0
 * @return Array
 */
if( ! function_exists( 'ctmgs_slider_page_settings' ) ) {
	function ctmgs_slider_page_settings() {
		$settings = array(
			'slider_margin' => array(
				'id'		=> 'slider_margin',
				'label'		=> __( 'Margin', 'ctmgs' ),
				'callback'	=> 'text_callback',
				'description' => __( 'Margin between images in slider.', 'ctmgs' ),
				'page'		=> 'ctmgs_slider',
				'section'	=> 'ctmgs_slider_settings',
			),
		);
		
		return $settings;
	}

}

/**
 * Returns an array of settings for the Lightbox Slider tab
 *
 * @since 1.0.0
 * @return Array
 */
if( ! function_exists( 'ctmgs_lightbox_page_settings' ) ) {
	function ctmgs_lightbox_page_settings() {
		$settings = array(
			'enable_slider' => array(
				'id'		=> 'enable_slider',
				'label'		=> __( 'Enable Lightbox Slider', 'ctmgs' ),
				'callback'	=> 'checkbox_callback',
				'description' => __( 'Add lightbox slider when user clicks a masonry gallery image.', 'ctmgs' ),
				'page'		=> 'ctmgs_lightbox',
				'section'	=> 'ctmgs_lightbox_settings',
			),
		);
		
		return $settings;
	}

}