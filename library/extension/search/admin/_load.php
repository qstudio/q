<?php

namespace q\extension\search;

// Q ##
use q\core;
use q\core\helper as h;
use q\extension;

// load it up ##
\q\extension\search\admin::run();

class admin extends extension\search {

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
			'ajax' => h::get( 'extension/search/admin/ajax.php', 'return', 'path' ),
			// 'library' => self::get_plugin_path( 'library/admin/option.php' )
		];

    }



}
