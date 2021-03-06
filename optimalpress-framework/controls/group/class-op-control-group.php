<?php

class OP_Control_Group extends Optimalpress_Control {
	
	/**
	 * Indicates if can reorder elements in the list.
	 * @var Array
	 */
	public $sortable;
	
	/**
	 * All control object used in the group.
	 * @var ArrayObj
	 */
	public $controls;
	
	/**
	 * Add new element in the group text. By default "Add New".
	 * @var string
	 */
	private $add_new_button_text;
	
	/**
	 * Default title or each element in the group.
	 * @var string
	 */
	private $default_title;
	
	/**
	 * Indicates the name of the control to takes the value for add to the title.
	 * @var string
	 */
	private $dynamic_title;
	
	/**
	 * Dependencies all controls inside the group.
	 * @var Array
	 */
	private $deps;
	
	/**
	 * The name of all controls used in the group.
	 * @var Array
	 */
	private $controls_used;
		
	public function __construct( $type, $name, $control_args ) {
		
		parent::__construct( $type, $name, $control_args );
		
		$this->default_value		= array( 'lastkey'	=> '', 'fields' => array() );
		$this->sortable				= isset( $control_args['sortable'] ) ? $control_args['sortable'] : false;
		$this->default_title		= isset( $control_args['default_title'] ) ? $control_args['default_title'] : '';
		$this->dynamic_title		= isset( $control_args['dynamic_title'] ) ? $control_args['dynamic_title'] : '';
		$this->add_new_button_text	= isset( $control_args['add_new_button_text'] ) ? $control_args['add_new_button_text'] : __( 'Add New', 'optimalpress-domain' );
		$this->deps					= array();
		$this->controls_used		= array();
		
		$g_controls = array();
		
		$this->deps = parent::get_deps();

		foreach( $control_args['fields'] as $control ) {
			
			$path = ( isset( $control['is_custom'] ) && $control['is_custom'] ) ? OP_CUSTOM_CONTROLS_PATH . '/' : OP_PATH . '/controls/';
			
			require_once( $path . $control['type'] . '/class-op-control-' . $control['type'] . '.php' );
			$field_classname 		= 'OP_Control_' . $control['type'];
			$control['group_name']	= $name;
			$control_ins			= new $field_classname( $control['type'], $control['name'], $control );
			$g_controls[]			= $control_ins;
			
			$deps					= apply_filters( 'op_apply_control_deps', $control_ins->get_deps(), $control, $control_ins );
			$controls_used			= apply_filters( 'op_apply_controls_used', $control_ins->get_controls_used(), $control, $control_ins );
	
			if( $deps ) {
				
				foreach( $deps as &$dep ) {
					$dep['group'] = $name;
				}
				
				$this->deps				=  array_merge( $this->deps, $deps );
				
			}
			
			if( $controls_used ) {
			
				$this->controls_used	=  array_merge( $this->controls_used, $controls_used );
				
			}
			
		}
		
		$this->controls	= $g_controls;
		
	}
	
	public function enqueue_scripts_styles() {
		
		wp_enqueue_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.css', array(), '4.1.0' );				
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'optimalpress-groups', OP_URL . '/controls/'. $this->type . '/js/op-groups.js', array( 'jquery' ), '1.0', true );	
		wp_enqueue_style( 'optimalpress-groups-jquery-ui', OP_URL . '/controls/public/css/jquery-ui.min.css', array(), '1.0' );
		
		wp_localize_script( 
			'optimalpress-groups', 
			'optimalpressGroupsData', 
			array( 
				'deleteGroupText'	=> __( 'Are you sure?', 'op-domain' ),
			) 
		);
			
		return;
			
	}
	
	public function validate( $group_fields ) {

		$group_options = array();
				
		foreach( $group_fields as $key => $field ){
	
			if( $key != 'optimalpress-lastkey' && $key != 'lastkey' ){
			
				foreach( $this->controls as $control ){

					$field_value	= isset( $field[ $control->name ] ) ? $field[ $control->name ]: '';
					
					$validate_option 	= $control->validate( $field_value );
					$group_options[ $key ][ $control->name ] = $validate_option;

				}
				
			}
			
		}
		
		$validated_group_options = array(
			'lastkey'	=> isset( $group_fields['lastkey'] ) ? $group_fields['lastkey'] : 0,
			'fields'	=> $group_options
		);

		return $validated_group_options;
			
	}

	public function render( $group_fields, $name = false ) {
		
		$name			= ( ! $name ) ? $this->name : $name;
		$last_key 		= isset( $group_fields['lastkey'] ) && ! empty( $group_fields['lastkey'] ) ? intval( $group_fields['lastkey'] ) : 1;
		$sortable_class	=  $this->sortable ? ' op-sortable' : '';

		?> 
			
		<div class="op-group-wrapper standard op-loop op_loop-<?php echo esc_attr( $name ); ?><?php echo esc_attr( $sortable_class ); ?> op-meta-group" id="field-<?php echo esc_attr( $name ); ?>" data-dynamic-title="<?php echo esc_attr( $this->dynamic_title ); ?>" >
			<input type="hidden" class="op-lastkey" id="<?php echo esc_attr( $name ); ?>[lastkey]" name="<?php echo esc_attr( $name ); ?>[lastkey]" value="<?php echo esc_attr( $last_key ); ?>"/>
		<?php

		foreach( $group_fields['fields'] as $key => $field ){

			?>
			<div id="<?php echo $name . '[' . $key . ']'?>" class="op-group op_group-<?php echo esc_attr( $name ); ?>">
			<div class="op-group-heading"><a href="#" class="op-group-title"><i class="fa fa-arrows"></i><?php echo esc_html( $this->default_title );?></a><a href="#" class="op-group-remove" title="Remove"><i class="fa fa-times"></i> Remove</a></div>
			<div class="op-controls">
			<?php
						
			foreach( $this->controls as $control ){
				
				$default_value 	= isset( $control->default_value ) ? $control->default_value : '';
				$value 			= isset( $field[ $control->name ] ) ? $field[ $control->name ] : $default_value;
				
				$control->render( $value, $name . '[' . $key . ']' . '[' .  $control->name . ']', $this->name );	
				
			}
			
			?></div></div><?php				
			
		}

		?>
			<div id="<?php echo $name . '[optimalpress-lastkey]'; ?>" data-lastkey="<?php echo esc_attr( $last_key ); ?>" class="op-group op_group-<?php echo esc_attr( $name ); ?> op-hide last to-copy">
			<div class="op-group-heading"><a href="#" class="op-group-title"><i class="fa fa-arrows"></i><?php echo esc_html( $this->default_title );?></a><a href="#" class="op-group-remove" title="Remove"><i class="fa fa-times"></i> Remove</a></div>
			<div class="op-controls">
			<?php

			foreach( $this->controls as $control ){

				$default_value 	= isset( $control->default_value ) ? $control->default_value : '';
				$control->render( $default_value, $name . '[optimalpress-lastkey]' . '[' . $control->name . ']' );	

			}

			?></div></div><?php
			
			?>
			<div class="op-group-add"><a href="#" class="docopy docopy-<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $this->add_new_button_text ); ?></a><div class="list-item-description"><?php echo esc_html( $this->description ); ?></div></div>
		</div> 
		<?php
		
	}
	
	public function get_deps(){
		
		$return	= false;
		
		if( ! empty( $this->deps ) ) {
			return $this->deps;
		}
		
		return $return;
				
	}
	
	public function get_controls_used(){
		
		return $this->controls_used;
		
	}
	
}

?>