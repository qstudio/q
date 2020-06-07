<?php

namespace q\core;

use q\core as core;
use q\core\helper as h;

// Q Theme Config ##
use q\theme as theme;

// load it up ##
\q\core\config::run();

class config extends \Q {

    public static function run()
    {

        // filter intermediate image sizes ##
        // \add_filter( 'intermediate_image_sizes_advanced', [ get_class(), 'intermediate_image_sizes_advanced' ] );

        // add_image_sizes for all themes ##
        \add_action( 'init', [ get_class(), 'add_image_sizes' ], 1 );

        if ( \is_admin() ) {


		} else {

            // load template properties ##
            // \add_action( 'wp', [ get_class(), "load_properties" ] );

        }

        // make sure properties are loaded when AJAX requests run ##
        if ( \wp_doing_ajax() ) {

            // self::load_properties();

        }

	}


	/**
	 * Get stored config setting, merging in any new of changed settings from \q_theme::$config ##
	 */
	public static function get( $field = null ) {

		// starts with an empty array ##
		$config = [];

		// load config from JSON ##
		if ( 
			$array = include( self::get_plugin_path('q.config.php') )
		){
		
			// h::log( self::get_plugin_path('q.config.php') );

			// check if we have a 'config' key.. and take that ##
			if ( is_array( $array ) ) {

				// ok.. empty, filter in from other plugins, then check again ##
				// h::log( 'Q config NOT, empty...loading' );
				// h::log( $array );

				// assign ##
				$config = $array;

			}

		}

		// h::log( $config );
		// h::log( $config[$field] );

		// filter all config early ##
		$filter_config = \apply_filters( 'q/config/get/all', $config );

		// merge filtered data into default data ##
		$config = core\method::parse_args( $filter_config, $config );

		// now, check if we are looking for a specific field ##
		if ( 
			is_null( $field ) 
		) {

			h::log( 'Getting all config data' );

			// kick back ##
			return $config;

		}

		// h::log( 'Looking for specific Field: "'.$field.'"' );

		// check if field is set ##
		if ( 
			! isset( $config[$field] ) 
		){

			h::log( 'No matching config found for Field: "'.$field.'"' );

			return false;

		}

		// kick back specific field ##
		return $config[$field];

	}



    /**
     * Remove standard image sizes so that these sizes are not
     * created during the Media Upload process
     *
     * Tested with WP 3.2.1
     *
     * Hooked to intermediate_image_sizes_advanced filter
     * See wp_generate_attachment_metadata( $attachment_id, $file ) in wp-admin/includes/image.php
     *
     * @param $sizes, array of default and added image sizes
     * @return $sizes, modified array of image sizes
     * @author http://www.wpmayor.com/code/remove-image-sizes-in-wordpress/
     */
    public static function intermediate_image_sizes_advanced( $sizes)
    {

        unset( $sizes['slides']);
        unset( $sizes['slides-small']);
        unset( $sizes['home']);
        unset( $sizes['new-photos']);
        unset( $sizes['hero']);

        return $sizes;

    }



    /**
     * Add image sizes for all devices - so that all device images sizes are prepared when files are uploaded
     * Note: Tablet uses desktop sized images
     *
     * @since       0.1
     * @return      void
     */
    public static function add_image_sizes()
    {

        // generic ##
        \add_image_size( 'icon', 80, 80, true ); // icon ##
        \add_image_size( 'thumb', 250, 250, true ); // small thumb ##

    }



}