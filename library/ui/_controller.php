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
			'method' => self::get_plugin_path( 'library/ui/method.php' ),

			// template config @todo... ?? ##
			'template' => self::get_plugin_path( 'library/ui/template.php' ),

			// widgets... really?? ##
			'widget' => self::get_plugin_path( 'library/ui/widget.php' ),

			// assets ##
			'asset' => self::get_plugin_path( 'library/ui/asset/_controller.php' ),

			// template modules ##
			'module' => self::get_plugin_path( 'library/ui/module/_controller.php' ),

		];


	}
	
}