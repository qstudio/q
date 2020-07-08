<?php

// return an array ##
return [ 'taxonomy' => [

	// get/taxonomy/terms
	'terms'		=> [

		// config ##
		'config'				=> [ 
			'run' 				=> true, 
			'debug' 			=> false, 
		],
		// 
		
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

		// args ##
		'query_args' => [
			'taxonomy' 			=> 'category',
			'hide_empty' 		=> true,
			'parent'   			=> 0
		]
	],

]];
