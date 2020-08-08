<?php
 
//  use q\core as core;
use q\core\helper as h;

// quick check :) ##
defined( 'ABSPATH' ) OR exit;

// return an array ##
return [ 'widget' => [ 'sharelines' => [

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
				<li class="q-sharelines row mt-3">
					<ul class="col-12 p-0">{@ {: data :} 
						<li class="item" data-shareline="{{ text }}">
							{{ icons }}
							<span class="text"><span class="fade"></span>{{ short_text }}</span>
							<div class="q-clear"></div>
						</li>
					@}</ul>
				</li>
				<div id="fb-root"></div><!-- REQUIRED FOR FB SHARE -->
			',

		],
		
	]	

]];
