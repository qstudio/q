<?php

namespace q\search\admin;

// load it up ##
\q\search\admin\ajax::run();

class ajax extends \q_search {

    public static function run()
    {

        // ajax search calls ##
        \add_action( 'wp_ajax_q_search', array( 'q\\search\\core\\method', 'query' ) );
        \add_action( 'wp_ajax_nopriv_q_search', array( 'q\\search\\core\\method', 'query' ) );

    }

}