<?php

namespace q\ui;

// use q\core\core as core;
use q\core;
use q\core\helper as h;
use q\wordpress\core as wp_core; // @todo ##
use q\wordpress\post as wp_post; // @todo ##

class method extends \Q {

	/**
	 * Prepare passed args ##
	 * 
	 */
	public static function prepare_args( Array $args = null ) {

		// get stored config - pulls from Q, but available to filter via q/config/get/all ##
		$config = 
			( // force config settings to return by passing "config" -> "property" ##
				isset( $args['config'] ) 
				&& core\config::get( $args['config'] ) 
			) ?
			core\config::get( $args['config'] ) :
			core\config::get( str_replace( 'get_', '', debug_backtrace()[1]['function'] ) ) ; // we replace "get_", as calls come from UI the_ methods ##

		// test ##
		// h::log( $config );

		// Parse incoming $args into an array and merge it with $config defaults ##
		// allows specific calling methods to alter passed $args ##
		$args = \wp_parse_args( $args, $config );
				
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

		// get post ##
		if ( 
			isset( $args['post'] ) 
			&& $args['post'] instanceof \WP_Post
		) {

			$args['post'] = $args['post'];

		} else {

			$args['post'] = wp_post::get();

		}

		// last check ##
		if ( ! $args['post'] ) {

			h::log( 'Error with post object, validate - returned as null.' );

			$args['post'] = null;

			// return false;

		}

		// kick back args ##
		return $args;

	}
	


	
	/**
	 * Prepare return method
	 * 
	 */
	public static function prepare_return( Array $args = null, Array $array ) {

		// h::log( debug_backtrace()[1]['function'] );
		$method = debug_backtrace()[1]['function'];

		// merge any default args with any pass args ##
		if ( 
			is_null( $args )
			|| ! is_array( $args )
		) {

			h::log( 'Error in passed $args' );

			return false;

		}

		// h::log( $args );
		// h::log( $array );
		
		// return array ##
		if ( isset( $args['return'] ) && 'return' == $args['return'] ) {

			// filter ##
			$array = \apply_filters( 'q/theme/get/array/'.$method, $array, $args );
			
			// kick back ##
			return $array ;

		}

		// no markup passed ##
		if ( ! isset( $args['markup'] ) ) {

			h::log( 'Missing "markup", returning false.' );

			return false;

		}

		// markup ##
		$string = markup::apply( $args['markup'], $array );

		// filter ##
        $string = \apply_filters( 'q/theme/get/string/'.$method, $string, $args );

		// test ##
		// h::log( $string );

		// echo ##
		echo $string ;

		return true;

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

    
}