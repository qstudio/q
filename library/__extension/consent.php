<?php

namespace q\ui;

use q\core;
use q\core\helper as h;
use q\ui;
// use q\controller\minifier as minifier;
// use q\controller\css as css;

// load it up ##
// \q\controller\generic\generic::run();

class consent extends \Q {

    public static function run()
    {


    }

	// @todo -- Move all consent logic in here ##


    /** 
     * Check which consent is given by the user
     * 
     * 
     * */
    public static function given( $setting = null )
    {

        if ( is_null( $setting ) ) {

            // h::log( 'No setting passed, default to true.' );

            return true;

        }

        if ( 
			// ! class_exists( '\q\ui\cookie' )
			// || 
			! class_exists( '\q\consent\core\cookie' )
        ) {

            // h::log( 'Consent Class not found, defalt to true' );

            // no ##
            return true;

        }

        if (
            ! \q\consent\core\cookie::is_active( $setting ) 
        ) {

            // h::log( 'Setting not allowed: '.$setting );

            // no ##
            return false;

        }

        // h::log( 'Setting allowed: '.$setting );

        // ok ##
        return true;

    }


}