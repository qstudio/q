<?php

namespace q\module;

use q\core;
use q\core\helper as h;

// gist oembed, forked from https://plugins.trac.wordpress.org/browser/oembed-gist/trunk/oembed-gist.php

// load it up ##
\q\module\gist::__run();

class gist extends \Q {

	private static $shotcode_tag = 'gist';
	private static $noscript;
	private static $regex = '#(https://gist.github.com/([^\/]+\/)?([a-zA-Z0-9]+)(\/[a-zA-Z0-9]+)?)(\#file(\-|_)(.+))?$#i';

    public static function __run()
    {

		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> str_replace( __NAMESPACE__.'\\', '', static::class ),
			'name'		=> 'Q ~ Gist Oembed',
			// 'selected'	=> true,
		]);

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('tab') );
		if ( 
			! isset( core\option::get('module')->gist )
			|| true !== core\option::get('module')->gist 
		){

			// h::log( 'd:>Helper is not enabled.' );

			return false;

		}

        \add_action( 'plugins_loaded', [ get_class(), 'plugins_loaded' ] );

	}
	
	public static function plugins_loaded()
	{
		
		\wp_embed_register_handler(
			'oe-gist',
			self::get_gist_regex(),
			array( get_class(), 'handler' )
		 );

		\add_shortcode( self::get_shortcode_tag(), array( get_class(), 'shortcode' ) );

		/*
		// needed ?? ##
		\add_filter(
			'jetpack_shortcodes_to_include',
			array( get_class(), 'jetpack_shortcodes_to_include' )
		);
		*/

		\add_filter(
			'oembed_providers',
			array( get_class(), 'oembed_providers' )
		);

	}

	/*
	public static function jetpack_shortcodes_to_include( $incs ) {

		$includes = array();

		foreach ( $incs as $inc ) {
			if ( !preg_match( "/gist\.php\z/", $inc ) ) {
				$includes[] = $inc;
			}
		}
		
		return $includes;

	}
	*/

	public static function oembed_providers( $providers ) {

		// Support to Press This.
		global $pagenow;

		if ( 'press-this.php' == $pagenow && ! array_key_exists( self::get_gist_regex(), $providers ) ) {
			$providers[ self::get_gist_regex() ] = array(
				'https://gist.github.com/{id}.{format}', //dummy value
				true
			);
		}

		return $providers;

	}

	public static function handler( $m, $attr, $url, $rattr ) {

		if ( !isset( $m[7] ) || !$m[7] ) {
			$m[7] = null;
		}

		return self::shortcode( array(
			'url'  => $m[1],
			'id'   => $m[3],
			'file' => $m[7],
		) );

	}

	public static function shortcode( $p ) {

		if ( isset( $p['url'] ) && $p['url'] ) {
			$url = $p['url'];
		} elseif ( preg_match( "/^[a-zA-Z0-9]+$/", $p['id'] ) ) {
			$url = 'https://gist.github.com/' . $p['id'];
		}

		// If displaying AMP page with wp-amp plugin - return link.
		if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
			return sprintf( __( '<a href="%s">See the gist on github</a>.', 'oembed-gist' ), esc_url( $url ) );
		}

		$noscript = sprintf(
			__( 'View the code on <a href="%s">Gist</a>.', 'oembed-gist' ),
			esc_url( $url )
		);

		$url = $url . '.js';

		if ( isset( $p['file'] ) && $p['file'] ) { //RRD: Fixed line 79 error by adding isset()
			$file = preg_replace( '/[\-\.]([a-z]+)$/', '.\1', $p['file'] );
			$url = $url . '?file=' . $file;
		}

		/*
		wp_enqueue_script(
			'oembed-gist',
			plugins_url( 'js/script.min.js', __FILE__ ),
			array(),
			self::version,
			true
		);
		*/

		// JS hack ##
		// $script = self::script();

		if( is_feed() ){
			return $noscript;
		} else {
			return sprintf(
				'<div class="oembed-gist"><script src="%s"></script><noscript>%s</noscript></div>',
				$url,
				$noscript,
				// self::script(),
				// self::style()
			);
		}
	}

	public static function style(){

		ob_start();

?>
<style>
	.gist .line,.gist .line span{word-wrap:normal!important}.gist table{margin-bottom:0!important;table-layout:auto!important}.gist .line-numbers{width:4em!important}.gist .line,.gist .line-number{font-size:12px!important;height:18px!important;line-height:18px!important}.gist .line{white-space:pre!important;width:auto!important}
</style>
<?php
		
		return ob_get_clean();
		
	}


	public static function script(){

		ob_start();

?>
<script>
	document.querySelector(".gist")&&function(){for(var e="/oembed-gist/js/script.min.js",t="/oembed-gist/css/style.min.css",s=document.querySelectorAll("script"),r=0;r<s.length;r++){var i=s[r].getAttribute("src");i&&0<i.indexOf(e)&&(t=i.replace(e,t))}var c=document.createElement("link");c.setAttribute("rel","stylesheet"),c.setAttribute("type","text/css"),c.setAttribute("media","all"),c.setAttribute("href",t),document.head.appendChild(c)}();
</script>
<?php

		return ob_get_clean();

	}

	public static function get_gist_regex(){

		return self::$regex;

	}

	private static function get_shortcode_tag(){

		return \apply_filters( 'oembed_gist_shortcode_tag', self::$shotcode_tag );

	}

}
