<?php

/**
 * Actions to call on switch_theme() hook
 *
 * @since 0.4
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 * @author:     Q Studio
 * @URL:        https://qstudio.us/
 */
namespace q\hook;

class switch_theme {

	function __construct(){}
	
	function hooks() {

        if ( \is_admin() ) { // make sure this is only loaded up in the admin ##

            \add_action( 'switch_theme', array ( get_class(), 'switch_theme' ), 1 );

        }

    }


    // function on swtich theme ##
    public function switch_theme(){

        h::log( 'switched' );

    }

}
