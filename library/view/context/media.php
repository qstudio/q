<?php

// return an array ##
return [ 'media' => [

	'config'			=> [
		
		// run context->task ##
		'run' 			=> true,
		
		// context->task debugging ##
		'debug'			=> false,

	// ],

	// // image sizes ## @todo - add sizes and function to add via add_image_sizes in config ##
	// 'src'							=> [ 

		// add srcset to src references ##
		'srcset' 					=> false, 

		// get attachment media data -- can be enabled on a per reference basis ##
		'meta' 						=> true, 
		
		// @ todo, make this a global controller ##
		// wrap src in 'picture' element, with srcset ##
		'picture' 					=> false,

		// holder as data ref ##
		'holder'					=> 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjxzdmcgZGF0YS1uYW1lPSJMYXllciAxIiBpZD0iTGF5ZXJfMSIgdmlld0JveD0iMCAwIDUxMiA1MTIiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHN0eWxlPi5jbHMtMXtmaWxsOm5vbmU7c3Ryb2tlOiMwODNiNDM7c3Ryb2tlLWxpbmVjYXA6cm91bmQ7c3Ryb2tlLWxpbmVqb2luOnJvdW5kO3N0cm9rZS13aWR0aDoyMHB4O30uY2xzLTJ7ZmlsbDojMDgzYjQzO308L3N0eWxlPjwvZGVmcz48dGl0bGUvPjxjaXJjbGUgY2xhc3M9ImNscy0xIiBjeD0iMjU2IiBjeT0iMjY3LjQyIiByPSIzMy45OSIvPjxwb2x5Z29uIGNsYXNzPSJjbHMtMSIgcG9pbnRzPSIzMDIuMiAyMDIuNTIgMjg4LjkgMTc5LjY4IDIyMy4xIDE3OS42OCAyMDkuOCAyMDIuNTIgMTQ0IDIwMi41MiAxNDQgMzMyLjMyIDM2OCAzMzIuMzIgMzY4IDIwMi41MiAzMDIuMiAyMDIuNTIiLz48Y2lyY2xlIGNsYXNzPSJjbHMtMiIgY3g9IjMzNy44IiBjeT0iMjMyLjQ5IiByPSIxMS40OSIvPjwvc3ZnPg==',

		// Golden Ratio ##
		'generate'					=> true, // set to false to skip generation of these images ##
		'ratio' 					=> 1.618,
		'open' 						=> 9999, // open size ##
		'open_height' 				=> 600, // open size ##
		'scale'						=> 1, // scale sizes ##
		'pixel'						=> 2, // double vision ##
		'default'					=> 'horizontal-lg', // default handle ##

		// shape handles ##
		'handles'					=> [
			'square' 				=> [
				'sizes'				=> 'all', // create in all sizes 
				'width'				=> 'equal', // height = width ##
				'height'			=> 'equal', // height = width ##
				'pixel'				=> true, // handle for pixel image ##
				'crop'				=> true, // hard crop ##
				'open'				=> false // no open sized format
			],
			'horizontal' 			=> [
			 	'sizes'				=> 'all', // create in all sizes 
			 	'width'				=> 'equal', // height = width \ ratio ##
				'height'			=> 'divide', // height = width \ ratio ## 
			 	'crop'				=> true, // hard crop ##
				'open'				=> 'width', // open sized image with no fixed width
				'pixel'				=> true, // handle for pixel image ##
			],
			'vertical' 				=> [
			 	'sizes'				=> 'all', // create in all sizes 
			 	'width'				=> 'equal', // height = width \ ratio ##
				'height'			=> 'multiply', // height = width * ratio ## 
			 	'crop'				=> true, // hard crop ##
				'open'				=> 'height', // open sized image with no fixed width
				'pixel'				=> true, // handle for pixel image ##
			],
		],

		// image_size logic based on BS grid breakpoints ##
		'sizes'						=> [
			'xs'					=> 300,
			'sm'					=> 576,
			'md'					=> 720,
			'lg'					=> 960,
			'xl'					=> 1200, 
		]
	],

	// post_thumbnail -- ready for lazy loading ##
	'thumbnail' 					=> [
		'markup' 					=> '<img class="col-12 fill lazy mt-2 mb-2" src="" data-src="{{ src }}" srcset="{{ src_srcset }}" sizes="{{ src_sizes }}" alt="{{ src_alt }}" data-src-caption="{{ src_caption }}" data-src-title="{{ src_title }}" data-src-content="{{ src_description }}" />',
		'config' 					=> [ 
			'meta'					=> true, // add meta data ##
			'srcset'				=> true // add srcset data ##
		],
	],

	// @todo ##
	'avatar' => [
		'markup' => '<div class="col-12"><img class="avatar" src="{{ src }}"/></div>'
	],

]];
