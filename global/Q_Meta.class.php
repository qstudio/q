<?php

/**
 * Q Meta Class
 */

defined( 'ABSPATH' ) OR exit;

use q\q_theme\core\config as config;
use q\q_theme\theme\meta as meta;

if ( ! class_exists( 'Q_Meta' ) ) {

    class Q_Meta extends Q {

        /**
         * Get and return post meta fields
         *
         * @since       1.0.1
         * @return      string   HTML
         */
        public static function load( $args = array() )
        {

            #self::log( $args );

            // return variables ##
            $return = '';
            $tabs = array(); // nada ##

            // Parse incoming $args into an array and merge it with $defaults - caste to object ##
            $args = ( object )wp_parse_args( $args, config::$the_meta );

            #self::log( $args );

            // // check if args ##
            // if ( ! isset ( $args->group ) ) {

            //     self::log( "kicked - no group" );
            //     return false;

            // }

            // get meta fields from group name ##
            $fields = ( object ) $args->fields ;
            #pr( $fields );

            // last sanity check ##
            if ( ! $fields ) {

                #self::log( "kicked - no fields" );
                return false;

            }

            // repeater ##
            if ( 'repeater' == $fields->type ) {

                // check we have all the field data we need ##
                if (
                    ! isset ( $fields->name )
                    || ! isset( $fields->sub_fields )
                    || ! is_array( $fields->sub_fields )
                    || count( $fields->sub_fields ) == 0
                ) { //  || empty( array_filter( $fields->sub_fields )

                    #self::log( "kicked - missing config properties" );
                    return false;

                }

                // start with an empty object ##
                $object = null;

                #self::log( $args );

                if ( isset( $args->is_tax ) ) {

                    // additional logic to deal with pulling rows back from taxonomy terms ##
                    $queried_object = get_queried_object();

                    #self::log( 'is_tax' );
                    $object = $queried_object;

                } elseif ( isset( $args->post_id ) ) {

                    #self::log( 'post_id:'. $args->post_id );
                    $object = $args->post_id;

                }

                // check we have all the field data we need ##
                if ( ! have_rows( $fields->name, $object ) ) {

                    #self::log( "kicked - no rows in ". $fields->name );
                    return false;

                }

                // loop through the rows of data
                while ( have_rows( $fields->name, $object ) ) {

                    // set-up the row ##
                    the_row();

                    // counter ##
                    $count = 0;

                    // open repeater sub_field element ##
                    #$return .= Q_Theme::the_meta_html_wrap( array ( 'action' => 'sub-open', 'class' => 'panel' ) );

                    foreach ( $fields->sub_fields as $sub_field_name => $sub_field_value ) {

                        #self::log( count( $fields->sub_fields ) );

                        #self::log( $sub_field_name );
                        #self::log( $meta_value );
                        #self::log( get_sub_field( $meta_value[1] ) );

                        $sub_field = get_sub_field( $sub_field_value["name"] );

                        // add tabs ##
                        if ( isset( $sub_field_value["tab"] ) && true == $sub_field_value["tab"] ) {

                            $tabs[] = $sub_field;

                        }

                        // open repeater sub_field element ##
                        if ( 0 == $count ) {

                            $return .= meta::the_meta_html_wrap( array ( 'action' => 'sub-open', 'id' => sanitize_title( $sub_field ) ) );

                        }

                        // image type ##
                        if ( "image" == $sub_field_value["type"] && isset ( $sub_field_value["handle"] ) ) {

                            if ( $image = wp_get_attachment_image_src( $sub_field, $sub_field_value["handle"] ) ) {

                                #pr( $image );
                                $sub_field = $image[0];

                            }

                        }

                        // pass each sub field to the meta_printer ##
                        $return .= self::meta_print(
                            array(
                                'name'      => $sub_field_value["name"],
                                'type'      => $sub_field_value["type"],
                                'field'     => $sub_field,
                                'label'     => $sub_field_value["label"],
                                'loaded'    => true
                            )
                        );

                        #pr( $return );

                        // iterate ##
                        $count ++ ;

                        if ( $count == count( $fields->sub_fields ) ) {

                            // close sub ##
                            $return .= meta::the_meta_html_wrap( array ( 'action' => 'sub-close' ) );

                        }

                    }

                    // close sub ##
                    #$return .= meta::the_meta_html_wrap( array ( 'action' => 'sub-close' ) );

                }

            // single field ##
            } else {

                // check we have all the field data we need ##
                if ( ! isset ( $fields->name ) ) {

                    #self::log( "kicked - missing properties" );
                    return false;

                }

                #pr( "group of single fields" );

                foreach ( $fields->fields as $field_name => $field_value ) {

                    #$return .= self::meta_print( $meta_key, $meta_value[0], $sub_field, $meta_value[2], true );
                    $return .= self::meta_print(
                        array(
                            'name'      => $field_name,
                            'type'      => $field_value["type"],
                            'field'     => $field_value["field"],
                            'label'     => $field_value["label"],
                            'post'      => isset( $args->post ) ? $args->post : false
                        )
                    );

                }


            }

            // if we've got some results - let's wrap it up in mark-up ##
            if ( $return ) {

                // tabbed layout ##
                if ( "tabs" == $args->layout ) {

                    // grab tabs ##
                    $tabs_html = self::meta_tabs( $tabs );

                    // all for side and top tabs ##
                    $wrapper_class = ( "side" == $args->tabs ) ? 'tabs tabs-side meta-'.$fields->name : 'tabs meta-'.$fields->name ;

                    // open wrapper ##
                    $wrapper_open = meta::the_meta_html_wrap( array ( 'action' => 'wrapper-open', 'class' => $wrapper_class ) );

                    // parent open ##
                    $parent_open = meta::the_meta_html_wrap( array ( 'action' => 'parent-open', 'class' => 'tabs-container q-tabs' ) );

                    // close parent ##
                    $parent_close = meta::the_meta_html_wrap( array ( 'action' => 'parent-close' ) );

                    // close wrapper ##
                    $wrapper_close = meta::the_meta_html_wrap( array ( 'action' => 'wrapper-close' ) );

                    // compile ##
                    $return = $wrapper_open.$tabs_html.$parent_open.$return.$parent_close.$wrapper_close;

                // standard layout ##
                } else {

                    // open wrapper ##
                    $wrapper_open = meta::the_meta_html_wrap( array ( 'action' => 'wrapper-open', 'class' => 'meta meta-'.$fields->name, 'tag' => 'ul' ) );

                    // close wrapper ##
                    $wrapper_close = meta::the_meta_html_wrap( array ( 'action' => 'wrapper-close', 'tag' => 'ul' ) );

                    // compile ##
                    $return = $wrapper_open.$return.$wrapper_close;

                }

            }

            // echo the $return ##
            echo $return;

        }



        /**
         * Get and return post meta fields
         *
         * @since       1.0.1
         * @return      string   HTML
         */
        public static function meta_print( $args = array() )
        {

            // Parse incoming $args into an array and merge it with $defaults - caste to object ##
            $args = ( object )wp_parse_args( $args );

            // check if group sent ##
            #pr( $args->name );
            #pr( $args->field );
            if ( ! isset ( $args->name ) || ! isset ( $args->type ) || ! isset ( $args->field ) || ! isset ( $args->label ) ) {

                #self::log( "kicked - missing properties 2" );
                return false;

            }

            // field value passed already ##
            if ( isset( $args->loaded ) && true === $args->loaded && isset( $args->field ) ) {

                #self::log( "field value loaded already" );
                $post_field = $args->field;

            } else {

                // grab global post ##
                #global $post;

                // sanity check ##
                #self::log( $args );
                if ( ! $post = Q_Control::the_post( array( 'post' => $args->post ) ) ) {

                    #self::log( "kicked - no post" );
                    return false;

                }

                // assign value magically ##
                $field = $args->field;
                $post_field = $post->$field; ##
                #if ( $post_field ) self::log( 'Post meta for: '.$field.' returned: '.$post_field );

            }

            // check meta isset and not empty ##
            if ( ! isset( $post_field ) || empty ( $post_field ) ) {

                #self::log( 'Post ID: '.$post->ID );
                $post_field = get_post_meta( $post->ID, $field, true );
                #self::log( 'Post meta for: '.$field.' returned: '.$post_field );

            }

            // check meta isset and not empty ##
            if ( ! isset( $post_field ) || empty ( $post_field ) ) {

                #self::log( "kicked - meta empty" );
                return false;

            }

            // some tests ##
            #pr( $type );
            #pr( $field );
            #pr( $post_field );

            //switch over types ##
            switch ( $args->type ) {

                // flags, saved in a comma seperated list ##
                // need to decide how these are presented ( etc. icons, text ) ##
                case 'flag':

                    $post_fields = is_array( $post_field ) ? $post_field : ( array )$post_field;
                    $post_field = ''; // empty string ##

                    // wrap each flag in an <img> tag ##
                    foreach ( $post_fields as $flag ) {

                        if ( file_exists( q_get_option("path_parent").'library/images/flags/icon-'.$flag.'.png' ) ) {

                            $post_field .= '<img src="'.q_get_option("uri_parent").'library/images/flags/icon-'.$flag.'.png" />';

                        }

                    }

                    $args->value = ( $post_field );
                    return meta::the_meta_html ( $args );

                break;

                // phone ##
                case 'phone':

                    $args->value = ( $post_field );
                    return meta::the_meta_html ( $args );

                break;

                // email ##
                case 'email':

                    if ( ! is_email( $post_field ) ) { return false; }

                    $args->value = '<a href="mailto:'.antispambot( $post_field ).'">'.antispambot( $post_field ).'</a>';
                    return meta::the_meta_html ( $args );

                break;

                // url ##
                case 'url':

                    #pr( $post_field );
                    $args->value = '<a href="'.q_add_http($post_field).'" target="_blank">'.str_replace( array( "http://", "https://", "www." ), "", $post_field ).'</a>';
                    return meta::the_meta_html ( $args );

                break;

                // url ##
                case 'radio':
                case 'select':

                    $field = get_field_object( $args->field );
                    $value = get_field( $args->field );
                    $args->value = $field['choices'][ $value ];
                    return meta::the_meta_html ( $args );

                break;

                // boolean ##
                case 'boolean':

                    $args->value = $post_field == 1 ? __( "Yes", self::text_domain ) : __( "No", self::text_domain );
                    return meta::the_meta_html ( $args );

                break;

                // Google Map ##
                case 'map':

                    #pr( 'key: : '.$key );
                    #pr( $field );
                    $args->value = implode(', ', array_map(function ($v, $k) { return sprintf("%s='%s'", $k, $v); }, $post_field, array_keys( $post_field ) ) );
                    return meta::the_meta_html ( $args );

                break;

                // Legacy Location Date ##
                case 'location':

                    #pr( 'key: : '.$key );
                    #pr( $field );
                    $explode = explode( '|', $post_field );
                    $args->value = $explode[0];
                    return meta::the_meta_html ( $args );

                break;

                // image ##
                case 'image':

                    #pr( 'key: '.$key );
                    #pr( $post_field );
                    $args->value = '<img src="'.esc_html( $post_field ).'" />';
                    return meta::the_meta_html ( $args );

                break;

                // textarea ##
                case 'textarea':

                    $args->value = wpautop( $post_field );
                    return meta::the_meta_html ( $args );

                break;

                // commit message ##
                case 'commit_message':

                    $args->value = '<ul><li>' . str_replace( "\n", '</li><li>', $post_field ) . '</li></ul>';
                    #$args->value = $post_field; // add WP style commit tags ##
                    return meta::the_meta_html ( $args );

                break;

                // commit title ##
                case 'commit_version':

                    $args->value = '= '.$post_field.' ='; // add WP style commit tags ##
                    return meta::the_meta_html ( $args );

                break;

                // tab ##
                case 'tab' :

                    $args->value = "<a href='#".sanitize_title( $post_field )."' class='faq-tab'>".$post_field."</a>";
                    return meta::the_meta_html ( $args );

                break;

                // oembed ##
                case 'oembed':

                    #pr( "text.." );
                    #pr( $field );
                    $args->value = wp_oembed_get( $post_field );
                    return meta::the_meta_html ( $args );

                break;

                // text and default ##
                case 'text':
                default:

                    #pr( "text.." );
                    #pr( $field );
                    $args->value = ( $post_field );
                    return meta::the_meta_html ( $args );

                break;

            }

        }



        /**
         * Build simple tab nav list
         *
         * @param type $args
         */
        public static function meta_tabs( $tabs = array() )
        {

            // sanity check ##
            if ( ! is_array( $tabs ) || count( $tabs ) == 0 ) { return ''; }

            // counter ##
            $counter = 1;
            # class='nav-{$counter}'

            #pr( $tabs );

            // built return ##
            $return = "<ul class='tabs-options'>";

            #$current = ( 1 == $counter ) ? " class='current'" : '' ; {$current}
            #".strtolower($tab)."

            // loop over tabs ##
            foreach ( $tabs as $tab ) {

                // add tab ##
                $return .= "<li class='tab'><a href='#".sanitize_title($tab)."'>{$tab}</a></li>";

                // iterate ##
                $counter ++ ;

            }

            // close tabs ##
            $return .= "</ul>";

            // return it ##
            return $return;

        }



        /**
         * Get a list of the allowed fields types for meta entries
         *
         * @since       1.0.1
         * @return      Object   Meta types
         */
        public static function meta_allowed_fields()
        {

            // set-up new object ##
            return apply_filters( 'meta_allowed_fields', array(

                        'text'
                    ,   'textarea'
                    ,   'image'
                    ,   'url'
                    ,   'radio'
                    ,   'select'
                    ,   'repeater'
                    ,   'map'
                    ,   'oembed'

                ) 
            );

        }



    }

}