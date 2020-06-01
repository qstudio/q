<?php

namespace q\ui;

// use q\core;
use q\core\helper as h;
// use q\core\options as options;

// load it up ##
\q\ui\controller::run();

class controller extends \Q {

    public static function run()
    {

        // load templates ##
        self::load_libraries();

    }



    /**
    * Load Libraries
    *
    * @since        2.0.0
    */
    private static function load_libraries()
    {

		// UI core ##
        require_once self::get_plugin_path( 'library/ui/method.php' );

        // widgets == @TODO, should be filter for q_theme_x settings and global controller ##
        require_once self::get_plugin_path( 'library/ui/widget.php' );

        // meta controller -- deprecated ##
		// require_once self::get_plugin_path( 'library/theme/meta.php' ); 
		
		// Markup tools ##
		require_once self::get_plugin_path( 'library/ui/markup.php' ); 
		
		// UI controls ##
		require_once self::get_plugin_path( 'library/ui/render.php' ); 

        // template controller, allows plugins to inject rules via filters ##
        require_once self::get_plugin_path( 'library/ui/template.php' ); 

        // add assets based on Q settings ##
		require_once self::get_plugin_path( 'library/ui/assets.php' );

		// render engines ##
        require_once self::get_plugin_path( 'library/ui/javascript.php' );
		require_once self::get_plugin_path( 'library/ui/css.php' );
		
		// custom field engines ##
		require_once self::get_plugin_path( 'library/ui/field.php' );
		## require_once self::get_plugin_path( 'library/ui/fields.php' );

        // cookies ##
        // require_once self::get_plugin_path( 'library/ui/cookie.php' );
        
        // minify ##
        require_once self::get_plugin_path( 'library/ui/minifier.php' );

        // UI controllers ##
        require_once self::get_plugin_path( 'library/ui/navigation.php' );
		// require_once self::get_plugin_path( 'library/controller/generic.php' );
		require_once self::get_plugin_path( 'library/ui/consent.php' );

        // UI / JS / AJAX features ## @todo ##
        // require_once self::get_plugin_path( 'library/ui/modal.php' );
        // require_once self::get_plugin_path( 'library/ui/tab.php' );
        // require_once self::get_plugin_path( 'library/ui/select.php' );
        // require_once self::get_plugin_path( 'library/ui/scroll.php' );
        // require_once self::get_plugin_path( 'library/ui/push.php' );
        // require_once self::get_plugin_path( 'library/ui/filter.php' );
        // require_once self::get_plugin_path( 'library/ui/toggle.php' );
        // require_once self::get_plugin_path( 'library/ui/load.php' );

    }



}