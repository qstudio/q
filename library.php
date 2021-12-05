<?php

namespace q;

// import classes ##
use q;
use q\plugin;
use q\core\helper as h;

// If this file is called directly, Bulk!
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/*
* Library Factory Class
*/
final class library {

    /**
     * Class constructor
     * 
     * @since   6.0.0
     * @return  void
    */
    function __construct() {

        // empty ##
		
	}

	function load(){

		// we need the current $q instance ##
		$q = q\plugin::get_instance();

		// validate ##
		if (
			! $q
			|| ! $q instanceof q\plugin
		){

			error_log( 'Error loading $q instance' );

			return false;

		}

		// array to load dynamically, via theme / plugin fallback lookup ##
		$array = [];

		// core <------- ##
		require_once $q::get_plugin_path( 'library/core/method.php' );
		require_once $q::get_plugin_path( 'library/core/helper.php' );
		require_once $q::get_plugin_path( 'library/core/filter.php' );
		require_once $q::get_plugin_path( 'library/core/load.php' );
		require_once $q::get_plugin_path( 'library/core/config.php' );
		require_once $q::get_plugin_path( 'library/core/device.php' );
		require_once $q::get_plugin_path( 'library/core/media.php' );
		require_once $q::get_plugin_path( 'library/core/option.php' );
		require_once $q::get_plugin_path( 'library/core/wpdb.php' );
		require_once $q::get_plugin_path( 'library/core/filter.php' );

		// get <------- ##

		// theme object ##
		$array['get/theme'] = h::get( 'get/theme.php', 'return', 'path' );

		// post object ##
		$array['get/post'] = h::get( 'get/post.php', 'return', 'path' );

		// view <------- ##

		// methods ##
		require_once $q::get_plugin_path( 'library/view/method.php' );

		// filters ##
		require_once $q::get_plugin_path( 'library/view/filter.php' );

		// update <--------- ##
		require_once $q::get_plugin_path( 'library/update/filter.php' );
		require_once $q::get_plugin_path( 'library/update/check.php' );

		// asset <------- ##

		// add assets ##
		$array['asset/enqueue'] = $q::get_plugin_path( 'library/asset/enqueue.php' );

		// minification ##
		$array['asset/minifier'] = $q::get_plugin_path( 'library/asset/minifier.php' );

		// js loaded ##
		$array['asset/js'] = $q::get_plugin_path( 'library/asset/js.php' );

		// module <------- ##
		require_once $q::get_plugin_path( 'library/module/module.php' );
		$array['module/sticky'] = h::get( 'module/sticky/sticky.php', 'return', 'path' );

		// admin <----- ##
		
		// functions ##
		$array['admin/method'] = h::get( 'admin/method.php', 'return', 'path' );

		// filters ##
		$array['admin/filter'] = h::get( 'admin/filter.php', 'return', 'path' );

		// actions ##
		$array['admin/action'] = h::get( 'admin/action.php', 'return', 'path' );

		// options ##
		$array['admin/option'] = h::get( 'admin/option.php', 'return', 'path' );

		// tinymce ##
		$array['admin/tinymce'] = h::get( 'admin/tinymce.php', 'return', 'path' );

		// test suite ##
		require_once $q::get_plugin_path( 'library/test/_load.php' );

		// hook <------- ##
		// $array['hook/switch_theme'] = h::get( 'hook/switch_theme.php', 'return', 'path' );
		$array['hook/admin_init'] = h::get( 'hook/admin_init.php', 'return', 'path' );
		$array['hook/after_switch_theme'] = h::get( 'hook/after_switch_theme.php', 'return', 'path' );
		// $array['hook/comment_post'] = h::get( 'hook/comment_post.php', 'return', 'path' );
		// $array['hook/save_post'] = h::get( 'hook/save_post.php', 'return', 'path' );

		// front-end hooks ##
		$array['hook/wp_enqueue_script'] = h::get( 'hook/wp_enqueue_script.php', 'return', 'path' );
		$array['hook/wp_enqueue_style'] = h::get( 'hook/wp_enqueue_style.php', 'return', 'path' );
		$array['hook/wp_head'] = h::get( 'hook/wp_head.php', 'return', 'path' );
		$array['hook/wp_footer'] = h::get( 'hook/wp_footer.php', 'return', 'path' );

		// global hooks ##
		$array['hook/the_post'] = h::get( 'hook/the_post.php', 'return', 'path' );
		$array['hook/plugins_loaded'] = h::get( 'hook/plugins_loaded.php', 'return', 'path' );

		// check for dependencies, required for UI components - admin will still run ##
		if ( ! self::has_dependencies() ) {

			return false;

		}

		// plugins required to run other plugins... ##
		$array['plugin/acf'] = h::get( 'plugins/acf.php', 'return', 'path' );

		// load array core\load ##
		core\load::libraries( $array );

	}

	/**
	 * Check for required breaking dependencies
	 *
	 * @return      Boolean
	 * @since       1.0.0
	 */
	public static function has_dependencies(){

		// check for what's needed ##
		if (
			! class_exists( 'ACF' )
		) {

			h::log( 'e:>Q requires ACF to run correctly..' );

			return false;

		}

		// ok ##
		return true;

	}

}
