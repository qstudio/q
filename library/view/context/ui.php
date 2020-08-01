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

	// ui footer ##
	'footer'  			=> [
		'markup' => '
				<footer class="container footer" data-scroll="footer">
					<div class="row">
						<div class="col-12 col-lg-8 mt-3 mb-3">
							PARTIAL FOOTER
						</div>
					</div>
				</footer> 
				{~ action~wp_footer ~}
			</body>
		</html>
		'
	],

	// ui open ##
	'open'  			=> [
		'markup' => '
		<main class="container {{ classes }}">
			<div class="row">
		'
	],

	// ui close() ##
	'close'  			=> [
		'markup' => '
			</div>
		</main>
		'
	],

	// comments ##
	'comment'  			=> [
		'markup' => '{{ comment }}'
	],

]];
