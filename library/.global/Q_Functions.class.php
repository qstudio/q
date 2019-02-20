<?php

/**
 * Q Admin Class
 * 
 * @since       1.0
 * @Author:     Q Studio
 * @URL:        http://qstudio.us/
 */

if ( ! class_exists ( 'Q_Theme_Functions' ) ) 
{

    // instatiate class via WP admin_init hook ##
    add_action( 'after_setup_theme', array ( 'Q_Theme_Functions', 'init' ), 0 );
    
    // Q_Admin Class
    class Q_Theme_Functions extends Q
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
        

        public function __construct()
        {
            
            #if ( is_admin() ) {
            
                // Add Support for WordPress 3.0 features ##
                add_action( "after_setup_theme", array( $this, 'add_wp_support' ), 1 );
                
                // add parent scripts - removable by framework options page ##
                #add_action( 'wp_enqueue_scripts', array ( $this, 'parent_wp_enqueue_scripts' ), 100000 );
                
            #}
            
        }
        
        
        
        /**
         * Add Support for WordPress 3.0 features 
         * 
         * @since       1.2.0
         * @return      void
         */
        public function add_wp_support( )
        {
            
            // add support for all required post types here ##
            add_theme_support( 'post-thumbnails' );
            
            // rss thingy ##
            add_theme_support('automatic-feed-links');

            // adding post format support
            /*
            add_theme_support( 'post-formats',
                array(
                    'aside',             // title less blurb
                    'gallery',           // gallery of images
                    'link',              // quick link to other site
                    'image',             // an image
                    'quote',             // a quick quote
                    'status',            // a Facebook like status update
                    'video',             // video
                    'audio',             // audio
                    'chat'               // chat transcript
                )
            );
            */

            // wp menus
            add_theme_support( 'menus' );
            
            // add excerpt to pages ##
            add_post_type_support( 'page', 'excerpt' );
            
        }
        
        
        
        
        /**
         * include extra parent scripts
         * 
         * @since       1.2.0
         * @return      void
         * @todo        Remove global variable ##
         */
        public function parent_wp_enqueue_scripts() 
        {

            global $q_options;

            // jQuery initilisation -- best to leave this one in place !! ##
            if ( $q_options->parent_js === TRUE ) {

                wp_register_script( 'q-parent-scripts', q_locate_template( "javascript/scripts.js", false ), array( 'jquery' ),'0.9',true );
                wp_enqueue_script( 'q-parent-scripts' );

                // lozalize script ##
                /*
                $translation_array = array( 
                        'open' => __( 'Open' ), 
                        'close' => __( 'Close' ), 
                    );
                wp_localize_script( 'q_scripts', 'object_name', $translation_array );
                */

            }

        }
        
        
        
        
        /**
         * Get Featured Image Settings
         * 
         * @todo        Add matching setter function 
         * @since       1.2.0
         * @return      Object
         */
        public static function get_featured_image( )
        {
        
            // rules for including featured image on single.php ##
            $q_featured_image = array(
                'class'     => 'aligncenter', // image css class ##
                'small'     => 'slides', // small image size ##
                'large'     => 'full', // full image size ##
                'link'      => 'image', // image href - link to image or attachment ##
            );
            
            return q_array_to_object($q_featured_image); // convert array to an object ##
            
        }
        
        
        

    }


}

// Flush menu cache if menus are changed
if( isset($_POST['action']) && isset($pagenow) && $pagenow === 'nav-menus.php' ){
    array_map( 'unlink', glob(__DIR__ . '/cache/'.'*.html.cache') );
}
