<?php

namespace q\asset;

use q\plugin as q;
use q\core;
use q\core\helper as h;

class enqueue {

	private $option;
	private $q;
	
	function __construct(){

		// grab the options ##
		$this->option = core\option::get();
		
		// we need the current $q instance ##
		$this->q = \q\plugin::get_instance();

	}

    function hooks(){

        if ( ! \is_admin() ) {

			// plugins and enhanecments ##
			\add_action( 'wp_enqueue_scripts', array ( $this, 'wp_enqueue_scripts_general' ), 2 );
			
			// local external scripts ##
			\add_action( 'wp_enqueue_scripts', array ( $this, 'wp_enqueue_scripts_external' ), 3 );

            // local optional scripts ##
			\add_action( 'wp_enqueue_scripts', array ( $this, 'wp_enqueue_scripts_local' ), 4 );
			
			// plugin css / js -- includes defaults and resets and snippets from controllers ##
            \add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts_module' ), 999 );

            // css / js from theme ##
			\add_action( 'wp_enqueue_scripts', array ( $this, 'wp_enqueue_scripts_theme' ), 1000 );

        }

    }

    /**
     * Check for required classes to build UI features
     * 
     * @return      Boolean 
     * @since       0.1.0
     */
    public static function has_dependencies(){

        // check for what's needed ##
        if (
            ! function_exists( 'q_theme' )
        ) {

            h::log( 'e:>Q requires q_theme to run correctly..' );

            return false;

        }

        // ok ##
        return true;

    }

    /*
    * script enqueuer 
    *
    * @since  2.0
    */
    function wp_enqueue_scripts_external() {

        // dump - shuold be an interger repesenting how many external libraries are added ##
        // h::log( $this->option->external );
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
            ! isset( $this->option->external )
            || 1 > $this->option->external
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

                        \wp_register_style( $handle, $url.'?__nodefer', '', $version, 'all' );
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
    function wp_enqueue_scripts_module() {

		// check we have dependencies ##
        if ( ! self::has_dependencies() ){

            return false;

        }

        // h::log( $this->option->module );
        // h::log( 'd:>debug set to: '. ( true === q::$_debug ? 'True' : 'False' ) );

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
			isset( $this->option->module_asset->js ) 
			&& 1 == $this->option->module_asset->js
        ) {

			// load list of modules, stored in site_option "q_modules" - includes list of parameters to localize ##
			$q_modules = \get_option( "q_modules" );
			// h::log( $q_modules );

			// minified - .min - version used based on debugging setting - local OR global ##
			// $min = ( true === q::$_debug ) ? '' : '.min' ;
			// $min = '.min' ;

			if ( 
				true === q::$_debug 
			){

				// cache-buster hash used based on debugging setting - local OR global ##
				$hash = 'hash='.rand() ;

				// get all "active" modules ##
				// $modules = $this->js->get();
				$modules = $this->q->get( '_q_modules' );

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
					$defer = in_array( $module, [  ] ) ? '?__nodefer&' : '?__js_defer&' ; // js_async

					// h::log( 'd:>Load JS modules: '.'library/asset/js/module/'.$module.'.js' );

					// include single file with cache-busting hash ##
					\wp_enqueue_script( 
						$handle, 
						$file_url.$defer.$hash, 
						$dependecy, 
						q::$_version,
						// true
					);

				}

			} else {

				// add single module.min.js ##
				\wp_enqueue_script( 
					'q-module', 
					\q\theme\plugin::get_child_url( "/library/asset/js/module.min.js?__js_defer" ), 
					array( 'jquery' ), 
					\q\theme\child\plugin::$_version,
					// true
				);

			}

			// default localize values ##
			$localize = [
				'ajaxurl'           => \admin_url( 'admin-ajax.php' ), // q_data.ajaxurl
				'debug'             => q::$_debug, // q_data.debug
				'nonce'             => \wp_create_nonce( 'q_data_nonce' ), // q_data.nonce
			];

			// filter localize array from plugins ##
			$localize = \apply_filters( 'q/asset/localize', $localize );

			// h::log( $localize );

            // pass variable values defined in parent class ##
			\wp_localize_script( 
				'q-module', 
				'q_data', 
				$localize
			);

        }


    }





    /*
    * script enqueuer 
    *
    * @since  2.0
    */
    function wp_enqueue_scripts_general() {

        // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions ##
		\wp_register_script( 'q-html5', h::get( "vendor/js/html5.js?__js_defer", 'return' ), array(), q::$_version, 'all' );
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
    function wp_enqueue_scripts_local() {

        // h::debug( $this->option->library );

        // loop over libraries and include - checking for "min" version is debugging ##
        foreach( $this->option->library as $key => $value ) {

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
            // $min = ( true === q::$_debug ) ? '' : '.min' ;
            
            // handle ##
			$handle = 'q-local-'.$key;
			
			// directory ##
			$directory = 'vendor/'.$type_dir.'/';

            // array for files ##
			$files = [];
			
			// if debugging, try to load non-minified version first ##
			if ( true === q::$_debug ) { $files[] = $type[1].".".$type_ext; }

            // load minified version ##
			$files[] = $type[1].".min.".$type_ext;
			
			// load non-minified version as last option ##
			$files[] = $type[1].".".$type_ext;

            // loop over all files in priority, loading whichever is found first ##
            foreach( $files as $file ) {

				// load first found file ##
				if ( $found ) break;

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
		
							\wp_register_style( $handle, $load, '', q::$_version, 'all' );
							\wp_enqueue_style( $handle );
		
						break ;
		
						case "js" :
		
							\wp_register_script( $handle, $load.'?__js_defer', [ 'jquery' ], q::$_version, 'all' );
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
    function wp_enqueue_scripts_theme(){

		// check we have dependencies ##
        if ( ! self::has_dependencies() ){

			// h::log( 'e:>No Q Theme class...');

            return false;

		}
		
		// Load Child CSS
		if ( 
            isset( $this->option->theme_child->css ) 
            && '1' == $this->option->theme_child->css    
        ) {

			// \wp_register_style( 'q-plugin-css-theme', \q\theme\plugin::get_child_path( '/library/asset/css/theme.min.css' ), array(), \q\theme\plugin::version, 'all' );
			// \wp_enqueue_style( 'q-plugin-css-theme' );

            // h::log( 'd:> Loading Child CSS...' );

            // IE ##
            if ( 
				file_exists( 
					$file_path_ie = \q\theme\plugin::get_child_path( '/library/asset/css/ie.css' )
				)
			) {

				$file_uri_ie = \q\theme\plugin::get_child_url( '/library/asset/css/ie.css' );
         
                \wp_enqueue_style( 'q-child-ie-css', $file_uri_ie, '', \q\theme\child\plugin::$_version );
                \wp_style_add_data( 'q-child-ie-css', 'conditional', 'IE' );

            }

            // css hierarchy ---- ##

            // track ##
            $found = false;

            // minified - .min - version used based on debugging setting ##
            $min = ( true === q::$_debug ) ? '' : '.min' ;
            
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

				$file_uri = \q\theme\plugin::get_child_url( '/library/asset/css/'.$file );
				$file_path = \q\theme\plugin::get_child_path( '/library/asset/css/'.$file );

				// h::log( 'd:>looking up file: '.$file_uri );

                // file exists check ##
                if ( 
					file_exists( $file_path )
				) {

                    // h::log( 'd:>Loading up file: '.$file_uri );

					\wp_register_style( $handle, $file_uri.'?__preload', '', \q\theme\child\plugin::$_version );
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

		// load child theme JS
		if ( 
            isset( $this->option->theme_child->js ) 
            && '1' == $this->option->theme_child->js
        ) {

			// JS hierarchy ---- ##

            // track ##
            $found = false;

            // minified - .min - version used based on debugging setting - local OR global ##
            $min = ( true === q::$_debug ) ? '' : '.min' ;
			
			// debug from _source -> production from asset ##
			$asset_path = ( true === q::$_debug ) ? '_source' : 'asset' ;

            // handle ##
			$handle = 'q-child-theme-js';
			
			// base file name ##
			$base = 'theme';

            // array for files ##
            $files = [];

            // library/asset/js/theme(.min).js ## all networks + all devices
			$files[] = "$base$min.js";
			
            // loop over all files in priority, loading whichever is found first ##
            foreach( $files as $file ) {

				// load first found file ##
				if ( $found ) break;

				$file_uri = \q\theme\plugin::get_child_url( '/library/'.$asset_path.'/js/'.$file );
				$file_path = \q\theme\plugin::get_child_path( '/library/'.$asset_path.'/js/'.$file );

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
						\q\theme\child\plugin::$_version, 
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
                'debug'             => q::$_debug,
                'nonce'             => $nonce
            ));

		}
		

    }


}
