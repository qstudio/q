<?php

namespace q\test;

use q\core\core as core;
use q\core\options as options;
use q\core\helper as helper;

use q\test\email as email ;

// load it up ##
\q\test\controller::run();

class controller extends \Q {

    public static $output = false;

    public static function run()
    {

        // add fields ##
        \add_action( 'acf/init', array( get_class(), 'add_fields' ), 1 );

        // check if the test suite is activated via Q settings ##
        if ( ! self::check() ) {

            return false;

        }

        // admin assets ##
        if ( \is_admin() ){

            // admin js ##
            \add_action( 'admin_enqueue_scripts', array( get_class(), 'admin_enqueue_scripts' ), 1 );

        }

        // load templates ##
        self::load_libraries();
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
        foreach( $groups as $key => $value ) {

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

            'test'   => array(
                'key' => 'group_q_option_test',
                'title' => 'Test Options',
                'fields' => array(
                    array(
                        'key' => 'field_q_option_debug',
                        'label' => 'Debugging',
                        'name' => 'q_option_debug',
                        'type' => 'true_false',
                        'instructions' => 'Control plugin debug settings',
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
                    array(
                        'key' => 'field_q_option_test',
                        'label' => 'Cron & Logs',
                        'name' => 'q_option_test',
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
                            'email'     => 'Email',
                            'error'     => 'Error Log',
                            // 'url'   => 'URL',
                        ),
                        'allow_custom' => 0,
                        'default_value' => array(
                            // 0 => 'q-gh-brand-bar',
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





    public static function check()
    {

        // helper::log( 'Checking if test suite is active' );
        // helper::log( options::get( 'test' ) );

        if (
            options::get( 'test' )
            && ! empty( options::get( 'test' ) )
            && is_object( options::get( 'test' ) )
        ) {

            // helper::log( 'test suite options active' );

            // seems good ##
            return true;
        
        }

        // helper::log( 'test suite options inactive' );

        // inactive ##
        return false;    

    }



    /**
    * include plugin admin assets
    *
    * @since        0.1.0
    * @return       __void
    */
    public static function admin_enqueue_scripts() {

        // add JS ## -- after all dependencies ##
        \wp_enqueue_script( 'q-test-admin-js', helper::get( "test/admin.js", 'return' ), array( 'jquery' ), self::version );

        // nonce ##
        $nonce = \wp_create_nonce( 'q-test-nonce' );

        // pass variable values defined in parent class ##
        \wp_localize_script( 'q-test-admin-js', 'q_test_admin', array(
            'ajaxurl'           => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ), ## add 'https' to use secure URL ##
            'debug'             => self::$debug,
            'nonce'             => $nonce
        ));

    }




    /**
    * Load Libraries
    *
    * @since        0.0.1
    */
    private static function load_libraries()
    {

        // core ##
        require_once self::get_plugin_path( 'library/test/log.php' );
        require_once self::get_plugin_path( 'library/test/asana.php' );

        // helper::log( options::get( 'test' ) );

        // these are loaded conditionally, via check to stored settings Q Options ##
        foreach ( options::get( 'test' ) as $key => $value ) {
            
            // check if file exists ##
            if ( self::get_plugin_path( "library/test/{$key}.php" ) ) {

                require_once self::get_plugin_path( "library/test/{$key}.php" );

            }

        }

    }



}