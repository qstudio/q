<?php

// Global functions added by Q, site outside of the namespace and are pluggable

// use willow\core;
use q\core\log;

/** 
 * Q API 
 *
 * @todo 
 */
if ( ! function_exists( 'q' ) ) {

	function q(){

		// sanity ##
		if(
			! class_exists( '\Q\plugin' )
		){

			error_log( 'e:>Q is not available to '.__FUNCTION__ );

			return false;

		}

		// cache ##
		$q = \q\plugin::get_instance();

		// sanity - make sure willow instance returned ##
		if( 
			is_null( $q )
			|| ! ( $q instanceof \q\plugin ) 
		) {

			// get stored Q instance from filter ##
			$q = \apply_filters( 'Q/instance', NULL );

			// sanity - make sure Q instance returned ##
			if( 
				is_null( $q )
				|| ! ( $q instanceof \q\plugin ) 
			) {

				error_log( 'Error in Q object instance returned to '.__FUNCTION__ );

				return false;

			}

		}

		// w__log( 'Q is ok..' );

		// return Q instance ## 
		return $q;

	}

}

if ( ! function_exists( 'q__log' ) ) {

	/**
     * Write to WP Error Log
     *
     * @since       1.5.0
     * @return      void
     */
	function q__log( $args = null ){

		// shift callback level, as we added another level.. ##
		\add_filter( 
			'willow/core/log/backtrace/function', function () {
			return 4;
		});
		\add_filter( 
			'willow/core/log/backtrace/file', function () {
			return 3;
		});

		// pass to core\log::set();
		return log::set( $args );

	}

}

