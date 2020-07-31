<?php

namespace q\admin;

use q\core;
use q\core\helper as h;

// load it up ##
\q\admin\action::run();

class action extends \Q {

    public static function run()
    {

        if ( \is_admin() ) {

            // admin js ##
            \add_action( 'admin_enqueue_scripts', array( get_class(), 'admin_enqueue_scripts' ), 1 );

            // set-up admin image sizes ##
            \add_action( "admin_init", array( get_class(), 'admin_setup_images' ) );
                
            // add theme support ##
            \add_action( 'init', array( get_class(), 'add_support' ) );
                
        }

        // remove admin search bar ##
        // \add_action( 'admin_bar_menu', array( get_class(), 'admin_bar_menu' ), 999 );   

        // remove admin color schemes - silly idea ##
        \remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

	}



	

    /**
    * include plugin admin assets
    *
    * @since        0.1.0
    * @return       __void
    */
    public static function admin_enqueue_scripts() {

        // add JS ## -- after all dependencies ##
        \wp_enqueue_script( 'q-admin-js', h::get( "ui/javascript/admin/global.js", 'return' ), array( 'jquery' ), self::version );

        // nonce ##
        $nonce = \wp_create_nonce( 'q-admin-nonce' );

        // pass variable values defined in parent class ##
        \wp_localize_script( 'q-admin-js', 'q_admin', array(
            'ajaxurl'           => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ), ## add 'https' to use secure URL ##
            'debug'             => self::$debug,
            'nonce'             => $nonce
        ));

        // add snackbar CSS ##
        \wp_register_style( 'q-snackbar-admin', h::get( "ui/css/vendor/snackbar.min.css", 'return' ), '', self::version, 'all' );
        \wp_enqueue_style( 'q-snackbar-admin' );

        // add snackbar JS ##
        \wp_register_script( 'q-snackbar-admin', h::get( "ui/javascript/vendor/snackbar.min.js", 'return' ), array( 'jquery' ), self::version );
        \wp_enqueue_script( 'q-snackbar-admin' );

    }




    /**
     * Set-up image sizes in WP admin 
     * 
     * @since       1.2.0
     * @return      void
     */
    public static function admin_setup_images()
    {
    
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
    public static function add_support()
    {

        // add thumbnails ##
        \add_theme_support( 'post-thumbnails' );

        // default Post Thumbnail dimensions
        \set_post_thumbnail_size( 194, 97 );

    }





    /**
    * Remove admin bar search, as gives SSl Error
    *
    * @return      void
    */
    public static function admin_bar_menu( $wp_admin_bar )
    {

        $wp_admin_bar->remove_node( 'search' );
        $wp_admin_bar->remove_node( 'customize' );
        $wp_admin_bar->remove_node( 'my-account' );

    }




    /**
    * Remove unrequired menu items
    *
    * @since    2.0.0
    * @return   _false
    */
    public static function remove_menus()
    {

        \remove_menu_page( 'edit.php?post_type=ai_galleries' );       

    }


    


    /**
    * Add Admin Bar menu item ##
    *
    * @since      1.0.1
    * @link       http://blog.rutwick.com/add-items-anywhere-to-the-wp-3-3-admin-bar
    * @return     void
    */
    public function admin_bar_menu_device( $admin_bar )
    {

        #self::log( 'AdminBar..' );

        // check plugin is active ##
        if ( 
            function_exists('is_plugin_active') && 
            ! is_plugin_active( "device-theme-switcher/dts_controller.php" ) 
        ) {

            return false;

        }

        global $current_user;

        if ( ! $current_user->has_cap( 'manage_options' ) ) {

            return false;

        }

        $args = array(
            'id'        => 'dts-switch'
            ,'title'    => 'Theme'
            ,'href'     => '#'
            ,'meta'     => array(
                            'title' => __('Theme')
                        )
        );

        // parent menu ##
        $admin_bar->add_menu( $args);

        // array of children menu items ##
        $children = array(
                array( 'id' => 'dts-switch-handheld', 'title' => __( 'Handheld', 'q-textdomain' ), 'url' => '?theme=handheld' )
            ,   array( 'id' => 'dts-switch-tablet', 'title' => __( 'Tablet', 'q-textdomain' ), 'url' => '?theme=tablet' )
            ,   array( 'id' => 'dts-switch-desktop', 'title' => __( 'Desktop', 'q-textdomain'), 'url' => '?theme=desktop' )
            #,   array( 'id' => 'dts-switch-low-support', 'title' => __('Low Support', self::text_domain ), 'url' => '?theme=low_support' )
        );

        // get the current URL ##
        #global $wp;
        #$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );

        // loop over array and add each child item ##
        foreach ( $children as $child ) {

            $args = array(
                'id'        => $child["id"]
                ,'title'    => $child["title"]
                ,'href'     => $child["url"]
                ,'meta'     => array(
                                'title' => $child["title"]
                            )
                ,'parent'   => 'dts-switch'
            );

            // child menu items##
            $admin_bar->add_menu( $args);

        }

    }



}
