<?php

namespace q;

use q\core;
use q\core\helper as h;
use q\render;
use q\willow;

// load it up ##
\q\render::run();

class render { // why not extend \Q ?? @todo ##

	
	/**
	 * Fire things up
	*/
	public static function run(){

		// load libraries ##
		core\load::libraries( self::load() );

	}
	

    /**
    * Load Libraries
    *
    * @since        4.1.0
    */
    public static function load()
    {

		return $array = [

			// tag management ##
			// 'tags' => h::get( 'render/tags.php', 'return', 'path' ),

			// methods ##
			'method' => h::get( 'render/method.php', 'return', 'path' ),
			
			// validate and assign args ##
			// 'args' => h::get( 'render/args.php', 'return', 'path' ),

			// parser ##
			// 'parser' => h::get( 'render/parse/_load.php', 'return', 'path' ),

			// buffer processor ##
			// 'buffer' => h::get( 'render/buffer.php', 'return', 'path' ),

			// class extensions ##
			// 'extend' => h::get( 'render/extend.php', 'return', 'path' ),

			// check callbacks on defined fields ## 
			// 'callback' => h::get( 'render/callback.php', 'return', 'path' ),

			// get field data ##
			// 'get' => h::get( 'render/get.php', 'return', 'path' ), 

			// prepare and manipulate field data ##
			// 'fields' => h::get( 'render/fields.php', 'return', 'path' ), 

			// check format of each fields data and modify as required to markup ##
			// 'format' => h::get( 'render/format.php', 'return', 'path' ),

			// defined field types to generate field data ##
			// 'type' => h::get( 'render/type/_load.php', 'return', 'path' ),

			// prepare defined markup, search for and replace variables 
			// 'markup' => h::get( 'render/markup.php', 'return', 'path' ),

			// manage placeholders in markup object ## 
			// 'placeholder' => h::get( 'render/placeholder.php', 'return', 'path' ),

			// output string ##
			// 'output' => h::get( 'render/output.php', 'return', 'path' ),

			// log activity ##
			// 'log' => h::get( 'render/log.php', 'return', 'path' ),

			// context classes ##
			// 'context' => h::get( 'render/context/_load.php', 'return', 'path' ),

		];

	}
	



	/** 
	 * bounce to function getter ##
	 * function name can be any of the following patterns:
	 * 
	 * group__  acf field group
	 * block__  TODO
	 * field__  single post meta field ( can be any type, such as repeater )
	 * partial__  snippets, code, blocks, collections like post_meta
	 * post__  content, title, excerpt etc..
	 * media__
	 * navigation__ 
	 * taxonomy__
	 * ui__
	 * extension__
	 * widget__
	 */
	/*
	public static function __callStatic( $function, $args ){	

		// h::log( '$function: '.$function );

		// check class__method is formatted correctly ##
		if ( 
			false === strpos( $function, '__' )
		){

			h::log( 'e:>Error in passed render method: "'.$function.'" - should have format CLASS__METHOD' );

			return false;

		}	

		// we expect all render methods to have standard format CLASS__METHOD ##	
		list( $class, $method ) = explode( '__', $function );

		// sanity ##
		if ( 
			! $class
			|| ! $method
		){
		
			h::log( 'e:>Error in passed render method: "'.$function.'" - should have format CLASS__METHOD' );

			return false;

		}

		// h::log( 'd:>search if -- class: '.$class.'::'.$method.' available' );

		// look for "namespace/render/CLASS" ##
		$namespace = __NAMESPACE__."\\render\\".$class;
		// h::log( 'd:>namespace --- '.$namespace );

		if (
			class_exists( $namespace ) // && exists ##
		) {

			// reset args ##
			render\args::reset();

			// h::log( 'd:>class: '.$namespace.' available' );

			// h::log( $args );

			// take first array item, unwrap array - __callStatic wraps the array in an array ##
			if ( is_array( $args ) && isset( $args[0] ) ) { 
				
				// h::log('Taking the first array item..');
				$args = $args[0];
			
			}

			// extract markup from passed args ##
			render\markup::pre_validate( $args );

			// make args an array, if it's not ##
			if ( ! is_array( $args ) ){
			
				// h::log( 'Caste $args to array' );

				$args = [];
			
			}

			// define context for all in class -- i.e "post" ##
			$args['context'] = $class;

			// set task tracker -- i.e "title" ##
			$args['task'] = $method;

			// h::log( $args );

			if (
				! \method_exists( $namespace, 'get' ) // base method is get() ##
				&& ! \method_exists( $namespace, $args['task'] ) ##
				&& ! render\extend::get( $args['context'], $args['task'] ) // look for extends ##
			) {
	
				render\log::set( $args );
	
				h::log( 'e:>Cannot locate method: '.$namespace.'::'.$args['task'] );
	
				// we need to reset the class ##

				// reset all args ##
				render\args::reset();

				return false;
	
			}
	
			// validate passed args ##
			if ( ! render\args::validate( $args ) ) {
	
				render\log::set( $args );
				
				h::log( 'e:>Args validation failed' );

				// reset all args ##
				render\args::reset();
	
				return false;
	
			}

			// prepare markup, fields and handlers based on passed configuration ##
			willow\parse::prepare( $args );

			// call class::method to gather data ##
			// return render\ui::open( $args );
			// $namespace::run( $args );

			if (
				$extend = render\extend::get( $args['context'], $args['task'] )
			){

				// 	h::log( 'load extended method: '.$extend['class'].'::'.$extend['method'] );

				// gather field data from extend ##
				$extend['class']::{ $extend['method'] }( self::$args );

			} else if ( 
				\method_exists( $namespace, $args['task'] ) 
			){

				// 	h::log( 'load base method: '.$extend['class'].'::'.$extend['method'] );

				// gather field data from $method ##
				$namespace::{ $args['task'] }( self::$args );

			} else if ( 
				\method_exists( $namespace, 'get' ) 
			){

				// 	h::log( 'load default get() method: '.$extend['class'].'::'.$extend['method'] );

				// gather field data from get() ##
				$namespace::get( self::$args );

			} else {

				// oddly, no matching class::method found, so stop ##

				render\log::set( $args );
				
				h::log( 'e:>No matching class::method found' );

				// reset all args ##
				render\args::reset();
	
				return false;

			}

			// prepare field data ##
			render\fields::prepare();
			// h::log( self::$fields );
			// h::log( self::$markup );

			// check if feature is enabled ##
			if ( ! render\args::is_enabled() ) {

				render\log::set( $args );

				h::log( 'd:>Not enabled...' );

				// reset all args ##
				render\args::reset();
	
				return false;
	
		   }    
		
			// h::log( self::$fields );

			// Prepare template markup ##
			render\markup::prepare();

			// clean up left over tags ##
			willow\parse::cleanup();

			// optional logging to show removals and stats ##
			render\log::set( $args );

			// return or echo ##
			return render\output::prepare();

		}

		// nothing matched, so report and return false ##
		h::log( 'e:>No matching render context for: '.$namespace );

		// optional clean up.. how do we know what to clean ?? ##
		// @todo -- add shutdown cleanup, so remove all lost pieces ##

		// kick back nada - as this renders on the UI ##
		return false;

	}
	*/

}
