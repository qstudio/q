<?php

namespace q\controller;

use q\core\core as core;
use q\core\helper as helper;
use q\core\config as config;
use MatthiasMullie\Minify;

// load it up ##
\q\controller\minifier::run();

class minifier extends \Q {

    public static function run()
    {

        // make sure to update the path to where you cloned the projects to!
        require_once self::get_plugin_path( 'library/controller/minify/src/Minify.php' );
        require_once self::get_plugin_path( 'library/controller/minify/src/CSS.php' );
        require_once self::get_plugin_path( 'library/controller/minify/src/JS.php' );
        require_once self::get_plugin_path( 'library/controller/minify/src/Exception.php' );
        require_once self::get_plugin_path( 'library/controller/minify/src/Exceptions/BasicException.php' );
        require_once self::get_plugin_path( 'library/controller/minify/src/Exceptions/FileImportException.php' );
        require_once self::get_plugin_path( 'library/controller/minify/src/Exceptions/IOException.php' );
        require_once self::get_plugin_path( 'library/controller/path-converter/src/ConverterInterface.php' );
        require_once self::get_plugin_path( 'library/controller/path-converter/src/Converter.php' );

    }

    
    /**
    * Minify JS
    *
    * @since        2.0.0
    */
    public static function javascript( $string = null )
    {

        $minifier = new Minify\JS( $string );
        
        return $minifier->minify();

    }



    /**
    * Minify CSS
    *
    * @since        2.0.0
    */
    public static function css( $string = null )
    {

        $minifier = new Minify\CSS( $string );
        
        return $minifier->minify();

    }


}