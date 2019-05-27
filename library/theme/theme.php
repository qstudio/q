<?php

namespace q\theme;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;
use q\core\wordpress as wordpress;

// load it up ##
\q\theme\theme::run();

class theme extends \Q {

    // public static $plugin_version;
    public static $plugin_version;
    public static $options;

    public static function run()
    {

        // load templates ##
        self::load_properties();

        if ( ! \is_admin() ) {

            // plugin css / js -- includes defaults and resets and snippets from controllers ##
            \add_action( 'wp_enqueue_scripts', array( get_class(), 'wp_enqueue_scripts_plugin' ), 1 );

            // plugins and enhanecments ##
            \add_action( 'wp_enqueue_scripts', array ( get_class(), 'wp_enqueue_scripts_general' ), 2 );

            // theme css / js -- theme assets loaded by q_theme ##

        }

        // load templates ##
        self::load_libraries();

    }


    /**
    * Load Properties
    *
    * @since        2.0.0
    */
    private static function load_properties()
    {

        // assign values ##
        self::$plugin_version = self::version ;

        // grab the options ##
        self::$options = options::get();
        #helper::log( self::$options );

    }



    /**
    * Load Libraries
    *
    * @since        2.0.0
    */
    private static function load_libraries()
    {

        // ui ##
        require_once self::get_plugin_path( 'library/theme/ui.php' );

        // render engines ##
        // require_once self::get_plugin_path( 'library/controller/javascript.php' );
        // require_once self::get_plugin_path( 'library/controller/css.php' );

        // // cookies ##
        // require_once self::get_plugin_path( 'library/controller/cookie.php' );
        
        // // minify ##
        // require_once self::get_plugin_path( 'library/controller/minifier.php' );

        // // UI controllers ##
        // require_once self::get_plugin_path( 'library/controller/navigation.php' );
        // require_once self::get_plugin_path( 'library/controller/generic.php' );

        // // UI / JS / AJAX features ##
        // require_once self::get_plugin_path( 'library/controller/modal.php' );
        // require_once self::get_plugin_path( 'library/controller/tab.php' );
        // require_once self::get_plugin_path( 'library/controller/select.php' );
        // require_once self::get_plugin_path( 'library/controller/scroll.php' );
        // require_once self::get_plugin_path( 'library/controller/push.php' );
        // require_once self::get_plugin_path( 'library/controller/filter.php' );
        // require_once self::get_plugin_path( 'library/controller/toggle.php' );
        // require_once self::get_plugin_path( 'library/controller/load.php' );

    }




    /**
    * include plugin assets
    *
    * @since        0.1.0
    * @note         This file contrains css / js pushed to files from controllers and is required ##
    * @return       __void
    */
    public static function wp_enqueue_scripts_plugin() {

        // helper::log( self::$options );
        // helper::log( 'debug set to: '. ( true === self::$debug ? 'True' : 'False' ) );

        if ( 
            isset( self::$options->plugin_css ) 
            && false === self::$debug 
        ) {

            \wp_register_style( 'q-theme-css', helper::get( "theme/css/q.theme.css", 'return' ), array(), self::$plugin_version, 'all' );
            \wp_enqueue_style( 'q-theme-css' );

        }

        if ( 
            isset( self::$options->plugin_css ) 
            // && false === self::$debug 
        ) {
            
            \wp_register_style( 'q-wordpress-css', helper::get( "theme/css/q.wordpress.css", 'return' ), array(), self::$plugin_version, 'all' );
            \wp_enqueue_style( 'q-wordpress-css' );

            \wp_register_style( 'q-wordpress-global-css', helper::get( "theme/css/q.global.css", 'return' ), array(), self::$plugin_version, 'all' );
            \wp_enqueue_style( 'q-wordpress-global-css' );

            \wp_register_style( 'q-theme', helper::get( "theme/scss/index.css", 'return' ), array(), self::$plugin_version, 'all' );
            \wp_enqueue_style( 'q-theme' );

        }

        if ( 
            isset( self::$options->plugin_js ) 
            && false === self::$debug 
        ) {

            // helper::log( 'Adding q.theme.js' );

            // add JS ## -- after all dependencies ##
            \wp_enqueue_script( 'q-theme-js', helper::get( "theme/javascript/q.theme.js", 'return' ), array( 'jquery' ), self::$plugin_version );
            
            // pass variable values defined in parent class ##
            \wp_localize_script( 'q-theme-js', 'q_theme_js', array(
                 'ajaxurl'           => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ), /*, 'https' */ ## add 'https' to use secure URL ##
                 'debug'             => self::$debug
            ));

        }


        // global JS ##
        if ( 
            isset( self::$options->plugin_js ) 
        ) {

            // helper::log( 'Adding q.global.js' );

            // add JS ## -- after all dependencies ##
            \wp_enqueue_script( 'q-global-js', helper::get( "theme/javascript/q.global.js", 'return' ), array( 'jquery' ), self::$plugin_version );

        }

    }





    /*
    * script enqueuer 
    *
    * @since  2.0
    */
    public static function wp_enqueue_scripts_general() {

        if ( ! \is_admin() ) { // probably not required ##

            global $q_browser; // get browser agent info ##
            #global $options; // load plugin options ##
            #wp_die(pr($options)); // test options ##

            // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions ##
            if (
                (
                    $q_browser
                    && is_array( $q_browser ) 
                )
                && ( 
                    $q_browser['type'] == 'ie8' 
                    || $q_browser['type'] == 'ie7' 
                    || $q_browser['type'] == 'ie6' 
                    && self::$options->plugin_js === TRUE 
                )
            ) {

                \wp_register_script( 'q-html5', helper::get( "theme/javascript/q.html5.js", 'return' ), array(), self::$plugin_version, 'all' );
                \wp_enqueue_script( 'q-html5' );

            }

            // add jquery ##
            \wp_enqueue_script( "jquery" );

            // Required for nested reply function that moves reply inline with JS ##
            if ( 
                \is_singular() 
                && \comments_open() 
                && \get_option( 'thread_comments' ) 
            ) {
            
                \wp_enqueue_script( 'comment-reply' ); // enqueue the javascript that performs in-link comment reply fanciness
            
            }

            // helper::log( self::$options->library );

            // loop over libraries and include - checking for "min" version is debugging ##
            foreach( self::$options->library as $key => $value ) {

                // helper::log( 'working: '.$key );

                // CSS or JS
                $type = explode( "_" , $key );

                // if no type - skip ##
                if ( 
                    ! is_array( $type ) 
                    || 2 > count( $type )
                ) {

                    // helper::log( 'Skipping: '.$key );

                    continue;

                }

                $type_dir = ( 'css' == $type[0] ) ? 'css' : 'javascript' ;
                $type_ext = ( 'css' == $type[0] ) ? 'css' : 'js' ;

                // give it a handle ##
                $handle = 'q_'.$key;

                // look for minified library ##
                $file = helper::get( "theme/".$type_dir."/".$type[1].".min.".$type_ext, 'return' );

                // if not debugging, check if we can find a non-min version ##
                if ( 
                    ( ! $file )
                    ||
                    (
                        self::$debug 
                        && helper::get( "theme/".$type_dir."/".$type[1].".".$type_ext, 'return' )
                    )
                ) {

                    $file = helper::get( "theme/".$type_dir."/".$type[1].".".$type_ext, 'return' ) ;

                }

                // if no type - skip ##
                if ( ! $file ) {

                    helper::log( 'Skipping: '.$handle.' - File missing...' );

                    continue;

                }

                // helper::log( 'Adding library: '.$handle.' with file: '.$file.' as type: '.$type_ext );

                // register and enqueue ##
                switch ( $type_ext ) {

                    case "css" :

                        \wp_register_style( $handle, $file, self::$plugin_version, 'all' );
                        \wp_enqueue_style( $handle );

                    break ;

                    case "js" :

                        \wp_register_script( $handle, $file, array(), self::$plugin_version, 'all' );
                        \wp_enqueue_script( $handle );

                    break ;

                }

            }

            /*
            // https://github.com/wilddeer/stickyfill 
            if ( isset( self::$options->library->stickyfill ) ) {
            
                \wp_register_script( 'q-stickyfill', helper::get( "theme/javascript/stickyfill.min.js", 'return' ), array(), self::$plugin_version, 'all' );
                \wp_enqueue_script( 'q-stickyfill' );

            }

            // snackbar ##
            // https://github.com/FezVrasta/snackbarjs
            if ( isset( self::$options->library->snackbar_js ) ) {

                // helper::log( 'Loading snackbar: '.helper::get( "theme/javascript/snackbar".( ! self::$debug ? '.min' : '' ).".js", 'return' ) );
                
                // jquery snackbar ##
                \wp_register_script( 'q-jquery-snackbar', helper::get( "theme/javascript/snackbar".( ! self::$debug ? '.min' : '' ).".js", 'return' ), array( 'jquery' ), self::$plugin_version, true );
                \wp_enqueue_script( 'q-jquery-snackbar' );

            }

            if ( isset( self::$options->library->snackbar_css ) ) {

                // helper::log( 'Loading snackbar: '.helper::get( "theme/javascript/snackbar".( ! self::$debug ? '.min' : '' ).".js", 'return' ) );
                
                \wp_register_style( 'q-snackbar', helper::get( "theme/css/snackbar".( ! self::$debug ? '.min' : '' ).".css", 'return' ), self::$plugin_version, 'all' );
                \wp_enqueue_style( 'q-snackbar' );

            }

            // bootstrap -- removed, but might be needed on older sites to patch ##
            if ( isset( self::$options->library->bootstrap ) ) {

//               \wp_register_style( 'bootstrap-grid', helper::get( "theme/css/bootstrap-grid.css", "return" ), array( 'theme-css' ), self::$plugin_version, 'all' );
//               \wp_enqueue_style( 'bootstrap-grid' );

            }

            // Hashchange - http://benalman.com/projects/jquery-hashchange-plugin/ ##
            if ( isset( self::$options->library->ba_hashchange) ) {

                // jquery bxslider ##
                \wp_register_script( 'q-jquery-ba-hashchange', helper::get( "theme/javascript/ba-hashchange.js", 'return' ), array( 'jquery' ), self::$plugin_version, true );
                \wp_enqueue_script( 'q-jquery-ba-hashchange' );  

            }
            
            
            // // Easy Tabs - http://os.alfajango.com/easytabs/ ##
            // if ( isset(self::$options->easy_tabs) && self::$options->easy_tabs === TRUE ) {

            //     \wp_register_script( 'jquery-easy-tabs', helper::get( "theme/javascript/easytabs".( ! self::$debug ? '.min' : '' ).".js", 'return' ), array( 'jquery', 'jquery-ba-hashchange' ), self::$plugin_version, true );
            //     \wp_enqueue_script( 'jquery-easy-tabs' );  

            // }
            
            
            // Tipsy - OG Version - http://onehackoranother.com/projects/jquery/tipsy/ ##
            // if ( isset(self::$options->tipsy) && self::$options->tipsy === TRUE ) {

            //     \wp_register_script( 'jquery-tipsy', helper::get( "theme/javascript/tipsy.js", 'return' ), array( 'jquery' ), self::$plugin_version, true );
            //     \wp_enqueue_script( 'jquery-tipsy' );  

            // }


            // Sly - http://darsa.in/sly/#!examples ##
            if ( isset( self::$options->library->sly ) ) {
                
                \wp_register_script( 'q-jquery-sly', helper::get( "theme/javascript/sly".( ! self::$debug ? '.min' : '' ).".js", 'return' ), array( 'jquery' ), self::$plugin_version, true );
                \wp_enqueue_script( 'q-jquery-sly' );  

            }
            

            // Lazy Load - http://eisbehr.de/lazy/ ##
            if ( isset( self::$options->library->lazy ) ) {
                
                // helper::log( 'Loading Lazy: '.helper::get( "theme/javascript/lazy".( ! self::$debug ? '.min' : '' ).".js", 'return' ) );

                \wp_register_script( 'q-jquery-lazy', helper::get( "theme/javascript/lazy".( ! self::$debug ? '.min' : '' ).".js", 'return' ), array( 'jquery' ), self::$plugin_version, false );
                \wp_enqueue_script( 'q-jquery-lazy' );  

            }



            // colorbox ##
            if ( isset( self::$options->library->colorbox_js ) ) {

                // colorbox js ##
                \wp_register_script( 'q-jquery-colorbox', helper::get( "theme/javascript/colorbox.js", 'return' ), array('jquery'),self::$plugin_version, true );
                \wp_enqueue_script( 'q-jquery-colorbox' );   

            }

            // colorbox ##
            if ( isset( self::$options->library->colorbox_css ) ) {

                // colorbox css ##
                \wp_register_style( 'q-colorbox', helper::get( "theme/css/colorbox.css", 'return' ), array( 'q-wordpress-css' ), self::$plugin_version, 'all' );
                \wp_enqueue_style( 'q-colorbox' );

            }

            // twitter ##
            if ( isset( self::$options->library->twitter ) ) {
                
                // twitter css ##
                \wp_register_style( 'q-twitter', helper::get( "theme/css/twitter.css", 'return' ),  '', self::$plugin_version, 'all' );
                \wp_enqueue_style( 'q-twitter' );

                // oauth library ##
                #helper::get( "theme/functions/q_twitter.php", 'return', true );

            }


            // masonry ##
            if ( isset( self::$options->library->masonry) ) {

                // isotope js ##
                // \wp_register_script( 'q-jquery-masonry', helper::get( "theme/javascript/masonry.js", 'return' ), array('jquery'), self::$plugin_version, true );
                // \wp_enqueue_script( 'q-jquery-masonry' );   


            } 
            
            // Hover Intent ##
            // http://cherne.net/brian/resources/jquery.hoverIntent.html
            // if ( isset( self::$options->library->hoverintent ) ) {

            //     // isotope js ##
            //     \wp_register_script( 'jquery-hoverintent', helper::get( "theme/javascript/hoverintent.js", 'return' ), array('jquery'), self::$plugin_version,true );
            //     \wp_enqueue_script( 'jquery-hoverintent' );   


            // } 

            // flickr ##
            if ( isset( self::$options->library->flickr ) ) {

                // flickr js ##
                \wp_register_script( 'q-jquery-flickr', helper::get( "theme/javascript/flickr.js", 'return' ), array('jquery'), self::$plugin_version, false );
                \wp_enqueue_script( 'q-jquery-flickr' );   

            }


            // Gravity Forms ##
            if ( 
                // wordpress::plugin_is_active( 'gravityforms/gravityforms.php' ) 
                // && 
                isset( self::$options->library->gravityforms )
            ) {

                \wp_register_style( 'q-gravityforms', helper::get( "theme/css/gravityforms.css", 'return' ), '', self::$plugin_version, 'all' );
                \wp_enqueue_style( 'q-gravityforms' );

            }

            // tubepress ##
            if ( 
                // ( 
                    // wordpress::plugin_is_active( 'tubepress/tubepress.php' ) 
                    // || wordpress::plugin_is_active( 'tubepress_pro/tubepress.php') 
                // ) 
                // && 
                isset( self::$options->library->tubepress )
            ) {

                \wp_register_style( 'q-tubepress', helper::get( "theme/css/tubepress.css", 'return' ), '', self::$plugin_version, 'all' );
                \wp_enqueue_style( 'q-tubepress' );

            }

            // mailchimp ##
            if ( 
                // wordpress::plugin_is_active( 'mailchimp/mailchimp.php' ) 
                // && 
                isset( self::$options->library->mailchimp ) 
            ) {

                // \wp_register_style( 'q-mailchimp', helper::get( "theme/css/mailchimp.css", 'return' ), '', self::$plugin_version, 'all' );
                // \wp_enqueue_style( 'q-mailchimp' );

            }

            // */
            
        }

    }

}