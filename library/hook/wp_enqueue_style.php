<?php

/**
 * Actions to call on wp_footer() hook
 *
 * @since       1.0
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 */

namespace q\hook;

use q\plugin as q;
use q\core;
use q\core\helper as h;

class wp_enqueue_style {

    function __construct(){}
	
	function hooks() {

		// not in the admin ##
        if ( ! \is_admin() ) { 

            \add_filter( 'style_loader_tag', [ $this, 'style_loader_tag' ], 0, 4 );

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
	function style_loader_tag( $html, $handle, $href, $media ) {

		// h::log( $html );
		// h::log( $tag );
		// h::log( $href );
		// h::log( $handle );

		$avoid = [];

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
