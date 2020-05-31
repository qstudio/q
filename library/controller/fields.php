<?php

namespace q\controller;

use q\core\core as core;
use q\core\helper as helper;
use q\core\config as config;
use q\theme\ui as ui;
use q\controller\minifier as minifier;
use q\controller\css as css;

// load it up ##
// \q\controller\generic\generic::run();

class fields extends \Q {

    public static function run()
    {


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
        // helper::log( $array );

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

            // echo $args->wrapper['open'];
            echo \apply_filters( 'q/generic/render/open/'.$args->fields, $args->wrapper['open'] );

        }

        // format markup with translated data ##
        foreach( $data as $array ) {

            // grab the markup template ##
            // note, this could be passed as a string - single value for all devices - or as an array with a key for each device
            // fallback index is "all" ##
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

            // filter ##
            $markup = \apply_filters( 'q/generic/render/markup/'.$args->fields, $markup );

            // final check ##
            #helper::log( $markup );

            // echo markup and repeat ##
            echo $markup;

        }

        // append markup ##
        if ( isset( $args->wrapper['close'] ) ) {

            echo \apply_filters( 'q/generic/render/close/'.$args->fields, $args->wrapper['close'] );

        }

        // close tag ##
        ui::get_tag( $args->tag, '', 'close' );

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


}