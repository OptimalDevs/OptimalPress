<?php

class OP_Control_Section extends Optimalpress_Control {
	
	private $controls_used;
	private $deps;
	public $title;
	public $controls;
	public $is_section;
			
	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
		
		$this->default_value	= isset( $control_args['default'] ) ? $control_args['default'] : array();
		$this->title			= isset( $control_args['title'] )  ? $control_args['title'] : '';
		$this->is_section		= true;
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

			

			if( ! in_array( $control_obj->type, $this->controls_used ) ) {
				$this->controls_used[] = $control_obj->type;
			}

			if( ! empty( $control_obj->dependency ) ) {
				$this->deps[] = array( 'type' => $control_obj->type, 'field' => $control_obj->name, 'depends_of' => $control_obj->dependency['field'], 'values' => $control_obj->dependency['values'], 'group' =>  false );
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

	public function render( $group_fields, $name ) {
		
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