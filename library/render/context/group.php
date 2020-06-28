<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
use q\plugin;
use q\render;

class group extends \q\render {

	public static function __callStatic( $function, $args ) {

        return self::run( $args ); 
	
	}

	public static function run( $args = [] ){

		// check for extensions ##
		$extension = render\extension::get( $args['context'], $args['task'] );

		if (
			! \method_exists( get_class(), 'get' ) // base method is get\meta ##
			&& ! $extension // look for extensions ##
		) {

			render\log::set( $args );

			h::log( 'e:>Cannot locate method: '.__CLASS__.'::'.$args['task'] );

            return false;

		}

        // validate passed args ##
        if ( ! render\args::validate( $args ) ) {

			render\log::set( $args );
			
			// h::log( 'd:>Bunked here..' );

            return false;

		}

		// base class ##
		if ( 
			\method_exists( get_class(), 'get' ) 
		){

			// 	h::log( 'load base method: '.$extension['class'].'::'.$extension['method'] );

			// call render method ##
			self::get( self::$args );

		// extended class ##
		} elseif (
			$extension
		){

			// 	h::log( 'load extended method: '.$extension['class'].'::'.$extension['method'] );

			// h::log( 'd:>render extension..' );
			$extension['class']::{ $extension['method'] }( self::$args );

		}

        // // validate passed args ##
        // if ( ! render\args::validate( $args ) ) {

        //     render\log::set( $args );

        //     return false;

		// }
		
        // // get field names from passed $args ##
        // if ( ! render\get::fields() ) {

        //     render\log::set( $args );

        //     return false;

		// }
		
		// h::log( self::$fields );

		// Now we can loop over each field ---
		// running callbacks ##
		// formatting none string types to strings ##
		// removing placeholders in markup, if no field data found etc ##
		render\fields::prepare();
		
		// h::log( self::$fields );

        // Prepare template markup ##
        render\markup::prepare();

        // optional logging to show removals and stats ##
        render\log::set( $args );

        // return or echo ##
        return render\output::return();

	}
	

	// ---------- methods ##


	/**
     * Get group data via meta handler
     *
     * @param       Array       $args
     * @since       1.3.0
	 * @uses		define
     * @return      Array
     */
    public static function get( $args = null ) {

        // sanity ##
        if ( 
            is_null( self::$args ) 
            || ! is_array( self::$args )
            // || ! isset( self::$args['fields'] )
        ) {

			// log ##
			h::log( self::$args['task'].'~>e:Error in passed parameter $args');

            return false;

        }

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
			h::log( self::$args['task'].'~>e:Error extracting field list from passed data');

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
        if ( ! render\args::is_enabled() ) {

            return false;

       }    

        // h::log( self::$fields );

        // we should do a check if $fields is empty after all the filtering ##
        if ( 
            0 == count( self::$fields ) 
        ) {

			// log ##
			h::log( self::$args['task'].'~>n:Fields array is empty, so nothing to process...');

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
		$array = 
			\get_fields( 
				isset( self::$args['config']['post']->ID ) ? 
				self::$args['config']['post']->ID : 
				false 
			);
		
		// h::log( $array );

        // sanity ##
        if ( 
            ! $array 
            || ! is_array( $array )
        ) {

			// log ##
			h::log( self::$args['task'].'~>n:Post has no ACF field data or corrupt data returned');

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
        $group = self::$args['task'];

        // try to get fields ##
        $array = plugin\acf::get_field_group( $group );

        // h::log( $array );

        if ( 
            ! $array
            || ! \is_array( $array )
        ) {

			// log ##
			h::log( self::$args['task'].'~>e:No valid ACF field group returned for Group: "'.$group.'"');

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
			h::log( self::$args['task'].'~>e:Error in passed $args');

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
			h::log( self::$args['task'].'~>e:Error in passed $args or $fields');

            return false;

		}
		
		// h::log( self::$acf_fields );

        if ( 
            isset( self::$args['task'] )
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
