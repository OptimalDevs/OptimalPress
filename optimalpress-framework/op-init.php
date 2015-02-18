<?php

if ( ! class_exists( 'Optimalpress_Init' ) ) {
	
	/**
	 * OptimalPress Framework init
	 *
	 * In this class we require all framework core classes and functions.
	 * All constants needed are defined here.
	 */
	class Optimalpress_Init {

		public function __construct() {
			
			require_once( 'user-functions.php');
			
			if( is_admin() ) {
				
				$this->op_admin_init();
				
			}
			
		}
		
		public function op_admin_init() {
		
			/*
			 * Require all core classes
			 */
			require_once( 'core/class-optimalpress-control.php');
			require_once( 'core/class-optimalpress-metabox.php');
			require_once( 'core/class-optimalpress-metabox-manager.php');
			require_once( 'core/class-optimalpress-options-page.php');
			require_once( 'core/class-optimalpress-shortcode-generator.php');
			require_once( 'core/class-optimalpress-shortcode-generator-manager.php');
						
			/* 
			 * Defining all constants
			 */
			$content_url	= untrailingslashit( dirname( dirname( get_stylesheet_directory_uri() ) ) );
			$content_dir	= untrailingslashit( dirname( dirname( get_stylesheet_directory() ) ) );
			$file 			= str_replace( '\\', '/', __DIR__ );
			$content_dir 	= str_replace( '\\', '/', $content_dir );
			$current_url 	= str_replace( $content_dir, $content_url, $file );
			
			define( 'OP_NONCE_SECURITY', 'optimalpress-nonce-security' );
			define( 'OP_PATH', untrailingslashit( __DIR__ ) );
			define( 'OP_URL', $current_url );
			define( 'OP_VERSION', 'alpha' );
			define( 'OP_DOMAIN', 'op-domain' );
		
		}

		/**
		 * Define the "Custom Controls" folder.
		 *
		 * @param string	$path	Path to the custom controls folder.
		 * @param string	$url 	URL to the custom controls folder (Optional).
		 */
		public function set_custom_controls_constants( $path, $url = '' ) {
			
			define( 'OP_CUSTOM_CONTROLS_PATH', $path );
			define( 'OP_CUSTOM_CONTROLS_URL', $url );
			
		}
		
	}

	$optimalpress_init = new Optimalpress_Init();

}

?>