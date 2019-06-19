<?php

namespace q\controller;

use q\core\core as core;
use q\core\helper as helper;
use q\core\config as config;
use q\controller\generic as generic;

// load it up ##
\q\controller\javascript::run();

class javascript extends \Q {
    
    static $args = array();
    static $array = array();
    static $force = true; // force refresh of JS file ##

    public static function run()
    {

        #helper::log( 'scripts file loaded...' );

        // add JS to footer if debugging or script if not ##
        \add_action( 'wp_footer', [ get_class(), 'wp_footer' ], 10000000000 );

    }



    public static function args( $args = false )
    {

        #helper::log( 'passed args to class' );
        #helper::log( $args );

        // update passed args ##
        self::$args = \wp_parse_args( $args, self::$args );

    }



    public static function strip_script( $string = null ){

        return str_replace( array( '<script>', '</script>' ), '', $string );
        #return preg_replace( '#<script(.*?)>(.*?)</script>#is', '', $string );

    }


    public static function add_script( $string = null ){

        return '<script>'.$string.'</script>';

    }


    public static function comment( $string = null, $priority = 10 )
    {

        // sanity ##
        if ( is_null( $string ) ) {

            return false;

        }

$return = 
"
/**
$string
Priority: {$priority}
*/
";

        // kick it back ##
        return $return;

    }



    

    public static function ob_get( Array $args = null )
    {

        // sanity ##
        if ( 
            is_null( $args )
            || ! isset( $args["view"] )
            || ! isset( $args["method"] )
        ){

            helper::log( 'Missing args..' );

            return false;

        }

        if ( 
            ! method_exists( $args['view'], $args['method'] )
            || ! is_callable( array( $args['view'], $args['method'] ) )
        ){

            helper::log( 'handler wrong - class:'.$args['view'].' / method: '.$args['method'] );

            return false;

        }

        // helper::log( self::$args );
        ob_start();

        // call class method and pass arguments ##
        $data = call_user_func_array (
                array( $args['view'], $args['method'] )
            ,   array( $args )
        );

        // grab ##
        $data = ob_get_clean(); 

        if ( ! $data ) {
            
            helper::log( 'Handler method returned bad data..' );

            return false;

        }

        #helper::log( $string );

        // add script ##
        self::add( $data, $args["priority"], $args["handle"] ) ;

        // ok ##
        return true;

    }




    /**
    * build array for rendering
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function add( $string = null, $priority = 10, $comment = false )
    {

        // helper::log( 'javascript render called for: '.$comment .' --- length: '. strlen( $string ) );

        // sanity ##
        if ( is_null( $string ) ) {

            #helper::log( 'nothing passed to renderer...' );

            return false;

        }

        // we need to strip the <script> tags ##
        $string = self::strip_script( $string );

        // add the passed value to the array ##
        self::$array[$priority] = 
            isset( $array[$priority] ) ?
            $array[$priority].self::comment( $comment, $priority ).$string :
            self::comment( $comment, $priority ).$string ;

    }




    public static function header()
    {

        // version ##
        $version = self::version;

        // date ##
        $date = date( 'd/m/Y h:i:s a' );

// return it ##
return "/**
Plugin:     Q Theme
Version:    {$version}
Date:       {$date}
*/
";

    }



    /**
    * Render inline or to a script file
    *
    * @since    2.0.0
    * @return   Mixed
    */
    public static function wp_footer()
    {

        #helper::log( 'javascript footer called...' );
        #helper::log( self::$array );

        // sanity ##
        if ( 
            is_null( self::$array ) 
            || ! array_filter( self::$array )
        ) {

            helper::log( 'array is empty.' );

            return false;

        }

        // empty string ##
        $string = '';

        // render inline or to a file - depending on debug status  ##
        switch ( self::$debug ) {

            // if we are debugging ##
            case ( true ):

                // loop over all array keys and dump value to string ##
                foreach( self::$array as $key => $value ) {

                    $string .= $value;

                }

                // prefix header to string ##
                $string = self::header().$string;

                // wrap in tags ##
                $string = self::add_script( $string );

                #helper::log( $string );

                // echo back into the end of the markup ##
                echo $string;

            break ;        

            // if we are not debugging, then we generate a file "q.theme.js" and dump the scripts in order - stripping the <script> tag wrappers ##
            case ( false ):
            default:

                //  file ##
                // $file = self::get_plugin_path( 'library/theme/javascript/q.theme.js' );
                $file = \q_theme::get_plugin_path( 'library/theme/javascript/q.theme.js' );

                // helper::log( 'File: '.$file );
                // helper::log( 'File: '.$file );

                if ( ! file_exists( $file ) ) {

                    // helper::log( 'theme/javascript/q.theme.js missing, so creating..' );

                    touch( $file ) ;

                }

                // flatten ##
                $string .= implode( "", self::$array );

                // mimnify ##
                $string = generic::minify( $string, 'js' );

                // add header to empty string ##
                $string = self::header().$string;

                // get the length of the total new string with header ##
                $length = strlen( $string );

                // check the stored length of the file to see if it has changed ##
                if ( self::is_unchanged( $length ) ) {

                    return false;

                }

                // truncate file ##
                self::truncate( $file );

                // put contents to file ##
                $file_put_contents = file_put_contents( 
                    $file, 
                    $string.PHP_EOL , 
                    FILE_APPEND | LOCK_EX 
                );

                // update transient of length ##
                \set_site_transient( 'q_javascript_length', $length, 1 * WEEK_IN_SECONDS );

            break ;

        }

    }



    public static function is_unchanged( $length = 0 ) 
    {

        // force refresh ##
        if ( self::$force ) {

            \delete_site_transient( 'q_javascript_length' );

            helper::log( 'Force refresh of JS file..' );

            return false;

        }

        // sanity ##
        if ( 
            is_null( $length ) 
        ) {

            #helper::log( 'Error in passed parameters.' );

            // defer to negative ##
            return false;

        }

        // get the stored file length from teh database ##
        if ( false === ( $stored_length = \get_site_transient( 'q_javascript_length' ) ) ) {

            #helper::log( 'Nothing found in transients.' );

            return false;

        }

        // log ##
        #helper::log( 'stored length: '.$stored_length );

        // compare lengths ##
        if ( $length == $stored_length ) {

            #helper::log( 'File is unchanged ( '.$length.' == '.$stored_length.' ), so not remaking' );

            return true;

        }

        #helper::log( 'File length is different ( '.$length.' != '.$stored_length.' ), so remaking' );

        return false;

    }



    public static function truncate( $file = null )
    {

        $f = @fopen( $file, "r+");
        if ( $f !== false ) {
            ftruncate( $f, 0 );
            fclose( $f );
        }


    }


}