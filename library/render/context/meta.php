<?php

namespace q\render;

use q\core\helper as h;
use q\ui;
use q\get;
use q\render;

class meta extends \q\render {

	/**
     * Get  eta field data via meta handler
     *
     * @param       Array       $args
     * @since       1.3.0
	 * @uses		define
     * @return      Array
     */
    public static function get( $args = null ) {

		// get title - returns array with key 'title' ##
		$args['field'] = $args['task']; // get\meta::field required "args->field" ## 
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
