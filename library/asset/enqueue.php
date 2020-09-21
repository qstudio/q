<?php

namespace q\asset;

use q\core;
use q\core\helper as h;

// load it up ##
\q\asset\enqueue::run();

class enqueue extends \Q {

    public static $plugin_version;
    public static $option;

    public static function run()
    {

        // load templates ##
		self::load_properties();
		
        if ( ! \is_admin() ) {

			// plugins and enhanecments ##
			\add_action( 'wp_enqueue_scripts', array ( get_class(), 'wp_enqueue_scripts_general' ), 2 );
			
			// local external scripts ##
			\add_action( 'wp_enqueue_scripts', array ( get_class(), 'wp_enqueue_scripts_external' ), 3 );

            // local optional scripts ##
			\add_action( 'wp_enqueue_scripts', array ( get_class(), 'wp_enqueue_scripts_local' ), 4 );
			
			// plugin css / js -- includes defaults and resets and snippets from controllers ##
            \add_action( 'wp_enqueue_scripts', array( get_class(), 'wp_enqueue_scripts_module' ), 999 );

            // css / js from theme ##
			\add_action( 'wp_enqueue_scripts', array ( get_class(), 'wp_enqueue_scripts_theme' ), 1000 );

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

            h::log( 'e:>@todo ---> Q requires q_theme to run correctly..' );

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
        self::$option = core\option::get();
        // h::log( self::$option );

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

                        \wp_register_style( $handle, $url.'?__preload', '', $version, 'all' );
                        \wp_enqueue_style( $handle );

                    break ;

                    case "js" :

                        \wp_register_script( $handle, $url.'?__js_defer', array(), $version, 'all' );
                        \wp_enqueue_script( $handle );

                    break ;

                }

            }

        } else {

            // h::log( 'No external libraries to load...' );

        }

	}
	

	


    /**
    * include plugin assets
    *
    * @since        0.1.0
    * @note         This file contrains css / js pushed to files from controllers and is required ##
    * @return       __void
    */
    public static function wp_enqueue_scripts_module() {

		// check we have dependencies ##
        if ( ! self::has_dependencies() ){

            return false;

        }

        // h::log( self::$option->module );
        // h::log( 'd:>debug set to: '. ( true === self::$debug ? 'True' : 'False' ) );

		// module JS ##
		/*
		* when debugging:
		* - script is compilled via uglify --- NO ORDER, try for now...
		* - cache-busting hash is added to file ?cachebuster123
		*
		* when not debugging:
		* - script is compilled and minified / mangled via uglify
		* - no cache-buster
		*/
        if ( 
			isset( self::$option->module_asset->js ) 
			&& 1 == self::$option->module_asset->js
        ) {

			// load list of modules, stored in site_option "q_modules" - includes list of parameters to localize ##
			$q_modules = \get_option( "q_modules" );
			// h::log( $q_modules );

			// minified - .min - version used based on debugging setting - local OR global ##
			// $min = ( true === self::$debug ) ? '' : '.min' ;
			// $min = '.min' ;

			if ( 
				true === self::$debug 
				// || ! \q_theme::get_parent_theme_url( "/library/asset/js/module/module.min.js" ) // minified version missing ##
			){

				// cache-buster hash used based on debugging setting - local OR global ##
				$hash = 'hash='.rand() ;

				// get all "active" modules ##
				$modules = \q\asset\js::get();

				// h::log( $modules );

				// validate we have modules ##
				if(
					$modules
					&& is_array( $modules )
					&& isset( $modules['javascript'] )
					&& is_array( $modules['javascript'] )
				)

				// loop over each asset + enqueue ##
				foreach( $modules['javascript'] as $module ){

					// h::log( 'd:>Checking for JS modules: '.'library/asset/js/module/'.$module.'.js' );

					// check if there is matching js files in ~/asset/js/module/MODULE.js ##
					// $file = \q_theme::get_parent_theme_path( '/library/_source/js/module/'.$module.'.js' );

					// use fallback lookup, prioritize child over parent ##
					$file = h::get( '_source/js/module/'.$module.'.js', 'return', 'path' );

					if ( 
						! $file
						|| ! file_exists( $file )
					){

						h::log( 'd:>Unable to locate modules: _source/js/module/'.$module.'.js' );

						continue;

					}

					$file_url = h::get( '_source/js/module/'.$module.'.js', 'return' );

					// handle ##
					$handle = ( '__q' == $module ) ? 'q-module' : 'q-module-'.$module ;

					// dependency ##
					$dependecy = ( '__q' == $module ) ? [ 'jquery' ] : [ 'jquery', 'q-module' ] ;

					// defer rule ##
					$defer = in_array( $module, [  ] ) ? '?__no_defer&' : '?__js_defer&' ; // js_async

					// h::log( 'd:>Load JS modules: '.'library/asset/js/module/'.$module.'.js' );

					// include single file with cache-busting hash ##
					\wp_enqueue_script( 
						$handle, 
						$file_url.$defer.$hash, 
						$dependecy, 
						self::version,
						// true
					);

				}

			} else {

				// add single module.min.js ##
				\wp_enqueue_script( 
					'q-module', 
					\q_theme::get_child_theme_url( "/library/asset/js/module.min.js?__js_defer" ), 
					array( 'jquery' ), 
					self::version,
					// true
				);

			}

            // pass variable values defined in parent class ##
			\wp_localize_script( 
				'q-module', 
				'q_module', 
				array_merge( array(
				'ajaxurl'           => \admin_url( 'admin-ajax.php' ), // q_module.ajaxurl
				'debug'             => self::$debug, // q_module.debug
				'nonce'             => \wp_create_nonce( 'q_module_nonce' ), // q_module.nonce
				'modal_target'		=> '#q_modal', // default modal selector ##
				'modal_size'		=> 'modal-lg', // default modal size ##
				'ajax_preload'      => \__( \esc_js ( 'Rummaging in the cupboards for that last crumb..', 'q-textdomain' ) ), // q_module.ajac_preload
				'ajax_loading'      => \__( \esc_js ( 'Just a minute, we know it is somewhere here...', 'q-textdomain' ) ), // q_module.ajax_loading
				'ajax_failed'       => \__( \esc_js( 'Opps! Something is not right...', 'q-textdomain' ) ), // q_module.ajax_failed
				'ajax_success'      => \__( \esc_js( 'Here is your freshly squeezed data :)', 'q-textdomain' ) ), // q_module.ajax_success
				), 
				\q\asset\js::localize() // merge in args passed from modules ##
				)
			);

        }


    }





    /*
    * script enqueuer 
    *
    * @since  2.0
    */
    public static function wp_enqueue_scripts_general() {

        // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions ##
		\wp_register_script( 'q-html5', h::get( "vendor/js/html5.js?__js_defer", 'return' ), array(), self::version, 'all' );
		\wp_enqueue_script( 'q-html5' );
		wp_style_add_data( 'q-html5', 'conditional', 'lt IE 9' );

        // add jquery ##
        \wp_enqueue_script( "jquery" );

        // // Required for nested reply function that moves reply inline with JS ##
        // if ( 
        //     \is_singular() 
        //     && \comments_open() 
        //     && \get_option( 'thread_comments' ) 
        // ) {
        
        //     \wp_enqueue_script( 'comment-reply' ); // enqueue the js that performs in-link comment reply fanciness
        
        // }

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
			$type_dir = ( 'css' == $type[0] ) ? 'css' : 'js' ;
			
			// fixed type css or js ##
            $type_ext = ( 'css' == $type[0] ) ? 'css' : 'js' ;

			// track ##
            $found = false;

            // minified - .min - version used based on debugging setting - local OR global ##
            // $min = ( true === self::$debug ) ? '' : '.min' ;
            
            // handle ##
			$handle = 'q-local-'.$key;
			
			// directory ##
			$directory = 'vendor/'.$type_dir.'/';

            // array for files ##
			$files = [];
			
			// if debugging, try to load non-minified version first ##
			if ( true === self::$debug ) { $files[] = $type[1].".".$type_ext; }

            // load minified version ##
			$files[] = $type[1].".min.".$type_ext;
			
			// load non-minified version as last option ##
			$files[] = $type[1].".".$type_ext;

            // loop over all files in priority, loading whichever is found first ##
            foreach( $files as $file ) {

				// load first found file ##
				if ( $found ) break;

				// $file_uri = h::get( '/library/asset/css/'.$file );
				// $file_path = \q_theme::get_parent_theme_path( '/library/asset/css/'.$file );

				// h::log( 'd:>looking up file: '.$directory.$file );

                // file exists check ##
                if ( 
					// file_exists( $file_path )
					$load = h::get( $directory.$file, 'return' )
					// "asset/".$type_dir."/".$type[1].".".$type_ext, 'return'
				) {

                    // h::log( 'd:>Loading up file: '.$directory.$file );

					// register and enqueue ##
					switch ( $type_ext ) {

						case "css" :
		
							\wp_register_style( $handle, $load.'?__preload', '', self::version, 'all' );
							\wp_enqueue_style( $handle );
		
						break ;
		
						case "js" :
		
							\wp_register_script( $handle, $load.'?__js_defer', [ 'jquery' ], self::version, 'all' );
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


		}

    }





    /*
    * script enqueuer -- loaded from q_theme
    *
    * @since  2.0
    */
    public static function wp_enqueue_scripts_theme(){

		// check we have dependencies ##
        if ( ! self::has_dependencies() ){

			h::log( 'e:>No Q Theme class...');

            return false;

		}
		
		// h::log( 'e:>Loading Theme files.....');
		// h::log( self::$option );
		/*
        // Load Parent CSS
        if ( 
            isset( self::$option->theme_parent->css ) 
            && '1' == self::$option->theme_parent->css    
        ) {

			// h::log( 'd:> Loading Parent CSS...' );
			
			// \wp_register_style( 'q-plugin-css-theme', \q_theme::get_parent_theme_url( '/library/asset/css/theme.min.css' ), array(), \q_theme::version, 'all' );
			// \wp_enqueue_style( 'q-plugin-css-theme' );

            // IE ##
            if ( 
				file_exists( 
					$file_path_ie = \q_theme::get_parent_theme_path( '/library/asset/css/ie.css' )
				)
			) {

				$file_uri_ie = \q_theme::get_parent_theme_url( '/library/asset/css/ie.css' );
         
                \wp_enqueue_style( 'q-parent-ie-css', $file_uri_ie, '', \q_theme::version );
                \wp_style_add_data( 'q-parent-ie-css', 'conditional', 'IE' );

            }

            // css hierarchy ---- ##

            // track ##
            $found = false;

            // minified - .min - version used based on debugging setting - local OR global ##
            $min = ( true === self::$debug ) ? '' : '.min' ;
            
            // handle ##
			$handle = 'q-parent-theme-css';
			
			// base file name ##
			$base = 'theme';

            // array for files ##
            $files = [];

            // library/asset/css/theme.2.desktop(.min).css ## network site + device
            $files[] = $base.".".\get_current_blog_id().".".h::device()."$min.css";

            // library/asset/css/theme.2(.min).css ## network site + all devices
            $files[] = $base.".".\get_current_blog_id()."$min.css";

            // library/asset/css/theme.desktop(.min).css ## all network sites + device
            $files[] = $base.".".h::device()."$min.css";

            // library/asset/theme(.min).css ## all networks + all devices
            $files[] = "$base$min.css";

            // loop over all files in priority, loading whichever is found first ##
            foreach( $files as $file ) {

				// load first found file ##
				if ( $found ) break;

				$file_uri = \q_theme::get_parent_theme_url( '/library/asset/css/'.$file );
				$file_path = \q_theme::get_parent_theme_path( '/library/asset/css/'.$file );

				h::log( 'd:>looking up file: '.$file_uri );

                // file exists check ##
                if ( 
					file_exists( $file_path )
				) {

                    h::log( 'd:>Loading up file: '.$file_uri );

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
		*/

		// Load Child CSS
		if ( 
            isset( self::$option->theme_child->css ) 
            && '1' == self::$option->theme_child->css    
        ) {

			// \wp_register_style( 'q-plugin-css-theme', \q_theme::get_child_theme_path( '/library/asset/css/theme.min.css' ), array(), \q_theme::version, 'all' );
			// \wp_enqueue_style( 'q-plugin-css-theme' );

            // h::log( 'd:> Loading Child CSS...' );

            // IE ##
            if ( 
				file_exists( 
					$file_path_ie = \q_theme::get_child_theme_path( '/library/asset/css/ie.css' )
				)
			) {

				$file_uri_ie = \q_theme::get_child_theme_url( '/library/asset/css/ie.css' );
         
                \wp_enqueue_style( 'q-child-ie-css?__preload', $file_uri_ie, '', \q_theme::version );
                \wp_style_add_data( 'q-child-ie-css', 'conditional', 'IE' );

            }

            // css hierarchy ---- ##

            // track ##
            $found = false;

            // minified - .min - version used based on debugging setting ##
            $min = ( true === self::$debug ) ? '' : '.min' ;
            
            // handle ##
			$handle = 'q-child-theme-css';
			
			// base file name ##
			$base = 'theme';

            // array for files ##
            $files = [];

            // library/asset/css/theme.2.desktop(.min).css ## network site + device
            // $files[] = $base.".".\get_current_blog_id().".".h::device()."$min.css";

            // library/asset/css/theme.2(.min).css ## network site + all devices
            // $files[] = $base.".".\get_current_blog_id()."$min.css";

            // library/asset/css/theme.desktop(.min).css ## all network sites + device
            // $files[] = $base.".".h::device()."$min.css";

            // library/asset/css/theme(.min).css ## all networks + all devices
            $files[] = "$base$min.css";

            // loop over all files in priority, loading whichever is found first ##
            foreach( $files as $file ) {

				// load first found file ##
				if ( $found ) break;

				$file_uri = \q_theme::get_child_theme_url( '/library/asset/css/'.$file );
				$file_path = \q_theme::get_child_theme_path( '/library/asset/css/'.$file );

				// h::log( 'd:>looking up file: '.$file_uri );

                // file exists check ##
                if ( 
					file_exists( $file_path )
				) {

                    // h::log( 'd:>Loading up file: '.$file_uri );

					\wp_register_style( $handle, $file_uri.'?__preload', '', \q_theme::version );
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


		/*
		// load parent theme js
        if ( 
            isset( self::$option->theme_parent->js ) 
            && '1' == self::$option->theme_parent->js
        ) {

			// add JS ## -- after all dependencies ##
            // \wp_enqueue_script( 'q-plugin-js-theme', \q_theme::get_parent_theme_url( '/library/asset/js/theme.min.js' ), array( 'jquery' ), \q_theme::version );

			// JS hierarchy ---- ##

            // track ##
            $found = false;

            // minified - .min - version used based on debugging setting - local OR global ##
			$min = ( true === self::$debug ) ? '' : '.min' ;
			
			// debug from _source -> production from asset ##
			$asset_path = ( true === self::$debug ) ? '_source' : 'asset' ;
            
            // handle ##
			$handle = 'q-parent-theme-js';
			
			// base file name ##
			$base = 'theme';

            // array for files ##
            $files = [];

            // library/asset/js/theme.2.desktop(.min).js ## network site + device
            $files[] = $base.".".\get_current_blog_id().".".h::device()."$min.js";

            // library/asset/js/theme.2.theme(.min).js ## network site + all devices
            $files[] = $base.".".\get_current_blog_id()."$min.js";

            // library/asset/js/theme.1.desktop(.min).js ## all network sites + device
            $files[] = $base.".1.".h::device()."$min.js";

            // library/asset/js/theme(.min).js ## all networks + all devices
			$files[] = "$base$min.js";
			
			// OOPS - forgot to minify -- library/asset/js/theme.js ## all networks + all devices
            // $files[] = "$base.js";

            // loop over all files in priority, loading whichever is found first ##
            foreach( $files as $file ) {

				// load first found file ##
				if ( $found ) break;

				$file_uri = \q_theme::get_parent_theme_url( '/library/'.$asset_path.'/js/'.$file );
				$file_path = \q_theme::get_parent_theme_path( '/library/'.$asset_path.'/js/'.$file );

				h::log( 'd:>looking up file: '.$file_uri );

                // file exists check ##
                if ( 
					file_exists( $file_path )
				) {

                    h::log( 'd:>Loading up file: '.$file_uri );

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
                'debug'             => self::$debug,
                'nonce'             => $nonce
            ));

		}
		*/
		

		// load child theme JS
		if ( 
            isset( self::$option->theme_child->js ) 
            && '1' == self::$option->theme_child->js
        ) {

			// add JS ## -- after all dependencies ##
            // \wp_enqueue_script( 'q-plugin-js-theme', \q_theme::get_child_theme_url( '/library/asset/js/theme.min.js' ), array( 'jquery' ), \q_theme::version );

			// JS hierarchy ---- ##

            // track ##
            $found = false;

            // minified - .min - version used based on debugging setting - local OR global ##
            $min = ( true === self::$debug ) ? '' : '.min' ;
			
			// debug from _source -> production from asset ##
			$asset_path = ( true === self::$debug ) ? '_source' : 'asset' ;

            // handle ##
			$handle = 'q-child-theme-js';
			
			// base file name ##
			$base = 'theme';

            // array for files ##
            $files = [];

            // library/asset/js/theme.2.desktop(.min).js ## network site + device
            // $files[] = $base.".".\get_current_blog_id().".".h::device()."$min.js";

            // library/asset/js/theme.2(.min).js ## network site + all devices
            // $files[] = $base.".".\get_current_blog_id().".$min.js";

            // library/asset/js/theme.desktop(.min).js ## all network sites + device
            // $files[] = $base.".".h::device()."$min.js";

            // library/asset/js/theme(.min).js ## all networks + all devices
			$files[] = "$base$min.js";
			
			// OOPS.. forgot to minify... library/asset/js/theme.js ## all networks + all devices
            // $files[] = "$base.js";

            // loop over all files in priority, loading whichever is found first ##
            foreach( $files as $file ) {

				// load first found file ##
				if ( $found ) break;

				$file_uri = \q_theme::get_child_theme_url( '/library/'.$asset_path.'/js/'.$file );
				$file_path = \q_theme::get_child_theme_path( '/library/'.$asset_path.'/js/'.$file );

				// h::log( 'd:>looking up file: '.$file_uri );

                // file exists check ##
                if ( 
					file_exists( $file_path )
				) {

                    // h::log( 'd:>Loading up file: '.$file_uri );

					\wp_register_script( 
						$handle, 
						$file_uri.'?__js_defer', 
						array( 'jquery', 'q-module' ), 
						\q_theme::version, 
						// true 
					);
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
                'debug'             => self::$debug,
                'nonce'             => $nonce
            ));

		}
		

    }


}
