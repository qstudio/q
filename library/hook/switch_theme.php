<?php

/**
 * Actions to call on switch_theme() hook
 *
 * @since 0.4
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 * @author:     Q Studio
 * @URL:        http://qstudio.us/
 */
namespace q\hook;

use q\core;
use q\core\helper as h;

// load it up ##
\q\hook\switch_theme::run();

class switch_theme extends \Q {

    public static function run()
    {

        if ( \is_admin() ) { // make sure this is only loaded up in the admin ##

            \add_action( 'switch_theme', array ( get_class(), 'switch_theme' ), 1 );

        }

    }


    // function on swtich theme ##
    public static function switch_theme(){

        h::log( 'switched' );

    }

}
