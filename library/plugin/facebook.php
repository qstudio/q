<?php

namespace q\plugins;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;
use q\controller\generic as generic;

// load it up ##
\q\plugins\facebook::run();

class facebook extends \Q {

    public static function run()
    {
        
        if ( ! \is_admin() ) {

            // add facebook pixel ##
            \add_action( 'wp_head', [ get_class(), 'pixel'], 12 );

            // add <noscript> after opening <body> tag ##
            \add_action( 'q_action_body_open', [ get_class(), 'pixel_noscript'], 3 );

        }

    }



    /**
     * Add FB Pixel <head>
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function pixel()
    {

        // bulk on localhost ##
        if ( helper::is_localhost() ) { 
        
            // helper::log( 'FB pixel not added on localhost' );

            return false; 
        
        }

        // check if consent given to load script ##
        if ( ! generic::consent( 'marketing' ) ) {

            // helper::log( 'Marketing NOT allowed...' );

            // kick out ##
            return false;

        }

        // grab the options ##
        $q_options = options::get();

        #helper::log( $q_options );

        // bulk if no options found ##
        if ( 
            ! $q_options 
            || ! is_array( $q_options )    
        ) {

            helper::log( 'Error: Options missing...' );

            return false;

        }


        // check if we have tag_manager defined in config ##
        if ( ! $q_options['facebook_pixel'] ) {

            // helper::log( 'Facebook Pixel not defined in config' );

            return false;

        }

        // kick it back, cleanly... ##
        echo $q_options['facebook_pixel'];

    }



 /**
     * Add GTM noscript to the <body>
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function pixel_noscript()
    {

        // bulk on localhost ##
        if ( helper::is_localhost() ) { 
                
            // helper::log( 'Analytics skipped, as on localhost...' );

            return false; 

        }

        // check if consent given to load script ##
        if ( ! generic::consent( 'marketing' ) ) {

            // helper::log( 'Marketing NOT allowed...' );

            // kick out ##
            return false;

        }

        // grab the options ##
        $q_options = options::get();

        #helper::log( $q_options );

        // bulk if no options found ##
        if ( 
            ! $q_options 
            || ! is_array( $q_options )    
        ) {

            helper::log( 'Error: Options missing...' );

            return false;

        }

        // check for UI ##
        if ( ! $q_options["facebook_pixel_noscript"] ) { 

            // Log ##
            // helper::log( 'Facebook Pixel No Script not defined' );

            // kick off ##
            return false; 

        }

        // kick it back, cleanly... ##
        echo $q_options['facebook_pixel_noscript'];

    }


}