<?php

namespace q\module;

use q\core;
use q\core\helper as h;

// let'#'s go ##
\q\module\navigation::__run();

class navigation extends \Q {

	public static function __run(){

		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> str_replace( __NAMESPACE__.'\\', '', static::class ),
			'name'		=> 'Q ~ Navigation',
			'selected'	=> true,
		]);

		// h::log( core\option::get('modal') );
		if ( 
			! isset( core\option::get('module')->navigation )
			|| true !== core\option::get('module')->navigation 
		){

			// h::log( 'd:>Modal is not enabled.' );

			return false;

		}

		\add_filter( 'nav_menu_css_class', [ get_class(), 'nav_menu_css_class_li' ], 1, 3 );

	}


	/**
	 * Filter nav_menu LI
	 * 
	 * @link	https://stackoverflow.com/questions/14464505/how-to-add-class-in-li-using-wp-nav-menu-in-wordpress
	*/
	public static function nav_menu_css_class_li($classes, $item, $args) {

		if( isset( $args->li_class ) ) {

			$classes[] = $args->li_class;

		}

		return $classes;

	}



}
