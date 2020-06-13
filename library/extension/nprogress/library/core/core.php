<?php

namespace q\nprogress\core;

use q\nprogress\core\helper as h;
// use q\nprogress\theme\theme as theme;

// load it up ##
#\q\nprogress\core\core::run();

class core extends \q_nprogress {

    public static function run( $args = null )
    {


    }



    public static function config()
    {

        // new array ##
        $config = [];

        // helper::log( 'Device: '.helper::get_device() );

        // values ##
        $config["results"]          = \apply_filters( 'q/nprogress/example', 'example' ); // results text ##

        // check ##
        // helper::log( $config );

        // populate static property ##
        return self::$properties = $config;

    }


    /**
    * Load plugin properties
    *
    * @since    2.0.0
    * @return   Array
    */
    public static function properties( $key = null, $return = 'string' )
    {

        // helper::log( 'called for key: '.$key );

        // properties not defined yet ##
        if ( ! self::$properties ) {

            // helper::log( 'properties empty, so loading fresh...' );
            // helper::log( self::$passed_args );

            self::config();

        }

        // helper::log( self::$properties );

        // kick back specified key or whole array ##
        return 
            ( ! is_null( $key ) && isset( self::$properties[$key] ) && array_key_exists( $key, self::$properties ) ) ? 

            // single array item ##
            ( is_array ( self::$properties[$key] ) && 'string' == $return ) ? 
            implode( ",", self::$properties[$key] ) : // flat csv ##
            self::$properties[$key] : // as array ##
            
            // whole thing ##
            self::$properties ;

    }



    /**
     * Check if this is a mobile/handheld device
     *
     * @since		0.2
     * @return		Boolean
     */
    public static function is_mobile()
    {

        if ( 
            'handheld' == h::device() 
            || 'tablet' == h::device() 
        ) {

            return true;

        }

        // negative ##
        return false;

    }


}