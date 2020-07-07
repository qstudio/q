<?php

// return an array ##
return [ 'ui' => [

'_global'			=> [
	'config' => [
		'run' 	=> true,
		'debug'	=> false
	],
],

// ui header ##
'header'  			=> [
	'markup' 		=> [
		'template' => '
<header></header>'
	]
],

// ui footer ##
'footer'  			=> [
	'markup' 		=> [
		'template' => '
<footer></footer>'
	]
],

// ui open ##
'open' => [
	'config' => [
		'run' 	=> true,
		'debug'	=> false
	],
	'markup' => [
		'template' => '
<main class="container {{ classes }}">
	<div class="row">',

		'wrap' => '
<div>{{ content }}</div>'
		]
],

// ui close() ##
'close'  			=> [
	'markup' 		=> [
		'template' => '
	</div>
</main>'
	]
],

] ];
