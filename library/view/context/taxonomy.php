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
			'wrap'				=> '<div class="row ml-0">
										<div class="col-12 list-group list-group-flush list-group navigation navigation-terms">
											{{ template }}
										</div>
									</div>',
			'template'			=> '
			 						{@ {: terms :}
			 						<a class="list-group-item list-group-item-action{{ active }}" href="{{ permalink }}">
			 							{{ title }}
			 						</a>
			 						@}	
								',
		],

		// args ##
		'query_args' => [
			'taxonomy' 			=> 'category',
			'hide_empty' 		=> true,
			'parent'   			=> 0
		]
	],

	// category ##
	'category' 			=> [
		'markup'				=> [
			'template'			=> '
								<span class="post-meta-category ml-1">
									<a class="p-1 p-sm-2 btn btn-primary" href="{{ permalink }}" title="See posts in {{ title }}">{{ title }}</a>
								</span>
								',
		]
	],

	// tag ##
	'tag' 				=> [
		'markup'				=> [
			'template'			=> '
								<span class="post-meta-tag ml-1">
									<a class="p-1 p-sm-2 btn btn-primary" href="{{ permalink }}" title="See posts in {{ title }}">{{ title }}</a>
								</span>
								',
		]
	],

	// tags ##
	'tags' 				=> [
		'markup'				=> [
			'template'			=> '
								<span class="post-meta-tags ml-1">
									{@ {: tags :}
										<a class="p-1 p-sm-2 btn btn-primary" href="{{ permalink }}" title="See posts in {{ title }}">{{ title }}</a>&nbsp;
									@}
								</span>
								',
		]
	],

]];
