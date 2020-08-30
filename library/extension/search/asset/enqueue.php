<?php

namespace q\extension\search;

use q\core;
use q\core\helper as h;
use q\extension;

// load it up ##
// \q\search\ui\enqueue::run();

class enqueue extends extension\search {


	/*
	* Script enqueuer
	*
	* @since  2.0
	*/
	public static function wp_enqueue_scripts() {

		// h::log( 'ADDING Q Search assets...' );

		// minified - .min - version used based on debugging setting - local OR global ##
		$min = ( true === ( \Q::$debug ?: \Q::$debug ) ) ? '' : '.min' ;
		// $min = '' ; // temp over-rule ##

		// add JS ## -- after all dependencies ##
		\wp_enqueue_script( 
			'q-search-js', 
			h::get( "extension/search/asset/javascript/q.search$min.js", 'return' ), // overrideable ##
			array( 'jquery' ), 
			self::version, 
			true // in footer ##
		);

		// pass variable values defined in parent class ##
		\wp_localize_script( 'q-search-js', 'q_search', array(
				'ajaxurl'           => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ), 
				'debug'             => self::$debug,
				'site_name'         => \get_bloginfo("sitename")
			,   'search'            => __( 'Search', 'q-search' )
			,   'search_results_for'=> __( 'Results', 'q-search' )
			//,   'on_load_text' => __( 'Search & filter to see results', 'q-search' )
		));

	  }

}
