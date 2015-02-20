<?php

class OP_Control_NoteBox extends Optimalpress_Control {
	
	/**
	 * Items for the notebox (normal, info, success, warning, error). 
	 * @var String
	 */
	private $status;
	
	/**
	 * Indicates if the notebox will be hidden or not. 
	 * @var bool
	 */
	private $hidden;
	
	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
		
		$this->is_hidden	= isset( $control_args['hidden'] ) ? ' op-hide' : '';
		$this->status		= isset( $control_args['status'] ) ? $control_args['status'] : 'normal';

	}
	
	public function enqueue_scripts_styles() {
		
		wp_enqueue_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css', array(), '4.1.0' );	
		
		return;
	
	}
	
	public function validate( $field_value ) {
	
		return null;
	
	}

	public function render( $value, $name = false ) {
		
		$name	= ( ! $name ) ? $this->name : $name;
		
		?>
		<div class="<?php echo esc_attr( $this->is_hidden );?> not-sc op-control-notebox-wrapper note-<?php echo esc_attr( $this->status ); ?> <?php echo esc_attr( $this->container_classes ); ?>" id="field-<?php echo esc_attr( $name ); ?>" >

			<?php 
			switch( $this->status ) {
				
				case 'normal':
					$icon_class = 'fa-lightbulb-o';
					break;
				case 'info':
					$icon_class = 'fa-info-circle';
					break;
				case 'success':
					$icon_class = 'fa-check-circle';
					break;
				case 'warning':
					$icon_class = 'fa-exclamation-triangle';
					break;
				case 'error':
					$icon_class = 'fa-times-circle';
					break;
				default:
					$icon_class = 'fa-lightbulb-o';
					break;
					
			}
			?>
			<i class="fa <?php echo esc_attr( $icon_class ); ?>"></i>
			<div class="label"><?php echo esc_attr( $this->label ); ?></div>
			<?php if( ! empty( $this->description ) ) : ?>
				<div class="description" ><p><?php echo esc_html( $this->description ); ?></p></div>
			<?php endif; ?>

		</div>
		<?php
		
	}

}

?>