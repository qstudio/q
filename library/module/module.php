<?php

namespace q;

use q\core;
use q\core\helper as h;

class module {

	// properties ##
	public static $count = 0; // count modules added ##

	function __construct(){}
	
	/**
    * Filter modules via ACF options page
    *
    * @since        2.0.0
    */
    public static function filter( $args = null ){

		// sanity ##
		if( 
			is_null( $args ) 
			|| ! is_array( $args )	
			|| ! isset( $args['module'] )
			|| ! isset( $args['name'] )	
		){

			return false;

		}

		// should this item be pre-selected on the Options page ? ##
		if ( isset( $args['selected'] ) ) {
			
			$args['default'] = true;

			$args['count'] = self::$count;

			// iterate ##
			self::$count ++;

		}

		// style <---- crude ##
		$style = 'style="background: #ddd; padding: 2px 6px; font-weight: strong;" class=""';
		
		// look for module assets ( scss / js ) with matching name, to indicate which files will be included ##
		$scss = h::get( '_source/scss/module/_'.$args['module'].'.scss', 'return', 'path' );
		if(
			file_exists( $scss )
		){

			// find location ##
			$location = ( false !== strpos( $scss, 'q-theme-parent' ) ) ? "[ Parent ]" : '[ Child ]' ; 

			$scss = h::get( '_source/scss/module/_'.$args['module'].'.scss', 'return', 'url' );

			$args['name'] .= ' ~ <a '.$style.' href="'.$scss.'" target="_blank">_'.$args['module'].'.scss</a> '.$location;

		}

		$js = h::get( '_source/js/module/'.$args['module'].'.js', 'return', 'path' );
		if(
			file_exists( $js )
		){

			// find location ##
			$location = ( false !== strpos( $js, 'q-theme-parent' ) ) ? "[ Parent ]" : '[ Child ]' ; 

			$js = h::get( '_source/js/module/'.$args['module'].'.js', 'return', 'url' );

			$args['name'] .= ' ~ <a '.$style.' href="'.$js.'" target="_blank">'.$args['module'].'.js</a> '.$location;

		}

		// wrap ##
		// $args['name'] = '<span style="display: inline-block; padding: 8px 0px;">'.$args['name'].'</span>';

		// add option, via filter ##
		\add_filter( 'acf/load_field/name=q_option_module', function( $field ) use( $args ) {

			// pop on a new choice ##
			$field['choices'][$args['module']] = $args['name'];

			// make it selected ##
			if( isset( $args['default'] ) ) $field['default_value'][$args['count']] = $args['module'];

			// kick back ##
			return $field;

		}, 10, 1 );

	}

}
