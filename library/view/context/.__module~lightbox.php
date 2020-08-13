<?php
 
//  use q\core as core;
use q\core\helper as h;

// quick check :) ##
defined( 'ABSPATH' ) OR exit;

// return an array ##
return [ 'module' => [ 'lightbox' => [

		'config'			=> [
		
			// run context->task ##
			'run' 			=> true,
			
			// context->task debugging ##
			'debug'			=> false,

			// return method ##
			'return'		=> 'return'
	
		],

		// markup ##
		'markup' 			=> [

			// main template ##
			'template'		=> '
								<a href="{{ src_large }}" data-toggle="lightbox" data-gallery="example-gallery" class="col-sm-4">
									<img src="{{ src_small }}" class="img-fluid">
								</a>
								',

			'wrap'			=> '<div class="row justify-content-center">
									<div class="col-md-8">
										<div class="row">
											{{ template }}
										</div>
									</div>
								</div>'

		],
		
	]	

]];
