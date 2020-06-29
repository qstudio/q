<?php

namespace q\render;

use q\core\helper as h;
use q\ui;
use q\get;
use q\render;

class navigation extends \q\render {

	/**
    * Render nav menu
    *
    * @since       4.1.0
    */
    public static function menu( $args = null ){

        //  ##
		render\fields::define([
			'content' => 'menu..' #get\navigation::menu( $args )
		]);

    }


}
