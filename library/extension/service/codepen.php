<?php

namespace q\extension;

use q\core;
use q\core\helper as h;

// load it up ##
\q\extension\codepen::__run();

class codepen extends \Q {

    public static function __run(){

        \add_action("init", function(){

			\wp_oembed_add_provider( 'http://codepen.io/*/pen/*', 'http://codepen.io/api/oembed' );
		
		});

    }

}
