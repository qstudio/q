<?php

namespace q\core;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;

// load it up ##
\q\core\config::run();

class config extends \Q {

    public static function run()
    {

        // filter intermediate image sizes ##
        \add_filter( 'intermediate_image_sizes_advanced', array ( get_class(), 'intermediate_image_sizes_advanced' ) );

        // filter meta title ##
        \add_filter( 'wp_title', array ( get_class(), 'wp_title' ), 10, 2 );
        
        // add_image_sizes for all themes ##
        \add_action( 'init', array( get_class(), 'add_image_sizes' ) );

        if ( \is_admin() ) {

            // Add Filter Hook
            \add_filter( 'post_mime_types', array( get_class(), 'post_mime_types' ) );

        } else {


        }

        // make sure properties are loaded when AJAX requests run ##
        if ( \wp_doing_ajax() ) {

            // self::load_properties();

        }

        // remove admin color schemes - silly idea ##
        \remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

        // sharelines widget ##
        // \add_filter( 'q/widget/sharelines/facebook', function() { return '137150683665520'; } ); // APP ID ##

    }



    /*
    public static function google_tag_manager()
    {

        self::$google_tag_manager = '
            <script async src="https://www.googletagmanager.com/gtag/js?id=UA-44211158-1"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag(\'js\', new Date());

                gtag(\'config\', \'UA-44211158-1\');
            </script>
        ';

    }
    */


    /*
    public static function fb_pixel()
    {

        self::$fb_pixel = '
        <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version=\'2.0\';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window,document,\'script\',
            \'https://connect.facebook.net/en_US/fbevents.js\');
            fbq(\'init\', \'511950592603984\'); 
            fbq(\'track\', \'PageView\');
        </script>
        <noscript>
            <img height="1" width="1" 
            src="https://www.facebook.com/tr?id=511950592603984&ev=PageView
            &noscript=1"/>
        </noscript>
        ';

    }
    */

    


    
    /**
     * Add filters to WP Media Library
     *
     * @since       1.4.2
     * @return      Array
     */
    public static function post_mime_types( $post_mime_types )
    {

        // select the mime type, here: 'application/pdf'
        // then we define an array with the label values
        $post_mime_types['application/pdf'] = array(
            __( 'PDF' ),
            __( 'Show PDF' ),
            _n_noop( 'PDF <span class="count">(%s)</span>', 'PDFs <span class="count">(%s)</span>' )
        );

        // then we return the $post_mime_types variable
        return $post_mime_types;

    }



    /**
     * Filters the page title appropriately depending on the current page
     * This function is attached to the 'wp_title' filter hook.
     *
     * @uses	get_bloginfo()
     * @uses	is_home()
     * @uses	is_front_page()
     * 
     * @since       0.1
     */
    public static function wp_title( $title, $sep ) {

        global $page, $paged, $post;
        
        $page_title = $title;

        // helper::log( $page_title );
            
        // get site desription ##
        $site_description = \get_bloginfo( 'description' );
        
        if ( $post ) { 

            // allow for custom title - via post meta "metatitle" ##
            $page_title = \get_post_meta( $post->ID, "metatitle", true ) ? \get_post_meta( $post->ID, "metatitle", true ).' '.$sep. ' ' : $title;
            
            // if this is a singular post - but not of type page or post add post type name as parent ##
            if ( 
                \is_singular( \get_post_type() ) 
                && \get_post_type() !== 'post' 
                && \get_post_type() !== 'page' 
            ) {
                
                if ( $obj = \get_post_type_object( \get_post_type() ) ) {
                
                    $page_title = $page_title.' '.$obj->labels->menu_name.' '.$sep.' ';

                }
                
            }
            
            // add parent page, if page ##
            if ( 
                $post->post_parent && 
                $post->post_type === 'page' 
                && ! \is_search() 
            ) {

                if ( $get_post_ancestor = \get_post_ancestors( $post->ID ) ) {

                    $page_title = $page_title.' '.\get_the_title( array_pop( $get_post_ancestor ) ).' '.$sep.' ';

                }

            }
            
        }
        
        // if we're on a single category check if that page has a parent ##
        if ( \is_archive() ) {

            $term = \get_term_by( 'slug', \get_query_var( 'term' ), \get_query_var( 'taxonomy' ) );

            if ( 
                $term 
                // && $term->parent > 0 
            ) {

                // helper::log( 'Archive title' );

                // just use the term name ##
                $page_title = $term->name.' '.$sep.' ';

                // // get parent name ##
                // $term_parent = \get_term_by( 'ID', $term->parent, \get_query_var( 'taxonomy' ) ) ;

                // if ( $term_parent && $term_parent->name ) {

                //     $page_title .= $term_parent->name.' '.$sep.' ';

                // }

            }

        }
        
        // compile ##
        $page_title = $page_title . \get_option( 'blogname' ); // with site name ##
        #$filtered_title = $page_title; // without site name ##
        
        // add site description if not empty and on front page ##
        $page_title .= ( ! empty( $site_description ) && ( \is_front_page() ) ) ? ' | ' . $site_description : '' ;
        
        // add paging number, if paged ##
        $page_title .= ( 2 <= $paged || 2 <= $page ) ? ' | ' . sprintf( __( 'Page %s' ), max( $paged, $page ) ) : '' ;

        // helper::log( $page_title );

        // return title ##
        return $page_title;

    }




    /**
     * Remove standard image sizes so that these sizes are not
     * created during the Media Upload process
     *
     * Tested with WP 3.2.1
     *
     * Hooked to intermediate_image_sizes_advanced filter
     * See wp_generate_attachment_metadata( $attachment_id, $file ) in wp-admin/includes/image.php
     *
     * @param $sizes, array of default and added image sizes
     * @return $sizes, modified array of image sizes
     * @author http://www.wpmayor.com/code/remove-image-sizes-in-wordpress/
     */
    public static function intermediate_image_sizes_advanced( $sizes)
    {

        unset( $sizes['slides']);
        unset( $sizes['slides-small']);
        unset( $sizes['home']);
        unset( $sizes['new-photos']);
        unset( $sizes['hero']);

        return $sizes;

    }



    /**
     * Add image sizes for all devices - so that all device images sizes are prepared when files are uploaded
     * Note: Tablet uses desktop sized images
     *
     * @since       0.1
     * @return      void
     */
    public static function add_image_sizes()
    {

        // generic ##
        \add_image_size( 'icon', 80, 80, false ); // icon ##
        \add_image_size( 'thumb', 270, 9999, false ); // small thumb ##

        // generic ##
        \add_image_size( 'thumb', 194, 97, true ); // small thumb ##

    }



    public static function list_image_sizes()
    {

        global $_wp_additional_image_sizes; 
        if( self::$debug ) helper::log( $_wp_additional_image_sizes ); 

    }






}

