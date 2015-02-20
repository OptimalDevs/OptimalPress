<?php

class OP_Control_TextArea extends Optimalpress_Control {
	
	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
	
	}
	
	public function validate( $field_value ) {
	
		// Sanitize the user input.
		$value = sanitize_text_field( $field_value );

		return $value;
	
	}

	protected function render_field( $value, $name ) {
	
		?>
		<div class="input op-control-textarea">
			<textarea class="widefat op-input <?php echo esc_attr( $this->field_classes ); ?>" name="<?php echo esc_attr( $name ); ?>"><?php echo esc_attr( $value ); ?></textarea>
		</div>
		<?php
	
	}

}

?>