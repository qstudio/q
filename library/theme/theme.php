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

            // theme css / js -- @note theme assets loaded by q_theme ##
            // \add_action( 'wp_enqueue_scripts', array ( get_class(), 'wp_enqueue_scripts_theme' ), 11 );

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
        #self::$theme_version = \wp_get_theme()->get( 'Version' ) ? \wp_get_theme()->get( 'Version' ) : self::version ;

        // grab the options ##
        self::$options = core::array_to_object( options::get() );
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
        require_once self::get_plugin_path( 'library/controller/javascript.php' );
        require_once self::get_plugin_path( 'library/controller/css.php' );

        // cookies ##
        require_once self::get_plugin_path( 'library/controller/cookie.php' );
        
        // minify ##
        require_once self::get_plugin_path( 'library/controller/minifier.php' );

        // UI controllers ##
        require_once self::get_plugin_path( 'library/controller/navigation.php' );
        require_once self::get_plugin_path( 'library/controller/generic.php' );

        // UI / JS / AJAX features ##
        require_once self::get_plugin_path( 'library/controller/modal.php' );
        require_once self::get_plugin_path( 'library/controller/tab.php' );
        require_once self::get_plugin_path( 'library/controller/select.php' );
        require_once self::get_plugin_path( 'library/controller/scroll.php' );
        require_once self::get_plugin_path( 'library/controller/push.php' );
        require_once self::get_plugin_path( 'library/controller/filter.php' );
        require_once self::get_plugin_path( 'library/controller/toggle.php' );
        require_once self::get_plugin_path( 'library/controller/load.php' );

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

        if ( 
            isset( self::$options->plugin_css ) 
            && false === self::$debug 
        ) {
            \wp_register_style( 'q-wordpress-css', helper::get( "theme/css/q.wordpress.css", 'return' ), array(), self::$plugin_version, 'all' );
            \wp_enqueue_style( 'q-wordpress-css' );

            \wp_register_style( 'q-theme-css', helper::get( "theme/css/q.theme.css", 'return' ), array(), self::$plugin_version, 'all' );
            \wp_enqueue_style( 'q-theme-css' );

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
    public static function wp_enqueue_scripts_theme() {
        
        if ( TRUE === self::$options->theme_css ) {

            if ( 
                defined( 'Q_CHILD_THEME' )
                && Q_CHILD_THEME
                #&& \is_child_theme()   
            ) {

                // add parent css ##
                \wp_register_style( 'theme-parent-css', \get_template_directory_uri() . '/style.css', '', self::$plugin_version );
                \wp_enqueue_style( 'theme-parent-css' );

            }

            #helper::log( get_stylesheet_directory() . '/style.css' );

            // add css ##
            \wp_register_style( 'theme-css', \get_stylesheet_directory_uri() . '/style.css', '', self::$plugin_version );
            \wp_enqueue_style( 'theme-css' );

            // load site/device.css - re: theme/css/q.1.desktop.css
            // @TODO helper::get_device() @viktor
            // $handle = "q.".\get_current_blog_id().".".helper::get_device().".css";
            $handle = "q.".\get_current_blog_id().".".helper::get_device().".css";

            // first check if the file exists ##
            if ( $file = helper::get( "theme/sass/".$handle, "return" ) ) {

                // helper::log( 'Loading up file: '.$file );

                \wp_register_style( 'q-theme-style', $file, '', self::$plugin_version );
                \wp_enqueue_style( 'q-theme-style' );

            } else {

                $handle = "q.1.theme.css";
                $file = helper::get( "theme/sass/".$handle, "return" );

                // helper::log( 'Loading up file: '.$file );

                \wp_register_style( 'q-theme-style', $file, '', self::$plugin_version );
                \wp_enqueue_style( 'q-theme-style' );

                // helper::log( "Cannot locate file: 'theme/sass/".$handle."' - using default" );

            }

        }

        // load site/device.js - re: theme/javascript/q.1.desktop.js
        if ( TRUE === self::$options->theme_js ) {

            \wp_register_script( 'theme-js', helper::get( "theme/javascript/scripts.js", 'return' ), array( 'jquery' ), self::$plugin_version, true );
            \wp_enqueue_script( 'theme-js' );

            // pass variable values defined in parent class ##
            \wp_localize_script( 'theme-js', 'q_theme', array(
                'ajaxurl'           => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ), /*, 'https' */ ## add 'https' to use secure URL ##
                'debug'             => self::$debug
            ));

            // load site/device.css - re: theme/css/q.1.desktop.js
            $handle = "q.".\get_current_blog_id().".".helper::get_device().".js";

            if ( ! helper::get( "theme/javascript/".$handle, "return" ) ) {

                $handle = "q.1.".helper::get_device().".js";

            }

            // first check if the file exists ##
             if ( $file = helper::get( "theme/javascript/".$handle, "return" ) ) {

                // helper::log( 'Loading up file: '.$file );

                \wp_register_script( $handle, $file, array( 'jquery' ), self::$plugin_version, true );
                \wp_enqueue_script( $handle );

                // nonce ##
                $nonce = \wp_create_nonce( 'q-'.\get_current_blog_id().'-nonce' );

                // pass variable values defined in parent class ##
                \wp_localize_script( $handle, 'q_'.\get_current_blog_id(), array(
                    'ajaxurl'           => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ), /*, 'https' */ ## add 'https' to use secure URL ##
                    'debug'             => self::$debug,
                    'nonce'             => $nonce
                ));

             } else {

                helper::log( "Cannot locate file: theme/javascript/".$handle );

             }

        }

    }


    /*
    * script enqueuer 
    *
    * @since  2.0
    */
    public static function wp_enqueue_scripts_general() {

        if ( ! is_admin() ) { // probably not required ##

            global $q_browser; // get browser agent info ##
            #global $options; // load plugin options ##
            #wp_die(pr($options)); // test options ##

            \wp_register_script( 'stickyfill', helper::get( "theme/javascript/stickyfill.min.js", 'return' ), array(), self::$plugin_version, 'all' );
            \wp_enqueue_script( 'stickyfill' );

            // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions ##
            if ( $q_browser['type'] == 'ie8' || $q_browser['type'] == 'ie7' || $q_browser['type'] == 'ie6' && self::$options->plugin_js === TRUE ) {

                \wp_register_script( 'html5', helper::get( "theme/javascript/q.html5.js", 'return' ), array(), self::$plugin_version, 'all' );
                \wp_enqueue_script( 'html5' );

            }

            // add jquery ##
            // could be loaded from google to improve caching.. ##
            // http://www.wpbeginner.com/wp-themes/replace-default-wordpress-jquery-script-with-google-library/
            \wp_enqueue_script( "jquery" );

            // Required for nested reply function that moves reply inline with JS ##
            if ( \is_singular() && \comments_open() && \get_option( 'thread_comments' ) ) {
                \wp_enqueue_script( 'comment-reply' ); // enqueue the javascript that performs in-link comment reply fanciness
            }

            // snackbar ##
            // https://github.com/FezVrasta/snackbarjs
            if ( isset( self::$options->snackbar ) && self::$options->snackbar === TRUE ) {

                #helper::log( 'Loading snackbar...' );
                // helper::log( helper::get( "theme/css/jquery.snackbar.min.css", "return" ) );

                // jquery snackbar ##
                \wp_register_script( 'q-jquery-snackbar', helper::get( "theme/javascript/jquery.snackbar".( ! self::$debug ? '.min' : '' ).".js", 'return' ), array( 'jquery' ), self::$plugin_version, true );
                \wp_enqueue_script( 'q-jquery-snackbar' );

                \wp_register_style( 'q-snackbar', helper::get( "theme/css/jquery.snackbar".( ! self::$debug ? '.min' : '' ).".css", 'return' ), array( 'theme-css' ), self::$plugin_version, 'all' );
                \wp_enqueue_style( 'q-snackbar' );

            }

            // bootstrap ##
            if ( isset(self::$options->bootstrap) && self::$options->bootstrap === TRUE ) {

//               \wp_register_style( 'bootstrap-grid', helper::get( "theme/css/bootstrap-grid.css", "return" ), array( 'theme-css' ), self::$plugin_version, 'all' );
//               \wp_enqueue_style( 'bootstrap-grid' );

            }

            // Hashchange - http://benalman.com/projects/jquery-hashchange-plugin/ ##
            if ( isset( self::$options->ba_hashchange) && self::$options->ba_hashchange === TRUE ) {

                // jquery bxslider ##
                \wp_register_script( 'jquery-ba-hashchange', helper::get( "theme/javascript/jquery.ba-hashchange.js", 'return' ), array( 'jquery' ), self::$plugin_version, true );
                \wp_enqueue_script( 'jquery-ba-hashchange' );  

            }
            
            
            // Easy Tabs - http://os.alfajango.com/easytabs/ ##
            if ( isset(self::$options->easy_tabs) && self::$options->easy_tabs === TRUE ) {

                \wp_register_script( 'jquery-easy-tabs', helper::get( "theme/javascript/jquery.easytabs".( ! self::$debug ? '.min' : '' ).".js", 'return' ), array( 'jquery', 'jquery-ba-hashchange' ), self::$plugin_version, true );
                \wp_enqueue_script( 'jquery-easy-tabs' );  

            }
            
            
            // Tipsy - OG Version - http://onehackoranother.com/projects/jquery/tipsy/ ##
            if ( isset(self::$options->tipsy) && self::$options->tipsy === TRUE ) {

                \wp_register_script( 'jquery-tipsy', helper::get( "theme/javascript/jquery.tipsy.js", 'return' ), array( 'jquery' ), self::$plugin_version, true );
                \wp_enqueue_script( 'jquery-tipsy' );  

            }


            // Sly - http://darsa.in/sly/#!examples ##
            if ( isset(self::$options->sly) && self::$options->sly === TRUE ) {
                
                \wp_register_script( 'jquery-sly', helper::get( "theme/javascript/jquery.sly".( ! self::$debug ? '.min' : '' ).".js", 'return' ), array( 'jquery' ), self::$plugin_version, true );
                \wp_enqueue_script( 'jquery-sly' );  

            }
            

            // Lazy Load - http://jquery.eisbehr.de/lazy/ ##
            if ( isset(self::$options->lazy) && self::$options->lazy === TRUE ) {
                
                // helper::log( 'Loading Lazy: '.helper::get( "theme/javascript/jquery.lazy".( ! self::$debug ? '.min' : '' ).".js", 'return' ) );

                \wp_register_script( 'jquery-lazy', helper::get( "theme/javascript/jquery.lazy".( ! self::$debug ? '.min' : '' ).".js", 'return' ), array( 'jquery' ), self::$plugin_version, false );
                \wp_enqueue_script( 'jquery-lazy' );  

            }



            // colorbox ##
            if ( isset( self::$options->colorbox ) && self::$options->colorbox === TRUE ) {

                // colorbox js ##
                \wp_register_script( 'jquery-colorbox', helper::get( "theme/javascript/jquery.colorbox.js", 'return' ), array('jquery'),self::$plugin_version, true );
                \wp_enqueue_script( 'jquery-colorbox' );   

                // colorbox css ##
                \wp_register_style( 'q-colorbox', helper::get( "theme/css/jquery.colorbox.css", 'return' ), array( 'q-wordpress-css' ), self::$plugin_version, 'all' );
                \wp_enqueue_style( 'q-colorbox' );

            }

            // twitter ##
            if ( isset( self::$options->twitter ) && self::$options->twitter === TRUE ) {
                
                // twitter css ##
                \wp_register_style( 'q-twitter', helper::get( "theme/css/jquery.twitter.css", 'return' ),  '', self::$plugin_version, 'all' );
                \wp_enqueue_style( 'q-twitter' );

                // oauth library ##
                #helper::get( "theme/functions/q_twitter.php", 'return', true );

            }


            // masonry ##
            if ( isset(self::$options->masonry) && self::$options->masonry === TRUE ) {

                // isotope js ##
                \wp_register_script( 'jquery-masonry', helper::get( "theme/javascript/jquery.masonry.js", 'return' ), array('jquery'), self::$plugin_version, true );
                \wp_enqueue_script( 'jquery-masonry' );   


            } 
            
            // Hover Intent ##
            // http://cherne.net/brian/resources/jquery.hoverIntent.html
            if ( isset(self::$options->hoverintent) && self::$options->hoverintent === TRUE ) {

                // isotope js ##
                \wp_register_script( 'jquery-hoverintent', helper::get( "theme/javascript/jquery.hoverintent.js", 'return' ), array('jquery'), self::$plugin_version,true );
                \wp_enqueue_script( 'jquery-hoverintent' );   


            } 

            // flickr ##
            if ( isset(self::$options->flickr) && self::$options->flickr === TRUE ) {

                // flickr js ##
                \wp_register_script( 'jquery-flickr', helper::get( "theme/javascript/jquery.flickr.js", 'return' ), array('jquery'), self::$plugin_version, false );
                \wp_enqueue_script( 'jquery-flickr' );   

            }


            // Gravity Forms ##
            if ( wordpress::plugin_is_active( 'gravityforms/gravityforms.php' ) && self::$options->plugin_css === TRUE ) {

                \wp_register_style( 'q-gravityforms', helper::get( "theme/css/plugin.gravityforms.css", 'return' ), '', self::$plugin_version, 'all' );
                \wp_enqueue_style( 'q-gravityforms' );

            }

            // tubepress ##
            if ( ( wordpress::plugin_is_active( 'tubepress/tubepress.php' ) || wordpress::plugin_is_active( 'tubepress_pro/tubepress.php') ) && self::$options->plugin_css === TRUE ) {

                \wp_register_style( 'q-tubepress', helper::get( "theme/css/plugin.tubepress.css", 'return' ), '', self::$plugin_version, 'all' );
                \wp_enqueue_style( 'q-tubepress' );

            } // tubepress ##

            // mailchimp ##
            if ( wordpress::plugin_is_active( 'mailchimp/mailchimp.php' ) && self::$options->plugin_css === TRUE ) {

                \wp_register_style( 'q-mailchimp', helper::get( "theme/css/plugin.mailchimp.css", 'return' ), '', self::$plugin_version, 'all' );
                \wp_enqueue_style( 'q-mailchimp' );

            } // mailchimp ##

            
        }

    }

}