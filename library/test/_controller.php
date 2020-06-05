<?php

namespace q\test;

use q\core;
use q\core\helper as h;
use q\plugin; 

// load it up ##
\q\test\controller::run();

class controller extends \Q {

    // public static $output = false;

    public static function run()
    {

        // add ACF fields ##
        \add_action( 'acf/init', function() { plugin\acf::add_field_groups( self::add_field_groups() ); }, 1 );

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



    

    // /**
    // * Add ACF Fields
    // *
    // * @since    2.0.0
    // */
    // public static function add_fields()
    // {

    //     // get all field groups ##
    //     $groups = self::get_fields();

    //     if ( 
    //         ! $groups 
    //         || ! is_array( $groups )
    //     ) {

    //         h::log( 'No groups to load.' );

    //         return false;

    //     }

    //     // loop over gruops ##
    //     foreach( $groups as $key => $value ) {

    //         // h::log( 'filter: q/core/options/add_field/'.$key );
    //         // h::log( $value );

    //         // load them all up ##
    //         \acf_add_local_field_group( $value );

    //     }

    // }


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

            'q_option_test'   => array(
                'key' => 'group_q_option_test',
                'title' => 'Testing Options',
                'fields' => array(
                    'debug' => array(
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
                    'test' => array(
                        'key' => 'field_q_option_test',
                        'label' => 'Trackers & Logs',
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
                            // 'email'     => 'SMTP Tracker',
                            'error'     => 'PHP Errors',
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
                'menu_order' => 1,
                'position' => 'side',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
            ),

        );

		// h::log( $groups );
		return $groups;

    }





    public static function check()
    {

        // h::log( 'Checking if test suite is active' );
        // h::log( core\option::get( 'test' ) );

        if (
            core\option::get( 'test' )
            && ! empty( core\option::get( 'test' ) )
            && is_object( core\option::get( 'test' ) )
        ) {

            // h::log( 'test suite options active' );

            // seems good ##
            return true;
        
        }

        // h::log( 'test suite options inactive' );

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
        \wp_enqueue_script( 'q-test-admin-js', h::get( "test/admin.js", 'return' ), array( 'jquery' ), self::version );

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

        // log controller ##
        require_once self::get_plugin_path( 'library/test/log.php' );

        // Asana controller ##
        require_once self::get_plugin_path( 'library/plugin/asana.php' );

        // h::log( options::get( 'test' ) );

        // these are loaded conditionally, via check to stored settings Q Options ##
        foreach ( core\option::get( 'test' ) as $key => $value ) {
            
            // check if file exists ##
            if ( file_exists( self::get_plugin_path( "library/test/{$key}.php" ) ) ) {

                require_once self::get_plugin_path( "library/test/{$key}.php" );

            }

        }

    }



}