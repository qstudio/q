<?php

/**
 * Q Admin Class
 * 
 * @since       1.0
 * @Author:     Q Studio
 * @URL:        http://qstudio.us/
 */

if ( ! class_exists ( 'Q_Admin' ) ) 
{

    // instatiate class via WP admin_init hook ##
    add_action( 'init', array ( 'Q_Admin', 'init' ), 1 );
    
    // Q_Admin Class
    class Q_Admin extends Q
    {

        /**
        * Creates a new instance.
        *
        * @wp-hook      init
        * @see          __construct()
        * @since        0.1
        * @return       void
        */
        public static function init() 
        {
            new self;
        }
        
        /**
         * Class Constructor
         * 
         * @since       1.2.0
         * @return      void
         */
        public function __construct()
        {
            
            if ( is_admin() ) {
            
                // set-up admin image sizes ##
                add_action( "admin_init", array( $this, 'admin_setup_images' ) );
                
                // add thumbnails to admin columns ##
                add_action( 'admin_init', create_function( '', Q_Admin::add_thumbnail_to( array( 'posts', 'pages' ) ) ) );
                
            } else {

               #self::log( 'Here..' );

                // admin menus - front-end ##
                add_action( 'admin_bar_menu', array ( $this, 'admin_bar_menu_device' ), 1000 );

            }
            
        }
        
        
        /**
         * Add Thumbnail Column to Post Type in admin
         * 
         * @since       1.2.0
         * @param       Array    $post_types
         */
        public static function add_thumbnail_to( $post_types = null )
        {
            
            // sanity check ##
            if ( ! $post_types ) { return false; } // nothing to do ##
            
            // make sure this is only loaded up in the admin ##
            if ( is_admin() ) {
                
                foreach ( $post_types as $post_type ) {
                    
                    if ( post_type_supports( $post_type, 'thumbnail' ) ) {
                    
                        // add thumbnails for post_type ##
                        add_filter( "manage_{$post_type}_columns", array( 'Q_Admin', 'admin_add_thumbnail_column' ) );
                        add_action( "manage_{$post_type}_custom_column", array( 'Q_Admin', 'admin_add_thumbnail_value' ), 10, 2 );

                    }
                    
                }
                
            }
            
        }
        
        
        /**
         * Add thumbnail column
         * 
         * @param       Array    $cols
         * @return      Array
         */
        public static function admin_add_thumbnail_column( $cols ) 
        {
            
            $cols['thumbnail'] = __('Thumbnail');
            return $cols;
            
        }

        
        /**
         * Add row thumbnail value 
         * 
         * @param type $column_name
         * @param type $post_id
         */
        public static function admin_add_thumbnail_value( $column_name, $post_id ) 
        {

            $width = (int) 200;
            $height = (int) 125;

            if ( 'thumbnail' == $column_name ) {
                // thumbnail of WP 2.9
                $thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
                // image from gallery
                $attachments = get_children( array('post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image') );
                if ( $thumbnail_id ) {
                    #$thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
                    #echo $thumbnail_id;
                    $thumb = wp_get_attachment_image( $thumbnail_id, 'admin-list-thumb', true );
                } elseif ($attachments) {
                    foreach ( $attachments as $attachment_id => $attachment ) {
                        #$thumb = wp_get_attachment_image( $attachment_id, array($width, $height), true );
                        $thumb = wp_get_attachment_image( $attachment_id, 'admin-list-thumb', true );
                    }
                }
                if ( isset($thumb) && $thumb ) {
                    echo $thumb;
                }
            }
        }
        
        
        /**
         * Set-up image sizes in WP admin 
         * 
         * @since       1.2.0
         * @return      void
         */
        public static function admin_setup_images( )
        {
        
            // default thumb size in admin ##
            set_post_thumbnail_size( 260, 200, true );

            // this theme uses post thumbnails - set the sizes below ##
            add_image_size( 'admin-list-thumb', 60, 40, true ); // admin thumbs ##
            add_image_size( 'dashboard', 100, 40, true );
            
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
                #,   array( 'id' => 'dts-switch-tablet', 'title' => __( 'Tablet', self::text_domain ), 'url' => '?theme=tablet' )
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

}

// Flush menu cache if menus are changed
if( isset($_POST['action']) && isset($pagenow) && $pagenow === 'nav-menus.php' ){
    array_map( 'unlink', glob(__DIR__ . '/cache/'.'*.html.cache') );
}
