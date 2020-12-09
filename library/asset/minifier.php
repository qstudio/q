<?php

namespace q\asset;

use q\plugin as q;
use q\core;
use q\core\helper as h;
use MatthiasMullie\Minify;

// load it up ##
// \q\asset\minifier::run();

class minifier {

	static $loaded = false;

	function __construct(){}

    protected static function libraries(){

		if ( true === self::$loaded ){ return; }

        // make sure to update the path to where you cloned the projects to!
        require_once q::get_plugin_path( 'library/asset/minify/src/Minify.php' );
        require_once q::get_plugin_path( 'library/asset/minify/src/CSS.php' );
        require_once q::get_plugin_path( 'library/asset/minify/src/JS.php' );
        require_once q::get_plugin_path( 'library/asset/minify/src/Exception.php' );
        require_once q::get_plugin_path( 'library/asset/minify/src/Exceptions/BasicException.php' );
        require_once q::get_plugin_path( 'library/asset/minify/src/Exceptions/FileImportException.php' );
        require_once q::get_plugin_path( 'library/asset/minify/src/Exceptions/IOException.php' );
        require_once q::get_plugin_path( 'library/asset/path-converter/src/ConverterInterface.php' );
		require_once q::get_plugin_path( 'library/asset/path-converter/src/Converter.php' );

		// load once ##
		self::$loaded = true;

    }
    
    /**
    * Minify JS
    *
    * @since        2.0.0
    */
    public static function javascript( $string = null ){

		self::libraries();

        $minifier = new Minify\JS( $string );
        
        return $minifier->minify();

    }

    /**
    * Minify CSS
    *
    * @since        2.0.0
    */
    public static function css( $string = null ){

		self::libraries();

        $minifier = new Minify\CSS( $string );
        
        return $minifier->minify();

    }

}
