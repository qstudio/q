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
    public static $action = 'email';
    public static $admin_parent = 'tools.php';
    public static $admin_title = 'SMTP Tracker';
    public static $admin_slug = 'q-smtp-tracker';

    public static function run()
    {

        // schedule cron ##
        self::schedule_cron();

        // add AJAX callback to clear log file
        \add_action( 'wp_ajax_'.self::$action.'_empty_log', array( get_class(), 'empty' ) );

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
        // \delete_site_transient( 'q/test/'.self::$action.'/log/check' );

        if ( false === ( $check = \get_site_transient( 'q/test/'.self::$action.'/log/check' ) ) ) {

            helper::log( 'setting up '.self::$action.' log check...' );

            // set-up log ##
            log::args([
                'file'  => self::$log_file
            ]);

            // run the logger ##
            log::run();

            // set tranny ##
            \set_site_transient( 'q/test/'.self::$action.'/log/check', true, 24 * HOUR_IN_SECONDS );
        
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
            self::$admin_parent, // 'users.php', // admin parent ##
            self::$admin_title,
            self::$admin_title,
            'manage_options',
            self::$admin_slug,
            [ get_class(), 'render' ]
        );

    }



    public static function render()
    {

        // set-up log ##
        log::args([
            'title'     => self::$admin_title,
            'file'      => self::$log_file
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
     * Cron Setup
     *
     * 
     * @since   0.0.01
     * @return  void
     */
    public static function cron()
    {

        // set-up log ##
        log::args([
            'file'  => self::$log_file
        ]);
        
        // run test to see if email can be delivered ##
        self::test();

    }



    /**
     * Check the SMTP plugin classes are available and attempt to deliver an email, catching the reponse to validate
     * 
     */
    public static function test( $url = null )
    {

        // get URL ##
        $url = \get_admin_url( \get_current_blog_id() ).self::$admin_parent.'?page='.self::$admin_slug;

        // test ##
        // helper::log( 'url: '.$url );

        // compile data ##
        $array = [ 
            'status'    => 'ERROR',
            'response'  => 'Waiting for test to begin',
        ];

        // run test ##
        if ( ! class_exists( 'EasyWPSMTP' ) ) {

            helper::log( 'SMTP class missing, no way to run test...' ) ;

            $array['response'] = 'SMTP class missing, no way to run test.';

            // write to the log file ##
            log::write( $array['status'].' --> '.$array['response'] );

            asana::create_task([
                'method'        => 'email', // email / api ##
                'response'      => $array['status'].' --> '.$array['response'],
                'email'         => 'x+310727860574480@mail.asana.com',
                'subject'       => 'SMTP Failure',
                'source'        => $url,
                'fake'          => false // fake it ##
            ]);

            // kick back ##
            // return false;

        } else {

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
                    'method'        => 'email', // email / api ##
                    'response'      => $array['status'].' --> '.$array['response'],
                    'email'         => 'x+310727860574480@mail.asana.com',
                    'subject'       => 'SMTP Failure',
                    'source'        => $url,
                    'fake'          => false // fake it ##
                ]);

            }

        }

        // kick back ##
        return true;

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
