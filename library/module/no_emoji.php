<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
use q\asset;

// load it up ##
\q\module\no_emoji::__run();

class no_emoji extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\add_filter( 'acf/load_field/name=q_option_module', [ get_class(), 'filter_acf_module' ], 10, 1 );

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('no_emoji') );
		if ( 
			! isset( core\option::get('module')->no_emoji )
			|| true !== core\option::get('module')->no_emoji 
		){

			// h::log( 'd:>Emoji is not enabled.' );

			return false;

		}
		
		\add_action( 'init', [ get_class(), 'disable_emojis' ] );

    }



    public static function args( $args = false )
    {

        #helper::log( 'passed args to modal' );
        // helper::log( $args );

        // update passed args ##
        self::$args = \wp_parse_args( $args, self::$args );

	}
	

	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function filter_acf_module( $field )
    {

        // h::log( $field['choices'] );
        // h::log( $field['default_value'] );

		// pop on a new choice ##
		$field['choices']['no_emoji'] = 'Q ~ No Emoji';
		// $field['choices']['banner'] = '@todo - News Banner';

		// make it selected ##
		$field['default_value'][0] = 'no_emoji';
		
        // h::log( $field['choices'] );
        // h::log( $field['default_value'] );

         return $field;

	}


    /**
	 * Disable the emoji's
	 */
	public static function disable_emojis() {

		\remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		\remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		\remove_action( 'wp_print_styles', 'print_emoji_styles' );
		\remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
		\remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		\remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
		\remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

		\add_filter( 'tiny_mce_plugins', [ get_class(), 'disable_emojis_tinymce' ] );
		\add_filter( 'wp_resource_hints', [ get_class(), 'disable_emojis_remove_dns_prefetch' ], 10, 2 );

	}
  
   
   /**
	* Filter function used to remove the tinymce emoji plugin.
	* 
	* @param array $plugins 
	* @return array Difference betwen the two arrays
	*/
   	public static function disable_emojis_tinymce( $plugins ) {

		if ( is_array( $plugins ) ) {

			return array_diff( $plugins, array( 'wpemoji' ) );

		} else {

			return array();

		}
	}
   
   /**
	* Remove emoji CDN hostname from DNS prefetching hints.
	*
	* @param array $urls URLs to print for resource hints.
	* @param string $relation_type The relation type the URLs are printed for.
	* @return array Difference betwen the two arrays.
	*/
	public static function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
		
		if ( 'dns-prefetch' == $relation_type ) {

			/** This filter is documented in wp-includes/formatting.php */
			$emoji_svg_url = \apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
			
			$urls = array_diff( $urls, array( $emoji_svg_url ) );

		}
	
		return $urls;

	}

}
