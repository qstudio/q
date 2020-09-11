<?php

namespace q\core;

use q\core;
use q\core\helper as h;
use q\view;

// \q\core\config::run();

class config extends \Q {

	/**
	 * Willow is in charge of config, so check for plugin, else return false with a warning
	 * 
	 * @since 4.1.0
	 */
	public static function get( $args = null ) {

		// check for Willow ##
		if( ! class_exists( 'willow' ) ){

			h::log( 'e:>Config loading requires Q Willow plugins, please install of activate' );

			return false;

		}

		// return via Willow ##
		return \willow\core\config::get( $args );

	}



}
