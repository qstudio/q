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
	* Defer style asset enqueuing
	* 
	* @param  String  $tag     The original enqueued <link rel="...> tag
	* @param  String  $handle  The registered unique name of the script
	* @return String  $tag     The modified <link rel="...> tag
	*/
	// only on the front-end
	public static function style_loader_tag( $html, $handle, $href, $media ) {

		// h::log( $html );
		// h::log( $tag );
		// h::log( $href );
		// h::log( $handle );

		// exclude files based on handle match -- controlled by passed filter ##
		$avoid = \apply_filters( 'q/hook/wp_enqueue_style/style_loader_tag/avoid', $avoid );

		// h::log( $avoid );

		if (
			in_array( $handle, $avoid )
		){

			h::log( 'Not deferring load of style: '.$handle );

			return $html;

		}

		// skip asset via "__nodefer" in $html - normally appended to src url i.e. 'src.css?__nodefer' ##
		if ( strpos( $html, '__nodefer') !== false ) {

			h::log( 'Not deferring load of style: '.$handle );

			return $html;

		}

		// check for passed query vars ##
		$parts = parse_url(	$href );
		parse_str( $parts['query'], $query );

		// get version fragment ##
		$version = isset( $query['ver'] ) ? '?ver='.$query['ver'] : '' ;

		// link to "media = 'null'" '.$media.' - onload swap to defined media type ##
		$html = '<link rel="stylesheet" id="'.$handle.'" href="'.$href.$version.'" media="null" onload=\'if( media == "null" ) media="'.$media.'"\'>';

		// add no JS backup ##
		$html .= '<noscript><link rel="stylesheet" id="'.$handle.'" href="'.$href.$version.'" type="text/css" media="'.$media.'" /></noscript>';

		// return html ##
		return $html;

	}

}
