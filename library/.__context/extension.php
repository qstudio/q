<?php

namespace q\context;

use q\core\helper as h;
use q\ui;
use q\get;
use q\context;
use q\render; 
use q\extension as extensions;

class extension extends \q\context {

	
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
