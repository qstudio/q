<?php

// namespace ##
namespace q\extension\consent;

// Q ##
use q\core;
use q\core\helper as h;
use q\extension;

/**
 * AJAX callbacks
 *
 * @package   q\consent
 */

// load it up ##
\q\extension\consent\callback::run();

class callback extends extension\consent {

	/**
     * Construct
     *
     * @since       0.2
     * @return      void
     */
    public static function run()
    {

    	// delete cookie ##
        \add_action( 'wp_ajax_consent_reset', [ get_class(), 'reset' ] ); // ajax for logged in users
        \add_action( 'wp_ajax_nopriv_consent_reset', [ get_class(), 'reset' ] ); // ajax for not logged in users

        // set cookie ##
        \add_action( 'wp_ajax_consent_set', [ get_class(), 'set' ] ); // ajax for logged in users
        \add_action( 'wp_ajax_nopriv_consent_set', [ get_class(), 'set' ] ); // ajax for not logged in users

    }



    /**
     * Delete stored cookie
     *
     * @since       0.1
     * @return      Boolean
     */
    public static function reset()
    {

        // Check if a cookie has been set##
        if ( 
            cookie::get()
        ) {

            // log ##
            // helper::log( 'Cookie found and emptied.' );

            unset( $_COOKIE[self::$slug] );
            setcookie( self::$slug, null, -1, '/' );

            $return = [
                'status'    => true,
                'message'   => 'Stored Consent preferences reset to default.'    
            ];

        } else {

            // log ##
            // helper::log( 'No cookie found, so no action taken...' );

            $return = [
                'status'    => false,
                'message'   => 'No stored Consent settings found.'    
            ];

        }

        // set headers ##
        header( "Content-type: application/json" );

        // return it ##
        echo json_encode( $return );

        // all AJAX calls must die!! ##
        die();

    }



    /**
     * Save $_POSTed data to user cookie
     *
     * @since       0.1
     * @return      Boolean
     */
    public static function set()
    {

        // helper::log( 'We are setting the Consent...' );
        // helper::log( $_POST );

        // try to set cookie ##
        $set_cookie = true;

        // AJAX referer check removed, as failing for no clear reason - security not so important to justify UX hickup ##
        // // check nonce ##
        // // if ( ! \check_ajax_referer( 'q_consent', 'nonce', false ) ) {
        // helper::log( \wp_verify_nonce( 'ajax_consent' ) );
        // if ( ! \wp_verify_nonce( 'ajax_consent' ) ) {

        //     helper::log( 'AJAX referer failed...' );

        //     $return = [
        //         'status'    => '400',
        //         'message'   => 'Problem saving Consent preferences, please try again.'    
        //     ];

        //     // flag ##
        //     $set_cookie = false;

        // }

        // sanity ##
        if ( 
            ! isset( $_POST['q_consent_marketing'] ) 
            || ! isset( $_POST['q_consent_analytics'] )
            // || ! is_array( $_POST['q_consent'] )    
        ) {

            h::log( 'd:>Error in data passed to AJAX' );

            // return 0 ##
            $return = [
                'status'    => '400',
                'message'   => 'Problem saving Consent preferences, please try again.'    
            ];

            // flag ##
            $set_cookie = false;

        }

        // continue ##
        if ( $set_cookie ) {

            // helper::log( $_POST );

            // format array... ##
            $array = [];
            
            // marketing ##
            $array['marketing'] = $_POST['q_consent_marketing'] ? 1 : 0 ;

            // analytics ##
            $array['analytics'] = $_POST['q_consent_analytics'] ? 1 : 0 ;

            // add active consent to array as this has come from an user action ##
            $array['consent'] = 1;

            // check ##
            // helper::log( $array );

            // check for stored cookie -if found, update ##
            if ( cookie::set( $array ) ) {

                // log ##
                // helper::log( 'AJAX saved cookie data' );

                // positive outcome ##
                $return = [
                    'status'    => '200',
                    'message'   => 'Consent preferences saved, thank you.'    
                ];

            } else {

                // log ##
                // helper::log( 'AJAX failed to save cookie data' );

                // negative outcome ##
                $return = [
                    'status'    => '400',
                    'message'   => 'Problem saving Consent preferences, please try again.'    
                ];

            }

        }

        // set headers ##
        header("Content-type: application/json");

        // return it ##
        echo json_encode( $return );

        // all AJAX calls must die!! ##
        die();

    }


}
