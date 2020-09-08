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

			// Bootstrap ##
			'bs_modal' 		=> h::get( 'module/bs_modal.php', 'return', 'path' ),
			'bs_toast' 		=> h::get( 'module/bs_toast.php', 'return', 'path' ),
			'bs_tab' 		=> h::get( 'module/bs_tab.php', 'return', 'path' ),
			'bs_collapse' 	=> h::get( 'module/bs_collapse.php', 'return', 'path' ),
			'bs_form' 		=> h::get( 'module/bs_form.php', 'return', 'path' ),
			'bs_toggle' 	=> h::get( 'module/bs_toggle.php', 'return', 'path' ),
			'bs_gallery' 	=> h::get( 'module/bs_gallery.php', 'return', 'path' ),
			'bs_helper' 	=> h::get( 'module/bs_helper.php', 'return', 'path' ),
			'bs_scrollspy' 	=> h::get( 'module/bs_scrollspy.php', 'return', 'path' ),
			
			// Q ##
			'javascript' 	=> h::get( 'module/javascript.php', 'return', 'path' ),
			'navigation' 	=> h::get( 'module/navigation.php', 'return', 'path' ), 
			'cookie' 		=> h::get( 'module/cookie.php', 'return', 'path' ),
			'no_emoji' 		=> h::get( 'module/no_emoji.php', 'return', 'path' ),
			'grunt' 		=> h::get( 'module/grunt.php', 'return', 'path' ),
			// 'load' 			=> h::get( 'module/load.php', 'return', 'path' ),
			'comment' 		=> h::get( 'module/comment.php', 'return', 'path' ),
			'scroll' 		=> h::get( 'module/scroll.php', 'return', 'path' ),
			'push' 			=> h::get( 'module/push.php', 'return', 'path' ),

			// plugins ##
			'plugin_anspress' 	=> h::get( 'module/plugin_anspress.php', 'return', 'path' ),
			'plugin_fa_form' 	=> h::get( 'module/plugin_fa_form.php', 'return', 'path' ),
			
			// 'sharelines' 	=> h::get( 'module/sharelines.php', 'return', 'path' ),
			// 'popper' 		=> h::get( 'module/popper.php', 'return', 'path' ),
			// 'toggle' => h::get( 'module/toggle.php', 'return', 'path' ), // ?? needed ??
			// 'filter' => h::get( 'ui/module/filter.php', 'return', 'path' ),
			// 'modal' => h::get( 'ui/module/modal.php', 'return', 'path' ),
			// 'tab' => h::get( 'ui/module/tab.php', 'return', 'path' ),
			// 'select' => h::get( 'ui/module/select.php', 'return', 'path' ),
			// 'filter' => h::get( 'ui/module/filter.php', 'return', 'path' ),
			
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
		if ( $args['selected'] ) {
			
			$args['default'] = true;

			$args['count'] = self::$count;

			// iterate ##
			self::$count ++;

		}
		
		// look for module assets ( scss / js ) with matching name, to indicate which files will be included ##
		$scss = self::get_plugin_path( 'library/_source/scss/module/_'.$args['module'].'.scss' );
		if(
			file_exists( $scss )
		){

			$scss = self::get_plugin_url( 'library/_source/scss/module/_'.$args['module'].'.scss' );

			$args['name'] .= ' + SCSS: <a href="'.$scss.'" target="_blank">_'.$args['module'].'.scss</a>';

		}

		$js = self::get_plugin_path( 'library/_source/js/module/'.$args['module'].'.js' );
		if(
			file_exists( $js )
		){

			$js = self::get_plugin_url( 'library/_source/js/module/'.$args['module'].'.js' );

			$args['name'] .= ' + JS: <a href="'.$js.'" target="_blank">'.$args['module'].'.js</a>';

		}

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
