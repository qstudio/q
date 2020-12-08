<?php

namespace q\module;

// Q ##
use q\plugin as q;
use q\core;
use q\core\helper as h;

// load it up ##
\q\module\sticky::run();

class sticky {

	public static $post_types = [ 'post' ]; // default, filtered later ##
                
	/**
	 * Runner..
	 * 
	 * @since       0.2
	 * @return      void
	 */
	public static function run() 
	{

		// load libraries ##
		core\load::libraries( self::load() );

	}


	/**
	* Load Libraries
	*
	* @since        2.0
	*/
	private static function load()
	{

		$array = [

			// core ##
			'option' => h::get( 'module/sticky/core/option.php', 'return', 'path' )

		];

		// h::log( core\option::get('module') );
		if ( 
			! isset( core\option::get('module')->sticky )
			|| true !== core\option::get('module')->sticky 
		){

			// h::log( 'd:>Sticky is not enabled.' );

			return $array;

		}

		// h::log( 'd:>Sticky is enabled.' );

		// backend ##
		$array['admin'] = h::get( 'module/sticky/admin/admin.php', 'return', 'path' );
		$array['render'] = h::get( 'module/sticky/admin/render.php', 'return', 'path' );
		$array['method'] = h::get( 'module/sticky/core/method.php', 'return', 'path' );
		$array['ajax'] = h::get( 'module/sticky/core/ajax.php', 'return', 'path' );

		// require_once self::get_plugin_path( 'library/core/core.php' );
		// require_once self::get_plugin_path( 'library/core/ajax.php' );
		// // backend ##
		// require_once self::get_plugin_path( 'library/admin/admin.php' );
		// require_once self::get_plugin_path( 'library/admin/theme.php' );

		return $array;

	}

}
