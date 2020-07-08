<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
use q\render;

class tag extends \q\render {
	

    /**
     * Get all tags of defined $type from passed $string 
     *  
     */
    public static function get( string $string = null, $type = 'variable' ) {
        
        // sanity ##
        if (
			is_null( $string ) 
			|| is_null( $type )
        ) {

			// log ##
			h::log( self::$args['task'].'~>e:>No string or type value passed to method' );

            return false;

		}
		
		switch ( $type ) {

			default :
			case "variable" :

				// note, we trim() white space off tags, as this is handled by the regex ##
				$open = trim( tags::g( 'var_o' ) );
				$close = trim( tags::g( 'var_c' ) );

				// h::log( 'open: '.$open );

				$regex_find = \apply_filters( 
					'q/render/parse/variable/get', 
					// '~\{{\s(.*?)\s\}}~' 
					"~\\$open\s+(.*?)\s+\\$close~" // note:: added "+" for multiple whitespaces.. not sure it's good yet...
				);

			break ;

		}

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
     * Check if single tag exists 
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
	/*
    public static function edit( string $from = null, $to = null, $from_type = 'function', $to_type = 'variable' ) {

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
				$needle_start = tags::g( 'var_o' ); #'{{ ';
				$needle_end = tags::g( 'var_c' ); #' }}';

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
	*/
	
	

	/**
     * Set {{ variable }} in self:markup['template'] at defined position
     * 
     */
    public static function set( string $variable = null, $position = null ) { // , $markup = null

		h::log( 't:>Position based replacement seems shaky, lets move to str_replace' );

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
				$needle_start = tags::g( 'var_o' ); #'{{ ';
				$needle_end = tags::g( 'var_c' ); #' }}';

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
    public static function swap( string $from = null, string $to = null, $from_type = 'function', $to_type = 'variable' ) { // , $markup = null

        // sanity ##
        if (
			is_null( $to ) 
			|| is_null( $to_type ) 
			|| is_null( $from )
			|| is_null( $from_type ) 
		) {

			// log ##
			h::log( self::$args['task'].'~>e:Error in data passed to method' );

            return false;

		}
		
		// validate to type ##
		switch ( $to_type ) {

			default :
			case "variable" :

				// check if variable is correctly formatted --> {{ STRING }} ##
				$needle_start = tags::g( 'var_o' ); #'{{ ';
				$needle_end = tags::g( 'var_c' ); #' }}';

			break ;

			case "partial" :

				// check if variable is correctly formatted --> {{> STRING }} ##
				$needle_start = tags::g( 'par_o' ); #'{{ ';
				$needle_end = tags::g( 'par_c' ); #' }}';

			break ;

			case "section" :

				// check if variable is correctly formatted --> {{> STRING }} ##
				$needle_start = tags::g( 'sec_o' ); #'{{ ';
				$needle_end = tags::g( 'sec_c' ); #' }}';

			break ;

			case "function" :

				// check if variable is correctly formatted --> {{> STRING }} ##
				$needle_start = tags::g( 'fun_o' ); #'{{ ';
				$needle_end = tags::g( 'fun_c' ); #' }}';

			break ;

		}

        if (
            ! render\method::starts_with( $to, $needle_start ) 
			|| ! render\method::ends_with( $to, $needle_end ) 
        ) {

			// log ##
			h::log( self::$args['task'].'~>e:>tag: "'.$to.'" is not correctly formatted - missing "'.$needle_start.'" at start or "'.$needle_end.'" at end.' );

            return false;

		}

		// validate from type ##
		switch ( $from_type ) {

			default :
			case "variable" :

				// check if variable is correctly formatted --> {{ STRING }} ##
				$needle_start = tags::g( 'var_o' ); #'{{ ';
				$needle_end = tags::g( 'var_c' ); #' }}';

			break ;

			case "partial" :

				// check if variable is correctly formatted --> {{> STRING }} ##
				$needle_start = tags::g( 'par_o' ); #'{{ ';
				$needle_end = tags::g( 'par_c' ); #' }}';

			break ;

			case "section" :

				// check if variable is correctly formatted --> {{> STRING }} ##
				$needle_start = tags::g( 'sec_o' ); #'{{ ';
				$needle_end = tags::g( 'sec_c' ); #' }}';

			break ;

			case "function" :

				// check if variable is correctly formatted --> {{> STRING }} ##
				$needle_start = tags::g( 'fun_o' ); #'{{ ';
				$needle_end = tags::g( 'fun_c' ); #' }}';

			break ;

		}

        if (
            ! render\method::starts_with( $from, $needle_start ) 
			|| ! render\method::ends_with( $from, $needle_end ) 
        ) {

			// log ##
			h::log( self::$args['task'].'~>e:>tag: "'.$from.'" is not correctly formatted - missing "'.$needle_start.'" at start or "'.$needle_end.'" at end.' );

            return false;

		}
		
		// h::log( 'd:>swapping from: "'.$from.'" to: "'.$to.'"' );

		// use strpos to get location of {{ variable }} ##
		// $position = strpos( self::$markup, $to );
		// h::log( 'Position: '.$position );

		// add new variable to $template as defined position - don't replace $from yet... ##
		$new_template = str_replace( $from, $to, self::$markup['template'] );

		// test ##
		// h::log( 'd:>'.$new_template );

		// push back into main stored markup ##
		self::$markup['template'] = $new_template;
		
		// h::log( 'd:>'.$markup );

		// log ##
		// h::log( self::$args['task'].'~>variable_added:>"'.$to.'" @position: "'.$position.'" by "'.core\method::backtrace([ 'level' => 2, 'return' => 'function' ]).'"' );

        // positive ##
        return true; #$markup['template'];

    }



    /**
     * Remove {{ variable }} from self:$args['markup'] array
     * 
     */
    public static function remove( string $variable = null, $markup = null, $type = 'variable' ) {

        // sanity ##
        if (
			is_null( $variable ) 
			|| is_null( $markup )
			|| is_null( $type )
		) {

			// log ##
			h::log( self::$args['task'].'~>e:>No variable or markkup value passed to method' );

            return false;

		}
		
		// h::log( 'remove: '.$variable );

        // check if variable is correctly formatted --> {{ STRING }} ##

		// what type of variable are we adding ##
		switch ( $type ) {

			default :
			case "variable" :

				// check if variable is correctly formatted --> {{ STRING }} ##
				$needle_start = tags::g( 'var_o' ); #'{{ ';
				$needle_end = tags::g( 'var_c' ); #' }}';

			break ;

		}

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



}