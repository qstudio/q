<?php

namespace q\extension\search;

// Q ##
use q\core;
use q\core\helper as h;

// load it up ##
\q\extension\search\option::run();

class option extends \Q {

    public static function run()
    {

		// add extra options in extension select API ##
		\add_filter( 'acf/load_field/name=q_option_extension', [ get_class(), 'filter_acf_extension' ], 10, 1 );
		
    }




	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function filter_acf_extension( $field )
    {

        // h::log( $field['choices'] );
        // h::log( $field['default_value'] );

		// pop on a new choice ##
		$field['choices']['search'] = 'AJAX Search <a href="://qstudio.us/willow/extension/search" target="_blank">Read More</a>'; 
		h::log( 't:>Create help file system... perhaps opens here in modal with fetch via API??' );
		// $field['choices']['banner'] = '@todo - News Banner';

		// make it selected ##
		// $field['default_value'][0] = 'device';
		
        // h::log( $field['choices'] );
        // h::log( $field['default_value'] );

         return $field;

	}
	


}
