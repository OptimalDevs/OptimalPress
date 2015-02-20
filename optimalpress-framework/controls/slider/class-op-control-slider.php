<?php

class OP_Control_Slider extends Optimalpress_Control {
	
	/**
	 * The maximum value of the slider.
	 * @var int
	 */
	private $min;
	
	/**
	 * The minimum value of the slider.
	 * @var int
	 */
	private $max;
	
	/**
	 * Determines the size or amount of each interval or step the slider takes between the min and max. 
	 * @var int
	 */
	private $step;
	
	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
		
		$this->min	= isset( $control_args['min'] ) && is_numeric( $control_args['min'] ) ? $control_args['min'] : 0;
		$this->max	= isset( $control_args['max'] ) && is_numeric( $control_args['max'] ) ? $control_args['max'] : 10;
		$this->step	= isset( $control_args['step'] ) && is_numeric( $control_args['step'] ) ? $control_args['step'] : 1;
		
	}
	
	public function enqueue_scripts_styles() {
	
		wp_enqueue_style( 'optimalpress-ui-slider', OP_URL . '/controls/public/css/jquery-ui.min.css', array(), '2.0.0' );					
		
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'optimalpress-slider', OP_URL . '/controls/'. $this->type . '/js/op-slider.js', array( 'jquery' ), '1.0', true );
		
		return;
			
	}
	
	public function validate( $field_value ) {
	
		$value = is_numeric( $field_value ) ? $field_value : 0;

		return $value;
			
	}

	protected function render_field( $value, $name ) {
	
		?>
		<div class="input op-control-slider">
			<input type="text" name="<?php echo esc_attr( $name ); ?>" class="op-input slideinput <?php echo esc_attr( $this->field_classes ); ?>" value="<?php echo esc_attr( $value ); ?>" />
			<div class="optimalpress-slider" id="<?php echo esc_attr( $name ); ?>" data-min="<?php echo esc_attr( $this->min ); ?>" data-max="<?php echo esc_attr( $this->max ); ?>" data-step="<?php echo esc_attr( $this->step ); ?>" data-value="<?php echo esc_attr( $value ); ?>"></div>
		</div>
		<?php
		
	}

}

?>