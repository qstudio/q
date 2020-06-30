<?php

namespace q;

use q\core;
use q\core\helper as h;

// load it up ##
\q\module::run();

class module extends \Q {

	public static function run(){

		core\load::libraries( self::load() );

	}

    /**
    * Load Libraries
    *
    * @since        2.0.0
    */
    public static function load()
    {

		return $array = [
			'comment' => h::get( 'module/comment.php', 'return', 'path' ),
			'navigation' => h::get( 'module/navigation.php', 'return', 'path' ), // @todo -- will go ##
			'nav_walker' => h::get( 'module/wp-navwalker.php', 'return', 'path' ),
			'cookie' => h::get( 'module/cookie.php', 'return', 'path' ),
			// 'filter' => h::get( 'ui/module/filter.php', 'return', 'path' ),
			// 'modal' => h::get( 'ui/module/modal.php', 'return', 'path' ),
			// 'tab' => h::get( 'ui/module/tab.php', 'return', 'path' ),
			// 'select' => h::get( 'ui/module/select.php', 'return', 'path' ),
			// 'scroll' => h::get( 'ui/module/scroll.php', 'return', 'path' ),
			// 'push' => h::get( 'ui/module/push.php', 'return', 'path' ),
			// 'filter' => h::get( 'ui/module/filter.php', 'return', 'path' ),
			// 'toggle' => h::get( 'ui/module/toggle.php', 'return', 'path' ),
			// 'load' => h::get( 'ui/module/load.php', 'return', 'path' ),
		];


    }

}
