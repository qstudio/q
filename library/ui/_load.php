<?php

namespace q\ui;

use q\core;
use q\core\helper as h;

// load it up ##
\q\ui\controller::run();

class controller extends \Q {

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

			// add assets ##
			'enqueue' => self::get_plugin_path( 'library/ui/enqueue.php' ),

			// minification ##
			'minifier' => self::get_plugin_path( 'library/ui/minifier.php' ),

			// css renderer ##
			'css' => self::get_plugin_path( 'library/ui/css.php' ),

			// js renderer ##
			'javascript' => self::get_plugin_path( 'library/ui/javascript.php' )

		];


	}

}

