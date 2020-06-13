<?php

namespace q\render;

use q\core\helper as h;
use q\ui;
use q\get;
use q\render;

class field extends \q\render {

	public static function __callStatic( $function, $args ) {

        return self::acf( $args ); 
	
	}

	public static function field( $args = null ){

        // global arg validator ##
		if ( ! $args = ui\method::prepare_args( $args ) ){ return false; }

		// empty ##
		$array = [];

		// field ##
		$array['field'] = $args['field'];

        // grab classes ##
		$array['value'] = get\post::field( $args );

		// @todo -- pass field by render/format ##
		h::log( 't:>pass field by render/format' );

		// h::log( $array );

        // return ##
		return ui\method::prepare_render( $args, $array );

    }

}
