<?php

namespace q\core;

// use q\core\core as core;
use q\core\helper as helper;
use q\theme\template as template;
use q\theme\ui as ui;
use q\controller\generic as generic;

// direct class parent ##
use q\wordpress\core as wordpress;

class deprecated extends wordpress {

    
    /**
     * Load and return a snippet from a method slug
     *
     * @since       1.0.1
     * @return      string       HTML
     */
    public static function get_snippet( $slug = null, $args = array() )
    {

        // check arguments ##
        if ( is_null( $slug ) ) { return false; }

        // sanitize input ##
        $slug = \sanitize_key( $slug );

        // check if method exists in 'q_theme' ##
        if (
            method_exists( '\q\controller\snippets\snippets', $slug )
            && is_callable( array( '\q\controller\snippets\snippets', $slug ) )
        ) {

            // check args are in array, if not caste ##
            #if ( ! is_array( $args ) ) { $args =  $args; }

            // call class emthod and pass arguments ##
            call_user_func_array (
                    array( '\q\controller\snippets\snippets', $slug )
                ,   ( array )$args
            );

        }

    }


}