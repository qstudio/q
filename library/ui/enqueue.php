<?php

namespace q\ui;

use q\core;
use q\core\helper as h;

// Q Theme ##
use q\theme\core\helper as theme_h;

// load it up ##
\q\ui\enqueue::run();

class enqueue extends \Q {

    // public static $plugin_version;
    public static $plugin_version;
    public static $option;

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

            // css / js from theme ##
            \add_action( 'wp_enqueue_scripts', array ( get_class(), 'wp_enqueue_scripts_theme' ), 10000 );

        }

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
            ! class_exists( 'q_theme' ) // how to get around this ?? ##
        ) {

            // h::log( 'e:>@todo --- Q requires q_theme to run correctly..' );

            // return false;

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
        self::$option = core\option::get();
        // h::log( self::$option );

    }




    /**
    * include plugin assets
    *
    * @since        0.1.0
    * @note         This file contrains css / js pushed to files from controllers and is required ##
    * @return       __void
    */
    public static function wp_enqueue_scripts_plugin() {

        // h::log( self::$option );
        // h::log( 'debug set to: '. ( true === self::$debug ? 'True' : 'False' ) );

        if ( 
            ( 
                isset( self::$option->plugin_css ) 
                && 1 == self::$option->plugin_css
            )
            && false === self::$debug 
        ) {

            \wp_register_style( 'q-plugin-css-theme', theme_h::get( "ui/css/q/theme.css", 'return' ), array(), self::version, 'all' );
            \wp_enqueue_style( 'q-plugin-css-theme' );

        }

        // if ( 
        //     isset( self::$option->plugin_css ) 
        //     && 1 == self::$option->plugin_css
        //     // && false === self::$debug 
        // ) {
            
        //     // @TODO - what is this file?? ##
        //     \wp_register_style( 'q-plugin-index-css', h::get( "ui/css/index.css", 'return' ), array(), self::version, 'all' );
        //     \wp_enqueue_style( 'q-plugin-index-css' );

        // }

        if ( 
            (
                isset( self::$option->plugin_js ) 
                && 1 == self::$option->plugin_js
            )
            && false === self::$debug 
        ) {

            // h::log( 'Adding q.theme.js' );

            // add JS ## -- after all dependencies ##
            \wp_enqueue_script( 'q-plugin-js-theme', theme_h::get( "ui/javascript/q/theme.js", 'return' ), array( 'jquery' ), self::version );
            
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
                && self::$option->plugin_js === TRUE 
            )
        ) {

            \wp_register_script( 'q-html5', h::get( "ui/javascript/vendor/html5.js", 'return' ), array(), self::version, 'all' );
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
        // h::log( self::$option->external );
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
            ! isset( self::$option->external )
            || 1 > self::$option->external
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

        // h::debug( self::$option->library );

        // loop over libraries and include - checking for "min" version is debugging ##
        foreach( self::$option->library as $key => $value ) {

            // h::log( 'd:>working: '.$key );

            // CSS or JS
            $type = explode( "_" , $key );

            // if no type - skip ##
            if ( 
                ! is_array( $type ) 
                || 2 > count( $type )
            ) {

                // h::log( 'd:>Skipping: '.$key );

                continue;

            }

			// fixed location - css or js ##
			$type_dir = ( 'css' == $type[0] ) ? 'css' : 'javascript' ;
			
			// fixed type css or js ##
            $type_ext = ( 'css' == $type[0] ) ? 'css' : 'js' ;

			// track ##
            $found = false;

            // minified - .min - version used based on debugging setting - local OR global ##
            // $min = ( true === \q_theme::$debug ) ? '' : '.min' ;
            
            // handle ##
			$handle = 'q-local-'.$key;
			
			// directory ##
			$directory = 'ui/'.$type_dir.'/vendor/';

            // array for files ##
			$files = [];
			
			// if debugging, try to load non-minified version first ##
			if ( true === \q_theme::$debug ) { $files[] = $type[1].".".$type_ext; }

            // load minified version ##
			$files[] = $type[1].".min.".$type_ext;
			
			// load non-minified version as last option ##
			$files[] = $type[1].".".$type_ext;

            // loop over all files in priority, loading whichever is found first ##
            foreach( $files as $file ) {

				// load first found file ##
				if ( $found ) break;

				// $file_uri = h::get( '/library/ui/css/'.$file );
				// $file_path = \q_theme::get_parent_theme_path( '/library/ui/css/'.$file );

				// h::log( 'd:>looking up file: '.$directory.$file );

                // file exists check ##
                if ( 
					// file_exists( $file_path )
					$load = h::get( $directory.$file, 'return' )
					// "ui/".$type_dir."/".$type[1].".".$type_ext, 'return'
				) {

                    // h::log( 'd:>Loading up file: '.$directory.$file );

					// register and enqueue ##
					switch ( $type_ext ) {

						case "css" :
		
							\wp_register_style( $handle, $load, '', self::version, 'all' );
							\wp_enqueue_style( $handle );
		
						break ;
		
						case "js" :
		
							\wp_register_script( $handle, $load, array(), self::version, 'all' );
							\wp_enqueue_script( $handle );
		
						break ;
		
					}

                    // update tracker ##
                    $found = true;

                    // kick out ##
                    // return true;

                }

            }

            // no asset found, so note this ##
            if ( ! $found  ) h::log( 'n:>Error loading Local Asset: '.$key );



			/*
            // template hierarchy ##

            // Debugging, so load non-minified version from q_theme library ##
            if ( 
                self::$debug
                && theme_h::get( "ui/".$type_dir."/".$type[1].".".$type_ext, 'return' )
            ) {

                $file = theme_h::get( "ui/".$type_dir."/".$type[1].".".$type_ext, 'return' ) ;

                // h::log( 'd:> - Adding '.$type_dir.'/'.$type[1].'.min.'.$type_ext.' from Q Theme' ) ;

            // load minified version from Q Theme ##
            } else if (
                theme_h::get( "ui/".$type_dir."/".$type[1].".min.".$type_ext, 'return' ) 
            ) {

                $file = theme_h::get( "ui/".$type_dir."/".$type[1].".min.".$type_ext, 'return' ) ;

                // h::log( 'd:>Adding '.$type_dir.'/'.$type[1].'.min.'.$type_ext.' from Q Theme' );

            // check for non-minified version in Q library, if debugging ##
            } else if ( 
                self::$debug
                && h::get( "ui/".$type_dir."/".$type[1].".".$type_ext, 'return' ) 
            ) {

                $file = h::get( "ui/".$type_dir."/".$type[1].".".$type_ext, 'return' );

                // h::log( 'd:> - Adding '.$type_dir.'/'.$type[1].'.'.$type_ext.' from Q' );
                
            // load minified version from Q ## 
            } else if ( h::get( "ui/".$type_dir."/".$type[1].".min.".$type_ext, 'return' ) ) {

                $file = h::get( "ui/".$type_dir."/".$type[1].".min.".$type_ext, 'return' ) ;

                // h::log( 'd:>Adding '.$type_dir.'/'.$type[1].'.min.'.$type_ext.' from Q' );

            // final fallback - non minified on Q ##
            } else {

                $file = h::get( "ui/".$type_dir."/".$type[1].".".$type_ext, 'return' );

                // h::log( 'd:>Final fallback - Adding '.$type_dir.'/'.$type[1].'.'.$type_ext.' from Q' );

            }

            // if no type - skip ##
            if ( ! $file ) {

                // h::log( 'd:>Skipping: '.$handle.' - File missing...' );

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
			*/

		}

    }





    /*
    * script enqueuer -- loaded from q_theme
    *
    * @since  2.0
    */
    public static function wp_enqueue_scripts_theme() 
    {

		// h::log( self::$option );

        // Load Parent CSS
        if ( 
            isset( self::$option->theme_parent->css ) 
            && '1' == self::$option->theme_parent->css    
        ) {

            // h::log( 'd:> Loading Parent CSS...' );

            // IE ##
            if ( 
				file_exists( 
					$file_path_ie = \q_theme::get_parent_theme_path( '/library/ui/css/ie.css' )
				)
			) {

				$file_uri_ie = \q_theme::get_parent_theme_url( '/library/ui/css/ie.css' );
         
                \wp_enqueue_style( 'q-parent-ie-css', $file_uri_ie, '', \q_theme::version );
                \wp_style_add_data( 'q-parent-ie-css', 'conditional', 'IE' );

            }

            // css hierarchy ---- ##

            // track ##
            $found = false;

            // minified - .min - version used based on debugging setting - local OR global ##
            $min = ( true === \q_theme::$debug ) ? '' : '.min' ;
            
            // handle ##
            $handle = 'q-parent-theme-css';

            // array for files ##
            $files = [];

            // q_theme/library/ui/css/2.desktop.css ## network site + device
            $files[] = "".\get_current_blog_id().".".h::device()."$min.css";

            // q_theme/library/ui/css/2.theme.css ## network site + all devices
            $files[] = "".\get_current_blog_id().".theme$min.css";

            // q_theme/library/ui/css/1.desktop.css ## all network sites + device
            $files[] = "1.".h::device()."$min.css";

            // q_theme/library/ui/theme.css ## all networks + all devices
            $files[] = "theme$min.css";

            // loop over all files in priority, loading whichever is found first ##
            foreach( $files as $file ) {

				// load first found file ##
				if ( $found ) break;

				$file_uri = \q_theme::get_parent_theme_url( '/library/ui/css/'.$file );
				$file_path = \q_theme::get_parent_theme_path( '/library/ui/css/'.$file );

				// h::log( 'd:>looking up file: '.$file_uri );

                // file exists check ##
                if ( 
					file_exists( $file_path )
				) {

                    // h::log( 'd:>Loading up file: '.$file_uri );

					\wp_register_style( $handle, $file_uri, '', \q_theme::version );
                    \wp_enqueue_style( $handle );

                    // update tracker ##
                    $found = true;

                    // kick out ##
                    // return true;

                }

            }

            // no asset found, so note this ##
            if ( ! $found  ) h::log( 'n:>Error loading SCSS Asset' );

		}
		

		// Load Child CSS
		if ( 
            isset( self::$option->theme_child->css ) 
            && '1' == self::$option->theme_child->css    
        ) {

            // h::log( 'd:> Loading Child CSS...' );

            // IE ##
            if ( 
				file_exists( 
					$file_path_ie = \q_theme::get_child_theme_path( '/library/ui/css/ie.css' )
				)
			) {

				$file_uri_ie = \q_theme::get_child_theme_url( '/library/ui/css/ie.css' );
         
                \wp_enqueue_style( 'q-child-ie-css', $file_uri_ie, '', \q_theme::version );
                \wp_style_add_data( 'q-child-ie-css', 'conditional', 'IE' );

            }

            // css hierarchy ---- ##

            // track ##
            $found = false;

            // minified - .min - version used based on debugging setting - local OR global ##
            $min = ( true === \q_theme::$debug ) ? '' : '.min' ;
            
            // handle ##
            $handle = 'q-child-theme-css';

            // array for files ##
            $files = [];

            // q_theme/library/ui/css/2.desktop.css ## network site + device
            $files[] = "".\get_current_blog_id().".".h::device()."$min.css";

            // q_theme/library/ui/css/2.theme.css ## network site + all devices
            $files[] = "".\get_current_blog_id().".theme$min.css";

            // q_theme/library/ui/css/1.desktop.css ## all network sites + device
            $files[] = "1.".h::device()."$min.css";

            // q_theme/library/ui/css/theme.css ## all networks + all devices
            $files[] = "theme$min.css";

            // loop over all files in priority, loading whichever is found first ##
            foreach( $files as $file ) {

				// load first found file ##
				if ( $found ) break;

				$file_uri = \q_theme::get_child_theme_url( '/library/ui/css/'.$file );
				$file_path = \q_theme::get_child_theme_path( '/library/ui/css/'.$file );

				// h::log( 'd:>looking up file: '.$file_uri );

                // file exists check ##
                if ( 
					file_exists( $file_path )
				) {

                    // h::log( 'd:>Loading up file: '.$file_uri );

					\wp_register_style( $handle, $file_uri, '', \q_theme::version );
                    \wp_enqueue_style( $handle );

                    // update tracker ##
                    $found = true;

                    // kick out ##
                    // return true;

                }

            }

            // no asset found, so note this ##
            if ( ! $found  ) h::log( 'n:>Error loading SCSS Asset' );

		}



		// load parent theme js
        if ( 
            isset( self::$option->theme_parent->js ) 
            && '1' == self::$option->theme_parent->js
        ) {

			// deprecated scripts.js.. ##
			/*
            \wp_register_script( 
				'q-parent-theme-js', 
				theme_h::get( "ui/javascript/scripts.js", 'return' ), 
				array( 'jquery' ), 
				\q_theme::version, 
				true 
			);
            \wp_enqueue_script( 'q-parent-theme-js' );

            // pass variable values defined in parent class ##
            \wp_localize_script( 'q-parent-theme-js', 'q_theme', array(
                'ajaxurl'           => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ), 
                'debug'             => self::$debug
			));
			*/


			// JS hierarchy ---- ##

            // track ##
            $found = false;

            // minified - .min - version used based on debugging setting - local OR global ##
            $min = ( true === \q_theme::$debug ) ? '' : '.min' ;
            
            // handle ##
            $handle = 'q-parent-theme-js';

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

				// load first found file ##
				if ( $found ) break;

				$file_uri = \q_theme::get_parent_theme_url( '/library/ui/javascript/'.$file );
				$file_path = \q_theme::get_parent_theme_path( '/library/ui/javascript/'.$file );

				// h::log( 'd:>looking up file: '.$file_uri );

                // file exists check ##
                if ( 
					file_exists( $file_path )
				) {

                    // h::log( 'd:>Loading up file: '.$file_uri );

					\wp_register_script( $handle, $file_uri, array( 'jquery' ), \q_theme::version, true );
                    \wp_enqueue_script( $handle );

                    // update tracker ##
                    $found = true;

                    // kick out ##
                    // return true;

                }

            }

            // no asset found, so note this ##
            if ( ! $found ) h::log( 'd:>Error loading Parent JS Asset' );

            // nonce ##
            $nonce = \wp_create_nonce( 'q-'.\get_current_blog_id().'-nonce' );

            // pass variable values defined in parent class ##
            \wp_localize_script( $handle, 'q_parent_theme_'.\get_current_blog_id(), array(
                'ajaxurl'           => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ), 
                'debug'             => \q_theme::$debug,
                'nonce'             => $nonce
            ));

		}
		

		 // load child theme JS
		 if ( 
            isset( self::$option->theme_child->js ) 
            && '1' == self::$option->theme_child->js
        ) {

			// deprecated scripts.js.. ##
			/*
            \wp_register_script( 
				'q-parent-theme-js', 
				theme_h::get( "ui/javascript/scripts.js", 'return' ), 
				array( 'jquery' ), 
				\q_theme::version, 
				true 
			);
            \wp_enqueue_script( 'q-parent-theme-js' );

            // pass variable values defined in parent class ##
            \wp_localize_script( 'q-parent-theme-js', 'q_theme', array(
                'ajaxurl'           => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ), 
                'debug'             => self::$debug
			));
			*/


			// JS hierarchy ---- ##

            // track ##
            $found = false;

            // minified - .min - version used based on debugging setting - local OR global ##
            $min = ( true === \q_theme::$debug ) ? '' : '.min' ;
            
            // handle ##
            $handle = 'q-child-theme-js';

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

				// load first found file ##
				if ( $found ) break;

				$file_uri = \q_theme::get_child_theme_url( '/library/ui/javascript/'.$file );
				$file_path = \q_theme::get_child_theme_path( '/library/ui/javascript/'.$file );

				// h::log( 'd:>looking up file: '.$file_uri );

                // file exists check ##
                if ( 
					file_exists( $file_path )
				) {

                    // h::log( 'd:>Loading up file: '.$file_uri );

					\wp_register_script( $handle, $file_uri, array( 'jquery' ), \q_theme::version, true );
                    \wp_enqueue_script( $handle );

                    // update tracker ##
                    $found = true;

                    // kick out ##
                    // return true;

                }

            }

            // no asset found, so note this ##
            // if ( ! $found ) h::log( 'd:>Error loading JS Asset' );

            // nonce ##
            $nonce = \wp_create_nonce( 'q-'.\get_current_blog_id().'-nonce' );

            // pass variable values defined in parent class ##
            \wp_localize_script( $handle, 'q_child_theme_'.\get_current_blog_id(), array(
                'ajaxurl'           => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ), 
                'debug'             => \q_theme::$debug,
                'nonce'             => $nonce
            ));

		}
		

    }

}
