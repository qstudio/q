<?php

namespace q\ui;

use q\core;
use q\core\helper as h;
use MatthiasMullie\Minify;

// load it up ##
\q\ui\minifier::run();

class minifier extends \Q {

    public static function run()
    {

        // make sure to update the path to where you cloned the projects to!
        require_once self::get_plugin_path( 'library/ui/minify/src/Minify.php' );
        require_once self::get_plugin_path( 'library/ui/minify/src/CSS.php' );
        require_once self::get_plugin_path( 'library/ui/minify/src/JS.php' );
        require_once self::get_plugin_path( 'library/ui/minify/src/Exception.php' );
        require_once self::get_plugin_path( 'library/ui/minify/src/Exceptions/BasicException.php' );
        require_once self::get_plugin_path( 'library/ui/minify/src/Exceptions/FileImportException.php' );
        require_once self::get_plugin_path( 'library/ui/minify/src/Exceptions/IOException.php' );
        require_once self::get_plugin_path( 'library/ui/path-converter/src/ConverterInterface.php' );
        require_once self::get_plugin_path( 'library/ui/path-converter/src/Converter.php' );

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