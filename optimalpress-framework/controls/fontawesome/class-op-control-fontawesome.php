<?php

class OP_Control_FontAwesome extends Optimalpress_Control {

	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
	
	}
	
	public function enqueue_scripts_styles() {
	
		wp_enqueue_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css', array(), '4.1.0' );					
		wp_enqueue_style( 'optimalpress-fontawsesome', OP_URL . '/controls/'. $this->type . '/css/op-fontawesome.css', array(), '1.0' );					
		wp_enqueue_script( 'optimalpress-fontawesome', OP_URL . '/controls/'. $this->type . '/js/op-fontawesome.js', array(), '1.0', true );
		
		return;
			
	}
	
	public function validate( $field_value ) {
	
		$value = sanitize_text_field( $field_value );

		return $value;
	
	}

	protected function render_field( $value, $name ) {
	
		?>
		<div class="input op-control-fontawesome">
			<input type="hidden" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" class="widefat edit-menu-item-fontawesome op-fontawesome-selected-icon op-input <?php echo esc_attr( $this->field_classes ); ?>" value="<?php echo esc_attr( $value ); ?>" />
			<a class="button op-show-fontawesome-modalbox" href="#"><?php echo esc_html__( 'Select', 'optimalpress-domain' ); ?></a>
			<a class="button op-remove-fontawesome" href="#"><?php echo esc_html__( 'Remove', 'optimalpress-domain' ); ?></a>
			<i class="fa <?php echo esc_attr( $value ); ?> op-fontawesome-preview"></i>
		</div>
		<?php
		
	}

}

?>