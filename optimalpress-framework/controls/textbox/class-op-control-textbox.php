<?php

class OP_Control_TextBox extends Optimalpress_Control {
			
	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
						
	}
		
	public function validate( $field_value ) {
		
		$value = sanitize_text_field( $field_value );

		return $value;
			
	}

	public function render_field( $value, $name ) {
		
		?>
		<div class="input op-control-textbox">
			<input type="text" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" class="widefat op-input <?php echo $this->field_classes; ?>" value="<?php echo esc_attr( $value ); ?>" />
		</div>
		<?php
		
	}

}

?>