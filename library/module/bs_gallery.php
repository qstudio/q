<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
use q\asset;

// load it up ##
\q\module\bs_gallery::__run();

class bs_gallery extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> str_replace( __NAMESPACE__.'\\', '', static::class ),
			'name'		=> 'Bootstrap ~ Gallery',
			'selected'	=> true,
		]);

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('tab') );
		if ( 
			! isset( core\option::get('module')->bs_gallery )
			|| true !== core\option::get('module')->bs_gallery 
		){

			// h::log( 'd:>Gallery is not enabled.' );

			return false;

		}

		// add acf fields ##
		\add_action( 'acf/init', function() { \q\plugin\acf::add_field_groups( self::add_field_groups() ); }, 1 );
		
    }




	
    /**
    * Load up ACF fields
    * 
    * @since       1.0.0
    */
    public static function add_field_groups()
    {

		// define field groups - exported from ACF ##
        $groups = array (

            'q_media_gallery'   => array(
                'key' => 'media_gallery',
				'title' => 'Gallery',
				'fields' => array(
					array(
						'key' => 'field_media_gallery',
						'label' => 'Gallery',
						'name' => 'media_gallery',
						'type' => 'gallery',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'return_format' => 'id',
						'preview_size' => 'medium',
						'insert' => 'append',
						'library' => 'all',
						'min' => 0,
						'max' => 12,
						'min_width' => '',
						'min_height' => '',
						'min_size' => '',
						'max_width' => '',
						'max_height' => '',
						'max_size' => '',
						'mime_types' => 'png, jpg',
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'post',
						),
					),
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'page',
						),
					),
				),
				'menu_order' => 3,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',

			)
		
		);

		// h::log( $groups );
		return $groups;

    }
  

}
