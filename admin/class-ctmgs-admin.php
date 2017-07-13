<?php
/**
 * Discussion Board admin class
*/

// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin admin class
 **/
if( ! class_exists( 'CTMGS_Admin' ) ) { // Don't initialise if there's already a Discussion Board activated
	
	class CTMGS_Admin {
		
		public function __construct() {
			//
		}
		
		/**
		 * Initialize the class and start calling our hooks and filters
		 * @since 1.0.0
		 */
		public function init() {
			add_action( 'admin_menu', array( $this, 'add_settings_submenu' ) );
			add_action( 'admin_init', array( $this, 'register_gallery_settings' ) );
			add_action( 'admin_init', array( $this, 'register_slider_settings' ) );
			add_action( 'admin_init', array( $this, 'register_lightbox_settings' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			// Filter new gallery defaults
			add_filter( 'media_view_settings', array( $this, 'filter_gallery_settings' ) );
		}
		
		public function filter_gallery_settings( $settings ) {
			$options = get_option( 'ctmgs_gallery_settings' );
			if( isset( $options['link_to'] ) ) {
				$settings['galleryDefaults']['link'] = $options['link_to'];
			}
			if( isset( $options['columns'] ) ) {
				$settings['galleryDefaults']['columns'] = $options['columns'];
			}
			return $settings;
		}
		
		public function enqueue_scripts() {
			wp_enqueue_style( 'ctmgs-admin-style', CTMGS_PLUGIN_URL . 'assets/css/admin-style.css' );
		}
		
		// Add the menu item
		public function add_settings_submenu() {
			add_options_page( __( 'Masonry Gallery', 'ctmgs' ), __( 'Masonry Gallery', 'ctmgs' ), 'manage_options', 'ctmgs', array( $this, 'options_page' ) );
		}
		
		public function register_gallery_settings() {
			register_setting( 'ctmgs_gallery', 'ctmgs_gallery_settings' );
			
			add_settings_section(
				'ctmgs_gallery_section', 
				__( 'Gallery Settings', 'ctmgs' ),
				array( $this, 'gallery_settings_section_callback' ), 
				'ctmgs_gallery'
			);
			
			// Check defaults
			$options = get_option( 'ctmgs_gallery_settings' );
			if( false === $options ) {
				$options = $this->get_default_gallery_settings();
				update_option( 'ctmgs_gallery_settings', $options );
			}
			
			$gallery_settings = ctmgs_gallery_page_settings();
			if( ! empty( $gallery_settings ) ) {
				foreach( $gallery_settings as $gallery_setting ) {
					add_settings_field( 
						$gallery_setting['id'], 
						$gallery_setting['label'], 
						array( $this, $gallery_setting['callback'] ),
						'ctmgs_gallery',
						'ctmgs_gallery_section',
						$gallery_setting
					);
				}
			}
			
		}		
		
		public function get_default_gallery_settings() {
			$defaults = array(
				'gallery_format'			=> 'masonry',
				'columns'					=> 3,
				'size'						=> 'none',
				'link_to'					=> 'none',
				'existing_gallery_format'	=> 'default'
			);
			return $defaults;
		}
		
		public function register_slider_settings() {
			register_setting( 'ctmgs_slider', 'ctmgs_slider_settings' );
			
			add_settings_section(
				'ctmgs_slider_section', 
				__( 'Gallery Slider Settings', 'ctmgs' ),
				array( $this, 'slider_settings_section_callback' ), 
				'ctmgs_slider'
			);
			
			// Check defaults
			$options = get_option( 'ctmgs_slider_settings' );
			if( false === $options ) {
				$options = $this->get_default_slider_settings();
				update_option( 'ctmgs_slider_settings', $options );
			}
			
			$slider_settings = ctmgs_slider_page_settings();
			if( ! empty( $slider_settings ) ) {
				foreach( $slider_settings as $slider_setting ) {
					add_settings_field( 
						$slider_setting['id'], 
						$slider_setting['label'], 
						array( $this, $slider_setting['callback'] ),
						'ctmgs_slider',
						'ctmgs_slider_section',
						$slider_setting
					);
				}
			}
			
		}		
		
		public function get_default_slider_settings() {
			$defaults = array(
			//	'enable_slider'		=> 1
			);
			return $defaults;
		}
		
		public function register_lightbox_settings() {
			register_setting( 'ctmgs_lightbox', 'ctmgs_lightbox_settings' );
			
			add_settings_section(
				'ctmgs_lightbox_section', 
				__( 'Lightbox Slider Settings', 'ctmgs' ),
				array( $this, 'lightbox_settings_section_callback' ), 
				'ctmgs_lightbox'
			);
			
			// Check defaults
			$options = get_option( 'ctmgs_lightbox_settings' );
			if( false === $options ) {
				$options = $this->get_default_lightbox_settings();
				update_option( 'ctmgs_lightbox_settings', $options );
			}
			
			$lightbox_settings = ctmgs_lightbox_page_settings();
			if( ! empty( $lightbox_settings ) ) {
				foreach( $lightbox_settings as $lightbox_setting ) {
					add_settings_field( 
						$lightbox_setting['id'], 
						$lightbox_setting['label'], 
						array( $this, $lightbox_setting['callback'] ),
						'ctmgs_lightbox',
						'ctmgs_lightbox_section',
						$lightbox_setting
					);
				}
			}
			
		}		
		
		public function get_default_lightbox_settings() {
			$defaults = array(
				'enable_slider'		=> 1
			);
			return $defaults;
		}
	
		// Callback for header setting
		public function page_header_callback( $args ) {
			$options = get_option( $args['section'] );
			$value = '';
			if( isset( $options[$args['id']] ) ) {
				// Ensure value is prefixed with #
				$value = '#' . str_replace( '#', '', $options[$args['id']] );
			}
		}
		
		// Callback for pages select field
		public function pages_select_callback( $args ) {
			$options = get_option( $args['section'] );
			$value = '';
			if( isset( $options[$args['id']] ) ) {
				$value = $options[$args['id']];
			}
			// Get all pages
			$pages = get_pages();
			
			// Iterate through the pages
			if( $pages ) { ?>
				<select name='<?php echo $args['section']; ?>[<?php echo $args['id']; ?>]'>
					<option></option>
					<?php foreach( $pages as $page ) { ?>
						<option value='<?php echo $page->ID; ?>' <?php selected( $value, $page->ID ); ?>><?php echo $page->post_title; ?></option>
					<?php } ?>
				</select>
			<?php }
			if( isset( $args['description'] ) ) { ?>
				<p class="description"><?php echo $args['description']; ?></p>
			<?php }
		}
		
		public function page_header_render() {
		}
		
		/**
		 * Checkbox callback
		 * @since 2.2.1
		 */
		public function checkbox_callback( $args ) {
			$options = get_option( $args['section'] );
			$value = '';
			if( isset( $options[$args['id']] ) ) {
				$value = $options[$args['id']];
			}
			$checked  = ! empty( $value ) ? checked( 1, $value, false ) : '';
			?>
			<input type='checkbox' name="<?php echo $args['section']; ?>[<?php echo $args['id']; ?>]" <?php echo $checked; ?> value='1'>
			<?php
			if( isset( $args['description'] ) ) { ?>
				<p class="description"><?php echo $args['description']; ?></p>
			<?php }
		}
		
		public function text_callback( $args ) {
			$options = get_option( $args['section'] );
			$value = '';
			if( isset( $options[$args['id']] ) ) {
				$value = $options[$args['id']];
			}
			?>
			<input type='text' name="<?php echo $args['section']; ?>[<?php echo $args['id']; ?>]" value="<?php echo esc_attr( $value ); ?>" />
			<?php if( isset( $args['description'] ) ) { ?>
				<p class="description"><?php echo $args['description']; ?></p>
			<?php }
		}
		
		public function wysiwyg_callback( $args ) {
			$options = get_option( $args['section'] );
			$value = '';
			if( isset( $options[$args['id']] ) ) {
				$value = $options[$args['id']];
			}
			$name = $args['section'] . '[' . $args['id'] . ']';
			wp_editor( 
				$value,
				$args['id'],
				array( 
					'textarea_name' => $name,
					'media_buttons'	=> false,
					'wpautop'		=> false,
					'tinymce'		=> true,
					'quicktags'		=> true,
					'textarea_rows'	=> 5
				) 
			);
			if( isset( $args['description'] ) ) { ?>
				<p class="description"><?php echo $args['description']; ?></p>
			<?php }
		}
		
		public function select_callback( $args ) {
			$options = get_option( $args['section'] );
			$setting = '';
			if( isset( $options[$args['id']] ) ) {
				$setting = $options[$args['id']];
			}
			?>
				<select name="<?php echo $args['section']; ?>[<?php echo $args['id']; ?>]">
					<?php foreach( $args['choices'] as $key=>$value ) { ?>
						<option value="<?php echo $key; ?>" <?php selected( $setting, $key ); ?>><?php echo $value; ?></option>
					<?php } ?>
				</select>
			<?php
			if( isset( $args['description'] ) ) { ?>
				<p class="description"><?php echo $args['description']; ?></p>
			<?php }
		}
		
		public function email_callback( $args ) {
			$options = get_option( $args['section'] );
			$value = '';
			if( isset( $options[$args['id']] ) ) {
				$value = $options[$args['id']];
			}
			?>
			<input type='email' name="<?php echo $args['section']; ?>[<?php echo $args['id']; ?>]" value="<?php echo esc_attr( $value ); ?>" />
			<?php
		}

		
		public function gallery_settings_section_callback() { 
			echo '<p>' . __( 'This will affect settings for any new galleries you add. Any galleries that have already been added before this plugin was activated will use their existing settings.', 'ctmgs' ) . '</p>';
		//	echo '<p>' . __( 'Do you have a few seconds to <a target="_blank"  href="https://translate.wordpress.org/projects/wp-plugins/wp-discussion-board/stable">help with translating Discussion Board into other languages</a>? Even if you just translated a couple of words, that would really help.', 'ctmgs' ) . '</p>';
		}
		
		public function slider_settings_section_callback() { 
			echo '<p>' . __( 'These settings apply to the gallery slider.', 'ctmgs' ) . '</p>';
		//	echo '<p>' . __( 'Do you have a few seconds to <a target="_blank"  href="https://translate.wordpress.org/projects/wp-plugins/wp-discussion-board/stable">help with translating Discussion Board into other languages</a>? Even if you just translated a couple of words, that would really help.', 'ctmgs' ) . '</p>';
		}
		
		public function lightbox_settings_section_callback() { 
			echo '<p>' . __( 'These settings apply to the lightbox slider that displays when the user clicks an image in a masonry gallery.', 'ctmgs' ) . '</p>';
		//	echo '<p>' . __( 'Do you have a few seconds to <a target="_blank"  href="https://translate.wordpress.org/projects/wp-plugins/wp-discussion-board/stable">help with translating Discussion Board into other languages</a>? Even if you just translated a couple of words, that would really help.', 'ctmgs' ) . '</p>';
		}
		
		
		public function options_page() {
			$current = isset( $_GET['tab'] ) ? $_GET['tab'] : 'gallery';
			$title =  __( 'Masonry Gallery Slider', 'ctmgs' );
			$tabs = array(
				'gallery'	=>	__( 'Masonry Gallery', 'ctmgs' ),
				'slider'	=>	__( 'Gallery Slider', 'ctmgs' ),
				'lightbox'	=>	__( 'Lightbox Slider', 'ctmgs' ),
			);
			$tabs = apply_filters( 'ctmgs_settings_tabs', $tabs );
			?>			
			<div class="wrap">
				<h1><?php echo $title; ?></h1>
				<div class="ctmgs-outer-wrap">
					<div class="ctmgs-inner-wrap">
						<h2 class="nav-tab-wrapper">
							<?php foreach( $tabs as $tab => $name ) {
								$class =( $tab == $current ) ? ' nav-tab-active' : '';
								echo "<a class='nav-tab$class' href='?page=ctmgs&tab=$tab'>$name</a>";
							} ?>
						</h2>
						
						<form action='options.php' method='post'>
							<?php
							settings_fields( 'ctmgs_' . strtolower( $current ) );
							do_settings_sections( 'ctmgs_' . strtolower( $current ) );
							submit_button();
							?>
						</form>
					</div><!-- .ctmgs-inner-wrap -->
					<div class="ctmgs-banners">
						<div class="ctmgs-banner hide-dbpro">
							<a target="_blank" href="https://discussionboard.pro/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=ctdb&utm_campaign=dbpro"><img src="<?php echo CTMGS_PLUGIN_URL . 'assets/images/discussion-board-banner-ad.png'; ?>" alt="" ></a>
						</div>
						<div class="ctmgs-banner">
							<a target="_blank" href="http://superheroslider.catapultthemes.com/?utm_source=plugin_ad&utm_medium=wp_plugin&utm_content=ctdb&utm_campaign=superhero"><img src="<?php echo CTMGS_PLUGIN_URL . 'assets/images/shs-banner-ad.png'; ?>" alt="" ></a>
						</div>
						<div class="ctmgs-banner">
							<a target="_blank" href="https://wordpress.org/plugins/restrictly/"><img src="<?php echo CTMGS_PLUGIN_URL . 'assets/images/restrictly-banner-ad.png'; ?>" alt="" ></a>
						</div>			
					</div>
				</div><!-- .ctmgs-outer-wrap -->
			</div><!-- .wrap -->
			<?php
		}
		
	}
	
}

function ctmgs_admin_init() {
	$CTMGS_Admin = new CTMGS_Admin();
	$CTMGS_Admin->init();
	do_action( 'ctmgs_init' );
}
add_action( 'plugins_loaded', 'ctmgs_admin_init' );
