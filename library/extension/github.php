<?php

namespace q\extension;

use q\core;
use q\core\helper as h;

// load it up ##
\q\extension\github::run();

class github extends \Q {

    public static function run()
    {

        // no background processing for github updatre ##
        \add_filter( 'github_updater_disable_wpcron', '__return_true' );

    }

}
