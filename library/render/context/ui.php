<?php

namespace q\render;

use q\core; // core functions, options files ##
use q\core\helper as h; // helper shortcut ##
use q\plugin; // plugins ## 
// use q\ui; // template, ui, markup... ##
use q\get; // wp, db, data lookups ##
use q\render; // self ##

// Q Theme ##
use q\theme;

class ui extends \q\render {


	/**
     * get_header
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function header( $args = null )
    {

		$name = null;
		if ( isset( $args['name'] ) ) {
			$name = $args['name'];
		}
		\do_action( 'get_header', $name );

		return theme\view\ui\header::render( $args );

	}



	/**
     * get_footer
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function footer( $args = null )
    {

		$name = null;
		if ( isset( $args['name'] ) ) {
			$name = $args['name'];
		}
		\do_action( 'get_footer', $name );

		return theme\view\ui\footer::render( $args );

	}



	/**
     * Open .content HTML
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function open( $args = null )
    {

		return render\fields::define([
			'classes' => get\theme::body_class( $args ) // grab classes ##
		]);

	}

	

	/**
     * Open .content HTML
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function close( $args = null )
    {

        // set-up new array -- nothing really to do ##
		// grab classes ##
		return render\fields::define([
			'oh' => '' // hack.. nothing to pass here ##
		]);

	}


}
