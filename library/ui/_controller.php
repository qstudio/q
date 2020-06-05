<?php

namespace q\ui;

use q\core;
use q\core\helper as h;

// load it up ##
\q\ui\controller::run();

class controller extends \Q {

	public static function run(){

		core\load::libraries( self::load() );

	}

	/**
	* Load Libraries
	*
	* @since        2.0.0
	*/
	public static function load()
	{

		return $array = [

			// ui methods ##
			'method' => h::get( 'ui/method.php', 'return', 'path' ),

			// template config ##
			'template' => h::get( 'ui/template.php', 'return', 'path' ),

			// widgets... really?? ##
			'widget' => h::get( 'ui/widget.php', 'return', 'path' ),

			// assets ##
			'asset' => h::get( 'ui//asset/_controller.php', 'return', 'path' ),

			// template modules ##
			'module' => h::get( 'ui/module/_controller.php', 'return', 'path' ),

			// render ##
			'render' => h::get( 'ui/render/_controller.php', 'return', 'path' ), // NEW ##

			// 'field' => h::get( 'ui/field/_controller.php', 'return', 'path' ), // @todo - deprecate ##
			
		];


	}
	
}