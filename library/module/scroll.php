<?php

namespace q\module;

use q\core;
use q\core\helper as h;

// load it up ##
\q\module\scroll::__run();

class scroll extends \Q {
    
    public static $args = [];

    public static function __run()
    {

		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> str_replace( __NAMESPACE__.'\\', '', static::class ),
			'name'		=> 'Q ~ Scroll',
			'selected'	=> true,
		]);

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('tab') );
		if ( 
			! isset( core\option::get('module')->scroll )
			|| true !== core\option::get('module')->scroll 
		){

			// h::log( 'd:>scroll is not enabled.' );

			return false;

		}

    }




    /**
    * Build scroll UI navigation
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
            ! isset( self::$args['elements'] )
            || empty( array_filter( self::$args['elements'] ) ) 
        ) {

            helper::log( 'No elements' );

            return false;

        }

        // add navigation ##
        echo self::navigation();

    }



    /**
    * Markup for navigation
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function navigation()
    {

        // get markup model ##
        $markup = self::$args['markup'];

        // empty rows ##
        $rows = '';

        // loop over paassed rows adding details ##
        foreach( self::$args['elements'] as $key => $value ){

            $row = [];
            $row['slug'] = $key;
            $row['title'] = $value;

            $rows .= markup::apply( self::$args['markup_row'], $row );

        }

        // compile markup ##
        $markup = str_replace( '%rows%', $rows, $markup );

        // return ##
        return $markup;

    }

}
