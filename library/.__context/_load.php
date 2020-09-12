<?php

namespace q;

use q\core;
use q\core\helper as h;
// use q\render;

// load it up ##
\q\context::run();

class context extends \Q {

    public static function run(){

		// h::log( 'e:>HERE..' );

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

			'post' => h::get( 'context/post.php', 'return', 'path' ),
			'ui' => h::get( 'context/ui.php', 'return', 'path' ),
			'module' => h::get( 'context/module.php', 'return', 'path' ),
			'navigation' => h::get( 'context/navigation.php', 'return', 'path' )

		];

	}


}
