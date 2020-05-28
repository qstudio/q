<?php

namespace q\controller;

use q\core\core as core;
use q\core\helper as helper;
use q\core\config as config;
use q\core\wordpress as wordpress;
use q\plugin\acf as acf;
use q\controller\generic as generic;
use q\controller\javascript as javascript;
use q\controller\css as css;

// Q Program ##
use q\program\core\core as program_core;

// load it up ##
\q\controller\tab::run();

class tab extends \Q {

    public static $args = [];

    public static function run()
    {

        // add ACF fields
        \add_action( 'acf/init', function() { acf::add_field_groups( self::add_field_groups() ); }, 1 );

        // filter q/tab/special/script ##
        \add_filter( 'q/tab/special/script', [ get_class(), 'tab_special_script' ], 10, 2 );

        // add JS to footer if debugging or single q.theme.js script if not ##
        \add_action( 'wp_footer', [ get_class(), 'wp_footer' ], 5 );

        // add css ##
        // \add_action( 'wp_head', [ get_class(), 'wp_head' ], 4 );

    }




    /**
    * Define field groups
    *
    * @since    2.0.0
    * @return   Mixed
    */
    public static function add_field_groups( $group = null )
    {

        // define field groups - exported from ACF ##
        $groups = array (

            'tab' => array (
                'key' => 'group_q_tab',
                'title' => 'Tabs',
                'fields' => array(
                    array(
                        'key' => 'field_q_tab_enable',
                        'label' => 'Settings',
                        'name' => 'q_tab_enable',
                        'type' => 'radio',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            0 => 'Disabled',
                            1 => 'Enabled',
                        ),
                        'allow_null' => 0,
                        'other_choice' => 0,
                        'save_other_choice' => 0,
                        'default_value' => 0,
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                    ),
                    array(
                        'key' => 'field_q_tab',
                        'label' => 'Tabs',
                        'name' => 'q_tab',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => array (
                            array (
                                array (
                                    'field' => 'field_q_tab_enable',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => 'field_q_tab_title',
                        'min' => 0,
                        'max' => 20,
                        'layout' => 'row',
                        'button_label' => 'Add Tab',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_q_tab_type',
                                'label' => 'Content Type',
                                'name' => 'type',
                                'type' => 'radio',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'choices' => array(
                                    'text' => 'Text',
                                    'faq' => 'FAQ',
                                    // 'blog' => 'Blog',
                                    'gallery' => 'Gallery',
                                    'special' => 'Special',
                                ),
                                'allow_null' => 0,
                                'other_choice' => 0,
                                'save_other_choice' => 0,
                                'default_value' => 'text',
                                'layout' => 'horizontal',
                                'return_format' => 'value',
                            ),
                            array(
                                'key' => 'field_q_tab_options',
                                'label' => 'Special',
                                'name' => 'special',
                                'type' => 'select',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_q_tab_type',
                                            'operator' => '==',
                                            'value' => 'special',
                                        ),
                                    ),
                                ),
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'choices' => array(
                                    'script'    => 'Script',
                                    // 'job'       => 'Job Fairs',
                                ),
                                'default_value' => array(
                                ),
                                'allow_null' => 0,
                                'multiple' => 0,
                                'ui' => 0,
                                'ajax' => 0,
                                'return_format' => 'value',
                                'placeholder' => '',
                            ),
                            array(
                                'key' => 'field_q_tab_title',
                                'label' => 'Title',
                                'name' => 'title',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => 30,
                            ),
                            array(
                                'key' => 'field_q_tab_text',
                                'label' => 'Content',
                                'name' => 'text',
                                'type' => 'wysiwyg',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_q_tab_type',
                                            'operator' => '==',
                                            'value' => 'text',
                                        ),
                                    ),
                                    array(
                                        array(
                                            'field' => 'field_q_tab_type',
                                            'operator' => '==',
                                            'value' => 'gallery',
                                        ),
                                    ),
                                    array(
                                        array(
                                            'field' => 'field_q_tab_type',
                                            'operator' => '==',
                                            'value' => 'special',
                                        ),
                                    ),
                                ),
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'tabs' => 'all',
                                'toolbar' => 'full',
                                'media_upload' => 1,
                                'delay' => 1,
                                'display_word_limit' => 1,
                                'word_limit' => '',
                            ),
                            array(
                                'key' => 'field_q_tab_faq',
                                'label' => 'FAQ',
                                'name' => 'faq',
                                'type' => 'repeater',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_q_tab_type',
                                            'operator' => '==',
                                            'value' => 'faq',
                                        ),
                                    ),
                                ),
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'collapsed' => 'field_q_tab_faq_title',
                                'min' => 2,
                                'max' => 30,
                                'layout' => 'table',
                                'button_label' => 'Add FAQ',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_q_tab_faq_title',
                                        'label' => 'Title',
                                        'name' => 'title',
                                        'type' => 'text',
                                        'instructions' => '',
                                        'required' => 1,
                                        'conditional_logic' => 0,
                                        'wrapper' => array(
                                            'width' => '',
                                            'class' => '',
                                            'id' => '',
                                        ),
                                        'default_value' => '',
                                        'placeholder' => '',
                                        'prepend' => '',
                                        'append' => '',
                                        'maxlength' => 200,
                                    ),
                                    array(
                                        'key' => 'field_q_tab_faq_content',
                                        'label' => 'Content',
                                        'name' => 'content',
                                        'type' => 'wysiwyg',
                                        'instructions' => '',
                                        'required' => 1,
                                        'conditional_logic' => 0,
                                        'wrapper' => array(
                                            'width' => '',
                                            'class' => '',
                                            'id' => '',
                                        ),
                                        'default_value' => '',
                                        'tabs' => 'all',
                                        'toolbar' => 'basic',
                                        'media_upload' => 0,
                                        'delay' => 1,
                                        'display_word_limit' => 1,
                                        'word_limit' => '',
                                    ),
                                ),
                            ),
                            array(
                                'key' => 'field_q_tab_special_gallery',
                                'label' => 'Images',
                                'name' => 'tab_special_gallery',
                                'type' => 'gallery',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_q_tab_type',
                                            'operator' => '==',
                                            'value' => 'gallery',
                                        ),
                                    ),
                                ),
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'min' => 3,
                                'max' => 12,
                                'insert' => 'append',
                                'library' => 'all',
                                'min_width' => 300,
                                'min_height' => 300,
                                'min_size' => '',
                                'max_width' => '',
                                'max_height' => '',
                                'max_size' => 8,
                                'mime_types' => 'jpg',
                            ),
                            array (
                                'key' => 'field_q_tab_special_script',
                                'label' => 'Embed Script',
                                'name' => 'tab_special_script',
                                'type' => 'textarea',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => array (
                                    array (
                                        array (
                                            'field' => 'field_q_tab_options',
                                            'operator' => '==',
                                            'value' => 'script',
                                        ),
                                    ),
                                ),
                                'wrapper' => array (
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'maxlength' => '',
                                'rows' => 4,
                                'new_lines' => '',
                            ),
                        ),
                    ),
                ),
                'location' => array(
                    // array(
                    //     array(
                    //         'param' => 'post_type',
                    //         'operator' => '==',
                    //         'value' => 'page',
                    //     ),
                    // ),
                    array (
                        array (
                            'param' => 'page_template',
                            'operator' => '==',
                            'value' => 'page.php',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => array(
                    // 0 => 'the_content',
                ),
                'active' => 1,
                'description' => '',
            ),

        );


        // check if we ar returning a single set or all groups ##
        if ( is_null( $group ) ) {

            #helper::log( 'Returning all groups.' );

            // @todo -- we need to filter here ##

            return $groups;

        } elseif ( 
            isset( $group ) 
            && is_array( $groups )
            && array_key_exists( $group, $groups )
            && array_key_exists( 'fields', $groups[$group] )
        ) {

            #helper::log( 'returning fields in group: '.$group );

            return $groups[$group]['fields'];

        }

        // nothing cooking ##
        return false;

    }




    /**
    * Load tabs from ACF repeater field
    *
    * @since    2.0.0
    * @return   Mixed Boolean on error or Array
    */
    public static function get( Array $args = null )
    {

        // grab global post ##
        if (
            is_null( $args )
            || ! isset( $args['field'] )
        ) {

            helper::log( 'Kicked early, missing params..' );

            // nothing found ##
            return false;

        }

        // get post data ##
        $array = \get_field( $args['field'], \get_the_ID() );

        // check ##
        // helper::log( 'field: '.$args['field'] );
        // helper::log( $array );

        // kick back ##
        return $array;

    }



    /**
     * Secure content, if password protected
     *
     * @since
     * @return      Mixed
     */
    public static function is_secure()
    {

        if (
            ! $the_post = wordpress::the_post()
        ) {

            helper::log( 'No post object...' );

            return false;

        }

        if ( \post_password_required( $the_post ) ) {

            // we will show password form here ##
            return true;

        }

        // default ##
        return false;

    }



    


    public static function is_enabled()
    {

        // check we have all the field data we need ##
        if (
            isset( self::$args['enable'] ) // function set to enable disabling ##
        ) { 

            // helper::log( "checking enable status for tabs" );
            // helper::log( "post ID: ".\get_the_ID() );
            
            $value = \get_field( self::$args['enable'], \get_the_ID() );

            // helper::log( "we got the enable value: ".$value );

            if ( 
                false === $value
                || '0' == $value 
                || ! $value
            ) {
        
                // helper::log( "tabs is not enabled" );

                return false;

            }

        }

        // default to yes ##
        return true;

    }


    /**
    * Build tab UI navigation
    *
    * @since    2.0.0
    * @return   Mixed Boolean on error or HTML string
    */
    public static function render( Array $args = null )
    {

        // assign ##
        self::$args = isset( $args ) ? (array) $args: [] ;

        // grab global post ##
        if (
            is_null( $args )
        ) {

            helper::log( 'Kicked early, missing params..' );

            // nothing found ##
            return false;

        }

        // secure ##
        if ( self::is_secure() ) {

            return wordpress::get_the_password_form();

        }

        // format $string ready for rendering ##
        $string = self::prepare( $args );

        // render ##
        echo $string;

    }



    /**
     * Prepare tabs and gather content, ready for rendering
     *
     * @since   2.0.0
     * @return  Mixed Boolean on error or String
     */
    public static function prepare( $args = null ){

        // we need to check if this post has some tabs stored ##
        if (
            ! $tabs = self::get( $args )
        ) {

            // log ##
            // helper::log( 'No tabs found for this post - return all post content' );

            // return ##
            // return false;

        }

        // not enabled ##
        if ( ! self::is_enabled() ) {

            // log ##
            // helper::log( 'Tabs not enabled, so droppping to post_content.' );

            $tabs = false; // clear variable ##

        }


        // tabs should be an array ##
        if (
            $tabs
            && ! is_array( $tabs )
        ) {

            // log ##
            helper::log( 'Tabs corrupt - return all post content' );

            // return ##
            // return false;

        }

        // convert data returned from acf into a workable format ##
        // $array gets formatted and rendered at the end of this method ##
        if (
            $tabs
            && is_array( $tabs )
        ) {
            /*
            0 => [
                'title'         => 'Overview', // string ##
                'hash'          => 'overview' // string ##
                'content'       => 'blah', // html blob ##
                'type'          => 'text', // string ##
            ]
            */
            $array = self::format( $tabs, $args );

            // markup using passed config in $args ##
            $string = self::markup( $array, $args );

        // backup content - no tabs ##
        } else {

            // remove known shortcodes ##
            $string = \strip_shortcodes( \get_post_field( 'post_content', \get_the_ID() ) );

            // strip all missing shortcodes ##
            $string = preg_replace( '#\[[^\]]+\]#', '', $string );

            // get the post content ##
            $string = \apply_filters( 'the_content', $string );

            // apply some basic markup ##
            if ( isset( $args['markup']['default'] ) ) $string = str_replace( '%string%', $string, $args['markup']['default'] );

            // filter complete markup ##
            $string = \apply_filters( 'q/tab/markup/default', $string, $args );

        }

        #helper::log( $string );

        // kick it back ##
        return $string;

    }



    /**
     * Markup array of tabs based onpassed config
     *
     * @since   2.0.0
     * @return  Mixed Boolean on error or String
     */
    public static function markup( $array = null, $args = null ){

        // tabs should be an array ##
        if (
            ! is_array( $array ) ) {

            // log ##
            helper::log( 'Tabs corrupt' );

            // return ##
            return false;

        }

        // prepare return strings ##
        $navigation = '';
        $content = '';
        $string = '';

        // helper::log( $array );

        // loop over tabs and apply markup
        foreach( $array as $key => $value ){

            $navigation_row = [
                'url'       => \get_permalink( \get_the_ID() ),
                'hash'      => $value['hash'],
                'title'     => $value['title']
            ];

            // build nav ##
            $navigation .= generic::markup( $args['markup']['navigation']['row'][helper::get_device()], $navigation_row );

            // build content ##
            $content_row = [
                'title'     => $value['title'],
                'hash'      => $value['hash'],
                'content'   => $value['content'],
                'type'      => $value['type']
            ];

            // build nav ##
            $content .= generic::markup( $args['markup']['content']['row'], $content_row ); ;

        }

        // compile markup & order ##
        $string .= str_replace( '%row%', $navigation, $args['markup']['navigation']['wrap'][helper::get_device()] );
        $string .= str_replace( '%row%', $content, $args['markup']['content']['wrap'] );

        // program cta ##
        // %program_cta%
        $string =
            '#' == self::get_purl() ?
            str_replace( '%program_cta%', '', $string ) :
            str_replace( '%program_cta%', $args['markup']['content']['cta'] , $string ) ;

        // filter complete markup ##
        $string = \apply_filters( 'q/tab/markup/complete', $string, $array, $args );

        // kick it back ##
        return $string;

    }




    /**
     * Format tabs and gather content, ready for rendering
     *
     * @since   2.0.0
     * @return  Mixed Boolean on error or String
     */
    public static function format( $tabs = null, $args = null ){

        // tabs should be an array ##
        if (
            ! is_array( $tabs ) ) {

            // log ##
            helper::log( 'Tabs corrupt' );

            // return ##
            return false;

        }

        // prepare return array ##
        $return = [];

        // loop over tab data ##
        foreach( $tabs as $tab ) {

            // switch over type
            switch( $tab['type'] ){

                case 'special' :

                    // string -re: "mos" ##
                    $content = self::type_special( $tab, $args );

                break ;

                case 'blog' :

                    // array of WP_Post objects
                    $content = self::type_blog( $tab, $args );

                break ;

                case 'gallery' :

                    // array of media objects
                    $content = self::type_gallery( $tab, $args );

                break ;

                case 'faq' :

                    /*
                    [0] => Array
                        (
                            [title] => FAQ One
                            [content] => Content
                        )
                    */
                    $content = self::type_faq( $tab, $args );

                break ;

                case "text" :
                default :

                    // helper::log( $args['markup']['text']['wrap'] );
                    $wrap = $args['markup']['text']['wrap'];

                    // string -re: "Content" ##
                    $content = self::type_text( $tab['text'] );

                    // wrap ##
                    $content = str_replace( '%string%', $content, $wrap );

                break ;

            }

            // generate return array ##
            $return[] = [
                'type'          => $tab['type'],
                'title'         => $tab['title'],
                'hash'          => \sanitize_title_with_dashes( $tab['title'] ),
                'content'       => $content // returned from switch ##
            ];

        }

        // kick it back ##
        return $return;

    }



    /**
    * Text Engine
    *
    * @since    2.0.0
    * @return   Mixed Boolean on error or HTML string
    */
    public static function type_text( $string = null ){

        // perhaps nothing to do here - or wpautop ##
        return $string;

    }



    /**
    * Blog Engine
    *
    * @since    2.0.0
    * @return   Mixed Boolean on error or HTML string
    */
    public static function type_blog( $array = null, $args ){

        // helper::log( $args['markup']['blog'] );
        // helper::log( $array );

        if (
            ! $the_post = wordpress::the_post()
        ) {

            helper::log( 'No post object...' );

            return false;

        }

        // new string ##
        $string = '';
        $category_url = '';

        // admin controls should ensure we have an array with at least 2 rows ##
        foreach( $array['blog'] as $row ) {

            // helper::log( $row['post']->ID );

            $markup = $args['markup']['blog']['row'];

            $data = [
                // 'faq'        => \sanitize_title_with_dashes( $row['title'] ), // current faq hash ##
                'url'           => \esc_html( \get_permalink( $row['post']->ID ) ),
                'title'         => \esc_html( $row['post']->post_title ),
                'href_title'    => \esc_html( \sanitize_title( $row['post']->post_title, 'Post', 'save' ) ),
                'content'       => \wpautop( preg_replace("/<img[^>]+\>/i", "(image) ", $row['post']->post_content ) ),
                'category'      => \esc_html( \get_the_category( $row['post']->ID )[0]->name ),
                'category_url'  => \esc_html( \get_category_link( \get_the_category( $row['post']->ID )[0]->term_id ) ),
                'author'        => \esc_html( \get_the_author_meta( 'display_name', \get_post_field ( 'post_author', $row['post']->ID ) ) ),
                'src'           =>  \has_post_thumbnail( $row['post']->ID ) ?
                                    \get_the_post_thumbnail( $row['post']->ID, $args['markup']['blog']['handle'][helper::get_device()] ) :
                                    ''
                // 'hash'       => \sanitize_title_with_dashes( $array['title'] ) // current tab hash ##
            ];

            if ( ! $category_url ) { $category_url = $data['category_url']; }

            // helper::log( $data );

            $string .= generic::markup( $markup, $data ); ;

        }

        // get wrapper ##
        $return = str_replace( '%row%', $string, $args['markup']['blog']['wrap'] );

        // category link ##
        $link = str_replace( '%category_url%', $category_url, $args['markup']['blog']['link'] );
        $return = str_replace( '%link%', $link, $return );


        // content ##
        $content = '';
        if( \have_rows( $args['markup']['blog']['parent'], $the_post->ID ) ):

            while ( \have_rows( $args['markup']['blog']['parent'], $the_post->ID ) ) :

                \the_row();

                $content = \get_sub_field( $args['markup']['blog']['content'], $the_post->ID );

            endwhile;

        endif;

        // description ##
        // helper::log( 'Field = '.$args['markup']['blog']['content'] );
        // helper::log( \get_sub_field( $args['markup']['blog']['content'], $the_post->ID ) );

        $return = str_replace( '%content%', $content, $return );

        // kick back ##
        return $return;

    }



    /**
    * Gallery Engine
    *
    * @since    2.0.0
    * @return   Mixed Boolean on error or HTML string
    */
    public static function type_gallery( $array = null, $args ){

        // helper::log( $args['markup']['gallery'] );
        // helper::log( $array );

        if (
            ! $the_post = wordpress::the_post()
        ) {

            helper::log( 'No post object...' );

            return false;

        }

        // stop here for now ##
        // return false;

        // new string ##
        $string = '';

        // admin controls should ensure we have an array with at least 2 rows ##
        foreach( $array['tab_special_gallery'] as $row ) {

            // helper::log( $row );

            // we need images for a gallery, so bulk if missing ##
            if ( ! isset( $row['sizes'][ $args['markup']['gallery']['handle']['large'][helper::get_device()] ] ) ) {

                helper::log( 'handle missing: '.$args['markup']['gallery']['handle']['large'][helper::get_device()] );

                continue;

            }

            $markup = $args['markup']['gallery']['row'];

            $data = [
                'tab'           => \sanitize_title( $array['title'] ), // remake tab title ##
                'key'           => \esc_html( \sanitize_title_with_dashes( $row['title'] ) ), // key for modal ##
                'title'         => \esc_html( $row['title'] ),
                'image_thumb'   => \esc_html( $row['sizes'][ $args['markup']['gallery']['handle']['thumb'][helper::get_device()] ] ),
                'image_large'   => \esc_html( $row['sizes'][ $args['markup']['gallery']['handle']['large'][helper::get_device()] ] ),
            ];

            // helper::log( $data );

            $string .= generic::markup( $markup, $data ); ;

        }

        // get wrapper ##
        $return = str_replace( '%row%', $string, $args['markup']['gallery']['wrap'] );

        // add content ##
        $return = str_replace( '%content%', $array['text'], $return );

        // kick back ##
        return $return;

    }



    /**
    * FAQ Engine
    *
    * @since    2.0.0
    * @return   Mixed Boolean on error or HTML string
    */
    public static function type_faq( $array = null, $args ){

        // helper::log( $array );

        // new string ##
        $string = '';

        // admin controls should ensure we have an array with at least 2 rows ##
        foreach( $array['faq'] as $row ) {

            // helper::log( $row );

            $markup = $args['markup']['faq']['row'];

            $data = [
                'faq'       => \sanitize_title( $row['title'] ), // current faq hash ##
                'url'       => \get_permalink( \get_the_ID() ),
                'title'     => $row['title'],
                'content'   => $row['content'],
                'hash'      => \sanitize_title_with_dashes( $array['title'] ) // current tab hash ##
            ];

            // helper::log( $data );

            $string .= generic::markup( $markup, $data ); ;

        }

        // get wrapper ##
        $return = str_replace( '%row%', $string, $args['markup']['faq']['wrap'] );

        // kick back ##
        return $return;

    }


    /**
    * Special Engine
    *
    * @since    2.0.0
    * @return   Mixed Boolean on error or HTML string
    */
    public static function type_special( $tab = null, $args = null ){

        // helper::log( 'filtering special tab: '.$tab['special'] );

        // filter value run via seperate plugins ##
        return \apply_filters( 'q/tab/special/'.$tab['special'], $args, $tab );

    }




    /**
     * Deal nicely with JS
     */
    public static function wp_footer()
    {

        javascript::ob_get([
            'view'      => get_class(),
            'method'    => 'javascript',
            'priority'  => 13,
            'handle'    => 'Tab'
        ]);

    }



    /**
    * JS for scroll UI
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function javascript( $args = null )
    {

?>
<script>

// jQuery ##
if ( typeof jQuery !== 'undefined' ) {

    // fully loaded ##
    jQuery( window ).bind( "load", function(){

        if( $the_hash = q_tab_hash() ) {

            q_tab( $the_hash );

        } else {

            q_tab_default();

        }

    });

    // ready ##
    jQuery(document).ready(function($) {

        // modern browsers hashchange event ##
        jQuery( window ).bind( 'hashchange', function( e ) {

            // console.log( 'Doing hash change for tabs...' );
            history.navigationMode = 'compatible';
            e.preventDefault();
            $the_hash = q_tab_hash();
            if( $the_hash ) q_tab( $the_hash );

        });

        // handheld, select change event ##
        jQuery('select.tab-navigation').on( 'change', function() {

            // console.dir( jQuery(this) );

            $trigger = jQuery(this).find( 'option:selected' ).attr( "data-tab-trigger" );

            // console.log( 'Trigger: '+ $trigger );

            // swap visible ##
            q_tab( $trigger );

            // compile ##
            $trigger = '#tab/'+$trigger

            // push state ##
            if( history.pushState ) {
                 history.pushState(null, null, $trigger );
            }
            else {
                 location.hash = $trigger ;
            }

        })

        // @todo - Viktor - pre-select select/option if hash set ##

        // hack to handle faqs
        $(document).on('click', '.faqs .q-tab-trigger', function() {
            var $tabActive = $('[data-tab-target="'+ $(this).data('tab-trigger') +'"]');

            if (!$(this).hasClass('q-tab-current')) {
                // handle faq open with the same hash
                if (q_get_hash_value_from_key('tab') === $(this).data('tab-trigger')) {
                    $(this).addClass('q-tab-current');
                    $tabActive.removeClass('q-tab-hidden').addClass('q-tab-current').show();
                    return;
                } else {
                    return;
                }
            }

            // handle faq close
            $(this).removeClass('q-tab-current');
            $tabActive.removeClass('q-tab-current').addClass('q-tab-hidden').hide();
        });
    });

    function q_tab_default(){

        // console.log( 'No hash..' );

        jQuery('.q-tab-target').hide().addClass('q-tab-hidden').removeClass('q-tab-current');
        jQuery('.q-tab-trigger').removeClass('q-tab-current');

        jQuery( '.q-tab-trigger:first-child' ).addClass('q-tab-current');
        jQuery( '.q-tab-target:first-child' ).removeClass('q-tab-hidden').addClass('q-tab-current').show();

    }

    function q_tab( data_id ){

        // @todo - early check if clicked tab is same as current - if so, bale ##

        // check if target exists ##
        $target = jQuery( "[data-tab-target='"+data_id+"']" );
        if( $target.length ){

            // console.log( 'data_id: '+ data_id ) ;
            // console.log( 'tab_target: '+ $target.data('tab-target') ) ;
            // console.log( 'q_tab, target found:' );

            // hide all targets ##
            jQuery('.q-tab-target').each( function(){

                // console.log( 'hide..' );
                jQuery(this).hide().addClass('q-tab-hidden').removeClass('q-tab-current');

            });

            // remove highlight from all triggers ##
            jQuery( ".q-tab-trigger" ).removeClass('q-tab-current');

            // show target ##
            $target.show().addClass('q-tab-current').removeClass('q-tab-hidden');
            jQuery( "[data-tab-trigger='"+data_id+"']" ).addClass('q-tab-current');

            // scroll to --- this should be removed.. ##
            // var targetOffset = ( $target.offset().top ) - 230;

            // scroll ##
            // jQuery('html,body').animate({
            //     scrollTop: targetOffset + "px"
            // }, 500, 'swing');

        };

    }

    /*
    Check for passed hash value
    */
    function q_tab_hash(){

        // get new hash string ##
        var $hash = q_get_hash_value_from_key( 'tab' );

        if ( ! $hash ) {

            // console.log( 'No toggle...' );

            return false;

        }

        return $hash;

    }

}
</script>
<?php

    }




    public static function wp_head()
    {

        css::ob_get([
            'view'      => get_class(),
            'method'    => 'css',
            'priority'  => 42,
            'handle'    => 'Tab'
        ]);

    }


    public static function get_markup()
    {

        // build defaults ##
        /* REVERTED Q-TAB TO ORIGINAL STATE 11-15-19 - Benny*/
        $array = [
            'default'       => '<div class="col-md-10 col-12 wysiwyg page-content">
                                    %string%
                                </div>',
            'navigation'    => [
                'wrap'      => [
                            'desktop' => '
                                <div class="p-3 col-lg-2 col-md-4 col-sm-12">
                                    <div class="myaffix">
                                        <ul class="nav flex-column" role="tablist">
                                            %row%
                                        </ul>
                                        <div id="program-button-container"></div>
                                    </div>
                                </div>',
                            'tablet' => '
                                <div class="col-sm-8">
                                    <select class="form-control tab-navigation nav-pills nav-stacked" role="tablist">%row%</select>
                                </div>
                                <div id="program-button-container" class="col-sm-4">
                                </div>
                            ',
                            'handheld' => '
                                <div class="col-12 col-xs-12 myaffix">
                                    <div class="row">
                                        <div class="col-12 col-xs-6">
                                            <select class="form-control tab-navigation nav-pills nav-stacked" role="tablist">%row%</select>
                                        </div>
                                        <div id="program-button-container" class="col-xs-6"></div>
                                    </div>
                                </div>
                            '
                            ],
                'row'       => [
                            'desktop' => '
                                <li data-tab-trigger="%hash%" class="q-tab-trigger nav-item">
                                    <a href="%url%#/tab/%hash%" class="nav-link">
                                        %title%
                                    </a>
                                </li>',
                            'tablet' => '
                                <option data-tab-trigger="%hash%" class="q-tab-trigger">
                                    %title%
                                </option>',
                            'handheld' => '
                                <option data-tab-trigger="%hash%" class="q-tab-trigger">
                                    %title%
                                </option>'
                            ],
            ],
            'content'       => [
                'wrap'      => '
                                <div class="col-lg-8 col-md-8 col-xs-12 col-12">
                                    <ul class="tab-content" data-scroll-slug="content">
                                        %row%
                                    </ul>
                                </div>
                                '
                            ,
                // 'cta'       => '<a class="btn btn-sm c-red apply-now" href="'.core::get_purl().'" class="apply r-desktop">Apply Now</a>',
                'row'       => '<li data-tab-target="%hash%" class="clearfix q-tab-target type-%type%"><h3>%title%</h3>%content%</li>'
            ],
            'text'          => [
                'wrap'      => '<div class="wysiwyg">%string%</div>' // text element wrapper ##
            ],
            'faq'           => [
                'wrap'      => '<div class="panel-group panel-group-default">%row%</div>',
                'row'       => '
                                    <h5 data-toggle="collapse" data-target="#%faq%" class="panel-heading">%title%</h5>
                                    <div id="%faq%" class="panel-collapse collapse"><div class="wysiwyg panel-body">%content%</div></div>
                                '
            ],
            'script'    	=> '
                                    <div class="wysiwyg">%string%</div>
                                    <div class="embed embed-script">%script%</div>
                                ',
            'gallery'       => [
                'wrap'      => '%content%<div class="gallery sly sly-mobile">
                                    <div class="row slidee">%row%</div>
                                </div>',
                'row'       => '
                                <div class="col-sm-3 p-3 item">
                                    <a class="q-gallery" href="%image_large%"><img src="" data-src="%image_thumb%" class="lazy" title="%title%" /></a>
                                </div>
                                ',
                'handle'    => [
                                'thumb' => [
                                    'handheld'  => 'handheld-thumb-gallery',
                                    'desktop'   => 'desktop-thumb-gallery'
                                ],
                                'large' => [
                                    'handheld'  => 'handheld-modal-gallery',
                                    'desktop'   => 'desktop-modal-gallery'
                                ]
                            ],


            ],
        ];

        // filter ##
        $array = \apply_filters( 'q/tab/markup/get', $array );

        // return ##
        return $array;

    }




    /**
     * Render inline CSS
     *
     * @since   2.0.0
     * @return  String
     */
    public static function css()
    {

?>
    <style>
    .q-tab-target:not(:first-child) {
        display: none;
    }
    .q-tab-hidden{
        display: none;
    }
    .q-tab-current{
        color: red;
    }
    </style>
<?php

    }


    public static function get_purl()
    {

        $string = '#';

        if ( class_exists( 'q\program\core\core' ) ) {

            $string = program_core::get_purl();

        }

        // filter ##
        $string = \apply_filters( 'q/tab/markup/get_purl', $string );

        // return ##
        return $string;

    }



    
    
    /**
     * Tab Filter - script - wysiwyg + textarea with script
     * Note: filters run via seperate class method, filters to only run on this current template
     *
     * @since   2.0.0
     * @return  String
     */
    public static function tab_special_script( $args, $tabs )
    {

        if (
            ! $the_post = wordpress::the_post() 
        ) {

            helper::log( 'No post object...' );

            return false;

        }

        // start with nada ##
        $content = '';

        // helper::log( $args );

        // get markup ##
        $content = $args['markup']['script'];

        // kill ##
        $content = 
            $tabs['text'] ? 
            str_replace( '%string%', $tabs['text'], $content ) : 
            false ;
        
        $content = 
            $tabs['tab_special_script'] ? 
            str_replace( '%script%', $tabs['tab_special_script'], $content ) : 
            false ;

        // test ##
        // helper::log( $content );

        // kick it back ##
        return $content;
 
    }



}