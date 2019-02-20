<?php

namespace q\plugin;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;
use q\controller\generic as generic;

// load it up ##
\q\plugin\google::run();

class google extends \Q {

    public static function run()
    {
        
        if ( ! \is_admin() ) {

            // define Google Tag Manager ##
            \add_action( 'wp_head', [ get_class(), 'tag_manager'], 10 );

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
    * Get Google Analtics code for insertion in template - on GHT also includes Pardot tracking code
    *
    * @since       1.0.2
    * @return      string   HTML
    */
    public static function analytics()
    {

        // bulk on localhost ##
        if ( helper::is_localhost() ) { return false; }

        // @todo - add consent checks ##

        // grab global ##
        global $post;

        // grab the options ##
        $q_options = options::get();

        #helper::log( $q_options );

        // bulk if no options found ##
        if ( 
            ! $q_options 
            || ! is_array( $q_options )    
        ) {

            helper::log( 'Error: Options missing...' );

            return false;

        }

        // check for UI ##
        if ( ! $analytics = $q_options["google_analytics"] ) { return false; }

        // check for custom GA code additions ##
        $google_analytics = isset( $post->google_analytics ) ? $post->google_analytics : false ;

?>
        <script type="text/javascript">
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            // create event listener ##
            ga('create', '<?php echo $analytics; ?>', '<?php echo parse_url( get_site_url(), PHP_URL_HOST ); ?>');
            
            <?php echo $google_analytics; ?>
            
            // send to GA ##
            ga('send', 'pageview');
        </script>
<?php

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
            
            return false; 
            
        }

        // @todo - add consent checks ##

        // check if we have tag_manager defined in config ##
        if ( 
            ! self::$google_tag_manager 
        ) {

            helper::log( 'Google Tag Manager not defined in config' );

            return false;

        }

        // kick it back ##
        echo self::$google_tag_manager;

    }




     /**
     * Get Google Adwords code for insertion in template
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function adwords()
    {

        // bulk on localhost ##
        if ( helper::is_localhost() ) { return false; }

        // @todo - add consent checks ##

        // get the_post ##
        if (
            ! $the_post = wordpress::the_post()
        ) {

            return false;

        }

        // check for q_program and that we're on a "thanks" page ##
        if (
            ! class_exists( 'q_program' )
            || 'thanks' != \q_program::get_qpage()
        ) {

            return false;

        }

        // check for adwords markup ##
        if (
            ! $template_adwords_markup = $the_post->template_adwords_markup
        ) {

            return false;
            
        }

        // print filtered markup ##
        #pr( wp_kses_allowed_html() );
        echo $template_adwords_markup;

    }



}