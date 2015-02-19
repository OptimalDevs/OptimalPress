<?php

class OP_Control_DatePicker extends Optimalpress_Control {

	private $format;
	
	private $show_time_picker;
	
	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
		
		$this->format			= isset( $control_args['format'] ) ? $control_args['format'] : 'Y-m-d H:i:s';
		$this->show_time_picker	= isset( $control_args['show_time_picker'] ) && is_bool( $control_args['show_time_picker'] ) ? $control_args['show_time_picker'] : true;

	}
	
	public function enqueue_scripts_styles() {
	
		wp_enqueue_style( 'optimalpress-time-picker', OP_URL . '/controls/' . $this->type . '/css/jquery.datetimepicker.css', array(), '2.0.0' );					
		wp_enqueue_script( 'optimalpress-time-picker', OP_URL . '/controls/' . $this->type . '/js/jquery.datetimepicker.min.js', array( 'jquery' ), '2.0.0', true );
		wp_enqueue_script( 'optimalpress-datepicker', OP_URL . '/controls/' . $this->type . '/js/op-date-time-picker.js', array(), '1.0', true );
		
		return;
			
	}
	
	public function validate( $field_value ) {
	
		// Sanitize the user input.
		$value = sanitize_text_field( $field_value );
		
		return $value;
	
	}

	protected function render_field( $value, $name ) {
	
		?>
		<div class="field op-control-datepicker">
			<input type="text" name="<?php echo $name; ?>" id="<?php echo $name; ?>" class="optimalpress-datepicker op-input <?php echo $this->field_classes; ?>" value="<?php echo esc_attr( $value ); ?>" data-op-format="<?php echo esc_attr( $this->format ); ?>" data-op-timepicker="<?php echo esc_attr( $this->show_time_picker ); ?>" />
		</div>
		<?php
		
	}

}

?>