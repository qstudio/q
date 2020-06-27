<?php

namespace q\search;

// Q ##
use q\core;

// Q Search ##
use q\search\core\helper as h;

// load it up ##
\q\search\admin::run();

class admin extends \q_search {

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
			'ajax' => self::get_plugin_path( 'library/admin/ajax.php' ),
			'library' => self::get_plugin_path( 'library/admin/option.php' )
		];

    }



}
