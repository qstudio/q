<?php

namespace q\module;

use q\core;
use q\core\helper as h;

// load it up ##
\q\module\push::run();

class push extends \Q {
    
    public static $args = [];

    public static function run()
    {

		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> str_replace( __NAMESPACE__.'\\', '', static::class ),
			'name'		=> 'Q ~ Push',
			'selected'	=> true,
		]);

		// make running dependent on module selection in Q settings ##
		if ( 
			! isset( core\option::get('module')->push )
			|| true !== core\option::get('module')->scroll 
		){

			// h::log( 'd:>push is not enabled.' );

			return false;

		}

	}
	


    /**
    * Build UI
    *
    * @since    2.0.0
    * @return   Mixed Boolean on error or HTML string
    */
    public static function render( Array $args = null )
    {

        // assign ##
        self::$args = isset( $args ) ? (array) $args: [] ;

        #helper::log( self::$args );

        // check if we have any elements to scroll over ##
        if ( 
            ! isset( self::$args['target'] )
        ) {

            helper::log( 'No target' );

            return false;

        }

        // compile markup ##
        $markup = str_replace( '{{ target }}', $args['target'], $args['markup'] );

        // echo ##
        echo $markup;

    }

}
