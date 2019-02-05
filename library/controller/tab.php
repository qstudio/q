<?php

namespace q\controller;

use q\core\core as core;
use q\core\helper as helper;
use q\core\config as config;
use q\controller\generic as generic;
use q\controller\javascript as javascript;
use q\controller\css as css;

// load it up ##
\q\controller\tab::run();

class tab extends \Q {
    
    public static $args = [];

    public static function run()
    {

        // add JS to footer if debugging or single q.theme.js script if not ##
        \add_action( 'wp_footer', [ get_class(), 'wp_footer' ], 5 ); 

        // add css ##
        // \add_action( 'wp_head', [ get_class(), 'wp_head' ], 4 ); 

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
        // helper::log( $array );

        // kick back ##
        return $array;

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
            helper::log( 'No tabs found for this post - return all post content' );

            // return ##
            // return false;

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

            // get the post content ##
            $string = \apply_filters( 'the_content', \get_post_field( 'post_content', \get_the_ID() ) ); 

            // apply some basic markup ##
            $string = str_replace( '%string%', $string, $args['markup']['default'] );

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
        return \apply_filters( 'q/tab/special/'.$tab['special'], $args );

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
    jQuery(document).ready(function() {

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



    /**
     * Render inline CSS
     * 
     * @since   2.0.0
     * @return  String
     */
    public static function css(  )
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



}