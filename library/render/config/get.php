<?php

namespace q\render\config;

use q\core;
use q\core\helper as h;
use q\plugin; 
use q\render;

class get extends render\config {


    /**
    * Contexts
    * 
    * @since       1.0
    * @return      void
    */
    public static function context()
    {

		// get all contexts as array with CONTEXT->TASK ##
		$config = render\config\context::load();
		// h::log( $config );

		$keys = array_keys( $config );

		// append "context_" to array keys ##
		foreach ( $keys as $key ) {
			
			$array[ $key ] = $key;
		
		}

		// sort nicely ##
		ksort( $array );

		// h::log( $array );

		// apply filter ##
		$array = \apply_filters( 'q/render/config/get/context', $array );

		// return ##
		return $array;

	}
	


	/**
    * Tasks
    * 
    * @since       1.0
    * @return      void
    */
    public static function task( $context = null )
    {

		// get all contexts as array with CONTEXT->TASK ##
		$config = render\config\context::load();
		// h::log( $config );

		if( ! isset( $config[$context] ) ){

			h::log( 'e:>Loaded config does not include data for context: '.$context );

			return false;

		}

		// take context-> task data ## 
		// $array = array_keys( $config[$context] );

		$keys = array_keys( $config[$context] );

		// append "context_" to array keys ##
		foreach ( $keys as $key ) {
			
			$array[ $context.'__'.$key ] = $key;
		
		}

		// sort nicely ##
		ksort( $array );

		// h::log( $array );

		// // apply filter, for extended contexts / tasks ##
		$array = \apply_filters( 'q/render/config/get/task', $array, $context );

		// return ##
		return $array;

    }




	/**
    * Values -- pulled from one single config file...
    * 
    * @since       1.0
    * @return      void
    */
    public static function value( $context = null, $task = null )
    {

		// @todo - sanity ##
		// h::log( '$context: '.$context.' $task: '.$task );

		// get all contexts as array with CONTEXT->TASK ##
		$config = render\config\context::load();
		// h::log( $config );

		/*
		@TODO -- get logic.. 

		from vanilla - h::get looks for config in Child > Parent > Q - 
		--- loading which file is found first and merging all together to form base config 

		after first save - config is pull from DB via ACF 
		--- what about extra config files ( partials or willows - perhaps these can only be added via admin.. why not? )
		--- what about new updates from Q OR Parent ? ( special __new lookup fields? how to know they are new.. instead DIFF is better option, to ensure all required context->tags are loaded.. so always load Q > Parent > Child.. then merge with saved values? )
		--- what about accidental deletions / errors ( REVERT SINGLE || REVERT ALL option )
		
		*/

		if( ! isset( $config[$context][$task] ) ){

			h::log( 'e:>Loaded config does not include data for context: '.$context.' -> task: '.$task );

			return false;

		}

		// h::log( $config[$context][$task] );

		if ( ! is_array( $config[$context][$task] ) ){

			h::log( 'e:>Stored values are not an array' );

			return false;

		}

		/*
		[0] => Array
        (
            [field_key] => markup
            [field_value] => Array
                (
                    [0] => Array
                        (
                            [field_sub_key] => template
                            [field_sub_value] => <div></div>
                        )
                    [1] => Array
                        (
                            [field_sub_key] => wrap
                            [field_sub_value] => <div>{{ content }}</div>
                        )
                )
		)
		[1] => Array
        (
            [field_key] => config
            [field_value] => Array
                (
                    [0] => Array
                        (
                            [field_sub_key] => debug
                            [field_sub_value] => 0
                        )
                    [1] => Array
                        (
                            [field_sub_key] => run
                            [field_sub_value] => 1
                        )
                )
		)
		
		(
			[config] => Array
				(
					[run] => true
					[debug] => false
				)
			[markup] => Array
				(
					[template] => 
					<main class="container {{ classes }}">
						<div class="row">
					[wrap] => 
					<div>{{ content }}</div>
				)
		)
		*/

		$array = [];
		foreach( $config[$context][$task] as $key => $value ){

			if ( is_array( $value ) ) {
				
				// build array values ##
				$sub_array = [];

				foreach( $value as $sub_key => $sub_value ){

					// sub_value might be a bool false -- so, let's convert it to "0"
					if ( 
						! $sub_value
						|| '' == $sub_value 
						|| '0' == $sub_value
					){

						$sub_value = 0;

					}

					// field_sub_type ##
					$field_sub_type = 'text';
					if ( 
						is_bool( $sub_value )
						|| '0' == $sub_value
					){

						$field_sub_type = 'boolean';

					// very basic ##
					} else if ( 
						render\method::starts_with( $sub_value, '<' )
						|| render\method::ends_with( $sub_value, '>' )
					){

						$field_sub_type = 'code';

					}

					$sub_array[] = [
						'field_sub_key' 			=> $sub_key,
						'field_sub_type' 			=> $field_sub_type,
						'field_sub_value_text' 		=> trim( $sub_value ), #trim( preg_replace('/\t+/', '', $sub_value ) )
						'field_sub_value_code' 		=> $sub_value, #trim( preg_replace('/\t+/', '', $sub_value ) )
						'field_sub_value_boolean'	=> (bool) $sub_value #trim( preg_replace('/\t+/', '', $sub_value ) )
					];

				}

				// assign value ##
				$value = $sub_array;

			} else {

				$value = $value;

			}

			$array[] = [
				'field_key' 	=> $key,
				'field_value'	=> $value // set value ##
			];

		}

		// h::log( $array );

		// validate that we have an array ##
		if(
			! is_array( $array )
		){

			h::log( 'e:>Error in built array' );

			return false;

		}

		// apply filter, for extended contexts / tasks ##
		$array = \apply_filters( 'q/render/config/get/value', $array, $context ,$task );

		// return ##
		return $array;

	}


}
