<?php

/**
 * The class to create metaboxes.
 *
 * This class is responsible for creating the METABOX, iterate all the controls and display them on screen and saves the METABOX in the database. 
 */
 
class Optimalpress_Metabox {
	
	/**
	 * Unique id for the metabox
	 * @var String
	 */
	private $id;
	
	/**
	 * Title for the metabox
	 * @var String
	 */
	private $title;
	
	/**
	 * The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side')
	 * @var String
	 */
	private $context;
	
	/**
	 * The priority within the context where the boxes should show ('high', 'core', 'default' or 'low')
	 * @var String
	 */
	private $priority;
	
	/**
	 * The controls used in this metabox
	 * @var ArrayObject
	 */
	private $controls_inst;
	
	/**
	 * Dependencies for this metabox
	 * @var Array
	 */
	private $deps;
	
	/**
	 * List of controls used in this metabox
	 * @var Array
	 */
	private $controls_used;

	/**
	 * Hook into the appropriate actions when the class is constructed.
	 *
	 * Instance all controls used in the metabox and stores it in an array of objects.
	 * Also each control dependency is stored.
	 *
	 * @param string	$metabox_id			The unique id for the metabox.
	 * @param string	$metabox_title 		Title for the metabox, visible to user.
	 * @param array		$metabox_fields 	Controls list used in the metabox.
	 * @param string	$metabox_context	The part of the page where the edit screen section should be shown ('normal', 'advanced', or 'side').
	 * @param string	$metabox_priority	The priority within the context where the boxes should show ('high', 'core', 'default' or 'low')
	 */
	public function __construct( $metabox_id, $metabox_title, $metabox_fields, $metabox_context = 'advanced', $metabox_priority = 'default' ) {
					
		$this->id 				= $metabox_id;
		$this->title 			= $metabox_title;
		$this->context 			= $metabox_context;
		$this->priority			= $metabox_priority;
		$this->deps				= array();
		$this->controls_inst 	= array();
		$this->controls_used	= array();
		
		foreach( $metabox_fields as $control ) {
							
			$path = ( isset( $control['is_custom'] ) && $control['is_custom'] ) ? OP_CUSTOM_CONTROLS_PATH . '/' : OP_PATH . '/controls/';
			
			require_once( $path . $control['type'] . '/class-op-control-' . $control['type'] . '.php' );
			
			$field_classname 		= 'OP_Control_' . $control['type'];
			$control_inst			= new $field_classname( $control['type'], $control['name'], $control );
			$this->controls_inst[]	= $control_inst;			
			$deps					= apply_filters( 'op_apply_control_deps', $control_inst->get_deps(), $control, $control_inst );
			$controls_used			= apply_filters( 'op_apply_controls_used', $control_inst->get_controls_used(), $control, $control_inst );
			
			if( $deps ) {
			
				$this->deps				=  array_merge( $this->deps, $deps );
				
			}
			
			if( $controls_used ) {
			
				$this->controls_used	=  array_merge( $this->controls_used, $controls_used );
				
			}
						
		}
		
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );	
		add_action( 'save_post', array( $this, 'save' ) );
		
	}
	
	/**
	 * Adds the meta box to the administrative interface.
	 * 
	 * @param String $post_type The post type ('post', 'page', 'dashboard', 'link', 'attachment' or 'custom_post_type')
	 */
	public function add_meta_box( $post_type ) {
		
		add_meta_box(
			$this->id,
			$this->title,
			array( $this, 'render_meta_box_content' ),
			$post_type,
			$this->context,
			$this->priority
		);

	}
	
	/**
	 * Get all deps in this metabox.
	 * 
	 */
	public function get_deps() {
		
		return ( ! empty( $this->deps ) ) ? $this->deps : false;
		
	}
	
	/**
	 * Get all controls used in this metabox.
	 * 
	 */
	public function get_controls_used() {
		
		return ( ! empty( $this->controls_used ) ) ? $this->controls_used : false;
		
	}
	
	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {

		if ( ! isset( $_POST[ $this->id . '_optimalpress_inner_custom_box_nonce' ] ) ) {
			return $post_id;
		}

		if ( ! wp_verify_nonce( $_POST[ $this->id . '_optimalpress_inner_custom_box_nonce' ], OP_NONCE_SECURITY ) ) {
			return $post_id;
		}
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		$validated_fields = array();

		foreach( $this->controls_inst as $control ) {
				
			$field_value	= ( isset( $_POST[ $control->name ] ) ) ? $_POST[ $control->name ] : null;
			$field_value 	= apply_filters( 'op_before_validate_metabox_field', $field_value, $this->id, $control->name );
			$field_value 	= $control->validate( $field_value );
			$field_value 	= apply_filters( 'op_after_validate_metabox_field', $field_value, $this->id, $control->name );
			
			$validated_fields[ $control->name ] = $field_value;
	
		}
	
		$validated_fields	= apply_filters( 'op_metabox_save', $validated_fields, $this->id );

		update_post_meta( $post_id, $this->id, $validated_fields );
		
	}

	/**
	 * Render Metabox content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_meta_box_content( $post ) {

		// Add an nonce field.
		wp_nonce_field( OP_NONCE_SECURITY, $this->id . '_optimalpress_inner_custom_box_nonce' );

		// Retrieve an existing value from the database.
		$metabox = get_post_meta( $post->ID, $this->id, true );

		// Display the form using the retrieved values.
		?><div class="op-metabox"><?php

		foreach( $this->controls_inst as $control ) {
			
			$value	= isset( $metabox[ $control->name ] ) ? $metabox[ $control->name ] : $control->default_value;
			
			$control->render( $value );
			
		}
		
		?></div><?php
		
	}
	
}

?>