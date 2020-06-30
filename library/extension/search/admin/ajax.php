<?php

namespace q\extension\search;

use q\extension;

// load it up ##
\q\extension\search\ajax::run();

class ajax extends extension\search {

    public static function run()
    {

        // ajax search calls ##
        \add_action( 'wp_ajax_q_search', array( 'q\\extension\\search\\method', 'query' ) );
        \add_action( 'wp_ajax_nopriv_q_search', array( 'q\\extension\\search\\method', 'query' ) );

    }

}
