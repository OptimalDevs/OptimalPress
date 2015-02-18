<?php
/**
 * The class that manage all shortcodes generator
 *
 * This class stores all "Shortcode Generator" created in an array.
 * Also embeds the generic css and js.
 */
class Optimalpress_Shortcode_Generator_Manager {
	
	/**
	 * List of all "shortcode generator"
	 * @var Array
	 */
	private $shortcode_generator_list;
	
	/**
	 * For check if can output in the current page
	 * @var Bool
	 */
	private $output;
	
	/**
	 * Dependecies for the shortcodes.
	 * @var Array
	 */
	private $fields_deps;
	
	/**
	 * Controls used in the shortcodes.
	 * @var Array
	 */
	private $controls_used;
	
	/**
	 * Array for localize in javascript needed for some internal functions.
	 * @var Array
	 */
	private $localize;
	
	/**
	 * Startup
	 *
	 * @param array	$metaboxes	Array that containing all "Shortcode generator". 
	 *							Optional parameter if the developer wants to send the array with all Metaboxes 
	 *							or otherwise use the method "add_metabox" to add one by one.
	 */
	public function __construct( $shortcode_generator_list = false ) {
			
		$this->output						= false;
		$this->fields_deps					= array();
		$this->shortcode_generator_list		= ( $shortcode_generator_list ) ? $shortcode_generator_list : array();
		
		add_action( 'current_screen', array( $this, 'render_shortcodes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
		add_action( 'in_admin_footer', array( $this, 'enqueue_footer' ) );
				
	}
	
	/**
	 * Add a shortcode to the shortcodes array
	 *
	 * @param string	$name			The name for the shortcode.
	 * @param array		$template 		Array containing each menu item. Each menu contains the controls / fields used in that menu.
	 * @param string	$modal_title 	Title for the shortcode generator modal, visible to user.
	 * @param string	$button_title	Button title.
	 * @param string	$main_image		Image for the button.
	 * @param string	$post_types		The post type ('post', 'page', 'dashboard', 'link', 'attachment' or 'custom_post_type')
	 */
	public function add_shortcode_generator( $name, $template, $modal_title = '', $button_title = '', $main_image, $post_types = array( 'post', 'page' ) ) {
		
		$this->shortcode_generator_list[]	= array(
			'name'			=> $name,
			'template'		=> $template,
			'modal_title'	=> $modal_title,
			'button_title'	=> $button_title,
			'main_image'	=> empty( $main_image ) ? OP_URL . '/core/img/op_shortcode_icon.png' : $main_image,
			'post_types'	=> $post_types,
		);
				
	}
	
	/**
	 * Render shortcodes in the page
	 *
	 * This method iterate all shortcodes and it call to Optimalpress Shortcode Generator Class that manage each shortcode.
	 * Also stores each control used and dependencies.
	 */
	public function render_shortcodes() {
		
		$screen = get_current_screen();

		foreach( $this->shortcode_generator_list as $shortcode_generator ) {
				
			if( ! in_array( $screen->id, $shortcode_generator['post_types'] ) ) {
				return false;
			}
			
			$new_shortcode_generator	= new Optimalpress_Shortcode_Generator( $shortcode_generator['name'], $shortcode_generator['template'], $shortcode_generator['modal_title'], $shortcode_generator['button_title'], $shortcode_generator['main_image'] );
		
			$this->output	= true;
			
			if( $new_shortcode_generator->get_deps() ) {
				
				$this->fields_deps = $new_shortcode_generator->get_deps();
			
			}
			
			if( $new_shortcode_generator->get_controls_used() ) {
				
				$this->controls_used[] = $new_shortcode_generator->get_controls_used();
			
			}
			
			$this->localize[] = array(
				'name'         	=> $shortcode_generator['name'],
				'modal_title'  	=> $shortcode_generator['modal_title'],
				'button_title' 	=> $shortcode_generator['button_title'],
				'main_image'   	=> $shortcode_generator['main_image'],
				'id'         	=> $shortcode_generator['name'] . '_modal',
				'dependencies'  => $this->fields_deps,
			);
			
		}		
		
	}
	
	/**
	 * Enqueue the common scripts and styles.
	 * 
	 */
	public function enqueue_scripts_styles() {
		
		if( $this->output ) {
		
			wp_enqueue_style( 'optimalpress-shortcode', OP_URL . '/core/css/shortcode.css', array(), '1.0' );
			wp_enqueue_style( 'optimalpress-controls', OP_URL . '/core/css/controls.css', array(), '1.0' );
			wp_enqueue_script( 'optimalpress-dependency-js', OP_URL . '/core/js/dependency.js', array( 'jquery' ), '1.0', true );
			wp_enqueue_script( 'optimalpress-shortcode-js', OP_URL . '/core/js/shortcode.js', array( 'jquery', 'optimalpress-dependency-js' ), '1.0', true );
			
			wp_localize_script( 'optimalpress-shortcode-js', 'op_sg', $this->localize );
		
		}
		
	}
	
	/**
	 * Enqueue some code needed for some controls.
	 *
	 * This enqueue some code in the footer only if the control is in use.
	 */
	public function enqueue_footer() {
		
		$fontawesome_included 	= false;
		$link_included 			= false;
		
		if( $this->output ) {
		
			foreach( $this->controls_used as $control_used ) {
			
				if( in_array( 'fontawesome', $control_used ) && ! $fontawesome_included ) {
					
					$fontawesome_included = true;
					
					$awesome_icon_list =  array( 'fa-glass', 'fa-music', 'fa-search', 'fa-envelope-o', 'fa-heart', 'fa-star', 'fa-star-o', 'fa-user', 'fa-film', 'fa-th-large', 'fa-th', 'fa-th-list', 'fa-check', 'fa-times', 'fa-search-plus', 'fa-search-minus', 'fa-power-off', 'fa-signal', 'fa-gear', 'fa-trash-o', 'fa-home', 'fa-file-o', 'fa-clock-o', 'fa-road', 'fa-download', 'fa-arrow-circle-o-down', 'fa-arrow-circle-o-up', 'fa-inbox', 'fa-play-circle-o', 'fa-rotate-right', 'fa-refresh', 'fa-list-alt', 'fa-lock', 'fa-flag', 'fa-headphones', 'fa-volume-off', 'fa-volume-down', 'fa-volume-up', 'fa-qrcode', 'fa-barcode', 'fa-tag', 'fa-tags', 'fa-book', 'fa-bookmark', 'fa-print', 'fa-camera', 'fa-font', 'fa-bold', 'fa-italic', 'fa-text-height', 'fa-text-width', 'fa-align-left', 'fa-align-center', 'fa-align-right', 'fa-align-justify', 'fa-list', 'fa-dedent', 'fa-indent', 'fa-video-camera', 'fa-photo', 'fa-pencil', 'fa-map-marker', 'fa-adjust', 'fa-tint', 'fa-edit', 'fa-share-square-o', 'fa-check-square-o', 'fa-arrows', 'fa-step-backward', 'fa-fast-backward', 'fa-backward', 'fa-play', 'fa-pause', 'fa-stop', 'fa-forward', 'fa-fast-forward', 'fa-step-forward', 'fa-eject', 'fa-chevron-left', 'fa-chevron-right', 'fa-plus-circle', 'fa-minus-circle', 'fa-times-circle', 'fa-check-circle', 'fa-question-circle', 'fa-info-circle', 'fa-crosshairs', 'fa-times-circle-o', 'fa-check-circle-o', 'fa-ban', 'fa-arrow-left', 'fa-arrow-right', 'fa-arrow-up', 'fa-arrow-down', 'fa-mail-forward', 'fa-expand', 'fa-compress', 'fa-plus', 'fa-minus', 'fa-asterisk', 'fa-exclamation-circle', 'fa-gift', 'fa-leaf', 'fa-fire', 'fa-eye', 'fa-eye-slash', 'fa-warning', 'fa-plane', 'fa-calendar', 'fa-random', 'fa-comment', 'fa-magnet', 'fa-chevron-up', 'fa-chevron-down', 'fa-retweet', 'fa-shopping-cart', 'fa-folder', 'fa-folder-open', 'fa-arrows-v', 'fa-arrows-h', 'fa-bar-chart-o', 'fa-twitter-square', 'fa-facebook-square', 'fa-camera-retro', 'fa-key', 'fa-gears', 'fa-comments', 'fa-thumbs-o-up', 'fa-thumbs-o-down', 'fa-star-half', 'fa-heart-o', 'fa-sign-out', 'fa-linkedin-square', 'fa-thumb-tack', 'fa-external-link', 'fa-sign-in', 'fa-trophy', 'fa-github-square', 'fa-upload', 'fa-lemon-o', 'fa-phone', 'fa-square-o', 'fa-bookmark-o', 'fa-phone-square', 'fa-twitter', 'fa-facebook', 'fa-github', 'fa-unlock', 'fa-credit-card', 'fa-rss', 'fa-hdd-o', 'fa-bullhorn', 'fa-bell', 'fa-certificate', 'fa-hand-o-right', 'fa-hand-o-left', 'fa-hand-o-up', 'fa-hand-o-down', 'fa-arrow-circle-left', 'fa-arrow-circle-right', 'fa-arrow-circle-up', 'fa-arrow-circle-down', 'fa-globe', 'fa-wrench', 'fa-tasks', 'fa-filter', 'fa-briefcase', 'fa-arrows-alt', 'fa-group', 'fa-chain', 'fa-cloud', 'fa-flask', 'fa-cut', 'fa-copy', 'fa-paperclip', 'fa-save', 'fa-square', 'fa-navicon', 'fa-list-ul', 'fa-list-ol', 'fa-strikethrough', 'fa-underline', 'fa-table', 'fa-magic', 'fa-truck', 'fa-pinterest', 'fa-pinterest-square', 'fa-google-plus-square', 'fa-google-plus', 'fa-money', 'fa-caret-down', 'fa-caret-up', 'fa-caret-left', 'fa-caret-right', 'fa-columns', 'fa-unsorted', 'fa-sort-down', 'fa-sort-up', 'fa-envelope', 'fa-linkedin', 'fa-rotate-left', 'fa-legal', 'fa-dashboard', 'fa-comment-o', 'fa-comments-o', 'fa-flash', 'fa-sitemap', 'fa-umbrella', 'fa-paste', 'fa-lightbulb-o', 'fa-exchange', 'fa-cloud-download', 'fa-cloud-upload', 'fa-user-md', 'fa-stethoscope', 'fa-suitcase', 'fa-bell-o', 'fa-coffee', 'fa-cutlery', 'fa-file-text-o', 'fa-building-o', 'fa-hospital-o', 'fa-ambulance', 'fa-medkit', 'fa-fighter-jet', 'fa-beer', 'fa-h-square', 'fa-plus-square', 'fa-angle-double-left', 'fa-angle-double-right', 'fa-angle-double-up', 'fa-angle-double-down', 'fa-angle-left', 'fa-angle-right', 'fa-angle-up', 'fa-angle-down', 'fa-desktop', 'fa-laptop', 'fa-tablet', 'fa-mobile-phone', 'fa-circle-o', 'fa-quote-left', 'fa-quote-right', 'fa-spinner', 'fa-circle', 'fa-mail-reply', 'fa-github-alt', 'fa-folder-o', 'fa-folder-open-o', 'fa-smile-o', 'fa-frown-o', 'fa-meh-o', 'fa-gamepad', 'fa-keyboard-o', 'fa-flag-o', 'fa-flag-checkered', 'fa-terminal', 'fa-code', 'fa-mail-reply-all', 'fa-star-half-empty', 'fa-location-arrow', 'fa-crop', 'fa-code-fork', 'fa-unlink', 'fa-question', 'fa-info', 'fa-exclamation', 'fa-superscript', 'fa-subscript', 'fa-eraser', 'fa-puzzle-piece', 'fa-microphone', 'fa-microphone-slash', 'fa-shield', 'fa-calendar-o', 'fa-fire-extinguisher', 'fa-rocket', 'fa-maxcdn', 'fa-chevron-circle-left', 'fa-chevron-circle-right', 'fa-chevron-circle-up', 'fa-chevron-circle-down', 'fa-html5', 'fa-css3', 'fa-anchor', 'fa-unlock-alt', 'fa-bullseye', 'fa-ellipsis-h', 'fa-ellipsis-v', 'fa-rss-square', 'fa-play-circle', 'fa-ticket', 'fa-minus-square', 'fa-minus-square-o', 'fa-level-up', 'fa-level-down', 'fa-check-square', 'fa-pencil-square', 'fa-external-link-square', 'fa-share-square', 'fa-compass', 'fa-toggle-down', 'fa-toggle-up', 'fa-toggle-right', 'fa-euro', 'fa-gbp', 'fa-dollar', 'fa-rupee', 'fa-cny', 'fa-ruble', 'fa-won', 'fa-bitcoin', 'fa-file', 'fa-file-text', 'fa-sort-alpha-asc', 'fa-sort-alpha-desc', 'fa-sort-amount-asc', 'fa-sort-amount-desc', 'fa-sort-numeric-asc', 'fa-sort-numeric-desc', 'fa-thumbs-up', 'fa-thumbs-down', 'fa-youtube-square', 'fa-youtube', 'fa-xing', 'fa-xing-square', 'fa-youtube-play', 'fa-dropbox', 'fa-stack-overflow', 'fa-instagram', 'fa-flickr', 'fa-adn', 'fa-bitbucket', 'fa-bitbucket-square', 'fa-tumblr', 'fa-tumblr-square', 'fa-long-arrow-down', 'fa-long-arrow-up', 'fa-long-arrow-left', 'fa-long-arrow-right', 'fa-apple', 'fa-windows', 'fa-android', 'fa-linux', 'fa-dribbble', 'fa-skype', 'fa-foursquare', 'fa-trello', 'fa-female', 'fa-male', 'fa-gittip', 'fa-sun-o', 'fa-moon-o', 'fa-archive', 'fa-bug', 'fa-vk', 'fa-weibo', 'fa-renren', 'fa-pagelines', 'fa-stack-exchange', 'fa-arrow-circle-o-right', 'fa-arrow-circle-o-left', 'fa-toggle-left', 'fa-dot-circle-o', 'fa-wheelchair', 'fa-vimeo-square', 'fa-turkish-lira', 'fa-plus-square-o', 'fa-space-shuttle', 'fa-slack', 'fa-envelope-square', 'fa-wordpress', 'fa-openid', 'fa-institution', 'fa-mortar-board', 'fa-yahoo', 'fa-google', 'fa-reddit', 'fa-reddit-square', 'fa-stumbleupon-circle', 'fa-stumbleupon', 'fa-delicious', 'fa-digg', 'fa-pied-piper-square', 'fa-pied-piper-alt', 'fa-drupal', 'fa-joomla', 'fa-language', 'fa-fax', 'fa-building', 'fa-child', 'fa-paw', 'fa-spoon', 'fa-cube', 'fa-cubes', 'fa-behance', 'fa-behance-square', 'fa-steam', 'fa-steam-square', 'fa-recycle', 'fa-automobile', 'fa-cab', 'fa-tree', 'fa-spotify', 'fa-deviantart', 'fa-soundcloud', 'fa-database', 'fa-file-pdf-o', 'fa-file-word-o', 'fa-file-excel-o', 'fa-file-powerpoint-o', 'fa-file-photo-o', 'fa-file-zip-o', 'fa-file-sound-o', 'fa-file-movie-o', 'fa-file-code-o', 'fa-vine', 'fa-codepen', 'fa-jsfiddle', 'fa-life-bouy', 'fa-circle-o-notch', 'fa-ra', 'fa-ge', 'fa-git-square', 'fa-git', 'fa-hacker-news', 'fa-tencent-weibo', 'fa-qq', 'fa-wechat', 'fa-send', 'fa-send-o', 'fa-history', 'fa-circle-thin', 'fa-header', 'fa-paragraph', 'fa-sliders', 'fa-share-alt', 'fa-share-alt-square', 'fa-bomb' ); 
					?>

					<div id="op-fontawesome-overlay">

						<div class="op-fontawesome-container">
							<input type="search" id="op-fontawesome-searcher" class="search-field" placeholder="<?php esc_attr_e( 'Search…', 'optimalpress-domain' ); ?>" value="" title="<?php esc_attr_e( 'Search for:', 'optimalpress-domain' ); ?>">

							<div class="op-fontawesome-list">
							
								<?php foreach( $awesome_icon_list as $icon ) : ?>
								
									<i class="fa <?php echo esc_attr( sanitize_html_class( $icon ) ); ?>" data-class-name="<?php echo esc_attr( $icon ); ?>"></i>
									
								<?php endforeach; ?>
								
							</div>
							
						</div>


					</div>

					<?php
				
				}
				
				if( in_array( 'link', $control_used ) && ! $link_included ) {
					
					$link_included = true;
					$search_panel_visible = '1' == get_user_setting( 'wplink', '0' ) ? ' class="search-panel-visible"' : '';

					?>
					
					<div id="op-wp-link-backdrop"></div>
					<div id="op-wp-link-wrap"<?php echo $search_panel_visible; ?>>
					<form id="op-wp-link" tabindex="-1">
					<?php wp_nonce_field( 'internal-linking', '_ajax_linking_nonce', false ); ?>
					<div id="op-link-modal-title">
						<?php _e( 'Insert/edit link' ) ?>
						<div id="op-wp-link-close" tabindex="0"></div>
					</div>
					<div id="op-link-selector">
						<div id="op-link-options">
							<p class="howto"><?php _e( 'Enter the destination URL' ); ?></p>
							<div>
								<label><span><?php _e( 'URL' ); ?></span><input id="op-url-field" type="text" name="href" /></label>
							</div>
							<div>
								<label><span><?php _e( 'Title' ); ?></span><input id="op-link-title-field" type="text" name="linktitle" /></label>
							</div>
							<div class="link-target">
								<label><span>&nbsp;</span><input type="checkbox" id="op-link-target-checkbox" /> <?php _e( 'Open link in a new window/tab' ); ?></label>
							</div>
						</div>
						<p class="howto" id="op-wp-link-search-toggle"><?php _e( 'Or link to existing content' ); ?></p>
						<div id="op-search-panel">
							<div class="link-search-wrapper">
								<label>
									<span class="search-label"><?php _e( 'Search' ); ?></span>
									<input type="search" id="op-search-field" class="link-search-field" autocomplete="off" />
									<span class="spinner"></span>
								</label>
							</div>
							<div id="op-search-results" class="query-results">
								<ul></ul>
								<div class="river-waiting">
									<span class="spinner"></span>
								</div>
							</div>
							<div id="op-most-recent-results" class="query-results">
								<div class="query-notice"><em><?php _e( 'No search term specified. Showing recent items.' ); ?></em></div>
								<ul></ul>
								<div class="river-waiting">
									<span class="spinner"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="submitbox">
						<div id="op-wp-link-update">
							<input type="submit" value="<?php esc_attr_e( 'Add Link' ); ?>" class="button button-primary" id="op-wp-link-submit" name="wp-link-submit">
						</div>
						<div id="op-wp-link-cancel">
							<a class="submitdelete deletion" href="#"><?php _e( 'Cancel' ); ?></a>
						</div>
					</div>
					</form>
					</div>
					
					<?php
				}
			
			}
			
		}
		
	}
		
}

?>