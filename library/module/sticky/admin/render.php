<?php

namespace q\module\sticky;

use q\plugin as q;
use q\core\helper as h;
use q\module;

class render extends module\sticky {

    function hooks(){

        if ( \is_admin() ) {

            // load css in admin ##
            \add_action( 'admin_print_styles', array( $this, 'admin_print_styles' ), 2 );
        
            // load JS in admin ##
            \add_action( 'admin_init', array( $this, 'admin_init' ), 2 );

        }

    }

    /*
    * script enqueuer 
    *
    * @since  2.0
    */
    function admin_print_styles() {

        \wp_register_style( 'q-sticky-css', h::get( "module/sticky/asset/css/q-sticky.css", 'return' ), array(), q::$_version, 'all' );
        \wp_enqueue_style( 'q-sticky-css' );

    }

    /*
    * script enqueuer 
    *
    * @since  2.0
    */
    function admin_init() {

        // add JS ## -- after all dependencies ##
        \wp_enqueue_script( 'q-sticky-js', h::get( "module/sticky/asset/js/q-sticky.js", 'return' ), array( 'jquery' ), q::$_version );
        
        // pass variable values defined in parent class ##
        \wp_localize_script( 'q-sticky-js', 'q_sticky_js', array(
                'ajax_nonce'    => wp_create_nonce( 'q_sticky_nonce' )
            ,   'ajax_url'      => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ) 
            ,   'debug'         => q::$_debug
        ));

    }

}
