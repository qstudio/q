<?php

namespace q\module;

// Q ##
use q\plugin as q;
use q\core;
use q\core\helper as h;

// load it up ##
// \q\module\sticky::run();

class sticky {

	public static $post_types = [ 'post' ]; // default, filtered later ##

	function __construct(){}
                
	/**
	 * Runner..
	 * 
	 * @since       0.2
	 * @return      void
	 */
	function build(){

		// load libraries ##
		core\load::libraries( $this->libraries() );

		// add options page ##
		$option = new \q\module\sticky\option();
		$option->hooks();

		// h::log( core\option::get('module') );
		if ( 
			! isset( core\option::get('module')->sticky )
			|| true !== core\option::get('module')->sticky 
		){

			// h::log( 'd:>Sticky is not enabled.' );

			return false;

		}

		// admin ##
		$admin = new \q\module\sticky\admin();
		$admin->hooks();

		// render ##
		$render = new \q\module\sticky\render();
		$render->hooks();

		// ajax ##
		$ajax = new \q\module\sticky\ajax();
		$ajax->hooks();

	}


	/**
	* Load Libraries
	*
	* @since        2.0
	*/
	function libraries(){

		$array = [];

		$array['module/sticky/option'] = h::get( 'module/sticky/core/option.php', 'return', 'path' );
		$array['module/sticky/admin'] = h::get( 'module/sticky/admin/admin.php', 'return', 'path' );
		$array['module/sticky/render'] = h::get( 'module/sticky/admin/render.php', 'return', 'path' );
		$array['module/sticky/method'] = h::get( 'module/sticky/core/method.php', 'return', 'path' );
		$array['module/sticky/ajax'] = h::get( 'module/sticky/core/ajax.php', 'return', 'path' );

		return $array;

	}

}
