<?php

namespace q\search;

// Q ##
use q\core;

// Q Search
use q\search\core\helper as h;
use q\search;

// load it up ##
\q\search\ui::run();

class ui extends \q_search {

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
			'render' => self::get_plugin_path( 'library/ui/render.php' ),
			'asset' => self::get_plugin_path( 'library/ui/asset/_controller.php' ),
			// 'library' => h::get( 'ui/render.php', 'return', 'path' )
		];

	}
	
}
