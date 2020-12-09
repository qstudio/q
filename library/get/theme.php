<?php

namespace q\get;

use q\plugin as q;
use q\core\helper as h;

class theme {
	
	public static function is_child():bool {

		$theme = \wp_get_theme(); // gets the current theme
		// h::log( $theme );

		if ( $theme->template ) {
			return true;
		}

		return false;

	}


	public static function is_parent():bool {

		$theme = \wp_get_theme(); // gets the current theme
		
		if ( ! $theme->parent_theme ) {

			return true;

		}

		return false;

	}


}	
