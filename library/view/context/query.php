<?php

// return an array ##
return [ 'query' => [

	// get_post_by_meta() ##
	'get_post_by_meta' => [
		'meta_key'              => 'page_name',
		'post_type'             => 'page',
		'posts_per_page'        => 1,
		'order'					=> 'DESC',
		'orderby'				=> 'date'
	],

]];
