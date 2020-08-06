<?php

namespace q\extension\search;

// Q ##
use q\core;
use q\core\helper as h;
use q\extension;

// load it up ##
\q\extension\search\asset::run();

class asset extends extension\search {

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
			'enqueue' => h::get( 'extension/search/asset/enqueue.php', 'return', 'path' ),
		];

    }


}
