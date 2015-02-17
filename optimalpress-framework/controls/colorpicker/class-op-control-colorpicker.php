<?php

class OP_Control_ColorPicker extends Optimalpress_Control {
	
	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
		
	}
	
	public function enqueue_scripts_styles() {
		
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_script( 'optimalpress-colorpicker', OP_URL . '/controls/'. $this->type . '/js/op-wpcolorpicker.js', array( 'jquery' ), '1.0', true );
					
		return;
			
	}
	
	public function validate( $field_value ) {

		if ( '' === $field_value )
			return '';

		// 3 or 6 hex digits, or the empty string.
		if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $field_value ) ) {
			return $field_value;
		}
		
		return null;
			
	}

	public function render_field( $value, $name ) {
	
		?>
		<div class="input op-control-colorpicker">
			<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="optimalpress-colorpicker op-input <?php echo $this->field_classes; ?>" value="<?php echo esc_attr( $value ); ?>" />
		</div>
		<?php
		
	}

}

?>