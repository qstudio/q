<?php

namespace q\controller;

use q\core\core as core;
use q\core\helper as helper;
use q\core\config as config;
use q\theme\ui as ui;
// use q\q_theme\core\options as options;
// use q\q_theme\theme\template as template; // @todo -- what ??
use q\controller\minifier as minifier;
use q\controller\css as css;

// load it up ##
// \q\controller\generic\generic::run();

class generic extends \Q {

    public static function run()
    {

        // CORS header ##
        // \add_action( 'init', [ get_class(), 'add_cors_http_header' ] );

    }




    /**
     * Generic post meta renderer
     *
     * @since        2.0.0
     */
    public static function render( $args = null )
    {

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object )\wp_parse_args( $args, \q_theme::$the_render );

        $args->post = isset( $args->post ) ? $args->post : \get_the_ID(); // allows for passing a post ID, defaults to current ##

        // grab global post ##
        if (
            // ! $the_post = wordpress::the_post()
            // || 
            is_null( $args )
            || ! isset( $args->markup )
        ) {

            helper::log( 'Kicked early..' );

            // nothing found ##
            return false;

        }

        // check if field is enabled ##
        if (
            false == self::is_enabled( $args )['enabled']
        ){

            helper::log( "kicked - field group disabled in admin..." );

            #helper::log( self::is_enabled( $args ) );

            echo \wpautop( self::is_enabled( $args )['message'] );

            return false;

        }

        // helper::log( '$args->post: '. $args->post );

        // meta taxonomy query ##
        if (
            // \is_tax() 
            // && 
            // and we did not override, by passing a post ID ##
            (
                // isset( $args->post )
                // && 
                'is_tax' == $args->post
            )
            && function_exists( 'get_field' )
            && \get_queried_object()
        ) {

            // helper::log( 'is_tax()' );

            if (
            ! $array = \get_field( $args->fields, \get_queried_object()  )
            ){

                helper::log( 'ACF returned no fields for taxonomy fields: '.$args->fields );

                return false;

            }

        } else {

            // helper::log( $the_post );

            // define the post ##
            $post =
                isset( $args->post ) ?
                    $args->post :
                    wordpress::the_post() ;

            // helper::log( $post );
            // helper::log( \get_field( $args->fields, $post ) );

            //  get value stored for selected repeater row from ACF ##
            if (
            ! $array = \get_field( $args->fields, $post )
            ){

                helper::log( 'ACF returned no fields for post_meta: '.$args->fields );

                return false;

            }

        }

        // check ##
        #helper::log( $array );

        // check for custom handler ##
        if (
            ! method_exists( $args->view, $args->method )
            || ! is_callable( array( $args->view, $args->method ) )
        ){

            helper::log( 'handler wrong - class:'.$args->view.' / method: '.$args->method );

            return false;

        }

        // call class method and pass arguments ##
        $data = call_user_func_array (
            array( $args->view, $args->method )
            ,   array( $array, $args )
        );

        if ( ! $data ) {

            helper::log( 'Handler method returned bad data..' );

            return false;

        }

        #helper::log( 'data returned to generic:render():' );
        #helper::log( $data );

        // open tag ##
        ui::get_tag( $args->tag, ( is_array( $args->class ) ? $args->class : array ( $args->class ) ) );

        // prefix markup ##
        if ( isset( $args->wrapper['open'] ) ) {

            echo $args->wrapper['open'];

        }

        // format markup with translated data ##
        foreach( $data as $array ) {

            // grab the markup template ##
            // note, this could be passed as a string - single value for all devices - or as an array with a key for each device - fallback index is "all" ##
            if (
            is_array( $args->markup )
            ){

                // check if device key exists ##
                if ( isset( $args->markup[helper::get_device()] ) ){

                    // helper::log( 'returning markup for device: '.helper::get_device() );

                    $markup = $args->markup[helper::get_device()];

                } elseif ( isset( $args->markup['all'] ) ){

                    // helper::log( 'Using "all" device markup' );

                    $markup = $args->markup['all'];

                } else {

                    // helper::log( 'No usable markup found, returning nada..' );

                    return false;

                }

            } else {

                // take string value ##
                $markup = $args->markup;

            }

            #helper::log( 'original markup: ' );
            #helper::log( $markup );

            foreach( $array as $key => $value ) {

                #helper::log( 'working key: '.$key.' with value: '.$value );

                // template replacement ##
                $markup = str_replace( '%'.$key.'%', $value, $markup );

            }

            // final check ##
            #helper::log( $markup );

            // echo markup and repeat ##
            echo $markup;

        }

        // append markup ##
        if ( isset( $args->wrapper['close'] ) ) {

            echo $args->wrapper['close'];

        }

        // close tag ##
        ui::get_tag( $args->tag, '', 'close' );

    }





    /**
     * Markup object based on %placeholders% and template
     *
     * @since    2.0.0
     * @return   Mixed
     */
    public static function markup( $markup = null, $data = null )
    {

        // sanity ##
        if (
            is_null( $markup )
            || is_null( $data )
            ||
            (
                ! is_array( $data )
                && ! is_object( $data )
            )
        ) {

            helper::log( 'missing parameters' );

            return false;

        }

        #helper::log( $data );
        #helper::log( $markup ); 

        // grab markup ##
        $return = $markup;

        // format markup with translated data ##
        foreach( $data as $key => $value ) {

            #helper::log( 'key: '.$key.' / value: '.$value );

            // only replace keys found in markup ##
            if ( false === strpos( $markup, '%'.$key.'%' ) ) {

                #helper::log( 'skipping '.$key );

                continue ;

            }

            // template replacement ##
            $return = str_replace( '%'.$key.'%', $value, $return );

        }

        #helper::log( $return );

        // return markup ##
        return $return;

    }






    public static function is_enabled( $args = null )
    {

        // default ##
        $array = array(
            'enabled'   => '1',
            'message'   => isset( $args->enable['message'] ) ? $args->enable['message'] : false
        );

        // check we have all the field data we need ##
        if (
        isset( $args->enable ) // function set to enable disabling ##
        ) {

            // convert legacy format to array ##
            if ( ! is_array( $args->enable ) ) {

                // store value ##
                $store = $args->enable;

                $args->enable = array();
                $args->enable['trigger'] = $store;

            }

            #helper::log( "checking enable status of group: ".$args->fields );
            #helper::log( "post ID: ".get_the_ID() );

            $value = \get_field( $args->enable['trigger'], get_the_ID() );

            #helper::log( "we got the enable value: ".$value );
            #helper::log(  $args->enable );

            if ( '0' == $value ) {

                #helper::log( "Group is not enabled: ".$args->fields );

                $array['enabled'] = '0';

            }

        }

        #helper::log( $array );

        // default to yes ##
        return $array;

    }





    public static function minify( $string = null, $type = 'js' )
    {

        // if debugging, do not minify ##
        if ( self::$debug ) {

            return $string;

        }

        switch ( $type ) {

            case "css" :

                $string = minifier::css( $string );

                break ;

            case "js" :
            default :

                $string = minifier::javascript( $string );

                break ;

        }

        // kick back ##
        return $string;

    }




    /** 
     * Check which consent is given by the user
     * 
     * 
     * */
    public static function consent( $setting = null )
    {

        if ( is_null( $setting ) ) {

            // helper::log( 'No setting passed, default to true.' );

            return true;

        }

        if ( 
            ! class_exists( '\q\consent\core\cookie' )
        ) {

            // helper::log( 'Consent Class not found, defalt to true' );

            // no ##
            return true;

        }

        if (
            ! \q\consent\core\cookie::is_active( $setting ) 
        ) {

            // helper::log( 'Setting not allowed: '.$setting );

            // no ##
            return false;

        }

        // helper::log( 'Setting allowed: '.$setting );

        // ok ##
        return true;

    }




    /**
     * Format passed date value
     *
     * @since   2.0.0
     * @return  Mixed String
     */
    public static function date( $array = null ){

        // test ##
        #helper::log( $array );

        // did we pass anything ##
        if ( ! $array ) {

            #helper::log( 'kicked 1' );

            return false;

        }

        $return = false;

        // loop over array of date options ##
        foreach( $array as $key => $value ) {

            #helper::log( $value );

            // nothing happening ? ##
            if ( false === $value['date'] ) {

                #helper::log( 'kicked 2' );

                continue;

            }

            if ( 'end' == $key ) {

                // helper::log( 'Formatting end date: '.$value['date'] );

                // if start date and end date are the same, we need to just return the start date and start - end times ##
                if (
                    // $array['start']['date'] == $array['end']['date']
                    date( $value['format'], strtotime( $array['start']['date'] ) ) == date( $value['format'], strtotime( $array['end']['date'] ) )
                ) {

                    // helper::log( 'Start and end dates match, return time' );

                    // use end date ##
                    $date = ' '.date( 'g:i:a', strtotime( $array['start']['date'] ) ) .' - '. date( 'g:i:a', strtotime( $array['end']['date'] ) );

                } else {

                    // helper::log( 'Start and end dates do not match..' );

                    // use end date ##
                    $date = ' - '.date( $value['format'], strtotime( $value['date'] ) );

                }

            } else {

                // helper::log( 'Formatting start date' );

                $date = date( $value['format'], strtotime( $value['date'] ) );

            }

            // add item ##
            $return .= $date;
            #false === $return ?
            #$date :
            #$date ;

        }

        // kick it back ##
        return $return;

    }



    /**
     * Truncate text to a fixed length
     *
     * @since       0.4
     * @return      Mixed       String is content passed OR boolean false if not
     */
    public static function chop( $content = null, $length = 0, $prepend = '...' )
    {

        // nothing to chop ##
        if ( is_null( $content ) ) { return false; }

        // trim required, perhaps ##
        if ( $length > 0 ) {

            if ( strlen( $content ) > $length ) { // long so chop ##

                return substr( $content , 0, $length ) . $prepend;

            } else { // no chop ##

                return $content;

            }

        } else { // send as is ##

            return $content;

        }

    }




    public static function add_cors_http_header(){

        // club login status ##
        if ( core::is_site( "club" ) ) {

            return false;

        }

        header( "Access-Control-Allow-Origin: ".\get_site_url( '2', '/', 'https' ) );

    }


}