<?php

namespace q\willow;

use q\core;
use q\willow\core\helper as h;
use q\willow;

// load it up ##
\q\willow\parse::run();

class parse extends \q_willow {

	protected static $regex = [
		'clean'	=>"/[^A-Za-z0-9_]/" // clean up string to alphanumeric + _
		// @todo.. move other regexes here ##
	];

	
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

			// markup methods ##
			'markup' => h::get( 'parse/markup.php', 'return', 'path' ),

			// find + decode methods for variable + function arguments ##
			'arguments' => h::get( 'parse/arguments.php', 'return', 'path' ),

			// comments ##
			'comments' => h::get( 'parse/comments.php', 'return', 'path' ),

			// partials ##
			'partials' => h::get( 'parse/partials.php', 'return', 'path' ),

			// functions ##
			'functions' => h::get( 'parse/functions.php', 'return', 'path' ),

			// sections ##
			'sections' => h::get( 'parse/sections.php', 'return', 'path' ),

			// variables.. ##
			'variable' => h::get( 'parse/variable.php', 'return', 'path' ),

		];

	}


    /**
     * Apply Markup changes to passed template
     * find all placeholders in self::$markup and replace with matching values in self::$fields
	 * most complex and most likely to clash go first, then simpler last ##
     * 
     */
    public static function prepare( $args = null ){

		// h::log( self::$args['markup'] );

		// pre-format markup to extract functions ##
		functions::prepare();

		// pre-format markup to extract sections ##
		sections::prepare();

		// search for partials in passed markup ##
		partials::prepare();

		// pre-format markup to extract comments and place in html ##
		comments::prepare(); // 

		// pre-format markup to extract variable arguments - 
		// goes last, as other tags might have added new variables to prepare ##
		variable::prepare();

	}



	/**
     * Apply Markup changes to passed template
     * find all placeholders in self::$markup and replace with matching values in self::$fields
	 * most complex and most likely to clash go first, then simpler last ##
     * 
     */
    public static function cleanup( $args = null ){

		// h::log( self::$args['markup'] );

		// clean up stray function tags ##
		functions::cleanup();

		// clean up stray section tags ##
		sections::cleanup();

		// clean up stray partial tags ##
		partials::cleanup();

		// clean up stray comment tags ##
		comments::cleanup(); // @todo ##

		// pre-format markup to extract functions ##
		variable::cleanup();

		// search for config settings passed in markup, such as "src" handle ##
		// argument::cleanup(); // @todo ##

	}

	

}
