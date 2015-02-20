<?php

class OP_Control_Header extends Optimalpress_Control {
	
	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
		
	}
		
	public function validate( $field_value ) {
	
		return null;
	
	}

	public function render( $value, $name = false ) {
		
		$name	= ( ! $name ) ? $this->name : $name; 
		
		?>
		<div class="op-field not-sc op-control-header-wrapper <?php echo esc_attr( $this->container_classes ); ?>" id="field-<?php echo esc_attr( $name ); ?>" >

			<div class="title" ><?php echo esc_attr( $this->label ); ?></div>
			<?php if( ! empty( $this->description ) ) : ?>
				<div class="description" ><?php echo esc_html( $this->description ); ?></div>
			<?php endif; ?>

		</div>
		<?php
		
	}

}

?>