<?php

namespace q\device\theme;

use q\device\core\core as core;
use q\device\core\is as is;
use q\device\core\config as config;
use q\device\core\helper as helper;

// load it up ##
\q\device\theme\controller::run();

class controller extends \q_device {

    public static function run()
    {

        if ( ! \is_admin() ) {

            // add body classes ##
            \add_filter( 'body_class', array ( get_class(), 'body_class' ), 10, 1 );

        }


    }



    /**
     * Add browser classes to html body tag
     * 
     * @since 0.1
     */
    public static function body_class( $classes ) {
        
        // helper::log( $classes );

        // grab the post object ##
        global $post;
        
        // add post type
        if ( $post && is_object($post) ) {
         
            $classes[] = 'posttype-'.$post->post_type;
        
        }

        // let's grab and prepare our site URL ##
        $identifier = strtolower( \get_bloginfo( 'name' ) );

        // add our class ##
        $classes[] = 'install-'.str_replace( array( '.', ' '), '-', $identifier ); // 'install-'.\sanitize_key( $identifier );

        // admin bar - probably not needed ##
        if ( \is_admin_bar_showing() ) {

            $classes[] = 'wpadminbar';
        }

        // grab list of browsers ##
        // $browser = $this->browsers();
        
        // // add browser, version and OS body tags ##
        // $classes[] = ''.$browser['type']; // client ##
        // $classes[] = ''.$browser['type'].'-'.$browser['version']; // client-version ##
        // $classes[] = ''.$browser['agent'].'-'.$browser['type']; // OS-client ##
        
        // top level
        if ( is::handheld() ) { $array[] = "handheld"; };
        if ( is::mobile() ) { $array[] = "mobile"; };
        if ( is::iOS() ) { $array[] = "ios"; };
        if ( is::tablet() ) { $array[] = "tablet"; };
        if ( is::desktop() ) { $array[] = "desktop"; }

        // remove duplicates ##
        $array = array_unique( $array );

        // merge into $classes ##
        $classes = array_merge( $classes, core::prefix( $array, 'browsers-' ), core::prefix( $array, 'device-' ) );

        // test ##
        // helper::log( $classes );
        
        return $classes; // return classes ##

    }
    


}