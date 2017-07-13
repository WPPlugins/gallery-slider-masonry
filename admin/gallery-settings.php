<?php
/*
 * Functions and data for adding settings to the Gallery template
 */

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds some extra options to the Gallery Settings
 * @since 1.0.0
 */
if( ! function_exists( 'ctmgs_gallery_settings' ) ) {
	function ctmgs_gallery_settings() { ?>
		<script type="text/html" id="tmpl-ctmgs_view_select">
			<label class="setting">
				<h3><?php _e( 'Layout Settings', 'ctmgs' ); ?></h3>
			</label>

			<div id="ctmgs_view_settings" >

				<label class="setting">
					<span><?php _e( 'Layout', 'ctmgs' ); ?></span>
					<select data-setting="ctmgs_gallery_layout" name="ctmgs_gallery_layout" id="ctmgs_gallery_layout">
						<option value="masonry"><?php _e( 'Masonry', 'ctmgs' ); ?></option>
						<option value="slider"><?php _e( 'Slider', 'ctmgs' ); ?></option>
						<option value="default"><?php _e( 'Default', 'ctmgs' ); ?></option>
					</select>
				</label>

			</div>

		</script>
		
		<script type="text/html" id="tmpl-ctmgs_view_slider">
			<label class="setting">
				<h3><?php _e( 'Slider Settings', 'ctmgs' ); ?></h3>
			</label>
			<p class="description"><?php _e( 'Only applies if Slider chosen as Layout above. If you select Force Image Height, this will override the Visible Items setting.', 'ctmgs' ); ?></p>

			<div id="ctmgs_slider_settings" >
				
				<label class="setting">
					<span><?php _e( 'Force Image Height', 'ctmgs' ); ?></span>
					<input type="checkbox" data-setting="ctmgs_force_image_height" name="ctmgs_force_image_height" id="ctmgs_force_image_height">
				</label>
				
				<label class="setting">
					<span><?php _e( 'Visible Items', 'ctmgs' ); ?></span>
					<select data-setting="ctmgs_items" name="ctmgs_items" id="ctmgs_items">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
					</select>
				</label>

			</div>

		</script>
 
		<script>
			jQuery(document).ready(function(){
				
				// Add attribute to defaults
				_.extend(wp.media.gallery.defaults, {
					ctmgs_gallery_layout: 'masonry',
					ctmgs_items: '1',
				});
				// merge default gallery settings template with yours
				wp.media.view.Settings.Gallery = wp.media.view.Settings.Gallery.extend({
					template: function(view){
						return wp.media.template('gallery-settings')(view)
						+ wp.media.template('ctmgs_view_select')(view)
						+ wp.media.template('ctmgs_view_slider')(view);   
					}
				});
			});
		</script>
	<?php }
}
add_action( 'print_media_templates', 'ctmgs_gallery_settings' );