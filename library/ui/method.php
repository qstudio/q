<?php

namespace q\ui;

use q\core;
use q\core\helper as h;
use q\ui;
use q\plugin;
use q\get;
use q\render;

class method extends \Q {

	/**
	 * Prepare passed args ##
	 * 
	 */
	public static function prepare_args( $args = [] ) {

		// sanity ##
		if (
			// is_null( $args )
			// || 
			! is_array( $args )
		){

			h::log( 'Error in passed args' );

			return false;

		}

		// get calling method for filters ##
		$method = core\method::backtrace([ 'level' => 2, 'return' => 'function' ]);

		// get stored config - pulls from Q, but available to filter via q/config/get/all ##
		$config = 
			( // force config settings to return by passing "config" -> "property" ##
				isset( $args['config']['load'] ) 
				&& core\config::get( $args['config']['load'] ) 
			) ?
			core\config::get( $args['config']['load'] ) :
			core\config::get( $method ) ;

		// test ##
		// h::log( $config );

		// Parse incoming $args into an array and merge it with $config defaults ##
		// allows specific calling methods to alter passed $args ##
		if ( $config ) $args = \wp_parse_args( $args, $config );

		// let's set "group" to calling function, for debugging ##
		if ( ! isset( $args['group'] ) ) { 
			$args['group'] = $method; 
		}
				
		// h::log( $config );
		// h::log( $args );

		// merge any default args with any pass args ##
		if ( 
			is_null( $args )
			|| ! is_array( $args )
		) {

			h::log( 'Error in passed $args' );

			return false;

		}
		
		// no post set ##
		if ( ! isset( $args['config']['post'] ) ) {

			$args['config']['post'] = get\post::object();

		}

		// validate passed post ##
		if ( 
			isset( $args['config']['post'] ) 
			&& ! $args['config']['post'] instanceof \WP_Post
		) {

			// get new post, if corrupt ##
			$args['config']['post'] = get\post::object( $args );

		}

		// last check ##
		if ( ! $args['config']['post'] ) {

			h::log( 'Error with post object, validate - returned as null.' );

			$args['config']['post'] = null;

			// return false;

		}

		// kick back args ##
		return $args;

	}
	


	
	/**
	 * Prepare $array of data to be returned to render
	 * 
	 */
	public static function prepare_return( $args = null, $array = null ) {

		// get calling method for filters ##
		$method = core\method::backtrace([ 'level' => 2, 'return' => 'function' ]);

		// sanity ##
		if ( 
			is_null( $args )
			|| ! is_array( $args )
			|| is_null( $array )
			|| ! is_array( $array )
		) {

			h::log( 'Error in passed $args or $array' );

			return false;

		}

		// h::log( $args );
		// h::log( $array );

		// run global filter on $array by $method ##
		$array = \apply_filters( 'q/get/'.$method.'/array', $array, $args );

		// run template specific filter on $array by $method ##
		if ( $template = ui\template::get() ) {

			// h::log( 'Filter: "q/ui/get/array/'.$method.'/'.$template.'"' );

			$array = \apply_filters( 'q/get/'.$method.'/array/'.$template, $array, $args );

		}

		// another sanity check after filters... ##
		if ( 
			is_null( $array )
			|| ! is_array( $array )
		) {

			h::log( 'Error in passed $args or $array' );

			return false;

		}
		
		// return all arrays ##
		return $array ;

	}




	
	/**
	 * Prepare $array to be rendered
	 * 
	 */
	public static function prepare_render( $args = null, $array = null ) {

		// get calling method for filters ##
		$method = core\method::backtrace([ 'level' => 2, 'return' => 'function' ]);

		// sanity ##
		if ( 
			is_null( $args )
			|| ! is_array( $args )
			|| is_null( $array )
			|| ! is_array( $array )
			// || empty( $array )
		) {

			// h::log( 'Error in passed $args or $array: '.$method );

			// log ##
			render\log::add([
				'key' => 'error', 
				'field'	=> $method,
				'value' => 'Error in passed $args or $array'
			]);

			return false;

		}

		// empty results ##
		if ( 
			empty( $array )
		) {

			// h::log( 'Returned $array is empty: '.$method );

			// log ##
			render\log::add([
				'key' => 'notice', 
				'field'	=> $method,
				'value' => 'Returned $array is empty'
			]);

			return false;

		}

		// h::log( '$method: '.$method );
		// h::log( $args );
		// h::log( $array );

		// no markup passed ##
		if ( ! isset( $args['markup'] ) ) {

			h::log( 'Missing "markup", returning false.' );

			return false;

		}

		// last filter on array, before applying markup ##
		$array = \apply_filters( 'q/ui/render/prepare/'.$method.'/array', $array, $args );

		// do markup ##
		$string = self::markup( $args['markup'], $array );

		// filter $string by $method ##
		$string = \apply_filters( 'q/ui/render/prepare/'.$method.'/string', $string, $args );
		
		// filter $array by method/template ##
		if ( $template = ui\template::get() ) {

			// h::log( 'Filter: "q/theme/get/string/'.$method.'/'.$template.'"' );
			$string = \apply_filters( 'q/ui/render/prepare/'.$method.'/string/'.$template, $string, $args );

		}

		// test ##
		// h::log( $string );

		// all render methods echo ##
		echo $string ;

		// optional logging to show removals and stats ##
        render\log::render( $args );

		return true;

	}



	public static function search_the_content( Array $args = null ) {

		// sanity @todo ##
		if ( 
			is_null( $args )
			|| ! isset( $args['string'] )
		) {

			h::log( 'Error in passed params' );

			return false;

		}

		// get string ##
		$string = $args['string'];

		// get search term ##
		$search = \get_search_query();
		// h::log( $search );

        // $string = $args['string']; #\get_the_content();
        $keys = implode( '|', explode( ' ', $search ) );
		$string = preg_replace( '/(' . $keys .')/iu', '<mark>\0</mark>', $string );
		
		// get text length limit ##
		$length = isset( $args[ 'length' ] ) ? $args['length'] : 200 ;

		// get first occurance of search string ##
		$position = strpos($string, $search );

		// h::log( 'string pos: '.$position );

		if ( ( $length / 2 ) > $position ) {

			// h::log( 'first search term is less than 100 chars in, so return first 200 chars..' );

			$string = ( strlen( $string ) > 200 ) ? substr( $string,0,200 ).'...' : $string;

		} else {

			// move start point ##
			$string = '...'.substr( $string, $position - ( $length / 2 ), -1 );
			$string = ( strlen( $string ) > 200 ) ? substr( $string,0,200 ).'...' : $string;

		}

		// return ##
		return $string;
		
    }


	
    /**
     * Format passed date value
     *
     * @since   2.0.0
     * @return  Mixed String
     */
    public static function date( $array = null ){

        // test ##
        #h::log( $array );

        // did we pass anything ##
        if ( ! $array ) {

            #h::log( 'kicked 1' );

            return false;

        }

        $return = false;

        // loop over array of date options ##
        foreach( $array as $key => $value ) {

            #h::log( $value );

            // nothing happening ? ##
            if ( false === $value['date'] ) {

                #h::log( 'kicked 2' );

                continue;

            }

            if ( 'end' == $key ) {

                // h::log( 'Formatting end date: '.$value['date'] );

                // if start date and end date are the same, we need to just return the start date and start - end times ##
                if (
                    // $array['start']['date'] == $array['end']['date']
                    date( $value['format'], strtotime( $array['start']['date'] ) ) == date( $value['format'], strtotime( $array['end']['date'] ) )
                ) {

                    // h::log( 'Start and end dates match, return time' );

                    // use end date ##
                    $date = ' '.date( 'g:i:a', strtotime( $array['start']['date'] ) ) .' - '. date( 'g:i:a', strtotime( $array['end']['date'] ) );

                } else {

                    // h::log( 'Start and end dates do not match..' );

                    // use end date ##
                    $date = ' - '.date( $value['format'], strtotime( $value['date'] ) );

                }

            } else {

                // h::log( 'Formatting start date' );

                $date = date( $value['format'], strtotime( $value['date'] ) );

            }

            // add item ##
            $return .= $date;
            #false === $return ?
            #$date :
            #$date ;

        }

        // kick it back ##
        return $return;

    }
	




    /**
     * Add http:// if it's not in the URL?
     *
     * @param string $url
     * @return string
     * @link    http://stackoverflow.com/questions/2762061/how-to-add-http-if-its-not-exists-in-the-url
     */
    public static function add_http( $url = null ) {

        if ( is_null ( $url ) ) { return false; }

        if ( ! preg_match("~^(?:f|ht)tps?://~i", $url ) ) {

            $url = "http://" . $url;

        }

        return $url;

	}


	
    /**
     * Strip <style> tags from post_content
     *
     * @link        http://stackoverflow.com/questions/5517255/remove-style-attribute-from-html-tags
     * @since       0.7
     * @return      string HTML formatted text
     */
    public static function remove_style( $input = null )
    {
    
        if ( is_null ( $input ) ) { return false; }
    
        return preg_replace( '/(<[^>]+) style=".*?"/i', '$1', $input );
    
    }




    public static function rip_tags($string) {

        // ----- remove HTML TAGs -----
        $string = preg_replace ('/<[^>]*>/', ' ', $string);
    
        // ----- remove control characters -----
        $string = str_replace("\r", '', $string);    // --- replace with empty space
        $string = str_replace("\n", ' ', $string);   // --- replace with space
        $string = str_replace("\t", ' ', $string);   // --- replace with space
    
        // ----- remove multiple spaces -----
        $string = trim(preg_replace('/ {2,}/', ' ', $string));
    
        return $string;
    
    }


	

    public static function chop( $content, $length = 0 ) 
    {

        if ( $length > 0 ) { // trim required, perhaps ##
        
            if ( strlen( $content ) > $length ) { // long so chop ##
                return substr( $content , 0, $length ).'...';
            } else { // no chop ##
                return $content;
            }
        
        } else { // send as is ##
        
            return $content;
        
        }

    }
	

	public static function load_google_web_fonts( $fonts, $use_fallback = true, $debug = false ) 
    {

        // bounce to Google method ##
        return plugin\google::fonts( $fonts, $use_fallback = true, $debug = false );

    }



	

	/**
     * Markup object based on %placeholders% and template
     *
     * @since    2.0.0
     * @return   Mixed
     */
    public static function markup( $markup = null, $data = null )
    {

        // sanity ##
        if (
            is_null( $markup )
            || is_null( $data )
            ||
            (
                ! is_array( $data )
                && ! is_object( $data )
            )
        ) {

            helper::log( 'missing parameters' );

            return false;

        }

        #helper::log( $data );
		#helper::log( $markup ); 

		// get teh markup ##
		$string = $markup;

        // format markup with translated data ##
        foreach( $data as $key => $value ) {

			// // handle array being an array of WP_Post objects ##
			// if ( is_array( $value ) ) {

			// 	h::log( '$value is an array, so go back for more: '.$key );

			// 	// $string .= self::apply( $markup, $value, $string ); 
			// 	$value = 'array';

			// }

			// if ( is_object( $value ) ) {

			// 	h::log( '$value is an object, so go back for more: '.$key );

			// 	// $string .= self::apply( $markup, $value, $string ); 
			// 	$value = 'object';

			// }

			// check on the value ##
			// h::log( 'key: '.$key.' / value: '.$value );

            // only replace keys found in markup ##
            if ( false === strpos( $string, '%'.$key.'%' ) ) {

                #helper::log( 'skipping '.$key );

                continue ;

			}
			
			// template replacement ##
			$string = str_replace( '%'.$key.'%', $value, $string );

		}

        // h::log( $string );

        // return markup ##
        return $string;

    }



	
    public static function minify( $string = null, $type = 'js' )
    {

        // if debugging, do not minify ##
        if ( self::$debug ) {

            return $string;

        }

        switch ( $type ) {

            case "css" :

                $string = ui\asset\minifier::css( $string );

                break ;

            case "js" :
            default :

                $string = ui\asset\minifier::javascript( $string );

                break ;

        }

        // kick back ##
        return $string;

    }



	
    /**
    * Strip unwated tags and shortcodes from the_content
    *
    * @since       1.4.4
    * @return      String
    */
    public static function clean( $string = null )
    {

        // bypass ##
        return $string;

        // sanity check ##
        if ( is_null ( $string ) ) { return false; }

        // do some laundry ##
        $string = strip_tags( $string, '<a><ul><li><strong><p><blockquote><italic>' );

        // kick back the cleaned string ##
        return $string;

    }

    
}