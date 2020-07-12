<?php

namespace q\render;

use q\core\helper as h;
use q\ui;
use q\get;
use q\render;
use q\widget as widgets;

class widget extends \q\render {

	
	/**
    * Render nav menu
    *
    * @since       4.1.0
    */
    public static function search( $args = null ){

        // ##
		return ext\search\render::module( $args );

	}


}
