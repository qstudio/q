<?php

namespace q\extension;

// Q ##
use q\core;
use q\willow\render;
use q\get;
use q\core\helper as h;

// load it up ##
\q\extension\linkedin::run();

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
            \add_filter( 'q/plugin/acf/add_field_groups/q_option_analytics', [ get_class(), 'filter_acf_fields' ], 10, 1 );

        }

    }



    public static function filter_acf_fields( $array ) 
    {

        // test ##
        // h::log( $array );

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

        // h::log( $array['fields'] );

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
        // if ( h::is_localhost() ) { 
        
        //     // h::log( 'FB pixel not added on localhost' );

        //     // return false; 
        
        // }

        // check if consent given to load script ##
        if ( ! h::consent( 'marketing' ) ) {

            // h::log( 'Marketing NOT allowed...' );

            // kick out ##
            return false;

        }

        // grab the options ##
        // $option = core\option::get();

        // h::log( $option );

        // bulk if no options found ##
        if ( 
            ! core\option::get( 'linkedin' )
        ) {

            // h::log( 'Error: Options missing...' );

            return false;

        }


        // // check if we have tag_manager defined in config ##
        // if ( ! isset( $option->linkedin ) ) {

        //     // h::log( 'linkedin Pixel not defined in config' );

        //     return false;

        // }

        // kick it back, cleanly... ##
        echo core\option::get( 'linkedin' );

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
        // if ( h::is_localhost() ) { 
                
        //     // h::log( 'Analytics skipped, as on localhost...' );

        //     // return false; 

        // }

        // check if consent given to load script ##
        if ( ! h::consent( 'marketing' ) ) {

            // h::log( 'Marketing NOT allowed...' );

            // kick out ##
            return false;

        }

        // grab the options ##
        // $option = core\option::get();

        #h::log( $option );

        // bulk if no options found ##
        if ( 
            ! core\option::get( 'linkedin_noscript' )
        ) {

            // h::log( 'Error: Options missing...' );

            return false;

        }

        // check for UI ##
        // if ( ! isset( $option->linkedin_noscript ) ) { 

        //     // Log ##
        //     // h::log( 'linkedin No Script not defined' );

        //     // kick off ##
        //     return false; 

        // }

        // kick it back, cleanly... ##
        echo core\option::get( 'linkedin_noscript' );

    }


}
