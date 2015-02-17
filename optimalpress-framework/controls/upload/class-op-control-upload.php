<?php

class OP_Control_Upload extends Optimalpress_Control {

	private $multiple;
	
	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
		
		$this->multiple	= isset( $control_args['multiple'] ) ? $control_args['multiple'] : false;
			
	}
	
	public function enqueue_scripts_styles() {
		
		wp_enqueue_media();
		wp_enqueue_script( 'optimalpress-wpmediauploader', OP_URL . '/controls/'. $this->type . '/js/op-wpmediauploader.js', array( 'jquery' ), '1.0', true );
					
		return;
			
	}
	
	public function validate( $field_value ) {
		return $field_value;	
	}

	public function render_field( $value, $name ) {
	
		$att_ids	= explode( ',', $value );

		?>
		<div class="input op-control-upload">
			<div>
				<input class="op-input <?php echo $this->field_classes; ?>" type="hidden" id="<?php echo esc_attr( $name ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"  />
				<input class="op-upload-media op-button button" type="button" value="<?php esc_attr_e( 'Choose File', 'optimalpress-domain' ); ?>" data-multiple="<?php echo esc_attr( $this->multiple ); ?>"/>
				
			</div>
			
			<div class="images">
				<?php foreach( $att_ids as $att_id ) : ?>
				<?php if ( ! empty ( $att_id ) && wp_get_attachment_url( $att_id ) ) : ?>	
					<div class="image">
						<img class="centered" src="<?php echo wp_get_attachment_url( $att_id ); ?>" data-id="<?php echo esc_attr(  $att_id ); ?>" alt="" style="max-width:200px;" />
						<input class="op-remove-media op-button button" type="button" value="x" />
					</div>
				<?php endif; ?>	
				<?php endforeach; ?>
			</div>
		</div>
		<?php
		
	}

}

?>