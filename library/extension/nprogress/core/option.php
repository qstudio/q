<?php

namespace q\extension\nprogress;

// Q ##
use q\core;
use q\core\helper as h;

// load it up ##
\q\extension\nprogress\option::run();

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

		// pop on a new choice ##
		$field['choices']['nprogress'] = 'Progress Bar';

		return $field;

	}
	


}
