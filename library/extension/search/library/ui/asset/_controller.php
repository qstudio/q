<?php

namespace q\search\ui;

// Q ##
use q\core;

// Q Search ##
use q\search\core\helper as h;

// load it up ##
\q\search\ui\asset::run();

class asset extends \q_search {

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
			'enqueue' => self::get_plugin_path( 'library/ui/asset/enqueue.php' ),
			// 'minifier' => self::get_plugin_path( 'library/ui/asset/minifier.php' ),
			// 'css' => self::get_plugin_path( 'library/ui/asset/css.php' ),
			// 'javascript' => self::get_plugin_path( 'library/ui/asset/javascript.php' )
		];

    }


}
