<?php

namespace q\module;

use q\core;
use q\core\helper as h;
use q\plugin;

// load it up ##
\q\module\plugin_fa_form::__run();

class plugin_fa_form extends \Q {
    
    /**
    * __Run
    *
    * @since       4.5.0
    */
    public static function __run()
    {

		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> str_replace( __NAMESPACE__.'\\', '', static::class ),
			'name'		=> 'Plugin ~ Advanced Forms',
			'selected'	=> true,
		]);

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('tab') );
		if ( 
			! isset( core\option::get('module')->plugin_fa_form )
			|| true !== core\option::get('module')->plugin_fa_form 
		){

			// h::log( 'd:>Module->acf_form is not enabled.' );

			return false;

		}

		// check for willow ##
		if( ! class_exists( 'willow' ) ){ return false; }

		$class = new \ReflectionClass( __CLASS__ );
		$methods = $class->getMethods( \ReflectionMethod::IS_PUBLIC );
		foreach( $methods as $key ){ $public_methods[] = $key->name; } // match format returned by get_class_methods() ##

		// register new class methods ##
		\add_action( 'after_setup_theme', function() use ( $public_methods ) {
			\willow\context\extend::register([ 
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
		if ( ! \is_admin() ) {
			
			// \add_action( 'wp_enqueue_script', [ get_class(), '__form_remove_enqueues' ], 1000 );

		}

	}

	

	public static function __form_remove_enqueues() {

		// h::log( 'e:>HERE..' );

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
	 * Public API function {~ module~plugin_fa_form ~}
	 * 
	 * @since 4.5.0
	*/
	public static function plugin_fa_form( $args = null ){

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
		$config = core\config::get([ 'context' => 'module', 'task' => 'plugin_fa_form' ])['render'];

		// test ##
		// h::log( $args );
		// h::log( 'form_id: '.$args['config']['form_id'] );

		// get return ##		
		$return = \advanced_form( $args['config']['form_id'], $config );

		// test ##
		// h::log( $return );

		// return to willow ##
		return [ 
			'form' 	=> $return 
		];

	}


}
