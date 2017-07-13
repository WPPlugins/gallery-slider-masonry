<?php
/**
 * Gallery functions
 */

/**
 * Filter the gallery for masonry and other exciting layouts
 * @since 1.0.0
 * @return $gallery HTML
 */
function ctmgs_gallery( $output='', $atts, $instance ) {
	
	// Check what format we're using for the gallery
	$gallery_format = ctmgs_get_gallery_format( $atts );
	if( $gallery_format == 'masonry' ) {
		$output = ctmgs_get_masonry_gallery( $output='', $atts, $instance );
	} else {
		$output = ctmgs_get_slider_gallery( $output='', $atts, $instance );
	}
	
	return $output;
}
add_filter( 'post_gallery', 'ctmgs_gallery', 10, 3 );

/**
 * Return what gallery format we've specified
 * @since 	1.0.0
 * @return 	String	masonry | slider | false
 * @param 	$atts	Shortcode atts
 */
if( ! function_exists( 'ctmgs_get_gallery_format') ) {
	function ctmgs_get_gallery_format( $atts ) {
		if( isset( $atts['ctmgs_gallery_layout'] ) ) {
			return $atts['ctmgs_gallery_layout'];
		}
		// Else check if we have a default set
		$options = get_option( 'ctmgs_gallery_settings' );
		if( isset( $options['existing_gallery_format'] ) ) {
			return $options['existing_gallery_format'];
		}
		return 'default';
	}
}

/**
 * Return what gallery format we've specified
 * @since 	1.0.0
 * @return 	String	masonry | slider | false
 * @param 	$output 	Empty output string
 * @param 	$atts		Attributes from shortcode
 * @param 	$instance	Instance
 */
if( ! function_exists( 'ctmgs_get_masonry_gallery') ) {
	function ctmgs_get_masonry_gallery( $output='', $atts, $instance ) {
	
		// Columms
		$columns = 3;
		if( isset( $atts['columns'] ) ) {
			$columns = $atts['columns'];
		}
	
		// Link To
		$link_to = ctmgs_get_link_setting( $atts );
	
		// Size
		$size = ctmgs_get_default_size( $atts );
	
		// Get the image IDs
		$images = explode( ',', $atts['ids'] );
	
		// Gallery classes
		$classes = array( 'masonry-gallery' );
		$classes[] = 'columns-' . esc_attr( $columns );
	
		// Landscape / Portrait differential
		// Check for global default
		$options = get_option( 'ctmgs_gallery_settings' );
		if( ! empty( $options['landscape'] ) ) {
			$classes[] = 'landscape-differential';
		}
		if( ctmgs_is_lightbox_slider_enabled() ) {
			$classes[] = 'slider-enabled';
		}

		$gallery = '';
	
		if( $images ) {
			$count = 0;
			$gallery .= '<div id="masonry-gallery-' . esc_attr( $instance ) . '" class="masonry-gallery-wrapper">';
				$gallery .= '<div class="' . join( ' ', $classes ) . '">';
				$gallery .= '<div class="grid-sizer"></div>';
				foreach( $images as $image ) {
					$image_html = ctmgs_get_image_html( $image, $size, $link_to, $count );
				
					$image_html = apply_filters( 'ctmgs_filter_image_html', $image_html, $image, $size, $link_to, $count );
					$gallery .= $image_html;
					$count++;
				}
				$gallery .= '</div><!-- .masonry-gallery -->';
			$gallery .= '</div><!-- .masonry-gallery-wrapper -->';
		
		if( ctmgs_is_lightbox_slider_enabled() ) {
			$gallery .= ctmgs_get_lightbox_slider( $images, $instance );
		}
	
		$gallery .= "<script>
			jQuery(document).ready(function($){
				container = $('.masonry-gallery');
				container.masonry({
					itemSelector: '.item',
					columnWidth: '.grid-sizer',
					percentPosition: true
				});
				container.imagesLoaded().progress(function(){
					container.masonry('layout');
				});
			});
			</script>";
		}
	
		return $gallery;
	}
}


/**
 * Decide what image size to use
 * Either global default specified from plugin settings
 * Or specific value for given gallery
 * @since 1.0.0
 * @return String
 */
if( ! function_exists( 'ctmgs_get_default_size') ) {
	function ctmgs_get_default_size( $atts ) {
		// Size
		$size = 'thumbnail';
		// Check for global default
		$options = get_option( 'ctmgs_gallery_settings' );
		if( isset( $options['size'] ) && $options['size'] != 'none' ) {
			$size = $options['size'];
		} else if( isset( $atts['size'] ) ) {
			$size = $atts['size'];
		}
		
		return $size;
	}
}

/**
 * Return the link setting, either post, file or none
 * @since 1.0.0
 * @return String
 */
if( ! function_exists( 'ctmgs_get_link_setting') ) {
	function ctmgs_get_link_setting( $atts ) {
		if( ctmgs_is_lightbox_slider_enabled() ) {
			// If the slider is enabled, always return none
			return 'none';
		}
		// Link To
		$link_to = 'post';
		if( isset( $atts['link'] ) ) {
			$link_to = $atts['link'];
		}
		
		return $link_to;
	}
}



/**
 * Return the markup for each gallery image
 * @since 1.0.0
 * @param $image	Image object
 * @param $size		Size
 * @param $link_to	Either file, post, none
 * @return HTML
 */
if( ! function_exists( 'ctmgs_get_image_html') ) {
	function ctmgs_get_image_html( $image, $size, $link_to, $count ) {
		$image_html = '';
		$image_attributes = wp_get_attachment_image_src( $image, $size );
		// Decide if image is portrait or landscape
		$width = $image_attributes[1];
		$height = $image_attributes[2];
		$orientation = 'landscape';
		if( $height > $width ) {
			$orientation = 'portrait';
		}
		$image_html .= '<div data-id="' . esc_attr( $image ) . '" data-pos="' . esc_attr( $count ) . '" class="item ' . esc_attr( $orientation ) . '">';
		if( ctmgs_is_lightbox_slider_enabled() ) {
	//		$image_html .= '<a href="#' . esc_attr( $image ) . '">';
		}
		// Check for linking
		if( $link_to == 'file' ) {
			// Link to media
			$image_html .= wp_get_attachment_link( $image, $size, false );
		} else if( $link_to == 'post' ) {
			// Link to attachment page
			$image_html .= wp_get_attachment_link( $image, $size, true );
		} else {
			// No link
			$image_html .= '<img src="' . $image_attributes[0] . '">';
		}
		
		if( ctmgs_is_lightbox_slider_enabled() ) {
	//		$image_html .= '</a>';
		}
		$image_html .= '</div>';
		
		return $image_html;
	}
}

