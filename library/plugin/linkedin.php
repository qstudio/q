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

        } else {

            // add fields to Q settings ##
            \add_filter( 'q/core/options/add_field/analytics', [ get_class(), 'filter_acf_analytics' ], 10, 1 );

        }

    }



    public static function filter_acf_analytics( $array ) 
    {

        // test ##
        // helper::log( $array );

        // lets add our fields ##
        array_push( $array['fields'], [

            'key' => 'field_q_option_linkedin',
            'label' => 'LinkedIn Tracking',
            'name' => 'q_option_linkedin',
            'type' => 'textarea',
            'instructions' => 'Enter the complete LinkedIn snippet',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'maxlength' => '',
            'rows' => 4,
            'new_lines' => '',
        
        ]);

        array_push( $array['fields'], [
            'key' => 'field_q_option_linkedin_noscript',
            'label' => 'LinkedIn Tracking Noscript',
            'name' => 'q_option_linkedin_noscript',
            'type' => 'textarea',
            'instructions' => 'Enter the complete LinkedIn Noscript snippet',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'maxlength' => '',
            'rows' => 4,
            'new_lines' => '',
        ]);

        // helper::log( $array['fields'] );

        // kick it back, as it's a filter ##
        return $array;

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
        // $q_options = options::get();

        // helper::log( $q_options );

        // bulk if no options found ##
        if ( 
            ! options::get( 'linkedin' )
        ) {

            // helper::log( 'Error: Options missing...' );

            return false;

        }


        // // check if we have tag_manager defined in config ##
        // if ( ! isset( $q_options->linkedin ) ) {

        //     // helper::log( 'linkedin Pixel not defined in config' );

        //     return false;

        // }

        // kick it back, cleanly... ##
        echo options::get( 'linkedin' );

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
        // $q_options = options::get();

        #helper::log( $q_options );

        // bulk if no options found ##
        if ( 
            ! options::get( 'linkedin_noscript' )
        ) {

            // helper::log( 'Error: Options missing...' );

            return false;

        }

        // check for UI ##
        // if ( ! isset( $q_options->linkedin_noscript ) ) { 

        //     // Log ##
        //     // helper::log( 'linkedin No Script not defined' );

        //     // kick off ##
        //     return false; 

        // }

        // kick it back, cleanly... ##
        echo options::get( 'linkedin_noscript' );

    }


}