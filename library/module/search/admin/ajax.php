<?php

namespace q\module\search;

use q\module;

// load it up ##
\q\module\search\ajax::__run();

class ajax extends module\search {

    public static function __run()
    {

        // ajax search calls ##
        \add_action( 'wp_ajax_q_search', array( 'q\\module\\search\\method', 'query' ) );
        \add_action( 'wp_ajax_nopriv_q_search', array( 'q\\module\\search\\method', 'query' ) );

    }

}
