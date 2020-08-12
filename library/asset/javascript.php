<?php

namespace q\asset;

use q\core;
use q\core\helper as h;
use q\asset;
use q\strings;

// load it up ##
\q\asset\javascript::__run();

class javascript extends \Q {
    
    static $args = array();
    static $array = array();
    static $force = false; // force refresh of JS file ##

    public static function __run()
    {

        #h::log( 'scripts file loaded...' );

        // add JS to footer if debugging or script if not ##
        \add_action( 'wp_footer', [ get_class(), 'wp_footer' ], 10000000000 );

    }



    public static function args( $args = false )
    {

        #h::log( 'passed args to class' );
        #h::log( $args );

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


    public static function comment( $handle = null )
    {

        // sanity ##
        if ( is_null( $handle ) ) {

            return false;

        }

$return = 
"
/**
{$handle}
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

            h::log( 'e:>Missing args..' );

            return false;

		}
		
		$handle = $args['view'].'\\'.$args['method'];
		$handle = str_replace( '\\', '/', $handle );
		// h::log( '$handle: '.$handle );

		// allow view/method filtering ##
		$args = \apply_filters( 'q/filter/'.$handle, $args );

		// double sanity ##
        if ( 
            is_null( $args )
            || ! isset( $args["view"] )
            || ! isset( $args["method"] )
        ){

            h::log( 'e:>Missing args after filter..' );

            return false;

		}

        if ( 
            ! method_exists( $args['view'], $args['method'] )
            || ! is_callable( array( $args['view'], $args['method'] ) )
        ){

            h::log( 'e:>handler wrong - class: '.$args['view'].' / method: '.$args['method'] );

            return false;

        }

		// h::log( 'e:>class: '.$args['view'].' / method: '.$args['method'] );

		#h::log( $data );

        // h::log( self::$args );
        ob_start();

        // call class method and pass arguments ##
        $data = call_user_func_array (
                array( $args['view'], $args['method'] )
            ,   array( $args )
        );

        // grab ##
        $data = ob_get_clean(); 

        if ( ! $data ) {
            
            h::log( 'e:>'.$args["view"].'::'.$args['method'].' returned bad data to JS collector.' );

            return false;

        }

        // add script ##
        self::add( $data, $handle ) ;

        // ok ##
        return true;

    }




    /**
    * build array for rendering
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function add( $string = null, $handle = null )
    {

        // sanity ##
        if ( 
			is_null( $string )
			|| is_null( $handle ) 
		) {

            h::log( 'e:>nothing passed to renderer...' );

            return false;

		}

		// h::log( 'javascript handle called for: '.$hash .' --- length: '. strlen( $string ) );
		
		// create hash ##
		// $hash = 'js_'.\sanitize_key( $handle ).'_'.rand();
		// $hash

		// h::log( 'javascript render called for: '.$hash .' --- length: '. strlen( $string ) );

        // we need to strip the <script> tags ##
        $string = self::strip_script( $string );

        // add the passed value to the array ##
        self::$array[$handle] = 
            isset( $array[$handle] ) ?
            $array[$handle].self::comment( $handle ).$string :
            self::comment( $handle ).$string ;

    }




    public static function header( $length = 0 )
    {

        // version ##
        $version = self::version;

        // date ##
        $date = date( 'd/m/Y h:i:s a' );

// return it ##
return "
/**
Plugin:     Q Module
Version:    {$version}
Length:		{$length}
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

        #h::log( 'javascript footer called...' );
        #h::log( self::$array );

        // sanity ##
        if ( 
            is_null( self::$array ) 
            || ! array_filter( self::$array )
        ) {

            h::log( 'd:>array is empty.' );

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
                $string = self::header( strlen( $string ) ).$string;

                // wrap in tags ##
                $string = self::add_script( $string );

                #h::log( $string );

                // echo back into the end of the markup ##
                echo $string;

            break ;        

            // if we are not debugging, then we generate a file "q.theme.js" and dump the scripts in order - stripping the <script> tag wrappers ##
            case ( false ):
            default:

                //  file ##
                $file = \q_theme::get_parent_theme_path( '/library/asset/js/module/theme.min.js' );

                // h::log( 'File: '.$file );
                // h::log( 'File: '.$file );

                if ( ! file_exists( $file ) ) {

                    // h::log( 'theme/javascript/q.theme.js missing, so creating..' );

                    touch( $file ) ;

                }

                // flatten ##
                $string .= implode( "", self::$array );

                // mimnify ##
                $string = strings\method::minify( $string, 'js' );

                // add header to empty string ##
                $string = self::header( strlen( $string ) ).$string;

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

            h::log( 'Force refresh of JS file..' );

            return false;

        }

        // sanity ##
        if ( 
            is_null( $length ) 
        ) {

            #h::log( 'Error in passed parameters.' );

            // defer to negative ##
            return false;

        }

        // get the stored file length from teh database ##
        if ( false === ( $stored_length = \get_site_transient( 'q_javascript_length' ) ) ) {

            #h::log( 'Nothing found in transients.' );

            return false;

        }

        // log ##
        #h::log( 'stored length: '.$stored_length );

        // compare lengths ##
        if ( $length == $stored_length ) {

            #h::log( 'File is unchanged ( '.$length.' == '.$stored_length.' ), so not remaking' );

            return true;

        }

        #h::log( 'File length is different ( '.$length.' != '.$stored_length.' ), so remaking' );

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
