<?php

namespace q\render\config;

use q\core;
use q\core\helper as h;
use q\render;

// load it up ##
// \q\render\config::run();

class context extends render {

	public static $cache = false;

	// public static function run(){

	// 	self::load();

	// }

    /**
    * Load Libraries
    *
    * @since        2.0.0
    */
    public static function data( $context = null, $task = null )
    {

		// cached?? ##
		if ( self::$cache ) { return self::$cache; }

		// start empty ##
		$return = [];

		$array = [

			// ui ##
			'ui' => h::get( 'render/config/context/ui.php', 'return', 'path' ),

			// partial ##
			'partial' => h::get( 'render/config/context/partial.php', 'return', 'path' ),

			// post ##
			'post' => h::get( 'render/config/context/post.php', 'return', 'path' ),

			// extension__search ##
			'search' => h::get( 'render/config/context/search.php', 'return', 'path' ),

			// taxonomy ##
			'taxonomy' => h::get( 'render/config/context/taxonomy.php', 'return', 'path' ),

			// media ##
			'media' => h::get( 'render/config/context/media.php', 'return', 'path' ),

			// navigation ##
			'navigation' => h::get( 'render/config/context/navigation.php', 'return', 'path' ),

		];

		// apply filter ##
		$array = \apply_filters( 'q/render/config/load', $array );

		// loop over files and direct merge array+array ##
		foreach ( $array as $k => $file ) {

			// h::log( 'looking in file: '.$file );
			$return += require( $file );

		}
		
		// then load parent /_config.php ( h::get methods above will pull from child, parent or theme.. ) - and merge ##
		if ( method_exists( 'q_theme', 'get_parent_theme_path' ) ){ 

			if ( $file = \q_theme::get_parent_theme_path( '/_config.php' ) ) {

				if ( is_file( $file ) ){

					// h::log( 'd:>Parent config found..' );
					$parent = require_once( $file );
					if( is_array( $parent ) ){

						// parent is an array ##
						// h::log( 'd:>Parent is an array..' );
						// h::log( $parent );

						// merge over defaults ##
						$return = core\method::parse_args( $parent, $return );

					}

				}

			}

		}

		// then load child /_config.php ( which is saved from acf routing ) --- and merge ##
		if ( method_exists( 'q_theme', 'get_child_theme_path' ) ){ 

			if ( $file = \q_theme::get_child_theme_path( '/_config.php' ) ) {

				if ( is_file( $file ) ){

					// h::log( 'd:>Child config found..' );
					$child = require_once( $file );
					if( is_array( $child ) ){

						// child is an array ##
						// h::log( 'd:>Child is an array..' );
						// h::log( $child );

						// merge over defaults ##
						$return = core\method::parse_args( $child, $return );

					}

				}

			}

		}

		// What about extended methods from parent / child ?? ##
		$extend = render\extend::get_all(); // get_all could return in correct format #### @todo
		// h::log( $extend );

		/*
		Array
		(
			[q\theme\render\ui] => Array
				(
					[context] => ui
					[class] => q\theme\render\ui
					[methods] => Array
						(
							[0] => hello
						)

				)

		)
		*/

		if (
			$extend
			&& is_array( $extend )
		){

			$extends = [];

			// base template ##
			$template = [
				'config'		=> [
					'run'		=> true,
					'debug'		=> false
				],
				'markup'		=> [
					'template'	=> '<div>{{ content }}</div>'
				]
			];

			foreach( $extend as $key => $value ){

				// h::log( $value );
				if( is_array( $value['methods'] ) ){

					// $methods = [];

					foreach( $value['methods'] as $method ){

						$extends[ $value['context'] ][$method] = $template;

					}

				}

				// $extends[ $value['context'] ] = $methods;

			}

			// h::log( $extends );

			// merge over defaults ##
			$return = core\method::parse_args( $extends, $return );

		}

		// cache and kick back ##
		return self::$cache = $return;

    }

}
