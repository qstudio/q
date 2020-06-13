<?php

namespace q\device\core;

use q\device\core\core as core;
use q\device\core\helper as helper;

// piggyback Q helper ##
use q\core\helper as q_helper;

// load it up ##
// \q\device\core\is::run();

class is extends \q_device {


    /**
     * Is the device a desktop
     */
    public static function desktop()
    {

        if ( 
            ! core::get()->isMobile() 
            && ! core::get()->isTablet()
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
            core::get()->isMobile() 
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
            core::get()->isTablet()
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
            core::get()->isMobile() 
            || core::get()->isTablet()
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
            core::get()->isiOS() 
        ){

            // helper::log( 'is::iOS' );

            return true;

        }

        // helper::log( 'not::iOS' );

        // default ##
        return false;

    }


}