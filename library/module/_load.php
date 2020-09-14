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
			// 'bootstrap_modal' 		=> h::get( 'module/bootstrap_modal.php', 'return', 'path' ),
			// 'bootstrap_toast' 		=> h::get( 'module/bootstrap_toast.php', 'return', 'path' ),
			// 'bootstrap_tab' 		=> h::get( 'module/bootstrap_tab.php', 'return', 'path' ),
			// 'bootstrap_collapse' 	=> h::get( 'module/bootstrap_collapse.php', 'return', 'path' ),
			// 'bootstrap_form' 		=> h::get( 'module/bootstrap_form.php', 'return', 'path' ),
			// 'bootstrap_toggle' 		=> h::get( 'module/bootstrap_toggle.php', 'return', 'path' ),
			// 'bootstrap_gallery' 	=> h::get( 'module/bootstrap_gallery.php', 'return', 'path' ),
			// 'bootstrap_helper' 		=> h::get( 'module/bootstrap_helper.php', 'return', 'path' ),
			// 'bootstrap_scrollspy' 	=> h::get( 'module/bootstrap_scrollspy.php', 'return', 'path' ),
			
			// Q ##
			// 'javascript' 			=> h::get( 'module/javascript.php', 'return', 'path' ),
			// 'navigation' 			=> h::get( 'module/navigation.php', 'return', 'path' ), 
			// 'cookie' 				=> h::get( 'module/cookie.php', 'return', 'path' ),
			// 'no_emoji' 				=> h::get( 'module/no_emoji.php', 'return', 'path' ),
			// 'grunt' 				=> h::get( 'module/grunt.php', 'return', 'path' ),
			// 'gist' 					=> h::get( 'module/gist.php', 'return', 'path' ),
			// 'google' 				=> h::get( 'module/google.php', 'return', 'path' ),
			// 'facebook' 				=> h::get( 'module/facebook.php', 'return', 'path' ),
			// 'linkedin' 				=> h::get( 'module/linkedin.php', 'return', 'path' ),
			// 'comment' 				=> h::get( 'module/comment.php', 'return', 'path' ),
			// 'scroll' 				=> h::get( 'module/scroll.php', 'return', 'path' ),
			// 'push' 					=> h::get( 'module/push.php', 'return', 'path' ),
			// 'twitter' 				=> h::get( 'module/twitter.php', 'return', 'path' ),
			// 'asana' 				=> h::get( 'module/asana.php', 'return', 'path' ),
			// 'nprogress' 			=> h::get( 'module/nprogress.php', 'return', 'path' ),
			// 'device' 				=> h::get( 'module/device/device.php', 'return', 'path' ),
			// 'consent' 				=> h::get( 'module/consent/consent.php', 'return', 'path' ),
			// 'search' 				=> h::get( 'module/search/search.php', 'return', 'path' ),

			// plugins ##
			// 'plugin_anspress' 		=> h::get( 'module/plugin_anspress.php', 'return', 'path' ),
			// 'plugin_fa_form' 		=> h::get( 'module/plugin_fa_form.php', 'return', 'path' ),
			// 'plugin_github' 		=> h::get( 'module/plugin_github.php', 'return', 'path' ),

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
		$scss = \q_theme::get_parent_theme_path( '/library/_source/scss/module/_'.$args['module'].'.scss' );
		if(
			file_exists( $scss )
		){

			$scss = \q_theme::get_parent_theme_url( '/library/_source/scss/module/_'.$args['module'].'.scss' );

			$args['name'] .= ' ~~ <strong>SCSS</strong>: <a href="'.$scss.'" target="_blank">_'.$args['module'].'.scss</a>';

		}

		$js = \q_theme::get_parent_theme_path( '/library/_source/js/module/'.$args['module'].'.js' );
		if(
			file_exists( $js )
		){

			$js = \q_theme::get_parent_theme_url( '/library/_source/js/module/'.$args['module'].'.js' );

			$args['name'] .= ' ~~ <strong>JS</strong>: <a href="'.$js.'" target="_blank">'.$args['module'].'.js</a>';

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
