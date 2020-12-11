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
* Factory Class
*/
final class factory {

	private
		$q // Q instance ##
		// $factory // q\factory object ##
		// $hook // q\factory\hook object ##
	;

    /**
     * Class constructor to define object props --> empty
     * 
     * @since   0.0.1
     * @return  void
    */
    function __construct() {

        // we need the current $q instance ##
		$this->q = \q\plugin::get_instance();

	}

	protected function q_ready():bool {

		// validate ##
		if (
			! $this->q
			|| ! $this->q instanceof q\plugin
		){

			error_log( 'Error loading $q instance' );

			return false;

		}

		return true;

	}

	/**
	 * Core Hooks
	*/
	function core(){

		// sanity ##
		if ( ! $this->q_ready() ){ return false; }

		// build hook objects ##
		$option = new \q\core\option();

		// set up debug option ##
		$option->hooks();


	}

	/**
	 * View classes
	 * 
	 * $filter sets up custom and native template filtering -- @todo, move away from static methods - encapsulate ..
	 * 
	*/
	function view(){

		// sanity ##
		if ( ! $this->q_ready() ){ return false; }

		// view filter ##
		$filter = new \q\view\filter();
		$filter->hooks();

	}

	/**
	 * Asset classes
	 * 
	*/
	function asset(){

		// sanity ##
		if ( ! $this->q_ready() ){ return false; }

		// enqueue scripts / styles ##
		$enqueue = new \q\asset\enqueue();
		$enqueue->hooks();
		
		// JS localize and tracking ##
		$js = new \q\asset\js();
		$js->hooks();

	}

	/**
	 * Admin classes
	 * 
	*/
	function admin(){

		// sanity ##
		if ( ! $this->q_ready() ){ return false; }

		// admin only ##
		if ( ! \is_admin() ) { return false; }

		// admin filters ##
		$filter = new \q\admin\filter();
		$filter->hooks();
		
		// Q options page ##
		$option = new \q\admin\option();
		$option->hooks();

	}

	/**
	 * Global Hooks
	*/
	function hook(){

		// sanity ##
		if ( ! $this->q_ready() ){ return false; }

		// build hook objects ##
		$admin_init = new \q\hook\admin_init();
		$admin_init->hooks();

		// $after_switch_theme = new \q\hook\after_switch_theme();
		// $comment_post = new \q\hook\comment_post();
		// $plugins_loaded = new \q\hook\plugins_loaded();
		// $save_post = new \q\hook\save_post();
		// $switch_theme = new \q\hook\switch_theme();
		
		$the_post = new \q\hook\the_post();
		$the_post->hooks();

		$wp_enqueue_script = new \q\hook\wp_enqueue_script();
		$wp_enqueue_script->hooks();
		
		$wp_enqueue_style = new \q\hook\wp_enqueue_style();
		$wp_enqueue_style->hooks();
		
		// $wp_footer = new \q\hook\wp_footer();
		
		$wp_head = new \q\hook\wp_head();
		$wp_head->hooks();

	}

	/***/
	function module(){

		// sanity ##
		if ( ! $this->q_ready() ){ return false; }

		// sticky module ##
		$sticky = new \q\module\sticky();
		$sticky->build();

	}

	/***/
	function plugins(){

		// sanity ##
		if ( ! $this->q_ready() ){ return false; }

		// acf filter ##
		$acf = new \q\plugins\acf();
		$acf->hooks();

	}

	/**
	 * Test classes
	 * 
	*/
	function test(){

		// sanity ##
		if ( ! $this->q_ready() ){ return false; }

		// test suite ##
		$test = new \q\test();
		$test->hooks();

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

			error_log( 'e:>Q requires ACF to run correctly..' );

			return false;

		}

		// ok ##
		return true;

	}

}
