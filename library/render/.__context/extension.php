<?php

namespace q\render;

use q\core\helper as h;
use q\ui;
use q\get;
use q\render;
use q\extension as extensions;

class extension extends \q\render {

	
	/**
    * Render search extension
    *
    * @since       4.1.0
    */
    public static function search( $args = null ){

        // ##
		return extensions\search\render::module( $args );

	}


}
