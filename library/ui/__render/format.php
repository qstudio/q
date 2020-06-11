<?php

namespace q\ui\render;

use q\core;
use q\core\helper as h;
use q\ui;

class format extends ui\render {

    /**
     * Check allowed formats based on passed $value, format and return a string ready for markup  
     * 
     * @return      String
     */
    public static function field( String $field = null, $value = null ) {

        // sanity ##
        if ( is_null( $field ) ) {

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'No field value passed to method.'
			]);

			// h::log( 'Field value: '.$value );

            return false;

        }

        // sanity ##
        if ( is_null( $value ) ) {

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'No value passed to method.'
			]);

			// h::log( 'Field value: '.$value );

            return false;

        }

        // Check if there are any allowed formats ##
        // Also runs filters to add custom formats ##
        $formats = self::get_allowed();

        if ( 
            ! $formats
            || ! \is_array( $formats ) 
        ) {

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'No formats allowed in plugin or array corrupt'
			]);

            return false;

        }

        // Now check the format of $value - Array requires repeat check on each row ##
        $format = self::get( $value, $field );

        // now try to format value ##
		$return = self::apply( $value, $field, $format );
		
        // self::$fields should all be String values by now, ready for markup ##
        return $return;

    }


    /**
     * Allow text field to be filtered ##
     * 
     */
    public static function apply( $value = null, String $field = null, String $format = null )
    {

        // sanity ##
        if ( 
            is_null( $value )
            || is_null( $field )
            || is_null( $format )
        ) {

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'Error in parameters passed to "apply", $value returned empty and field removed from $fields'
			]);

            // this item needs to be removed from self::$fields
            fields::remove( $field );

             // we do not return the $value either ##
            return false;

        }

        // h::log( 'Checking Format for - Field: '.$field.' with method: '.$format );

        // we can now distribute the $value to the relevant format method ##
        if (
            ! method_exists( __CLASS__, $format )
            || ! is_callable( array( __CLASS__, $format ) )
        ){

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'handler wrong - class: '.__CLASS__.' / method: '.$format
			]);

            // this item needs to be removed from self::$fields
            fields::remove( $field );

            // we do not return the $value either ##
            return false; 

        }

        // call class method and pass arguments ##
        $value = call_user_func_array (
            array( __CLASS__, $format )
            ,   array( $value, $field )
        );

        if ( ! $value ) {

            // h::log( 'Handler method returned bad OR empty data for Field: '.$field );

            // this item needs to be removed from self::$fields
			// self::remove_field( $field, 'Removed by "apply" due to bad or empty data' );
			
			// h::log( 'Field value bad: '.$field );

            return false; // we do not return the $value either ##

        }

        // test returned data ##
		// h::log( self::$fields );
		// h::log( 'Field value now: '.$value );

        // fields are filtered and saved by each type handler, as new fields might be added or removed internally ##

        // kick back ##
        return true;

    }



    /**
     * Get format of $field $value from defined list of allowed formats ##
     * 
     */
    public static function get( $value = null, $field = null )
    {

        // sanity ##
        if ( 
            is_null( $value )
            || is_null( $field )
        ) {

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'Error in parameters passed to check_format'
			]);

            return false;

        }

        // get formats ##
        $formats = self::get_allowed();
        // h::log( $formats );

        // tracker, if we find a match ##
        $tracker = false;

        // assign default in case we don't find a matching type ##
        // this is alterable via a filter ##
        $return = \apply_filters( 'q/render/format/default', 'format_text' ); 

        // h::log( 'Default method is: '.$return );

        // loop over formats and search for a match ##
        foreach ( $formats as $format => $format_value ){

            // h::log( 'Checking type: '.$format_value['type'] );

            if ( ! function_exists( $format_value['type'] ) ) {

				// log ##
				log::add([
					'key' => 'notice', 
					'field'	=> __FUNCTION__,
					'value' => 'Function not found: '.$format_value['type']
				]);

                continue;

            }

            // h::log( 'function exists: '.$format_value['type'] );

            // boolean check ## is_TYPE === true
            if ( 
                TRUE === call_user_func_array( $format_value['type'], array( $value ) ) 
            ) {

                // log ##
                // h::log( 'Field value: '.$field.' is Type: '.$format_value['type'].' Format with: '.$format_value['method'] );

                // update tracker ##
                $tracker = true;

                // field type assigned ##
                $return = $format_value['method'];

            }

        }

        // note use of default type if no match found ##
        if ( false === $tracker ) {

			// log ##
			log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' => 'No valid value type found for field: '.$field.' so assigned: '.$return
			]);

        }

        // final filter on field format type ##
        $return = \apply_filters( 'q/render/format/get/'.self::$args['group'].'/'.$field, $return );

        // kick back ##
        return $return;

    }



    /**
     * Format text - allow for external filtering ##
     * 
     */
    public static function format_text( $value = null, $field = null )
    {

        // h::log( $value );

        return \apply_filters( 'q/render/format/text/'.self::$args['group'].'/'.$field, $value );

    }


    /**
     * Allow integer field to be filtered ##
     * 
     */
    public static function format_integer( $value = null, $field = null )
    {

        return \apply_filters( 'q/render/format/integer/'.self::$args['group'].'/'.$field, $value );

    }



    /**
     * Format Array values
     * These need to be looped over and each value passed back into the format() process
     * 
     * Array data "SHOULD" come from a repeater
     * which has one single %placeholder% and markup in a property with a name matching key to the field name
     * we need to update the template based on number of array items and defined markup with numbered values ##
     * 
     */
    public static function format_array( $value = null, $field = null )
    {

        // allow filtering early ##
        $value = \apply_filters( 'q/render/format/array/'.self::$args['group'].'/'.$field, $value );

        // array of arrays containing named indexes ( not WP_Post Objects ) needs to be be marked up as a block, like an Object ##

        // add check to see if array is a collection of array - as exported by repeater fields ##
        if ( 'repeater' == fields::get_type( $field ) ) {

            // h::log( 'Array is a repeater' );

            self::format_array_repeater( $value, $field );

        } else {

            // check how many items are in array and format ##
            $count = 0;

            // we need to loop over the array and check what each the value of each key using self::format()
            // Formats that are not registered in self::$formats will be removed ## 
            foreach( $value as $key ) {

                // h::log( $key );

                // create a new, named and numbered field based on field_COUNT -- empty value ##
                $key_field = $field.'__'.$count;
                fields::set( $key_field, '' );

                // Format each field value based on type ( int, string, array, WP_Post Object ) ##
                // each item is filtered as looped over -- q/render/format/GROUP/FIELD - ( $args, $fields ) ##
                // results are saved back to the self::$fields array in String format ##
                if ( self::field( $key_field, $key ) ) {

                    // format ran ok ##
                    // h::log( 'format ran ok.. so now we can update markup for field: '.$field );
                    markup::set_markup( $field, $count );

                }

                // iterate count ##
                $count ++ ;

            }

        }

        // remove placeholder from markup template
        self::$markup['template'] = markup::remove_placeholder( '%'.$field.'%', self::$markup['template'] );

        // delete sending field ##
        fields::remove( $field, 'Removed by format_array after working' );

        // checkout markup ##
        // h::log( self::$markup['template'] );

        // returning false will delete the original passed field ##
        return true;

    }



    public static function format_array_repeater( $value = null, $field = null )
    {

        // h::log( 'Formatting repeater array...' );
        // h::log( $value );

        // check how many items are in array and format ##
        $count = 0;

        // loop over array of arrays, work inner keys and values ## 
        foreach( $value as $r1 => $v1 ) {

            foreach( $v1 as $r2 => $v2 ) {

                // h::log( 'Working "'.$r2.'" Key value: "'.$v2.'"' );

                // create a new, named and numbered field based on field_COUNT__row_key ##
                // $key_field = $field.'__'.$count.'__'.$r2;
                fields::set( $field.'__'.$count.'__'.$r2, $v2 );

            }

            // format ran ok ##
            markup::set_markup( $field, $count );

            // iterate count ##
            $count ++ ;

        }

        return true;

    }




    /**
     * Format Object values ##
     * Currently, we only support Objects of type WP_Post - so validate with instance of ## 
     * @todo -- Extend this method a lot to deal with extra object types ##
     */
    public static function format_object( $value = null, $field = null )
    {

        // allow filtering early ##
        $value = \apply_filters( 'q/render/format/object/'.self::$args['group'].'/'.$field, $value );

        // WP Object format ##
        if ( $value instanceof \WP_Post ) {

            // pass to WP formatter ##
            $value = self::format_object_wp_post( $value, $field );

        // @todo - add more formats here ... ##

        } else {

			// log ##
			log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' => 'Object is not of type WP_Post, so emptied, $value returned empty and field removed from $fields'
			]);

            // this item needs to be removed from self::$fields
            fields::remove( $field, 'Removed by format_object because Object format is not allowed in $formats' );

            // we do not return the $value either ##
            return false; 

        }

        // delete sending field ##
        fields::remove( $field, 'Removed by format_object after working' );

        // return false will delete the passed field ##
        return true;

    }



    /**
     * Format WP_Post Objects
     */
    public static function format_object_wp_post( Object $value = null, $field = null ){

        // sanity ##
        if (
            is_null( $value )
            || is_null( $field )
        ) {

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' =>  'No value or field passed to format_wp_post_object.'
			]);

            return false;

		}
		
		// h::log( 'Formatting WP Post Object: '.$value->post_title );
		// h::log( $value ); // whole object ##

        // now, we need to create some new $fields based on each value in self::$wp_post_fields ##
        foreach( self::$wp_post_fields as $wp_post_field ) {
            
            // let's auto-assign some values - then hand create the rest ##
            if ( $value->$wp_post_field ) {

				// h::log( 'Field: "'.$wp_post_field.'" value already set.' );

				// filter magic post fields -- global ##
				$value->$wp_post_field = \apply_filters( 
					'q/render/format/wp_post/field/'.$wp_post_field, $value->$wp_post_field 
				);

				// h::log( 'Filter: q/render/format/wp_post/field/'.$wp_post_field );

				// filter magic post fields -- field specific ##
				$value->$wp_post_field = \apply_filters( 
					'q/render/format/wp_post/field/'.self::$args['group'].'/'.$wp_post_field, $value->$wp_post_field 
				);

				// set field ##
                fields::set( $field.'__'.$wp_post_field, $value->$wp_post_field );

            // hand crafted ##
            } else {

				// note that this field value was not found ##
				log::add([
					'key' => 'notice', 
					'field'	=> __FUNCTION__,
					'value' =>  'No value found for field: '.$wp_post_field
				]);

				// @todo - do we need to remove this field? ##

			}

		}

		// custom field value handlers ##
        foreach( self::$wp_post_fields_custom as $wp_post_field ) {

			// get categories ##
			$categories = \get_the_category( $value->ID );
			
			// h::log( 'Working: '.$wp_post_field );

			switch( $wp_post_field ) {

				// human readable date ##
				case 'human_date' :

					// h::log( self::$args['date_format'] );

					$string = \human_time_diff( 
						\get_the_date( 
							isset( self::$args['date_format'] ) ? self::$args['date_format'] : 'U', 
							$value->ID 
						), \current_time('timestamp') );
					
				break ;

				case 'permalink' :

					$string = \get_permalink( $value->ID );

				break ;

				case 'post_excerpt' :

					$string = $value->post_excerpt;

					// if is_search - highlight ##
					if ( \is_search() ) {

						$string = 
							ui\method::search_the_content([
								'string' 	=> \apply_filters( 'q/get/wp/post_content', $value->post_content ),
								'limit'		=> self::$args['length']
							]) ? 
							ui\method::search_the_content([
								'string' 	=> \strip_shortcodes(\apply_filters( 'q/get/wp/post_content', $value->post_content )),
								'limit'		=> self::$args['length']
							]) : 
							$value->post_excerpt ;

					}

				break ;

				case 'category_name' :

					$string = isset( $categories[0] ) ? $categories[0]->name : null ; // category missing ##

				break ;

				case 'category_permalink' :

					$string = isset( $categories[0] ) ? \get_category_link( $categories[0] ) : null ; // category missing ##

				break ;

				// images ###
				case 'src' :

					// h::log( 'src: '.$value->ID );

					// get post_thumbnail ID ##
					// $src_id = \get_post_thumbnail_id( $value->ID );
					$string = type::src( $value->ID, $field );
					// h::log( 'Final string: '.$string );

				break ;

			}

			// assign field and value ##
			fields::set( $field.'__'.$wp_post_field, $string );

		}

        // kick back ##
        return true;

    }




    /**
     * Get allowed fomats with filter ##
     * 
     */
    public static function get_allowed()
    {

        return \apply_filters( 'q/render/format/get_allowed', self::$format );

    }



}