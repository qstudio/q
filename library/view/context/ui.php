<?php

// return an array ##
return [ 'ui' => [

	'config'			=> [
		
		// run context->task ##
		'run' 			=> true,
		
		// context->task debugging ##
		'debug'			=> false,

	],

	// ui header ##
	'header'  			=> [
		'markup' => ''
	],

	// ui footer ##
	'footer'  			=> [
		'markup' => ''
	],

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
