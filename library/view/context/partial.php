<?php

// return an array ##
return [

	// single post_meta ##
	'partial__single_post_meta'  => [
		'markup'				=> '<div class="row pb-1">
										<div class="col-12 the-post-meta">
											Posted {{ post_date_human }} ago in {{ category_name }}, Tagged {{ tags }} | <a href="#comment">{{ comments }}</a>
										</div>
									</div>'
	],

	// loop post_meta -- for search reults ##
	'partial__loop_post_meta'  => [
		'markup'				=> '<div class="row pb-1">
										<div class="col-12 the-post-meta">
											Posted {{ post_date_human }} ago in {{ category_name }}
										</div>
									</div>',
	],

];
