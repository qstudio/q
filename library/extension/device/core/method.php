<?php

namespace q\extension\device;

use q\core;
use q\core\helper as h;
use q\extension;

// load it up ##
// \q\extension\device\method::run();

class method extends extension\device {

    public static function run()
    {

        // set it ##
        // self::set();

    }


    /**
     * Get all device data 
     */
    public static function get( Array $args = null )
    {

        // get or run a detection ##
        if ( self::$get ) {
         
            // helper::log( 'Mobile_Detect already got..' );

            return self::$get ; 
        
        }

        // helper::log( 'instatiating new Mobile_Detect..' );

        return self::$get = new \Mobile_Detect ;

    }




    /**
     * Set the device device header and user agent via $_GET request
     * 
     * examples: ?q_device=desktop OR =tablet OR ?q_device=client:destkop:browser:opera:version:3_4_1
     */
    public static function set()
    {

        return true;

        // @todo ##
        // only if logged in with x rights...
        // check for $_GET['q_device']

        // Batch methods.
        // $detect->setUserAgent($userAgent);
        // $detect->setHttpHeaders($httpHeaders);

    }



    
    /**
     * Get a simple device handle - desktop, tablet, mobile
     */
    public static function handle( $default = 'handheld' )  
    {

        // grab default ## 
        $string = $default;

        // tablet ##
        if ( is::tablet() ) {

            // assign ##
            $string = 'tablet';

        // } elseif ( is::mobile() ) {

        //     $string = 'mobile';

        // }

        } elseif ( is::handheld() ) {

            // assign ## 
            $string = 'handheld';

        } elseif ( is::desktop() ) {

            // assign ## 
            $string = 'desktop';

        }

        // filter ##
        $string = \apply_filters( 'q/device/handle', $string );

        // return ##
        return $string;

    }




    /**
     * Check a condition versus a limited number of pre-build arguments
     */
    public static function is( $device = 'handheld' )
    {

        // check if we already have a $get object ##
        if ( ! self::$get ) {

            h::log( 'd:>We need to start a new Mobile Detect check' );

            // start a new detection ##
            self::get();

        }

        // for now, we need to know the device - mobile, tablet, desktop or more generically, handheld ##
        switch ( $device ) {

            case "desktop" :

                return is::desktop() ;

            break ;

            case "tablet" :

                return is::tablet() ;

            break ;

            case "handheld" :

                return is::handheld() ;

            break ;

            // default :
            case "mobile" :

                return is::mobile() ;

            break ;

            default :

                h::log( 'd:>There is no test for device: '.$device );

                return false;

            break ;

        }

	}
	


	/**
     * Get the current client
     */
    public static function client()
    {

        // check if we already have a $get object ##
        if ( ! self::$get ) {

            h::log( 'd:>We need to start a new Mobile Detect check' );

            // start a new detection ##
            self::get();

		}
		
		h::log( self::$get );

        return false;

    }




    public static function prefix( Array $array = null, $prefix = 'device-' ) 
    {

        // sanity ##
        if ( 
            is_null( $array )
            || ! is_array( $array ) 
        ){

            h::log( 'd:>Error in passed array' );

            return [];

        } 

        // loop over each item and prefix values ##
        foreach ( $array as $key => $value ){

            $array[$key] = $prefix.$value;

        }

        // test ##
        // helper::log( $array );

        // kick it back ##
        return $array;

    }




}
