<?php

namespace q\view;

use q\core;
use q\core\helper as h;
// use q\core\options as options;

// load it up ##
// \q\view\method::run();

class is {


    /**
     * Get Q template name, if set - else return
     * 
     * 
     */
    public static function get() 
    {

        if( ! isset( $GLOBALS['q_template'] ) ) {

            // h::log( 'Page template empty' );
            
			// return false;
			
			// changes to return WP template -- check for introduced issues ##
			return str_replace( '.php', '', \get_page_template_slug() );

        } else {

            // h::log( 'Page template: '.$GLOBALS['q_template'] );

            return str_replace( '.php', '', $GLOBALS['q_template'] );        

        }

	}

	

	/**
	 * Check is the current view matches the controller
	 * 
	 * @since 4.0.0
	*/
	public static function showing( $file = null ): bool {

		// h::log( 'd:>temp: '.view\is::get() );
		// h::log( 'd:>file: '.$file  );

		return self::get() == trim( $file ) ;

	}



}
