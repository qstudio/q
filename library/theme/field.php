<?php

namespace q\theme;

use q\core\core as core;
use q\core\helper as helper;

// define class ##
class field extends \Q {

    protected static

        // default args to merge with passed array ##
        $args = [
            'config'            => [
                'run'           => true, // don't run this item ##
                'debug'         => false, // don't debug this item ##
                'return'        => 'echo' // default to echo return string ##
            ],
        ],

        // frontend pre-processor callbacks ##
        $callbacks = [
            'get_posts'         => [ // standard WP get_posts()
                'class'         => 'global', // global scope to allow for namespacing ##
                'method'        => '\get_posts()',
                'args'          => [] // default - can be edited via global and specific filters ##
            ]
        ],

        // value formatters ##
        $formats = [
            'array'             => [ // Arrays could be collection of WP Post Objects OR text - so check ##
                'type'          => 'is_array',
                'method'        => 'format_array'
            ],
            'post_object'       => [
                'type'          => 'is_object',
                'method'        => 'format_object'
            ],
            'integer'           => [
                'type'          => 'is_int',
                'method'        => 'format_integer'
            ],
            'string'            => [
                'type'          => 'is_string',
                'method'        => 'format_text',
            ],
        ],

        // standard fields to add from wp_post objects
        $wp_post_fields = [
            'ID',
            'post_title',
            'post_content',
            'post_excerpt',

            // required additional lookup ##
            'permalink', 
            'category_name', 
            // 'category_permalink',
            
            // requires additional lookup and handle ##
            'img', 
        ],

        $output = null, // return string ##
        $fields = null, // field names and values ##
        $markup = null, // store local version of passed markup ##
        $log = null, // tracking array for feedback ##
        // $debug = null, // debugging option ##
        $acf_fields = null // fields grabbed by acf function ##

    ;


    /**
     * Render fields based on passed $args
     * 
     */
    public static function render( Array $args ){

        // // assign properties with initial filters ##
        // self::assign( $args );

        // validate passed args ##
        if ( ! self::validate( $args ) ) {

            self::log();

            return false;

        }

        // // Get all ACF fields for this post ##
        // if ( ! self::get_acf_fields() ) {

        //      return false;

        // }

        // get field names from passed $args ##
        if ( ! self::get_fields() ) {

            self::log();

            return false;

        }

        // // remove skipped fields, if defined ##
        // self::skip();

        // // if group specified, get only fields from this group ##
        // self::get_group();

        // check if feature is enabled ##
        // if ( ! self::is_enabled() ) {

        //      return false;

        // }    

        // helper::log( self::$fields );

        // Filter things before starting ## 
        // This allows us to convert values - for example from an int to an array of objects ##
        // self::filter([ 'property' => 'fields', 'filter' => 'q/field/before/fields/'.self::$args['group'] ]); // pass ( $fields, $args, $output ) ##

        // // filter fields ##
        // self::$fields = self::filter([ 
        //     'parameters'    => [ self::$fields, self::$args ], // pass ( $fields, $args ) as single array ##
        //     'filter'        => 'q/field/before/fields/'.self::$args['group'], // filter handle ##
        // ]); 

        // // filter $args now that we have fields data from ACF ##
        // self::$args = self::filter([ 
        //     'parameters'    => [ self::$fields, self::$args ], // pass ( $fields, $args ) as single array ##
        //     'filter'        => 'q/field/before/args/'.self::$args['group'], // filter handle ##
        // ]); 

        // Now we can loop over each field, running callbacks, formatting, removing placeholders in markup
        self::fields();

        // helper::log( self::$fields );

        // Apply Markup to each $field -- based on generic or specific rules passed in the $args ##
        // wrap in open, close markup at this point -- if defined ##
        // returns value to self::$output ##
        self::markup();

        // clean up -- removes %placeholders% and unused $fields ( we need to track each field and remove it from the list when added.. ) ##
        // self::clean();

        // Filter $output last thing ## 
        // This allows us to do any post formatting changes ##
        // self::filter([ 'property' => 'output', 'filter' => 'q/field/output/'.self::$args['group'] ]); // pass ( $fields, $args, $output ) ##
        // self::$output = self::filter([ 
        //     'parameters'    => [ self::$fields, self::$args, self::$output ], // pass ( $fields, $args ) as single array ##
        //     'filter'        => 'q/field/output/'.self::$args['group'], // filter handle ##
        // ]); 

        // optional logging to show removals and stats ##
        self::log();

        // return or echo ##
        return self::return();

    }


    protected static function validate( Array $args ) {

        // checks on required fields in $args array ##
        /*
        We needs:
        - fields // array ##
        - group
        - markup  // array ##
            - template
        */
        if (
            ! isset( $args )
            || ! is_array( $args )
            || ! isset( $args['fields'] )
            || ! is_array( $args['fields'] )
            || ! isset( $args['group'] )
            || ! isset( $args['markup'] )
            || ! is_array( $args['markup'] )
            || ! isset( $args['markup']['template'] )
        ){

            self::$log['error'][] = 'Missing required args in Group, so stopping here.. ';

            return false;

        }

        // helper::log( $args['config'] );

        // assign properties with initial filters ##
        $args = self::assign( $args );

        // helper::log( $args['config'] );

        // check if module asked to run $args['config']['run']
        if ( 
            // isset( $args['config']['run'] )
            // && 
            false === $args['config']['run']
        ){

            self::$log['notice'][] = 'config->run defined as false for Group: '.$args['group'].', so stopping here.. ';

            return false;

        }

        // helper::log( 'Passed second validation' );

        // // check if feature is enabled ##
        // if ( ! self::is_enabled() ) {

        //     return false;

        // }    

        // helper::log( 'Passed third validation' );

        // ok - should be good ##
        return true;

    }




    /**
     * Assign class properties with initial filters, merging in passed $args from calling method
     */
    protected static function assign( Array $args = null ) {

        // apply global filter to $args - specific calls should be controlled by parameters included directly ##
        self::$args = self::filter([
             'filter'        => 'q/field/args',
             'parameters'    => self::$args,
             'return'        => self::$args
        ]);

        // grab all passed args and merge with defaults ##
        self::$args = self::parse_args( $args, self::$args );
        
        // test ##
        // helper::log( self::$args );

        // grab args->markup ##
        self::$markup = $args['markup'];

        // return args for validation ##
        return self::$args;

    }


    /**
     * Recursive pass args 
     * 
     * @link    https://mekshq.com/recursive-wp-parse-args-wordpress-function/
     */
    protected static function parse_args( &$a, $b ) {

        $a = (array) $a;
        $b = (array) $b;
        $result = $b;
        
        foreach ( $a as $k => &$v ) {
            if ( is_array( $v ) && isset( $result[ $k ] ) ) {
                $result[ $k ] = self::parse_args( $v, $result[ $k ] );
            } else {
                $result[ $k ] = $v;
            }
        }

        return $result;

    }
     


    
    /**
     * Work passed field data into rendering format
     */
    protected static function fields(){

        // check we have fields to loop over ##
        if ( 
            ! self::$fields
            || ! is_array( self::$fields ) 
        ) {

            self::$log['error'][] = 'Error in $fields array';

            return false;

        }

        // filter $args now that we have fields data from ACF ##
        self::$args = self::filter([ 
            'parameters'    => [ 'fields' => self::$fields, 'args' => self::$args ], // pass ( $fields, $args ) as single array ##
            'filter'        => 'q/field/before/args/'.self::$args['group'], // filter handle ##
            'return'        => self::$args
        ]); 

        // filter all fields before processing ##
        self::$fields = self::filter([ 
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
            $value = self::callbacks( $field, $value );

            // helper::log( 'After callback -- field: '.$field .' With Value:' );
            // helper::log( $value );

            // Format each field value based on type ( int, string, array, WP_Post Object ) ##
            // each item is filtered as looped over -- q/field/format/GROUP/FIELD - ( $args, $fields ) ##
            // results are saved back to the self::$fields array in String format ##
            self::format( $field, $value );

        }

        // filter all fields ##
        self::$fields = self::filter([ 
            'parameters'    => [ 'fields' => self::$fields, 'args' => self::$args ], // pass ( $fields, $args ) as single array ##
            'filter'        => 'q/field/after/fields/'.self::$args['group'], // filter handle ##
            'return'        => self::$fields
        ]); 

    }




    /**
     * Get Defined Fields
     */
    protected static function get_fields(){

        // sanity ##
        if ( 
            is_null( self::$args ) 
            || ! is_array( self::$args )
            || ! isset( self::$args['fields'] )
        ) {

            self::$log['error'][] = 'Error in passed parameter "fields"';

            return false;

        }

        // Get all ACF fields for this post ##
        if ( ! self::get_acf_fields() ) {

            return false;

       }

        // helper::log( $args[ 'fields' ] );

        // get field names from passes $args ##
        $array = array_column( self::$args[ 'fields' ], 'name' );

        // sanity ##
        if ( 
            ! $array 
            || ! is_array( $array )
        ) {

            self::$log['error'][] = 'Error extracting field list from passed data';

            return false;

        }

        // helper::log( $array );

        // assign class property ##
        self::$fields = $array;

        // check if feature is enabled ##
        if ( ! self::is_enabled() ) {

            return false;

       }    

        // remove skipped fields, if defined ##
        self::skip();

        // if group specified, get only fields from this group ##
        self::get_group();

        // we should do a check if $fields is empty after all the filtering ##
        if ( 
            0 == count( self::$fields ) 
        ) {

            self::$log['notice'][] = 'Fields array is empty, so nothing to process...';

            return false;

        }

        // positive ##
        return true;

    }



    /**
     * Get ACF Fields
     */
    protected static function get_acf_fields(){

        // option to pass post ID to function ##
        // this can be passed as an arg ##
        $post = 
            isset( $args['post'] ) ? 
            $args['post'] : 
            \get_the_ID() ;

        // get fields ##
        $array = \get_fields( $post );

        // sanity ##
        if ( 
            ! $array 
            || ! is_array( $array )
        ) {

            self::$log['notice'][] = 'Post: '.$post.' has no ACF field data or corrupt data returned';

            return false;

        }

        // helper::log( $acf_fields );

        return self::$acf_fields = $array;

    }



    protected static function skip(){

        // sanity ##
        if ( 
            ! self::$args 
            || ! is_array( self::$args )
        ) {

            self::$log['error'][] = 'Error in passed $args';

            return false;

        }

        if ( 
            isset( self::$args['skip'] ) 
            && is_array( self::$args['skip'] )
        ) {

            // helper::log( self::$args['skip'] );
            self::$fields = array_diff( self::$fields, self::$args['skip'] );

        }

    }



    protected static function get_group(){

        // sanity ##
        if ( 
            ! self::$args 
            || ! is_array( self::$args )
            || ! self::$fields
            || ! is_array( self::$fields )
        ) {

            self::$log['error'][] = 'Error in passed $args or $fields';

            return false;

        }

        if ( 
            isset( self::$args['group'] )
        ) {

            // helper::log( 'Removing fields from other groups... BEFORE: '.count( self::$fields ) );
            // helper::log( self::$fields );

            self::$fields = array_intersect_key( self::$acf_fields, array_flip( self::$fields ) );

            // helper::log( 'Removing fields from other groups... AFTER: '.count( self::$fields ) );

        }

        // kick back ##
        return true;

    }



    protected static function is_enabled()
    {

        // sanity ##
        if ( 
            ! self::$args 
            || ! is_array( self::$args )
        ) {

            self::$log['error'][] = 'Error in passed self::$args';

            return false;

        }

        // check for enabled flag - if none, return true ##
        if ( 
            ! isset( self::$fields[self::$args['enable']] ) 
        ) {

            self::$log['notice'][] = 'No enable check defined in Group: '.self::$args['group'];

            return true;

        }

        // helper::log( 'We are looking for field: '.self::$args['enable'] );

        // kick back ##
        if ( 
            isset( self::$fields[self::$args['enable']] )
            && 1 == self::$fields[self::$args['enable']] 
        ) {

            self::$log['notice'][] = 'Field Group: '.self::$args['group'].' Enabled, continue';

            // helper::log( self::$args['enable'] .' == 1' );

            return true;

        }

        self::$log['notice'][] = 'Field Group: '.self::$args['group'].' NOT Enabled, stopping.';

        // helper::log( self::$args['enable'] .' != 1' );

        // negative ##
        return false;

    }


    /**
     * Filter items at set points to allow for manipulation
     * 
     * 
     */
    protected static function filter( Array $args = null ){

        // sanity ##
        if ( 
            ! $args 
            || ! is_array( $args )
            || ! isset( $args['filter'] )
            || ! isset( $args['parameters'] )
            || ! is_array( $args['parameters'] )
            || ! isset( $args['return'] )
        ) {

            self::$log['error'][] = 'Error in passed self::$args';

            return 'Error';

        }

        if( \has_filter( $args['filter'] ) ) {

            // helper::log( 'Running Filter: '.$args['filter'] );

            // run filter ##
            $return = \apply_filters( $args['filter'], $args['parameters'] );

            // check return ##
            // helper::log( $return );

        } else {

            // helper::log( 'No matching filter found: '.$args['filter'] );
            $return = $args['return']; 

        }

        // return true ##
        return $return;

    }


    /**
     * Check allowed formats based on passed $value, format and return a string ready for markup  
     * 
     * @return      String
     */
    protected static function format( String $field = null, $value = null ) {

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
            if ( self::format( $key_field, $key ) ) {

                // format ran ok ##
                // helper::log( 'format ran ok.. so now we can update markup for field: '.$field );
                self::set_markup( $field, $count );

            }

            // iterate count ##
            $count ++ ;

        }

        // remove placeholder from markup template
        self::remove_placeholder( '%'.$field.'%' );

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

        if ( ! $value instanceof \WP_Post ) {

            self::$log['notice'][] = 'Object is not of type WP_Post, so emptied, $value returned empty and field removed from $fields';

            // this item needs to be removed from self::$fields
            self::remove_field( $field, 'Removed by format_object because Object format is not allowed in $formats' );

            // we do not return the $value either ##
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

        // delete sending field ##
        self::remove_field( $field, 'Removed by format_object after working' );

        // return false will delete the passed field ##
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
     * Run defined callbacks on specific field ##
     * Retur alters the static class property $args
     * 
     */
    protected static function callbacks( String $field = null, $value = null ){

        // sanity ##
        if ( is_null( $field ) ) {

            self::$log['error'][] = 'No field value passed to method.';

            return $value;

        }

        // sanity ##
        if ( is_null( $value ) ) {

            self::$log['error'][] = 'No value passed to method.';

            return $value;

        }

        // Check if there are any allowed callbacks ##
        // Also runs filters to add custom callbacks ##
        $callbacks = self::get_callbacks();

        if ( 
            ! $callbacks
            || ! \is_array( $callbacks ) 
        ) {

            self::$log['error'][] = 'No callbacks allowed in plugin';

            return $value;

        }

        // Check if we have any callbacks to run ## 
        if ( 
            ! isset ( self::$args['callback'] ) 
        ) {

            // helper::log( 'No callbacks registered for field group' );

            return $value;

        } 

        // Check if callbacks are formatted as an array ## 
        if ( 
            ! is_array ( self::$args['callback'] ) 
        ) {

            self::$log['error'][] = 'Error in callbacks format - not Array';

            return $value;

        } 

        // check if this field has any callbacks ##
        if ( 
            ! isset ( self::$args['callback'][$field] ) 
        ) {

            // helper::log( 'No callbacks registered for field: '. $field );

            return $value;

        } 

        // assign method to variable ##
        $method = self::$args['callback'][$field]['method'];
        $field_value = self::$fields[$field];

        // Check we have a real field value to work with ##
        if ( ! $field_value ) {

            self::$log['notice'][] = 'No field value found, stopping callback';

            return $value;

        }

        // Clean up args, with actual passed value ##
        self::$args['callback'][$field]['args'] = str_replace( 
            '%value%', 
            $field_value, 
            self::$args['callback'][$field]['args'] 
        );

        // assign args ##
        $args = self::$args['callback'][$field]['args'];

        // helper::log( $method );
        // helper::log( self::$args );

        // check if field callback is listed in the allowed array of callbacks ##
        if ( ! array_key_exists( $method, $callbacks ) ) {

            self::$log['notice'][] = 'Cannot find callback: '.$method;

            return $value;

        }

        // Check if the method is usable ##
        if (
            // ! method_exists( $args->view, $args->method )
            // || 
            ! is_callable( $method )
        ){

            self::$log['notice'][] = 'Method is not callable: '.$method;

            return $value;

        }

        // checks over ##
        // helper::log( 'Field: '.$field.' has a valid callback: '.$method);

        // filter callback specific to this field ##
        $callbacks = \apply_filters( 
            'q/field/callbacks/'.$method.'/'.$field, 
            $callbacks 
        );

        // run callback using original value of field ##
        $data = call_user_func (
            $method,
            $args
        );

        // Opps ##
        if ( ! $data ) {

            self::$log['notice'][] = 'Method returned bad data..';

            return $value;

        }

        // check ##
        // helper::log( $data );

        // now add new data to class property $fields ##
        self::$fields[$field] = $data;

        // done ##
        return $data;

    }



    /**
     * Run defined callbacks on fields ##
     * 
     */
    public static function get_callbacks()
    {

        return \apply_filters( 'q/field/callbacks/get', self::$callbacks );

    }



    protected static function return() {

        // filter output ##
        self::$output = self::filter([ 
            'parameters'    => [ // pass ( $fields, $args, $output ) as single array ##
                'fields'    => self::$fields, 
                'args'      => self::$args, 
                'output'    => self::$output ], 
            'filter'        => 'q/field/output/'.self::$args['group'], // filter handle ##
            'return'        => self::$output
        ]); 

        // helper::log( self::$output );

        // either return or echo ##
        if ( 'echo' === self::$args['config']['return'] ) {

            echo self::$output;

            return true;

        } else {

            return self::$output;

        }

    }

    

    /**
     * Apply Markup changes to passed template
     * find all placeholders in self::$markup['template'] and replace with matching values in self::$fields
     * 
     */
    protected static function markup(){

        // sanity checks @todo ##

        // check we have what we need ##
        // helper::log( self::$fields );
        // helper::log( self::$markup['template'] );

        // new string to hold output ## 
        $string = self::$markup['template'];

        // loop over each field, replacing placeholders with values ##
        foreach( self::$fields as $key => $value ) {

            // we only want string values here -- so check and remove, as required ##
            if ( ! \is_string( $value ) ) {

                self::$log['notice'][] = 'The value of: '.$key.' is not a string';

                unset( self::$fields[$key] );

                continue;

            }

            // helper::log( 'working key: '.$key.' with value: '.$value );

            // template replacement ##
            $string = str_replace( '%'.$key.'%', $value, $string );

        }

        // helper::log( $string );

        // check for any left over placeholders - remove them ##
        if ( 
            $placeholders = self::get_placeholders( $string ) 
        ) {

            self::$log['notice'][] = count( $placeholders ) .' placeholders found in formatted string - these will be removed';

            // helper::log( $placeholders );

            // remove any leftover placeholders in string ##
            foreach( $placeholders as $key => $value ) {
            
                self::remove_placeholder( $value );
            
            }

        }

        // filter ##
        // $string = \apply_filters( 'q/field/markup/'.self::$args['group'], $string );
        $string = self::filter([ 
            'parameters'    => [ 'string' => $string ], // pass ( $string ) as single array ##
            'filter'        => 'q/field/markup/'.self::$args['group'], // filter handle ##
            'return'        => $string
        ]); 

        // check ##
        // helper::log( $string );

        // apply to class property ##
        self::$output = $string;

        // self::$output = self::filter([ 
        //     'parameters'    => [ self::$fields, self::$args, self::$output ], // pass ( $fields, $args ) as single array ##
        //     'filter'        => 'q/field/output/'.self::$args['group'], // filter handle ##
        // ]); 

        // return ##
        return true;

    }



    /**
     * Update Markup based for passed field ##
     * 
     */
    protected static function set_markup( string $field = null, $count = null ){

        // sanity ##
        if ( 
            is_null( $field )
            || is_null( $count )
        ) {

            self::$log['error'][] = 'No field value or count iterator passed to method.';

            return false;

        }

        // check ##
        // helper::log( 'Update template markup for field: '.$field.' @ count: '.$count );

        // look for required markup ##
        if ( ! isset( self::$markup[$field] ) ) {

            self::$log['notice'][] = 'Field: '.$field.' does not have required markup defined in $args -- markup => '.$field;

            // bale if not found ##
            return false;

        }

        // get markup ##
        // $markup = self::$markup[$field];

        // helper::log( $markup );
        /*
        <div class="col-12">
            <h3>
                <a href="%permalink%">
                    %post_title%
                </a>
            </h3>
            <span class="badge badge-pill badge-primary">
                %category_name%
            </span>
        </div>
        */

        // get target placeholder ##
        $placeholder = '%'.$field.'%';
        if ( 
            ! self::get_placeholder( $placeholder )
        ) {

            self::$log['notice'][] = 'Placeholder: '.$placeholder.' is not in the passed markup template';

            return false;

        }

        // so, we have the repeater markup to copy, placeholder in template to locate new markup ... 
        // && we need to find all placeholders in markup and append field__ID__PLACEHOLDER

        // get all placeholders from markup->$field ##
        if ( 
            ! $placeholders = self::get_placeholders( self::$markup[$field] ) 
        ) {

            self::$log['notice'][] = 'No placeholders found in passed string';

            return false;

        }

        // test ##
        // helper::log( $placeholders );

        // iterate over %placeholders% adding prefix ##
        $new_placeholders = [];
        foreach( $placeholders as $key => $value ) {

            // helper::log( 'Working placeholder: '.$value );

            $new_placeholders[] = '%'.$field.'__'.$count.'__'.str_replace( '%', '', $value ).'%';

        } 

        // testnew placeholders ##
        // helper::log( $new_placeholders );

        // generate new markup from template with new_placeholders ##
        $new_markup = str_replace( $placeholders, $new_placeholders, self::$markup[$field] );

        // helper::log( $new_markup );

        // use strpos to get location of %placeholder ##
        $position = strpos( self::$markup['template'], $placeholder );
        // helper::log( 'Position: '.$position );

        // add new markup to $template as defined position - don't replace %placeholder% yet... ##
        $new_template = substr_replace( self::$markup['template'], $new_markup, $position, 0 );

        // test ##
        // helper::log( $new_template );

        // push back to store markup ##
        self::$markup['template'] = $new_template;

        // kick back ##
        return true;

    }



    
    /**
     * Add $field from self:$fields array
     * 
     */
    protected static function set_field( string $field = null, string $value = null, string $message = null ) {

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
            self::backtrace() ;

        // positive ##
        return true;

    }



    /**
     * Remove $field from self:$fields array
     * 
     */
    protected static function remove_field( string $field = null, string $message = null ) {

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
            self::backtrace() ;

        // positive ##
        return true;

    }



    /**
     * Get all placeholders from passed string value 
     *  
     */
    protected static function get_placeholders( string $string = null ) {
        
        // @todo - sanity ##

        if ( ! preg_match_all('~\%(\w+)\%~', $string, $matches ) ) {

            // helper::log( 'No placeholders found in passed string' );

            return false;

        }

        // test ##
        // helper::log( $matches[0] );

        // kick back placeholder array ##
        return $matches[0];

    }


    /**
     * Check if single placeholder exists 
     * @todo - work on passed params 
     *  
     */
    protected static function get_placeholder( string $placeholder = null, $template = 'template' ) {
        
        if ( ! substr_count( self::$markup[$template], $placeholder ) ) {

            return false;

        }

        // good ##
        return true;

    }


    /**
     * Remove %placeholder% from self:$args['markup'] array
     * 
     */
    protected static function remove_placeholder( string $string = null, string $message = null ) {

        // sanity ##
        if ( is_null( $string ) ) {

            self::$log['error'][] = 'No string value passed to method.';

            return false;

        }

        // check if placeholder is correctly formatted --> %STRING% ##
        $needle = '%';
        if (
            $needle != $string[0] // returns first character ## 
            || 
            $needle != substr( $string, -1 ) // returns last character ##
        ) {

            self::$log['notice'][] = 'Placeholder is not correctly formatted - missing % at start or end of passed string.';

            return false;

        }

        // remove from args ##
        // @todo - replace from all values keys in markup ##
        self::$markup['template'] = str_replace( 
            $string, 
            '', // nada ##
            self::$markup['template']
        );

        // track removal ##
        self::$log['placeholder']['removed'][$string] = 
            ! is_null( $message ) ? 
            $message : 
            self::backtrace() ;

        // positive ##
        return true;

    }


    /**
     * Logging function
     * 
     */
    protected static function log( Array $args = null ){

        if (
            ! isset( self::$args['config']['debug'] )
            || false === self::$args['config']['debug']
        ) {

            helper::log( 'Debugging is turned off for Field Group: '.$args['group'] );

            return false;

        }   

        // option to debug only specific fields ##
        $return = 
            (
                isset( $args['field'] )
                && isset( self::$log[ $args['field'] ] ) 
            ) ?
            self::$log[ $args['field'] ] :
            self::$log ;

        // log to log ##
        helper::log( $return );

    }


    protected static function backtrace(){

        return \debug_backtrace()[1]['function'];

    }



}