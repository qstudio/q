<?php

namespace q\module\sticky;

// Q ##
use q\core;

// Q Theme
use q\core\helper as h;

class option {

    function hooks(){

		// add extra options in module select API ##
		\add_filter( 'acf/load_field/name=q_option_module', [ $this, 'filter_acf_module' ], 10, 1 );
        
    }

	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    function filter_acf_module( $field ){

		// pop on a new choice ##
		$field['choices']['sticky'] = 'Admin ~ Sticky Posts';

		return $field;

	}

}
