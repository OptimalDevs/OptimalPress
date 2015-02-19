<?php

class OP_Control_Select extends Optimalpress_Control {
	
	private $items;
	
	private $multiple;
	
	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
				
		$this->items	= isset( $control_args['items'] ) && is_array( $control_args['items'] ) ? $control_args['items'] : null;
		$this->multiple	= isset( $control_args['multiple'] ) && $control_args['multiple'] == true ? 'multiple' : '';
		
		if( empty( $this->default_value ) ) {
			
			$this->default_value	= $this->multiple ? array() : '';
			
		}
		
	}
	
	public function enqueue_scripts_styles() {
		
		wp_enqueue_style( 'op-choosen', OP_URL . '/controls/public/css/chosen.min.css', array(), '1.0' );		
		wp_enqueue_script( 'optimalpress-select', OP_URL . '/controls/' . $this->type . '/js/op-select.js', array(), '1.0', true );
		wp_enqueue_script( 'optimalpress-chosen-jquery', OP_URL . '/controls/public/js/chosen.jquery.min.js', array(), '1.0', true );
		
		return;
			
	}
	
	public function validate( $field_value ) {
		
		if( $this->multiple == 'multiple' ) {
			$value = is_array( $field_value ) ? $field_value : array();	
		} else {
			$value = sanitize_text_field( $field_value );
		}
		
		return $value;
			
	}

	protected function render_field( $value, $name ) {

		?>
		<div class="input op-control-select">
			<select <?php echo $this->multiple; ?> name="<?php echo esc_attr( $name ); ?><?php echo ( $this->multiple ) ? '[]' : ''; ?>" id="<?php echo esc_attr( $name ); ?>" class="op-input optimalpress-chosen-select <?php echo $this->field_classes; ?>" autocomplete="off" style="width:100%;">
				<option></option>
				<?php if( ! empty( $this->items ) ) : 
				foreach( $this->items as $item ): ?>
				<option <?php if( ( $this->multiple && in_array( $item['value'], $value ) ) || ( !$this->multiple && $item['value'] == $value ) ) echo "selected" ?> value="<?php echo esc_attr( $item['value'] ); ?>"><?php echo esc_html( $item['label'] ); ?></option>
				<?php endforeach; ?>
				<?php endif; ?>
			</select>
		</div>
		<?php
		
	}

}

?>