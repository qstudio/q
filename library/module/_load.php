<?php

namespace q;

use q\core;
use q\core\helper as h;

// load it up ##
\q\module::__run();

class module extends \Q {

	// properties ##
	public static $count = 0; // count modules added ##

	public static function __run(){

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

			// admin ##
			'sticky' 				=> h::get( 'module/sticky/sticky.php', 'return', 'path' ),

		];


	}
	

	
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
		
		// look for module assets ( scss / js ) with matching name, to indicate which files will be included ##
		// $scss = \q_theme::get_parent_theme_path( '/library/_source/scss/module/_'.$args['module'].'.scss' );
		$scss = h::get( '_source/scss/module/_'.$args['module'].'.scss', 'return', 'path' );
		if(
			file_exists( $scss )
		){

			// $scss = \q_theme::get_parent_theme_url( '/library/_source/scss/module/_'.$args['module'].'.scss' );
			$scss = h::get( '_source/scss/module/_'.$args['module'].'.scss', 'return', 'url' );

			$args['name'] .= ' ~ <a style="background: #ddd; padding: 2px 6px; font-weight: strong;" class="" href="'.$scss.'" target="_blank">_'.$args['module'].'.scss</a>';

		}

		// $js = \q_theme::get_parent_theme_path( '/library/_source/js/module/'.$args['module'].'.js' );
		$js = h::get( '_source/js/module/'.$args['module'].'.js', 'return', 'path' );
		if(
			file_exists( $js )
		){

			// $js = \q_theme::get_parent_theme_url( '/library/_source/js/module/'.$args['module'].'.js' );
			$js = h::get( '_source/js/module/'.$args['module'].'.js', 'return', 'url' );

			$args['name'] .= ' ~ <a style="background: #ddd; padding: 2px 6px; font-weight: strong;" class="" href="'.$js.'" target="_blank">'.$args['module'].'.js</a>';

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
