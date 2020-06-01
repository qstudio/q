<?php

namespace q\controller;

use q\core\core as core;
use q\core\helper as helper;
use q\core\config as config;
// use q\theme\theme\view\fourzerofour as fourzerofour;
// use q\theme\theme\view\post as post;
// use q\theme\core\core as theme_core;

/*
* @read - https://stackoverflow.com/questions/15966812/user-recognition-without-cookies-or-local-storage/16120977#16120977
*/

// load it up ##
\q\controller\cookie::run();

class cookie extends \Q {
    
    static $args = array();
    static $slug = false;
    static $value = false;

    public static function run( $args = false )
    {

        // update passed args ##
        self::$args = \wp_parse_args( $args, self::defaults() );

        // check if we have a cookie ##
        #\add_action( 'plugins_loaded', [ get_class(), 'check' ], 6 );

        // add JS to footer ##
        // \add_action( 'wp_footer', [ get_class(), 'wp_footer' ], 10000000 );

    }



    /**
    * Set Cookie Content
    *
    * @since    2.0.0
    * @return   Boolean 
    */
    public static function defaults()
    {

        $args = array(
            'slug'      => 'greenheart_cookie'
        );

        // assign slug ##
        self::$slug = $args['slug'];

        #helper::log( $args );

        return $args;

    }

    

    /**
    * Check Cookie Content
    *
    * @since    2.0.0
    * @return   Mixed
    */
    public static function check()
    {

        // check if the cookie exists ##
        if( ! self::get() ) {

            helper::log( 'First Hit..' );

            self::$value = 1; // first hit ##

        // if not add it ##
        } else {

            helper::log( 'Hit Before..' );

            self::$value = self::get() + 1 ;

        }

        // update it ##
        return self::set();

    }





    /**
    * Set Cookie Content
    *
    * @since    2.0.0
    * @return   Boolean 
    */
    public static function set()
    {

        // if ( ! self::get_client_id() ) {

        //     helper::log( 'We could not find a unique identifier' );

        //     return false;

        // }

        // helper::log( 'Client ID: '.self::get_client_id() );

        if ( headers_sent() ) {
    
            helper::log ("Can't change cookie " . self::$slug . " after sending headers.");

            return false;

        }

        // set ##
        #$set = \setcookie( self::$slug, intval( self::$value ), 365 * DAY_IN_SECONDS, '/', NULL ); // COOKIEPATH, COOKIE_DOMAIN
        $set = setcookie( self::$slug, intval( self::$value ), time() - YEAR_IN_SECONDS, SITECOOKIEPATH );

        helper::log( $set );  

        helper::log( 'Set with value: '.intval( self::$value ) );

        // kick back ##
        return true;

    }


    
    /**
    * Get Cookie Content
    *
    * @since    2.0.0
    * @return   Mixed Boolean on error or string
    */
    public static function get()
    {

        // if ( ! self::get_client_id() ) {

        //     helper::log( 'We could not find a unique identifier' );

        //     return false;

        // }

        helper::log( $_COOKIE );

        if ( ! isset( $_COOKIE[self::$slug] ) ) {

            helper::log( 'We could not find a cookie.' );
            
            return false;

        }

        helper::log( 'Cookie value: '.$_COOKIE[self::$slug] );

        // kick it back ##
        return $_COOKIE[self::$slug];

    }



    /** 
    * Function to get the client IP address
    *
    * @source       https://stackoverflow.com/questions/15699101/get-the-client-ip-address-using-php
    * @return       String
    * @since        2.0.0
    **/

    public static function get_client_id() 
    {
        
        $string = false;

        if ( getenv( 'HTTP_CLIENT_IP' ) ) {

            $string = getenv('HTTP_CLIENT_IP');

        } else if(getenv('REMOTE_ADDR')) {
            
              $string = getenv('REMOTE_ADDR');
        
        } else if ( getenv('HTTP_X_FORWARDED_FOR' ) ) {
        
            $string = getenv('HTTP_X_FORWARDED_FOR');
        
        } else if(getenv('HTTP_X_FORWARDED')) {
        
            $string = getenv('HTTP_X_FORWARDED');
        
        } else if(getenv('HTTP_FORWARDED_FOR')) {
        
            $string = getenv('HTTP_FORWARDED_FOR');
        
        } else if(getenv('HTTP_FORWARDED')) {
            
            $string = getenv('HTTP_FORWARDED');
        
        } else {
          
            $string = false;

        }

        #helper::log( 'ID: '.$string );
        
        return self::format( $string );

    }




    public static function format( $string )
    {

        $string = $string || null;

        return password_hash( $string, PASSWORD_BCRYPT );

    }



    public static function delete()
    {

        if ( isset($_COOKIE[self::$slug]) ) {
         
            unset($_COOKIE[self::$slug]);
         
            setcookie( self::$slug, '', time() - 3600, '/' ); // empty value and old timestamp
        
            helper::log( 'Cookie Deleted.' );

            return true;

        }

        helper::log( 'No Cookie Found.' );
        
        return false;

    }




    /**
    * JS for modal
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function wp_footer( $args = null )
    {

    // helper::log( self::$args );

?>
<script>
var $q_cookie_name = 'greenheart_cookie';
var $q_cookie_value = 1;
var $document = false;
// var $element = false;
var className = 'hasScrolled';

// FINDING AND BINDING
jQuery( document ).ready( function(){

    // $q_cookie_name = 'greenheart_cookie';
    $q_cookie_value = Number( readCookie( $q_cookie_name ) );
    $document = jQuery( document );
    // $element = jQuery('#some-element'),
    // className = 'hasScrolled';

    if ( 
        ! $q_cookie_value 
        || 0 == $q_cookie_value
    ) {

        // console.log( 'No cookie found, so setting...' );

        createCookie( $q_cookie_name, 1, 365 );

    } else {

        if ( 3 == $q_cookie_value  ) {

            // cookie above 3, so open newsletter ##
            window.location.hash = '#/modal/newsletter'; 

            // update ( +1 ) ##
            createCookie( $q_cookie_name, 4, 365 );

        } else {

            // new value ##
            $q_cookie_value = parseInt( $q_cookie_value + 1 );

            // update ( +1 ) ##
            createCookie( $q_cookie_name, $q_cookie_value, 365 );

            // console.log( 'Cookie Found, updating: '+ $q_cookie_value );

        }

    }

    // kill it ##
    // eraseCookie( $q_cookie_name );

});
</script>
<?php

    }


}