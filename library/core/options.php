<?php

namespace q\core;

use q\core\core as core;
use q\core\helper as helper;
// use q\core\wordpress as wordpress;

// load it up ##
\q\core\options::run();

class options extends \Q {

    // store db query ##
    public static $query = false;

    /**
    * Class Constructor
    * 
    * @since       1.0
    * @return      void
    */
    public static function run()
    {

        // add acf options page ##
        self::acf_add_options_page();
        
        // add fields ##
        \add_action( 'acf/init', array( get_class(), 'add_fields' ), 1 );

        // @todo - add caching to get options call, which is bust when the options page is visited -- is this really more effecient?? ##

        // example how to inject extra options in libraries select API ##
        // \add_filter( 'acf/load_field/name=q_option_library', [ get_class(), 'filter_acf_library' ], 10, 1 );

        // add link to view library from
        \add_filter( 'acf/load_field/name=q_option_library', [ get_class(), 'filter_acf_library' ], 9, 1 );
        
        // get stored options, early ##
        // \add_action( 'plugins_loaded', [ get_class(), 'get' ], 1 );

        // set debug from Q settings page ---- very late ##
        \add_action( 'plugins_loaded', [ get_class(), 'debug' ], 10 );

    }



    /**
     * API to add fields to Q settings
     * 
     * @since 2.3.0
     */
    public static function api( Array $args = null )
    {

        // @todo ... but should be a wrapper to: 
        // \add_filter( 'acf/load_field/name=q_option_library', [ get_class(), 'filter_acf_library' ], 1000000, 1 );

    }



    /**
     * Add view to link assets from backend
     * 
     * @since 2.3.0
     */
    public static function filter_acf_library( $field )
    {

        // helper::log( $field['choices'] );

        // empty array ##
        $array = [];

        foreach( $field['choices'] as $key => $value ) {

            // helper::log( 'working: '.$key );

            // add to array ##
            $array[$key] = $value; 

            // CSS or JS
            $type = explode( "_" , $key );

            // if no type - skip ##
            if ( 
                ! is_array( $type ) 
                || 2 > count( $type )
            ) {

                // helper::log( 'Skipping: '.$key );

                continue;

            }

            $type_dir = ( 'css' == $type[0] ) ? 'css' : 'javascript' ;
            $type_ext = ( 'css' == $type[0] ) ? 'css' : 'js' ;

            // give it a handle ##
            $handle = 'q-'.$key;

            // look for minified library ##
            $file = helper::get( "theme/".$type_dir."/".$type[1].".min.".$type_ext, 'return' );

            // if not debugging, check if we can find a non-min version ##
            if ( 
                ( ! $file )
                ||
                (
                    self::$debug 
                    && helper::get( "theme/".$type_dir."/".$type[1].".".$type_ext, 'return' )
                )
            ) {

                $file = helper::get( "theme/".$type_dir."/".$type[1].".".$type_ext, 'return' ) ;

            }

            // if no type - skip ##
            if ( ! $file ) {

                // // helper::log( 'Skipping: '.$handle.' - File missing...' );

                continue;

            }

            // helper::log( 'Adding library: '.$handle.' with file: '.$file.' as type: '.$type_ext );

            // Add link to view ##
            $array[$key] = $value.' ( <a href="'.$file.'" target="_blank">view</a> )';

        }

        // replace array ##
        $field['choices'] = $array;

        // kick it all back ##
        return $field;

    }



    public static function acf_add_options_page()
    {

        if ( ! function_exists( 'acf_add_options_page' ) ) {

            helper::log( 'ACF Missing, please install or activate...' );

            return false;

        }

        // helper::log( 'Adding ACF settings page...' );

        \acf_add_options_page( array(
            'page_title' 	=> 'Q Settings',
            'menu_title'	=> 'Q',
            'menu_slug' 	=> 'q',
            'capability'	=> 'manage_options',
            'parent'        => 'options-general.php',
            'redirect'		=> false
        ));

    }



    /**
    * Add ACF Fields
    *
    * @since    2.0.0
    */
    public static function add_fields()
    {

        if ( ! function_exists( 'acf_add_local_field_group' ) ) {

            helper::log( 'ACF Missing, please install or activate...' );

            return false;

        }

        // get all field groups ##
        $groups = self::get_fields();

        if ( 
            ! $groups 
            || ! is_array( $groups )
        ) {

            helper::log( 'No groups to load.' );

            return false;

        }

        // loop over gruops ##
        foreach( $groups as $key => $value ) {

            // filter groups ##
            $value = \apply_filters( 'q/core/options/add_field/'.$key, $value );

            // helper::log( 'filter: q/core/options/add_field/'.$key );
            // helper::log( $value );

            // load them all up ##
            \acf_add_local_field_group( $value );

        }

    }


    /**
    * Define field groups
    *
    * @since    2.0.0
    * @return   Mixed
    */
    public static function get_fields( $group = null )
    {

        // define field groups - exported from ACF ##
        $groups = array (

            'analytics' => array(
                'key' => 'group_q_option_analytics',
                'title' => 'Analytics and Marketing',
                'fields' => array(
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'q',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
            ),

            'ui' => array(
                'key' => 'group_q_option_ui',
                'title' => 'Asset Inclusion',
                'fields' => array(
                    array(
                        'key' => 'field_q_option_plugin_css',
                        'label' => 'Plugin CSS',
                        'name' => 'q_option_plugin_css',
                        'type' => 'radio',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'allow_null' => 0,
                        'other_choice' => 0,
                        'default_value' => 1,
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                        'save_other_choice' => 0,
                    ),
                    array(
                        'key' => 'field_q_option_plugin_js',
                        'label' => 'Plugin JavaScript',
                        'name' => 'q_option_plugin_js',
                        'type' => 'radio',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'allow_null' => 0,
                        'other_choice' => 0,
                        'default_value' => 1,
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                        'save_other_choice' => 0,
                    ),
                    array(
                        'key' => 'field_q_option_theme_css',
                        'label' => 'Theme CSS',
                        'name' => 'q_option_theme_css',
                        'type' => 'radio',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'allow_null' => 0,
                        'other_choice' => 0,
                        'default_value' => 1,
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                        'save_other_choice' => 0,
                    ),
                    array(
                        'key' => 'field_q_option_theme_scss',
                        'label' => 'Theme SCSS',
                        'name' => 'q_option_theme_scss',
                        'type' => 'radio',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'allow_null' => 0,
                        'other_choice' => 0,
                        'default_value' => 0,
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                        'save_other_choice' => 0,
                    ),
                    array(
                        'key' => 'field_q_option_theme_js',
                        'label' => 'Theme JavaScript',
                        'name' => 'q_option_theme_js',
                        'type' => 'radio',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            1 => 'Yes',
                            0 => 'No',
                        ),
                        'allow_null' => 0,
                        'other_choice' => 0,
                        'default_value' => 1,
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                        'save_other_choice' => 0,
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'q',
                        ),
                    ),
                ),
                'menu_order' => 4,
                'position' => 'side',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
            ), 

            'library' => array(
                'key' => 'group_q_option_library',
                'title' => 'CSS & JS Libraries',
                'fields' => array(
                    array(
                        'key' => 'field_q_option_library',
                        'label' => 'Local',
                        'name' => 'q_option_library',
                        'type' => 'checkbox',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'js_sly'            => 'Sly Swipe JS',
                            'js_lazy'           => 'Lazy Load JS',
                            'js_snackbar'       => 'Snackbar JS',
                            'js_popper'         => 'Popper JS',
                            'css_snackbar'      => 'Snackbar CSS',
                            'js_stickyfill'     => 'Stickyfill JS',
                            'js_hashchange'     => 'BA Hashchange JS',
                            'css_tubepress'     => 'TubePress CSS',
                            'css_gravityforms'  => 'Gravity Forms CSS',
                            'css_q.wordpress'   => 'Q WordPress CSS',
                            'css_q.global'      => 'Q Global CSS',
                            'css_bs4'           => 'Bootstrap 4 CSS',
                            'css_bsg'           => 'Bootstrap 4 Grid Temp CSS',
                            'js_q.global'       => 'Q Global JS',
                            'js_bs4'            => 'Bootstrap 4 JS',
                        ),
                        'allow_custom' => 0,
                        'default_value' => array(
                            // 0 => 'css_bootstrapgrid',
                            0 => 'js_sly',
                            1 => 'js_lazy',
                            2 => 'js_snackbar',
                            3 => 'css_snackbar',
                            4 => 'js_stickyfill',
                            5 => 'css_fa'
                        ),
                        'layout' => 'vertical',
                        'toggle' => 1,
                        'return_format' => 'value',
                        'save_custom' => 0,
                    ),

                    array(
                        'key' => 'field_q_option_external',
                        'label' => 'External',
                        'name' => 'q_option_external',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => 'field_q_option_external_title',
                        'min' => 0,
                        'max' => 10,
                        'layout' => 'block',
                        'button_label' => 'ADD',
                        'sub_fields' => array(
                            
                            array(
                                'key' => 'field_q_option_external_type',
                                'label' => 'Type',
                                'name' => 'type',
                                'type' => 'select',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'choices' => array(
                                    'css'   => 'CSS',
                                    'js'    => 'Javascript',
                                    // 'font'  => 'Font',
                                ),
                                'default_value' => 'css',
                                'allow_null' => 0,
                                'multiple' => 0,
                                'ui' => 0,
                                'ajax' => 0,
                                'placeholder' => '',
                                'disabled' => 0,
                                'readonly' => 0,
                                'return_format' => 'value',
                            ),

                            array(
                                'key' => 'field_q_option_external_title',
                                'label' => 'Title',
                                'name' => 'title',
                                'type' => 'text',
                                // 'instructions' => 'File Handle',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => 'Font Awesome',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                                'readonly' => 0,
                                'disabled' => 0,
                            ),

                            array(
                                'key' => 'field_q_option_external_version',
                                'label' => 'Version',
                                'name' => 'version',
                                'type' => 'text',
                                // 'instructions' => 'File Handle',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '5.5.0',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                                'readonly' => 0,
                                'disabled' => 0,
                            ),

                            array(
                                'key' => 'field_q_option_external_url',
                                'label' => 'URL',
                                'name' => 'url',
                                'type' => 'url',
                                // 'instructions' => 'Enter Full URL',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => 'https://use.fontawesome.com/releases/v5.5.0/css/all.css',
                                'placeholder' => '',
                            ),

                        ),

                    ),


                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'q',
                        ),
                    ),
                ),
                'menu_order' => 2,
                'position' => 'side',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
            ),

            'plugin'   => array(
                'key' => 'group_q_option_plugin',
                'title' => 'Global Plugins',
                'fields' => array(
                    array(
                        'key' => 'field_q_option_plugin',
                        'label' => 'Plugins',
                        'name' => 'q_option_plugin',
                        'type' => 'checkbox',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'brandbar'  => 'Global Brand Bar',
                            // 'promo'     => 'Promotion',
                            'consent'   => 'Consent System',
                            'ticker'    => 'News Ticker',
                        ),
                        'allow_custom' => 0,
                        'default_value' => array(
                            0 => 'q-gh-brand-bar',
                            1 => 'q-gh-consent',
                        ),
                        'layout' => 'vertical',
                        'toggle' => 0,
                        'return_format' => 'value',
                        'save_custom' => 0,
                    ),

                    array(
                        'key' => 'field_q_option_plugin_ticker_title',
                        'label' => 'Ticker Title',
                        'name' => 'q_option_plugin_ticker_title',
                        'type' => 'text',
                        'instructions' => 'Add short Ticker Title here ( Max 60 Chars )',
                        'required' => 1,
                        'conditional_logic' => array (
                            array (
                                array (
                                    'field' => 'field_q_option_plugin',
                                    'operator' => '==',
                                    'value' => 'ticker',
                                ),
                            ),
                        ),
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => 'Coronavirus Update',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '60',
                        'readonly' => 0,
                        'disabled' => 0,
                    ),

                    array(
                        'key' => 'field_q_option_plugin_ticker_url',
                        'label' => 'Ticker URL',
                        'name' => 'q_option_plugin_ticker_url',
                        'type' => 'url',
                        'instructions' => 'Enter the full URL',
                        'required' => 1,
                        'conditional_logic' => array (
                            array (
                                array (
                                    'field' => 'field_q_option_plugin',
                                    'operator' => '==',
                                    'value' => 'ticker',
                                ),
                            ),
                        ),
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => 'https://greenheart.org/blog/exchange/coronavirus-update/',
                        'placeholder' => '',
                    ),


                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'q',
                        ),
                    ),
                ),
                'menu_order' => 3,
                'position' => 'side',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
            ),

        );

        // check if we are returning a single set or all groups ##
        if ( is_null( $group ) ) {

            #helper::log( 'Returning all groups.' );

            return $groups;

        } elseif ( 
            isset( $group ) 
            && is_array( $groups )
            && array_key_exists( $group, $groups )
            && array_key_exists( 'fields', $groups[$group] )
        ) {

            #helper::log( 'returning fields in group: '.$group );

            return $groups[$group]['fields'];

        }

        // nothing cooking ##
        return false;

    }



    
    /**
    * Get stored values of defined options
    * 
    * @since       1.0
    * @return      Object
    */
    public static function get( String $field = null ) 
    {
        
        // we need to get all stored options from WP ##
        if ( ! $array = self::wpdb() ) {

            helper::log( 'No stored values found.' );

            return false;

        }

        // now we need to format them into something which all existing theme controllers expect:
        // an array with "q_option_" removed and a value of 1 or 0 ##
        if ( ! $object = self::prepare( $array ) ) {

            helper::log( 'Error preparing stored values' );

            return false;

        }

        // helper::log( $object );

        // check if we have an object ##
        if ( ! is_object( $object ) ) {

            helper::log( 'Error converting stored values to object' );    
            
            return false;

        }

        // test ##
        // helper::log( $object->debug );

        // check if we return a single field or the entire array/object ##
        if ( is_null( $field ) ) {

            // helper::log( 'Returning all options.' );

            return $object;

        } elseif ( 
            isset( $field )
            && isset( $object->$field )
        ) {

            // helper::log( 'returning field: '.$field );

            return $object->$field;

        }

        // return ##
        return false;

    }



    /**
     * Single query to retrieve all stored options saved via ACF
     * 
     * @since 2.3.0
    */
    public static function wpdb()
    {

        if ( self::$query ) {

            // helper::log( 'query already returned, so using stored values...' );

            return self::$query;

        }

        // grab the global object ##
        global $wpdb;

        // run the query ##
        $query = $wpdb->get_results( 
            $wpdb->prepare( 
                "SELECT option_name AS name, option_value AS value FROM $wpdb->options WHERE `option_name` LIKE %s limit 0, 1000",
                'options_q_option%'
            ),
            'ARRAY_A' // array ##
        );

        // test ##
        // helper::log( $query );

        // validate ##
        if ( 
            ! $query  
            || ! is_array ( $query )
            || 0 == count ( $query ) 
        ) {

            // helper::log( 'wpdb failure...' );

            return false;

        }

        // kick it back ##
        return self::$query = $query;

    }



    /**
     * Single query to retrieve all stored options saved via ACF
     * 
     * @since 2.3.0
    */
    public static function prepare( Array $array = null )
    {

        // sanity check ##
        if (
            is_null( $array )
            || ! is_array( $array )
        ) {

            helper::log( 'Passed Array is corrupt.' );

            return false;

        }

        // we will create a new array, with name and value ##
        $object = new \stdClass();

        // loop over each item and remove - some are strings, some are serliazed ##
        foreach ( $array as $item ) {

            // helper::log( $item );

            // get key ##
            $key = str_replace( 'options_q_option_', '', $item['name'] );

            // check if value is serlized, if so, break out as single items ##
            if ( is_serialized( $item['value'] ) ) {

                $option = unserialize( $item['value'] );

                // helper::log( $option );
                // helper::log( core::array_to_object( $option ) );

                // new sub object ##
                $option_object = new \stdClass();

                // we need these to be converted to an object ##
                foreach( $option as $option_key => $option_value ) {

                    // if ( 1 == $option_value ) {

                        // helper::log( $option_value );
                 
                        $option_object->$option_value = true;

                    // }

                }

                $value = $option_object;

            } else {

                $value = ( 1 == $item['value'] ) ? true : $item['value'] ;

            }

            // add ##
            $object->$key = $value ;

        }

        // test ##
        // helper::log( $array );

        // validate ##
        if ( 
            ! is_object ( $object )
            // || 0 == count ( $object ) 
        ) {

            helper::log( 'Prepared object is corrupt.' );

            return false;

        }

        // kick it back ##
        return $object;

    }
    
    

    /**
    * define debug setting from stored option
    *
    * @since 2.3.1   
    */
    public static function debug( $option = null )
    {

        // if debug set in code, use that setting first ##
        if ( self::$debug ) { 
        
            // helper::log( 'Debug set to true in code, so respect that...' );

            return self::$debug; 
        
        }

        // helper::log( 'debug set to: '.self::get('debug') );

        // get all stored options ##
        $debug = self::get('debug'); // \get_field( 'q_option_debug', 'option' ); 
        // \get_site_option( 'options_q_option_debug', false );

        // check ##
        // helper::log( \get_field( 'q_option_debug', 'option') );
        // helper::log( 'debug pulled from options table: '.json_encode( $debug ) );
        // helper::log( 'debug pulled from options table: '. ( 1 == $debug ? 'True' : 'False' ) );

        // make a real boolean ##
        $debug = ( 
            ( 
                '1' == $debug
                || true === $debug 
            ) ? 
            true : 
            false 
        ) ;

        // check what we got ##
        // helper::log( 'debug set to: '. ( $debug ? 'True' : 'False' ) );

        // update property ##
        self::$debug = $debug;

        // kick back something ##
        return self::$debug;

    }



    /**
    * Delete Q Options - could be used to clear old settings
    */
    public static function delete( $option = null )
    {

        

    }



    public static function add_theme_support( $support )
    {

       helper::log( 'add_theme_support is deprecated, please use the new Q settings page and filters.' );

       return false;

    }
    
}