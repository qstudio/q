<?php

namespace q\module\sticky;

// Q ##
use q\core;

// Q Theme
use q\core\helper as h;

// load it up ##
\q\module\sticky\option::run();

class option {

    public static function run()
    {

		// add extra options in module select API ##
		\add_filter( 'acf/load_field/name=q_option_module', [ get_class(), 'filter_acf_module' ], 10, 1 );
        
    }




	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function filter_acf_module( $field )
    {

        // h::log( $field['choices'] );
        // h::log( $field['default_value'] );

		// pop on a new choice ##
		$field['choices']['sticky'] = 'Admin ~ Sticky Posts';

		// make it selected ##
		// $field['default_value'][0] = 'consent';
		
        // h::log( $field['choices'] );
        // h::log( $field['default_value'] );

         return $field;

	}
	

}
