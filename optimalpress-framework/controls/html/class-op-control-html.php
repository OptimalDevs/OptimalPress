<?php

class OP_Control_HTML extends Optimalpress_Control {
	
	/**
	 * HTML string.
	 * @var string
	 */
	private $html;
		
	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
		
		$this->html	= isset( $control_args['html'] ) ? $control_args['html'] : '';
		
	}
	
	public function validate( $field_value ) {
	
		return null;
	
	}

	public function render( $value, $name = false ) {
		
		$name	= ( ! $name ) ? $this->name : $name;
		
		?>
		<div class="op-field not-sc op-control-html-wrapper <?php echo esc_attr( $this->container_classes ); ?>" id="field-<?php echo esc_attr( $name ); ?>" >
			
			<div class="field">
				<div class="input op-control-html" id="<?php echo esc_attr( $name ) . '_dom'; ?>">
					<?php echo $this->html; ?>
				</div>
			</div>
		</div>
		<?php
		
	}

}

?>