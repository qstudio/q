<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
use q\plugin;
// use q\render;

class get extends \q\render {

    /**
     * Get Defined Fields
     */
    public static function fields(){

        // sanity ##
        if ( 
            is_null( self::$args ) 
            || ! is_array( self::$args )
            // || ! isset( self::$args['fields'] )
        ) {

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'Error in passed parameter $args'
			]);

            return false;

        }

		// h::log( self::$args['config']['post'] );

        // Get all ACF field data for this post ##
        if ( ! self::acf_fields() ) {

            return false;

        }

        // get all fields defined in this group -- pass to $args['fields'] ##
        if ( ! self::group_fields() ) {

            return false;

        }

        // h::log( self::$args[ 'fields' ] );

        // get field names from passed $args ##
        $array = array_column( self::$args[ 'fields' ], 'name' );

        // sanity ##
        if ( 
            ! $array 
            || ! is_array( $array )
        ) {

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'Error extracting field list from passed data'
			]);

            return false;

        }

        // h::log( $array );

        // assign class property ##
        self::$fields = $array;
		// h::log( self::$fields );

        // remove skipped fields, if defined ##
        self::skip();

        // if group specified, get only fields from this group ##
        self::group();

        // check if feature is enabled ##
        if ( ! args::is_enabled() ) {

            return false;

       }    

        // h::log( self::$fields );

        // we should do a check if $fields is empty after all the filtering ##
        if ( 
            0 == count( self::$fields ) 
        ) {

			// log ##
			log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' => 'Fields array is empty, so nothing to process...'
			]);

            return false;

		}
		
		// h::log( self::$fields );

        // positive ##
        return true;

    }

	

    /**
     * Get ACF Fields
     */
    public static function acf_fields(){

		if ( ! function_exists( 'get_fields' ) ) {

			h::log( 'ACF Plugin missing' );

			return false;

		}

        // get fields ##
		$array = \get_fields( isset( self::$args['config']['post']->ID ) ? self::$args['config']['post']->ID : false );
		
		// h::log( $array );

        // sanity ##
        if ( 
            ! $array 
            || ! is_array( $array )
        ) {

			// log ##
			log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' => 'Post: '.$post.' has no ACF field data or corrupt data returned'
			]);

            return false;

        }

        // h::log( $array );

        return self::$acf_fields = $array;

    }



	/**
	 * Get ACF Field Group from passed $group reference
	 */
    public static function group_fields(){

        // assign variable ##
        $group = self::$args['group'];

        // try to get fields ##
        $array = plugin\acf::get_field_group( $group );

        // h::log( $array );

        if ( 
            ! $array
            || ! \is_array( $array )
        ) {

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'No valid ACF field group returned for Group: "'.$group.'"'
			]);

            return false;

        }

        // filter ##
        $array = core\filter::apply([ 
            'parameters'    => [ 'fields' => $array ], // pass ( $fields ) as single array ##
            'filter'        => 'q/render/get/group_fields/'.$group, // filter handle ##
            'return'        => $array
        ]); 

        // assign to class property ##
        self::$args['fields'] = $array;

        // h::log( $array[0] );

        // return
        return true;

    }



	/**
	 * Skip fields marked to avoid
	 */
    public static function skip(){

        // sanity ##
        if ( 
            ! self::$args 
            || ! is_array( self::$args )
        ) {

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'Error in passed $args'
			]);

            return false;

        }

        if ( 
            isset( self::$args['skip'] ) 
            && is_array( self::$args['skip'] )
        ) {

            // h::log( self::$args['skip'] );
            self::$fields = array_diff( self::$fields, self::$args['skip'] );

        }

    }



	/**
	* Get the fields from the listed ACF group, removing fields returned form acf_fields()
	*/
    public static function group(){

        // sanity ##
        if ( 
            ! self::$args 
            || ! is_array( self::$args )
            || ! self::$fields
            || ! is_array( self::$fields )
        ) {

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'Error in passed $args or $fields'
			]);

            return false;

		}
		
		// h::log( self::$acf_fields );

        if ( 
            isset( self::$args['group'] )
        ) {

            // h::log( 'Removing fields from other groups... BEFORE: '.count( self::$fields ) );
            // h::log( self::$fields );

            self::$fields = array_intersect_key( self::$acf_fields, array_flip( self::$fields ) );

            // h::log( 'Removing fields from other groups... AFTER: '.count( self::$fields ) );

        }

        // kick back ##
        return true;

    }



}