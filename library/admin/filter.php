<?php

namespace q\admin;

use q\plugin as q;
use q\core;
use q\core\helper as h;

// load it up ##
\q\admin\filter::run();

class filter {

    public static function run()
    {

        if ( \is_admin() ) {

            // filter admin preview link ##
            \add_filter( 'preview_post_link', [ get_class(), 'preview_post_link' ], 10, 2 );

            // Add Filter Hook
            \add_filter( 'post_mime_types', array( get_class(), 'post_mime_types' ) );

        }

    }




    /**
     * Fix for broken preview link in admin - link to default url ?p=ID
     * 
     * @since       0.1.0
     */
    public static function preview_post_link( $preview_link, $post ) 
    {

        if ( 
            \get_post_status ( $post->ID ) != 'draft' 
            && \get_post_status ( $post->ID ) != 'auto-draft' 
        ) {
      
            // preview URL for all published posts ##
            return \home_url()."?p=".$post->ID; 
      
         } else {
            
            // preview URL for all posts which are in draft ##
            return \home_url()."?p=".$post->ID; 

        }

    }



    
    
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
            \_n_noop( 'PDF <span class="count">(%s)</span>', 'PDFs <span class="count">(%s)</span>' )
        );

        // then we return the $post_mime_types variable
        return $post_mime_types;

    }



}
