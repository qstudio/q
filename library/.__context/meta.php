<?php

namespace q\context;

use q\core\helper as h;
use q\ui;
use q\get;
use q\context;
use q\render; 

class meta extends \q\context {

	/**
     * Get Meta field data via meta handler
     *
     * @param       Array       $args
     * @since       1.3.0
	 * @uses		define
     * @return      Array
     */
    public static function get( $args = null ) {

		// get\meta::field required "args->field" ## 
		// $args['field'] = $args['task']; 

		// returns string or array OR false.. ##
		render\fields::define([
			$args['task'] => get\meta::field( $args )
		]);

	}



	/**
     * Get author data
     *
     * @param       Array       $args
     * @since       1.3.0
	 * @uses		define
     * @return      Array
     */
    public static function author( $args = null ) {

		// get title - returns array with key 'title' ##
		// $args['field'] = $args['task']; // get\meta::field required "args->field" ## -- ?? why again ??
		render\fields::define(
			get\meta::author( $args )
		);

	}
	
}