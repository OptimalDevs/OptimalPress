<?php

class OP_Control_Toggle extends Optimalpress_Control {
	
	/**
	 * Indicates the "ON" text. By default "ON".
	 * @var string
	 */
	private $on_text;
	
	/**
	 * Indicates the "OFF" text. By default "OFF".
	 * @var string
	 */
	private $off_text;
		
	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
		
		$this->on_text	= isset( $control_args['on_text'] ) && ! empty( $control_args['on_text'] ) ? $control_args['on_text'] : 'ON';
		$this->off_text	= isset( $control_args['off_text'] ) && ! empty( $control_args['off_text'] ) ? $control_args['off_text'] : 'OFF';
		
	}
	
	public function enqueue_scripts_styles() {
		
		wp_enqueue_style( 'op-toggle', OP_URL . '/controls/'. $this->type . '/css/op-toggle.css', array(), '1.0' );
	
		return;
	
	}
	
	public function validate( $field_value ) {
		
		// Sanitize the user input.
		$value = ( $field_value == 1 || $field_value === 0 ) ? $field_value : 0;

		return $value;
	
	}

	protected function render_field( $value, $name ) {
	
		?>
		
		<div class="input op-control-toggle">
			<div class="onoffswitch">
				<input type="checkbox" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" class="onoffswitch-checkbox op-input <?php echo esc_attr( $this->field_classes ); ?>" value="1" <?php checked( $value, '1' ); ?> >
				<label class="onoffswitch-label" for="<?php echo esc_attr( $name ); ?>">
					<span class="onoffswitch-inner" data-on="<?php echo esc_attr( $this->on_text ); ?>" data-off="<?php echo esc_attr( $this->off_text ); ?>"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
		</div>

		<?php

	}

}

?>