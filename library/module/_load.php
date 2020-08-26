<?php

namespace q;

use q\core;
use q\core\helper as h;

// load it up ##
\q\module::__run();

class module extends \Q {

	public static function __run(){

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
			'javascript' 	=> h::get( 'module/javascript.php', 'return', 'path' ),
			'navigation' 	=> h::get( 'module/navigation.php', 'return', 'path' ), 
			'cookie' 		=> h::get( 'module/cookie.php', 'return', 'path' ),
			'bs_modal' 		=> h::get( 'module/bs_modal.php', 'return', 'path' ),
			'bs_toast' 		=> h::get( 'module/bs_toast.php', 'return', 'path' ),
			'bs_tab' 		=> h::get( 'module/bs_tab.php', 'return', 'path' ),
			'bs_accordion' 	=> h::get( 'module/bs_accordion.php', 'return', 'path' ),
			'bs_form' 		=> h::get( 'module/bs_form.php', 'return', 'path' ),
			'bs_toggle' 	=> h::get( 'module/bs_toggle.php', 'return', 'path' ),
			'bs_gallery' 	=> h::get( 'module/bs_gallery.php', 'return', 'path' ),
			'bs_helper' 	=> h::get( 'module/bs_helper.php', 'return', 'path' ),
			'no_emoji' 		=> h::get( 'module/no_emoji.php', 'return', 'path' ),
			'grunt' 		=> h::get( 'module/grunt.php', 'return', 'path' ),
			'load' 			=> h::get( 'module/load.php', 'return', 'path' ),
			'comment' 		=> h::get( 'module/comment.php', 'return', 'path' ),
			'scroll' 		=> h::get( 'module/scroll.php', 'return', 'path' ),
			'sharelines' 	=> h::get( 'module/sharelines.php', 'return', 'path' ),
			'push' 			=> h::get( 'module/push.php', 'return', 'path' ),
			'anspress' 		=> h::get( 'module/anspress.php', 'return', 'path' ),
			'acf_form' 		=> h::get( 'module/acf_form.php', 'return', 'path' ),
			// 'popper' 		=> h::get( 'module/popper.php', 'return', 'path' ),
			// 'toggle' => h::get( 'module/toggle.php', 'return', 'path' ), // ?? needed ??
			// 'filter' => h::get( 'ui/module/filter.php', 'return', 'path' ),
			// 'modal' => h::get( 'ui/module/modal.php', 'return', 'path' ),
			// 'tab' => h::get( 'ui/module/tab.php', 'return', 'path' ),
			// 'select' => h::get( 'ui/module/select.php', 'return', 'path' ),
			
			// 'filter' => h::get( 'ui/module/filter.php', 'return', 'path' ),
		];


    }

}
