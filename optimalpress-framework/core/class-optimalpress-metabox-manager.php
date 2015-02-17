<?php
/**
 * The class that manage all metaboxes.
 *
 * This class stores all Metaboxes created in an array.
 * Also embeds the generic css and js for Metaboxes.
 */
 
class Optimalpress_Metabox_Manager {
	
	/**
	 * List of all metaboxes.
	 * @var Array
	 */
	private $metaboxes;
	
	/**
	 * For check if can output in the current page.
	 * @var Bool
	 */
	private $output;
	
	/**
	 * Dependecies for the metaboxes.
	 * @var Array
	 */
	private $fields_deps;
	
	/**
	 * Controls used in the metaboxes.
	 * @var Array
	 */
	private $controls_used;
	
	/**
	 * Startup
	 *
	 * @param array	$metaboxes	Array that containing all Metaboxes. 
	 *							Optional parameter if the developer wants to send the array with all Metaboxes 
	 *							or otherwise use the method "add_metabox" to add one by one.
	 */
	public function __construct( $metaboxes = false ) {
			
		$this->output				= false;
		$this->fields_deps			= array();
		$this->metaboxes			= ( $metaboxes ) ? $metaboxes : array();
		
		add_action( 'current_screen', array( $this, 'render_metaboxes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
		add_action( 'in_admin_footer', array( $this, 'enqueue_footer' ) );
				
	}
	
	/**
	 * Add metabox to the metaboxes array
	 *
	 * @param string	$metabox_id			The unique id for the metabox.
	 * @param string	$metabox_title 		Title for the metabox, visible to user.
	 * @param array		$metabox_fields 	Controls list used in the metabox.
	 * @param string	$metabox_context	The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side').
	 * @param string	$metabox_priority	The priority within the context where the boxes should show ('high', 'core', 'default' or 'low')
	 */
	public function add_metabox( $metabox_id, $metabox_title, $metabox_post_types, $metabox_fields, $metabox_context = 'advanced', $metabox_priority = 'default' ) {
		
		$this->metaboxes[]	= array(
			'id'			=> $metabox_id,
			'title'			=> $metabox_title,
			'post_types'	=> $metabox_post_types,
			'fields'		=> $metabox_fields,
			'context'		=> $metabox_context,
			'priority'		=> $metabox_priority,
		);
				
	}
	
	/**
	 * Render metaboxes in the page
	 *
	 * This method iterate all metaboxes and it call to Optimalpress Metabox Class that manage each metabox.
	 * Also stores each control used and dependencies.
	 */
	public function render_metaboxes() {
		
		$screen = get_current_screen();

		foreach( $this->metaboxes as $metabox ) {
			
			//Check if we are in the correct place
			if( ! in_array( $screen->id, $metabox['post_types'] ) ) {
				return false;
			}
			
			$new_metabox	= new Optimalpress_Metabox( $metabox['id'], $metabox['title'], $metabox['fields'], $metabox['context'], $metabox['priority'] );
		
			$this->output	= true;
			
			if( $new_metabox->get_deps() ) {
				
				$this->fields_deps[] = array(
					'id'			=> $metabox['id'],
					'dependencies'	=> $new_metabox->get_deps()
				);
				
			}
			
			if( $new_metabox->get_controls_used() ) {
				
				$this->controls_used[] = $new_metabox->get_controls_used();
			
			}
			
		}		
		
	}
	
	/**
	 * Enqueue the common scripts and styles for metaboxes.
	 * 
	 */
	public function enqueue_scripts_styles() {
		
		if( $this->output ) {
		
			wp_enqueue_style( 'optimalpress-metabox', OP_URL . '/core/css/metabox.css', array(), '1.0' );
			wp_enqueue_style( 'optimalpress-controls', OP_URL . '/core/css/controls.css', array(), '1.0' );
			wp_enqueue_script( 'optimalpress-dependency-js', OP_URL . '/core/js/dependency.js', array( 'jquery' ), '1.0', true );
			wp_enqueue_script( 'optimalpress-metabox-js', OP_URL . '/core/js/metabox.js', array( 'jquery', 'optimalpress-dependency-js' ), '1.0', true );
			
			if( ! empty( $this->fields_deps ) ) {
				
				wp_localize_script( 'optimalpress-metabox-js', 'op_metabox', $this->fields_deps );
				
			}
		
		}
		
	}
	
	/**
	 * Enqueue some code needed for some controls.
	 *
	 * This enqueue some code in the footer only if the control is in use.
	 *
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