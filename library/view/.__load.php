<?php

namespace q;

use q\plugin as q;
use q\core;
use q\core\helper as h;

// load it up ##
// \q\view::run();

class view {

	function __construct(){

		core\load::libraries( self::load() );

	}

    function hooks(){

		// start get things ##

	}

	/**
	* Load Libraries
	*
	* @since        2.0.0
	*/
	public static function load(){

		return $array = [

			// is methods ##
			'is' => q::get_plugin_path( 'library/view/is.php' ),

			// filters ##
			'view' => q::get_plugin_path( 'library/view/filter.php' ),

		];


	}

}

