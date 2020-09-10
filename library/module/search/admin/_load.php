<?php

namespace q\module\search;

// Q ##
use q\core;
use q\core\helper as h;
use q\module;

// load it up ##
\q\module\search\admin::__run();

class admin extends module\search {

    public static function __run(){

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
			'ajax' => h::get( 'module/search/admin/ajax.php', 'return', 'path' ),
		];

    }



}
