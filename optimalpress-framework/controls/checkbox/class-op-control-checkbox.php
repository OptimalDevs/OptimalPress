<?php

class OP_Control_Checkbox extends Optimalpress_Control {

	private $items;
	
	public function __construct( $type, $name, $control_args ) {
			
		parent::__construct( $type, $name, $control_args );
		
		$this->default_value	= isset( $control_args['default'] ) && is_array( $control_args['default'] ) ? $control_args['default'] : array();
		$this->items			= isset( $control_args['items'] ) && is_array( $control_args['items'] ) ? $control_args['items'] : array();

	}
	
	public function validate( $field_value ) {
		
		$value = is_array( $field_value ) ? $field_value : array();
		return $value;
			
	}

	protected function render_field( $value, $name ) {
		
		?>
		<div class="input op-control-checkbox">
		<?php
		if( ! empty( $this->items ) ) : 
		foreach( $this->items as $item ):

		?>
		<label>
			<?php $checked =( in_array( $item['value'], $values ) ); ?>
			<input <?php if( $checked ) echo 'checked'; ?> class="op-input <?php echo $this->field_classes; ?><?php if( $checked ) echo " checked"; ?>" type="checkbox" name="<?php echo esc_attr( $name ); ?>[]" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $item['value'] ); ?>" />
			<span></span><?php echo esc_attr( $item['label'] ); ?>
		</label>
		<?php
		endforeach;
		endif;
		?>
		</div>
		<?php
	
	}

}

?>