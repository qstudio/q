<?php

namespace q\ui;

use q\core;
use q\core\helper as h;

// load it up ##
\q\ui\asset::run();

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
			'enqueue' => h::get( 'ui/asset/enqueue.php', 'return', 'path' ),
			'minifier' => h::get( 'ui/asset/minifier.php', 'return', 'path' ),
			'css' => h::get( 'ui/asset/css.php', 'return', 'path' ),
			'javascript' => h::get( 'ui/asset/javascript.php', 'return', 'path' )
		];

    }


}