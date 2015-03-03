<?php

class OP_Control_TextEditor extends Optimalpress_Control {
	
	/*
	 * The number of rows to display for the textarea.
	 * @var int
	 */
	private $textarea_rows;
	
	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
		
		$this->textarea_rows	= isset( $control_args['textarea_rows'] ) ? $control_args['textarea_rows'] : 10;
		
	}
	
	public function enqueue_scripts_styles() {
	
		return;
		
	}
	
	public function validate( $field_value ) {
	
		return $field_value;
	
	}

	protected function render_field( $value, $name ) {
		
		wp_editor( $value, $name, array( 'textarea_rows' => $this->textarea_rows ) );
		
	}

}

?>