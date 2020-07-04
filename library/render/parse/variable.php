<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
use q\render;

class variable extends \q\render {
	


    /**
     * Get all variables from passed string value 
     *  
     */
    public static function get( string $string = null ) {
        
        // sanity ##
        if (
			is_null( $string ) 
			// || is_null( $type )
        ) {

			// log ##
			h::log( self::$args['task'].'~>e:>No string or type value passed to method' );

            return false;

		}
		
		// switch ( $type ) {

		// 	default :
		// 	case "variable" :

				// note, we trim() white space off tags, as this is handled by the regex ##
				$open = trim( tag::g( 'var_o' ) );
				$close = trim( tag::g( 'var_c' ) );

				// h::log( 'open: '.$open );

				$regex_find = \apply_filters( 
					'q/render/parse/variable/get', 
					// '~\{{\s(.*?)\s\}}~' 
					"~\\$open\s+(.*?)\s+\\$close~" // note:: added "+" for multiple whitespaces.. not sure it's good yet...
				);

		// 	break ;

		// }

		// $regex_find = \apply_filters( 'q/render/markup/variables/get', '~\{{\s(.*?)\s\}}~' );
		// if ( ! preg_match_all('~\%(\w+)\%~', $string, $matches ) ) {
        if ( ! preg_match_all( $regex_find, $string, $matches ) ) {

			// log ##
			h::log( self::$args['task'].'~>n:>No extra variables found in string to clean up - good!' );

            return false;

        }

        // test ##
        // h::log( $matches[0] );

        // kick back variable array ##
        return $matches[0];

    }



    /**
     * Check if single variable exists 
     * @todo - work on passed params 
     *  
     */
    public static function exists( string $variable = null, $field = null ) {
		
		// if $markup template passed, check there, else check self::$markup ##
		$markup = is_null( $field ) ? self::$markup['template'] : self::$markup[$field] ;

        if ( ! substr_count( $markup, $variable ) ) {

            return false;

        }

        // good ##
        return true;

	}



	/**
     * Edit {{ variable }} in self:$args['markup']
     * 
     */
    public static function edit( string $variable = null, $new_variable = null ) {

        // sanity ##
        if (
			is_null( $variable ) 
			|| is_null( $new_variable )
			// || is_null( $type )
		) {

			// log ##
			h::log( self::$args['task'].'~>e:>No variable or new_variable value passed to method' );

            return false;

		}

		// check if variable is correctly formatted --> {{ STRING }} ##
		// $needle_start = '{{ ';
		// $needle_end = ' }}';

		// // what type of variable are we adding ##
		// switch ( $type ) {

		// 	default :
		// 	case "variable" :

				// check if variable is correctly formatted --> {{ STRING }} ##
				$needle_start = tag::g( 'var_o' ); #'{{ ';
				$needle_end = tag::g( 'var_c' ); #' }}';

		// 	break ;

		// }
		
		if (
			! render\method::starts_with( $variable, $needle_start ) 
			|| ! render\method::ends_with( $variable, $needle_end ) 
			|| ! render\method::starts_with( $new_variable, $needle_start ) 
			|| ! render\method::ends_with( $new_variable, $needle_end ) 
        ) {

			// log ##
			h::log( self::$args['task'].'~>e:>Placeholder is not correctly formatted - missing {{ at start or }} at end.' );
			// h::log( 'd:>Placeholder is not correctly formatted - missing {{ at start or end }}.' );

            return false;

		}
		
		// ok - we should be good to search and replace old for new ##
		$string = str_replace( $variable, $new_variable, self::$markup['template'] );

		// test new string ##
		// h::log( 'd:>'.$string );

		// overwrite markup property ##
		self::$markup['template'] = $string;

		// kick back ##
		return true;

	}
	
	

	/**
     * Set {{ variable }} in self:markup['template'] at defined position
     * 
     */
    public static function set( string $variable = null, $position = null ) { // , $markup = null

        // sanity ##
        if (
			// is_null( $type ) 
			// || 
			is_null( $variable ) 
			// || is_null( $markup )
			|| is_null( $position )
		) {

			// log ##
			h::log( self::$args['task'].'~>e:Error in data passed to method' );

            return false;

		}
		
		// // what type of variable are we adding ##
		// switch ( $type ) {

		// 	default :
		// 	case "variable" :

				// check if variable is correctly formatted --> {{ STRING }} ##
				$needle_start = tag::g( 'var_o' ); #'{{ ';
				$needle_end = tag::g( 'var_c' ); #' }}';

		// 	break ;

		// }

        if (
            ! render\method::starts_with( $variable, $needle_start ) 
			|| ! render\method::ends_with( $variable, $needle_end ) 
        ) {

			// log ##
			h::log( self::$args['task'].'~>e:>Variable: "'.$variable.'" is not correctly formatted - missing {{ at start or }} at end.' );

            return false;

		}
		
		// h::log( 'd:>Adding variable: "'.$variable.'"' );

		// use strpos to get location of {{ variable }} ##
		// $position = strpos( self::$markup, $variable );
		// h::log( 'Position: '.$position );

		// add new variable to $template as defined position - don't replace {{ variable }} yet... ##
		$new_template = substr_replace( self::$markup['template'], $variable, $position, 0 );

		// test ##
		// h::log( 'd:>'.$new_template );

		// push back into main stored markup ##
		self::$markup['template'] = $new_template;
		
		// h::log( 'd:>'.$markup );

		// log ##
		// h::log( self::$args['task'].'~>variable_added:>"'.$variable.'" @position: "'.$position.'" by "'.core\method::backtrace([ 'level' => 2, 'return' => 'function' ]).'"' );

        // positive ##
        return true; #$markup['template'];

    }



	
	/**
     * Set {{ variable }} in self:markup['template'] at defined position
     * 
     */
    public static function swap( string $from = null, string $variable = null ) { // , $markup = null

        // sanity ##
        if (
			// is_null( $type ) 
			// || 
			is_null( $variable ) 
			// || is_null( $markup )
			|| is_null( $from )
		) {

			// log ##
			h::log( self::$args['task'].'~>e:Error in data passed to method' );

            return false;

		}
		
		// // what type of variable are we adding ##
		// switch ( $type ) {

		// 	default :
		// 	case "variable" :

				// check if variable is correctly formatted --> {{ STRING }} ##
				$needle_start = tag::g( 'var_o' ); #'{{ ';
				$needle_end = tag::g( 'var_c' ); #' }}';

		// 	break ;

		// }

        if (
            ! render\method::starts_with( $variable, $needle_start ) 
			|| ! render\method::ends_with( $variable, $needle_end ) 
        ) {

			// log ##
			h::log( self::$args['task'].'~>e:>Variable: "'.$variable.'" is not correctly formatted - missing {{ at start or }} at end.' );

            return false;

		}
		
		// h::log( 'd:>swapping from: "'.$from.'" to variable: "'.$variable.'"' );

		// use strpos to get location of {{ variable }} ##
		// $position = strpos( self::$markup, $variable );
		// h::log( 'Position: '.$position );

		// add new variable to $template as defined position - don't replace {{ variable }} yet... ##
		$new_template = str_replace( $from, $variable, self::$markup['template'] );

		// test ##
		// h::log( 'd:>'.$new_template );

		// push back into main stored markup ##
		self::$markup['template'] = $new_template;
		
		// h::log( 'd:>'.$markup );

		// log ##
		// h::log( self::$args['task'].'~>variable_added:>"'.$variable.'" @position: "'.$position.'" by "'.core\method::backtrace([ 'level' => 2, 'return' => 'function' ]).'"' );

        // positive ##
        return true; #$markup['template'];

    }



    /**
     * Remove {{ variable }} from self:$args['markup'] array
     * 
     */
    public static function remove( string $variable = null, $markup = null ) {

        // sanity ##
        if (
			is_null( $variable ) 
			|| is_null( $markup )
		) {

			// log ##
			h::log( self::$args['task'].'~>e:>No variable or markkup value passed to method' );

            return false;

		}
		
		// h::log( 'remove: '.$variable );

        // check if variable is correctly formatted --> {{ STRING }} ##

		// what type of variable are we adding ##
		// switch ( $type ) {

		// 	default :
		// 	case "variable" :

				// check if variable is correctly formatted --> {{ STRING }} ##
				$needle_start = tag::g( 'var_o' ); #'{{ ';
				$needle_end = tag::g( 'var_c' ); #' }}';

			// break ;

		// }

        if (
            ! render\method::starts_with( $variable, $needle_start ) 
            || ! render\method::ends_with( $variable, $needle_end ) 
        ) {

			// log ##
			h::log( self::$args['task'].'~>e:>Placeholder: "'.$variable.'" is not correctly formatted - missing "{{ " at start or " }}" at end.' );

            return false;

		}
		
		// h::log( 'Removing variable: "'.$variable.'"' );
		// return $markup;

        // remove variable from markup ##
		$markup = 
			str_replace( 
            	$variable, 
            	'', // nada ##
            	$markup
			);
		
		// h::log( 'd:>'.$markup );

		// log ##
		h::log( self::$args['task'].'~>variable_removed:>"'.$variable.'" by "'.core\method::backtrace([ 'level' => 2, 'return' => 'function' ]).'"' );

        // positive ##
        return $markup;

    }



	public static function cleanup(){

		$open = trim( tag::g( 'var_o' ) );
		$close = trim( tag::g( 'var_c' ) );

		// h::log( self::$markup['template'] );

		// strip all function blocks, we don't need them now ##
		// // $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
		$regex = \apply_filters( 
		 	'q/render/parse/variable/cleanup/regex', 
			 // "/$open.*?$close/ms" 
			//  "/$open\s+.*?\s+$close/s"
			"~\\$open\s+(.*?)\s+\\$close~"
		);

		// use callback to allow for feedback ##
		self::$markup['template'] = preg_replace_callback(
			$regex, 
			function($matches) {
				
				// h::log( $matches );
				if ( 
					! $matches 
					|| ! is_array( $matches )
					|| ! isset( $matches[1] )
				){

					return false;

				}

				// h::log( $matches );

				// get count ##
				$count = strlen($matches[1]);

				if ( $count > 0 ) {

					// h::log( $count .' variable tags removed...' );

				}

				// return nothing for cleanup ##
				return "";

			}, 
			self::$markup['template'] 
		);

		// h::log( self::$markup['template'] );
		
		// self::$markup['template'] = preg_replace( $regex, "", self::$markup['template'] ); 

	}



}
