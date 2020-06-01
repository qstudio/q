<?php

namespace q\ui;

use q\core;
use q\core\helper as h;
// use q\core\options as options;
// use q\core\wordpress as wordpress;

// Q Theme ##
use q\theme\core\helper as theme_h;

// load it up ##
\q\ui\assets::run();

class assets extends \Q {

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
        // self::load_libraries();

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

            h::log( 'Q requires q_theme to run correctly..' );

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
        self::$options = core\option::get();
        // h::log( self::$options );

    }



    /**
    * Load Libraries
    *
    * @since        2.0.0
    */
    private static function load_libraries()
    {

        // ui ##
        // require_once self::get_plugin_path( 'library/theme/ui.php' );

    }




    /**
    * include plugin assets
    *
    * @since        0.1.0
    * @note         This file contrains css / js pushed to files from controllers and is required ##
    * @return       __void
    */
    public static function wp_enqueue_scripts_plugin() {

        // h::log( self::$options );
        // h::log( 'debug set to: '. ( true === self::$debug ? 'True' : 'False' ) );

        if ( 
            ( 
                isset( self::$options->plugin_css ) 
                && 1 == self::$options->plugin_css
            )
            && false === self::$debug 
        ) {

            \wp_register_style( 'q-plugin-css-theme', theme_h::get( "theme/css/q.theme.css", 'return' ), array(), self::version, 'all' );
            \wp_enqueue_style( 'q-plugin-css-theme' );

        }

        // if ( 
        //     isset( self::$options->plugin_css ) 
        //     && 1 == self::$options->plugin_css
        //     // && false === self::$debug 
        // ) {
            
        //     // @TODO - what is this file?? ##
        //     \wp_register_style( 'q-plugin-index-css', h::get( "theme/css/index.css", 'return' ), array(), self::version, 'all' );
        //     \wp_enqueue_style( 'q-plugin-index-css' );

        // }

        if ( 
            (
                isset( self::$options->plugin_js ) 
                && 1 == self::$options->plugin_js
            )
            && false === self::$debug 
        ) {

            // h::log( 'Adding q.theme.js' );

            // add JS ## -- after all dependencies ##
            \wp_enqueue_script( 'q-plugin-js-theme', theme_h::get( "theme/javascript/q.theme.js", 'return' ), array( 'jquery' ), self::version );
            
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

            \wp_register_script( 'q-html5', h::get( "theme/javascript/q.html5.js", 'return' ), array(), self::version, 'all' );
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
        // h::log( self::$options->external );
        // h::log( \get_field( 'q_option_external', 'option' ) );

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

            // h::log( 'No external libraries to load' );

            return false;

        }

        // our query returns all items are single properties of the $options object - so, let's make an array ##
        if( \have_rows( 'q_option_external', 'option' ) ) {

            while( \have_rows( 'q_option_external', 'option' ) ) {
                
                // set things up ##
                \the_row(); 

                // properties ##
                // external libraries are saved in an array with "type", "title", "version" and "url" ##
                $type = \get_sub_field('type');
                $title = \get_sub_field('title');
                $version = \get_sub_field('version');
                $url = \get_sub_field('url');

                // h::log( 'working External: '.$title );

                // sanitize title to handle ##
                $handle = \sanitize_key( $title );

                // validate URL ##

                // debug ##
                // h::log( 'Adding external library: '.$handle.' version '.$version.' from url: '.$url.' as type: '.$type );

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

            // h::log( 'No external libraries to load...' );

        }

    }




    
    /*
    * script enqueuer 
    *
    * @since  2.0
    */
    public static function wp_enqueue_scripts_local() {

        // h::log( self::$options->library );

        // loop over libraries and include - checking for "min" version is debugging ##
        foreach( self::$options->library as $key => $value ) {

            // h::log( 'working: '.$key );

            // CSS or JS
            $type = explode( "_" , $key );

            // if no type - skip ##
            if ( 
                ! is_array( $type ) 
                || 2 > count( $type )
            ) {

                // h::log( 'Skipping: '.$key );

                continue;

            }

            $type_dir = ( 'css' == $type[0] ) ? 'css' : 'javascript' ;
            $type_ext = ( 'css' == $type[0] ) ? 'css' : 'js' ;

            // give it a handle ##
            $handle = 'q-'.$key;

            // template hierarchy ##

            // Debugging, so load non-minified version from q_theme library ##
            if ( 
                self::$debug
                && theme_h::get( "ui/".$type_dir."/".$type[1].".".$type_ext, 'return' )
            ) {

                $file = theme_h::get( "ui/".$type_dir."/".$type[1].".".$type_ext, 'return' ) ;

                // h::log( 'DEUBBING - Adding '.$type_dir.'/'.$type[1].'.min.'.$type_ext.' from Q Theme' ) ;

            // load minified version from Q Theme ##
            } else if (
                theme_h::get( "ui/".$type_dir."/".$type[1].".min.".$type_ext, 'return' ) 
            ) {

                $file = theme_h::get( "ui/".$type_dir."/".$type[1].".min.".$type_ext, 'return' ) ;

                // h::log( 'Adding '.$type_dir.'/'.$type[1].'.min.'.$type_ext.' from Q Theme' );

            // check for non-minified version in Q library, if debugging ##
            } else if ( 
                self::$debug
                && h::get( "ui/".$type_dir."/".$type[1].".".$type_ext, 'return' ) 
            ) {

                $file = h::get( "ui/".$type_dir."/".$type[1].".".$type_ext, 'return' );

                // h::log( 'DEUBBING - Adding '.$type_dir.'/'.$type[1].'.'.$type_ext.' from Q' );
                
            // load minified version from Q ## 
            } else if ( h::get( "ui/".$type_dir."/".$type[1].".min.".$type_ext, 'return' ) ) {

                $file = h::get( "ui/".$type_dir."/".$type[1].".min.".$type_ext, 'return' ) ;

                // h::log( 'Adding '.$type_dir.'/'.$type[1].'.min.'.$type_ext.' from Q' );

            // final fallback - non minified on Q ##
            } else {

                $file = h::get( "ui/".$type_dir."/".$type[1].".".$type_ext, 'return' );

                // h::log( 'Final fallback - Adding '.$type_dir.'/'.$type[1].'.'.$type_ext.' from Q' );

            }

            // if no type - skip ##
            if ( ! $file ) {

                h::log( 'Skipping: '.$handle.' - File missing...' );

                continue;

            }

            // h::log( 'Adding library: '.$handle.' with file: '.$file.' as type: '.$type_ext );

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

            // h::log( 'Running CSS...' );

            // IE ##
            if ( theme_h::get( "ui/css/ie.css", "return" ) ) {
         
                \wp_enqueue_style( 'q-ie', theme_h::get( "ui/css/ie.css", "return" ), '', \q_theme::version );
                \wp_style_add_data( 'q-ie', 'conditional', 'IE' );

            }

            // css hierarchy ---- ##

            // track ##
            $found = false;

            // minified - .min - version used based on debugging setting - local OR global ##
            $min = ( true === \q_theme::$debug ) ? '' : '.min' ;
            
            // handle ##
            $handle = 'q-theme-css';

            // array for files ##
            $files = [];

            // q_theme/library/ui/css/q.2.desktop.css ## network site + device
            $files[] = "q.".\get_current_blog_id().".".h::device()."$min.css";

            // q_theme/library/ui/css/q.2.theme.css ## network site + all devices
            $files[] = "q.".\get_current_blog_id().".theme$min.css";

            // q_theme/library/ui/q.1.desktop.css ## all network sites + device
            $files[] = "q.".\get_current_blog_id().".".h::device()."$min.css";

            // q_theme/library/ui/q.1.theme.css ## all networks + all devices
            $files[] = "q.1.theme$min.css";

            // loop over all files in priority, loading whichever is found first ##
            foreach( $files as $file ) {

                // file exists check ##
                if ( $uri = theme_h::get( "ui/css/".$file, "return" ) ) {

                    // h::log( 'Loading up file: '.$file );

                    \wp_register_style( $handle, $uri, '', \q_theme::version );
                    \wp_enqueue_style( $handle );

                    // update tracker ##
                    $found = true;

                    // kick out ##
                    return true;

                }

            }

            // no asset found, so note this ##
            if ( ! $found  ) h::log( 'Error loading CSS Asset' );

        }

        // Q Check if we need to include SCSS Modules 
        // based on binary switch per install found in Q Settings self::$options->theme_css_scss ##
        // can be loaded in addition or alone from standard CSS 
        if ( 
            isset( self::$options->theme_scss ) 
            && 1 == self::$options->theme_scss    
        ) {

            // h::log( 'Running SCSS...' );

            // IE ##
            if ( theme_h::get( "ui/scss/ie.css", "return" ) ) {
         
                \wp_enqueue_style( 'q-ie-scss', theme_h::get( "ui/scss/ie.css", "return" ), '', \q_theme::version );
                \wp_style_add_data( 'q-ie-scss', 'conditional', 'IE' );

            }

            // css hierarchy ---- ##

            // track ##
            $found = false;

            // minified - .min - version used based on debugging setting - local OR global ##
            $min = ( true === \q_theme::$debug ) ? '' : '.min' ;
            
            // handle ##
            $handle = 'q-theme-scss';

            // array for files ##
            $files = [];

            // q_theme/library/ui/css/q.scss.2.desktop.css ## network site + device
            $files[] = "q.scss.".\get_current_blog_id().".".h::device()."$min.css";

            // q_theme/library/ui/css/q.scss.2.theme.css ## network site + all devices
            $files[] = "q.scss.".\get_current_blog_id().".theme$min.css";

            // q_theme/library/ui/q.scss.1.desktop.css ## all network sites + device
            $files[] = "q.scss.1.".h::device()."$min.css";

            // q_theme/library/ui/q.scss.theme.css ## all networks + all devices
            $files[] = "q.scss.theme$min.css";

            // loop over all files in priority, loading whichever is found first ##
            foreach( $files as $file ) {

				if ( $found ) break;

                // file exists check ##
                if ( $uri = theme_h::get( "ui/css/".$file, "return" ) ) {

                    // h::log( 'Loading up file: '.$file );

                    \wp_register_style( $handle, $uri, '', \q_theme::version );
                    \wp_enqueue_style( $handle );

                    // update tracker ##
                    $found = true;

                    // kick out ##
                    // return true;

                }

            }

            // no asset found, so note this ##
            if ( ! $found  ) h::log( 'Error loading SCSS Asset' );

        }

        // load generic JS from ui/javascript/scripts.js
        if ( 
            isset( self::$options->theme_js ) 
            && 1 == self::$options->theme_js
        ) {

            \wp_register_script( 'theme-js', theme_h::get( "ui/javascript/scripts.js", 'return' ), array( 'jquery' ), \q_theme::version, true );
            \wp_enqueue_script( 'theme-js' );

            // pass variable values defined in parent class ##
            \wp_localize_script( 'theme-js', 'q_theme', array(
                'ajaxurl'           => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ), /*, 'https' */ ## add 'https' to use secure URL ##
                'debug'             => self::$debug
            ));


			// JS hierarchy ---- ##

            // track ##
            $found = false;

            // minified - .min - version used based on debugging setting - local OR global ##
            $min = ( true === \q_theme::$debug ) ? '' : '.min' ;
            
            // handle ##
            $handle = 'q-theme-js';

            // array for files ##
            $files = [];

            // q_theme/library/ui/javascript/q.2.desktop.js ## network site + device
            $files[] = "q.".\get_current_blog_id().".".h::device()."$min.js";

            // q_theme/library/ui/javascript/q.2.theme.js ## network site + all devices
            $files[] = "q.".\get_current_blog_id().".theme$min.js";

            // q_theme/library/ui/javascript/q.1.desktop.js ## all network sites + device
            $files[] = "q.1.".h::device()."$min.js";

            // q_theme/library/ui/javascript/q.1.theme.js ## all networks + all devices
            $files[] = "q.1.theme$min.js";

            // loop over all files in priority, loading whichever is found first ##
            foreach( $files as $file ) {

				// h::log( 'Loading up file: '.$file );

				if ( $found ) break;

                // file exists check ##
                if ( $uri = theme_h::get( "ui/javascript/".$file, "return" ) ) {

                    // h::log( 'Loading up file: '.$file );

                    // \wp_register_style( $handle, $uri, '', \q_theme::version );
					// \wp_enqueue_style( $handle );
					
					\wp_register_script( $handle, $uri, array( 'jquery' ), \q_theme::version, true );
                    \wp_enqueue_script( $handle );

                    // update tracker ##
                    $found = true;

                    // kick out ##
                    // return true;

                }

            }

            // no asset found, so note this ##
            if ( ! $found ) h::log( 'Error loading JS Asset' );

            // nonce ##
            $nonce = \wp_create_nonce( 'q-'.\get_current_blog_id().'-nonce' );

            // pass variable values defined in parent class ##
            \wp_localize_script( $handle, 'q_theme_'.\get_current_blog_id(), array(
                'ajaxurl'           => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ), 
                'debug'             => \q_theme::$debug,
                'nonce'             => $nonce
            ));

        }

    }

}