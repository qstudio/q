<?php

/**
 * Get entries from php error log
 * 
 * 
 */

namespace q\test;

use q\core\core as core; 
use q\core\helper as h;
use q\test\log as log;
// use q\extension\service\asana as asana;

class error {
	
	function __construct(){}

    function hooks(){

		// h::log( 'Here.. somehow..' );

        // schedule cron ##
        // self::schedule_cron();

        // move error logging into Q test temporarily..
        if ( 
            defined( 'WP_DEBUG_LOG' ) 
            && WP_DEBUG_LOG 
        ){
            ini_set( 'error_log', WP_CONTENT_DIR . '/logs/error.log' );
        }

        // helper::log( ini_get('error_log') );

        // add AJAX callback to clear log file
        \add_action( 'wp_ajax_error_empty_log', array( get_class(), 'empty' ) );

        // error logging ##
        \add_action( 'admin_init', array( get_class(), 'setup' ), 1 );

        if ( \is_admin() ) {

            // add admin menu ##
            \add_action( 'admin_menu', array( get_class(), 'admin_menu' ), 1000 );
    
            // allow cron method to be tested via http GET request  ##
            // if ( 
            //     isset( $_GET['q_test'] ) 
            //     && $_GET['q_test'] == 'error' 
            // ) {

            //     // \add_action( 'wp_loaded', [ get_class(), 'cron' ] );

            // }

        }

    }

    /**
     * Run once a day to check if log files are set-up correctly
     * 
     * @since 0.0.1
     */
    public static function setup(){

        // crash it ##
        // \delete_site_transient( 'q/test/error/log/check' );

        if ( false === ( $check = \get_site_transient( 'q/test/error/log/check' ) ) ) {

            h::log( 'd:>setting up error log check...' );

            // set-up log ##
            log::args([
                'action'    => 'error',
                'file'      => 'error.log'
            ]);

            // run the logger ##
            log::run();

            \set_site_transient( 'q/test/error/log/check', true, 24 * HOUR_IN_SECONDS );
        
        }
          
    }




    /*
    * Add submenu item
    *
    * @since      0.0.1
    */
    public static function admin_menu()
    {

        \add_submenu_page(
            'options-general.php',
            __('Q : PHP Errors','q-textdomain'),
            __('Q : PHP Errors','q-textdomain'),
            'manage_options',
            'q-test-errors',
            [ get_class(), 'render' ]
        );

    }



    public static function render()
    {

        // set-up log ##
        log::args([
            'title'     => 'PHP Error Log',
            'action'    => 'error',
            'file'      => 'error.log'
        ]);

        // run log render ##
        log::render();

        // add javascript ##
        // self::javascript();

    }


    
    /**
     * AJAX callback to clear log file
     * 
     */
    public static function empty()
    {

        // helper::log( 'Empty log..' );
        // helper::log( $_POST );

        // set-up log ##
        log::args([
            'action'    => 'error',
            'file'      => 'error.log'
        ]);

        // run the logger ##
        $response = log::empty();

        // helper::log( $response );

        // post data passed, so update values ##
        if( $_POST ){
            
            // secure with a nonce ##
            // if ( false === \check_ajax_referer( 'q-log-nonce', 'nonce', false ) ) {
            
                // helper::log( 'nonce failed' );

                echo json_encode([ 
                    'status'    => true, 
                    'text'      => 'Emptied Log File: '.$_POST['log_file'],
                    'code'      => 1
                ]);
                
                die();

            // }

        }

    }



}
