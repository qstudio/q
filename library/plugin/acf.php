<?php

namespace q\plugin;

use q\core;
use q\core\helper as h;

\q\plugin\acf::__run();

class acf extends \Q {

	public static function __run(){

		\add_filter( 'q/hook/wp_enqueue_script/script_loader_tag/avoid', function( $array ){

			// add files to skip ##
			$skip = [
				'acf', // main acf file ##
				'acf-input', // acf input ##
				'acf-pro-input' // acf input pro ##
			];

			$return = array_merge( $skip, $array );

			// check ##
			// h::log( $return );

			// kick back to filter ##
			return $return;

		});

	}

    /**
    * Add ACF Fields
    *
    * @since    2.0.0
    */
    public static function add_field_groups( Array $groups = null )
    {

        // get all field groups ##
		// $groups = self::get_fields();
		
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {

            h::log( 'e:>ACF Missing, please install or activate: "https://www.advancedcustomfields.com/"' );

            return false;

        }

        if ( 
            ! $groups 
            || ! is_array( $groups )
        ) {

            h::log( 'd:>No groups to load.' );

            return false;

        }

		// loop over gruops ##
        foreach( $groups as $key => $value ) {

			// h::log( 'Filter: '.'q/plugin/acf/add_field_groups/'.$key );

            // filter groups -- NEW ##
			$value = \apply_filters( 'q/plugin/acf/add_field_groups/'.$key, $value );
			
            // h::log( $value );

            // load them all up ##
            \acf_add_local_field_group( $value );

        }

    }


    /**
     * Get field group
     */
    public static function get_field_group( String $group = null ) {

        // sanity ##
        if ( ! \function_exists('acf_get_field_group') ) {

            h::log( 'q:>function "acf_get_field_group" not found' );

            return false;

        }

        // check if string passed ##
        if ( is_null( $group ) ) {

            h::log( 'e:> No "group" string passed to method.' );

            return false;

        }

        // look for field group and return boolen if fails ##
        if ( ! $array = \acf_get_fields( $group ) ) {

            h::log( 'd:>Group: "'.$group.'" not found.' );

            return false;

        }

        // filter ##
        $array = \apply_filters( 'q/plugin/acf/get_field_group/'.$group, $array );

        // return ##
        return $array;

    }   


}
