<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\ui;
use q\get;
use q\render;

class args extends \q\render {
	
    public static function validate( $args = null, $process = null ) {

		// h::log( core\method::backtrace([ 'level' => 2, 'return' => 'function' ]) );
		// h::log( $args );

		// get stored config - pulls from Q, but available to filter via q/config/get/all ##
		// @todo -- core\config::load( $args['config']['load'] ); -- checks for template specific config + ui\template::get();
		h::log( 't:>add group__NAME config settings to share config over templates...' );
		$config = 
			( // force config settings to return by passing "config" -> "property" ##
				isset( $args['config'] ) 
				&& isset( $args['config']['load'] ) 
				&& core\config::get( $args['config']['load'] ) 
			) ?
			core\config::get( $args['config']['load'] ) :
			// core\config::get( core\method::backtrace([ 'level' => 4, 'return' => 'function' ]) ) ; // get config based on calling function ##
			$process ;

		// test ##
		// h::log( $config );
		// h::log( 'd:>method: '.core\method::backtrace([ 'level' => 4, 'return' => 'function' ]) );

		// Parse incoming $args into an array and merge it with $config defaults ##
		// allows specific calling methods to alter passed $args ##
		// if ( $config ) $args = \wp_parse_args( $args, $config );
		if ( $config ) $args = core\method::parse_args( $args, $config );

		// h::log( $args );

        // checks on required fields in $args array ##
        if (
			// ! isset( $args )
			is_null( $args )
            || ! is_array( $args )
            // || ! isset( $args['fields'] )
            // || ! is_array( $args['fields'] )
            // || ! isset( $args['group'] ) // @todo --- this is specific to the_group calls ##
            // || ! isset( $args['markup'] )
            // || ! is_array( $args['markup'] )
            // || ! isset( $args['markup']['template'] )
        ){

			// log ##
			h::log( self::$args['process'].'~>e:>Missing required args, so stopping here' );
			// h::log( 'Kicked here...' );

            return false;

		}

		// assign "group" - this is used by group to pull acf fields, or to know the calling method for the_ calls ##
		// $args['group'] = isset( $args['process'] ) ? $args['process'] : core\method::backtrace([ 'level' => 2, 'return' => 'function' ]) ;
		$args['group'] = $process;

		// h::log( $args['config']['post'] );

		// If posts is passed as an int, then get a matching post Object, as we can use the data later ## 

		// validate passed post ##
		if ( 
			isset( $args['config']['post'] ) 
			// is_int( $args['config']['post'] )
			&& ! $args['config']['post'] instanceof \WP_Post
		) {

			// get new post, if corrupt ##
			$args['config']['post'] = get\post::object( $args['config'] );

			// h::log( 'Post set, but not an Object.. so getting again..: '.$args['config']['post']->ID );

		}

		// no post set ##
		if ( ! isset( $args['config']['post'] ) ) {

			$args['config']['post'] = get\post::object();

			// h::log( 'No post set, so getting: '.$args['config']['post']->ID );

		}

		// last check ##
		if ( ! isset( $args['config']['post'] ) ) {

			// h::log( 'Error with post object, validate - returned as null.' );

			$args['config']['post'] = null;

			// return false;

		}

		// h::log( $args['config']['post']->ID );

		// assign "group" - this is used by group to pull acf fields, or to know the calling method for the_ calls ##
		// $args['group'] = isset( $args['group'] ) ? $args['group'] : core\method::backtrace([ 'return' => 'function' ]) ;
		
        // assign properties with initial filters ##
		$args = self::assign( $args );
		
		// h::log( $args );

        // check if module asked to run $args['config']['run']
        if ( 
            // isset( $args['config']['run'] )
            // && 
            false === $args['config']['run']
        ){

			// log ##
			h::log( self::$args['group'].'~>n:>config->run defined as false for: '.$args['group'].', so stopping here.. ' );

            return false;

        }

        // ok - should be good ##
        return $args;

    }




    /**
     * Assign class properties with initial filters, merging in passed $args from calling method
     */
    public static function assign( Array $args = null ) {

        // apply global filter to $args - specific calls should be controlled by parameters included directly ##
        self::$args = core\filter::apply([
			'filter'        => 'q/render/args',
			'parameters'    => self::$args,
			'return'        => self::$args
		]);
		
		// apply template level filter to $args - specific calls should be controlled by parameters included directly ##
        self::$args = core\filter::apply([
			'filter'        => 'q/render/args/'.ui\template::get(),
			'parameters'    => self::$args,
			'return'        => self::$args
        ]);

        // grab all passed args and merge with defaults ##
        $args = core\method::parse_args( $args, self::$args_default );
		
		// assign class property ##
		self::$args = $args;

        // test ##
        // h::log( $args );

		// if no markup sent.. ##
		if ( 
			! isset( $args['markup'] )
			&& is_array( $args ) 
		) {

			// default -- almost useless - but works for single values.. ##
			$args['markup'] = '%value%';

			foreach( $args as $k => $v ) {

				if ( is_string( $v ) ) {

					// take first string value in $args markup ##
					$args['markup'] = $v;

					break;

				}

			}

		}

        // assign markup ##
		self::$markup = $args['markup'];
		
        // return args for validation ##
        return $args;

    }




	
    public static function is_enabled()
    {

        // sanity ##
        if ( 
            ! self::$args 
            || ! is_array( self::$args )
        ) {

			// log ##
			h::log( self::$args['group'].'~>e:>Error in passed self::$args');

            return false;

        }

        // helper::log( self::$fields );
        // helper::log( 'We are looking for field: '.self::$args['enable'] );

        // check for enabled flag - if none, return true ##
        // we also take one guess at the field name -- if it's not passed in config ##
        if ( 
            ! isset( self::$args['enable'] )
            && ! isset( self::$fields[self::$args['group'].'_enable'] )
        ) {

			// log ##
			h::log( self::$args['group'].'~>n:>No enable defined in $args or enable field found for Group: "'.self::$args['group'].'"');

            return true;

        }

        // kick back ##
        if ( 
            (
                isset( self::$args['enable'] )
                && 1 == self::$fields[self::$args['enable']]
            )
            || 
            	1 == self::$fields[self::$args['group'].'_enable']
        ) {

			// log ##
			h::log( self::$args['group'].'~>n:>Field Group: "'.self::$args['group'].'" Enabled, continue');

            // helper::log( self::$args['enable'] .' == 1' );

            return true;

        }

		// log ##
		h::log( self::$args['group'].'~>n:>Field Group: "'.self::$args['group'].'" NOT Enabled, stopping.');

        // helper::log( self::$args['enable'] .' != 1' );

        // negative ##
        return false;

    }



	/**
	 * Empty all render args
	 * 
	 * @since 4.0.0
	 */ 
	public static function reset(){

		// h::log( 'd:>reset args for: '.self::$args['group'] );

		self::$args = [];

		return true;

	}

     
}
