<?php

namespace q\extension\sticky;

use q\core\helper as h;
use q\extension;

// load it up ##
\q\extension\sticky\render::run();

class render extends extension\sticky {

    public static function run()
    {

        if ( \is_admin() ) {

            // load css in admin ##
            \add_action( 'admin_print_styles', array( get_class(), 'admin_print_styles' ), 2 );
        
            // load JS in admin ##
            \add_action( 'admin_init', array( get_class(), 'admin_init' ), 2 );

        }

    }



    /*
    * script enqueuer 
    *
    * @since  2.0
    */
    public static function admin_print_styles() {

        \wp_register_style( 'q-sticky-css', h::get( "extension/sticky/asset/css/q-sticky.css", 'return' ), array(), self::version, 'all' );
        \wp_enqueue_style( 'q-sticky-css' );

    }



    
    /*
    * script enqueuer 
    *
    * @since  2.0
    */
    public static function admin_init() {

        // add JS ## -- after all dependencies ##
        \wp_enqueue_script( 'q-sticky-js', h::get( "extension/sticky/asset/js/q-sticky.js", 'return' ), array( 'jquery' ), self::version );
        
        // pass variable values defined in parent class ##
        \wp_localize_script( 'q-sticky-js', 'q_sticky_js', array(
                'ajax_nonce'    => wp_create_nonce( 'q_sticky_nonce' )
            ,   'ajax_url'      => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ) 
            ,   'debug'         => self::$debug
        ));

    }


}
