<?php

namespace q\plugin;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;
use q\core\wordpress as wordpress;
use q\theme\ui as ui;
use q\controller\generic as generic;

// load it up ##
\q\plugin\github::run();

class github extends \Q {

    public static function run()
    {

        // no background processing for github updatre ##
        \add_filter( 'github_updater_disable_wpcron', '__return_true' );

    }

}