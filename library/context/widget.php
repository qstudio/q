<?php

namespace q\context;

use q\core\helper as h;
use q\ui;
use q\get;
use q\context;
use q\widget as widgets;
// use q\extensions;

class widget extends \q\context {

	
	/**
    * Render nav menu
    *
    * @since       4.1.0
    */
    public static function sharelines( $args = null ){

        // ##
		return widgets\sharelines::module( $args );

	}


}
