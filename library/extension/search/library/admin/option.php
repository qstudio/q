<?php

namespace q\search\admin;

// Q ##
use q\core;

// Q Theme
use q\theme\core\helper as h;

// load it up ##
\q\search\admin\option::run();

class option extends \q_theme {

    public static function run()
    {

        // add extra options in asset control API ##
		// \add_filter( 'q/plugin/acf/add_field_groups/q_option_ui', [ get_class(), 'filter_acf_option_ui' ], 10, 1 );
        
    }



    
    /**
     * Add new Asset control to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function filter_acf_option_ui( $array )
    {

		// h::log( $array );

		// add new option ##
		$array['fields'][] = [
			'key' => 'field_q_option_q_search',
			'label' => 'Q Search',
			'name' => 'q_option_q_search',
			'type' => 'checkbox',
			'instructions' => 'Control Assets for <a href="https://github.com/qstudio/q-search" target="_blank">Q Search</a>',
			'required' => 0,
			'conditional_logic' => 0,
			'choices' => array(
				'css' => 'CSS',
				'js' => 'JS',
			),
			'default_value' => array(
				0 => 'css',
				1 => 'js'
			),
			'layout' => 'horizontal',
			'return_format' => 'value',
		];

        return $array;

    }


}
