<?php

namespace q\theme;

use q\core\core as core;
use q\core\helper as helper;
// use q\core\config as config;

// load it up ##
#\q\q_theme\theme\frontpage::run();

class meta extends \Q {

    // count meta items - empty or not, to allow for $args->markup ##
    static 
        $count = 0,
        $markup = array(),
        $markup_field = array(),
        $markup_tracker = array(),
        $markup_group = '',
        $pre_filter = false, // presume that all values are not pre_filter ##
        $filtered = false,
        $return = false,
        $logger = false
        ;


    public static function log( $log = null, $write = false )
    {

        // add array item ##
        if ( ! is_null ( $log ) ) self::$logger[] = $log;

        if ( $write ) {

            helper::log( self::$logger );

        }

    }


    /**
    * Get and return post meta fields from array exported from ACF
    *
    * @since       1.0.1
    * @return      string   HTML
    */
    public static function render( $args = array() )
    {

        // clear variables ##
        self::reset();

        // empty string
        $return = '';

        // Parse incoming $args into an array and merge it with $defaults ##
        $args = \wp_parse_args( $args, \q_theme::$the_meta );

        #self::log( $args );

        // last sanity check ##
        if ( 
            ! $args['fields'] 
            || ! is_array( $args['fields'] )
        ) {

            self::log( "kicked - no fields" );

            return false;

        }


        if ( 
            false == self::is_enabled( $args )['enabled']
        ){

            // helper::log( "kicked - field group disabled in admin..." );

            // helper::log( self::is_enabled( $args ) );

            echo \wpautop( self::is_enabled( $args )['message'] );

            return false;

        }       

        // pass all the field data to the worker ##
        $return .= self::field_worker( $args['fields'], $args );

        // helper::log( 'this is what we got...' );
        // helper::log( $return );

        // if we've got some results - let's wrap it up in mark-up ##
        if ( $return ) {
            
            // get class name ##
            $class = 
                is_array( $args['class'] ) ? 
                implode( ' ', $args['class'] ) : 
                $args['class'] ;

            // define class ##
            $class .= 'meta meta-'.$args['group'].' '.$class;

            // open wrapper ##
            $wrapper_open = self::wrapper( array ( 
                'action' => 'wrapper-open', 
                'class' => 'meta meta-'.$args['group'].' '.( is_array( $args['class'] ) ? implode( ' ', $args['class'] ) : $args['class'] ), 
                'tag' => 'div' //NOTE - I changed the default wrapper from ul to div - BEN
            ) );

            // close wrapper ##
            $wrapper_close = self::wrapper( array ( 
                'action' => 'wrapper-close', 
                'tag' => 'div' 
            ) );

            // compile ##
            $return = $wrapper_open.$return.$wrapper_close;

        }

        // log it all ##
        // self::log( '', true );

        // echo the $return ##
        if ( 
            isset( $args['return'] )
            && 'return' == $args['return'] 
        ){

            // helper::log( 'returning meta..' );

            // return it ##
            return $return;

        } 
        
        // else - echo ##
        echo $return;

        // stop here ##
        return true;

    }




    public static function field_worker( $fields = null, $args = null )
    {

        if ( 
            is_null( $args ) 
            || is_null( $fields )
            || ! is_array( $fields ) 
        ){

           #self::log( 'BAD field data passed' );

            return false;

        }

        // holder ##
        #self::$return = '';

        // helper::log( isset( $args['post'] ) ? $args['post'] : '1- No post passed...' );

        // loop-over fields ##
        foreach( $fields as $key => $field ) {

            // count ##
            self::$count ++ ;

            // helper::log( $args['skip'] );

            // skip ? ##
            if ( 
                isset( $args['skip'] )
                && is_array( $args['skip'] )
                && in_array( $field['name'], $args['skip'] )
            ) {

                // helper::log( 'Skipping: '.$field['name'] );

                continue;

            }

            // log ##
            #self::log( 'Field: '.$field['name']. ' - Type: '.$field['type'] );

            // grab original markup template and add to static property ##
            #self::markup_template( $field, $args );

            // define markup field to find and replace for single field ##
            #self::markup_field( $field, $args );

            // repeater ##
            if ( 
                'repeater' == $field['type'] 
            ) { 

                // helper::log( 'is repeater...' );

                // grab original markup template and add to static property ##
                #self::markup_template( $field, $args );

                // pass to repeater ##
                #self::repeater( $field, $args );

                // return false;

            // single field in group ##
            } else {

                // helper::log( 'WP ID: '.\get_the_ID() );
                // helper::log( isset( $args['post'] ) ? $args['post'] : '2- No post passed...' );

                // build args ##
                $array = array(
                    'name'      => $field['name'],
                    'type'      => $field["type"],
                    'label'     => $field["label"],
                    'post'      => isset( $args['post'] ) ? $args['post'] : \get_the_ID(), // allows for passing a post ID, defaults to current ##
                    'required'  => $field['required']
                );

                // grab original markup template and add to static property ##
                self::markup_template( $field, $args );

                // define markup field to find and replace for single field ##
                self::markup_field( $array, $args );

                // send for formatting ##
                self::format_value( $array, $args );

            }

        }

        self::log( 'renderer called here: 1' );

        // add completed marked-up field groups ##
        self::markup_render();

        // kick it back ##
        if ( 
            isset( self::$return ) 
            && ! is_null( self::$return )
        ) {

            #self::log( 'returning markup:' );
            #self::log( self::$return );

            return self::$return ;

        } else {

            self::log( 'failed to return markup...' );
            return false;

        }

    }



    public static function repeater( $field = null, $args = null )
    {

        // check we have all the field data we need ##
        if (
            ! isset ( $field['name'] )
            || ! isset( $field['sub_fields'] )
            || ! is_array( $field['sub_fields'] )
            || count( $field['sub_fields'] ) == 0
        ) { 

           #self::log( "kicked - missing \q_theme properties:" );
            
            return false;

        }

        // start with an empty object ##
        $object = null;

        if ( isset( $args->is_tax ) ) {

            // additional logic to deal with pulling rows back from taxonomy terms ##
            $queried_object = \get_queried_object();

            #self::log( 'is_tax' );
            $object = $queried_object;

        } elseif ( isset( $args->post_id ) ) {

            #self::log( 'post_id:'. $args->post_id );
            $object = $args->post_id;

        }

        // check we have all the field data we need ##
        if ( ! \have_rows( $field['name'], $object ) ) {

            #self::log( "kicked - no rows in ". $field['name'] );
            
            return false;

        }

        // loop through the rows of data
        while ( \have_rows( $field['name'], $object ) ) {

            // set-up the row ##
            \the_row();

            // counter ##
            $count = 0;

            // returner ##
            #$open = '';
            #$return = '';
            #$close = '';

            // grab original markup template and define group ##
            #self::markup_template( $field, $args );

            foreach ( $field['sub_fields'] as $sub_field_name => $sub_field_value ) {

                // grab value ##
                $sub_field_value['value'] = 
                    #is_array( \get_sub_field( $sub_field_value["name"], $object ) ) ? // if it's an array - juggle it up ##
                    #\get_sub_field( $sub_field_value["name"], $object )[0] :
                    \get_sub_field( $sub_field_value["name"], $object ) ;

                // open repeater sub_field element ##
                if ( 
                    0 == $count 
                ) {

                    self::log( 'opening sub html at: '.$sub_field_value["name"].' for: '.$sub_field_value['value'] );

                    $data_select = 
                        ! is_array( $sub_field_value['value'] ) ? 
                        strtolower( \sanitize_file_name( $sub_field_value['value'] ) ) :
                        false ;

                    self::$return .= self::wrapper( 
                        array ( 
                            'action'        => 'sub-open', 
                            'id'            => 'meta-'.\sanitize_title( $sub_field_value["name"] ), 
                            'class'         => 'meta meta-'.$field['key'],
                            'data'          => array(
                                'select'    => $data_select
                            )
                        ) 
                    );

                }

                // sub repeater ##
                if ( 'repeater' == $sub_field_value['type'] ) {

                    self::log( 'resending repeater field: '.$sub_field_value['name'] );
                    #self::log( $sub_field_value['sub_fields'] );

                    // grab original markup template and define group ##
                    self::markup_template( $sub_field_value, $args );

                    // define markup item to find and replace ##
                    self::markup_field( $sub_field_value, $args );

                    // return it to repeater ##
                    self::repeater( $sub_field_value, $args );

                } else {

                    // build array ##
                    $array = array(
                        'name'      => $sub_field_value["name"],
                        'type'      => $sub_field_value["type"],
                        'value'     => $sub_field_value['value'],
                        'label'     => $sub_field_value["label"],
                        'required'  => $sub_field_value['required']
                    );

                    // grab original markup template and define group ##
                    self::markup_template( $sub_field_value, $args );

                    // define markup item to find and replace ##
                    self::markup_field( $array, $args );

                    // log ##
                    self::log( 'Field: '.$sub_field_value['name']. ' - Type: '.$sub_field_value['type'] );
                    #self::log( $array );

                    // pass each sub field to the format_valueer ##
                    self::format_value( $array, $args );

                    // log ##
                    #self::log( 'renderer called here: 2' );

                    // render markup ##
                    #self::markup_render();

                    // log ##
                    self::log( 'renderer called here: 2' );

                    // render markup ##
                    self::markup_render();

                }


                // iterate ##
                $count ++ ;

                // close repeater sub_field element ##
                if ( $count == count( $field['sub_fields'] ) ) {

                    self::log( 'closing sub html at: '.$sub_field_value["name"] );

                    // close sub ##
                    self::$return .= self::wrapper( array ( 'action' => 'sub-close' ) );

                }

            }

        }

        // render ##
        #$return .= self::markup_render();

        // kick it back ##
        return true;

    }


    public static function is_enabled( $args = null )
    {

        // helper::log( $args );

        // default ##
        $array = array( 
            'enabled'   => '1',
            'message'   => isset( $args['enable']['message'] ) ? $args['enable']['message'] : false
        );

        // check we have all the field data we need ##
        if (
            isset( $args['enable'] ) // function set to enable disabling ##
        ) { 

            // convert legacy format to array ##
            if ( ! is_array( $args['enable'] ) ) {

                // store value ##
                $store = $args['enable'];

                $args['enable'] = array();
                $args['enable']['trigger'] = $store;

            }

            // helper::log( "checking enable status of group: ".$args['group'] );
            // helper::log( "post ID: ".get_the_ID() );
            
            $value = \get_field( $args['enable']['trigger'], get_the_ID() );

            // helper::log( "we got the enable value: ".$value );
            // helper::log(  $args->enable );

            if ( '0' == $value ) {
        
                // helper::log( "Group is not enabled: ".$args['group'] );

                $array['enabled'] = '0';

            }

        }

        #self::log( $array );

        // default to yes ##
        return $array;

    }




    /**
    * Get and return post meta fields
    *
    * @since       1.0.1
    * @return      string   HTML
    */
    public static function format_value( Array $array = null, $args = null )
    {

        // Parse incoming $array into an array and merge it with $defaults - caste to object ##
        $array = ( object ) \wp_parse_args( $array );

        // get field from name ##
        $field = $array->name;

        // filtering ##
        $filter = array();

        // build top level generic filter ##
        $filter['type'] = 'q/meta/'.$array->type;

        // build specific filter for single field ##
        $filter['field'] = 'q/meta/'.$args['group'].'/'.$field;

        #self::log( $filter );

        if ( 
            ! isset ( $array->name ) 
            || ! isset ( $array->type ) 
        ) {

            self::log( "kicked - missing properties 2" );
           
            return false;

        }

        // helper::log( $array->post );
        // helper::log( $args );
        // helper::log( isset( $args['post'] ) ? $args['post'] : 'No post passed..' );

        // sanity check ##
        if ( 
            isset( $array->post )
            && 'is_tax' != $array->post
        ) {

            // helper::log( 'Trying to load post->ID: '.$array->post );

            if ( 
                // ! $the_post = wordpress::the_post( 
                //     array( 
                //         'post' => $args['post'] 
                //     ) 
                // )
                ! $the_post = \get_post( $array->post )
            ) {

                // // helper::log( "kicked - no post: ".$array->post );
                
                return false;

            }

        }

        // helper::log( 'post->ID: '.$the_post->ID );

        // field value passed already ##
        if ( 
            isset( $array->value ) 
            && ! empty( $array->value )
        ) {

            // helper::log( 'field "'.$array->name.'" value loaded already: '.$array->value );

        // work tax fields ##
        } else if ( 
            // \is_tax() // WP knows this is a taxonomy ##
            // && 
            // and we did not override, by passing a post ID ##
            ( 
                // isset( $args['post'] )
                // && 
                'is_tax' == $array->post
            )
            && function_exists( 'get_field' )   
            && \get_queried_object() 
        ) {

            // helper::log( 'is_tax()' );

            $array->value = \get_field( $field, \get_queried_object() ); 

        } else if ( 
            isset( $the_post )
        ) {
            if (
                $array->value = \get_post_meta( $the_post->ID, $field, true ) 
            ) {
            
               #self::log( 'Post meta for: '.$field.' returned: '.$array->value );

            }

        }

        // store value pre_filter ##
        $pre_filtered = $array->value;

        // try to apply a filter ##
        $array->value = self::filter( $array, $args, $filter, $pre_filtered );

        // if filter worked, we can skip straight to markup ##
        if ( 
            #isset( $array->value )
            ! empty( $array->value )
            && $pre_filtered != $array->value 
            #true === self::$filtered
        ) {

            self::log( 'field '.$array->name.' value has been filtered, so skipping to markup.' );
            self::log( $array->value );

            // reset ##
            #self::$filtered = false;

            return self::markup_value( $array, $args );

        }

        // validate fields ##
        if ( 
            $array->required 
            && false === $array->value
        ) {

            // log ##
            self::log( 'Required field missing: '.$array->name );

            self::$markup_tracker[self::$markup_group]['total'] = self::$markup_tracker[self::$markup_group]['total'] - 1;

            // kick out ##
            return false;

        }

        // check meta isset and not empty ##
        if ( 
            ! isset( $array->value ) 
            || empty ( $array->value )
            || false == $array->value 
        ) {

            // we need to delete the placeholder value ##
            #$count = self::markup_count() ;
            #self::$markup_tracker[self::$markup_group]['total'] = self::$markup_tracker[self::$markup_group]['total'] - 1;
            self::$markup_tracker[self::$markup_group]['total'] = self::$markup_tracker[self::$markup_group]['total'] - 1;

            #self::log( "kicked - meta empty: ".$field );
            
            // kicked out as value is empty ##
            return false;

        }

        #self::log( 'check value, pre switch:' );
        #self::log( $array );

        //switch over types ##
        switch ( $array->type ) {

            // phone ##
            case 'phone':

                // nada ##
                #$array->value = $array->value;

            break;

            // email ##
            case 'email':

                if ( ! \is_email( $array->value ) ) { return false; }

                $array->value = '<a href="mailto:'.\antispambot( $array->value ).'">'.\antispambot( $array->value ).'</a>';

            break;

            // url ##
            case 'url':

                $array->value = '<a href="'.\q_add_http($array->value).'" target="_blank">'.str_replace( array( "http://", "https://", "www." ), "", $array->value ).'</a>';

            break;

            // url ##
            case 'radio':
            case 'select':

                if ( is_null( $array->value ) ) {

                    $field = \get_field_object( $array->name );
                    $value = \get_field( $array->name );
                    $array->value = $field['choices'][ $value ];

                }

               #self::log( 'value set here: '.$array->value );

            break;

            // boolean ##
            case 'boolean':

                $array->value = $array->value == 1 ? \__( "Yes", self::text_domain ) : \__( "No", self::text_domain );

            break;

            // Google Map ##
            case 'map':

                $array->value = implode(', ', array_map(function ($v, $k) { return sprintf("%s='%s'", $k, $v); }, $array->value, array_keys( $array->value ) ) );

            break;

            // Legacy Location Date ##
            case 'location':

                $explode = explode( '|', $array->value );
                $array->value = $explode[0];

            break;

            // image ##
            case 'image':

                if ( isset( $args['handle'][helper::get_device()] ) ) {

                    #self::log( 'Image Handle: '.$args['handle'] );

                    if ( $image = \wp_get_attachment_image_src( $array->value, $args['handle'][helper::get_device()] ) ) {

                        #self::log( $image );

                        $array->value = $image[0];

                    }

                // holder fallback ##
                } else if ( isset( $args['holder'][helper::get_device()] ) ) {

                    $array->value = $args['holder'][helper::get_device()];

                }

            break;

            // textarea ##
            case 'textarea':

                // do standard filtering ##
                $array->value = \wpautop( $array->value );

            break;

            // oembed ##
            case 'oembed':

                // do standard filtering ##
                $array->value = \wp_oembed_get( $array->value );

            break;

            // file ##
            #case 'file':
            
                // return value untouched ##
                #$array->value = $array->value;

            break;

            // text and default ##
            case 'text':
            default:

                // do standard filtering ##
                #$array->value = $array->value;

            break;

        }

        self::log( 'last check of value, before it is passed:' );
        self::log( $array );

        // mark it up ##
        return self::markup_value( $array, $args );

    }


    public static function filter( $array = null, $args, $filter = null, $pre_filtered )
    {

        // field filter tracker ##
        #self::$pre_filtered = $pre_filtered;
        #$array->value = false;

        // we need an array ##
        if ( 
            is_null( $filter ) 
            || ! is_array( $filter ) 
        ) {

           #self::log( '$filter \q_theme error.' );

            return $array->value;

        }

        // check filter name ##
        #self::log( 'Checking filter: '.$filter );
        #self::log( 'Filter value: '.$pre_filtered );

        if( \has_filter( $filter['type'] ) ) {

            // filter - if available  ##
            $array->value = \apply_filters( $filter['type'], $pre_filtered, $array, $args );

            #self::log( 'NEW Value: '.$array->value );

        } else if( \has_filter( $filter['field'] ) ) {

            // filter - if available  ##
            $array->value = \apply_filters( $filter['field'], $pre_filtered, $array, $args );

            #self::log( 'NEW Value: '.$array->value );

        }

        #$pre_filtered = false;

        // did we get something filtered ##
        if ( 
            #$array->value
            #&& 
            $pre_filtered !== $array->value 
        ) {

            #self::$filtered = true;

           #self::log( 'Post filter value: '.$array->value );

        }

        // kick something back ##
        return $array->value;

    }



    /**
    * Get a list of the allowed fields types for meta entries
    *
    * @since       1.0.1
    * @return      Object   Meta types
    */
    public static function allowed_fields()
    {

        // set-up new object ##
        return \apply_filters( 'meta_allowed_fields', array(

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


    /**
    * Populate $markup_field property for single field
    *
    * @return   Mixed
    * @since    2.0.0
    */
    public static function markup_field( $array = null, $args = null )
    {

        if ( is_null( $array ) ) {

            #self::log( 'missing field data.' );

            return false;

        }

        // helper::log( $args['markup'] );

        if ( ! isset( self::$markup_group ) ) {

            self::log( 'No markup_group defined for field: '.$array['name'].' -- setting to all..' );

            self::$markup_group = 'all'; 

            #return false;

        }

        return self::$markup_field[self::$markup_group] = '%'.$array['name'].'%';

    }



    /**
    * Populate $markup property from passed template
    *
    * @return   Mixed
    * @since    2.0.0
    */
    public static function markup_template( $array = null, $args = null )
    {

        // no args passed -- fail ##
        if ( is_null( $args ) ) {

            #self::log( 'missing $args data.' );

            return false;

        }

        // failed to assign markup_group ##
        if ( ! self::markup_group( $array, $args ) ) {

            #self::log( 'Requested markup group does not exist: '.$group );

            return false;

        }

        // markup group already populated ##
        if ( 
            #isset( self::$markup )
            #&&
            isset( self::$markup[self::$markup_group] )
            && ! is_null( self::$markup[self::$markup_group] )
        ) {

            self::log( '$markup key already populated for group: '.self::$markup_group );

            return false;

        }

        // no markup defined in template - or requested group does not exist use default ##
        if ( 
            ! isset( $args['markup'] ) 
            #|| ! isset( $args['markup'][$array['name']] )     
        ) {

            self::log( $args['markup'] );
            self::log( 'Template does not include a markup schema for: '.$array['name'] );

            #return false;

            $args['markup'] = self::markup_default( $array, $args );

        }

        // should be good to go ##
        self::log( 'markup_group: '.self::$markup_group );
        // if ( 
        //     ! isset( self::$markup )
        //     || is_null( self::$markup )
        // ) {

        //     self::log( 'defining $markup for the first time...' );
        //     self::$markup = array();

        // }
        #self::$markup[self::$markup_group] = array();

        // define which markup group to use ##
        // check if device key exists ##
        if ( isset( $args['markup'][helper::get_device()] ) ){

            // helper::log( 'Using markup for device: '.helper::get_device() );

            self::$markup[self::$markup_group]['template'] = $args['markup'][helper::get_device()];

        } elseif ( isset( $args['markup']['all'] ) ){

            // helper::log( 'Using "all" device markup' );

            self::$markup[self::$markup_group]['template'] = $args['markup']['all'];

        } else {

            // helper::log( 'No usable markup found, returning nada..' );

            return false;

        }

        // helper::log( $args['markup'][self::$markup] );
        // helper::log( $args['markup'] );
        // self::$markup[self::$markup_group]['template'] = 
        //     isset( $args['markup'][self::$markup_group] ) ? 
        //     $args['markup'][self::$markup_group] : 
        //     $args['markup']['all'] ;
        self::$markup[self::$markup_group]['original'] = self::$markup[self::$markup_group]['template'];

        self::log( self::$markup[self::$markup_group] );

        // open new tracker with count of total markup placeholders ##
        self::$markup_tracker[self::$markup_group] = array();
        self::$markup_tracker[self::$markup_group]['count'] = self::markup_count();
        self::$markup_tracker[self::$markup_group]['total'] = self::$markup_tracker[self::$markup_group]['count'];

        #self::log( '$markup template for group "'.self::$markup_group.'" set-up' );
        #self::log( self::$markup[self::$markup_group]['template'] );

        // positive ##
        return true;

    }



    /**
    * Check and define markup group to use
    *
    * @since    2.0.0
    */
    public static function markup_group( $array = null, $args = null )
    {

        self::log( 'markup_group():' );
        #self::log( $array );

        // no args passed -- fail ##
        if ( 
            is_null( $array ) 
            || is_null( $args )
        ) {

            self::log( 'missing $args data.' );

            return false;

        }

        if ( 
            ! isset( $args['markup'][$array['name']] ) 
        ) {

            #self::log( $args['markup'] );

            #self::log( 'markup_group rejected: '.$array['name'].' -- group stays: '.self::$markup_group );

            return true;

        } 

        self::log( 'markup group moved to: '.$array['name'] );

        self::$markup_group = $array['name'];

        #self::log( self::$markup_group );
        #self::log( self::$markup );

        return true;

        // set group ##
        #self::$markup_group = 'all';

        #self::log( 'markup group defaulted to: '.$group );

        // we need truth here also.. ##
        #return true;

    }


    /**
    * Search and replace $armkup_field with $value in defined markup template
    *
    * @return   Mixed
    * @since    2.0.0
    */
    public static function markup_value( $array = null, $args = null )
    {

        self::log( 'markup["template"]: '.self::$markup[self::$markup_group]['template'] );
        self::log( 'markup_group: '.self::$markup_group );
        self::log( 'value: '.$array->value );
        self::log( 'field["group"]: '.self::$markup_field[self::$markup_group] );

        // one less to track ##
        self::$markup_tracker[self::$markup_group]['total'] = self::$markup_tracker[self::$markup_group]['total'] - 1;

        if ( 
            ! isset( $array->value )
            #! isset( self::$markup ) // check if we have markup to format ##
            || ! isset( self::$markup_field[self::$markup_group] ) // now we check if we have a defined field to markup ##
            #|| ! self::$markup_group // we need a group to proceed ##
            || ! isset( self::$markup[self::$markup_group]['template'] ) // check if we have markup to format ##
        ) {

            self::log( 'Error marking-up value: '.self::$markup_field[self::$markup_group] );

            return false;

        }

        #self::log( self::$markup[self::$markup_group]['template'] );

        // now check if the current $markup_field[self::$markup_group] exists in the markup template ##
        if ( preg_match( '/'.self::$markup_field[self::$markup_group].'/', self::$markup[self::$markup_group]['template'] ) ) {

           #self::log( 'replacing holder value: '.self::$markup_field[self::$markup_group].' with: '.$array->value );

            self::$markup[self::$markup_group]['template'] = 
                str_replace( 
                    self::$markup_field[self::$markup_group], 
                    $array->value, 
                    self::$markup[self::$markup_group]['template'] 
                );

            self::log( 'markup current state: '. self::$markup[self::$markup_group]['template'] );

            // check if we are ready to add $markup to $return ##
            $count = self::markup_count() ;
            
            // kick back positive ##
            return true;

        }

        #self::log( 'error marking up value: '.self::$markup_field[self::$markup_group] );

        // negative ##
        return false;

    }



    
    /**
    * delete empty field value from markup template, if field holder exists
    *
    * @return   Mixed
    * @since    2.0.0
    */
    public static function markup_default( $array = null, $args = null )
    {

        if ( 
            is_null( $array )
            || is_null( $args ) 
        ) {

            self::log( 'missing parameters...' );

        }

        // define ##
        $markup = array( 
            'all' => "<li class='meta-key-".$array['name']." meta-field-".$array['type']."' data-meta-label='".$array['label']."'><span class='meta-value'>%".$array['name']."%</span></li>" 
        );

        // kick it back, with filterable value ##
        return \apply_filters( 'q_meta_default_markup', $markup );

    }


    /**
    * delete empty field value from markup template, if field holder exists
    *
    * @return   Mixed
    * @since    2.0.0
    */
    public static function markup_cleanup()
    {

        if ( 
            isset( self::$markup[self::$markup_group]['template'] )
            && ! is_null( self::$markup[self::$markup_group]['template'] )
            #&& preg_match( '/'.self::$markup_field[self::$markup_group].'/', self::$markup[self::$markup_group]['template'] )
        ) {

            // count the number of placeholder in the markup template ##
            $count = preg_match_all( '/\%(.*?)\%/s', self::$markup[self::$markup_group]['template'], $matches );

            self::log( 'template: '.self::$markup[self::$markup_group]['template'] );
            self::log( 'original: '.self::$markup[self::$markup_group]['original'] );

            if ( 
                self::$markup[self::$markup_group]['template'] == self::$markup[self::$markup_group]['original'] 
            ) {

                self::log( 'markup template is unchanged - so deleting everything...' );

                // update track properties ##
                self::$markup_tracker[self::$markup_group] = null;

                self::$markup[self::$markup_group] = null;

                self::$markup_group = 'all';

                return false;

            }

            if ( 0 === $count ) {

                self::log( 'all clear, no placeholders found.' );

                return true;

            }

            #self::log( $matches );

            foreach( $matches[0] as $key => $value ) {

                self::$markup[self::$markup_group]['template'] = 
                str_replace( 
                    $value, 
                    '', // nada ##
                    self::$markup[self::$markup_group]['template'] 
                );

                self::log( "removed placeholder for: ".$value );

            }

            // if ( 0 === $count ) {

            //     self::log( 'no placeholders left, all clear to print..' );

            //     return true;

            // }

            // kick back ##
            return true ;

        }

        // default ##
        return false;

    }



    /**
    * check if all placeholders have been filled or removed - if so, add to $return property
    *
    * @return   Mixed
    * @since    2.0.0
    */
    public static function markup_count()
    {

        // check if we have markup to return ##
        if ( 
            is_null ( self::$markup[self::$markup_group]['template'] ) 
        ) {

            #self::log( self::$markup );
            #self::log( self::$markup_group );
            self::log( 'nothing in the markup group: '.self::$markup_group );

            return false;

        }

        // count the number of placeholder in the markup template ##
        $count = preg_match_all( '/\%(.*?)\%/s', self::$markup[self::$markup_group]['template'], $matches );

        #self::log( $matches );

        if ( 0 === $count ) {

            #self::log( 'no placeholders left, so push markup into $return and reset properties.' );

            // update tracking ##
            self::$markup_tracker[self::$markup_group]['count'] = 'empty';

        } else {

            // log ##
            self::log( 'found '.$count.' placeholders in group '.self::$markup_group.' - field: '.self::$markup_field[self::$markup_group] );

            // update count ##
            self::$markup_tracker[self::$markup_group]['count'] = $count;

        }

        // kick it back ##
        return $count;

    }



    /**
    * grab markup, empty stored values - reset
    *
    * @return   Mixed
    * @since    2.0.0
    */
    public static function markup_render()
    {

        #self::log( 'markup: '.self::$markup[self::$markup_group]['template'] );
        #self::log( 'markup_group: '.self::$markup_group );
        #self::log( 'value: '.$value );
        #self::log( 'placeholder: '.self::$markup_field[self::$markup_group] );

        // asanity check ##
        if ( 
            ! isset( self::$markup_group )
            || ! isset( self::$markup_tracker[self::$markup_group]['count'] )
            #|| 'empty' != self::$markup_tracker[self::$markup_group]['count']
        ) {

            #self::log( self::$markup_tracker[self::$markup_group]['count'] );

            self::log( 'group '.self::$markup_group.' error' );

            return false;

        }

        // are we ready ?? ##
        if ( 
            0 <= self::$markup_tracker[self::$markup_group]['total']
        ) {

            self::log( self::$markup_tracker[self::$markup_group]['total'] );
            self::log( 'group '.self::$markup_group.' is not yet complete' );

            return false;

        }
        
        // clean-up unused placeholder values and reject if markup template is unchanged ##
        if ( ! self::markup_cleanup() ) {

            self::log( 'cleanup rejected markup.' );

            return false;

        }

        self::log( 'render stored: '.self::$markup[self::$markup_group]['template'] );

        // grab values ##
        self::$return .= self::$markup[self::$markup_group]['template'];

        // reset ##
        self::$markup[self::$markup_group] = null ;
        self::$markup_tracker[self::$markup_group] = null;

        // kick it back ##
        return true;

    }



    /**
    * build Meta HTML
    *
    * @param       string      $key
    * @param       string      $type
    * @param       string      $title
    * @param       Mixed       $value
    *
    * @since       1.0.1
    * @return      string      HTML
    */
    public static function markup( $array = array(), $args = null )
    {

        // Parse incoming $array into an array and merge it with $defaults - caste to object ##
        $array = ( object )\wp_parse_args( $array );
        
        #self::log( $args );

        // check sent data ##
        if ( ! isset ( $array->name ) || ! isset( $array->type ) || ! isset( $array->value ) ) {

            #self::log( "kicked: missing data or option." );
            #self::log( $array );

            return false;

        }

        // tidy up $key and $type ##
        #$array->name = str_replace( array( "_" ), "-", $array->name );
        #$array->type = str_replace( array( "_" ), "-", $array->type );

        // check if we can markup this value into a template ##
        self::markup_value( $array, $args );

        /*
        if ( self::markup_value( $array->value, $args ) ) {

            #self::log( 'markup_value: '.$markup_value );

            return false;

        // allow template without a title, required in some cases ##
        } else if ( 
            ! isset ( $array->label ) 
            || false === $array->label
        ) {

            return "
            <li class='meta-key-{$array->name} meta-field-{$array->type}'>
                <span class='meta-value'>{$array->value}</span>
            </li>";

        } else {

            return "
            <li class='meta-key-{$array->name} meta-field-{$array->type}'>
                <span class='meta-title'>{$array->label}</span>
                <span class='meta-colon colon'>:</span>
                <span class='meta-value'>{$array->value}</span>
            </li>";

        #} else {

            // nada ##
            #return false;

        }
        */

    }



    /**
    * Meta Sub Element HTML markup
    *
    * @param       string      $action
    * @since       1.1.1
    */
    public static function wrapper( $args = array() )
    {

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object )\wp_parse_args( $args, \q_theme::$the_meta_markup );

        // build up data array ##
        $data = false;
        if ( isset( $args->data ) && is_array( $args->data ) ) {

            foreach( $args->data as $key => $value ) {

                #self::log( 'adding data attr: data-'.$key.'="'.$value.'"' );

                $data .= ' data-'.$key.'="'.$value.'"';

            }

        }

        switch ( $args->action ) {

            case "sub-close" :

                return "
                </ul>";

            break ;

            case "sub-open" :

                return "
                <ul class='meta-sub {$args->class}'{$data}>";

            break ;

            case "parent-close" :

                return "
                </div>";

            break ;

            case "parent-open" :

                return "
                <div class='{$args->class}' id='{$args->id}'>";

            break ;

            case "wrapper-close" :

                return "
                </{$args->tag}>";

            break ;

            case "wrapper-open" :
            default :

                return "
                <{$args->tag} class='{$args->class}'>";

            break ;

        }

    }


    /**
    * Clean things up
    *
    *
    */
    public static function reset()
    {

       #self::log( 'resetting stuff...' );

        #$return = false;
        self::$count = 0;
        self::$markup = null;
        self::$markup_field = null;
        self::$markup_tracker = null;
        self::$return = null;
        #self::$filtered = false;

    }

}