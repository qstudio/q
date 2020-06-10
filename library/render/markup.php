<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
use q\render;

class markup extends \q\render {

    /**
     * Apply Markup changes to passed template
     * find all placeholders in self::$markup['template'] and replace with matching values in self::$fields
     * 
     */
    public static function prepare(){

        // sanity checks ##
        if (
            ! isset( self::$fields )
            || ! is_array( self::$fields )
            || ! isset( self::$markup['template'] )
        ) {

			// log ##
			render\log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' =>  'Error with passed $args'
			]);

            return false;

        }

        // test ##
        // helper::log( self::$fields );
        // helper::log( self::$markup['template'] );

        // new string to hold output ## 
        $string = self::$markup['template'];

        // loop over each field, replacing placeholders with values ##
        foreach( self::$fields as $key => $value ) {

			// cast booleans to integer ##
			if ( \is_bool( $value ) ) {

				// // log ##
				// render\log::add([
				// 	'key' => 'notice', 
				// 	'field'	=> __FUNCTION__,
				// 	'value' =>  'The value of: '.$key.' was a boolean, so we cast to an integer'
				// ]);

				// $value = (int) $value;

			}

            // we only want integer or string values here -- so check and remove, as required ##
            if ( 
				! \is_string( $value ) 
				&& ! \is_int( $value ) 
			) {

				// h::log( 'The value of: '.$key.' is not a string or integer - so we cannot render it' );

				// log ##
				render\log::add([
					'key' => 'notice', 
					'field'	=> __FUNCTION__,
					'value' =>  'The value of: '.$key.' is not a string or integer - so it will be skipper and removed from markup...'
				]);

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
			render\log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' =>  count( $placeholders ) .' placeholders found in formatted string - these will be removed'
			]);

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
        // helper::log( $string );

        // apply to class property ##
        self::$output = $string;

        // return ##
        return true;

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

			h::log( 'Error in passed args to "string" method' );

			return false;

		}

		// get string ##
		$string = $args['string'];
		$value = $args['value'];
		$key = $args['key'];

		// look for wrapper in markup ##
		if ( isset( self::$markup[$key] ) ) {

			$markup = self::$markup[ $key ];

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
     * Update Markup based for passed field ##
     * 
     */
    public static function set_markup( string $field = null, $count = null ){

        // sanity ##
        if ( 
            is_null( $field )
            || is_null( $count )
        ) {

			// self::$log['error'][] = 'No field value or count iterator passed to method.';
			
			// log ##
			render\log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' =>  'No field value or count iterator passed to method.'
			]);

            return false;

        }

        // check ##
        // helper::log( 'Update template markup for field: '.$field.' @ count: '.$count );

        // look for required markup ##
        if ( ! isset( self::$markup[$field] ) ) {

			// self::$log['notice'][] = 'Field: '.$field.' does not have required markup defined in $args -- markup => '.$field;
			
			// log ##
			render\log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' =>  'Field: '.$field.' does not have required markup defined in $args -- markup => '.$field
			]);

            // bale if not found ##
            return false;

        }

        // get markup ##
        // $markup = self::$markup[$field];

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

			// self::$log['notice'][] = 'Placeholder: '.$placeholder.' is not in the passed markup template';
			
			// log ##
			render\log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' => 'Placeholder: '.$placeholder.' is not in the passed markup template'
			]);

            return false;

        }

        // so, we have the repeater markup to copy, placeholder in template to locate new markup ... 
        // && we need to find all placeholders in markup and append field__ID__PLACEHOLDER

        // get all placeholders from markup->$field ##
        if ( 
            ! $placeholders = self::get_placeholders( self::$markup[$field] ) 
        ) {

			// self::$log['notice'][] = 'No placeholders found in passed string';
			
			// log ##
			render\log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' => 'No placeholders found in passed string'
			]);

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
        $new_markup = str_replace( $placeholders, $new_placeholders, self::$markup[$field] );

        // helper::log( $new_markup );

        // use strpos to get location of %placeholder ##
        $position = strpos( self::$markup['template'], $placeholder );
        // helper::log( 'Position: '.$position );

        // add new markup to $template as defined position - don't replace %placeholder% yet... ##
        $new_template = substr_replace( self::$markup['template'], $new_markup, $position, 0 );

        // test ##
        // helper::log( $new_template );

        // push back to store markup ##
        self::$markup['template'] = $new_template;

        // kick back ##
        return true;

    }




    /**
     * Get all placeholders from passed string value 
     *  
     */
    public static function get_placeholders( string $string = null ) {
        
        // @todo - sanity ##
        if (
            is_null( $string ) 
        ) {

			// self::$log['error'][] = 'No string value passed to method.';
			
			// log ##
			render\log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'No string value passed to method.'
			]);

            return false;

        }

        if ( ! preg_match_all('~\%(\w+)\%~', $string, $matches ) ) {

			// self::$log['notice'][] = 'No extra placeholders found in string to clean up - good!.';
			
			// log ##
			render\log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' => 'No extra placeholders found in string to clean up - good!.'
			]);

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
    public static function get_placeholder( string $placeholder = null, $template = 'template' ) {
        
        if ( ! substr_count( self::$markup[$template], $placeholder ) ) {

            return false;

        }

        // good ##
        return true;

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

			// h::log ('No string value passed to method.');
			
			// log ##
			render\log::add([
				'key' => 'error', 
				'field'	=> __FUNCTION__,
				'value' => 'No placeholder or markkup value passed to method.'
			]);

            return false;

		}
		
		// where are we replacing ##
		// $markup = ! \is_null( $markup ) ? $markup : self::$markup['template'] ;

		// h::log( $markup );

        // check if placeholder is correctly formatted --> %STRING% ##
        $needle = '%';
        if (
            $needle != $placeholder[0] // returns first character ## 
            || 
            $needle != substr( $placeholder, -1 ) // returns last character ##
        ) {

			// self::$log['notice'][] = 'Placeholder is not correctly formatted - missing % at start or end of passed string.';
			
			// log ##
			render\log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' => 'Placeholder is not correctly formatted - missing % at start or end of passed string.'
			]);

            return false;

		}
		
		// h::log( 'Removing placeholder: "'.$placeholder.'"' );

        // remove from args ##
		// @todo - replace from all values keys in markup ##
		// @todo -- does this actually work ??
		$markup = 
			str_replace( 
            	$placeholder, 
            	'', // nada ##
            	$markup
			);
		
		// h::log( $markup );

		// log ##
		render\log::add([
			'key' => 'placeholder_removed', 
			'field'	=> $placeholder,
			'value' => core\method::backtrace([ 'level' => 2, 'return' => 'function' ])
		]);

        // positive ##
        return $markup;

    }


}