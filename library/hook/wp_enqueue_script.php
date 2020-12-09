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

class wp_enqueue_script {

	function __construct(){}
	
	function hooks(){

		// not in the admin ##
        if ( ! \is_admin() ) { 

			// update jQuery - risky ##
			// add_action( 'wp_enqueue_scripts', [ get_class(), 'update_jquery' ], 0 );

			// defer / async asset loading ##
            \add_filter( 'script_loader_tag', [ get_class(), 'script_loader_tag' ], 1, 3 );

        }

	}
	
	// Front-end not excuted in the wp admin and the wp customizer (for compatibility reasons)
	// See: https://core.trac.wordpress.org/ticket/45130 and https://core.trac.wordpress.org/ticket/37110
	public static function update_jquery() {

		// jQuery
		if ( \is_admin() || \is_customize_preview() ) {
			
			// echo 'We are in the WP Admin or in the WP Customizer';
			return;

		}

		// Deregister WP core jQuery, see https://github.com/Remzi1993/wp-jquery-manager/issues/2 and https://github.com/WordPress/WordPress/blob/91da29d9afaa664eb84e1261ebb916b18a362aa9/wp-includes/script-loader.php#L226
		\wp_deregister_script( 'jquery' ); // the jquery handle is just an alias to load jquery-core with jquery-migrate
		// Deregister WP jQuery
		\wp_deregister_script( 'jquery-core' );
		// Deregister WP jQuery Migrate
		\wp_deregister_script( 'jquery-migrate' );

		// Register jQuery in the footer
		\wp_register_script( 'jquery-core', 'https://code.jquery.com/jquery-3.3.1.min.js', [], '3.3.1', true );

		/**
		 * Register jquery using jquery-core as a dependency, so other scripts could use the jquery handle
		 * see https://wordpress.stackexchange.com/questions/283828/wp-register-script-multiple-identifiers
		 * We first register the script and afther that we enqueue it, see why:
		 * https://wordpress.stackexchange.com/questions/82490/when-should-i-use-wp-register-script-with-wp-enqueue-script-vs-just-wp-enque
		 * https://stackoverflow.com/questions/39653993/what-is-diffrence-between-wp-enqueue-script-and-wp-register-script
		 */
		\wp_register_script( 'jquery', false, array( 'jquery-core' ), null, false );
		\wp_enqueue_script( 'jquery' );

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

		if ( 'jquery-core' === $handle ) {
			$tag = str_replace( 
				"type='text/javascript'", 
				"type='text/javascript' integrity='sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=' crossorigin='anonymous'", 
				$tag 
			);
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
