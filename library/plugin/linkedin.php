<?php

namespace q\plugin;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;
use q\core\wordpress as wordpress;
use q\controller\generic as generic;

// load it up ##
\q\plugin\linkedin::run();

class linkedin extends \Q {

    public static function run()
    {
        
        if ( ! \is_admin() ) {

            // add linkedin pixel ##
            \add_action( 'wp_head', [ get_class(), 'marketing'], 2 );

            // add <noscript> after opening <body> tag ##
            \add_action( 'q_action_body_open', [ get_class(), 'noscript'], 2 );

        }

    }



    /**
     * Add FB Pixel <head>
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function marketing()
    {

        // bulk on localhost ##
        if ( helper::is_localhost() ) { 
        
            // helper::log( 'FB pixel not added on localhost' );

            // return false; 
        
        }

        // check if consent given to load script ##
        if ( ! generic::consent( 'marketing' ) ) {

            // helper::log( 'Marketing NOT allowed...' );

            // kick out ##
            return false;

        }

        // grab the options ##
        $q_options = options::get();

        // helper::log( $q_options );

        // bulk if no options found ##
        if ( 
            ! $q_options 
            || ! is_object( $q_options )   
        ) {

            helper::log( 'Error: Options missing...' );

            return false;

        }


        // check if we have tag_manager defined in config ##
        if ( ! isset( $q_options->linkedin ) ) {

            // helper::log( 'linkedin Pixel not defined in config' );

            return false;

        }

        // kick it back, cleanly... ##
        echo $q_options->linkedin;

    }



    /**
     * Add GTM noscript to the <body>
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function noscript()
    {

        // bulk on localhost ##
        if ( helper::is_localhost() ) { 
                
            // helper::log( 'Analytics skipped, as on localhost...' );

            // return false; 

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
            || ! is_object( $q_options )   
        ) {

            helper::log( 'Error: Options missing...' );

            return false;

        }

        // check for UI ##
        if ( ! isset( $q_options->linkedin_noscript ) ) { 

            // Log ##
            // helper::log( 'linkedin No Script not defined' );

            // kick off ##
            return false; 

        }

        // kick it back, cleanly... ##
        echo $q_options->linkedin_noscript;

    }


}