<?php

namespace q\willow;

use q\willow;
use q\willow\core;
use q\core\helper as h;

// @todo ##
use q\render;

class comments extends willow\parse {

	/**
	 * Scan for comments in markup and convert to variables and $fields and also to error log ##
	 * 
	 * @since 4.1.0
	*/
	public static function prepare( $args = null ){

		// sanity -- this requires ##
		if ( 
			! isset( render::$markup )
			|| ! is_array( render::$markup )
			|| ! isset( render::$markup['template'] )
		){

			h::log( 'e:>Error in stored $markup' );

			return false;

		}

		// get markup ##
		$string = render::$markup['template'];

		// sanity ##
		if (  
			! $string
			|| is_null( $string )
		){

			h::log( render::$args['task'].'~>e:>Error in $markup' );

			return false;

		}

		// h::log('d:>'.$string);

		// get all comments, add markup to $markup->$field ##
		// note, we trim() white space off tags, as this is handled by the regex ##
		$open = trim( willow\tags::g( 'com_o' ) );
		$close = trim( willow\tags::g( 'com_c' ) );

		// h::log( 'open: '.$open. ' - close: '.$close );

		$regex_find = \apply_filters( 
			'q/render/markup/comment/regex/find', 
			"/$open\s+(.*?)\s+$close/s"  // note:: added "+" for multiple whitespaces.. not sure it's good yet...
			// "/{{#(.*?)\/#}}/s" 
		);

		// h::log( 't:> allow for badly spaced tags around sections... whitespace flexible..' );
		if ( 
			preg_match_all( $regex_find, $string, $matches, PREG_OFFSET_CAPTURE ) 
		){

			// strip all section blocks, we don't need them now ##
			// $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
			// $regex_remove = \apply_filters( 
			// 	'q/render/markup/comment/regex/remove', 
			// 	"/$open.*?$close/ms" 
			// 	// "/{{#.*?\/#}}/ms"
			// );
			// render::$markup['template'] = preg_replace( $regex_remove, "", render::$markup['template'] ); 
		
			// preg_match_all( '/%[^%]*%/', $string, $matches, PREG_SET_ORDER );
			// h::log( $matches[1] );

			// sanity ##
			if ( 
				! $matches
				|| ! isset( $matches[1] ) 
				|| ! $matches[1]
			){

				h::log( 'e:>Error in returned matches array' );

				return false;

			}

			foreach( $matches[1] as $match => $value ) {

				// position to add placeholder ##
				if ( 
					! is_array( $value )
					|| ! isset( $value[0] ) 
					|| ! isset( $value[1] ) 
					|| ! isset( $matches[0][$match][1] )
				) {

					h::log( 'e:>Error in returned matches - no position' );

					continue;

				}

				// h::log( 'd:>Searching for comments data...' );

				$position = $matches[0][$match][1]; // take from first array ##
				// h::log( 'd:>position: '.$position );
				// h::log( 'd:>position from 1: '.$matches[0][$match][1] ); 
				
				// get a single comment ##
				$comment = core\method::string_between( $matches[0][$match][0], $open, $close );

				// sanity ##
				if ( 
					! isset( $comment ) 
				){

					h::log( 'e:>Error in returned match function' );

					continue; 

				}

				// clean up ##
				$comment = trim($comment);

				// test what we have ##
				// h::log( 'd:>comment: "'.$comment.'"' );

				// hash ##
				// $hash = bin2hex( random_bytes(16) );
				$hash = 'comment__'.\mt_rand();

				// no escaping.. yet
				// $config = [ 'config' => [ 'escape' => false ] ];
				// h::log( $config );
				// if ( ! isset( render::$args[$hash] ) ) render::$args[$hash] = [];
				// render::$args[$hash] = \q\core\method::parse_args( $config, render::$args[$hash] );
				// h::log( render::$args );

				// so, we can add a new field value to $args array based on the field name - with the comment as value
				render\fields::define([
					$hash 		=> '<!-- '.$comment.' -->',
				]);
				
				// also, add a log entry ##
				h::log( 'd:>'.$comment );

				// finally -- add a variable "{{ $field }}" before this comment block at $position to markup->template ##
				$variable = willow\tags::wrap([ 'open' => 'var_o', 'value' => $hash, 'close' => 'var_c' ]);
				willow\markup::set( $variable, $position, 'variable' ); // '{{ '.$field.' }}'

			}

		}

	}


	public static function cleanup( $args = null ){

		$open = trim( willow\tags::g( 'com_o' ) );
		$close = trim( willow\tags::g( 'com_c' ) );

		// strip all section blocks, we don't need them now ##
		// $regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
		$regex = \apply_filters( 
			'q/render/parse/comment/cleanup/regex', 
			"/$open.*?$close/ms" 
			// "/{{#.*?\/#}}/ms"
		);
		// render::$markup['template'] = preg_replace( $regex_remove, "", render::$markup['template'] ); 

		// use callback to allow for feedback ##
		render::$markup['template'] = preg_replace_callback(
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

					h::log( $count .' comment tags removed...' );

				}

				// return nothing for cleanup ##
				return "";

			}, 
			render::$markup['template'] 
		);

	}




}
