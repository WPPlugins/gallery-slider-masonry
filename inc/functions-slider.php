<?php
/**
 * Slider functions
 */

/**
 * Is the lightbox slider enabled?
 * @since 1.0.0
 * @return Boolean
 */
if( ! function_exists( 'ctmgs_is_lightbox_slider_enabled' ) ) {
	function ctmgs_is_lightbox_slider_enabled() {
		$options = get_option( 'ctmgs_lightbox_settings' );
		if( ! empty( $options['enable_slider'] ) ) {
			return true;
		}
		return false;
	}
}

/**
 * The markup for the gallery slider
 * @since 1.0.0
 * @param $images	Array of image IDs in gallery
 * @return HTML
 */
if( ! function_exists( 'ctmgs_get_slider_gallery' ) ) {
	function ctmgs_get_slider_gallery( $output='', $atts, $instance ) {

		// Filter the $atts in case you want to override some of the settings below
		$atts = apply_filters( 'ctmgs_filter_slider_gallery_atts', $atts, $instance );
		
		// Link To
		$link_to = ctmgs_get_link_setting( $atts );
	
		// Size
		$size = ctmgs_get_default_size( $atts );
	
		// Get the image IDs
		$images = explode( ',', $atts['ids'] );
	
		// Gallery classes
		$classes = array( 'slider-gallery' );
		
		// Get some additional gallery settings
		$force_image_height = "false";
		$items = 1;
		if( isset( $atts['ctmgs_force_image_height'] ) ) {
			$force_image_height = $atts['ctmgs_force_image_height'];
		}
		if( isset( $atts['ctmgs_items'] ) ) {
			$items = $atts['ctmgs_items'];
		}
		
		$autowidth = "false";
		$autoheight = "true";
		if( isset( $atts['ctmgs_force_image_height'] ) && $force_image_height == 'true' ) {
			// If we're forcing the image height, we need to set number of items to 1 and enforce auto_width
			$items = 1;
			$autowidth = "true";
			$autoheight = "false";
			$classes[] = 'force-image-height';
		}

		$gallery = '';
	
		if( $images ) {
			// Use this to find the shortest height
			$heights = array();
			$count = 0;
			$gallery .= '<div class="slider-gallery-wrapper ">';
				$gallery .= '<div class="' . join( ' ', $classes ) . '">';
				$gallery .= '<div id="ctmgs-carousel-' . esc_attr( $instance ) . '" class="owl-carousel">';
				if( ! empty( $images ) ) {
					foreach( $images as $image ) {
						$image_attributes = wp_get_attachment_image_src( $image, $size );
						$gallery .= '<div class="ctmgs-owl-item-inner">';
						$gallery .= '<img src="' . $image_attributes[0] . '">';
						// Add the height to our array
						if( $image_attributes[2] > 0 ) {
							$heights[] = $image_attributes[2];
						}
						$gallery .= '</div>';
					}
				}
				$gallery .= '</div><!-- .owl-carousel -->';
				$gallery .= '</div><!-- .slider-gallery -->';
			$gallery .= '</div><!-- .slider-gallery-wrapper -->';
			
			// We'll set the carousel to this height
			$min_height = min( $heights );
			
			// Get some global settings
			$slider_options = get_option( 'ctmgs_slider_settings' );
			$margin = 0;
			if( isset( $slider_options['slider_margin'] ) ) {
				$margin = $slider_options['slider_margin'];
			}
	
			$gallery .= "<script>
			jQuery(document).ready(function($){
				carousel = $('#ctmgs-carousel-" . esc_attr( $instance ) . "');\n";
				
				if( $force_image_height == 'true' ) {
					$gallery .= "carousel.css('height'," . esc_attr( $min_height ) . ");\n";
				}
				
				$gallery .= "carousel.owlCarousel({
					items: " . absint( $items ) . ",
					stagePadding: 0,
					nav: true,
					navText: ['<span></span><span></span>','<span></span><span></span>'],
					center: true,
					loop: true,
					margin: " . esc_attr( $margin ) . ",
					autoWidth: " . esc_attr( $autowidth ) . ",
					autoHeight: " . esc_attr( $autoheight ) . ",
					stageOuterClass: 'ctmgs-stage-outer',
					stageClass: 'ctmgs-stage',
					navContainerClass: 'ctmgs-nav',
					controlsClass: 'ctmgs-controls',
					dotsClass: 'ctmgs-dots',
					dotClass: 'ctmgs-dot',
					onInitialized: function(event){
						// Nothing here...
					}
				});
			});
			</script>";
		}
	
		return $gallery;
		
	}
}

/**
 * The markup for the lightbox slider
 * @since 1.0.0
 * @param $images	Array of image IDs in gallery
 * @param $instance	Gallery ID
 * @return HTML
 */
if( ! function_exists( 'ctmgs_get_lightbox_slider' ) ) {
	function ctmgs_get_lightbox_slider( $images, $instance ) {
		$slider_html = '<div class="ctmgs-slider-background ctmgs-slider-close"></div>';
		$slider_html .= '<div id="ctmgs-carousel-' . esc_attr( $instance ) . '-wrapper" class="ctmgs-slider-wrapper force-image-height">';
		$slider_html .= '<div class="owl-carousel">';
		if( ! empty( $images ) ) {
			foreach( $images as $image ) {
				$image_attributes = wp_get_attachment_image_src( $image, 'large' );
			//	$slider_html .= '<div class="ctmgs-owl-item-inner" data-hash="' . esc_attr( $image ) . '">';
				$slider_html .= '<div class="ctmgs-owl-item-inner">';
				$slider_html .= '<img src="' . $image_attributes[0] . '">';
				$slider_html .= '</div>';
			}
		}
		$slider_html .= '</div><!-- .owl-carousel -->';
		$slider_html .= '<div class="ctmgs-slider-exit ctmgs-slider-close"><span></span><span></span></div>';
		$slider_html .= '</div><!-- .ctmgs-slider-wrapper -->';
		
		// Apply any filters before the script
		$slider_html = apply_filters( 'ctmgs_filter_slider_html', $slider_html, $images );
		
		// Get the lightbox script
		$slider_html .= ctmgs_get_lightbox_slider_script( $instance );
		
		return $slider_html;
	}
}

/**
 * If we are displaying the lightbox slider, add a class to the body to keep it hidden and avoid nasty artifacts
 * @since 1.0.0
 * @param $classes
 * @return Array
 */
if( ! function_exists( 'ctmgs_filter_body_class' ) ) {
	function ctmgs_filter_body_class( $classes ) {
		if( ctmgs_is_lightbox_slider_enabled() ) {
			$classes[] = 'ctmgs-slider-hidden';
		}
		return $classes;
	}
}
add_filter( 'body_class', 'ctmgs_filter_body_class' );

/**
 * The script for the lightbox slider
 * @since 1.0.0
 * @param $instance	Gallery ID
 * @return HTML
 */
if( ! function_exists( 'ctmgs_get_lightbox_slider_script' ) ) {
	function ctmgs_get_lightbox_slider_script( $instance ) {
		$script = '<script>
		jQuery(document).ready(function($){
			var scrollPos; // Ensure we return user back to same position on page
			background = $(".ctmgs-slider-background");
			wrapper = $("#ctmgs-carousel-' . esc_attr( $instance ) . '-wrapper");
			carousel = $("#ctmgs-carousel-' . esc_attr( $instance ) . '-wrapper .owl-carousel");
			wrapper_height = $(window).height()*.8;
			top_height = $(window).height()*.1;
			$("#ctmgs-carousel-' . esc_attr( $instance ) . '-wrapper .ctmgs-owl-item-inner,#ctmgs-carousel-' . esc_attr( $instance ) . '-wrapper,#ctmgs-carousel-' . esc_attr( $instance ) . '-wrapper .ctmgs-stage-outer").css("height",wrapper_height);
			carousel.owlCarousel({
				items: 1,
				stagePadding: 64,
				nav: true,
				navText: [\'<span></span><span></span>\',\'<span></span><span></span>\'],
				center: true,
				loop: true,
				margin: 32,
				autoWidth: true,
				URLhashListener: false,
				dots: true,
		//		startPosition: \'URLHash\',
		//		stageOuterClass: \'ctmgs-stage-outer\',
		//		stageClass: \'ctmgs-stage\',
		//		navContainerClass: \'ctmgs-nav\',
		//		controlsClass: \'ctmgs-controls\',
		//		dotsClass: \'ctmgs-dots\',
		//		dotClass: \'ctmgs-dot\',
				onInitialized: function(){
					updatePos();
				}
			});
			function updatePos(event){
				$("body").addClass("ctmgs-slider-hidden");
			}
			$("#masonry-gallery-' . esc_attr( $instance ) . ' .item").on("click",function(){
				scrollPos = $("body").scrollTop();
				$("body").scrollTop(0); // Need to return to top of page to view slider
				position = ($(this).data("pos"));
				$("#ctmgs-carousel-' . esc_attr( $instance ) . '-wrapper .owl-carousel").trigger( "to.owl.carousel", [position,300,true] );
				background.attr("style","");
				wrapper.attr("style","");
				wrapper.css("height",wrapper_height);
				$("#ctmgs-carousel-' . esc_attr( $instance ) . '-wrapper").addClass("ctmgs-lightbox-activated");
				$("#ctmgs-carousel-' . esc_attr( $instance ) . '-wrapper.ctmgs-lightbox-activated").css("top",top_height);
				$("body").addClass("ctmgs-slider-active");
				$("body").removeClass("ctmgs-slider-hidden");
			});
			$(".ctmgs-slider-close").on("click",function(){
				$("body").scrollTop(scrollPos);
				background.animate({
					opacity: 0
					}, 500, function(){
					$("body").addClass("ctmgs-slider-hidden");
					$("body").removeClass("ctmgs-slider-active");
					$("#ctmgs-carousel-' . esc_attr( $instance ) . '-wrapper").removeClass("ctmgs-lightbox-activated");
					$("#ctmgs-carousel-' . esc_attr( $instance ) . '-wrapper").css("top",-9000);
				});
				wrapper.animate({ opacity: 0 }, 250 );
			});
		});
		</script>';
		$script = apply_filters( 'ctmgs_filter_lightbox_slider_script', $script );
		return $script;
	}
}