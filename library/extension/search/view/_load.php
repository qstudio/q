<?php

namespace q\extension\search;

// Q ##
use q\core;
use q\core\helper as h;
use q\extension;

// load it up ##
\q\extension\search\view::run();

class view extends extension\search {

	public static function run(){

		core\load::libraries( self::load() );

	}



    /**
    * Load Libraries
    *
    * @since        2.0
    */
    private static function load()
    {

		return [ 
			'render' => h::get( 'extension/search/view/render.php', 'return', 'path' ),
			// 'asset' => h::get( 'extension/search/view/asset/_load.php', 'return', 'path' ),
			// 'library' => h::get( 'ui/render.php', 'return', 'path' )
		];

	}
	
}
