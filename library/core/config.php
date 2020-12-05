<?php

namespace q\core;

use q\core;
use q\core\helper as h;
use q\view;

class config extends \Q {

	private static $willow = null;

	/**
	 * Willow is in charge of config, so check for plugin, else return false with a warning
	 * 
	 * @since 4.1.0
	 */
	public static function get( $args = null ) {

		// check for Willow ##
		if( 
			! class_exists( 'willow' )
			&& ! class_exists( 'Q\willow\plugin' )
		){

			h::log( 'e:>Config loading requires Willow plugins, please install of activate' );

			return false;

		}

		// hmm ##
		return willow()->get( 'config' )->get( $args );

	}

}
