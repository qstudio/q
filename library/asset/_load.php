<?php

namespace q;

use q\core;
use q\core\helper as h;

// load it up ##
\q\asset::run();

class asset extends \Q {

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
			'enqueue' => self::get_plugin_path( 'library/asset/enqueue.php' ),

			// minification ##
			'minifier' => self::get_plugin_path( 'library/asset/minifier.php' ),

			// css renderer ##
			'css' => self::get_plugin_path( 'library/asset/css.php' ),

			// scss compiler ##
			'scss' => self::get_plugin_path( 'library/asset/scss.php' ),

			// js renderer ##
			'javascript' => self::get_plugin_path( 'library/asset/javascript.php' )

		];


	}

}

