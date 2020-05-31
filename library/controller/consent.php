<?php

namespace q\controller;

use q\core\core as core;
use q\core\helper as helper;
use q\core\config as config;
use q\theme\ui as ui;
use q\controller\minifier as minifier;
use q\controller\css as css;

// load it up ##
// \q\controller\generic\generic::run();

class consent extends \Q {

    public static function run()
    {


    }

	// @todo -- ove all consent logic in here ##


    /** 
     * Check which consent is given by the user
     * 
     * 
     * */
    public static function given( $setting = null )
    {

        if ( is_null( $setting ) ) {

            // helper::log( 'No setting passed, default to true.' );

            return true;

        }

        if ( 
            ! class_exists( '\q\consent\core\cookie' )
        ) {

            // helper::log( 'Consent Class not found, defalt to true' );

            // no ##
            return true;

        }

        if (
            ! \q\consent\core\cookie::is_active( $setting ) 
        ) {

            // helper::log( 'Setting not allowed: '.$setting );

            // no ##
            return false;

        }

        // helper::log( 'Setting allowed: '.$setting );

        // ok ##
        return true;

    }


}