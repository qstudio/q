<?php

namespace q\render;

use q\core\helper as h;
use q\ui;
use q\get;
use q\render;

class media extends \q\render {

	/** MAGIC */
	public static function __callStatic( $function, $args ) {

        return self::run( $args ); 
	
	}

	public static function run( $args = null ){

		// run method to populate field data ##
		$method = $args['task'];
		$extension = render\extension::get( $args['context'], $args['task'] );

		if (
			! \method_exists( get_class(), $method ) // && exists ##
			&& ! $extension // look for extensions ##
		) {

			render\log::set( $args );

			h::log( 'e:>Cannot locate method: '.__CLASS__.'::'.$method );

            return false;

		}

        // validate passed args ##
        if ( ! render\args::validate( $args ) ) {

			render\log::set( $args );
			
			// h::log( 'd:>Bunked here..' );

            return false;

		}

		// base class ##
		if ( 
			\method_exists( get_class(), $method ) 
		){

			// 	h::log( 'load base method: '.$extension['class'].'::'.$extension['method'] );

			// call render method ##
			self::{ $method }( self::$args );

		// extended class ##
		} elseif (
			$extension
		){

			// 	h::log( 'load extended method: '.$extension['class'].'::'.$extension['method'] );

			// h::log( 'd:>render extension..' );
			$extension['class']::{ $extension['method'] }( self::$args );

		}

		// Now we can loop over each field ---
		// running callbacks ##
		// formatting none string types to strings ##
		// removing placeholders in markup, if no field data found etc ##
		render\fields::prepare();
		
		// h::log( self::$fields );

        // Prepare template markup ##
        render\markup::prepare();

        // optional logging to show removals and stats ##
        render\log::set( $args );

        // return or echo ##
        return render\output::return();

	}
	


	// ---------- methods ##


	/**
     * Src image - this requires a post_id / attachment_id to be bassed ##
     *
     * @param       Array       $args
     * @since       1.3.0
     * @return      String
     */
    public static function src( $args = null ) {

		// returns array with key 'src', 'srcset', 'alt' etc.... ##
		render\fields::define(
			get\media::src( $args )
		);

	}


	/**
     * lookup thumbnail image, this implies we are working with the current post
     *
     * @param       Array       $args
     * @since       1.3.0
     * @return      String
     */
    public static function thumbnail( $args = null ) {

		// h::log( self::$args );

		// returns array with key 'src', 'srcset', 'alt' etc.... ##
		render\fields::define(
			get\media::thumbnail( $args )
		);

	}
	


	/**
     * Get page Avatar style and placement
     *
     * @since       1.0.1
     * @return      Mixed       string HTML || Boolean false
     */
    public static function avatar( $args = array() )
    {

		/*
        // grab avatar object ##
        // if ( ! $object = self::get_avatar( $args ) ) { return false; }

		// <a class="circle <?php echo $object->class; ?>"><img src="<?php echo $object->src; ?>" /></a>
		*/
		
		// returns array with key 'src', 'srcset', 'alt' etc.... ##
		render\fields::define(
			get\media::avatar( $args )
		);

    }


}
