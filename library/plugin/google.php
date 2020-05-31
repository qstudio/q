<?php

namespace q\plugin;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;
use q\controller\consent as consent;
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

        } else {

            // add fields to Q settings ##
            \add_filter( 'q/core/options/add_field/analytics', [ get_class(), 'filter_acf_analytics' ], 10, 1 );

        }

    }



    public static function filter_acf_analytics( $array ) 
    {

        // test ##
        // helper::log( $array );

        // lets add our fields ##
        array_push( $array['fields'], [

            'key' => 'field_q_option_google_analytics',
            'label' => 'Google Analytics',
            'name' => 'q_option_google_analytics',
            'type' => 'textarea',
            'instructions' => 'Enter the complete Google Analytics snippet',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'maxlength' => '',
            'rows' => 3,
            'new_lines' => '',
        
        ]);

        array_push( $array['fields'], [

            'key' => 'field_q_option_google_tag_manager',
            'label' => 'Google Tag Manager',
            'name' => 'q_option_google_tag_manager',
            'type' => 'textarea',
            'instructions' => 'Enter the complete Google Tag Manager snippet',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'maxlength' => '',
            'rows' => 4,
            'new_lines' => '',

        ]);

        array_push( $array['fields'], [

            'key' => 'field_q_option_google_tag_manager_noscript',
            'label' => 'Google Tag Manager Noscript',
            'name' => 'q_option_google_tag_manager_noscript',
            'type' => 'textarea',
            'instructions' => 'Enter the complete Google Tag Manager noscript snippet',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'maxlength' => '',
            'rows' => 4,
            'new_lines' => '',
            
        ]);

        // helper::log( $array['fields'] );

        // kick it back, as it's a filter ##
        return $array;

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

        // filter $args ##
        $args = \apply_filters( 'q/google/recaptcha/hook', $args );

        // sanity ##
        if ( 
            is_null( $args )
        ){

            helper::log( 'Args empty..' );

            // nada ##
            return false;

        }

        // helper::log( $args );

        // load count ##
        $load_count = isset( $args['load_count'] ) ? intval( $args['load_count'] ) : 1 ; 

?>
    <script>
        console.log( 'Hacking load count...' );
        $load_count = <?php echo $load_count ?>;
    </script>
<?php

    }



    

    public static function fonts( $fonts, $use_fallback = true, $debug = false ) 
    {

        // if debugging, use &lt; and $gt; notation for output as plain text
        // otherwise, use < and > for output as html
        $debug ? $x = array('&lt;', '&gt;') : $x = array('<', '>');
        // create a new font array
        $array = array();
        // create a new fallback array for storing possible fallback urls
        $fallback_urls = array();
        // determine how many fonts are requested by checking if the array key ['name'] exists
        // if it exists, push that single font into the $array variable
        // otherwise, just copy the $fonts variable to $array
        isset($fonts['name']) ? array_push($array, $fonts) : $array = $fonts;
        // request the link for each font
        foreach ($array as $font) {
    
            // set the basic url
            $base_url = 'https://fonts.googleapis.com/css?family=' . str_replace(' ', '+', $font['name']) . ':';
            $url = $base_url;
            // create a new array for storing the font weights
            $weights = array();
            // if the font weights are passed as a string (from which all spaces will be removed), insert each value into the $weights array
            // otherwise, just copy the weights passed
            if(isset($font['weight'])) {
                gettype($font['weight']) == 'string' ? $weights = explode(',', str_replace(' ', '', $font['weight'])) : $weights = $font['weight'];
            // if font weights aren't defined, default to 400 (normal weight)
            } else {
                $weights = array('400');
            }
            // add each weight to $url and remove the last comma from the url string
            foreach($weights as $weight) {
                $url .= $weight . ',';
                // if the fallback notation is necessary, add a single weight url to the fallback array
                if($use_fallback && count($weights) != 1) array_push($fallback_urls, "$base_url$weight");
            }
            $url = substr_replace($url, '', -1);
            // normal html output
            echo $x[0] . 'link href="' . $url . '" rel="stylesheet" type="text/css" /' . $x[1] . "\n";
    
        }
        // add combined conditional comment for each font weight if necessary
        if ( $use_fallback && !empty( $fallback_urls ) ) {

            // begin conditional comment
            echo $x[0] . '!--[if lte IE 8]' . $x[1] . "\n";
            // add each fallback url within the conditional comment
            foreach($fallback_urls as $fallback_url) {
                echo '  ' . $x[0] . 'link href="' . $fallback_url . '" rel="stylesheet" type="text/css" /' . $x[1] . "\n";
            }
            // end conditional comment
            echo $x[0] . '![endif]--' . $x[1] . "\n";
            
        }
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
        // if ( helper::is_localhost() ) { 
        
        //     // helper::log( 'Tag Manager skipped, as on localhost...' );

        //     // return false; 
        
        // }

        // check if consent given to load script ##
        if ( ! consent::given( 'analytics' ) ) {

            // helper::log( 'Analytics NOT allowed...' );

            // kick out ##
            return false;

        }

        // grab the options ##
        // $q_options = options::get();

        #helper::log( $q_options );

        // bulk if no options found ##
        if ( 
            ! options::get( 'google_tag_manager' )
        ) {

            // helper::log( 'Error: Options missing...' );

            return false;

        }

        // // check for UI ##
        // if ( ! isset( $q_options->google_tag_manager ) ) { 

        //     // Log ##
        //     // helper::log( 'Google Tag Manager not defined' );

        //     // kick off ##
        //     return false; 

        // }

        // kick it back, cleanly... ##
        echo options::get( 'google_tag_manager' );
        
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
        // if ( helper::is_localhost() ) { 
                
        //     // helper::log( 'Analytics skipped, as on localhost...' );

        //     // return false; 

        // }

        // check if consent given to load script ##
        if ( ! consent::given( 'analytics' ) ) {

            // helper::log( 'Analytics NOT allowed...' );

            // kick out ##
            return false;

        }

        // grab the options ##
        // $q_options = options::get();

        #helper::log( $q_options );

        // bulk if no options found ##
        if ( 
            ! options::get( 'google_tag_manager_noscript' )
        ) {

            // helper::log( 'Error: Options missing...' );

            return false;

        }

        // // check for UI ##
        // if ( ! isset( $q_options->google_tag_manager_noscript ) ) { 

        //     // Log ##
        //     // helper::log( 'Google Tag Manager No Script not defined' );

        //     // kick off ##
        //     return false; 

        // }

        // kick it back, cleanly... ##
        echo options::get( 'google_tag_manager_noscript' );

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
        // if ( helper::is_localhost() ) { 
        
        //     // helper::log( 'Analytics skipped, as on localhost...' );

        //     // return false; 
        
        // }

        // check if consent given to load script ##
        if ( ! consent::given( 'analytics' ) ) {

            // helper::log( 'Analytics NOT allowed...' );

            // kick out ##
            return false;

        }

        // helper::log( options::get( 'google_analytics' ) );

        // grab the options ##
        // $q_options = options::get();

        #helper::log( $q_options );

        // bulk if no options found ##
        if ( 
            ! options::get( 'google_analytics' )
        ) {

            // helper::log( 'Error: Options missing...' );

            return false;

        }

        // kick it back, cleanly... ##
        echo options::get( 'google_analytics' );

    }



}