<?php

class OP_Control_Radioimage extends Optimalpress_Control {

	private $items;
		
	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
	
		$this->items	= isset( $control_args['items'] ) && is_array( $control_args['items'] ) ? $control_args['items'] : array();
			
	}
	
	public function enqueue_scripts_styles() {
	
		wp_enqueue_script( 'optimalpress-radioimage', OP_URL . '/controls/' . $this->type . '/js/op-radioimage.js', array(), '1.0', true );
		wp_enqueue_script( 'jquery-tipsy-js', OP_URL . '/controls/public/js/jquery.tipsy.js', array(), '1.0', true );
		wp_enqueue_style( 'jquery-tipsy', OP_URL . '/controls/public/css/tipsy.css', array(), '1.0' );
		
		return;
			
	}
	
	public function validate( $field_value ) {
	
		$value = sanitize_text_field( $field_value );

		return $value;
			
	}

	public function render_field( $value, $name ) {
	
		?>
		<div class="input op-control-radioimage">
			<?php if( ! empty( $this->items ) ) : ?>
			<?php foreach( $this->items as $item ): ?>
			<label>
				<?php $checked = ( $item['value'] == $value ); ?>
				<input <?php if( $checked ) echo 'checked'; ?> class="op-input op-hide <?php echo $this->field_classes; ?><?php if( $checked ) echo " checked"; ?>" type="radio" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $item['value'] ); ?>" />
				<img src="<?php echo esc_attr( $item['img'] ); ?>" alt="<?php echo esc_attr( $item['label'] ); ?>" title="<?php echo esc_attr( $item['label'] ); ?>" class="op-image-item" />
			</label>
			<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<?php
		
	}

}

?>