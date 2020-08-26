<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\get;
use q\plugin;

// load it up ##
\q\module\acf_form::__run();

class acf_form extends \Q {
    
    static $args = array();

    /**
    * __Run
    *
    * @since       4.5.0
    */
    public static function __run()
    {

		// add extra options in module select API ##
		\add_filter( 'acf/load_field/name=q_option_module', [ get_class(), '__filter_acf_module' ], 10, 1 );

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('tab') );
		if ( 
			! isset( core\option::get('module')->acf_form )
			|| true !== core\option::get('module')->acf_form 
		){

			// h::log( 'd:>Module->acf_form is not enabled.' );

			return false;

		}

		// check for willow ##
		if( ! class_exists( 'q_willow' ) ){ return false; }

		$class = new \ReflectionClass( __CLASS__ );
		$methods = $class->getMethods( \ReflectionMethod::IS_PUBLIC );
		foreach( $methods as $key ){ $public_methods[] = $key->name; } // match format returned by get_class_methods() ##

		// register new class methods ##
		\add_action( 'after_setup_theme', function() use ( $public_methods ) {
			\q\willow\context\extend::register([ 
				'context' 	=> 'module',#str_replace( __NAMESPACE__.'\\', '', __CLASS__ ), 
				// 'lookup'	=> \q_user::get_plugin_path( 'library/view/context/' ), // allow for extended .willow lookups ##
				'class' 	=> __CLASS__,
				'methods' 	=> $public_methods // public only 
				// 'methods'	=> get_class_methods( __CLASS__ ) // all class methods ##
			]);
		}, 2 );

		// acf / fa styles to BS ##
			
		// \add_action( 'wp_enqueue_scripts', [ get_class(), '__acf_form_deregister_styles' ] );

		// before fields ##
		\add_action( 'af/form/before_fields', [ get_class(), '__af_form_before_fields' ], 10, 2 );

		// after fields ##
		\add_action( 'af/form/after_fields', [ get_class(), '__af_form_after_fields' ], 10, 2 );

		// button ##
		\add_filter( 'af/form/button_attributes', [ get_class(), '__af_form_button_attributes' ], 10, 3 );

		// reduce loaded assets ##
		\add_action( 'af/form/enqueue/key=form_5f464607465e8', [ get_class(), '__form_remove_enqueues' ] );

	}

	
	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function __filter_acf_module( $field )
    {

		// pop on a new choice ##
		$field['choices']['acf_form'] = 'ACF Form';

		// make it selected ##
		$field['default_value'][0] = 'acf_form';

		// kick back ##
		return $field;

	}


	public static function __form_remove_enqueues() {

		// Stylized select (including user and post fields)
		wp_dequeue_script( 'select2' );
		wp_dequeue_style( 'select2' );
	  
		// Date picker
		wp_dequeue_script( 'jquery-ui-datepicker' );
		wp_dequeue_style( 'acf-datepicker' );
	  
		// Date and time picker
		wp_dequeue_script( 'acf-timepicker' );
		wp_dequeue_style( 'acf-timepicker' );
	  
		// Color picker
		wp_dequeue_script( 'wp-color-picker' );
		wp_dequeue_style( 'wp-color-picker' );

	  }


	public static function __acf_form_deregister_styles(){
    
		// Deregister ACF Form style ##
		\wp_deregister_style('acf-global');
		\wp_deregister_style('acf-input');
		
		// Avoid dependency conflict ##
		\wp_register_style('acf-global', false);
		\wp_register_style('acf-input', false);

	}


	public static function __af_form_before_fields( $form, $args ){
    
		// Before AF Form
		echo '<div class="col-12 m-0 p-0">'; 
		
	}


	public static function __af_form_after_fields( $form, $args ){
    
		// After AF Form
		echo '</div>'; 
		
	}


	public static function __af_form_button_attributes(  $attributes, $form, $args  ){
    
		$attributes['class'] .= ' ml-n3 mr-3 button btn btn-primary';
    
    	return $attributes;
		
	}


	/**
	 * Public API function {~ module~acf_form ~}
	 * 
	 * @since 4.5.0
	*/
	public static function acf_form( $args = null ){

		if ( ! function_exists( 'advanced_form' ) ){

			// log ##
			h::log( 'Advanced Forms plugin is required to use this module.' );

			return false;

		}

		// sanity ##
		if( 
			is_null( $args )
			|| ! is_array( $args )
			|| ! isset( $args['config']['form_id'] ) 
		){

			h::log( 'e:>A valid form_id is required to render' );

			return false;

		}

		// h::log( $args );

		// load config ##
		$config = core\config::get([ 'context' => 'module', 'task' => 'acf_form' ])['render'];

		// test ##
		// h::log( $args );
		// h::log( 'form_id: '.$args['config']['form_id'] );

		// get return ##		
		$return = \advanced_form( $args['config']['form_id'], $config );

		// test ##
		// h::log( $return );

		// return to willow ##
		return [ 
			// 'snack'	=> self::snack(), // add snackbar ##
			'form' 	=> $return 
		];

	}


}
