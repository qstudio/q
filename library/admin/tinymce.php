<?php

namespace q\admin;

use q\plugin as q;
use q\core;
use q\core\helper as h;

// load it up ##
// \q\admin\tinymce::__run();

class tinymce {

	function __construct(){

	}

    function hooks(){

        // if ( \is_admin() ) {

			\add_filter( 'tiny_mce_before_init', [ get_class(), 'style_formats' ] );
			
			\add_filter( 'mce_buttons_2', [ get_class(), 'buttons' ], 10, 1 );

			\add_filter( 'mce_external_plugins', [ get_class(), 'code_plugin' ], 10, 1 );

        // }

	}

	public static function code_plugin( $plugins ){

		$plugins['code'] = 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.9.4/plugins/code/plugin.min.js';
		
		return $plugins;
	
	}

	public static function buttons( $buttons ) {   
		
		/**
		 * Add in a core button that's disabled by default
		 */
		$buttons[] = 'code';     
		$buttons[] = 'fullscreen';
	
		return $buttons;

	}

    public static function style_formats( $init_array ){

		// Define the style_formats array
		$style_formats = array(
			array(
				'title' => 'code',
				'inline' => 'code',
				'classes' => 'tinymce-code',
				'wrapper' => false,
				'styles' => 'background-color: #eee',
			),
			// add more styles here if you want to...
			// https://codex.wordpress.org/TinyMCE_Custom_Styles
	
		);
		
		// Insert the array, JSON ENCODED, into 'style_formats'
		$init_array['style_formats'] = json_encode($style_formats);

		return $init_array;

	}	

}
