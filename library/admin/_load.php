<?php

namespace q;

use q\core;
use q\core\helper as h;

// load it up ##
\q\admin::run();

class admin extends \Q {

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

			// functions ##
			'method' => h::get( 'admin/method.php', 'return', 'path' ),

			// filters ##
			'filter' => h::get( 'admin/filter.php', 'return', 'path' ),

			// actions ##
			'action' => h::get( 'admin/action.php', 'return', 'path' ),

			// options ##
			'option' => h::get( 'admin/option.php', 'return', 'path' ),

			// tinymce ##
			'tinymce' => h::get( 'admin/tinymce.php', 'return', 'path' ),

		];

    }

}
