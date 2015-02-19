<?php

/**
 * Class for create an Option page.
 *
 * This class is responsible for creating the theme options page.
 * Create the structure that will contain all controls.
 * Store the dependencies and controls used.
 * Display and save the options
 */
class Optimalpress_Options_Page {
    
	/**
	 * Unique key for save option in DB
	 * @var String
	 */
	private $option_key;
	/**
	 * The text to be displayed in the title tags of the page when the menu is selected
	 * @var String
	 */
	private $page_title;
	
	/**
	 * The text to be used for the wordpress menu.
	 * @var String
	 */
	private $menu_title;
	
	/**
	 * The The capability required for this menu to be displayed to the user.
	 * @var String
	 */
	private $capability;
	
	/**
	 * The slug name to refer to this menu by (should be unique for this menu).
	 * @var String
	 */
	private $page_slug;
	
	/**
	 * Title that will appear at the beginning of the page
	 * @var String
	 */
	private $header_title;
	
	/**
	 * Array containing each menu item. Each menu contains the controls / fields used in that menu.
	 * @var Array
	 */
	private $template;
	
	/**
	 * Array containing all options previously stored in the database.
	 * @var Array
	 */	
	private $old_options;
	
	/**
	 * Array containing the array of instantiated menus and controls.
	 * @var ArrayObject
	 */	
	private $menus_inst;
	
	/**
	 * Array containing the array of instantiated controls.
	 * @var ArrayObject
	 */	
	private $controls_inst;
	
	/**
	 * Array containing all dependencies.
	 * @var Array
	 */	
	private $deps;
	
	/**
	 * Array containing all the controls used.
	 * @var Array
	 */	
	private $controls_used;
		
    /**
     * Start up
	 *
	 * @param string	$option_key		The unique id for the options.
	 * @param string	$page_title 	The text to be displayed in the title tags of the page when the menu is selected.
	 * @param string	$menu_title 	The text to be used for the WordPress menu.
	 * @param string	$capability		The capability required for this menu to be displayed to the user.
	 * @param string	$page_slug		The slug name to refer to this menu by (should be unique for this menu).
	 * @param string	$header_title 	Title for the option page, visible to user.
	 * @param array 	$template 		Array containing each menu item. Each menu contains the controls / fields used in that menu.
	 */
    public function __construct( $option_key, $page_title, $menu_title, $capability, $page_slug, $header_title, $template ) {
		
		$this->option_key 			= $option_key;
		$this->page_title			= ( isset( $page_title ) ) ? $page_title : '';
		$this->menu_title			= ( isset( $menu_title ) ) ? $menu_title : '';
		$this->capability			= ( isset( $capability ) ) ? $capability : 'manage_options';
		$this->page_slug			= ( isset( $page_slug) ) ? $page_slug : '';
		$this->header_title			= ( isset( $header_title) ) ? $header_title : '';
		$this->template				= ( isset( $template) ) ? $template : array();
		$this->old_options			= get_option( $this->option_key );
		$this->deps					= array();
		$this->menus_inst 			= array();
		$this->controls_used		= array();
		$this->controls_inst		= array();
		
		$menus	=  $template['menus'];

		foreach( $menus as $menu ) {
			
			$controls_inst = $this->iterate_menu( $menu['controls'] );				

			$this->menus_inst[] = array( 'name' => $menu['name'], 'icon' => $menu['icon'], 'title' => $menu['title'], 'controls' => $controls_inst );
			
		}
		
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
		add_action( 'in_admin_footer', array( $this, 'enqueue_footer' ) );
		add_action( 'wp_ajax_optimalpress_save_options_page_hook', array( $this, 'save_options' ) );
		
    }
	
	/**
	 *
	 * Iterates the array of each menu item and instance all controls used.
	 * Stores in an array of objects all controls used to later render them on screen.
	 * 
	 * @param array		$menus_controls 	Array containing the controls used in each menu.
	 */
	private function iterate_menu( $menus_controls ){
		
		$controls_inst = array();
		
		foreach( $menus_controls as $control ) {
			
			$path = ( isset( $control['is_custom'] ) && $control['is_custom']) ? OP_CUSTOM_CONTROLS_PATH . '/' : OP_PATH . '/controls/';
			
			require_once( $path . $control['type'] . '/class-op-control-' . $control['type'] . '.php' );
			$field_classname 		= 'OP_Control_' . $control['type'];
			$control_obj			= new $field_classname( $control['type'], $control['name'], $control );
			$controls_inst[]		= $control_obj;

			$this->controls_inst	= array_merge( $this->controls_inst, $control_obj->get_controls_inst() );
				
			$deps					= apply_filters( 'op_apply_control_deps', $control_obj->get_deps(), $control, $control_obj );
			$controls_used			= apply_filters( 'op_apply_controls_used', $control_obj->get_controls_used(), $control, $control_obj );
			
			if( $deps ) {
			
				$this->deps				=  array_merge( $this->deps, $deps );
				
			}
			
			if( $controls_used ) {
			
				$this->controls_used	=  array_merge( $this->controls_used, $controls_used );
				
			}
			
		}
		
		return $controls_inst;
			
	}
	
    /**
     * Add options page to wordpress menu
     */
    public function add_options_page() {
							
        add_theme_page(
            $this->page_title,
            $this->menu_title,
            $this->capability, 
            $this->page_slug, 
            array( $this, 'create_admin_page' )
        );
		
    }
	
	/**
     * Enqueue commons scripts and styles needed for option page
	 *
	 * @param string $hook The hook suffix for the current admin page.
     */
	public function enqueue_scripts_styles( $hook ) {

		if( $hook != 'appearance_page_' . $this->page_slug ) {
			return;
		}
		
		wp_register_style( 'optimalpress-option-page', OP_URL . '/core/css/options-page.css', array(), '1.0', 'all' );
		wp_enqueue_style( 'optimalpress-controls', OP_URL . '/core/css/controls.css', array(), '1.0' );
		wp_enqueue_style( 'optimalpress-option-page' );

		$dependencies = '';
		
		if( ! empty( $this->deps ) ){
			$dependencies[] = array( 
				'id'			=> 'op-option-panel',
				'dependencies' 	=> $this->deps,
			);

			wp_enqueue_script( 'optimalpress-dependency', OP_URL . '/core/js/dependency.js', array( 'jquery' ), '1.0', true );
		}
		
		wp_enqueue_script( 'optimalpress-options-page', OP_URL . '/core/js/options-page.js', array( 'jquery' ), '1.0', true );
		
		wp_localize_script( 
			'optimalpress-options-page', 
			'optimalpressData', 
			array( 
				'ajaxurl'		=> admin_url( 'admin-ajax.php' ),
				'nonce'  		=> wp_create_nonce( OP_NONCE_SECURITY ),
				'dependency'	=> $dependencies,
			)
		);
		
		?> <link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700' rel='stylesheet' type='text/css'> <?php
			
	}
	
	/**
	 * Enqueue some code needed for some controls.
	 *
	 * This enqueue some code in the footer only if the control is in use. 
	 */	
	public function enqueue_footer() {
		
		$current_screen = get_current_screen();

		if( $current_screen->id != 'appearance_page_' . $this->page_slug ) {
			return;
		}

		if( in_array( 'fontawesome', $this->controls_used ) ) {
			
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
		
		if( in_array( 'link', $this->controls_used ) ) {
			
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

    /**
     * Options page callback for create the panel.
     */
    public function create_admin_page() {
	
		?>	
		<div class="wrap">
			<h2><?php echo $this->header_title; ?></h2>
			<div id="op-wrap" class="op-wrap">
				<div id="op-option-panel" class="op-option-panel">
					<div class="op-left-panel">
						<div id="op-menus" class="op-menus">
							<ul class="op-menu-level-1">
								<?php foreach( $this->template['menus'] as $menu  ): ?>
								<?php $is_first_lvl_1 = false;//$this->is_first_lvl_1( $menu['name'] ); ?>
								<?php if( $is_first_lvl_1 ): ?>
								<li class="op-current">
								<?php else: ?>
								<li>
								<?php endif; ?>
									<a href="#<?php echo $menu['name'] ?>" class="op-menu-goto">
								
										<?php
										$icon = false;
										$font_awesome = isset( $menu['icon'] ) ? $menu['icon'] : false;
										?>
										
										<?php
										if( $font_awesome !== false ):
											echo '<i class="fa ' . esc_attr( $menu['icon'] ) .'"></i>';
										else:
											echo '<i class="fa fa-cogs"></i>';
										endif;
										?>
										<span><?php echo esc_html( $menu['title'] ); ?></span>
										
									</a>

								</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
					
					<div class="op-right-panel">
						<form id="op-option-form" class="op-option-form" method="POST">
							
							<?php foreach( $this->menus_inst as $menu ): ?>
								
								<div id="<?php echo esc_attr( $menu['name'] ); ?>" class="op-panel">
									<h2><span><?php echo esc_html( $menu['title'] ); ?></span></h2>
									<div class="op-controls">
										<?php 
										foreach( $menu['controls'] as $control ) {
										
											if( $control->type == 'section' ) {
												?>
												<div id="<?php echo esc_attr( $control->name ); ?>" class="op-section">
												<h3><?php echo esc_html( $control->title ); ?></h3>
												<div class="op-controls">
												<?php
												foreach( $control->controls as $cont ){
													
													$value 	= isset( $this->old_options[ $cont->name ] ) ? $this->old_options[ $cont->name ] : $cont->default_value;
													
													$cont->render( $value );
													
												}
												?></div></div><?php
											}else{
												
												$value 	= isset( $this->old_options[ $control->name ] ) ? $this->old_options[ $control->name ] : $control->default_value;

												$control->render( $value );
												
											}
										}
										?>
									</div>
								</div>
								
							<?php endforeach; ?>
							<div id="op-submit-bottom" class="op-submit bottom">
								<input class="op-save button button-primary" type="submit" value="<?php _e( 'Save Changes', 'optimalpress-domain' ); ?>" />
							</div>
						</form>
					</div>
					
				</div>
				
			</div>
		</div>

		<?php
    }
	
	/**
     * Save Options by Ajax.
     */
	public function save_options() {

		check_admin_referer( OP_NONCE_SECURITY, 'nonce' );
	
		$result 			= array();
		$validated_fields 	= array();
		
		foreach( $this->controls_inst as $control ) {

			$field_value	= ( isset( $_POST[ $control->name ] ) ) ? $_POST[ $control->name ] : null;
			$field_value 	= apply_filters( 'op_before_validate_option_field', $field_value, $this->option_key, $control->name );
			$field_value 	= $control->validate( $field_value  );
			$field_value 	= apply_filters( 'op_after_validate_option_field', $field_value, $this->option_key, $control->name );
			
			$validated_fields[ $control->name ] = $field_value;
			
		}

		$validated_fields = apply_filters( 'op_theme_options_save', $validated_fields, $this->option_key );
		
		update_option( $this->option_key, $validated_fields );
		
		$result['message']	= $this->controls_inst;
		
		header( 'content-type: application/json; charset=utf-8' );
		
		echo json_encode( $result );
		
		die();
		
	}
    
}

?>