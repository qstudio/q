<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
use q\render;

class markup extends \q\render {


    /**
     * Apply Markup changes to passed template
     * find all placeholders in self::$markup and replace with matching values in self::$fields
     * 
     */
    public static function prepare(){

        // sanity checks ##
        if (
            ! isset( self::$fields )
            || ! is_array( self::$fields )
			|| ! isset( self::$markup )
			|| ! is_array( self::$markup )
			|| ! isset( self::$markup['template'] ) // default markup property ##
        ) {

			// log ##
			h::log( self::$args['task'].'~>e:>Error with passed $args');

            return false;

		}
		
        // test ##
        // helper::log( self::$fields );
		// helper::log( self::$markup );
		
		// pre-format markup to extract comments ##
		// self::comments();

        // new string to hold output ## 
		$string = self::$markup['template'];
		
        // loop over each field, replacing placeholders with values ##
        foreach( self::$fields as $key => $value ) {

			// cast booleans to integer ##
			if ( \is_bool( $value ) ) {

				// @todo - is this required ?? ##
				// $value = (int) $value;

			}

            // we only want integer or string values here -- so check and remove, as required ##
            if ( 
				! \is_string( $value ) 
				&& ! \is_int( $value ) 
			) {

				// h::log( 'The value of: '.$key.' is not a string or integer - so we cannot render it' );

				// log ##
				h::log( self::$args['task'].'~>n:>The value of: "'.$key.'" is not a string or integer - so it will be skipped and removed from markup...');

                unset( self::$fields[$key] );

                continue;

            }

			// h::log( 'working key: '.$key.' with value: '.$value );
			
			// markup string, with filter and wrapper lookup ##
			$string = self::string([ 'key' => $key, 'value' => $value, 'string' => $string ]);

            // template replacement ##
            // $string = str_replace( '{{ '.$key.' }}', $value, $string );

        }

        // helper::log( $string );

        // check for any left over placeholders - remove them ##
        if ( 
            $placeholders = render\placeholder::get( $string ) 
        ) {

			// log ##
			h::log( self::$args['task'].'~>n:>"'.count( $placeholders ) .'" placeholders found in formatted string - these will be removed');

            // h::log( $placeholders );

            // remove any leftover placeholders in string ##
            foreach( $placeholders as $key => $value ) {
            
                $string = render\placeholder::remove( $value, $string );
            
            }

        }

        // filter ##
        $string = core\filter::apply([ 
            'parameters'    => [ 'string' => $string ], // pass ( $string ) as single array ##
            'filter'        => 'q/render/markup/'.self::$args['task'], // filter handle ##
            'return'        => $string
        ]); 

        // check ##
        // h::log( 'd:>'.$string );

        // apply to class property ##
        return self::$output = $string;

        // return ##
        return true;

	}



	/**
	 * filter passed args for markup
	 * 
	 * @since 4.1.0
	*/
	public static function args( $args = null ){

		// sanity ##
		
        // test ##
		// h::log( $args );

		// make an array ##
		self::$markup = []; 
		
		// default -- almost useless - but works for single values.. ##
		$markup = tag::wrap([ 'open' => 'vo', 'value' => 'value', 'close' => 'vc' ]);

		// filter ##
		$markup = \apply_filters( 'q/render/markup/default', $markup );
		// $markup = '<div>{{ value }}</div>';

		// if "markup" set in args, take this ##
		if ( isset( $args['markup'] ) ){

			if ( 
				is_array( $args['markup'] ) 
				// && isset( $args['markup']['template'] ) // not sure we need to check for this # ??
			) {

				// h::log('d:>Using array markup..');

				return self::$markup = $args['markup'];

			} else {

				// h::log('d:>Using single markup..');

				return self::$markup['template'] = $args['markup'];

			}

		}

		// args is a string - take the whole thing ##
		if ( is_string( $args ) ){

			// h::log('d:>Using string markup..');

			return self::$markup['template'] = $args;

		}

		// // if no markup sent, but args is an array.. ##
		// if ( 
		// 	! isset( $args['markup'] )
		// 	&& is_array( $args ) 
		// ) {

		// 	// default -- almost useless - but works for single values.. ##
		// 	$args['markup'] = '<div>{{ value }}</div>';

		// 	foreach( $args as $k => $v ) {

		// 		if ( is_string( $v ) ) {

		// 			// take first string value in $args markup ##
		// 			$args['markup'] = $v;

		// 			break;

		// 		}

		// 	}

		// }

		// h::log('d:>Using default markup..');

		// something went wrong ##
		// return false;

        // assign markup ##
		return self::$markup['template'] = $markup;

	}
	


	/**
	 * Scan for sections in markup and convert to placeholders and $fields
	 * 
	 * @since 4.1.0
	*/
	public static function section(){

		// h::log( $args['key'] );

		// sanity -- this requires ##


		// get markup ##
		$string = self::$markup['template'];

		// sanity ##
		if (  
			! $string
			|| is_null( $string )
		){

			h::log( self::$args['task'].'~>e:>Error in $markup' );

			return false;

		}

		// h::log('d:>'.$string);

		// get all sections, add markup to $markup->$field ##
		// {{# frontpage_work_more }}
		// $matches = [];
		// $regex_find = \apply_filters( 'q/render/markup/comment/regex/find', "/\<!--(.*?)--\>/s" );
		$regex_find = \apply_filters( 'q/render/markup/section/regex/find', "/{{#(.*?)\/#}}/s" );
		if ( 
			preg_match_all( $regex_find, $string, $matches, PREG_OFFSET_CAPTURE ) 
		){

			// strip all comment blocks, we don't need them now ##
			// $regex_remove = \apply_filters( 'q/render/markup/comment/regex/remove', "/<!--.*?-->/ms" );
			$regex_remove = \apply_filters( 'q/render/markup/section/regex/remove', "/{{#.*?\/#}}/ms" );
			self::$markup['template'] = preg_replace( $regex_remove, "", self::$markup['template'] ); 
		
			// preg_match_all( '/%[^%]*%/', $string, $matches, PREG_SET_ORDER );
			// h::debug( $matches );

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

				// h::log( 'd:>Searching for section field and markup...' );

				$position = $matches[0][$match][1]; // take from first array ##
				// h::log( 'd:>position: '.$position );
				// h::log( 'd:>position from 1: '.$matches[0][$match][1] ); 

				// foreach( $matches[1][0][0] as $k => $v ){
				// $delimiter = \apply_filters( 'q/render/markup/comments/delimiter', "::" );
				// list( $field, $markup ) = explode( $delimiter, $value[0] );
				$field = render\method::string_between( $matches[0][$match][0], '{{#', '}}' );
				$markup = render\method::string_between( $matches[0][$match][0], '{{# '.$field.' }}', '{{/#}}' );

				// sanity ##
				if ( 
					! isset( $field ) 
					|| ! isset( $markup ) 
				){

					h::log( 'e:>Error in returned match key or value' );

					continue; 

				}

				// clean up ##
				$field = trim($field);
				$markup = trim($markup);

				// test what we have ##
				// h::log( 'd:>field: "'.$field.'"' );
				// h::log( "d:>markup: $markup" );

				// so, we can add a new field value to $args array based on the field name - with the markup as value
				// self::$args[$field] = $markup;
				self::$markup[$field] = $markup;

				// and now we need to add a placeholder "{{ $field }}" before this comment block at $position to markup->template ##
				render\placeholder::set( "{{ $field }}", $position ); // , $markup

			}

		}

	}




	/**
	 * Scan for config in markup and convert to $fields
	 * 
	 * @since 4.1.0
	*/
	public static function config(){

		// h::log( $args['key'] );

		// get markup ##
		$string = self::$markup['template'];

		// sanity ##
		if (  
			! $string
			|| is_null( $string )
			// || ! isset( $args['key'] )
			// || ! isset( $args['value'] )
			// || ! isset( $args['string'] )
		){

			h::log( self::$args['task'].'~>e:>Error in $markup' );

			return false;

		}

		// h::log('d:>'.$string);

		// get all placeholders from markup string ##
        if ( 
            ! $placeholders = render\placeholder::get( $string ) 
        ) {

			// h::log( self::$args['task'].'~>d:>No placeholders found in $markup');
			// h::log( 'd:>No placeholders found in $markup: '.self::$args['task']);

			return false;

		}

		// log ##
		h::log( self::$args['task'].'~>d:>"'.count( $placeholders ) .'" placeholders found in string');
		// h::log( 'd:>"'.count( $placeholders ) .'" placeholders found in string');

		// remove any leftover placeholders in string ##
		foreach( $placeholders as $key => $value ) {

			// h::log( self::$args['task'].'~>d:>'.$value );

			// now, we need to look for the config pattern, defined as field(setting:value;) and try to handle any data found ##
			// $regex_find = \apply_filters( 'q/render/markup/config/regex/find', '/[[(.*?)]]/s' );
			
			// if ( 
			// 	preg_match( $regex_find, $value, $matches ) 
			// ){

			if ( 
				$config_string = render\method::string_between( $value, '[[', ']]' )
			){

				// store placeholder ##
				$placeholder = $value;

				// convert string to json format by adding "{}" ##
				// $config_string = '{'.$config_string.'}';

				// $config_string = [
				// 	'handle' 	=> [
				// 		'all' 	=> 'square-sm',
				// 		'lg'	=> 'vertical-lg'
				// 	],
				// 	'second'	=> 'string'
				// ];

				// $config_string = json_encode( $config_string );

				// h::log( $config_string );

				// // grab config JSON ##
				// $config_string = '{ "handle":{ "all":"square-sm", "lg":"vertical-lg" }, "string": "value" }';
				$config_object = json_decode( $config_string );
				// $config_object = isset( $config_json[0] ) ? $config_json[0] : false ;

				// h::log( 'd:>config: '.$config_string );
				// h::log( $config_json );
				// h::log( $config_object );

				// sanity ##
				if ( 
					! $config_string
					|| ! is_object( $config_object )
					// || ! isset( $matches[0] ) 
					// || ! $matches[0]
				){

					h::log( self::$args['task'].'~>e:>No config in placeholder: '.$placeholder ); // @todo -- add "loose" lookups, for white space '@s
					// h::log( 'd:>No config in placeholder: '.$placeholder ); // @todo -- add "loose" lookups, for white space '@s''

					continue;

				}

				// h::log( $matches[0] );
				// list( $config_setting, $config_value ) = str_replace( [ '[[', ']]' ], '', explode( ':', $value ) );
				// {{ frontpage_work_top__src[[ handle: square; ]] }}
				// $config_array = explode( ':', trim($config) );

				// we need an array, so check ##
				// if ( 
				// 	// ! $config_array
				// 	// || ! is_array( $config_array )
				// 	! $config
				// ){

				// 	h::log( self::$args['task'].'~>e:>Failed to extract good config from placeholder: '.$value );

				// 	continue;

				// }

				// get data and clean up ##
				// $config_setting = trim( $config_array[0] );
				// $config_value = str_replace( ';', '', trim( $config_array[1] ) );

				// // sanity ##
				// if ( 
				// 	! isset( $config_setting ) 
				// 	|| ! isset( $config_value ) 
				// ){

				// 	h::log( self::$args['task'].'~>e:>Error in extracted config from placeholder: '.$value );

				// 	continue; 

				// }

				// get field ##
				// h::log( 'value: '.$value );
				
				// $field = trim( render\method::string_between( $value, '{{ ', '[[' ) );
				$field = str_replace( $config_string, '', $value );

				// clean up field data ##
				$field = preg_replace( "/[^A-Za-z0-9_]/", '', $field );

				// h::log( 'field: '.$field );

				// check if field is sub field i.e: "post__title" ##
				if ( false !== strpos( $field, '__' ) ) {

					$field_array = explode( '__', $field );

					$field_name = $field_array[0]; // take first part ##
					$field_type = $field_array[1]; // take second part ##

				} else {

					$field_name = $field; // take first part ##
					$field_type = $field; // take second part ##

				}

				// we need field_name, so validate ##
				if (
					! $field_name
					|| ! $field_type
				){

					h::log( self::$args['task'].'~>e:>Error extracting $field_name or $field_type from placeholder: '.$placeholder );

					continue;

				}

				// matches[0] contains the whole string matched - for example "(handle:square;)" ##
				// we can use this to work out the new_placeholder value
				// $placeholder = $value;
				// $new_placeholder = explode( '(', $placeholder )[0].' }}';
				$new_placeholder = '{{ '.$field.' }}';

				// test what we have ##
				// h::log( "d:>placeholder: ".$value );
				// h::log( "d:>new_placeholder: ".$new_placeholder);
				// h::log( "d:>field_name: ".$field_name );
				// h::log( "d:>field_type: ".$field_type );

				foreach( $config_object as $k => $v ) {

					// h::log( "d:>config_setting: ".$k );
					// h::log( "d:>config_value: ".$v );

					// @todo - add config handlers... based on field type ##
					switch ( $field_type ) {

						case "src" :
							
							// assign new $args[FIELDNAME]['src'] with value of config --
							self::$args[$field_name]['config'][$k] = is_object( $v ) ? (array) $v : $v; // note, $v may be an array of values

						break ;

					}

				}

				// h::log( self::$args[$field_name] );

				// now, edit the placeholder, to remove the config ##
				render\placeholder::edit( $placeholder, $new_placeholder );

			}
		
        }

	}



	public static function string( $args = null ){

		// h::log( $args['key'] );

		// sanity ##
		if (  
			is_null( $args )
			|| ! isset( $args['key'] )
			|| ! isset( $args['value'] )
			|| ! isset( $args['string'] )
		){

			h::log( self::$args['task'].'~>e:>Error in passed args to "string" method' );

			return false;

		}

		// get string ##
		$string = $args['string'];
		$value = $args['value'];
		$key = $args['key'];

		// h::log( 'key: "'.$key.'" - value: "'.$value.'"' );

		// look for wrapper in markup ##
		// if ( isset( self::$args[$key] ) ) {
		// if ( isset( self::$markup[$key] ) ) { // ?? @todo -- how is this working ?? -- surely, this should look for 'wrap'
		if ( isset( self::$markup['wrap'] ) ) { // ?? @todo -- how is this working ?? -- surely, this should look for 'wrap'

			h::log( 't:>@todo.. string wrap logic...' );

			// $markup = self::$args[ $key ];
			$markup = self::$markup[ 'wrap' ];

			// filter ##
			$string = core\filter::apply([ 
				'parameters'    => [ 'string' => $string ], // pass ( $string ) as single array ##
				'filter'        => 'q/render/markup/wrap/'.self::$args['task'].'/'.$key, // filter handle ##
				'return'        => $string
			]); 

			// h::log( 'found: '.$markup );

			// wrap key value in found markup ##
			// example: markup->wrap = '<h2 class="mt-5">{{ content }}</h2>' ##
			$value = str_replace( 
				// '{{ content }}', 
				tag::wrap([ 'open' => 'vo', 'value' => 'content', 'close' => 'vc' ]), 
				$value, $markup 
			);

		}

		// filter ##
		$string = core\filter::apply([ 
             'parameters'    => [ 'string' => $string ], // pass ( $string ) as single array ##
             'filter'        => 'q/render/markup/string/before/'.self::$args['task'].'/'.$key, // filter handle ##
             'return'        => $string
        ]); 

		// template replacement ##
		// $string = str_replace( '{{ '.$key.' }}', $value, $string );
		// h::log( $string );

		// regex way ##
		$regex = \apply_filters( 'q/render/markup/string', "~\{{\s+$key\s+\}}~" ); // '~\{{\s(.*?)\s\}}~' 
		$string = preg_replace( $regex, $value, $string ); 

		// filter ##
		$string = core\filter::apply([ 
             'parameters'    => [ 'string' => $string ], // pass ( $string ) as single array ##
             'filter'        => 'q/render/markup/string/after/'.self::$args['task'].'/'.$key, // filter handle ##
             'return'        => $string
        ]); 

		// return ##
		return $string;

	}



    /**
     * Update Markup base for passed field ##
     * 
     */
    public static function set( string $field = null, $count = null ){

        // sanity ##
        if ( 
            is_null( $field )
            || is_null( $count )
        ) {

			// log ##
			h::log( self::$args['task'].'~>n:>No field value or count iterator passed to method');

            return false;

        }

        // check ##
        // helper::log( 'Update template markup for field: '.$field.' @ count: '.$count );

        // look for required markup ##
        // if ( ! isset( self::$args[$field] ) ) {
		if ( ! isset( self::$markup[$field] ) ) {

			// log ##
			h::log( self::$args['task'].'~>n:>Field: "'.$field.'" does not have required markup defined in "$markup->'.$field.'"' );

            // bale if not found ##
            return false;

        }

        // get markup ##
        // $markup = self::$args[$field];

        // get target placeholder ##
        $placeholder = '{{ '.$field.' }}';
        if ( 
            ! render\placeholder::exists( $placeholder )
        ) {

			// log ##
			h::log( self::$args['task'].'~>n:>Placeholder: "'.$placeholder.'" is not in the passed markup template' );

            return false;

        }

        // so, we have the repeater markup to copy, placeholder in template to locate new markup ... 
        // && we need to find all placeholders in markup and append field__X__PLACEHOLDER

        // get all placeholders from markup->$field ##
        if ( 
            ! $placeholders = render\placeholder::get( self::$markup[$field] ) 
        ) {

			// log ##
			h::log( self::$args['task'].'~>n:>No placeholders found in passed string' );

            return false;

        }

        // test ##
        // helper::log( $placeholders );

        // iterate over {{ placeholders }} adding prefix ##
        $new_placeholders = [];
        foreach( $placeholders as $key => $value ) {

            // helper::log( 'Working placeholder: '.$value );
			// new placeholder ## -- WOWO... looks flaky ##
			// h::log( 't:>todo.. make this new field name more reliable' );
			$new = '{{ '.trim($field).'__'.trim($count).'__'.trim( str_replace( [ '{{', '{{ ', '}}', ' }}' ], '', trim($value) ) ).' }}';

			// single whitespace max ## @might be needed ##
			// $new = preg_replace( '!\s+!', ' ', $new );	

			// h::log( 'new_placeholder: '.$new );

			$new_placeholders[] = $new;

            // $new_placeholders[] = '{{ '.trim($field).'__'.trim($count).'__'.str_replace( [ '{{', '{{ ', '}}', ' }}' ], '', trim($value) ).' }}';

        } 

        // testnew placeholders ##
        // h::log( $new_placeholders );

        // generate new markup from template with new_placeholders ##
        $new_markup = str_replace( $placeholders, $new_placeholders, self::$markup[$field] );

        // helper::log( $new_markup );

        // use strpos to get location of {{ placeholder }} ##
        $position = strpos( self::$markup['template'], $placeholder );
        // helper::log( 'Position: '.$position );

        // add new markup to $template as defined position - don't replace {{ placeholder }} yet... ##
        $new_template = substr_replace( self::$markup['template'], $new_markup, $position, 0 );

        // test ##
        // helper::log( $new_template );

        // push back into main stored markup ##
        self::$markup['template'] = $new_template;

        // kick back ##
        return true;

    }


}
