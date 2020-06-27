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

namespace q\extension;

// Q ##
use q\core;
use q\core\helper as h;

// load it up ##
\q\extension\device::run();

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
			'option' => h::get( 'extension/device/core/option.php', 'return', 'path' )

		];

		// h::log( core\option::get('extension') );
		if ( 
			! isset( core\option::get('extension')->device )
			|| true !== core\option::get('extension')->device 
		){

			// h::log( 'd:>Device is not enabled.' );

			return $array;

		}

		$array['method'] = h::get( 'extension/device/core/method.php', 'return', 'path' );
		$array['is'] = h::get( 'extension/device/core/is.php', 'return', 'path' );
		$array['render'] = h::get( 'extension/device/view/render.php', 'return', 'path' );

		// Mobile Detect library ##
		if ( ! class_exists( 'Mobile_Detect' ) ) { 
						
			$array['mobile_detect'] = h::get( 'extension/device/vendor/Mobile_Detect.php', 'return', 'path' );

		}

		return $array;

	}


}
