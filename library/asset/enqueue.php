<?php

namespace q\asset;

use q\core;
use q\core\helper as h;

// Q Theme ##
// use q\theme\core\helper as theme_h;

// load it up ##
\q\asset\enqueue::run();

class enqueue extends \Q {

    // public static $plugin_version;
    public static $plugin_version;
    public static $option;

    public static function run()
    {

        // check we have dependencies ##
        if ( ! self::has_dependencies() ){

            return false;

        }

        // load templates ##
		self::load_properties();
		
        if ( ! \is_admin() ) {

            // local external scripts ##
            \add_action( 'wp_enqueue_scripts', array ( get_class(), 'wp_enqueue_scripts_external' ), 3 );

        }

    }


    

    /**
     * Check for required classes to build UI features
     * 
     * @return      Boolean 
     * @since       0.1.0
     */
    public static function has_dependencies()
    {

        // check for what's needed ##
        if (
            ! class_exists( 'q_theme' ) // how to get around this ?? ##
        ) {

            // h::log( 'e:>@todo --- Q requires q_theme to run correctly..' );

            // return false;

        }

        // ok ##
        return true;

    }



    /**
    * Load Properties
    *
    * @since        2.0.0
    */
    private static function load_properties()
    {

        // assign values ##
        // self::$plugin_version = self::version ;

        // grab the options ##
        self::$option = core\option::get();
        // h::log( self::$option );

    }

    
    
    /*
    * script enqueuer 
    *
    * @since  2.0
    */
    public static function wp_enqueue_scripts_external() {

        // dump - shuold be an interger repesenting how many external libraries are added ##
        // h::log( self::$option->external );
        // h::log( \get_field( 'q_option_external', 'option' ) );

        /*
        [external] => 1
        [external_0_title] => Font Awesome
        [external_0_type] => css
        [external_0_url] => https://use.fontawesome.com/releases/v5.5.0/css/all.css
        [external_0_version] => 5.5.0
        */

        // sanity check ##
        if ( 
            ! isset( self::$option->external )
            || 1 > self::$option->external
        ){

            // h::log( 'No external libraries to load' );

            return false;

        }

        // our query returns all items are single properties of the $options object - so, let's make an array ##
        if( \have_rows( 'q_option_external', 'option' ) ) {

            while( \have_rows( 'q_option_external', 'option' ) ) {
                
                // set things up ##
                \the_row(); 

                // properties ##
                // external libraries are saved in an array with "type", "title", "version" and "url" ##
                $type = \get_sub_field('type');
                $title = \get_sub_field('title');
                $version = \get_sub_field('version');
                $url = \get_sub_field('url');

                // h::log( 'working External: '.$title );

                // sanitize title to handle ##
                $handle = \sanitize_key( $title );

                // validate URL ##

                // debug ##
                // h::log( 'Adding external library: '.$handle.' version '.$version.' from url: '.$url.' as type: '.$type );

                // register and enqueue ##
                switch ( $type ) {

                    case "css" :

                        \wp_register_style( $handle, $url, '', $version, 'all' );
                        \wp_enqueue_style( $handle );

                    break ;

                    case "js" :

                        \wp_register_script( $handle, $url, array(), $version, 'all' );
                        \wp_enqueue_script( $handle );

                    break ;

                }

            }

        } else {

            // h::log( 'No external libraries to load...' );

        }

    }


}
