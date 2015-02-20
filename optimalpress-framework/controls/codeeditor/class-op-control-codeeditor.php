<?php

class OP_Control_CodeEditor extends Optimalpress_Control {
	
	/**
	 * Format mode for the code editor. Possible Values: css, html, js.
	 * @var string
	 */
	private $mode;
	
	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
		
		$this->mode	= isset( $control_args['mode'] ) ? $control_args['mode'] : '';
		
	}
	
	public function enqueue_scripts_styles() {
	
		wp_enqueue_script( 'optimalpress-codemirror', OP_URL . '/controls/' . $this->type . '/js/ace/ace.js', array(), '2.0.0', true );
		wp_enqueue_script( 'optimalpress-codeeditor', OP_URL . '/controls/' . $this->type . '/js/op-codeeditor.js', array('jquery'), '1.0', true );
		
		return;
		
	}
	
	public function validate( $field_value ) {
	
		return $field_value;
	
	}

	protected function render_field( $value, $name ) {
	
		?>
		<div class="input op-control-codeeditor">
			<textarea class="op-input hidden <?php echo esc_attr( $this->field_classes ); ?>" name="<?php echo esc_attr( $name ); ?>" ><?php echo esc_attr( $value ); ?></textarea>
			<div class="op-js-codeeditor" data-mode="<?php echo esc_attr( $this->mode ); ?>"></div>
		</div>
		<?php
		
	}

}

?>