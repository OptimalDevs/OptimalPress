<?php

class OP_Control_Section extends Optimalpress_Control {
	
	/**
	 * Controls used in this section. 
	 * @var array
	 */
	private $controls_used;
	
	/**
	 * Dependencies for this section. 
	 * @var array
	 */
	private $deps;
	
	/**
	 * Title of this section. 
	 * @var array
	 */
	public $title;
	
	/**
	 * All the controls object used in this section. 
	 * @var arrayObj
	 */
	public $controls;
	
	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
		
		$this->default_value	= isset( $control_args['default'] ) ? $control_args['default'] : array();
		$this->title			= isset( $control_args['title'] )  ? $control_args['title'] : '';
		$this->deps				= array();
		$this->controls_used	= array();
		
		$g_controls = array();
		
		foreach( $control_args['fields'] as $group_control ) {
			
			$path = ( isset( $group_control['is_custom'] ) && $group_control['is_custom'] ) ? OP_CUSTOM_CONTROLS_PATH . '/' : OP_PATH . '/controls/';
			
			require_once( $path . $group_control['type'] . '/class-op-control-' . $group_control['type'] . '.php' );
			$field_classname 				= 'OP_Control_' . $group_control['type'];
			$group_control['group_name']	= false;
			$control_obj					= new $field_classname( $group_control['type'], $group_control['name'], $group_control );
			$g_controls[]					= $control_obj;
			
			$deps			= apply_filters( 'op_apply_control_deps', $control_obj->get_deps(), $group_control, $control_obj );
			$controls_used	= apply_filters( 'op_apply_controls_used', $control_obj->get_controls_used(), $group_control, $control_obj );
			
			if( $deps ) {
			
				$this->deps	=  array_merge( $this->deps, $deps );
				
			}
			
			if( $controls_used ) {
			
				$this->controls_used	=  array_merge( $this->controls_used, $controls_used );
				
			}
			
		}
		
		$this->controls	= $g_controls;
		
	}
	
	public function enqueue_scripts_styles() {
		
		foreach( $this->controls as $control ) {
			
			$control->enqueue_scripts_styles();
			
		}
		
		return;
			
	}
	
	public function validate( $group_fields ) {

		return;
	
	}

	public function render( $group_fields, $name = false ) {
		
		return;
		
	}
	
	public function get_controls_inst() {
	
		return $this->controls;
		
	}
	
	public function get_deps(){
		
		$return	= false;
		
		if( ! empty( $this->deps ) ) {
			
			return $this->deps;
		
		}
		
		return $return;
	
	}
	
	public function get_controls_used(){
		
		return $this->controls_used;
		
	}
	
	public function get_control_inst() {
	
		return $this->controls;
		
	}

}

?>