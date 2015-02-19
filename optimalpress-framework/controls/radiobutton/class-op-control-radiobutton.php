<?php

class OP_Control_Radiobutton extends Optimalpress_Control {
	
	private $items;

	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
		
		$this->items	= isset( $control_args['items'] ) && is_array( $control_args['items'] ) ? $control_args['items'] : array();
			
	}
	
	public function validate( $field_value ) {
	
		$value = sanitize_text_field( $field_value );

		return $value;
			
	}

	protected function render_field( $value, $name ) {
	
		?>
		<div class="input op-control-radiobutton">
			<?php if( ! empty( $this->items ) ) : ?>
			<?php foreach( $this->items as $item ): ?>
			<label>
				<?php  $checked = ( $item['value'] == $value ); ?>
				<input <?php if( $checked ) echo 'checked'; ?> class="op-input <?php echo $this->field_classes; ?><?php if( $checked ) echo " checked"; ?>" type="radio" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $item['value'] ); ?>" />
				<span></span><?php echo esc_attr( $item['label'] ); ?>
			</label>
			<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<?php
		
	}

}

?>