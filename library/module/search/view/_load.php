<?php

namespace q\module\search;

// Q ##
use q\core;
use q\core\helper as h;
use q\module;

// load it up ##
\q\module\search\view::run();

class view extends module\search {

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
			'render' => h::get( 'module/search/view/render.php', 'return', 'path' ),
		];

	}
	
}
