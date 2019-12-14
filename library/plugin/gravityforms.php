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

        // add universale filter on Auth.net transactions, adding descirption field to transaction object ##
        \add_filter('gform_authorizenet_transaction_pre_capture', [ get_class(), 'gform_authorizenet_transaction_pre_capture' ], 10, 5 );

        // Gravity Forms spinner ##
        \add_filter( "gform_ajax_spinner_url", [ get_class(), "gform_ajax_spinner_url" ], 10, 2 );

        // move GF to footer ##
        \add_filter( "gform_init_scripts_footer", [ get_class(), "gform_init_scripts_footer" ] );

        // Gravity Form default form settings ##
        // \add_filter( "gform_pre_render", array( get_class(), "gform_pre_render" ) );

        // fix tab index problem -- REVIEW ##
        // \add_filter( "gform_tabindex", function() { return 4; });

        // remove GF CSS ## - note removed, as causing problems on Docs site, can be added back in on individual site config files ##
        // \add_filter( 'pre_option_rg_gforms_disable_css', '__return_true' );

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



    /**
     * Check if GF is installed and active 
     * 
     * @since   2.5.0
     * @return  Boolean
     */
    public static function get_form_object( $form_id = null )
    {

        // sanity ##
        if ( is_null( $form_id ) ) {

            helper::log( 'No form_id passed' );

            return false;

        }

        if ( ! class_exists( 'GFAPI' ) ) {
            
            helper::log( 'GFAPI Class unavailable..' );

            return false;

        }

        // kick back what we find ##
        return \GFAPI::get_form( $form_id );
        
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
     * Append form data to description field
     * 
     * Format - Title: XXX | Invoices: 588, CAP, CP19-3475, CP-1903477 | Cust ID: 588 | Pax Name: Null | Pax ID: Null
     * Inputs - Form_Name: Form ID 9 , Form ID 13, Form ID 10 or 14, Form ID 11 or nothing, Form ID 12 or nothing
     * 
     * @method      https://docs.gravityforms.com/gform_authorizenet_transaction_pre_capture/
     * @uses        rgar - https://docs.gravityforms.com/rgar/
     */
    public static function gform_authorizenet_transaction_pre_capture( $transaction, $form_data, $config, $form, $entry ) 
    {
        
        // default ##
        $string = '';
        $delimit = ': '; // key : value ##
        $break = ' | '; // pair breaker ##

        // check if we have a form and form title ##
        $title = 'Form'.$delimit. 
            ( $form && isset( $form['title'] ) ) ? 
            $form['title'] : 
            'Error' ;
            
        // paying who - form ID 9 ##
        $paying = 'Paying'.$delimit.\rgar( $entry, '9', 'Error' );

        // which program - form ID 13 ##
        $program = 'Program'.$delimit.\rgar( $entry, '13', 'Error' ); 

        // check for invoices, might be a single or multiple values, seperated by comma ##
        if ( ! is_null( \rgar( $entry, '10' ) ) ) {

            $invoice = \rgar( $entry, '10' ); // single ##

        } else if ( ! is_null( \rgar( $entry, '14' ) ) ) {

            $invoice = \rgar( $entry, '14' ); // multiple -- risky format, as user-entered ##

        } else {

            $invoice = 'Error';

        }
        // concat ##
        $invoices = 'Invoices'.$delimit.$invoice; 

        // pax name - form ID 11 ##
        $pax_name = 'Pax Name'.$delimit.\rgar( $entry, '11', 'Error' ); 

        // which program - form ID 13 ##
        $pax_id = 'Pax ID'.$delimit.\rgar( $entry, '12', 'Error' ); 

        // concat values ##
        $string = 
            $title.$break. // Form Title ##
            $paying.$break. // Paying who ##
            $program.$break. // Program ##
            $invoices.$break. // Invoices ##
            $pax_name.$break. // Pax Name ##
            $pax_id; // Pax ID ##

        // add description ##
        $transaction->description = $string;

        // debug ##
        helper::log( 'Auth.net $transaction->description: ---------------' );
        helper::log( $transaction->description );

        // kick it back ##
        return $transaction;

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
     * Set default form settings for all Gravity Forms
     *
     * @since       1.4.2
     * @return      Array   $form;
     */
    public static function gform_pre_render( $form )
    {

        #self::log( $form['notifications'] );

        // define form settings ##
        $form['cssClass'] = 'gf-add-placeholder';
        $form['descriptionPlacement'] = 'above';
        $form['enableHoneypot'] = 0;

        // kick it back ##
        return $form;

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
    * Check if a post has a form assigned in post_meta
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



    

    /**
     * Get all GF entries by search criteria 
     * 
     * @link    https://docs.gravityforms.com/api-functions/#get-entries
     */
    public static function get_entries( Array $args = null )
    {

        // we need GF classes to do aynthing, so check ##
        if ( ! class_exists( 'GFAPI' ) ) {

            helper::log( 'GFAPI Missing, so unable to continue' );

            return false;

        }

        // check we have criteria ##
        if ( 
            is_null( $args )
            || ! is_array( $args )
            // || ! isset( $args['form'] )
            // || ! isset( $args['criteria'] )
        ){

            helper::log( 'Error in passed parameters.' );

            return false;

        }

        // some default values ##
        $form = isset( $args['form'] ) ? $args['form'] : 0 ;
        $criteria = isset( $args['criteria'] ) ? $args['criteria'] : [] ;
        $sorting = isset( $args['sorting'] ) ? $args['sorting'] : [] ;
        $paging = isset( $args['paging'] ) ? $args['paging'] : array( 'offset' => 0, 'page_size' => 5 ) ; // limit to 5 ##
        $sorting = isset( $args['count'] ) ? $args['count'] : 0 ; // return total count ##

        // run query ##
        $get = \GFAPI::get_entries( 
            $form, 
            $criteria,
            $sorting,
            $paging,
            $count
        );

        // check what we got ##
        if (
            ! $get
        ){

            helper::log( 'Search returned nothing..' );

            return false;

        }

        // check what we got ##
        if (
            \is_wp_error( $get )
        ){

            helper::log( $get->get_error_message() );

            return false;

        }

        // check what we have ##
        // helper::log( $get );

        // kick it back ##
        return $get; 

    }




    /**
     * Get one GF entry by id 
     * 
     * @link    https://docs.gravityforms.com/api-functions/#get-entry
     */
    public static function get_entry( $id = null )
    {

        // we need GF classes to do aynthing, so check ##
        if ( ! class_exists( 'GFAPI' ) ) {

            helper::log( 'GFAPI Missing, so unable to continue' );

            return false;

        }

        // sanity ##
        if ( is_null( $id ) ) {

            helper::log( 'No Entry id passed to method' );

            return false;

        }

        // run query ##
        $get = \GFAPI::get_entry( 
            $id
        );

        // check what we got ##
        if (
            ! $get
        ){

            helper::log( 'Search returned nothing..' );

            return false;

        }

        // check what we got ##
        if (
            \is_wp_error( $get )
        ){

            helper::log( $get->get_error_message() );

            return false;

        }

        // check what we have ##
        // helper::log( $get );

        // kick it back ##
        return $get; 

    }



    /**
     * Get all GF entries by search criteria 
     * 
     * @link    https://docs.gravityforms.com/api-functions/#get-entries
     */
    public static function update_entry( $entry = null )
    {

        // we need GF classes to do aynthing, so check ##
        if ( ! class_exists( 'GFAPI' ) ) {

            helper::log( 'GFAPI Missing, so unable to continue' );

            return false;

        }

        // sanity ##
        if ( is_null( $entry ) ) {

            helper::log( 'No Entry object passed to method' );

            return false;

        }

        // run query ##
        $update = \GFAPI::update_entry( $entry );

        // check what we got ##
        if (
            ! $update
        ){

            helper::log( 'Failed to update entry..' );

            return false;

        }

        // check what we got ##
        if (
            \is_wp_error( $update )
        ){

            helper::log( $update->get_error_message() );

            return false;

        }

        // check what we have ##
        helper::log( 'GFAPI::update_entry: '.$update );

        // kick it back ##
        return $update; 

    }
    


    /**
     * Get Gravity Forms form entry ID from label
     *
     * @label       String      $label      The label to find the ID for
     * @since       0.5
     * @return      void
     */
    public static function get_id_from_label( $label = null, $form = null )
    {

        // sanity check ##
        if ( 
            is_null( $label )
            // || is_null( $entry )
            || is_null( $form )
        ) {

            helper::log( 'Missing parameters' );

            return false;

        }

        #helper::log( $label, 'Label' );
        #helper::log( self::$gf_form["fields"] );

        foreach ( $form['fields'] as $field ) {

            #helper::log( $field['label'], 'Field Label' );

            if ( $label == $field['label'] ) {

                // return ID ##
                #helper::log( $field, 'Form Field' );
                #helper::log( $field['id'], 'Form Entry ID' );
                return $field['id'];

            }

        }

        helper::log( 'Nothing found...' );

        // nothing cooking ##
        return false;

    }



    




    /**
     * Return the checked value from a checkbox or multichoice options
     *
     * @param		int			$field_id	ID of Gravity Forms field element
     *
     * @since		1.4
     * @return		String		selected value or empty on failure
     */
    public static function get_field_value( $field_id = null, $entry = null, $form = null )
    {

        // sanity check ##
        if ( 
            is_null ( $field_id ) 
        ){

            helper::log( 'Missing field_id' );

            return false;

        }

        // check the $lead ##
        if ( 
            is_null ( $entry ) 
            || is_null ( $form ) 
        ){

            helper::log( 'Missing entry or form object' );

            return false;

        }

        // check for required class ##
        if ( 
            ! class_exists( 'RGFormsModel' ) 
            || ! class_exists( 'GFCommon' ) 
        ) {

            helper::log( 'RGFormsModel Class missing' );

            return false;

        }

        #helper::log( $entry["id"] );
        #helper::log( $form );

        // grab field data ##
        $field = \GFFormsModel::get_field( $form, $field_id );
        #helper::log( $field );

        // grab field values ##
        $field_values = is_object( $field ) ? $field->get_value_export( $entry ) : false;

        #wp_die( pr( $field_values ) );

        // kick it back ##
        return $field_values;

    }




}