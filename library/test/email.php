<?php

/**
 * Check if email is working, log checks, provde a viewer and download option
 * 
 * 
 */

namespace q\test;

use q\core\core as core; 
use q\core\helper as helper;
use q\test\log as log;
use q\plugin\asana as asana;

\q\test\email::run();

class email extends \Q {
    
    public static $log_file = 'email.log';

    public static function run()
    {

        // schedule cron ##
        self::schedule_cron();

        // add AJAX callback to clear log file
        \add_action( 'wp_ajax_email_empty_log', array( get_class(), 'empty' ) );

        // error logging ##
        \add_action( 'admin_init', array( get_class(), 'setup' ), 1 );

        if ( \is_admin() ) {

            // add Email menu ##
            \add_action( 'admin_menu', array( get_class(), 'admin_menu' ), 1000 );
    
            // // allow cron method to be tested via http GET request  ##
            // if ( 
            //     isset( $_GET['q_test'] ) 
            //     && $_GET['q_test'] == 'email' 
            // ) {

            //     \add_action( 'wp_loaded', [ get_class(), 'cron' ] );

            // }

        }

    }


    /**
     * Run once a day to check if log files are set-up correctly
     * 
     * @since 0.0.1
     */
    public static function setup() 
    {

        // crash it ##
        // \delete_site_transient( 'q/test/email/log/check' );

        if ( false === ( $check = \get_site_transient( 'q/test/email/log/check' ) ) ) {

            helper::log( 'setting up email log check...' );

            // set-up log ##
            log::args([
                'file'  => self::$log_file
            ]);

            // run the logger ##
            log::run();

            // set tranny ##
            \set_site_transient( 'q/test/email/log/check', true, 24 * HOUR_IN_SECONDS );
        
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
            __('Q : SMTP Tracker','q-textdomain'),
            __('Q : SMTP Tracker','q-textdomain'),
            'manage_options',
            'q-test-email',
            [ get_class(), 'render' ]
        );

    }



    public static function render()
    {

        // set-up log ##
        log::args([
            'file'  => self::$log_file
        ]);

        // run log render ##
        log::render();

        // add javascript ##
        // self::javascript();

    }


    

    /**
    * Schedule Email Cron check
    *
    */
    public static function schedule_cron()
    {

        // add geoid check cron event ##
        if ( ! \wp_next_scheduled ( 'q_test_hourly_email' ) ) {
            
            \wp_schedule_event(time(), 'hourly', 'q_test_hourly_email' );

        }

        // schedule geoid check ##
        \add_action( 'q_test_hourly_email', [ 'q\\test\\email', 'cron' ] );

    }



    /**
     * Cron check to see if email is deliverable via stored SMTP settings
     * Run once every hour or directly via http GET request
     *
     * 
     * @since   0.0.01
     * @return  void
     */
    public static function cron()
    {

        // helper::log( 'debugging: '.self::$debug );

        // bulk on localhost ##
        if ( 
            false === self::$debug
            && (
                helper::is_localhost() 
                // || helper::is_staging()
            )
        ) { 

            helper::log( 'Email Check blocked by debugging or domain settings...' );
            
            return false; 
        
        }

        // set-up log ##
        log::args([
            'file'  => self::$log_file
        ]);
        
        // empty array ##
        $debug = [];

        // run test to see if email can be delivered ##
        $debug = self::test();

        // grab data from buffer ##
        ob_start();
        var_dump($debug);
        $debug_data = ob_get_clean();

        // helper::log( 'Log finished..' );
        // helper::log( $debug_data );

        // email -- ironic ##
        \wp_mail( 'ray@qstudio.us', 'Cron : Q Test Email', $debug_data );

    }




    public static function test( $url = null )
    {

        // run test ##
        if ( ! class_exists( 'EasyWPSMTP' ) ) {

            helper::log( 'SMTP class missing, no way to run test...' ) ;

            return false;

        }

        // get instance of SMTP control class ##
        $EasyWPSMTP	= \EasyWPSMTP::get_instance();

        // test ##
        $results = $EasyWPSMTP->test_mail( 'wordpress@greenheart.org', 'Q Test Email', 'Test message...' );

        // helper::log( $results );

        // response is messy, let's clean it up ##
        $response = 
            isset( $results['error'] ) && isset( $results['debug_log'] ) ? 
            $results['debug_log'] :
            'Test email was successfully sent. No errors occurred during the process.' ;
        
        // clean up ##
        $response = str_replace( array( "\n", "\t", "\r" ), ' - ', $response );

        // helper::log( $response );

        // compile data ##
        $array = [ 
            'status'    => isset( $results['error'] ) ? 'ERROR' : 'WORKING',
            // 'code'      => '200', // @todo ##
            'response'  => $response,
        ];

        // write to the log file ##
        log::write( $array['status'].' --> '.$array['response'] );

        // if we found an error, we need to try and open a tas in Asana ##
        if( 'ERROR' == $array['status'] ) {

            asana::create_task([
                'method'    => 'email', // email / api ##
                'response'  => $array['status'].' --> '.$array['response'],
                'email'     => 'x+310727860574480@mail.asana.com'
            ]);

        }

        // kick it back ##
        return $array;

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
            'file'  => self::$log_file
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
