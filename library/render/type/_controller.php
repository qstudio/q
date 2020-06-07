<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\render;

// load it up ##
\q\render\type::run();

class type extends render {
	
	public static function run(){

		core\load::libraries( self::load() );

	}


    /**
    * Load Libraries
    *
    * @since        2.0.0
    */
    public static function load()
    {

		return $array = [

			// image src ##
			'src' => h::get( 'render/type/src.php', 'return', 'path' ),

			// post fields ##
			'post' => h::get( 'render/type/post.php', 'return', 'path' ),

			// author fields ##
			'author' => h::get( 'render/type/author.php', 'return', 'path' ),

			// category ##
			'category' => h::get( 'render/type/category.php', 'return', 'path' ),

		];

	}
	
	/** 
	 * bounce to function getter ##
	 * function name can be any of the following patterns:
	 * 
	 * the_group
	 * the_%%%
	 * 
	 * field_FIELDNAME // @todo
	 * type_IMAGE || ARRAY || WP_Object etc // @todo
	 */
	public static function __callStatic( $function, $args = null ){	

		// check if args format is correct ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log( 'Error in passed $args' );

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> $function,
				'value' => 'Error in pased $args'
			]);

			return false;

		}

		// $value needs to be a WP_Post object ##
		if ( ! $args[0] instanceof \WP_Post ) {

			h::log( 'Error in passed $args - not a WP_Post object' );

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> $function,
				'value' => 'Error in pased $args - not a WP_Post object'
			]);

			return false;

		}

		// $value needs to be a WP_Post object ##
		if ( ! isset( $args[1] ) ) {

			h::log( 'Error in passed $args - missing field' );

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> $function,
				'value' => 'Error in pased $args - missing field'
			]);

			return false;

		}

		// check if type allowed ##
		if ( ! array_key_exists( $function, self::get_allowed() ) ) {

			h::log( 'Value Type not allowed: '.$function );

			// log ##
			log::add([
				'key' => 'error', 
				'field'	=> $function,
				'value' => 'Value Type not allowed: '.$function
			]);

			return $args[0]->$args[1];

		}

		// test namespace ##
		$namespace = '\\q\\render\\type\\'.$function;
		$method_function = 'format';
		// h::log( $namespace.'::'.$function );

		// the__ methods ##
		if (
			\method_exists( $namespace, $method_function ) // && exists ##
			&& \is_callable([ $namespace, $method_function ]) // && exists ##
		) {

			// h::log( 'Found function: "'.$namespace.'::'.$method_function.'()"' );

			// call it and capture response ##
			$string = $namespace::{$method_function}( $args[0], $args[1] );

			// filter post fields -- global ##
			$string = \apply_filters( 
				'q/render/type/'.$args[1], $string 
			);

			// filter group/field -- field specific ##
			$string = \apply_filters( 
				'q/render/type/'.self::$args['group'].'/'.$args[1], $string
			);

			// test ##
			// h::log( $string );

			// return ##
			return $string;

		}

		// log ##
		h::log( 'No matching method found for: '.$function );

		// kick back nada - as this renders on the UI ##
		return false;

	}
	

    /**
     * Get allowed fomats with filter ##
     * 
     */
    public static function get_allowed()
    {

        return \apply_filters( 'q/render/type/get', self::$type );

    }


}