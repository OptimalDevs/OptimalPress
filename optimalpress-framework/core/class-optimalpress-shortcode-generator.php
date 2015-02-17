<?php

/**
 * The class to create shortcodes.
 *
 * This class creates the modal box for the "Shortcode Generator" and manages it 
 */
class Optimalpress_Shortcode_Generator {
	
	/**
	 * Name for the modalbox of the "Shortcode Generator".
	 * @var String
	 */
	private $name;
	
	/**
	 * Array containing each menu item. Each menu contains the controls / fields used in that menu.
	 * @var Array
	 */
	private $template;
	
	/**
	 * Title for the shortcode generator modalbox, visible to user.
	 * @var string
	 */
	private $modal_title;
	
	/**
	 * Title for the shortcode generator button.
	 * @var string
	 */
	private $button_title;
	
	/**
	 * Image for the shortcode generator button.
	 * @var string
	 */
	private $main_image;
	
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
	 * Array containing the array of instantiated menus and controls.
	 * @var ArrayObject
	 */	
	private $menu_inst;
	
	/*
	 *	Constructor
	 *
	 * @param string	$name			The name for the shortcode.
	 * @param array		$template 		Array containing each menu item. Each menu contains the controls / fields used in that menu.
	 * @param string	$modal_title 	Title for the shortcode generator modal, visible to user.
	 * @param string	$button_title	Button title.
	 * @param string	$main_image		Image for the button.
	 * @param string	$post_types		The post type ('post', 'page', 'dashboard', 'link', 'attachment' or 'custom_post_type')
	 */
    public function __construct( $name, $template, $modal_title, $button_title , $main_image ) {
				
		$this->name 			= $name;
		$this->template			= $template;
		$this->modal_title		= $modal_title;
		$this->button_title		= $button_title;
		$this->main_image     	= $main_image;
		$this->deps 			= array();
		$this->menu_inst 		= array();
		$this->controls_used 	= array();
		
		foreach( $this->template as $title => $menu ) {
			
			$elements_inst = array();
			
			foreach( $menu['elements'] as $key => $element ) {
				
				$control_inst	= array();
				
				foreach( $element['attributes'] as $control ) {
					
					$path = ( isset( $control['is_custom'] ) && $control['is_custom'] ) ? OP_CUSTOM_CONTROLS_PATH . '/' : OP_PATH . '/controls/';
			
					require_once( $path . $control['type'] . '/class-op-control-' . $control['type'] . '.php' );
					$field_classname 	= 'OP_Control_' . $control['type'];
					$control_ins		= new $field_classname( $control['type'], $control['name'], $control );
					$control_inst[]		= $control_ins;
				
					if( ! empty( $control_ins->dependency ) ) {
						$this->deps[] = array( 'type' => $control_ins->type, 'field' => $control_ins->name, 'depends_of' => $control_ins->dependency['field'], 'values' => $control_ins->dependency['values'], 'group' => false, 'shortcode_group' => $key );
					}
					
					$controls_used	= apply_filters( 'op_apply_controls_used', $control_ins->get_controls_used(), $control, $control_ins );
			
					if( $controls_used ) {
					
						$this->controls_used	=  array_merge( $this->controls_used, $controls_used );
						
					}
					
				}
				
				$elements_inst[] 	= array(
					'title'		=> $element['title'],
					'code'		=> $element['code'],
					'id'		=> $key,						
					'controls' 	=> $control_inst,
					
				);
				
			}
			
			$this->menu_inst[] = array(
				'title'		=> $title,
				'elements' 	=> $elements_inst
			);
			
		}
		
		add_action( 'admin_enqueue_scripts', array( $this, 'set_shortcode_generator_button' ) );
		add_action( 'admin_footer', array( $this, 'print_modal' ) );
		
	}
	
	/**
     * Print Modal
	 *
	 * This function prints the structure containing all sections of shortcodes.
     *
     */ 
	public function print_modal() {
	
		$modal_id = $this->name . '_modal';
		?>
		<div id="<?php echo $modal_id; ?>" class="op-modal">
			<h1><?php echo $this->modal_title; ?></h1>
			<div class="op-scroll-container">	
				<div class="op-wrapper">
					
					<!-- Menu Side -->
					<ul class="op-menu">
					<?php foreach( $this->menu_inst as $menu ): ?>
					
						<?php if( reset( $this->menu_inst ) == $menu ): ?>
							<li class="op-current"><a href="#<?php echo str_replace( ' ', '_', $menu['title'] ); ?>"><?php echo esc_attr( $menu['title'] ); ?></li></a>		
						<?php else: ?>						
							<li><a href="#<?php echo str_replace( ' ', '_', $menu['title'] ); ?>"><?php echo esc_attr( $menu['title'] ); ?></li></a>		
						<?php endif; ?>
						
					<?php endforeach; ?>
					</ul>
					<!-- End Menu Side -->
					<!-- Content Right Side -->
					<div class="op-main">
						<?php foreach( $this->menu_inst as $menu ): ?>
						
							<?php if( reset( $this->menu_inst ) == $menu ) : ?>
								<ul class="op-current op-sub-menu-list op-sub-menu-<?php echo str_replace( ' ', '_', $menu['title'] ); ?>">
							<?php else : ?>
								<ul class="op-hide op-sub-menu-list op-sub-menu-<?php echo str_replace( ' ', '_', $menu['title'] ); ?>">
							<?php endif; ?>
							
							<?php foreach( $menu['elements'] as $key => $element ): ?>
							
								<li class="op-element postbox">
									<h3 class="hndle op-element-heading">
										<span>
												<?php echo esc_attr( $element['title'] ); ?>
										</span>
									</h3>
									<div class="hidden op-code"><?php echo htmlentities( $element['code'] ); ?></div>
									<?php if( isset( $element['controls'] ) and ! empty( $element['controls'] ) ): ?>
										<form class="op-element-form<?php echo ' op-sc-group-' . $element['id']; ?> inside">
											<?php echo $this->print_shortcode_section( $element['controls'] ); ?>
										</form>
									<?php endif; ?>
								</li>
								
							<?php endforeach; ?>
							</ul>
							
						<?php endforeach; ?>
					</div>
					<!-- End Content Right Side -->
				
				</div>
				<a class="op-close-modal">&#215;</a>
			</div>
		</div>
		
		<?php
	}
	
	/**
     * Print Controls Inside Modal
	 *
	 * This function prints each control within each section.
     *
	 * @param  ArrayObject $controls - Array of object that contains all controls for this section.
     */ 
	public function print_shortcode_section( $controls )	{
		
		?><div class="op-fields"><?php
		
		foreach( $controls as $control ) {

			$default_value 	= isset( $control->default_value ) ? $control->default_value : '' ;
			$control->render( $default_value, $control->name );

		}
		
		?>
		</div>
		<div class="op-action">
			<button class="op-insert button"><?php _e( 'Insert', 'optimalpress-domain' ); ?></button>
			<button class="op-cancel button"><?php _e( 'Cancel', 'optimalpress-domain' ) ?></button>
		</div>
		<?php	
		
	}
	
    /**
     * Create a "Shortcode Generator" button for tinymce
     *
     */
    public function set_shortcode_generator_button() {
	
        if( current_user_can('edit_posts') && current_user_can('edit_pages') ) {
		
            add_filter( 'mce_external_plugins', array( $this, 'add_buttons' ) );
            add_filter( 'mce_buttons', array( $this, 'register_buttons' ) );
		
		}
		
    }

    /**
     * Add new Javascript to the plugin script array
     *
     * @param  Array $plugin_array - Array of scripts
     *
     * @return Array
     */
    public function add_buttons( $plugin_array ) {
	
        $plugin_array[ $this->name ] = OP_URL . '/core/js/shortcode-tinymce-button.js';

        return $plugin_array;
		
    }

    /**
     * Add new button to tinymce
     *
     * @param  Array $buttons - Array of buttons
     *
     * @return Array
     */
    public function register_buttons( $buttons ) {
	
        array_push( $buttons, $this->name );
				
        return $buttons;
    
	}
	
	/**
     * Get all deps
     *
     * @return Array 
     */
	public function get_deps() {
		
		return ( ! empty( $this->deps ) ) ? $this->deps : false;
		
	}
	
	/**
     * Get all used controls
     *
     * @return Array 
     */
	public function get_controls_used() {
		
		return ( ! empty( $this->controls_used ) ) ? $this->controls_used : false;
		
	}

}