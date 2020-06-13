<?php

namespace q\extension\device;

use q\core;
use q\core\helper as h;
use q\extension;

// load it up ##
\q\extension\device\is::run();

class is extends extension\device {


    /**
     * Is the device a desktop
     */
    public static function desktop()
    {

        if ( 
            ! method::get()->isMobile() 
            && ! method::get()->isTablet()
        ){

            // helper::log( 'is::desktop' );

            return true;

        }

        // helper::log( 'not::desktop' );

        // default ##
        return false;

    }



    /**
     * Is the device a mobile 
     */
    public static function mobile()
    {

        if ( 
            method::get()->isMobile() 
        ){

            // helper::log( 'is::mobile' );

            return true;

        }

        // helper::log( 'not::mobile' );

        // default ##
        return false;

    }



    
    /**
     * Is the device a tablet
     */
    public static function tablet()
    {

        if ( 
            method::get()->isTablet()
        ){

            // helper::log( 'is::tablet' );

            return true;

        }

        // helper::log( 'not::tablet' );

        // default ##
        return false;

    }



    /**
     * Is the device a handheld
     */
    public static function handheld()
    {

        if ( 
            method::get()->isMobile() 
            || method::get()->isTablet()
        ){

            // helper::log( 'is::handheld' );

            return true;

        }

        // helper::log( 'not::handheld' );

        // default ##
        return false;

    }



    /**
     * Is the device a iOS
     */
    public static function iOS()
    {

        if ( 
            method::get()->isiOS() 
        ){

            // helper::log( 'is::iOS' );

            return true;

        }

        // helper::log( 'not::iOS' );

        // default ##
        return false;

    }


}
