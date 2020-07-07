<?php

// return an array ##
return [ 'taxonomy' 			=> [

	'_global'					=> [
		'config' 				=> [
			'run' 				=> true,
			'debug'				=> false
		],
	],

	// get/taxonomy/terms
	'terms'						=> [

		'markup'            	=> [
			'wrap'				=> '<div class="row">
										<div class="col-12 list-group list-group-flush list-group navigation navigation-terms">
											{{ content }}
										</div>
									</div>',
			'template'			=> '
			 						{{# terms }}
			 						<a class="list-group-item list-group-item-action{{ active }}" href="{{ permalink }}">
			 							{{ title }}
			 						</a>
			 						{{/#}}	
								',
		],
		'args' 					=> [
			'taxonomy' 			=> 'category',
			'hide_empty' 		=> true,
			'parent'   			=> 0
		]
	],

]];
