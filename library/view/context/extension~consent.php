<?php
 
//  namespace q\core;

//  use q\core as core;
use q\core\helper as h;

// quick check :) ##
defined( 'ABSPATH' ) OR exit;

// re-usable config ------ ##

// h::log( 't:>Check if extension->search array set, if not set when adding to cache.. to allow for simpler array format in config files??' ) ;

// return an array ##
return [ 'extension' => [ 'consent' => [

		'config'			=> [
		
			// run context->task ##
			'run' 			=> true,
			
			// context->task debugging ##
			'debug'			=> false,

			// do not echo ##
			'return'		=> 'return'
	
		],

		// config ##
		// 'application' 		=> 'posts',
		
		// markup ##
		'markup' 			=> [

			'settings'		=> '
								<div class="col-12 col-md-6 col-lg-4 ajax-loaded {{class }}">
									<a href="{{ post_permalink }}" title="{{ post_title }}">
										<div class="lazy card-img-top" data-src="{{ src }}" alt="Open {{ post_title }}" src="{{ src }}"></div>
									</a>
									<div class="card-body">
										<h5 class="card-title"><a href="{{ permalink }}" title="Read More">{{ post_title }}</a></h5>
										<p class="card-text">{{ post_excerpt }}</p>
										<p class="card-text">
											<small class="text-muted">{{ post_date_human }}</small>
											<small class="text-muted">in <a href="{{ category_permalink }}" title="{{ category_name }}">{{ category_name }}</a></small>    
										</p>
									</div>
								</div>',
			'bar'			=> '',
			'template'		=> '{{ consent }}'
		],
		
	]	

]];
