<?php

namespace q\context;

use q\core; // core functions, options files ##
use q\core\helper as h; // helper shortcut ##
use q\plugin; // plugins ## 
// use q\ui; // template, ui, markup... ##
use q\get; // wp, db, data lookups ##
use q\context; // self ##
use q\render; 

// Q Theme ##
use q\theme;

class partial extends \q\context {


	/**
     * Generic Getter - looks for properties in config matching context->task
	 * can be loaded as a string in context/ui file
     *
     * @param       Array       $args
     * @since       1.4.1
	 * @uses		render\fields::define
     * @return      Array
     */
    public static function get( $args = null ) {

		// // look for property "args->task" in config ##
		// if ( 
		// 	$config = core\config::get([ 'context' => $args['context'], 'task' => $args['task'] ])
		// ){
			// h::log( $config );
			
			// "args->fields" are used for type and callback lookups ##
			// self::$args['fields'] = $array['fields']; 

			// define "fields", passing returned data ##
			render\fields::define(
				core\config::get([ 'context' => $args['context'], 'task' => $args['task'] ])
			);

		// }

	}


}
