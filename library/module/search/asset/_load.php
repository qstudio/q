<?php

namespace q\module\search;

// Q ##
use q\core;
use q\core\helper as h;
use q\module;

// load it up ##
\q\module\search\asset::run();

class asset extends module\search {

	public static function run(){

		// add module assets -- needs to be hooked before "wp_enqueue_scripts" ##
		\add_action( 'wp', function(){
			\q\asset\js::set([
				'module'     				=> 'search', // take clean class name ##
				'localize'  				=> [
					'site_name'         	=> \get_bloginfo("sitename"),
					'search'            	=> __( 'Search', 'q-search' ),
					'search_results_for'	=> __( 'Results', 'q-search' )
				],
				// 'debug'			=> true, // directly include JS file ##
		   ]);
		}, 10 );

	}


}
