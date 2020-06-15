<?php

namespace q\core;

use q\core;
use q\core\helper as h;
use q\plugin; 
// use q\core\wordpress as wordpress;

// load it up ##
\q\core\option::run();

class option extends \Q {

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
		// \add_action( 'acf/init', array( get_class(), 'add_fields' ), 1 );
		
		// add ACF fields
        \add_action( 'acf/init', function() { plugin\acf::add_field_groups( self::add_field_groups() ); }, 1 );

        // example how to inject extra options in libraries select API ##
        // \add_filter( 'acf/load_field/name=q_option_library', [ get_class(), 'filter_acf_library' ], 10, 1 );

        // add link to view library from
        \add_filter( 'acf/load_field/name=q_option_library', [ get_class(), 'filter_acf_library' ], 100, 1 );
        
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

		// h::log( 'd:>here..' );
        // h::log( $field['choices'] );

        // empty array ##
        $array = [];

        foreach( $field['choices'] as $key => $value ) {

			// h::log( 'working: '.$key );
			$location = "Plugin";

            // add to array ##
            $array[$key] = $value; 

            // CSS or JS
            $type = explode( "_" , $key );

            // if no type - skip ##
            if ( 
                ! is_array( $type ) 
                || 2 > count( $type )
            ) {

                // h::log( 'Skipping: '.$key );

                continue;

            }

            $type_dir = ( 'css' == $type[0] ) ? 'css' : 'javascript' ;
            $type_ext = ( 'css' == $type[0] ) ? 'css' : 'js' ;

            // give it a handle ##
            $handle = 'q-'.$key;

			// default ##
			$file = false;

			// look for minified library -- only if debuggin ##
            if ( self::$debug ) {
			
				$file = h::get( "ui/asset/".$type_dir."/".$type[1].".min.".$type_ext, 'return' );

			}

            // if not debugging, check if we can find a non-min version ##
            if ( 
                ! $file
                // ||
                // (
                    // self::$debug 
                    && h::get( "ui/asset/".$type_dir."/".$type[1].".".$type_ext, 'return' )
                // )
            ) {

                $file = h::get( "ui/asset/".$type_dir."/".$type[1].".".$type_ext, 'return' ) ;

            }

            // if no type - skip ##
            if ( ! $file ) {

                h::log( 'd:>Skipping: '.$handle.' - File missing...' );

                continue;

			}
			
			// find location ##
			if ( false !== strpos( $file, 'themes' ) ) { $location = 'Theme'; }

            // h::log( 'd:>Adding library: '.$handle.' with file: '.$file.' as type: '.$type_ext );

            // Add link to view ##
            $array[$key] = '<strong>'.$value.'</strong> from '.$location.' ( <a href="'.$file.'" target="_blank">view</a> )';

        }

        // replace array ##
        $field['choices'] = $array;

        // kick it all back ##
        return $field;

    }



    public static function acf_add_options_page()
    {

        if ( ! function_exists( 'acf_add_options_page' ) ) {

            h::log( 'e:>ACF Plugin Missing, please install or activate...' );

            return false;

        }

        // h::log( 'Adding ACF settings page...' );

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
    * Define field groups
    *
    * @since    2.0.0
    * @return   Mixed
    */
    public static function add_field_groups()
    {

        // define field groups - exported from ACF ##
        $groups = array (

            'q_option_analytics' => array(
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
                'menu_order' => 1,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
            ),

            'q_option_ui' => array(
                'key' => 'group_q_option_ui',
                'title' => 'Asset Inclusion',
                'fields' => array(
					array(
                        'key' => 'field_q_option_theme_parent',
                        'label' => 'Parent Theme -> '.\wp_get_theme()->parent(),
                        'name' => 'q_option_theme_parent',
                        'type' => 'checkbox',
                        'instructions' => 'Valid, if using a child/parent theme',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'css' => 'CSS',
                            'js' => 'JS',
                        ),
                        'allow_null' => 0,
                        'other_choice' => 0,
                        'default_value' => array(
							0 => 'css',
							1 => 'js'
						),
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                        'save_other_choice' => 0,
					),
					array(
                        'key' => 'field_q_option_theme_child',
                        'label' => 'Child Theme -> '.\wp_get_theme(),
                        'name' => 'q_option_theme_child',
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
                            'css' => 'CSS',
                            'js' => 'JS',
                        ),
                        'allow_null' => 0,
                        'other_choice' => 0,
                        'default_value' => array(
							0 => 'css',
							1 => 'js'
						),
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
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                // 'description' => 'Control how Q loads assets from the theme or plugins.',
            ), 

            'q_option_library' => array(
                'key' => 'group_q_option_library',
                'title' => 'Assets',
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
                            'js_lazy'           => 'Lazy Load JS',
                            'js_q.global'       => 'Q Global JS',
                        ),
                        'allow_custom' => 0,
                        'default_value' => array(
                            1 => 'js_q.global',
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

            'q_option_extension'   => array(
                'key' => 'group_q_option_extension',
                'title' => 'Extensions',
                'fields' => array(
                    array(
                        'key' => 'field_q_option_extension',
                        'label' => 'Development Modules',
                        'name' => 'q_option_extension',
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
							'consent'   => 'Consent System',
							'device'   	=> 'Device Detection',
                        ),
                        'allow_custom' => 0,
                        'default_value' => array(
							0 => 'consent',
							1 => 'device',
                        ),
                        'layout' => 'vertical',
                        'toggle' => 0,
                        'return_format' => 'value',
                        'save_custom' => 0,
					),
					
					array(
						'key' => 'field_q_option_extension_consent',
						'label' => 'Privacy URL',
						'name' => 'q_option_extension_consent',
						'type' => 'post_object',
						'instructions' => 'Required for the Consent System to work.',
						'required' => 1,
						'conditional_logic' => array(
							array(
								array(
									'field' => 'field_q_option_extension',
									'operator' => '==',
									'value' => 'consent',
								),
							),
						),
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'post_type' => array(
							0 => 'page',
						),
						'taxonomy' => '',
						'allow_null' => 0,
						'multiple' => 0,
						'return_format' => 'id',
						'ui' => 1,
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
		

		// logic to get all or single group ##
		return $groups;

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

            h::log( 'e:>No stored values found.' );

            return false;

        }

        // now we need to format them into something which all existing theme controllers expect:
        // an array with "q_option_" removed and a value of 1 or 0 ##
        if ( ! $object = self::prepare( $array ) ) {

            h::log( 'e:>Error preparing stored values' );

            return false;

        }

        // h::log( $object );

        // check if we have an object ##
        if ( ! is_object( $object ) ) {

            h::log( 'e:>Error converting stored values to object' );    
            
            return false;

        }

        // test ##
        // h::log( $object->debug );

        // check if we return a single field or the entire array/object ##
        if ( is_null( $field ) ) {

            // h::log( 'Returning all options.' );

            return $object;

        } elseif ( 
            isset( $field )
            && isset( $object->$field )
        ) {

            // h::log( 'returning field: '.$field );

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

            // h::log( 'query already returned, so using stored values...' );

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
        // h::log( $query );

        // validate ##
        if ( 
            ! $query  
            || ! is_array ( $query )
            || 0 == count ( $query ) 
        ) {

            // h::log( 'wpdb failure...' );

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

            h::log( 'e:>Passed Array is corrupt.' );

            return false;

        }

        // we will create a new array, with name and value ##
        $object = new \stdClass();

        // loop over each item and remove - some are strings, some are serliazed ##
        foreach ( $array as $item ) {

            // h::log( $item );

            // get key ##
            $key = str_replace( 'options_q_option_', '', $item['name'] );

            // check if value is serlized, if so, break out as single items ##
            if ( is_serialized( $item['value'] ) ) {

                $option = unserialize( $item['value'] );

                // h::log( $option );
                // h::log( core::array_to_object( $option ) );

                // new sub object ##
                $option_object = new \stdClass();

                // we need these to be converted to an object ##
                foreach( $option as $option_key => $option_value ) {

                    // if ( 1 == $option_value ) {

                        // h::log( $option_value );
                 
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
        // h::log( $array );

        // validate ##
        if ( 
            ! is_object ( $object )
            // || 0 == count ( $object ) 
        ) {

            h::log( 'e:>Prepared object is corrupt.' );

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
        
            // h::log( 'Debug set to true in code, so respect that...' );

            return self::$debug; 
        
        }

        // h::log( 'debug set to: '.self::get('debug') );

        // get all stored options ##
        $debug = self::get('debug'); // \get_field( 'q_option_debug', 'option' ); 
        // \delete_site_option( 'options_q_option_debug', false );

        // check ##
        // h::log( \get_field( 'q_option_debug', 'option') );
        // h::log( 'd:>debug pulled from options table: '.json_encode( $debug ) );
        // h::log( 'debug pulled from options table: '. ( 1 == $debug ? 'True' : 'False' ) );

        // make a real boolean ##
        $debug = ( 
            ( 
                'on' == $debug
                || true === $debug 
            ) ? 
            true : 
            false 
        ) ;

        // check what we got ##
        // h::log( 'd:>debug set to: '. ( $debug ? 'True' : 'False' ) );

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

       h::log( 'd:>add_theme_support is deprecated, please use the new Q settings page and filters.' );

       return false;

    }
    
}
