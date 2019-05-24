<?php

namespace q\core;

use q\core\core as core;
use q\core\helper as helper;
use q\core\wordpress as wordpress;

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

        // example how to inject extra options in libraries select API ##
        // \add_filter( 'acf/load_field/name=q_option_library', [ get_class(), 'filter_acf_library' ], 10, 1 );
        
        // get stored options, early ##
        // \add_action( 'plugins_loaded', [ get_class(), 'get' ], 1 );

        // set debug ##
        \add_action( 'plugins_loaded', [ get_class(), 'debug' ], 1 );

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
     * Example of how to add a select option for a new library
     * 
     * @since 2.3.0
     */
    public static function filter_acf_library( $field )
    {

        // helper::log( $field['choices'] );
        // helper::log( $field['default_value'] );

        // pop on a new choice ##
        $field['choices']['new'] = 'New Item';

        // make it selected ##
        $field['default_value'][] = 'new';

        // helper::log( $field['choices'] );
        // helper::log( $field['default_value'] );

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
        foreach( $groups as $group ) {

            // load them all up ##
            \acf_add_local_field_group( $group );

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
                    array(
                        'key' => 'field_q_option_google_analytics',
                        'label' => 'Google Analytics',
                        'name' => 'q_option_google_analytics',
                        'type' => 'textarea',
                        'instructions' => 'Enter the complete Google Analytics snippet',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'maxlength' => '',
                        'rows' => 3,
                        'new_lines' => '',
                    ),
                    array(
                        'key' => 'field_q_option_google_tag_manager',
                        'label' => 'Google Tag Manager',
                        'name' => 'q_option_google_tag_manager',
                        'type' => 'textarea',
                        'instructions' => 'Enter the complete Google Tag Manager snippet',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'maxlength' => '',
                        'rows' => 4,
                        'new_lines' => '',
                    ),
                    array(
                        'key' => 'field_q_option_google_tag_manager_noscript',
                        'label' => 'Google Tag Manager Noscript',
                        'name' => 'q_option_google_tag_manager_noscript',
                        'type' => 'textarea',
                        'instructions' => 'Enter the complete Google Tag Manager noscript snippet',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'maxlength' => '',
                        'rows' => 4,
                        'new_lines' => '',
                    ),
                    array(
                        'key' => 'field_q_option_facebook_pixel',
                        'label' => 'Facebook Pixel',
                        'name' => 'q_option_facebook_pixel',
                        'type' => 'textarea',
                        'instructions' => 'Enter the complete Facebook Pixel snippet',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'maxlength' => '',
                        'rows' => 4,
                        'new_lines' => '',
                    ),
                    array(
                        'key' => 'field_q_option_facebook_pixel_noscript',
                        'label' => 'Facebook Pixel Noscript',
                        'name' => 'q_option_facebook_pixel_noscript',
                        'type' => 'textarea',
                        'instructions' => 'Enter the complete Facebook Pixel Noscript snippet',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'maxlength' => '',
                        'rows' => 4,
                        'new_lines' => '',
                    ),
                    array(
                        'key' => 'field_q_option_linkedin',
                        'label' => 'LinkedIn Tracking',
                        'name' => 'q_option_linkedin',
                        'type' => 'textarea',
                        'instructions' => 'Enter the complete LinkedIn snippet',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'maxlength' => '',
                        'rows' => 4,
                        'new_lines' => '',
                    ),
                    array(
                        'key' => 'field_q_option_linkedin_noscript',
                        'label' => 'LinkedIn Tracking Noscript',
                        'name' => 'q_option_linkedin_noscript',
                        'type' => 'textarea',
                        'instructions' => 'Enter the complete LinkedIn Noscript snippet',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'maxlength' => '',
                        'rows' => 4,
                        'new_lines' => '',
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
                'menu_order' => 3,
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
                'title' => 'External Libraries',
                'fields' => array(
                    array(
                        'key' => 'field_q_option_library',
                        'label' => 'Libraries',
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
                            'bootstrap_grid_css'=> 'Bootstrap Grid CSS',
                            'sly_js'            => 'Sly Swipe JS',
                            'lazy_js'           => 'Lazy Load JS',
                            'snackbar_js'       => 'Snackbar JS',
                            'snackbar_css'      => 'Snackbar CSS',
                            'stickyfill_js'     => 'Stickyfill JS',
                            'ba_hashchange_js'  => 'Hashchange JS',
                            'colorbox_js'       => 'Colorbox JS',
                            'colorbox_css'      => 'Colorbox CSS',
                            'twitter_css'       => 'Twitter CSS',
                            'flickr_js'         => 'Flickr JS',
                            'tubepress_css'     => 'TubePress CSS',
                            'gravityforms_css'  => 'Gravity Forms CSS',
                        ),
                        'allow_custom' => 0,
                        'default_value' => array(
                            0 => 'bootstrap_grid_css',
                            1 => 'sly_js',
                            2 => 'lazy_js',
                            3 => 'snackbar_js',
                            4 => 'snackbar_css',
                            5 => 'stickyfill_js',
                        ),
                        'layout' => 'vertical',
                        'toggle' => 1,
                        'return_format' => 'value',
                        'save_custom' => 0,
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
                            'q-gh-brand-bar' => 'Global Brand Bar',
                            'q-gh-consent' => 'Consent',
                            'q-search' => 'Search',
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
                'menu_order' => 1,
                'position' => 'side',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
            ),

            'debug' => array(
                'key' => 'group_q_option_debug',
                'title' => 'Debug',
                'fields' => array(
                    array(
                        'key' => 'field_q_option_debug',
                        'label' => 'Enable debugging',
                        'name' => 'q_option_debug',
                        'type' => 'true_false',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'allow_custom' => 0,
                        'default_value' => 0,
                        'ui' => 0,
                        'ui_on_text' => '',
                        'ui_off_text' => '',
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

        // check if we ar returning a single set or all groups ##
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

        // grab teh global object ##
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
        
            helper::log( 'Debug set to true' );

            return true; 
        
        }

        // get all stored options ##
        $debug = \get_site_option( 'options_q_option_debug', false );

        // check ##
        // helper::log( $debug );
        // helper::log( 'debug pulled from options table: '. ( 1 == $debug ? 'True' : 'False' ) );

        // make a real boolean ##
        $debug = ( '1' == $debug ? true : false ) ;

        // check what we got ##
        // helper::log( 'debug set to: '. ( $debug ? 'True' : 'False' ) );

        // kick it back ##
        return self::$debug = $debug;

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