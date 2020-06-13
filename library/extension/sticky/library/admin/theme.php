<?php

namespace q\sticky\admin;
use q\sticky\core\core as core;
use q\sticky\core\helper as helper;

// load it up ##
\q\sticky\admin\theme::run();

class theme extends \q_sticky {

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

        \wp_register_style( 'q-sticky-css', helper::get( "theme/css/q-sticky.css", 'return' ), array(), self::version, 'all' );
        \wp_enqueue_style( 'q-sticky-css' );

    }



    
    /*
    * script enqueuer 
    *
    * @since  2.0
    */
    public static function admin_init() {

        // add JS ## -- after all dependencies ##
        \wp_enqueue_script( 'q-sticky-js', helper::get( "theme/javascript/q-sticky.js", 'return' ), array( 'jquery' ), self::version );
        
        // pass variable values defined in parent class ##
        \wp_localize_script( 'q-sticky-js', 'q_sticky_js', array(
                'ajax_nonce'    => wp_create_nonce( 'q_resource_nonce' )
            ,   'ajax_url'      => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ) /*, 'https' */ ## add 'https' to use secure URL ##
            ,   'debug'         => self::$debug
        ));

    }


}