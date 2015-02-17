<?php

/**
 * The control class, the parent of all controls.
 */
 
class Optimalpress_Control {
	
	/**
	 * Control type
	 * @var String
	 */	
	public $type;
	
	/**
	 * Unique name for the field
	 * @var String
	 */
	public $name;
	
	/**
	 * Label for the field
	 * @var String
	 */
	public $label;
	
	/**
	 * Description for the field
	 * @var String
	 */
	public $description;
	
	/**
	 * Default value for the field
	 * @var String|Array
	 */
	public $default_value;
	
	/**
	 * Dependencies for the field
	 * @var String|Array
	 */
	public $dependency;
	
	/**
	 * Class name for the control container
	 * @var String
	 */
	public $container_class;
	
	/**
	 * Class name for the field it self
	 * @var String
	 */
	public $field_class;
	
	/**
	 * Indicating whether the control is custom
	 * @var bool
	 */
	public $is_custom;
	
	/**
	 * Setup the control object
	 *
	 * @param String 	$type			Control type
	 * @param String 	$name			Unique name for the field
	 * @param Array 	$control_args	Contains the control args needed to create each control
	 */
	public function __construct( $type, $name, $control_args ) {
					
		$this->type					= $type;
		$this->name 				= $name;
		$this->is_custom			= ( isset( $control_args['is_custom'] ) ) ? $control_args['is_custom'] : false;
		$this->label 				= ( isset( $control_args['label'] ) ) ? $control_args['label'] : '';
		$this->description 			= ( isset( $control_args['description'] ) ) ? $control_args['description'] : '';
		$this->default_value		= ( isset( $control_args['default'] ) ) ? $control_args['default'] : '';
		$this->container_classes	= ( isset( $control_args['container_class'] ) ) ? sanitize_html_class( $control_args['container_class'] ) : '';	
		$field_class				= ( isset( $control_args['group_name'] ) && ! empty( $control_args['group_name'] ) ) ? sanitize_html_class( $control_args['group_name'] ) . '-' . $this->name : 'op-single ' . $this->name;
		$field_extra_classes		= ( isset( $control_args['field_class'] ) ) ? sanitize_html_class( $control_args['field_class'] ) : '';
		$this->field_classes		= $field_class . ' ' . $field_extra_classes;
		$this->dependency			= '';
		
		if( isset( $control_args['dependency'] ) && is_array( $control_args['dependency'] ) ) {
			
			$this->dependency = array(
			
				'field' 	=> ( isset( $control_args['dependency']['field'] ) ) ? $control_args['dependency']['field'] : '',
				'values'	=> ( isset( $control_args['dependency']['values'] ) ) ? $control_args['dependency']['values'] : '',
			
			);
			
		}
		
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
		
	}
	
	/**
	 * Enqueue the scripts and styles that each control needs
	 * 
	 */
	public function enqueue_scripts_styles() {
	
		return;
			
	}
	
	/**
	 * Validate function for each control
	 *
	 * @param mixed 	$value	The value of the field to be validated.
	 *
	 * @return mixed Returns the validated value
	 */
	public function validate( $value ) {

		return $value;
			
	}
	
	/**
	 * Render the control in frontend
	 * 
	 * @param mixed 	$value	The value of the field.
	 * @param string	$name 	The name of the field.
	 */
	public function render( $value, $name ) {
		
		?>
		
		<div class="op-field op-control-<?php echo $this->type; ?>-wrapper <?php echo $this->container_classes; ?>" id="field-<?php echo esc_attr( $name ); ?>" >
			
			<div class="label">
			<?php if ( ! empty( $this->label ) ) : ?> 
			<label for="<?php echo esc_attr( $name ); ?>">
			
			<span><?php echo esc_html( $this->label ); ?></span>
			
			</label>
			<?php endif; ?>
			
			<?php if ( ! empty( $this->description ) ) : ?> 
				<p class="description"> <?php echo esc_html( $this->description ); ?></p>
			<?php endif; ?>
			</div>
			<div class="field">
				<?php $this->render_field( $value, $name ); ?>
			</div>
		</div>
		
		<?php
		
	}
	
	/**
	 * Render the control in frontend (without the structure of the "standard" render)
	 *
	 * @param mixed 	$value	The value of the field.
	 * @param string	$name 	The name of the field.
	 */
	public function render_field( $value, $name ) {
		
		return;
		
	}
	
	public function get_deps() {
		
		$return	= false;
		
		if( ! empty( $this->dependency ) ) {
		
			$return	= array(
				array( 
					'type' 			=> $this->type,
					'field' 		=> $this->name,
					'depends_of' 	=> $this->dependency['field'],
					'values' 		=> $this->dependency['values'],
					'group' 		=> false,
				)
			);
			
		}
		
		return $return;
		
	}
	
	public function get_controls_used() {

		return array( $this->type );
		
	}
	
	public function get_controls_inst() {

		return array( $this );
		
	}
}

?>