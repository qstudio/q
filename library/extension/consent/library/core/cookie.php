<?php

// namespace ##
namespace q\extension\consent;

// Q ##
use q\core;
use q\core\helper as h;
use q\extension;

// load it up ##
\q\extension\consent\cookie::run();

class cookie extends extension\consent {

    /**
     * Instatiate Class
     *
     * @since       0.1.0
     * @return      void
     */
    public static function run()
    {

        // set defalt cookie, if not present ##
        \add_action( 'init', [ get_class(), 'init' ], 1, 0 );

    }


     /**
     * Set default cookie, if none found 
     * 
     * @since       0.1.0
     * @return      void
     */
    public static function init()
    {

        // check for class property ##
        if ( self::$cookie ) {

            // h::log( 'd:>No need to run this twice..' );

            return self::$cookie;

        }

        // check for cookie, bulk if found ##
		$cookie = self::get();
		
        if ( $cookie ) {

            // h::log( 'd:>Cookie found, setting class property...' );

            // assign to property ##
            self::$cookie = $cookie;

            // nothing to do ##
            return self::$cookie;

        }

        // helper::log( 'd:>Running set default cookie....' );

        // set default ##
        self::set( self::$defaults ); 

        // assign defaults to static property - returns an array ##
        self::$cookie = self::$defaults;

        // helper::log( self::$cookie );

        // return cookie values ##
        return self::$cookie;      

    }



    public static function is_active( $check = null ) {

        // sanity ##
        if ( is_null( $check ) ) {

            h::log( 'd:>No cookie value passed...' );

            return false;

        }

        // helper::log( 'Cookie check: '.$check );
        // helper::log( self::$cookie );

        // check if cookie set and correct ##
        if (
            ! self::$cookie
            || ! is_array( self::$cookie )
            || ! isset( self::$cookie[ $check ] )
        ) {

            d::log( 'd:>Error finding requested cookie value: '.$check );
            // helper::log( self::$cookie );

            return false;

        }

        // kick it back ##
        return ( 1 == self::$cookie[ $check ] ) ? true : false ;

    }


    /**
     * Get plugin cookie
     * 
     * @return      Mixed   Array if cookie set | boolean false
     * @since       0.1.0
     */
    public static function get()
    {

        if ( 
            isset( $_COOKIE[self::$slug] ) 
            && $_COOKIE[self::$slug] 
            // && is_array( $_COOKIE[self::$slug] )
        ) {

            // get ##
            $cookie = $_COOKIE[self::$slug];
            // helper::log( $cookie );

            // cookie values are serialized when stored ##
            if ( 
                is_string( $cookie )
                // || \is_serialized( $cookie )
            ) {

                // as cookie format has changed, we need to check for old format and convert to new format ##
                if ( strpos( $cookie, '#' ) ) {

                    // helper::log( $cookie );

                    // old format ##
                    // helper::log( 'Cookie data stored in old format' );

                    // string replace "_" to "__" ##
                    $cookie = str_replace( '_', '__', $cookie ) ;

                    // string replace "#" to "_" ##
                    $cookie = str_replace( '#', '_', $cookie ) ;

                    // test ##
                    // helper::log( $cookie );

                    // reasign cookie ##
                    $_COOKIE[self::$slug] = $cookie;

                }

                // helper::log( 'Cookie in string format, unpick...' );

                $explode = explode( '__', $cookie );
                // helper::log( $explode );

                // new array ##
                $array = [];

                foreach ( $explode as $row ) {

                    // split row into parts ##
                    $item = explode( '_', $row );

                    $array[$item[0]] = $item[1];

                }

                // re-assign ##
                $cookie = $array;

            }

            // it should now be an array ##
            if ( ! is_array( $cookie ) ) {

                d::log( 'd:>WTF...' );

                return false;

            }

            // helper::log( 'Cookie already set and returned' );
            // helper::log( $cookie );

            return $cookie;

        }

        // helper::log( 'Cookie not set...' );

        // set default ##
        // self::set_cookie( self::$defaults );  

        // returning default cookie ##
        return false;

    }



    /**
     * Create cookie
     *
     * @since       0.1
     * @return      Boolean
     */
    public static function set( $array = null )
    {

        // sanity check ##
        if ( 
            is_null ( $array ) 
            || ! is_array($array )
        ) {

            // nothng to do ##
            d::log( 'd:>Error in passed args' );

            return false ;

        }

        // helper::log( $array );

        // we need to convert our named array into something nice to store in the cookie ##
        // consent_1_marketing_0_analytics_1 ##

        $string = '';
        foreach( $array as $key => $value ) {

            $string .= $key.'_'.$value.'__';

        }

        // trim last "_" ##
        $string = trim( $string, '__' );

        // check it out ##
        // helper::log( $string );

        $urlparts = parse_url( \home_url() );

        // $domain = isset( $urlparts['host'] ) ? '.'.$urlparts['host'] : '' ;

        // $domain = q_helper::is_localhost() ? '/' : '.'.$urlparts['host'] ;
        $domain = '/';

        // check domain ##
        // helper::log( 'Domain: '.$domain );

        // set the cookie ##
        setcookie( self::$slug, $string, time() + 62208000, $domain ); // domain as empty string ##
        
        // set the cookie value in the global scope ##
        $_COOKIE[self::$slug] = $string; 

        // what happened ##
        // helper::log( 'Set cookie::' );
        // helper::log( $array );

        // kick back feedback ##
        return true ;

    }



    /**
     * Check if the user has taken an action and given consent to non-functional cookies
     * 
     * @since       0.1.0
     * @return      Boolean
     */
    public static function consent()
    {

        // helper::log( 'Checking if consent has been given..' );
        // helper::log( self::$cookie ) ;

        // check for active consent ##
        if ( 
            ! is_array( self::$cookie )
            || ! array_key_exists( 'consent', self::$cookie )
            || false === self::$cookie['consent']
            || 0 == self::$cookie['consent'] 
            || ! self::$cookie['consent'] 
        ) {

            // helper::log( 'We cannot 100% confirm consent given, so show the bar again..' );

            // if there is any error with the data, we presume no consent has been given ##
            return false;

        }

        // helper::log('The user has actively given their consent.. no need to show the bar..');

        return true;

    }


}
