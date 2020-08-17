<?php

// return an array ##
return [ 'meta' => [

	'config'			=> [
		
		// run context->task ##
		'run' 			=> true,
		
		// context->task debugging ##
		'debug'			=> false,

	],

	// parent ##
	'parent'  				=> [
		'markup' 				=> '<h4 class="pb-1 the-parent"><a href="{{ permalink }}" title="Open {{ title }}">{{ title }}</a></h4>',
	],

	// post_meta
	'data' 				=> [
		'markup'				=> [
			'template'			=> '
								Posted {{ date_human }} ago 
								by <a href="{{ author_permalink }}" title="See posts by {{ author_title }}">{{ author_title }}</a>
								in <a href="{{ category_permalink }}" title="See posts in {{ category_title }}">{{ category_title }}</a>,
								tagged: {@ {: tags :}
									<a href="{{ tag_permalink }}" title="See posts in {{ tag_title }}">{{ tag_title }}</a>&nbsp;
								@}
								<a href="{{ comment_permalink }}" title="Comments" class="btn btn-primary">{{ comment_title }}</a>
								',
			'wrap'				=> '<div class="post-meta-data col-12">{{ template }}</div>'
		]
	],

	// author ##
	'author' 				=> [
		'markup'				=> [
			'template'			=> '
								Posted {{ date_human }} ago 
									by <a href="{{ author_permalink }}" title="See posts by {{ author_title }}">{{ author_title }}</a>
								</div>
								',
		]
	],

	// date in human format - @ since ##
	'date_human' 			=> [
		'markup'				=> [
			'template'			=> '
								<span class="post-meta-date-human">
									Posted {{ date_human }} ago 
								</span>
								',
		]
	],

	// date in passed format - etc yyyy/mm/dd ##
	'date' 			=> [
		'markup'				=> [
			'template'			=> '
								<span class="post-meta-date">
									Posted {{ date }} 
								</span>
								',
		]
	],

	// comment ##
	'comment' 				=> [
		'markup'				=> [
			'template'			=> '
								<span class="post-meta-comment mx-1">
									<a class="p-1 p-sm-2 btn btn-primary q_comment_loadmore" href="{{ permalink }}" title="{{ title }}">{{ title }}</a>
								</span>
								',
		]
	],


]];
