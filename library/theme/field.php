<?php

namespace q\theme;

use q\core\core as core;
use q\core\helper as helper;

// define class ##
class field extends \Q {

    protected static

        $output = null,
        $args = null,
        $fields = null,
        $markup = null, // store local version of passed markup ##
        $log = null, // tracking array for feedback ##
        $acf_fields = null, // fields grabbed by acf function ##
        // $markup = null, // $markup passed from calling method ##

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
        ]

    ;


    /**
     * Get fields
     * 
     * Accepts: 
     * 
     * - Template with placeholders 
     * - groups of fields with markup rules
     * - skip [array] - fields to jump over ##
     * - handles repeater fields, WP Post_objects and can be extended with filters ##
     * - filters output before formatting q/field/before/GROUP ( $fields, $args ) ##
     * - filters output after formatting q/field/after/GROUP ( $fields, $args ) ##
     * - filters each field - q/field/GROUP/FIELD/ ( $value, $fields, $args ) ##
     * 
     */
    public static function get( Array $args = array() ){

        // @todo -- validate passed args ##
        // self::validate();

        // assign properties ##
        self::assign( $args );

        // Get all ACF fields for this post ##
        if ( ! self::get_acf_fields() ) {

             return false;

        }

        // get field names from passed $args ##
        if ( ! self::get_fields() ) {

            return false;

        }

        // remove skipped fields, if defined ##
        self::skip();

        // if group specified, get only fields from this group ##
        self::get_group();

        // check if feature is enabled ##
        if ( ! self::is_enabled() ) {

            return false;

        }    

        // Filter $fields before starting ## 
        // This allows us to convert values - for example from an int to an array of objects ##
        self::filter([ 'property' => 'fields', 'filter' => 'q/field/before/fields/'.self::$args['group'] ]); // pass ( $fields, $args, $output ) ##
        self::filter([ 'property' => 'args', 'filter' => 'q/field/before/args/'.self::$args['group'] ]); // pass ( $fields, $args, $output ) ##

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
        self::filter([ 'property' => 'output', 'filter' => 'q/field/output/'.self::$args['group'] ]); // pass ( $fields, $args, $output ) ##

        // optional logging to show removals and stats ##
        // self::log();

        // Return data to calling method ##
        return self::$output;

    }



    protected static function assign( Array $args = null ) {

        self::$args = $args;
        self::$markup = $args['markup'];

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

            helper::log( 'Error in $fields array' );

            return false;

        }

        // // helper::log( self::$fields );

        // start loop ##
        foreach ( self::$fields as $field => $value ) {

            // helper::log( 'Working field: '.$field .' With Value:' );
            // helper::log( $value );

            // check field has a value ##
            if ( 
                ! $value 
                || is_null( $value )
            ) {

                helper::log( 'Field: '.$field.' has no value, check for data issues.' );

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

    }



    /**
     * Get ACF Fields
     */
    protected static function get_acf_fields(){

        // @todo -- option to pass post ID to function ##

        // get fields ##
        $array = \get_fields();

        // sanity ##
        if ( 
            ! $array 
            || ! is_array( $array )
        ) {

            helper::log( 'This post has no ACF field data or corrupt data returned' );

            return false;

        }

        // helper::log( $acf_fields );

        return self::$acf_fields = $array;

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

            helper::log( 'Error in passed parameter "fields"' );

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

            helper::log( 'Error extracting field list from passed data' );

            return false;

        }

        // helper::log( $array );

        // return and assign class property ##
        return self::$fields = $array;

    }


    protected static function skip(){

        // sanity ##
        if ( 
            ! self::$args 
            || ! is_array( self::$args )
        ) {

            helper::log( 'Error in passed $args' );

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
        ) {

            helper::log( 'Error in passed self::$args' );

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

            helper::log( 'Error in passed self::$args' );

            return false;

        }

        // check for enabled flag - if none, return true ##
        if ( ! isset( self::$args['enable'] ) ) {

            helper::log( 'No enable check defined in this group.' );

            return true;

        }

        // helper::log( 'We are looking for field: '.self::$args['enable'] );

        // kick back ##
        if ( 1 == self::$fields[self::$args['enable']] ) {

            // helper::log( self::$args['enable'] .' == 1' );

            return true;

        }

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
        ) {

            helper::log( 'Error in passed self::$args' );

            return 'Error';

        }

        // assign return in case of missing filter ##
        switch ( $args['property'] ) {

            case 'args' :

                 $return = self::$args;

            break ;

            case 'output' :
    
                $return = self::$output;

            break ;

            case 'fields' :
            default :

                $return = self::$fields;

            break ;

        }

        if( \has_filter( $args['filter'] ) ) {

            // helper::log( 'Running Filter: '.$args['filter'] );

            // run filter ##
            $return = \apply_filters( $args['filter'], self::$fields, self::$args, self::$output );

            // check return ##
            // helper::log( $return );

        }

        // assign value back to selected property ##
        switch ( $args['property'] ) {

            case 'args' :

                self::$args = $return;

            break ;

            case 'output' :

                self::$output = $return;

            break ;

            case 'fields' :
            default :

                self::$fields = $return;

            break ;

        }

        // return true ##
        return true;

    }


    /**
     * Check allowed formats based on passed $value, format and return a string ready for markup  
     * 
     * @return      String
     */
    protected static function format( String $field = null, $value = null ) {

        // sanity ##
        if ( is_null( $field ) ) {

            helper::log( 'No field value passed to method.' );

            return false;

        }

        // sanity ##
        if ( is_null( $value ) ) {

            helper::log( 'No value passed to method.' );

            return false;

        }

        // Check if there are any allowed formats ##
        // Also runs filters to add custom formats ##
        $formats = self::get_formats();

        if ( 
            ! $formats
            || ! \is_array( $formats ) 
        ) {

            helper::log( 'No formats allowed in plugin' );

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

            helper::log( 'Error in parameters passed to apply_format, $value returned empty and field removed from $fields' );

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

            helper::log( 'handler wrong - class: '.__CLASS__.' / method: '.$format );

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

            helper::log( 'Error in parameters passed to check_format' );

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

                helper::log( 'Function not found: '.$format_value['type'] );

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

            helper::log( 'No valid value type found for field: '.$field.' so assigned: '.$return );

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
                helper::log( 'format ran ok.. so now we can update markup for field: '.$field );
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

            helper::log( 'Object is not of type WP_Post, so emptied, $value returned empty and field removed from $fields' );

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

            helper::log( 'No field value passed to method.' );

            return $value;

        }

        // sanity ##
        if ( is_null( $value ) ) {

            helper::log( 'No value passed to method.' );

            return $value;

        }

        // Check if there are any allowed callbacks ##
        // Also runs filters to add custom callbacks ##
        $callbacks = self::get_callbacks();

        if ( 
            ! $callbacks
            || ! \is_array( $callbacks ) 
        ) {

            helper::log( 'No callbacks allowed in plugin' );

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

            helper::log( 'Error in callbacks format - not Array' );

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

            helper::log( 'No field value found, stopping callback' );

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

            helper::log( 'Cannot find callback: '.$method );

            return $value;

        }

        // Check if the method is usable ##
        if (
            // ! method_exists( $args->view, $args->method )
            // || 
            ! is_callable( $method )
        ){

            helper::log( 'Method is not callable: '.$method );

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

            helper::log( 'Method returned bad data..' );

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

                helper::log( 'The value of: '.$key.' is not a string' );

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

            helper::log( count( $placeholders ) .' placeholders found in formatted string - these will be removed' );

            // helper::log( $placeholders );

            // remove any leftover placeholders in string ##
            foreach( $placeholders as $key => $value ) {
            
                self::remove_placeholder( $value );
            
            }

        }

        // filter ##
        $string = \apply_filters( 'q/field/markup/'.self::$args['group'], $string );

        // check ##
        // helper::log( $string );

        // apply to class property ##
        self::$output = $string;

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

            helper::log( 'No field value or count iterator passed to method.' );

            return false;

        }

        // check ##
        // helper::log( 'Update template markup for field: '.$field.' @ count: '.$count );

        // look for required markup ##
        if ( ! isset( self::$markup[$field] ) ) {

            helper::log( 'Field: '.$field.' does not have required markup defined in $args -- markup => '.$field );

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

            helper::log( 'Placeholder: '.$placeholder.' is not in the passed markup template' );

            return false;

        }

        // so, we have the repeater markup to copy, placeholder in template to locate new markup ... 
        // && we need to find all placeholders in markup and append field__ID__PLACEHOLDER

        // get all placeholders from markup->$field ##
        if ( 
            ! $placeholders = self::get_placeholders( self::$markup[$field] ) 
        ) {

            helper::log( 'No placeholders found in passed string' );

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

            helper::log( 'No field or value passed to method.' );

            return false;

        }

        // add field to array ##
        // @todo - perhaps more validation required ##
        self::$fields[$field] = $value;

        // track removal ##
        self::$log['fields']['added'][$field] = 
            ! is_null( $message ) ? 
            $message : 
            'Added by '.\debug_backtrace()[1]['function'] ;

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

            helper::log( 'No field value passed to method.' );

            return false;

        }

        // remove from array ##
        unset( self::$fields[$field] );

        // track removal ##
        self::$log['fields']['removed'][$field] = 
            ! is_null( $message ) ? 
            $message : 
            'Removed by '.\debug_backtrace()[1]['function'] ;

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

            helper::log( 'No string value passed to method.' );

            return false;

        }

        // check if placeholder is correctly formatted --> %STRING% ##
        $needle = '%';
        if (
            $needle != $string[0] // returns first character ## 
            || 
            $needle != substr( $string, -1 ) // returns last character ##
        ) {

            helper::log( 'Placeholder is not correctly formatted - missing % at start or end of passed string.' );

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
            'Removed by '.\debug_backtrace()[1]['function'] ;

        // positive ##
        return true;

    }



    protected static function log(){

        helper::log( self::$log );

    }



}