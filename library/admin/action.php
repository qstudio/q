<?php

namespace q\admin;

use q\core;
use q\core\helper as h;

// load it up ##
\q\admin\action::__run();

class action {

    public static function __run(){

        if ( \is_admin() ) {

            // set-up admin image sizes ##
            \add_action( "admin_init", array( get_class(), 'admin_setup_images' ) );
                
            // add theme support ##
            \add_action( 'init', array( get_class(), 'add_support' ) );
                
        }

        // remove admin color schemes - silly idea ##
        \remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

	}

    /**
     * Set-up image sizes in WP admin 
     * 
     * @since       1.2.0
     * @return      void
     */
    public static function admin_setup_images()    {
    
        // default thumb size in admin ##
        \set_post_thumbnail_size( 260, 200, true );

        // this theme uses post thumbnails - set the sizes below ##
        \add_image_size( 'admin-list-thumb', 60, 40, true ); // admin thumbs ##
        \add_image_size( 'dashboard', 100, 40, true );
        
    }
    
    /**
     * Adds Support for shared Q features.
     *
     * @since       0.1
     * @return      void
     */
    public static function add_support(){

        // add thumbnails ##
        \add_theme_support( 'post-thumbnails' );

        // default Post Thumbnail dimensions
        \set_post_thumbnail_size( 194, 97 );

    }

    /**
    * Remove unrequired menu items
    *
    * @since    2.0.0
    * @return   _false
    */
    public static function remove_menus(){

        \remove_menu_page( 'edit.php?post_type=ai_galleries' );       

    }

}
