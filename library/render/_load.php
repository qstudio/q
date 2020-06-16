<?php

namespace q;

use q\core;
use q\core\helper as h;
use q\render;

/*

------- One rendering Engine for all use cases ----------

Requirements:
# pass standerdized args ( markup, callbacks, filter ets )
# log on global or call specific basis
# manipulate markup and data to return at once 
# option to return pure data or marked-up data ( render/ or get/ ) 
# friendly from templates etc -- render\the_title();
@ pass fake data ( fields = [ 'the_title', 'Hello' ] ) - skips data fetching for field?? all??
# __magic function finder _find()

Modules:
# group - render field data from ACF field group
# the_ || wordpress - the_posts, the_title etc.. include getters and render methods
# field ( deal with single use case post_fields )
~ type ( src, video, gallery?? etc )

filters:
# validate args ( check requirements, filter.. )
# callbacks ( either via config or args -- defined list, any other render method, wp functions, openhouse.. ?? )
# pre process ( filter config, filter fields )
# format return ( filter args, filter data, apply markup.. filter again.. )
@ post-process ( js callbacks, srcset etc.. )

*/

// load it up ##
\q\render::run();

class render extends \Q {

	protected static

        // passed args ##
        $args = [
			'fields'	=> []
		],
		
		// default args to merge with passed array ##
        $args_default = [
            'config'            => [
                'run'           => true, // don't run this item ##
                'debug'         => false, // don't debug this item ##
				'return'        => 'echo', // default to echo return string ##
            ],
            // 'src'        		=> [
            //     'srcset' 		=> true, // add srcset to src references ##
			// 	'picture' 		=> true // wrap src in 'picture' element, with srcset ##
            // ]      
        ],

        // frontend pre-processor callbacks to update field values ##
        $callbacks = [
            'get_posts'         => [ // standard WP get_posts()
                'namespace'     => 'global', // global scope to allow for namespacing ##
                'method'        => '\get_posts()',
                'args'          => [] // default - can be edited via global and specific filters ##
            ],
        ],

        // value formatters ##
        $format = [
            // Arrays could be collection of WP Post Objects OR repeater block - so check ##
            'array'             => [
                'type'          => 'is_array',
                'method'        => 'format_array'
            ],
            'post_object'       => [
                'type'          => 'is_object',
                'method'        => 'format_object'
            ],
            'integer'           => [
                'type'          => 'is_int',
                'method'        => 'format_integer'
            ],
            'string'            => [
                'type'          => 'is_string',
                'method'        => 'format_text',
            ],
		],
		

		// allowed field types ##
        $type = [
			'repeater'       	=> [],
			'post'       		=> [],
			'src'             	=> [],
			'category'       	=> [],
			'taxonomy'       	=> [],
			'media'       		=> [],
			'author'       		=> [],
        ],

        // standard fields to add to wp_post objects
        $type_fields = [

			// standard WP fields ##
            'post_ID',
            'post_title',
            'post_content',
            'post_excerpt',
			'post_permalink',
			'post_is_sticky',
			
			// dates ##
			'post_date', // formatted ##
			'post_date_human', // human readable ##
			
			// category ##
			'category_name', 
			'category_permalink',
			
			// author ##
			'author_permalink',
			'author_name',
			
			// image src ##
			'src', 
        ],

        $output = null, // return string ##
        $fields = null, // field names and values ##
        $markup = null, // store local version of passed markup ##
        $acf_fields = null // fields grabbed by acf function ##

	;
	
	public static $log = null; // tracking array for feedback ##

	public static function run(){

		core\load::libraries( self::load() );

	}


    /**
    * Load Libraries
    *
    * @since        2.0.0
    */
    public static function load()
    {

		return $array = [
			
			// validate and assign args ##
			'args' => h::get( 'render/args.php', 'return', 'path' ),

			// check callbacks on defined fields ## 
			// @todo - allow to be passed from calling method ['callback' => 'srcset' ] etc ##
			'callback' => h::get( 'render/callback.php', 'return', 'path' ),

			// get field data ##
			'get' => h::get( 'render/get.php', 'return', 'path' ), 

			// prepare and manipulate field data ##
			'fields' => h::get( 'render/fields.php', 'return', 'path' ), 

			// check format of each fields data and modify as required to markup ##
			'format' => h::get( 'render/format.php', 'return', 'path' ),

			// defined field types to generate field data ##
			'type' => h::get( 'render/type/_load.php', 'return', 'path' ),

			// prepare defined markup, search for and replace placeholders 
			'markup' => h::get( 'render/markup.php', 'return', 'path' ),

			// output string ##
			'output' => h::get( 'render/output.php', 'return', 'path' ),

			// log activity ##
			'log' => h::get( 'render/log.php', 'return', 'path' ),

			// render methods ##

			// acf field handler ##
			'field' => h::get( 'render/method/field.php', 'return', 'path' ), 

			// acf group ##
			'group' => h::get( 'render/method/group.php', 'return', 'path' ),

			// post objects content, title, excerpt etc ##
			'post' => h::get( 'render/method/post.php', 'return', 'path' ),

			// navigation features ##
			// 'nav' => h::get( 'render/method/navigation.php', 'return', 'path' ),

			// ui render methods - open, close.. etc ##
			'ui' => h::get( 'render/method/ui.php', 'return', 'path' ),

			// block renders, such as post_meta ##
			// 'block' => h::get( 'render/method/block.php', 'return', 'path' ),

		];

	}
	
	/** 
	 * bounce to function getter ##
	 * function name can be any of the following patterns:
	 * 
	 * group__
	 * block__
	 * field__
	 * content, title, excerpt etc..
	 * type__ ?? needed ??
	 */
	public static function __callStatic( $function, $args ){	

		// $sdfsdf = $that;

		// take first array item, unwrap array - __callStatic wraps the array in an array ##
		if ( is_array( $args ) && isset( $args[0] ) ) { 
			
			// h::log('Taking the first array item..');
			$args = $args[0];
		
		}

		// check class__method is formatted correctly ##
		if ( 
			false === strpos( $function, '__' )
		){

			h::log( 'e:>Error in passed render method: "'.$function.'" - should have format CLASS__MOETHOD' );

			return false;

		}	

		// we expect all render methods to have standard format CLASS__METHOD ##	
		list( $class, $method ) = explode( '__', $function );

		// define config to load, if not passed ##
		// if (
		// 	! isset( $args['config']['load'] )
		// ){

		// 	// h::log( 'd:>do we need to force the config??' );
		// 	// $args['config']['load'] = $class.'_'.$method;

		// }

		// h::log( 'd:>search if -- class: '.$class.'::'.$method.' available' );

		// look for matching method to $function ##
		$namespace = __NAMESPACE__."\\render\\".$class;
		// render\ui::run();
		// h::log( 'd:>'.__NAMESPACE__ .' --- '.$namespace );
		if (
			class_exists( $namespace ) // && exists ##
			// && is_callable([ $namespace, $method ]) // && is callable ##
		) {

			// h::log( 'd:>class: '.$class.'::'.$method.' available' );

			// @todo - we need to make this more generic ##
			// $args['group'] = $method;
			// $args['field'] = $method;
			// $args['method'] = $method; // @todo --- use to control group, field, block .... etc ##
			
			// define config for all in class -- i.e "group" ##
			//@todo - could add group__NAME config, to share over templates ???
			// h::log( 't:>add group__NAME config settings to share config over templates...' );
			$args['config']['load'] = $class;

			// define config for post, block, type, ui ##
			if(
				'post' == $class 
				|| 'block' == $class
				|| 'type' == $class
				|| 'ui' == $class
			){ 

				// h::log( 'd:>config for class: '.$class.' set to: '.$class.'_'.$method );

				$args['config']['load'] = $class.'_'.$method;

			} else {

				//

			}

			// set global proces tracker ##
			$args['process'] = $method;

			// call render method ##
			// return render\ui::open( $args );
			return $namespace::run( $args, $method );

		}

		// return;
		/*
		// test namespace ##
		// h::log( __NAMESPACE__ );

		// group_ methods ##
		if ( 
			'group__' === substr( $function, 0, 7 ) 
			|| 'group' === substr( $function, 0, 5 ) 
		) {

			// take the second part of the function name as the group value ##
			if ( false !== strpos( $function, '__' ) ) {
				
				$args['group'] = str_replace( 'group__', '', $function );

				// note ##
				// h::log('d:>Group assigned: '.$args['group']);

			}

			// call it ##
			return render\group::run( $args );

		}


		// single field method ##
		if ( 
			'field__' === substr( $function, 0, 7 ) 
			|| 'field' === substr( $function, 0, 5 ) 
		) {

			// take the second part of the function name as the group value ##
			if ( false !== strpos( $function, '__' ) ) {
				
				$args['group'] = str_replace( 'field__', '', $function );
				$args['field'] = str_replace( 'field__', '', $function );

				// note ##
				// h::log('d:>Field assigned: '.$args['field']);

			}

			// call it ##
			return render\field::run( $args );

		}

		// block_ methods ##
		// allows to pass markup and ui method names - 
		// render\block__post_meta([ 'You are viewing post {title} in {category_name} from {post_data_human} ]);
		if ( 
			'block__' === substr( $function, 0, 7 ) 
			|| 'block' === substr( $function, 0, 5 ) 
		) {

			// take the second part of the function name as the group value ##
			if ( false !== strpos( $function, '__' ) ) {
				
				$args['group'] = str_replace( 'block__', '', $function );
				$args['block'] = str_replace( 'block__', '', $function );

				// note ##
				h::log('d:>BLock assigned: '.$args['block']);

			}

			// call it ##
			return render\block::run( $args );

		}


		// type_ methods ##
		// allows to render\type__src( $args );
		if ( 
			'type__' === substr( $function, 0, 5 ) 
			|| 'type' === substr( $function, 0, 3 ) 
		) {

			// take the second part of the function name as the group value ##
			if ( false !== strpos( $function, '__' ) ) {
				
				$args['group'] = str_replace( 'type__', '', $function );
				$args['type'] = str_replace( 'type__', '', $function );

				// note ##
				h::log('d:>Type assigned: '.$args['type']);

			}

			// call it ##
			return render\type::run( $args );

		}

		// look for matching method to $function ##
		if (
			\method_exists( __NAMESPACE__.'\render\ui', $function ) // && exists ##
			&& \is_callable([ __NAMESPACE__.'\render\ui', $function ]) // && is callable ##
		) {

			// h::log( 'Found function: "q\ui\render\method::'.$function.'()"' );

			// assign config to load ##
			$args['group'] = $function;
			$args['config']['load'] = $function;

			// call it ##
			return render\ui::run( $args, $function );
			// return render\method::{$function}( $args );

		}
		*/

		// @todo -- check what is going on when this log shows.. ##
		h::log( 'e:>No matching method found for: '.$class.'::'.$method );

		// kick back nada - as this renders on the UI ##
		return false;

	}


}
