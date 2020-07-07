<?php

namespace q\render\config;

use q\core;
use q\core\helper as h;
use q\plugin; 
// use q\get;
use q\render;

// load it up ##
\q\render\config\save::run();

class save extends render\config {


    /**
    * Class Constructor
    * 
    * @since       1.0
    * @return      void
    */
    public static function run()
    {

		if ( ! is_admin() ){ return false; }

		// h::log( 'Here in save...' );

		add_action( 'acf/save_post', [ get_class(), 'option_one' ], 5 );
		
		// add_action( 'acf/save_post', [ get_class(), 'option_two' ], 5 );

	}
	

	
	public static function option_two( $post_id ) {

		// h::log( 'post_id: '.$post_id );
		// h::log( $_POST['acf'] );

		/*
		[field_q_config_ui__open] => Array
        (
            [row-0] => Array
                (
                    [field_key] => config
                    [field_value] => Array
                        (
                            [row-0] => Array
                                (
                                    [field_sub_key] => run
                                    [field_sub_type] => boolean
                                    [field_sub_value_bool] => 1
                                )

                            [row-1] => Array
                                (
                                    [field_sub_key] => debug
                                    [field_sub_type] => boolean
                                    [field_sub_value_bool] => 1
                                )

                        )

                )

            [row-1] => Array
                (
                    [field_key] => markup
                    [field_value] => Array
                        (
                            [row-0] => Array
                                (
                                    [field_sub_key] => template
                                    [field_sub_type] => code
                                    [field_sub_value_code] => <main class=\"container {{ classes }}\">
	<div class=\"row\">
                                )

                            [row-1] => Array
                                (
                                    [field_sub_key] => wrap
                                    [field_sub_type] => code
                                    [field_sub_value_code] => <div>{{ content }}</div>
                                )

                        )

                )

        )
		*/

		// Get previous values.
		// $prev_values = get_fields( $post_id );

		// h::log( $prev_values );
	
		// Get submitted values.
		$posted = $_POST['acf'];

		// sanity ##
		if( ! $posted ){

			h::log( 'd:>No ACF fields posted' );

			return false;

		}

		// start empty ##
		$array = [];

		// loop over fields and create right data format, then save to a file ##
		foreach( $posted as $key => $value ){

			// we need to format the top level content key --- field_q_config_ui__open ##
			$context_task = str_replace( 'field_q_config_', '', $key );
			
			// check if field name can be exploded ##
			if( strpos( $context_task, '__' ) === false ){

				h::log( 'd:>key does not include "__" -> '.$context_task );

				continue;

			}

			$context_task_explode = explode( '__', $context_task );
			// h::log( $context_task_explode );

			$context = $context_task_explode[0];
			$task = $context_task_explode[1];

			// values array ##
			$values = [];

			// add context->task values from inner array ##
			if( is_array( $value ) ){

				// h::log( $value );
				/*
				[row-0] => Array (
					[field_key] => config
					[field_value] => Array
						(
							[row-0] => Array
								(
									[field_sub_key] => run
									[field_sub_type] => boolean
									[field_sub_value_bool] => 0
								)
							[row-1] => Array
								(
									[field_sub_key] => debug
									[field_sub_type] => boolean
									[field_sub_value_bool] => 0
								)
						)
				)
				*/
				foreach( $value as $inner_k => $inner_v ){

					// h::log( $inner_k );
					// h::log( $inner_v );
					
					$array_values = [];

					if ( is_array( $inner_v ) ){

						foreach( $inner_v as $inner_2_k => $inner_2_v ){

							// h::log( $inner_2_v );
							/*
							[row-0] => Array
								(
									[field_sub_key] => run
									[field_sub_type] => boolean
									[field_sub_value_boolean] => 1
								)

							[row-1] => Array
								(
									[field_sub_key] => debug
									[field_sub_type] => boolean
									[field_sub_value_boolean] => 1
								)
							*/
							if( is_array( $inner_2_v ) ){

								foreach( $inner_2_v as $inner_3_k => $inner_3_v ){

									// h::log( $inner_3_v );
									/*
									(
										[field_sub_key] => run
										[field_sub_type] => boolean
										[field_sub_value_boolean] => 1
									)
									*/

									// get key ##
									$key_3 = $inner_3_v['field_sub_key'];

									// get value ##
									switch( $inner_3_v['field_sub_type'] ){

										case "boolean" :

											$value_3 = $inner_3_v['field_sub_value_boolean'];

										break ;

										case "code" :

											$value_3 = $inner_3_v['field_sub_value_code'];

										break ;

										default :
										case "text" :

											$value_3 = $inner_3_v['field_sub_value_text'];

										break ;

									}

									$array_values[$key_3] = $value_3;

								}

							}

						}

					}

					$values[ $inner_v['field_key'] ] = $array_values;

				}

			}

			// add key and array values ##
			$array[$context][$task] = $values;

		}

		// test array ##
		// h::log( $array );

		// write to file ##
		// self::file_put_array( \Q::get_plugin_path( 'library/render/config/_config.php' ), $array );
		if ( method_exists( 'q_theme', 'get_child_theme_path' ) ){ 

			h::log( 'd:>Child theme method found, so trying to save data to _config.php' );

			self::file_put_array( \q_theme::get_child_theme_path( '/_config.php' ), $array );

			return true;

		} else {

			h::log( 'e:>Child theme method NOT found, could not write _config.php' );

			return false;

		}
	
	}




	public static function option_one( $post_id ) {

		// h::log( 'post_id: '.$post_id );
		// h::log( $_POST['acf'] );

		/*
		[field_q_config_ui__open] => Array
        (
            [row-0] => Array
                (
                    [field_key] => config
                    [field_value] => Array
                        (
                            [row-0] => Array
                                (
                                    [field_sub_key] => run
                                    [field_sub_type] => boolean
                                    [field_sub_value_bool] => 1
                                )

                            [row-1] => Array
                                (
                                    [field_sub_key] => debug
                                    [field_sub_type] => boolean
                                    [field_sub_value_bool] => 1
                                )

                        )

                )

            [row-1] => Array
                (
                    [field_key] => markup
                    [field_value] => Array
                        (
                            [row-0] => Array
                                (
                                    [field_sub_key] => template
                                    [field_sub_type] => code
                                    [field_sub_value_code] => <main class=\"container {{ classes }}\">
	<div class=\"row\">
                                )

                            [row-1] => Array
                                (
                                    [field_sub_key] => wrap
                                    [field_sub_type] => code
                                    [field_sub_value_code] => <div>{{ content }}</div>
                                )

                        )

                )

        )
		*/

		// Get previous values.
		// $prev_values = get_fields( $post_id );

		// h::log( $prev_values );
	
		// Get submitted values.
		$posted = $_POST['acf'];

		// sanity ##
		if( ! $posted ){

			h::log( 'd:>No ACF fields posted' );

			return false;

		}

		// start empty ##
		$array = [];

		// loop over fields and create right data format, then save to a file ##
		foreach( $posted as $key => $value ){

			// we need to format the top level content key --- field_q_config_ui__open ##
			$context_task = str_replace( 'field_q_config_', '', $key );
			
			// check if field name can be exploded ##
			if( strpos( $context_task, '__' ) === false ){

				h::log( 'd:>key does not include "__" -> '.$context_task );

				continue;

			}

			$context_task_explode = explode( '__', $context_task );
			// h::log( $context_task_explode );

			$context = $context_task_explode[0];
			$task = $context_task_explode[1];

			// values array ##
			$values = [];

			// add context->task values from inner array ##
			if( is_array( $value ) ){

				// h::log( $value );
				/*
				[row-0] => Array (
					[field_key] => config
					[field_value] => Array
						(
							[row-0] => Array
								(
									[field_sub_key] => run
									[field_sub_type] => boolean
									[field_sub_value_bool] => 0
								)
							[row-1] => Array
								(
									[field_sub_key] => debug
									[field_sub_type] => boolean
									[field_sub_value_bool] => 0
								)
						)
				)
				*/
				foreach( $value as $inner_k => $inner_v ){

					// h::log( $inner_k );
					// h::log( $inner_v );
					
					$array_values = [];

					if ( is_array( $inner_v ) ){

						foreach( $inner_v as $inner_2_k => $inner_2_v ){

							// h::log( $inner_2_v );
							/*
							[row-0] => Array
								(
									[field_sub_key] => run
									[field_sub_type] => boolean
									[field_sub_value_boolean] => 1
								)

							[row-1] => Array
								(
									[field_sub_key] => debug
									[field_sub_type] => boolean
									[field_sub_value_boolean] => 1
								)
							*/
							if( is_array( $inner_2_v ) ){

								foreach( $inner_2_v as $inner_3_k => $inner_3_v ){

									// h::log( $inner_3_v );
									/*
									(
										[field_sub_key] => run
										[field_sub_type] => boolean
										[field_sub_value_boolean] => 1
									)
									*/

									// get key ##
									$key_3 = $inner_3_v['field_sub_key'];

									// get value ##
									switch( $inner_3_v['field_sub_type'] ){

										case "boolean" :

											$value_3 = $inner_3_v['field_sub_value_boolean'];

										break ;

										case "code" :

											$value_3 = $inner_3_v['field_sub_value_code'];

										break ;

										default :
										case "text" :

											$value_3 = $inner_3_v['field_sub_value_text'];

										break ;

									}

									$array_values[$key_3] = $value_3;

								}

							}

						}

					}

					$values[ $inner_v['field_key'] ] = $array_values;

				}

			}

			// add key and array values ##
			$array[$context][$task] = $values;

		}

		// test array ##
		// h::log( $array );

		// write to file ##
		// self::file_put_array( \Q::get_plugin_path( 'library/render/config/_config.php' ), $array );
		if ( method_exists( 'q_theme', 'get_child_theme_path' ) ){ 

			h::log( 'd:>Child theme method found, so trying to save data to _config.php' );

			self::file_put_array( \q_theme::get_child_theme_path( '/_config.php' ), $array );

			return true;

		} else {

			h::log( 'e:>Child theme method NOT found, could not write _config.php' );

			return false;

		}
	
	}


	public static function file_put_array( $path, $array )
	{

		if ( is_array( $array ) ){

			$contents = self::var_export_short( $array, true );
			// $contents = var_export( $array, true );

			// stripslashes ## .. hmmm ##
			$contents = str_replace( '\\', '', $contents );

			h::log( 'd:>Array data good, saving to file' );

			// save in php as an array, ready to return ##
			file_put_contents( $path, "<?php\n return {$contents};\n") ;
			
			// done ##
			return true;

		}

		h::log( 'e:>Error with data format, config file NOT saved' );
		
		// failed ##
		return false;

	}


	public static function var_export_short( $data, $return = true ){

		$dump = var_export($data, true);

		$dump = preg_replace('#(?:\A|\n)([ ]*)array \(#i', '[', $dump); // Starts
		$dump = preg_replace('#\n([ ]*)\),#', "\n$1],", $dump); // Ends
		$dump = preg_replace('#=> \[\n\s+\],\n#', "=> [],\n", $dump); // Empties

		if (gettype($data) == 'object') { // Deal with object states
			$dump = str_replace('__set_state(array(', '__set_state([', $dump);
			$dump = preg_replace('#\)\)$#', "])", $dump);
		} else { 
			$dump = preg_replace('#\)$#', "]", $dump);
		}

		if ($return===true) {
			return $dump;
		} else {
			echo $dump;
		}

	}


	public static function var_export( $var, $indent ="" ) {
		switch (gettype($var)) {
			case 'integer':         
			case 'double':             
				return $var;
			case "string":
				return '"' . addcslashes($var, "\\\$\"\r\n\t\v\f") . '"';
			case "array":
				$indexed = array_keys($var) === range(0, count($var) - 1);
				$r = [];
				foreach ($var as $key => $value) {
					$r[] = "$indent    "
						 . ($indexed ? "" : self::var_export54($key) . " => " )
						 . self::var_export54( $value, "$indent    " );
				}
				return "[\n" . implode(",\n", $r) . "\n" . $indent . "]";
			case "boolean":
				return $var ? "TRUE" : "FALSE";
			default:
				return var_export($var, TRUE);
		}
	}



}
