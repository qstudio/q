<?php

namespace q\extension\sticky;

// Q ##
use q\core;

// Q Theme
use q\core\helper as h;

// load it up ##
\q\extension\sticky\option::run();

class option extends \Q {

    public static function run()
    {

		// add extra options in extension select API ##
		\add_filter( 'acf/load_field/name=q_option_extension', [ get_class(), 'filter_acf_extension' ], 10, 1 );
		
		// add fields to Q settings ##
		\add_filter( 'q/plugin/acf/add_field_groups/q_option_extension', [ get_class(), 'filter_acf_extension_conditional' ], 10, 1 );
        
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
		$field['choices']['sticky'] = 'Sticky Posts';

		// make it selected ##
		// $field['default_value'][0] = 'consent';
		
        // h::log( $field['choices'] );
        // h::log( $field['default_value'] );

         return $field;

	}
	

	public static function filter_acf_extension_conditional( $array ) 
    {

        // test ##
        // h::log( $array );

        // lets add our fields ##
        array_push( $array['fields'], [

			'key' => 'field_q_option_extension_consent',
			'label' => 'Privacy URL',
			'name' => 'q_option_extension_consent',
			'type' => 'post_object',
			'instructions' => 'Required for the Consent System to work.',
			'required' => 1,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_q_option_extension',
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
