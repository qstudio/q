<?php

namespace q\asset;

use q\core;
use q\core\helper as h;
use q\asset;
use q\strings;

// load it up ##
\q\asset\scss::__run();

class scss extends \Q {
    
    private static $option = 'q_asset_scss';
    // static $array = array();
    // static $force = false; // force refresh of CSS file ##

    public static function __run()
    {

        // h::log( 'style file loaded...' );

        // add CSS to head if debugging or file if not ##
        // \add_action( 'wp_head', [ get_class(), 'wp_head' ], 10000000000 );

    }



	/**
    * get stored list
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function get( $args = null ){

	}



	/**
    * set stored list
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function set( $args = null ){

	}


	

	/**
    * add item to stored list
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function add( $args = null ){

	}


}
