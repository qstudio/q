<?php

/**
 * Actions to call on wp_footer() hook
 *
 * @since       1.0
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 */

namespace q\hook;

use q\core;
use q\core\helper as h;

// load it up ##
\q\hook\wp_enqueue_style::run();

class wp_enqueue_style extends \Q {

    public static function run()
    {

		// not in the admin ##
        if ( ! \is_admin() ) { 

            \add_filter( 'style_loader_tag', [ get_class(), 'style_loader_tag' ], 0, 4 );

        }

    }


    /**
	* Add async or defer attributes to script enqueues
	* @author Mike Kormendy
	* @param  String  $tag     The original enqueued <script src="...> tag
	* @param  String  $handle  The registered unique name of the script
	* @return String  $tag     The modified <script async|defer src="...> tag
	*/
	// only on the front-end
	public static function style_loader_tag( $html, $handle, $href, $media ) {

		// h::log( $html );
		// h::log( $tag );
		
		// route two - exclude files based on handle match ##
		$avoid = [];

		// filter $avoid ##
		$avoid = \apply_filters( 'q/hook/wp_enqueue_style/style_loader_tag/avoid', $avoid );

		// h::log( $avoid );

		// h::log( $handle );

		if (
			in_array( $handle, $avoid )
		){

			// h::log( 'Not deferring load of style: '.$handle );

			return $html;

		}

		// check for passed query vars ##
		$parts = parse_url($href);
		parse_str( $parts['query'], $query );
		$version = isset( $query['ver'] ) ? '?ver='.$query['ver'] : '' ;

		// route one - include all files based on explicit usage of "__preload" in $html - normally appended to src url ##
		// if ( strpos( $html, '__preload') !== false ) {

			$html = '<link rel="preload"  media="'.$media.'" id="'.$handle.'" href="'.$href.$version.'" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">
			<noscript><link rel="stylesheet" id="'.$handle.'" href="'.$href.$version.'" type="text/css" media="'.$media.'" /></noscript>
			';

			// h::log( $html );

		// }

		// no change ##
		return $html;

	}

}
