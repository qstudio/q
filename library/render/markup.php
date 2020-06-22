<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
use q\render;

class markup extends \q\render {


	public static function pre_format(){

		// pre-format markup to extract comments ##
		render\markup::comments();

		// search for config settings in markup, such as "src" handle ##
		render\markup::config();

	}


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
        ) {

			// log ##
			h::log( self::$args['group'].'~>e:>Error with passed $args');

            return false;

		}
		
        // test ##
        // helper::log( self::$fields );
		// helper::log( self::$markup );
		
		// pre-format markup to extract comments ##
		// self::comments();

        // new string to hold output ## 
		$string = self::$markup;
		
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
				h::log( self::$args['group'].'~>n:>The value of: "'.$key.'" is not a string or integer - so it will be skipped and removed from markup...');

                unset( self::$fields[$key] );

                continue;

            }

			// h::log( 'working key: '.$key.' with value: '.$value );
			
			// markup string, with filter and wrapprer lookup ##
			$string = self::string([ 'key' => $key, 'value' => $value, 'string' => $string ]);

            // template replacement ##
            // $string = str_replace( '%'.$key.'%', $value, $string );

        }

        // helper::log( $string );

        // check for any left over placeholders - remove them ##
        if ( 
            $placeholders = self::get_placeholders( $string ) 
        ) {

			// log ##
			h::log( self::$args['group'].'~>n:>"'.count( $placeholders ) .'" placeholders found in formatted string - these will be removed');

            // helper::log( $placeholders );

            // remove any leftover placeholders in string ##
            foreach( $placeholders as $key => $value ) {
            
                $string = self::remove_placeholder( $value, $string );
            
            }

        }

        // filter ##
        $string = core\filter::apply([ 
            'parameters'    => [ 'string' => $string ], // pass ( $string ) as single array ##
            'filter'        => 'q/render/markup/'.self::$args['group'], // filter handle ##
            'return'        => $string
        ]); 

        // check ##
        // h::log( 'd:>'.$string );

        // apply to class property ##
        self::$output = $string;

        // return ##
        return true;

	}
	


	/**
	 * Scan for comments in markup and convert to placeholders and $fields
	 * 
	 * @since 4.1.0
	*/
	public static function comments(){

		// h::log( $args['key'] );

		// get markup ##
		$string = self::$markup;

		// sanity ##
		if (  
			! $string
			|| is_null( $string )
			// || ! isset( $args['key'] )
			// || ! isset( $args['value'] )
			// || ! isset( $args['string'] )
		){

			h::log( self::$args['group'].'~>e:>Error in $markup' );

			return false;

		}

		// h::log('d:>'.$string);

		// get all comments, add markup to $markup->$field ##
		// $matches = [];
		$regex_find = \apply_filters( 'q/render/markup/comments/regex/find', "/\<!--(.*?)--\>/s" );
		if ( 
			preg_match_all( $regex_find, $string, $matches, PREG_OFFSET_CAPTURE ) 
		){

			// strip all comment blocks, we don't need them now ##
			$regex_remove = \apply_filters( 'q/render/markup/comments/regex/remove', "/<!--.*?-->/ms" );
			self::$markup = preg_replace( $regex_remove, "", self::$markup );
		
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

				$position = $matches[0][$match][1]; // take from first array ##
				// h::log( 'd:>position: '.$position );
				// h::log( 'd:>position from 1: '.$matches[0][$match][1] ); 

				// foreach( $matches[1][0][0] as $k => $v ){
				$delimiter = \apply_filters( 'q/render/markup/comments/delimiter', "::" );
				list( $field, $markup ) = explode( $delimiter, $value[0] );

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
				// h::log( "d:>field: ".$field );
				// h::log( "d:>markup: ".$markup );

				// so, we can add a new field value to $args array based on the field name - with the markup as value
				self::$args[$field] = $markup;

				// and now we need to add a placeholder "%$field%" before this comment block at $position ##
				self::set_placeholder( "%$field%", $markup, $position );

			}

			// ok - done ##
			return true;

		}

		// kick it back ##
		return false;


	}




	/**
	 * Scan config in markup and convert to $fields
	 * 
	 * @since 4.1.0
	*/
	public static function config(){

		// h::log( $args['key'] );

		// get markup ##
		$string = self::$markup;

		// sanity ##
		if (  
			! $string
			|| is_null( $string )
			// || ! isset( $args['key'] )
			// || ! isset( $args['value'] )
			// || ! isset( $args['string'] )
		){

			h::log( self::$args['group'].'~>e:>Error in $markup' );

			return false;

		}

		// h::log('d:>'.$string);

		// get all placeholders from markup string ##
        if ( 
            ! $placeholders = self::get_placeholders( $string ) 
        ) {

			h::log( self::$args['group'].'~>d:>No placeholders found in $markup');

			return false;

		}

		// log ##
		// h::log( self::$args['group'].'~>n:>"'.count( $placeholders ) .'" placeholders found in string');
		h::log( self::$args['group'].'~>d:>"'.count( $placeholders ) .'" placeholders found in string');

		// h::log( self::$args['group'].'~>d:>'.$placeholders );

		// remove any leftover placeholders in string ##
		foreach( $placeholders as $key => $value ) {

			// h::log( self::$args['group'].'~>d:>'.$value );

			// now, we need to look for the config pattern, defined as field(setting:value;) and try to handle any data found ##
			$regex_find = \apply_filters( 'q/render/markup/config/regex/find', '/\((.*?)\)/s' );
			if ( 
				preg_match( $regex_find, $value, $matches ) 
			){

				// h::log( $matches );

				// sanity ##
				if ( 
					! $matches
					|| ! isset( $matches[0] ) 
					|| ! $matches[0]
				){

					h::log( self::$args['group'].'~>e:>Error in returned matches array' );

					continue;

				}

				// h::log( $matches[0] );
				list( $config_setting, $config_value ) = str_replace( [ '(', ')' ], '', explode( ':', $matches[0] ) );

				// sanity ##
				if ( 
					! isset( $config_setting ) 
					|| ! isset( $config_value ) 
				){

					h::log( 'e:>Error in returned match config or value' );

					continue; 

				}

				// clean up ##
				$config_setting = trim($config_setting);
				$config_value = str_replace( ';', '', trim($config_value) );

				// get field ##
				$field = str_replace( '%', '', $value );

				// check if field is sub field i.e: "post__title" ##
				if ( false !== strpos( $field, '__' ) ) {

					$field_array = explode( '__', $field );

					$field = $field_array[0]; // take first part ##

				}

				// matches[0] contains the whole string matched - for example "(handle:square;)" ##
				// we can use this to work out the new_placeholder value
				$placeholder = $value;
				$new_placeholder = explode( '(', $placeholder )[0].'%';

				// test what we have ##
				// h::log( "d:>placeholder: ".$value );
				// h::log( "d:>new_placeholder: ".$new_placeholder);
				// h::log( "d:>field: ".$field );
				// h::log( "d:>config_setting: ".$config_setting );
				// h::log( "d:>config_value: ".$config_value );

				// @todo - add config handler... based on field type ( field[1] from explode above )##
				self::$args['img'][$config_setting][$field] = $config_value;

				// now, edit the placeholder, to remove the config ##
				render\markup::edit_placeholder( $value, $new_placeholder );

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

			h::log( self::$args['group'].'~>e:>Error in passed args to "string" method' );

			return false;

		}

		// get string ##
		$string = $args['string'];
		$value = $args['value'];
		$key = $args['key'];

		// look for wrapper in markup ##
		if ( isset( self::$args[$key] ) ) {

			$markup = self::$args[ $key ];

			// filter ##
			$string = core\filter::apply([ 
				'parameters'    => [ 'string' => $string ], // pass ( $string ) as single array ##
				'filter'        => 'q/render/markup/wrap/'.self::$args['group'].'/'.$key, // filter handle ##
				'return'        => $string
			]); 

			// h::log( 'found: '.$markup );

			// wrap key value in found markup ##
			// <h2 class="mt-5">%content%</h2> ##
			$value = str_replace( '%content%', $value, $markup );

		}

		// filter ##
		$string = core\filter::apply([ 
             'parameters'    => [ 'string' => $string ], // pass ( $string ) as single array ##
             'filter'        => 'q/render/markup/string/before/'.self::$args['group'].'/'.$key, // filter handle ##
             'return'        => $string
        ]); 

		// template replacement ##
		$string = str_replace( '%'.$key.'%', $value, $string );
		
		// filter ##
		$string = core\filter::apply([ 
             'parameters'    => [ 'string' => $string ], // pass ( $string ) as single array ##
             'filter'        => 'q/render/markup/string/after/'.self::$args['group'].'/'.$key, // filter handle ##
             'return'        => $string
        ]); 

		// return ##
		return $string;

	}



    /**
     * Update Markup base for passed field ##
     * 
     */
    public static function set_markup( string $field = null, $count = null ){

        // sanity ##
        if ( 
            is_null( $field )
            || is_null( $count )
        ) {

			// log ##
			h::log( self::$args['group'].'~>n:>No field value or count iterator passed to method');

            return false;

        }

        // check ##
        // helper::log( 'Update template markup for field: '.$field.' @ count: '.$count );

        // look for required markup ##
        if ( ! isset( self::$args[$field] ) ) {

			// log ##
			h::log( self::$args['group'].'~>n:>Field: "'.$field.'" does not have required markup defined in $args->$field' );

            // bale if not found ##
            return false;

        }

        // get markup ##
        // $markup = self::$args[$field];

        // helper::log( $markup );
        /*
        <div class="col-12">
            <h3>
                <a href="%permalink%">
                    %post_title%
                </a>
            </h3>
            <span class="badge badge-pill badge-primary">
                %category_name%
            </span>
        </div>
        */

        // get target placeholder ##
        $placeholder = '%'.$field.'%';
        if ( 
            ! self::get_placeholder( $placeholder )
        ) {

			// log ##
			h::log( self::$args['group'].'~>n:>Placeholder: "'.$placeholder.'" is not in the passed markup template' );

            return false;

        }

        // so, we have the repeater markup to copy, placeholder in template to locate new markup ... 
        // && we need to find all placeholders in markup and append field__X__PLACEHOLDER

        // get all placeholders from markup->$field ##
        if ( 
            ! $placeholders = self::get_placeholders( self::$args[$field] ) 
        ) {

			// log ##
			h::log( self::$args['group'].'~>n:>No placeholders found in passed string' );

            return false;

        }

        // test ##
        // helper::log( $placeholders );

        // iterate over %placeholders% adding prefix ##
        $new_placeholders = [];
        foreach( $placeholders as $key => $value ) {

            // helper::log( 'Working placeholder: '.$value );

            $new_placeholders[] = '%'.$field.'__'.$count.'__'.str_replace( '%', '', $value ).'%';

        } 

        // testnew placeholders ##
        // helper::log( $new_placeholders );

        // generate new markup from template with new_placeholders ##
        $new_markup = str_replace( $placeholders, $new_placeholders, self::$args[$field] );

        // helper::log( $new_markup );

        // use strpos to get location of %placeholder ##
        $position = strpos( self::$markup, $placeholder );
        // helper::log( 'Position: '.$position );

        // add new markup to $template as defined position - don't replace %placeholder% yet... ##
        $new_template = substr_replace( self::$markup, $new_markup, $position, 0 );

        // test ##
        // helper::log( $new_template );

        // push back into main stored markup ##
        self::$markup = $new_template;

        // kick back ##
        return true;

    }




    /**
     * Get all placeholders from passed string value 
     *  
     */
    public static function get_placeholders( string $string = null ) {
        
        // sanity ##
        if (
            is_null( $string ) 
        ) {

			// log ##
			h::log( self::$args['group'].'~>e:>No string value passed to method' );

            return false;

        }

		$regex_find = \apply_filters( 'q/render/markup/placeholders/get', '~\%(.*?)\%~' );
		// if ( ! preg_match_all('~\%(\w+)\%~', $string, $matches ) ) {
        if ( ! preg_match_all( $regex_find, $string, $matches ) ) {

			// log ##
			h::log( self::$args['group'].'~>n:>No extra placeholders found in string to clean up - good!' );

            return false;

        }

        // test ##
        // helper::log( $matches[0] );

        // kick back placeholder array ##
        return $matches[0];

    }



    /**
     * Check if single placeholder exists 
     * @todo - work on passed params 
     *  
     */
    public static function get_placeholder( string $placeholder = null, $field = null ) {
		
		// if $markup template passed, check there, else check self::$markup ##
		$markup = is_null( $field ) ? self::$markup : self::$args[$field] ;

        if ( ! substr_count( $markup, $placeholder ) ) {

            return false;

        }

        // good ##
        return true;

	}



	/**
     * Edit %placeholder% in self:$args['markup']
     * 
     */
    public static function edit_placeholder( string $placeholder = null, $new_placeholder = null ) {

        // sanity ##
        if (
			is_null( $placeholder ) 
			|| is_null( $new_placeholder )
		) {

			// log ##
			h::log( self::$args['group'].'~>e:>No placeholder or new_placeholder value passed to method' );

            return false;

		}
		
        // check if placeholder is correctly formatted --> %STRING% ##
        $needle = '%';
        if (
            $needle != $placeholder[0] // returns first character ## 
			|| $needle != substr( $placeholder, -1 ) // returns last character ##
			|| $needle != $new_placeholder[0] // returns first character ## 
            || $needle != substr( $new_placeholder, -1 ) // returns last character ##
        ) {

			// log ##
			h::log( self::$args['group'].'~>e:>Placeholder is not correctly formatted - missing % at start or end.' );
			// h::log( 'd:>Placeholder is not correctly formatted - missing % at start or end.' );

            return false;

		}
		
		// ok - we should be good to search and replace old for new ##
		$string = str_replace( $placeholder, $new_placeholder, self::$markup );

		// test new string ##
		// h::log( 'd:>'.$string );

		// overwrite markup property ##
		self::$markup = $string;

		// kick back ##
		return true;

	}
	
	

	/**
     * Set %placeholder% in self:$args['markup'] at defined position
     * 
     */
    public static function set_placeholder( string $placeholder = null, $markup = null, $position = null ) {

        // sanity ##
        if (
			is_null( $placeholder ) 
			|| is_null( $markup )
			|| is_null( $position )
		) {

			// log ##
			h::log( self::$args['group'].'~>e:Error in data passed to method' );

            return false;

		}
		
		// where are we replacing ##
		// $markup = ! \is_null( $markup ) ? $markup : self::$markup ;

		// h::log( $markup );

        // check if placeholder is correctly formatted --> %STRING% ##
        $needle = '%';
        if (
            $needle != $placeholder[0] // returns first character ## 
            || 
            $needle != substr( $placeholder, -1 ) // returns last character ##
        ) {

			// log ##
			h::log( self::$args['group'].'~>e:>Placeholder: "'.$placeholder.'" is not correctly formatted - missing % at start or end.' );

            return false;

		}
		
		// h::log( 'd:>Adding placeholder: "'.$placeholder.'"' );

		// use strpos to get location of %placeholder ##
		// $position = strpos( self::$markup, $placeholder );
		// helper::log( 'Position: '.$position );

		// add new placeholder to $template as defined position - don't replace %placeholder% yet... ##
		$new_template = substr_replace( self::$markup, $placeholder, $position, 0 );

		// test ##
		// h::log( 'd:>'.$new_template );

		// push back into main stored markup ##
		self::$markup = $new_template;
		
		// h::log( 'd:>'.$markup );

		// log ##
		// h::log( self::$args['group'].'~>placeholder_added:>"'.$placeholder.'" @position: "'.$position.'" by "'.core\method::backtrace([ 'level' => 2, 'return' => 'function' ]).'"' );

        // positive ##
        return $markup;

    }



    /**
     * Remove %placeholder% from self:$args['markup'] array
     * 
     */
    public static function remove_placeholder( string $placeholder = null, $markup = null ) {

        // sanity ##
        if (
			is_null( $placeholder ) 
			|| is_null( $markup )
		) {

			// log ##
			h::log( self::$args['group'].'~>e:>No placeholder or markkup value passed to method' );

            return false;

		}
		
		// where are we replacing ##
		// $markup = ! \is_null( $markup ) ? $markup : self::$markup ;

		// h::log( $markup );

        // check if placeholder is correctly formatted --> %STRING% ##
        $needle = '%';
        if (
            $needle != $placeholder[0] // returns first character ## 
            || 
            $needle != substr( $placeholder, -1 ) // returns last character ##
        ) {

			// log ##
			h::log( self::$args['group'].'~>e:>Placeholder: "'.$placeholder.'" is not correctly formatted - missing % at start or end.' );

            return false;

		}
		
		// h::log( 'Removing placeholder: "'.$placeholder.'"' );

        // remove placeholder from markup ##
		$markup = 
			str_replace( 
            	$placeholder, 
            	'', // nada ##
            	$markup
			);
		
		// h::log( 'd:>'.$markup );

		// log ##
		h::log( self::$args['group'].'~>placeholder_removed:>"'.$placeholder.'" by "'.core\method::backtrace([ 'level' => 2, 'return' => 'function' ]).'"' );

        // positive ##
        return $markup;

    }


}
