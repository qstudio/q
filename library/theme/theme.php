<?php

namespace q\theme;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;
use q\core\wordpress as wordpress;

// Q Theme ##
use q\theme\core\helper as theme_helper;

// load it up ##
\q\theme\theme::run();

class theme extends \Q {

    // public static $plugin_version;
    public static $plugin_version;
    public static $options;

    public static function run()
    {

        // check we have dependencies ##
        if ( ! self::has_dependencies() ){

            return false;

        }

        // load templates ##
        self::load_properties();

        if ( ! \is_admin() ) {

            // plugin css / js -- includes defaults and resets and snippets from controllers ##
            \add_action( 'wp_enqueue_scripts', array( get_class(), 'wp_enqueue_scripts_plugin' ), 1 );

            // plugins and enhanecments ##
            \add_action( 'wp_enqueue_scripts', array ( get_class(), 'wp_enqueue_scripts_general' ), 2 );

            // local external scripts ##
            \add_action( 'wp_enqueue_scripts', array ( get_class(), 'wp_enqueue_scripts_external' ), 3 );

            // local optional scripts ##
            \add_action( 'wp_enqueue_scripts', array ( get_class(), 'wp_enqueue_scripts_local' ), 4 );

            // theme css / js from q_theme ##
            \add_action( 'wp_enqueue_scripts', array ( get_class(), 'wp_enqueue_scripts_theme' ), 10000 );

        }

        // load templates ##
        self::load_libraries();

    }


    

    /**
     * Check for required classes to build UI features
     * 
     * @return      Boolean 
     * @since       0.1.0
     */
    public static function has_dependencies()
    {

        // check for what's needed ##
        if (
            ! class_exists( 'q_theme' )
        ) {

            helper::log( 'Q requires q_theme to run correctly..' );

            return false;

        }

        // ok ##
        return true;

    }



    /**
    * Load Properties
    *
    * @since        2.0.0
    */
    private static function load_properties()
    {

        // assign values ##
        // self::$plugin_version = self::version ;

        // grab the options ##
        self::$options = options::get();
        // helper::log( self::$options );

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
            ( 
                isset( self::$options->plugin_css ) 
                && 1 == self::$options->plugin_css
            )
            && false === self::$debug 
        ) {

            \wp_register_style( 'q-plugin-css-theme', theme_helper::get( "theme/css/q.theme.css", 'return' ), array(), self::version, 'all' );
            \wp_enqueue_style( 'q-plugin-css-theme' );

        }

        if ( 
            isset( self::$options->plugin_css ) 
            && 1 == self::$options->plugin_css
            // && false === self::$debug 
        ) {
            
            // add compiled sass file ##
           # \wp_register_style( 'q-plugin-index-scss', helper::get( "theme/scss/index.css", 'return' ), array(), self::version, 'all' );
           # EDIT 1/11/20 - Placing the index file in the CSS folder means not maintaining separate image & assets folders for CSS
            \wp_register_style( 'q-plugin-index-scss', helper::get( "theme/css/index.css", 'return' ), array(), self::version, 'all' );
            \wp_enqueue_style( 'q-plugin-index-scss' );

        }

        if ( 
            (
                isset( self::$options->plugin_js ) 
                && 1 == self::$options->plugin_js
            )
            && false === self::$debug 
        ) {

            // helper::log( 'Adding q.theme.js' );

            // add JS ## -- after all dependencies ##
            \wp_enqueue_script( 'q-plugin-js-theme', theme_helper::get( "theme/javascript/q.theme.js", 'return' ), array( 'jquery' ), self::version );
            
            // pass variable values defined in parent class ##
            \wp_localize_script( 'q-plugin-js-theme', 'q_theme_js', array(
                 'ajaxurl'           => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ), /*, 'https' */ ## add 'https' to use secure URL ##
                 'debug'             => self::$debug
            ));

        }


    }





    /*
    * script enqueuer 
    *
    * @since  2.0
    */
    public static function wp_enqueue_scripts_general() {

        global $q_browser; // get browser agent info ##

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

            \wp_register_script( 'q-html5', helper::get( "theme/javascript/q.html5.js", 'return' ), array(), self::version, 'all' );
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

    }



    
    
    /*
    * script enqueuer 
    *
    * @since  2.0
    */
    public static function wp_enqueue_scripts_external() {

        // dump - shuold be an interger repesenting how many external libraries are added ##
        // helper::log( self::$options->external );
        // helper::log( \get_field( 'q_option_external', 'option' ) );

        /*
        [external] => 1
        [external_0_title] => Font Awesome
        [external_0_type] => css
        [external_0_url] => https://use.fontawesome.com/releases/v5.5.0/css/all.css
        [external_0_version] => 5.5.0
        */

        // sanity check ##
        if ( 
            ! isset( self::$options->external )
            || 1 > self::$options->external
        ){

            // helper::log( 'No external libraries to load' );

            return false;

        }

        // our query returns all items are single properties of the $options object - so, let's make an array ##
        if( \have_rows( 'q_option_external', 'option' ) ) {

            while( \have_rows( 'q_option_external', 'option' ) ) {
                
                // set things up ##
                \the_row(); 

                // properties ##
                $type = \get_sub_field('type');
                $title = \get_sub_field('title');
                $version = \get_sub_field('version');
                $url = \get_sub_field('url');

                // external libraries are saved in an array with "type", "title", "version" and "url" ##
                // foreach( self::$options->external as $key ) {

                // helper::log( 'working External: '.$title );

                // sanitize title to handle ##
                $handle = \sanitize_key( $title );

                // validate URL ##

                // debug ##
                // helper::log( 'Adding external library: '.$handle.' version '.$version.' from url: '.$url.' as type: '.$type );

                // register and enqueue ##
                switch ( $type ) {

                    case "css" :

                        \wp_register_style( $handle, $url, '', $version, 'all' );
                        \wp_enqueue_style( $handle );

                    break ;

                    case "js" :

                        \wp_register_script( $handle, $url, array(), $version, 'all' );
                        \wp_enqueue_script( $handle );

                    break ;

                }

            }

        } else {

            // helper::log( 'No external libraries to load...' );

        }

    }




    
    /*
    * script enqueuer 
    *
    * @since  2.0
    */
    public static function wp_enqueue_scripts_local() {

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
            $handle = 'q-'.$key;

            // template hierarchy ##

            // check for minified file in Q Theme ##
            if ( 
                self::$debug
                && theme_helper::get( "theme/".$type_dir."/".$type[1].".".$type_ext, 'return' )
            ) {

                $file = theme_helper::get( "theme/".$type_dir."/".$type[1].".".$type_ext, 'return' ) ;

                // helper::log( 'DEUBBING - Adding '.$type_dir.'/'.$type[1].'.min.'.$type_ext.' from Q Theme' ) ;

            // load minified version from Q Theme ##
            } else if (
                theme_helper::get( "theme/".$type_dir."/".$type[1].".min.".$type_ext, 'return' ) 
            ) {

                $file = theme_helper::get( "theme/".$type_dir."/".$type[1].".min.".$type_ext, 'return' ) ;

                // helper::log( 'Adding '.$type_dir.'/'.$type[1].'.min.'.$type_ext.' from Q Theme' );

            // check for non-minified version in Q Theme, if debugging ##
            } else if ( 
                self::$debug
                && helper::get( "theme/".$type_dir."/".$type[1].".".$type_ext, 'return' ) 
            ) {

                $file = helper::get( "theme/".$type_dir."/".$type[1].".".$type_ext, 'return' );

                // helper::log( 'DEUBBING - Adding '.$type_dir.'/'.$type[1].'.'.$type_ext.' from Q' );
                
            // load minified version from Q ## 
            } else if ( helper::get( "theme/".$type_dir."/".$type[1].".min.".$type_ext, 'return' ) ) {

                $file = helper::get( "theme/".$type_dir."/".$type[1].".min.".$type_ext, 'return' ) ;

                // helper::log( 'Adding '.$type_dir.'/'.$type[1].'.min.'.$type_ext.' from Q' );

            // final fallback - non minified on Q ##
            } else {

                $file = helper::get( "theme/".$type_dir."/".$type[1].".".$type_ext, 'return' );

                // helper::log( 'Final fallback - Adding '.$type_dir.'/'.$type[1].'.'.$type_ext.' from Q' );

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

                    \wp_register_style( $handle, $file, '', self::version, 'all' );
                    \wp_enqueue_style( $handle );

                break ;

                case "js" :

                    \wp_register_script( $handle, $file, array(), self::version, 'all' );
                    \wp_enqueue_script( $handle );

                break ;

            }

        }

    }





    /*
    * script enqueuer -- loaded from q_theme
    *
    * @since  2.0
    */
    public static function wp_enqueue_scripts_theme() 
    {

        if ( 
            isset( self::$options->theme_css ) 
            && 1 == self::$options->theme_css
        ) {

            // helper::log( 'Running CSS...' );

            // _deprecated -- add theme css ##
            // \wp_register_style( 'theme-css', \get_stylesheet_directory_uri() . '/style.css', '', self::version );
            // \wp_enqueue_style( 'theme-css' );

            // IE ##
            if ( theme_helper::get( "theme/css/ie.css", "return" ) ) {
         
                \wp_enqueue_style( 'q-ie', theme_helper::get( "theme/css/ie.css", "return" ), '', \q_theme::version );
                \wp_style_add_data( 'q-ie', 'conditional', 'IE' );

            }

            // helper::log( 'Device: '.helper::get_device() );

            // css hierarchy ----
            
            // q_theme/library/theme/css/q.2.desktop.css ##
            // q_theme/library/theme/css/q.2.theme.css ##
            // q_theme/library/theme/q.1.theme.css ##
            $handle = "q.".\get_current_blog_id().".".helper::get_device().".css";

            // helper::log( 'Handle: '.$handle );
            
            // first check if the file exists ##
            if ( $file = theme_helper::get( "theme/css/".$handle, "return" ) ) {

                // helper::log( '#1 : Loading up file: '.$file );

                \wp_register_style( $handle, $file, '', \q_theme::version );
                \wp_enqueue_style( $handle );

            // // edge-case, we are viewing a tablet and there is no tablet theme.. so fallback to desktop ##
            // } else if ( 
            //     'tablet' == $handle
            //     && ! theme_helper::get( "theme/css/".$handle, "return" )
            //     && theme_helper::get( "theme/css/q.".\get_current_blog_id().".desktop.css", "return" ) 
            // ) {

            //     $file = theme_helper::get( "theme/css/q.".\get_current_blog_id().".desktop.css", "return" ) ;

            //     // helper::log( '#2 : Loading up file: '.$file );

            //     \wp_register_style( $handle, $file, '', \q_theme::version );
            //     \wp_enqueue_style( $handle );

            // check for theme generic or backup ##
            } else {

                $handle = "q.".\get_current_blog_id().".theme.css";

                if ( $file = theme_helper::get( "theme/css/".$handle, "return" ) ) {

                    // helper::log( '#3 : Loading up file: '.$file );
    
                    \wp_register_style( $handle, $file, '', \q_theme::version );
                    \wp_enqueue_style( $handle );

                } else {

                    $handle = "q.1.theme.css";

                    $file = theme_helper::get( "theme/css/".$handle, "return" );

                    // if we can't find that.. log an error ##
                    if ( ! $file ) {

                        // helper::log( 'Error: cannot load '.$handle );

                    } else {

                        // helper::log( '#4 : Loading up file: '.$file );

                        \wp_register_style( $handle, $file, '', \q_theme::version );
                        \wp_enqueue_style( $handle );

                    }

                }

            }

        }

        // Q Check if we need to include SCSS Modules 
        // based on binary switch per install found in Q Settings self::$options->theme_css_scss ##
        if ( 
            isset( self::$options->theme_scss ) 
            && 1 == self::$options->theme_scss    
        ) {

            // helper::log( 'Running SCSS...' );

            // IE ##
            if ( theme_helper::get( "theme/scss/ie.css", "return" ) ) {
         
                \wp_enqueue_style( 'q-ie-scss', theme_helper::get( "theme/scss/ie.css", "return" ), '', \q_theme::version );
                \wp_style_add_data( 'q-ie-scss', 'conditional', 'IE' );

            }

            // q_theme/library/theme/scss/q.2.index.css ##
            // q_theme/library/theme/scss/q.1.index.css ##
            $handle = "q.".\get_current_blog_id().".index.css";

           # if ( $file = theme_helper::get( "theme/scss/".$handle, "return" ) ) {
           # EDIT 1/11/20 - Placing the index file in the CSS folder means not maintaining separate image & assets folders for CSS

            if ( $file = theme_helper::get( "theme/css/".$handle, "return" ) ) {
                // helper::log( 'Loading up file: '.$file );

                \wp_register_style( $handle, $file, '', \q_theme::version, 'all' );
                \wp_enqueue_style( $handle );

            } else {

                $handle = "q.1.index.css";

                #$file = theme_helper::get( "theme/scss/".$handle, "return" );
                # EDIT 1/11/20 - Placing the index file in the CSS folder means not maintaining separate image & assets folders for CSS

                $file = theme_helper::get( "theme/css/".$handle, "return" );

                // if we can't find that.. log an error ##
                if ( ! $file ) {

                    // helper::log( 'Error: cannot load '.$handle );

                } else {

                    // helper::log( '#4 : Loading up file: '.$file );

                    \wp_register_style( $handle, $file, '', \q_theme::version, 'all' );
                    \wp_enqueue_style( $handle );

                }

            }

        }

        // load generic JS from theme/javascript/scripts.js
        if ( 
            isset( self::$options->theme_js ) 
            && 1 == self::$options->theme_js
        ) {

            \wp_register_script( 'theme-js', theme_helper::get( "theme/javascript/scripts.js", 'return' ), array( 'jquery' ), \q_theme::version, true );
            \wp_enqueue_script( 'theme-js' );

            // pass variable values defined in parent class ##
            \wp_localize_script( 'theme-js', 'q_theme', array(
                'ajaxurl'           => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ), /*, 'https' */ ## add 'https' to use secure URL ##
                'debug'             => self::$debug
            ));

            // load site/device.css - re: theme/javascript/q.1.desktop.js
            $handle = "q.".\get_current_blog_id().".".helper::get_device().".js";
            
            // first check if the file exists ##
            if ( $file = theme_helper::get( "theme/javascript/".$handle, "return" ) ) {

                // helper::log( 'Loading up file: '.$file );

                \wp_register_script( $handle, $file, array( 'jquery' ), \q_theme::version, true );
                \wp_enqueue_script( $handle );

                // nonce ##
                $nonce = \wp_create_nonce( 'q-'.\get_current_blog_id().'-nonce' );

                // pass variable values defined in parent class ##
                \wp_localize_script( $handle, 'q_theme_'.\get_current_blog_id(), array(
                    'ajaxurl'           => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ), /*, 'https' */ ## add 'https' to use secure URL ##
                    'debug'             => \q_theme::$debug,
                    'nonce'             => $nonce
                ));

            } else {

                #helper::log( "Cannot locate file: theme/javascript/".$handle );

            }

        }

    }

}