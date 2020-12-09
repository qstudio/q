<?php

namespace q;

use q\plugin as q;
use q\core;
use q\core\helper as h;

class test {

	// public static $output = false;
	
	function __construct(){}

    function hooks(){

        // add ACF fields ##
        \add_action( 'acf/init', function() { \q\plugins\acf::add_field_groups( self::add_field_groups() ); }, 1 );

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
    * Define field groups
    *
    * @since    2.0.0
    * @return   Mixed
    */
    public static function add_field_groups(){

        // define field groups - exported from ACF ##
        $groups = array (

            'q_option_test'   => array(
                'key' => 'group_q_option_test',
                'title' => 'Tests & Debugging',
                'fields' => array(
                    'debug' => array(
                        'key' => 'field_q_option_debug',
                        'label' => 'Debugging',
                        'name' => 'q_option_debug',
                        'type' => 'radio',
                        'instructions' => 'Global plugin debug setting',
						'required' => 1,
						'layout' => 'horizontal',			
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'allow_custom' => 0,
                        'choices' 	=> array(
                            'on' 	=> 'On',
                            'off'   => 'Off',
                        ),
                        'default_value' => array(
                            0 		=> 'off',
                        ),
						// 'description' => 'dfdf',
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
                'menu_order' => 10,
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

    public static function check(){

        // h::log( 'Checking if test suite is active' );
        // h::log( core\option::get( 'test' ) );

        if (
            core\option::get( 'test' )
            && ! empty( core\option::get( 'test' ) )
            && is_object( core\option::get( 'test' ) )
        ) {

            // h::log( 'Q test suite options active' );

            // seems good ##
            return true;
        
        }

        // h::log( 'Q test suite options inactive' );

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
        \wp_enqueue_script( 'q-test-admin-js', h::get( "test/asset/javascript/admin.js", 'return' ), array( 'jquery' ), q::$_version );

        // nonce ##
        $nonce = \wp_create_nonce( 'q-test-nonce' );

        // pass variable values defined in parent class ##
        \wp_localize_script( 'q-test-admin-js', 'q_test_admin', array(
            'ajaxurl'           => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ), ## add 'https' to use secure URL ##
            'debug'             => q::$_debug,
            'nonce'             => $nonce
        ));

    }

    /**
    * Load Libraries
    *
    * @since        0.0.1
    */
    private static function load_libraries(){

        // log controller ##
        require_once q::get_plugin_path( 'library/test/log.php' );

        // Asana controller ##
        // require_once self::get_plugin_path( 'library/extension/service/asana.php' );

        // h::log( options::get( 'test' ) );

        // these are loaded conditionally, via check to stored settings Q Options ##
        foreach ( core\option::get( 'test' ) as $key => $value ) {
            
            // check if file exists ##
            if ( file_exists( q::get_plugin_path( "library/test/{$key}.php" ) ) ) {

				require_once q::get_plugin_path( "library/test/{$key}.php" );
				
				// instantiate test ##
				$class = "\\q\\test\\{$key}";
				$test = new $class();
				$test->hooks();

            }

        }

    }



}
