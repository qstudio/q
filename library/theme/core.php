<?php

namespace q\theme;

// use q\core\core as core;
use q\core\config as config;
use q\core\helper as helper;
use q\wordpress\core as wp_core;
use q\wordpress\post as wp_post;

class core extends \Q {

	/**
	 * Prepare passed args ##
	 * 
	 */
	public static function prepare_args( Array $args = null ) {

		// get stored config - pulls from Q, but available to filter via q/config/get/all ##
		$config = 
			( // force config settings to return by passing "config" -> "property" ##
				isset( $args['config'] ) 
				&& config::get( $args['config'] ) 
			) ?
			config::get( $args['config'] ) :
			config::get( str_replace( 'get_', '', debug_backtrace()[1]['function'] ) ) ; // we replace "get_", as calls come from UI the_ methods ##

		// test ##
		// helper::log( $config );

		// Parse incoming $args into an array and merge it with $config defaults ##
		// allows specific calling methods to alter passed $args ##
		$args = \wp_parse_args( $args, $config );
				
		// helper::log( $config );
		// helper::log( $args );

		// merge any default args with any pass args ##
		if ( 
			is_null( $args )
			|| ! is_array( $args )
		) {

			helper::log( 'Error in passed $args' );

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

			helper::log( 'Error with post object, validate - returned as null.' );

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

		// helper::log( debug_backtrace()[1]['function'] );
		$method = debug_backtrace()[1]['function'];

		// merge any default args with any pass args ##
		if ( 
			is_null( $args )
			|| ! is_array( $args )
		) {

			helper::log( 'Error in passed $args' );

			return false;

		}

		// helper::log( $args );
		// helper::log( $array );
		
		// return array ##
		if ( isset( $args['return'] ) && 'return' == $args['return'] ) {

			// filter ##
			$array = \apply_filters( 'q/theme/get/array/'.$method, $array, $args );
			
			// kick back ##
			return $array ;

		}

		// no markup passed ##
		if ( ! isset( $args['markup'] ) ) {

			helper::log( 'Missing "markup", returning false.' );

			return false;

		}

		// markup ##
		$string = markup::apply( $args['markup'], $array );

		// filter ##
        $string = \apply_filters( 'q/theme/get/string/'.$method, $string, $args );

		// test ##
		// helper::log( $string );

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
        #helper::log( $array );

        // did we pass anything ##
        if ( ! $array ) {

            #helper::log( 'kicked 1' );

            return false;

        }

        $return = false;

        // loop over array of date options ##
        foreach( $array as $key => $value ) {

            #helper::log( $value );

            // nothing happening ? ##
            if ( false === $value['date'] ) {

                #helper::log( 'kicked 2' );

                continue;

            }

            if ( 'end' == $key ) {

                // helper::log( 'Formatting end date: '.$value['date'] );

                // if start date and end date are the same, we need to just return the start date and start - end times ##
                if (
                    // $array['start']['date'] == $array['end']['date']
                    date( $value['format'], strtotime( $array['start']['date'] ) ) == date( $value['format'], strtotime( $array['end']['date'] ) )
                ) {

                    // helper::log( 'Start and end dates match, return time' );

                    // use end date ##
                    $date = ' '.date( 'g:i:a', strtotime( $array['start']['date'] ) ) .' - '. date( 'g:i:a', strtotime( $array['end']['date'] ) );

                } else {

                    // helper::log( 'Start and end dates do not match..' );

                    // use end date ##
                    $date = ' - '.date( $value['format'], strtotime( $value['date'] ) );

                }

            } else {

                // helper::log( 'Formatting start date' );

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