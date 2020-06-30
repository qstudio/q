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
# post - wordpress posts, title etc.. include getters and render methods
# ui - layout elements
# field ( deal with single use case post_fields )
~ partial - reusable blocks or code, like buttons or post_meta items
~ media ( src, video, gallery etc )

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

	public static

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
			'category'       	=> [],
			'taxonomy'       	=> [],
			'src'             	=> [], // @todo... this is too specific ##
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
			'src', // @todo.. needs to merge into media ##
			// 'media', 

		],
		
		/* define template delimiters */
		// based on Mustache, but not the same... https://github.com/bobthecow/mustache.php/wiki/Mustache-Tags
		$tags = [

			// variables ##
			'variable'		=> [
				'open' 		=> '{{ ', // open ## 
				'close' 	=> ' }}', // close ##
			],

			// parameters / arguments ##
			'parameter'		=> [
				'open' 		=> '[{ ', // open ## 
				'close' 	=> ' }]', // close ##
			],
			
			// section ##
			'section'		=> [
				'open' 		=> '{{# ', // open ##
				'close' 	=> ' }}', // close ##
				'end'		=> '{{/#}}' // end statement ##
			],

			// inversion ##  // else, no results ##
			'inversion'		=> [
				'open'		=> '{{^ ',
				'open'		=> ' }}', 
				'end'		=> '{{/}}'
			],

			// function -- also, an unescaped variable -- @todo --- ##
			'f/o'	=> '{{{ ', 
			'f/c'	=> ' }}}', // also, a variable without escaping ##

			// partial ##
			'p/o'	=> '{{> ', // partial open ##
			'p/c'	=> ' }}', // partial close ##

			// comment ##
			'c/o'	=> '{{! ', // comment open ##
			'c/c'	=> ' }}', // comment close ##

		],

        $output = null, // return string ##
        $fields = null, // array of field names and values ##
        $markup = null, // array to store passed markup and extra keys added by formatting ##
		$extend = [] // allow apps to extend render methods ##

	;
	
	public static $log = null; // tracking array for feedback ##

	/**
	 * Fire things up
	*/
	public static function run(){

		// load libraries ##
		core\load::libraries( self::load() );

	}


    /**
    * Load Libraries
    *
    * @since        4.1.0
    */
    public static function load()
    {

		return $array = [

			// tag management ##
			'tag' => h::get( 'render/tag.php', 'return', 'path' ),

			// methods ##
			'method' => h::get( 'render/method.php', 'return', 'path' ),
			
			// validate and assign args ##
			'args' => h::get( 'render/args.php', 'return', 'path' ),

			// class extensions ##
			'extend' => h::get( 'render/extend.php', 'return', 'path' ),

			// check callbacks on defined fields ## 
			// @todo - allow to be passed from calling method ['callback' => 'srcset' ] etc ##
			'callback' => h::get( 'render/callback.php', 'return', 'path' ),

			// get field data ##
			// 'get' => h::get( 'render/get.php', 'return', 'path' ), 

			// prepare and manipulate field data ##
			'fields' => h::get( 'render/fields.php', 'return', 'path' ), 

			// check format of each fields data and modify as required to markup ##
			'format' => h::get( 'render/format.php', 'return', 'path' ),

			// defined field types to generate field data ##
			'type' => h::get( 'render/type/_load.php', 'return', 'path' ),

			// prepare defined markup, search for and replace placeholders 
			'markup' => h::get( 'render/markup.php', 'return', 'path' ),

			// manage placeholders in markup object ## 
			'placeholder' => h::get( 'render/placeholder.php', 'return', 'path' ),

			// output string ##
			'output' => h::get( 'render/output.php', 'return', 'path' ),

			// log activity ##
			'log' => h::get( 'render/log.php', 'return', 'path' ),

			// context classes ##
			'context' => h::get( 'render/context/_load.php', 'return', 'path' ),

			// context classes ##
			/*

			// acf field handler ##
			'field' => h::get( 'render/context/field.php', 'return', 'path' ), 

			// acf field groups ##
			'group' => h::get( 'render/context/group.php', 'return', 'path' ),

			// post objects content, title, excerpt etc ##
			'post' => h::get( 'render/context/post.php', 'return', 'path' ),

			// navigation items ##
			'navigation' => h::get( 'render/context/navigation.php', 'return', 'path' ),

			// media items ##
			'media' => h::get( 'render/context/media.php', 'return', 'path' ),

			// taxonomies ##
			'taxonomy' => h::get( 'render/context/taxonomy.php', 'return', 'path' ),

			// ui render methods - open, close.. etc ##
			'ui' => h::get( 'render/context/ui.php', 'return', 'path' ),

			// block renders, such as post_meta ##
			// 'block' => h::get( 'render/context/block.php', 'return', 'path' ),

			// perhaps type css ##
			// perhaps type js ##
			// perhaps type font ##
			*/

		];

	}
	



	/** 
	 * bounce to function getter ##
	 * function name can be any of the following patterns:
	 * 
	 * group__  acf field group
	 * block__  ??
	 * field__  single post meta field ( can be any type, such as repeater )
	 * partial__  snippets, code, blocks, collections like post_meta
	 * post__  content, title, excerpt etc..
	 * media__
	 * navigation__ 
	 * taxonomy__
	 * ui__
	 */
	public static function __callStatic( $function, $args ){	

		// check class__method is formatted correctly ##
		if ( 
			false === strpos( $function, '__' )
		){

			h::log( 'e:>Error in passed render method: "'.$function.'" - should have format CLASS__METHOD' );

			return false;

		}	

		// we expect all render methods to have standard format CLASS__METHOD ##	
		list( $class, $method ) = explode( '__', $function );

		// sanity ##
		if ( 
			! $class
			|| ! $method
		){
		
			h::log( 'e:>Error in passed render method: "'.$function.'" - should have format CLASS__METHOD' );

			return false;

		}

		// h::log( 'd:>search if -- class: '.$class.'::'.$method.' available' );

		// look for "namespace/render/CLASS" ##
		$namespace = __NAMESPACE__."\\render\\".$class;
		// h::log( 'd:>namespace --- '.$namespace );

		if (
			class_exists( $namespace ) // && exists ##
		) {

			// h::log( 'd:>class: '.$namespace.' available' );

			// take first array item, unwrap array - __callStatic wraps the array in an array ##
			if ( is_array( $args ) && isset( $args[0] ) ) { 
				
				// h::log('Taking the first array item..');
				$args = $args[0];
			
			}

			// extrac markup from passed args ##
			render\markup::pre_validate( $args );

			// make args an array, if it's not ##
			if ( ! is_array( $args ) ){
			
				// h::log( 'Caste $args to array' );

				$args = [];
			
			}

			// define context for all in class -- i.e "post" ##
			$args['context'] = $class;

			// set task tracker -- i.e "title" ##
			$args['task'] = $method;

			if (
				! \method_exists( $namespace, 'get' ) // base method is get() ##
				&& ! \method_exists( $namespace, $args['task'] ) ##
				&& ! render\extend::get( $args['context'], $args['task'] ) // look for extends ##
			) {
	
				render\log::set( $args );
	
				h::log( 'e:>Cannot locate method: '.$namespace.'::'.$args['task'] );
	
				return false;
	
			}
	
			// validate passed args ##
			if ( ! render\args::validate( $args ) ) {
	
				render\log::set( $args );
				
				h::log( 'e:>Args validation failed' );
	
				return false;
	
			}

			// call class::method to gather data ##
			// return render\ui::open( $args );
			// $namespace::run( $args );

			if (
				$extend = render\extend::get( $args['context'], $args['task'] )
			){

				// 	h::log( 'load extended method: '.$extend['class'].'::'.$extend['method'] );

				// gather field data from extend ##
				$extend['class']::{ $extend['method'] }( self::$args );

			} else if ( 
				\method_exists( $namespace, $args['task'] ) 
			){

				// 	h::log( 'load base method: '.$extend['class'].'::'.$extend['method'] );

				// gather field data from $method ##
				$namespace::{ $args['task'] }( self::$args );

			} else if ( 
				\method_exists( $namespace, 'get' ) 
			){

				// 	h::log( 'load default get() method: '.$extend['class'].'::'.$extend['method'] );

				// gather field data from get() ##
				$namespace::get( self::$args );

			} else {

				// oddly, no matching class::method found, so stop ##

				render\log::set( $args );
				
				h::log( 'e:>No matching class::method found' );
	
				return false;

			}

			// prepare field data ##
			render\fields::prepare();

			// check if feature is enabled ##
			if ( ! render\args::is_enabled() ) {

				render\log::set( $args );

				h::log( 'd:>Not enabled...' );
	
				return false;
	
		   }    
		
			// h::log( self::$fields );

			// Prepare template markup ##
			render\markup::prepare();

			// optional logging to show removals and stats ##
			render\log::set( $args );

			// return or echo ##
			return render\output::return();

		}

		// nothing matched, so report and return false ##
		h::log( 'e:>No matching render context for: '.$namespace );

		// optional clean up.. how do we know what to clean ?? ##
		// @todo -- add shutdown cleanup, so remove all lost pieces ##

		// kick back nada - as this renders on the UI ##
		return false;

	}


}
