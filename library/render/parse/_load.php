<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\render;

// load it up ##
\q\render\parse::run();

class parse extends render {

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

			// shared methods ##
			'tag' => h::get( 'render/parse/tag.php', 'return', 'path' ),

			// find + decode methods for variable + function arguments ##
			'arguments' => h::get( 'render/parse/arguments.php', 'return', 'path' ),

			// comments ##
			'comments' => h::get( 'render/parse/comments.php', 'return', 'path' ),

			// partials ##
			'partials' => h::get( 'render/parse/partials.php', 'return', 'path' ),

			// functions ##
			'functions' => h::get( 'render/parse/functions.php', 'return', 'path' ),

			// sections ##
			'sections' => h::get( 'render/parse/sections.php', 'return', 'path' ),

			// variables.. ##
			'variable' => h::get( 'render/parse/variable.php', 'return', 'path' ),

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
		variable::prepare(); // @todo ##

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
