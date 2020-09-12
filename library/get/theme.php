<?php

namespace q\get;

use q\core\helper as h;
use q\get;

class theme extends \q\get {
	
	public static function is_child()
	{

		$theme = \wp_get_theme(); // gets the current theme
		// h::log( $theme );

		if ( $theme->template ) {
			return true;
		}

		return false;

	}


	public static function is_parent()
	{

		$theme = \wp_get_theme(); // gets the current theme
		
		if ( ! $theme->parent_theme ) {
			return true;
		}

		return false;

	}


}	
