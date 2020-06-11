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
			'enqueue' => self::get_plugin_path( 'library/ui/asset/enqueue.php' ),
			'minifier' => self::get_plugin_path( 'library/ui/asset/minifier.php' ),
			'css' => self::get_plugin_path( 'library/ui/asset/css.php' ),
			'javascript' => self::get_plugin_path( 'library/ui/asset/javascript.php' )
		];

    }


}