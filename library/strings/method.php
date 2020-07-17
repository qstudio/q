<?php

namespace q\strings;

use q\core;
use q\core\helper as h;
// use q\ui;
// use q\plugin;
// use q\get;
// use q\view;
use q\asset;

class method extends \q\strings {


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




    public static function chop( $content, $length = 0, $preprend = '...' )
    {

        if ( $length > 0 ) { // trim required, perhaps ##

            if ( strlen( $content ) > $length ) { // long so chop ##
                return substr( $content , 0, $length ).$preprend;
            } else { // no chop ##
                return $content;
            }

        } else { // send as is ##

            return $content;

        }

    }


	/*
	public static function load_google_web_fonts( $fonts, $use_fallback = true, $debug = false )
    {

        // bounce to Google method ##
        return plugin\google::fonts( $fonts, $use_fallback = true, $debug = false );

	}
	*/



    public static function minify( $string = null, $type = 'js' )
    {

        // if debugging, do not minify ##
        if ( 
			class_exists( 'q_theme' )
			&& \q_theme::$debug 
		) {

            return $string;

        }

        switch ( $type ) {

            case "css" :

                $string = asset\minifier::css( $string );

                break ;

            case "js" :
            default :

                $string = asset\minifier::javascript( $string );

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
	



	/**
     * Markup object based on {{ placeholders }} and template
	 * This feature is not for formatting data, just applying markup to pre-formatted data
     *
     * @since    2.0.0
     * @return   Mixed
     */
    public static function markup( $markup = null, $data = null, $args = null )
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

            h::log( 'e:>missing parameters' );

            return false;

		}

		if (
			class_exists( 'q_willow' )
		){

			// variable replacement -- regex way ##
			$open = \q\willow\tags::g( 'var_o' );
			$close = \q\willow\tags::g( 'var_c' );

		} else {

			h::log( 'e:>Q Willow Library Missing, using presumed variable tags {{ xxx }}' );

			$open = '{{ ';
			$close = ' }}';

		}
		
		// capture missing placeholders ##
		// $capture = [];

        // // h::log( $data );
		// h::log( $markup );
		// h::log( $data );
		// h::log( 't:>replace {{ with tag::var_o' );

		// empty ##
		$return = '';

        // format markup with translated data ##
        foreach( $data as $key => $value ) {

			if (
				is_array( $value )
			){

				// check on the value ##
				// h::log( 'd:>key: '.$key.' is array - going deeper..' );

				$return_inner = $markup;

				foreach( $value as $k => $v ) {

					// $string_inner = $markup;

					// check on the value ##
					// h::log( 'd:>key: '.$k.' / value: '.$v );

					// only replace keys found in markup ##
					if ( false === strpos( $return_inner, $open.$k.$close ) ) {

						// h::log( 'd:>skipping '.$k );
		
						continue ;
		
					}

					// template replacement ##
					$return_inner = str_replace( $open.$k.$close, $v, $return_inner );

				}

				$return .= $return_inner;

				continue;

			}

			// get new markup row ##
			$return .= $markup;

			// check on the value ##
			// h::log( 'd:>key: '.$key.' / value: '.$value );

            // only replace keys found in markup ##
            if ( false === strpos( $return, $open.$key.$close ) ) {

                // h::log( 'd:>skipping '.$key );

                continue ;

			}

			// template replacement ##
			$return = str_replace( $open.$key.$close, $value, $return );

		}

		// h::log( $return );

		// wrap string in defined string ?? ##
		if ( isset( $args['wrap'] ) ) {

			// h::log( 'd:>wrapping string before return: '.$args['wrap'] );

			// template replacement ##
			$return = str_replace( $open.'template'.$close, $return, $args['wrap'] );

		}

        // h::log( $return );

        // return markup ##
        return $return;

	}


}
