<?php

namespace q\plugin;

use q\core\core as core;
use q\core\helper as helper;

// load it up ##
\q\plugin\gravityforms::run();

class gravityforms extends \Q {

    public static function run()
    {

        // no GF ##
        if ( ! self::is_active() ){ 
            
            // helper::log( 'No GF... or called too early..' );
            
            return false; 
        
        }

        // scroll to set point on page, upon submission ##
        // \add_filter( 'gform_confirmation_anchor', array( get_class(), 'gform_confirmation_anchor' ), 10, 0 );

        // Gravity Forms spinner ##
        \add_filter( "gform_ajax_spinner_url", [ get_class(), "gform_ajax_spinner_url" ], 10, 2 );

        // move GF to footer ##
        \add_filter( "gform_init_scripts_footer", [ get_class(), "gform_init_scripts_footer" ] );

        // remove GF CSS ## - note removed, as causing problems on Docs site, can be added back in on individual site config files ##
        \add_filter( 'pre_option_rg_gforms_disable_css', '__return_true' );

        // add privacy policy to consent links ##
        // \add_filter( 'gform_submit_button', [ get_class(), 'gform_submit_button' ], 1000, 2 );

        // move upload folder outside "uploads" folder to avoid S3 losses ##
        \add_filter( 'gform_upload_path', [ get_class(), 'gform_upload_path' ], 10, 2 );

    }


    /**
     * Check if GF is installed and active 
     * 
     * @since   2.5.0
     * @return  Boolean
     */
    public static function is_active()
    {

        return function_exists( 'gravity_form' );

    }



    
    public static function gform_upload_path( $path_info, $form_id ) 
    {
    
        // define path and url to wp_content/gravityforms/ ##
        $path_info['path'] = WP_CONTENT_DIR.'/gravityforms/';
        $path_info['url'] = WP_CONTENT_URL.'/gravityforms/';
        
        // test settings ##
        // helper::log( $path_info );

        // kick it back ##
        return $path_info;
    
    }


    
    /**
    * Change Gravity Forms spinner icon
    *
    * @since       0.3
    * @return      string
    */
    public static function gform_ajax_spinner_url( $image_src, $form )
    {

        // helper::log( 'Spinner called..' );

        return helper::get( 'theme/css/images/global/ajax-loader.gif', 'return' );

    }



    /**
    * Set point to scroll to in px after Gravity Form submission
    *
    * @since       0.9
    * @return      int     Pixel value to scroll to
    */
    public static function gform_confirmation_anchor()
    {

        return ( 'handheld' == helper::get_device() ) ? 0 : 0 ;

    }



    /**
     * Append link to privacy policy to GF consent checkbox 
     * 
     * @since       2.0.1
     */
    public static function gform_submit_button( $string, $form ) {

        #helper::log( $string );

        // get privacy policy ##
        if ( $post = \get_page_by_title( 'Privacy Policy' ) ) {

            $string .= '<span class="privacy">Read our <a href="'.\get_permalink( $post ).'" target="_blank">Privacy Policy</a></span>';

            // helper::log( $string );

        }

        return $string;

    }



    /**
    * Allow GF scripts and forms to be loaded late
    *
    * @since       2.0.0
    * @return      boolean
    */
    public static function gform_init_scripts_footer()
    {

        return true;

    }

    
    /**
     * Add requires assets for Gravity Form
     *
     * @since       1.0.1
     * @return      void
     */
    public static function gform_enqueue_scripts()
    {

        // check for function ##
        if( ! function_exists( 'gravity_form_enqueue_scripts' ) ) { return false; }

        // get post gravity form ID ##
        \gravity_form_enqueue_scripts( self::gform_get_id(), true );

    }


    /**
     * Get a Garavity Form by it's title
     *
     * @since       2.5.0
     * @return      int
     */
    public static function get_by_title( $string = null )
    {

        // sanity ##
        if ( is_null( $string ) ) {

            helper::log( 'No string passed' );

            return false;

        }

        if ( ! class_exists( 'RGFormsModel' ) ) {
            
            helper::log( 'GF Class unavailable..' );

            return false;

        }

        // get ID ##
        $form_id = \RGFormsModel::get_form_id( $string );

        // kick back ##
        return $form_id;

    }




    /**
     * Render Gravity Form
     *
     * @since       1.3.0
     * @param       integer        $id     Gravity Form ID
     * @return      void
     */
    public static function render( $id = null )
    {

        // sanity check ##
        if ( is_null( $id ) ) { return false; }

        // check for function ##
        if( ! function_exists( 'gravity_form' ) ) { return false; }

        // call gravity form function ##
        \gravity_form( $id, false, false, false, '', true );

    }




    /**
    *
    *
    */
    public static function has_form( $args = null )
    {

        // sanity ##
        if ( is_null( $args ) ){

            // no chance ##
            return false;

        }

        // check if we were passed a post ##
        if ( 
            isset( $args['post'] )
            && ! is_object( $args['post'] ) 
        ) {

            $the_post = \get_post( $args['post'] );

        } else {

            $the_post = wordpress::the_post();

        }

        // check if we have a post ##
        if ( ! $the_post ) {
            
            helper::log( 'Error getting post.' );

            return false;

        }

        // if we passed as meta key - check that ##
        if ( isset( $args['meta_key'] ) ) {

            if ( \get_post_meta( $the_post->ID, $args['meta_key'], true ) ) {

                $form_id = \get_post_meta( $the_post->ID, $args['meta_key'], true );

            } elseif ( \has_shortcode( $the_post->post_content, 'gravityform' ) ) {

                // blah blah ##

            }

        }

    }



    public static function meta_gravity_form( $value = null, $array = null, $args = null  )
    {

        // check if we have markup ##
        if ( ! isset( $args['markup'] ) ) {

            helper::log( 'No markup passed.' );

            return $value;

        }

        #helper::log( 'passed value: '.$value );

        // let's try to get an attachment from the passed ID ##
        if ( 
            $value
            && $form = \gravity_form( intval( $value ), true, false, false, false, true, '', false )
        ) {

            #helper::log( $form );

            // Enqueue the scripts and styles
	        \gravity_form_enqueue_scripts( $form, true );

            // grab markup ##
            $markup = $args['markup']['all'];
            // $passed_value = $value;

            // add file size ##
            $markup = str_replace( '%'.$args['placeholder'].'%', $form, $markup );

            #helper::log( $markup );

            // swap value #
            $value = $markup;

        }

        return $value;

    }



}