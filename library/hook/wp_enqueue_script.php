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
\q\hook\wp_enqueue_script::run();

class wp_enqueue_script extends \Q {

    public static function run()
    {

		// not in the admin ##
        if ( ! \is_admin() ) { 

            \add_filter( 'script_loader_tag', [ get_class(), 'script_loader_tag' ], 0, 3 );

        }

    }


    /**
	* Add async or defer attributes to script enqueues
	* @param  String  $tag     The original enqueued <script src="...> tag
	* @param  String  $handle  The registered unique name of the script
	* @return String  $tag     The modified <script async|defer src="...> tag
	*/
	// only on the front-end
	public static function script_loader_tag( $tag, $handle, $src ) {

		// route two - exclude files based on handle match ##
		$avoid = [
			'underscore', // _underscore ##
			'backbone', // backbone ##
			'jquery-core', // main js ##
			// 'jquery-ui-core',
			// 'jquery-migrate', // migrate ##
			// 'wp-dom-ready', // obvious..
			'wp-i18n', // internationalizations ##
			'wp-tinymce-root', // tinymce root ##
			'wp-tinymce', // tinymce ##
			'editor', // wp editor ##
			// 'wp-embed', // embed
			// 'wp-a11y',
			// 'wplink'
		];

		// filter $avoid ##
		$avoid = \apply_filters( 'q/hook/wp_enqueue_script/script_loader_tag/avoid', $avoid );

		// h::log( $avoid );

		// h::log( $tag );
		// h::log( $handle );

		if (
			in_array( $handle, $avoid )
			|| strpos( $tag, '__nodefer' ) !== false
		){

			// h::log( 'Not deferring load of script: '.$handle );

			return $tag;

		}

		// track changes ##
		$param = '';

		// route one - include all files based on explicit usage of "__js_async" OR "__js_defer" in $tag - normally appended to src url ##

		// if the unique handle/name of the registered script has 'async' in it
		if ( strpos( $tag, '__js_async') !== false ) {

			// return the tag with the async attribute
			$param = 'async ';

		}

		// return the tag with the defer attribute
		$param .= 'defer ';

		if ( $param ) {

			return str_replace( '<script ', '<script ' . $param, $tag );

		}

		// no change ##
		return $tag;

	}

}
