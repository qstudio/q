<?php

namespace q\plugin;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;
use q\controller\generic as generic;
use q\core\wordpress as wordpress;

// load it up ##
\q\plugin\google::run();

class google extends \Q {

    public static function run()
    {
        
        if ( ! \is_admin() ) {

            // google analytics tracking code - add just before </head> ## 
            \add_action( 'wp_head', [ get_class(), 'analytics'], 100 );

            // define Google Tag Manager ##
            \add_action( 'wp_head', [ get_class(), 'tag_manager'], 100 );

            // add <noscript> after opening <body> tag ##
            \add_action( 'q_action_body_open', [ get_class(), 'tag_manager_noscript'], 2 );

        }

    }



    /**
     * Hook to enqueue Google ReCaptcha assets
     *
     * @since       1.0.1
     */
    public static function recaptcha_enqueue( $args = null )
    {

        $url = 'https://www.google.com/recaptcha/api.js';
        $url = 
            \add_query_arg([
                    #'onload'    => 'recaptchaCallback',
                    'render'    => 'explicit',
                    'hl'        => 'en'
                ]
                , $url 
            );
    
        // check ##
        // helper::log( $url );

        // add script ##
        \wp_register_script( 'google-recaptcha', $url, [], '2.0', false );
        \wp_enqueue_script( 'google-recaptcha' );

    }


    /**
     * Hook to set-up Google ReCaptcha form inline forms, not in modal - reverse hack...
     *
     * @since       2.4.9
     */
    public static function recaptcha_hook( Array $args = null )
    {

?>
    <script>
        // console.log( 'Hacking load count...' );
        $load_count = 2;
    </script>
<?php

    }



    /**
     * Hook to enqueue Google Maps assets
     *
     * @since       1.0.1
     */
    public static function map_enqueue()
    {

        // add action hook - wp_enqueue_scripts ##
        \add_action( 'wp_enqueue_scripts', array( get_class(), 'google_map_enqueue_hook' ), 2 );

    }



    /**
     * Enqueue assets for Google Maps
     *
     * @since       1.0.1
     */
    public static function map_enqueue_hook()
    {

        #<script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap"type="text/javascript"></script>

        #helper::log( 'adding Google Maps assets...' );

        \wp_register_script('google-maps', 'https://maps.googleapis.com/maps/api/js?key='.GOOGLE_MAPS_V3_API_KEY, false, '3');
        \wp_enqueue_script('google-maps');

        \wp_enqueue_script( 'acf-google-maps',  self::get_plugin_url( 'library/theme/javascript/acf-google-maps.js' ), array( 'jquery' ), self::version, true );

        \wp_register_style( 'q-map-css', helper::get( "theme/css/q.map.css", 'return' ), '', self::version, 'all' );
        \wp_enqueue_style( 'q-map-css' );

    }


    public static function map( $args = null )
    {

        // get location ##
        $location = \get_field( $args['field'] );
        #helper::log( $location );

        if( 
            ! empty( $location ) 
            && is_array( $location )
        ) {

            #helper::log( 'rending G Map..' );

?>
        <div class="acf-map <?php echo $args['class']; ?>">
            <div class="marker" data-lat="<?php echo $location['lat']; ?>" data-lng="<?php echo $location['lng']; ?>"></div>
        </div>
<?php 

        }

    }



    /**
     * Google Translate Widget
     *
     * @since       1.3.0
     * @return      String      HTML for translation widget
     */
    public static function translate()
    {

        // get the_post ##
        if ( ! $the_post = Q::the_post() ) { return false; }

        // check if the post has the translate option activated ##
        if ( 'true' !== \get_post_meta( $the_post->ID, 'template_translate', true ) ) { return false; }

        // still here - echo the translator ##
        echo \do_shortcode('[google-translator]');

    }



    


    /**
     * Add Google Tag Manager to <head>
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function tag_manager()
    {

        // bulk on localhost ##
        if ( helper::is_localhost() ) { 
        
            // helper::log( 'Tag Manager skipped, as on localhost...' );

            // return false; 
        
        }

        // check if consent given to load script ##
        if ( ! generic::consent( 'analytics' ) ) {

            // helper::log( 'Analytics NOT allowed...' );

            // kick out ##
            return false;

        }

        // grab the options ##
        $q_options = options::get();

        #helper::log( $q_options );

        // bulk if no options found ##
        if ( 
            ! $q_options 
            || ! is_object( $q_options )    
        ) {

            // helper::log( 'Error: Options missing...' );

            return false;

        }

        // check for UI ##
        if ( ! isset( $q_options->google_tag_manager ) ) { 

            // Log ##
            // helper::log( 'Google Tag Manager not defined' );

            // kick off ##
            return false; 

        }

        // kick it back, cleanly... ##
        echo $q_options->google_tag_manager;
        
    }




    
    /**
     * Add GTM noscript to the <body>
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function tag_manager_noscript()
    {

        // bulk on localhost ##
        if ( helper::is_localhost() ) { 
                
            // helper::log( 'Analytics skipped, as on localhost...' );

            // return false; 

        }

        // check if consent given to load script ##
        if ( ! generic::consent( 'analytics' ) ) {

            // helper::log( 'Analytics NOT allowed...' );

            // kick out ##
            return false;

        }

        // grab the options ##
        $q_options = options::get();

        #helper::log( $q_options );

        // bulk if no options found ##
        if ( 
            ! $q_options 
            || ! is_object( $q_options )    
        ) {

            // helper::log( 'Error: Options missing...' );

            return false;

        }

        // check for UI ##
        if ( ! isset( $q_options->google_tag_manager_noscript ) ) { 

            // Log ##
            // helper::log( 'Google Tag Manager No Script not defined' );

            // kick off ##
            return false; 

        }

        // kick it back, cleanly... ##
        echo $q_options->google_tag_manager_noscript;

    }




    /**
    * Get Google Analtics code for insertion in template - on GHT also includes Pardot tracking code
    *
    * @since       1.0.2
    * @return      string   HTML
    */
    public static function analytics()
    {

        // bulk on localhost ##
        if ( helper::is_localhost() ) { 
        
            // helper::log( 'Analytics skipped, as on localhost...' );

            // return false; 
        
        }

        // check if consent given to load script ##
        if ( ! generic::consent( 'analytics' ) ) {

            // helper::log( 'Analytics NOT allowed...' );

            // kick out ##
            return false;

        }

        // grab the options ##
        $q_options = options::get();

        #helper::log( $q_options );

        // bulk if no options found ##
        if ( 
            ! $q_options 
            || ! is_object( $q_options )    
        ) {

            // helper::log( 'Error: Options missing...' );

            return false;

        }

        // check for UI ##
        if ( ! isset( $q_options->google_analytics ) ) { 
        
            // Log ##
            // helper::log( 'Google Analytics not defined' );

            // kick off ##
            return false; 
        
        }

        // kick it back, cleanly... ##
        echo $q_options->google_analytics;

    }



}