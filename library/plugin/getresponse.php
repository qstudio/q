<?php

namespace q\plugin;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;
use q\controller\generic as generic;
use q\core\wordpress as wordpress;

// load it up ##
\q\plugin\getresponse::run();

class getresponse extends \Q {

    // public static $options = null;

    public static function run()
    {

        // add fields ##
        \add_action( 'acf/init', array( get_class(), 'add_fields' ), 2 );

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

            'getresponse' => array(
                'key' => 'group_q_option_getresponse',
                'title' => 'GetResponse',
                'fields' => array(
                    array(
                        'key' => 'field_q_option_getresponse_form',
                        'label' => 'GetResponse Form',
                        'name' => 'q_option_getresponse_form',
                        'type' => 'textarea',
                        'instructions' => 'Enter the complete GetResponse snippet',
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
     * Check if GetResponse form has been saved in ACF field
     * 
     * @since   2.5.1
     * @return  Boolean
     */
    public static function get()
    {

        // helper::log( options::get() );

        // we need to check if there is a field with saved data in Q $options
        // helper::log( options::get( 'getresponse_form' ) );
        if ( 
            options::get( 'getresponse_form' )
        ){

            // helper::log( 'GetResponse field returned.' );

            return options::get( 'getresponse_form' );

        }

        // helper::log( 'GetResponse field error.' );

        return false;

    }



    /**
     * Hook to enqueue assets
     *
     * @since       2.5.1
     */
    public static function hook( $args = null )
    {

        

    }


    /**
     * Render form
     *
     * @since       2.5.1
     */
    public static function render( Array $args = null )
    {

        // check if we have passed args ##
        if ( 
            is_null( $args )
            || empty( $args )
            || ! isset( $args['markup'] ) // only property we need so far ##
            || ! self::get() 
        ){

            // helper::log( 'Missing or corrupt config' );

            return false;

        }


        // we need an empty array and an empty string ##
        $array = [];
        $string = '';

        // add form to array ##
        $array['form'] = self::get();

        // compile ##
        $string = generic::markup( $args['markup'], $array );

        // test ##
        // helper::log( $string );

        // echo it back ##
        echo $string;

        // kick back ##
        return true;

    }


}