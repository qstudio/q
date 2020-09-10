<?php

namespace q\module\consent;

// Q ##
use q\core;

// Q Theme
use q\core\helper as h;

// load it up ##
\q\module\consent\option::run();

class option extends \Q {

    public static function run()
    {

		// add extra options in module select API ##
		\add_filter( 'acf/load_field/name=q_option_module', [ get_class(), 'filter_acf_module' ], 10, 1 );
		
		// add fields to Q settings ##
		\add_filter( 'q/plugin/acf/add_field_groups/q_option_module', [ get_class(), 'filter_acf_module_conditional' ], 10, 1 );
        
    }




	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function filter_acf_module( $field )
    {

		// pop on a new choice ##
		$field['choices']['consent'] = 'Q ~ Consent System';

		// make it selected ##
		$field['default_value'][0] = 'consent';
		
		return $field;

	}
	

	public static function filter_acf_module_conditional( $array ) 
    {

        // test ##
        // h::log( $array );

        // lets add our fields ##
        array_push( $array['fields'], [

			'key' => 'field_q_option_module_consent',
			'label' => 'Privacy URL',
			'name' => 'q_option_module_consent',
			'type' => 'post_object',
			'instructions' => 'Required for the Consent System to work.',
			'required' => 1,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_q_option_module',
						'operator' => '==',
						'value' => 'consent',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'post_type' => array(
				0 => 'page',
			),
			'taxonomy' => '',
			'allow_null' => 0,
			'multiple' => 0,
			'return_format' => 'id',
			'ui' => 1,
        
        ]);

        // h::log( $array['fields'] );

        // kick it back, as it's a filter ##
        return $array;

    }


}
