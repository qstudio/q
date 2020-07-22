<?php

// return an array ##
return [ 'ui' => [

	'config'			=> [
		
		// run context->task ##
		'run' 			=> true,
		
		// context->task debugging ##
		'debug'			=> false,

		// 'return'		=> 'return'

	],

	// ui header ##
	/*
	'header'  			=> [
		'config' 		=> [
			'return'	=> 'echo'
		]	
	],
	*/

	// ui footer ##
	// 'footer'  			=> [
	// 	'markup' => ''
	// ],

	// ui open ##
	'open'  			=> [
		'markup' => '
		<main class="container {{ classes }}">
			<div class="row">
		'
	],

	// ui close() ##
	// 'close'  			=> [
	// 	'markup' => '
	// 		</div>
	// 	</main>'
	// ],

]];
