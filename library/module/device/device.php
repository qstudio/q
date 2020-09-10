<?php

/*
@todo --
Allow device to be set via querysting - ?q_device=desktop OR =tablet OR =client:destkop:browser:opera:version:3_4_1
Declare q_device object 
    --> get()
        returns --> os, device, client, version --- can be called $q_device::handle{} == 'handheld';
    --> is('mobile')
    --> is('tablet')
    --> is('desktop')
    --> is('handheld') == mobile + tablet
*/

namespace q\module;

// Q ##
use q\core;
use q\core\helper as h;

// load it up ##
\q\module\device::run();

class device extends \Q {
                
	static $get = false; // start false ##

	/**
	 * Runner..
	 * 
	 * @since       0.2
	 * @return      void
	 */
	public static function run() 
	{

		// load libraries ##
		core\load::libraries( self::load() );

	}


	/**
	* Load Libraries
	*
	* @since        2.0
	*/
	private static function load()
	{

		$array = [

			// core ##
			'option' => h::get( 'module/device/core/option.php', 'return', 'path' )

		];

		// h::log( core\option::get('module') );
		if ( 
			! isset( core\option::get('module')->device )
			|| true !== core\option::get('module')->device 
		){

			// h::log( 'd:>Device is not enabled.' );

			return $array;

		}

		$array['method'] = h::get( 'module/device/core/method.php', 'return', 'path' );
		$array['is'] = h::get( 'module/device/core/is.php', 'return', 'path' );
		$array['render'] = h::get( 'module/device/view/render.php', 'return', 'path' );

		// Mobile Detect library ##
		if ( ! class_exists( 'Mobile_Detect' ) ) { 
						
			$array['mobile_detect'] = h::get( 'module/device/vendor/Mobile_Detect.php', 'return', 'path' );

		}

		return $array;

	}


}
