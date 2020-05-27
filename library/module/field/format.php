<?php

namespace q\module\field;

// use q\core\core as core;
use q\core\helper as helper;
// use q\core\config as config;

use q\module\field as field;
use q\module\field\core as core;
use q\module\field\filter as filter;
use q\module\field\format as format;
use q\module\field\get as get;
use q\module\field\log as log;
use q\module\field\markup as markup;
use q\module\field\output as output;
use q\module\field\ui as ui;

class format extends field {

    
    /**
     * Work passed field data into rendering format
     */
    public static function fields(){

        // check we have fields to loop over ##
        if ( 
            ! self::$fields
            || ! is_array( self::$fields ) 
        ) {

            self::$log['error'][] = 'Error in $fields array';

            return false;

        }

        // filter $args now that we have fields data from ACF ##
        self::$args = filter::apply([ 
            'parameters'    => [ 'fields' => self::$fields, 'args' => self::$args ], // pass ( $fields, $args ) as single array ##
            'filter'        => 'q/field/before/args/'.self::$args['group'], // filter handle ##
            'return'        => self::$args
        ]); 

        // filter all fields before processing ##
        self::$fields = filter::apply([ 
            'parameters'    => [ 'fields' => self::$fields, 'args' => self::$args ], // pass ( $fields, $args ) as single array ##
            'filter'        => 'q/field/before/fields/'.self::$args['group'], // filter handle ##
            'return'        => self::$fields
        ]); 

        // self::$log['debug'] = self::$fields;

        // start loop ##
        foreach ( self::$fields as $field => $value ) {

            // self::$log['debug'] = 'Working field: '.$field .' With Value:';
            // self::$log['debug'] = $value;

            // check field has a value ##
            if ( 
                ! $value 
                || is_null( $value )
            ) {

                self::$log['notice'][] = 'Field: '.$field.' has no value, check for data issues.';

                continue;

            }

            // Callback methods on specified fields ##
            // Note - field includes a list of standard callbacks, which can be extended via the filter q/field/callbacks/get ##
            $value = core::callbacks( $field, $value );

            // helper::log( 'After callback -- field: '.$field .' With Value:' );
            // helper::log( $value );

            // Format each field value based on type ( int, string, array, WP_Post Object ) ##
            // each item is filtered as looped over -- q/field/format/GROUP/FIELD - ( $args, $fields ) ##
            // results are saved back to the self::$fields array in String format ##
            self::field( $field, $value );

        }

        // filter all fields ##
        self::$fields = filter::apply([ 
            'parameters'    => [ 'fields' => self::$fields, 'args' => self::$args ], // pass ( $fields, $args ) as single array ##
            'filter'        => 'q/field/after/fields/'.self::$args['group'], // filter handle ##
            'return'        => self::$fields
        ]); 

    }


   
    /**
     * Check allowed formats based on passed $value, format and return a string ready for markup  
     * 
     * @return      String
     */
    public static function field( String $field = null, $value = null ) {

        // sanity ##
        if ( is_null( $field ) ) {

            self::$log['error'][] = 'No field value passed to method.';

            return false;

        }

        // sanity ##
        if ( is_null( $value ) ) {

            self::$log['error'][] = 'No value passed to method.';

            return false;

        }

        // Check if there are any allowed formats ##
        // Also runs filters to add custom formats ##
        $formats = self::get_formats();

        if ( 
            ! $formats
            || ! \is_array( $formats ) 
        ) {

            self::$log['error'][] = 'No formats allowed in plugin or array corrupt';

            return false;

        }

        // Now check the format of $value - Array requires repeat check on each row ##
        $format = self::get_format( $value, $field );

        // now try to format value ##
        $return = self::apply_format( $value, $field, $format );

        // self::$fields should all be String values by now, ready for markup ##
        return $return;

    }


    /**
     * Allow text field to be filtered ##
     * 
     */
    public static function apply_format( $value = null, String $field = null, String $format = null )
    {

        // sanity ##
        if ( 
            is_null( $value )
            || is_null( $field )
            || is_null( $format )
        ) {

            self::$log['error'][] = 'Error in parameters passed to apply_format, $value returned empty and field removed from $fields';

            // this item needs to be removed from self::$fields
            self::remove_field( $field );

             // we do not return the $value either ##
            return false;

        }

        // helper::log( 'Checking Format for - Field: '.$field.' with method: '.$format );

        // we can now distribute the $value to the relevant format method ##
        if (
            ! method_exists( __CLASS__, $format )
            || ! is_callable( array( __CLASS__, $format ) )
        ){

            self::$log['error'][] = 'handler wrong - class: '.__CLASS__.' / method: '.$format;

            // this item needs to be removed from self::$fields
            self::remove_field( $field );

            // we do not return the $value either ##
            return false; 

        }

        // call class method and pass arguments ##
        $value = call_user_func_array (
            array( __CLASS__, $format )
            ,   array( $value, $field )
        );

        if ( ! $value ) {

            // helper::log( 'Handler method returned bad OR empty data for Field: '.$field );

            // this item needs to be removed from self::$fields
            // self::remove_field( $field, 'Removed by apply_format due to bad or empty data' );

            return false; // we do not return the $value either ##

        }

        // test returned data ##
        // helper::log( self::$fields );

        // fields are filtered and saved by each type handler, as new fields might be added or removed internally ##

        // kick back ##
        return true;

    }



    /**
     * Get format of $field $value from defined list of allowed formats ##
     * 
     */
    public static function get_format( $value = null, $field = null )
    {

        // sanity ##
        if ( 
            is_null( $value )
            || is_null( $field )
        ) {

            self::$log['error'][] = 'Error in parameters passed to check_format';

            return false;

        }

        // get formats ##
        $formats = self::get_formats();
        // helper::log( $formats );

        // tracker, if we find a match ##
        $tracker = false;

        // assign default in case we don't find a matching type ##
        // this is alterable via a filter ##
        $return = \apply_filters( 'q/field/format/default', 'format_text' ); 

        // helper::log( 'Default method is: '.$return );

        // loop over formats and search for a match ##
        foreach ( $formats as $format => $format_value ){

            // helper::log( 'Checking type: '.$format_value['type'] );

            if ( ! function_exists( $format_value['type'] ) ) {

                self::$log['error'][] = 'Function not found: '.$format_value['type'];

                continue;

            }

            // helper::log( 'function exists: '.$item['function'] );

            // boolean check ## is_TYPE === true
            if ( 
                TRUE === call_user_func_array( $format_value['type'], array( $value ) ) 
            ) {

                // log ##
                // helper::log( 'Field value: '.$field.' is Type: '.$format_value['type'].' Format with: '.$format_value['method'] );

                // update tracker ##
                $tracker = true;

                // field type assigned ##
                $return = $format_value['method'];

            }

        }

        // note use of default type if no match found ##
        if ( false === $tracker ) {

            self::$log['notice'][] = 'No valid value type found for field: '.$field.' so assigned: '.$return;

        }

        // final filter on field format type ##
        $return = \apply_filters( 'q/field/format/get/'.self::$args['group'].'/'.$field, $return );

        // kick back ##
        return $return;

    }



    /**
     * Format text - allow for external filtering ##
     * 
     */
    public static function format_text( $value = null, $field = null )
    {

        // helper::log( $value );

        return \apply_filters( 'q/field/format/text/'.self::$args['group'].'/'.$field, $value );

    }


    /**
     * Allow integer field to be filtered ##
     * 
     */
    public static function format_integer( $value = null, $field = null )
    {

        return \apply_filters( 'q/field/format/integer/'.self::$args['group'].'/'.$field, $value );

    }



    /**
     * Format Array values
     * These need to be looped over and each value passed back into the format() process
     * 
     * Array data "MUST" come from a repeater
     * which has one single %placeholder% and markup in a property with a name matching key to the field name
     * we need to update the template based on number of array items and defined markup with numbered values ##
     * 
     */
    public static function format_array( $value = null, $field = null )
    {

        // allow filtering early ##
        $value = \apply_filters( 'q/field/format/array/'.self::$args['group'].'/'.$field, $value );

        // helper::log( $value );

        // @todo - and array of arrays containing named indexes ( not WP_Post Objects ) needs to be be marked up as a block, like an Object ##
        // ---------
        // ---------

        // check how many items are in array and format ##
        $count = 0;

        // we need to loop over the array and check what each the value of each key using self::format()
        // Formats that are not registered in self::$formats will be removed ## 
        foreach( $value as $key ) {

            // helper::log( $key );

            // create a new, named and numbered field based on field_COUNT -- empty value ##
            $key_field = $field.'__'.$count;
            self::set_field( $key_field, '' );

            // Format each field value based on type ( int, string, array, WP_Post Object ) ##
            // each item is filtered as looped over -- q/field/format/GROUP/FIELD - ( $args, $fields ) ##
            // results are saved back to the self::$fields array in String format ##
            if ( self::field( $key_field, $key ) ) {

                // format ran ok ##
                // helper::log( 'format ran ok.. so now we can update markup for field: '.$field );
                markup::set_markup( $field, $count );

            }

            // iterate count ##
            $count ++ ;

        }

        // remove placeholder from markup template
        markup::remove_placeholder( '%'.$field.'%' );

        // delete sending field ##
        self::remove_field( $field, 'Removed by format_array after working' );

        // checkout markup ##
        // helper::log( self::$markup['template'] );

        // returning false will delete the original passed field ##
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
        $value = \apply_filters( 'q/field/format/object/before/'.self::$args['group'].'/'.$field, $value );

        // WP Object format ##
        if ( $value instanceof \WP_Post ) {

            // pass to WP formatter ##
            $value = self::format_object_wp_post( $value, $field );

        // @todo - ad more formats here ... ##

        } else {

            self::$log['notice'][] = 'Object is not of type WP_Post, so emptied, $value returned empty and field removed from $fields';

            // this item needs to be removed from self::$fields
            self::remove_field( $field, 'Removed by format_object because Object format is not allowed in $formats' );

            // we do not return the $value either ##
            return false; 

        }

        /*
        // now, we need to create some new $fields based on each value in self::$wp_post_fields ##
        foreach( self::$wp_post_fields as $wp_post_field ) {
            
            // let's auto-assign some values - then hand create the rest ##
            if ( $value->$wp_post_field ) {

                self::set_field( $field.'__'.$wp_post_field, $value->$wp_post_field );

            // hand created ##
            } else {

                // get categories ##
                $categories = \get_the_category( $value->ID );

                switch( $wp_post_field ) {

                    case 'permalink' :

                        $string = \get_permalink( $value->ID );

                    break ;

                    case 'category_name' :

                        $string = $categories[0]->name;

                    break ;

                    case 'img' :

                        // we need a valid handle here ## might need to think about this more later ##
                        if ( ! isset( self::$args['img']['handle'][$field] ) ) {

                            // helper::log( 'Img requires a matching img->handle->$field reference - defaulting to "medium"' );

                        }

                        // check and assign ##
                        $handle = 
                            isset( self::$args['img']['handle'][$field] ) ?
                            self::$args['img']['handle'][$field] :
                            \apply_filters( 'q/field/format/img/handle', 'medium' ); ;

                        // helper::log( 'Image handle: '.$handle );

                        // get image ##
                        $string = \get_the_post_thumbnail_url( $value->ID, $handle );

                    break ;

                }

                // assign field and value ##
                // self::$fields[$field.'__'.$wp_post_field] = $string;
                self::set_field( $field.'__'.$wp_post_field, $string );

            }

        }
        */

        // delete sending field ##
        self::remove_field( $field, 'Removed by format_object after working' );

        // return false will delete the passed field ##
        return true;

    }



    /**
     * Format
     */
    public static function format_object_wp_post( Object $value = null, $field = null ){

        // sanity ##
        if (
            is_null( $value )
            || is_null( $field )
        ) {

            self::$log['error'][] = 'No value or field passed to format_wp_post_object.';

            return false;

        }

        // now, we need to create some new $fields based on each value in self::$wp_post_fields ##
        foreach( self::$wp_post_fields as $wp_post_field ) {
            
            // let's auto-assign some values - then hand create the rest ##
            if ( $value->$wp_post_field ) {

                self::set_field( $field.'__'.$wp_post_field, $value->$wp_post_field );

            // hand created ##
            } else {

                // get categories ##
                $categories = \get_the_category( $value->ID );

                switch( $wp_post_field ) {

                    case 'permalink' :

                        $string = \get_permalink( $value->ID );

                    break ;

                    case 'category_name' :

                        $string = $categories[0]->name;

                    break ;

                    case 'img' :

                        // we need a valid handle here ## might need to think about this more later ##
                        if ( ! isset( self::$args['img']['handle'][$field] ) ) {

                            // helper::log( 'Img requires a matching img->handle->$field reference - defaulting to "medium"' );

                        }

                        // check and assign ##
                        $handle = 
                            isset( self::$args['img']['handle'][$field] ) ?
                            self::$args['img']['handle'][$field] :
                            \apply_filters( 'q/field/format/img/handle', 'medium' ); ;

                        // helper::log( 'Image handle: '.$handle );

                        // get image ##
                        $string = \get_the_post_thumbnail_url( $value->ID, $handle );

                    break ;

                }

                // assign field and value ##
                // self::$fields[$field.'__'.$wp_post_field] = $string;
                self::set_field( $field.'__'.$wp_post_field, $string );

            }

        }

        // kick back ##
        return true;

    }



    /**
     * Get allowed fomats with filter ##
     * 
     */
    public static function get_formats()
    {

        return \apply_filters( 'q/field/formats/get', self::$formats );

    }





    
    /**
     * Add $field from self:$fields array
     * 
     */
    public static function set_field( string $field = null, string $value = null, string $message = null ) {

        // sanity ##
        if ( 
            is_null( $field )
            || is_null( $value ) 
        ) {

            self::$log['error'][] = 'No field or value passed to method.';

            return false;

        }

        // add field to array ##
        // @todo - perhaps more validation required ##
        self::$fields[$field] = $value;

        // track removal ##
        self::$log['fields']['added'][$field] = 
            ! is_null( $message ) ? 
            $message : 
            log::backtrace() ;

        // positive ##
        return true;

    }



    /**
     * Remove $field from self:$fields array
     * 
     */
    public static function remove_field( string $field = null, string $message = null ) {

        // sanity ##
        if ( is_null( $field ) ) {

            self::$log['error'][] = 'No field value passed to method.';

            return false;

        }

        // remove from array ##
        unset( self::$fields[$field] );

        // track removal ##
        self::$log['fields']['removed'][$field] = 
            ! is_null( $message ) ? 
            $message : 
            log::backtrace() ;

        // positive ##
        return true;

    }


    
    

}