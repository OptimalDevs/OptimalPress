<?php

class OP_Control_Link extends Optimalpress_Control {
	
	public function __construct( $type, $name, $control_args ) {
	
		parent::__construct( $type, $name, $control_args );
		
	}
	
	public function enqueue_scripts_styles() {
		
		wp_enqueue_style( 'op-wplink', OP_URL . '/controls/' . $this->type . '/css/op-wplink.css', array(), '1.0' );
		wp_enqueue_script( 'op-wplink', OP_URL . '/controls/' . $this->type . '/js/op-wplink.js', array( 'jquery', 'wplink' ), '1.0', true );
		wp_localize_script( 
			'op-wplink', 
			'op_wplink_data', 
			array( 
				'edit_link_text'	=> __( 'Edit Link', 'optimalpress-domain' ),
				'add_link_text'		=> __( 'Add Link', 'optimalpress-domain' ),
			) 
		);

		return;
			
	}
	
	public function validate( $field_value ) {
		
		$value = $field_value;
		
		if( ! empty( $field_value ) ){
		
			$value = array(
				'url'		=> esc_url( $field_value['url'] ),
				'title'		=> sanitize_text_field( $field_value['title'] ),
				'target'	=> $field_value['target'], 
			);
			
		}
		return $value;
			
	}

	public function render_field( $value, $name ) {

		?>
		<div class="input op-control-link">
			<button class="button button-large op_open_popup_link_editor_button"><?php echo ( isset( $value['url'] ) && ! empty( $value['url'] ) ) ? esc_html__( 'Edit Link', 'optimalpress-domain' ): esc_html__( 'Add Link', 'optimalpress-domain' ); ?></button><div class="op_title_link_container" style="display:<?php echo ( ( ! empty( $value['url'] ) ) ? 'inline-block;' : 'none;' ) ?>"><span class="op_module_link_span"><?php echo ( ( isset( $value['url'] ) ) ? esc_html( $value['url'] ) : '' ); ?></span><i class="op-remove-link-button fa fa-times-circle"></i></div>
			<input type="hidden" id="<?php echo esc_attr( $name ); ?>" class="op-input op-url-input cmb_text_link <?php echo $this->field_classes; ?>" name="<?php echo $name . '[url]'; ?>" value="<?php echo ( ( isset( $value['url'] ) ) ? esc_attr( $value['url'] ) : '' ) ?>" />
			<input type="hidden" class="op-title-input" name="<?php echo $name . '[title]'; ?>" value="<?php echo ( ( isset( $value['title'] ) ) ? esc_attr( $value['title'] ) : '' ) ?>" />
			<input type="hidden" class="op-target-input" name="<?php echo $name . '[target]'; ?>" value="<?php echo ( ( isset( $value['target'] ) ) ? esc_attr( $value['target'] ) : '' ) ?>" />
		</div>
		<?php
		
	}

}

?>