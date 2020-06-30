<?php

namespace q\render;

use q\core;
use q\core\helper as h;
use q\render;

// load it up ##
\q\render\context::run();

class context extends render {

	public static function run(){

		// load libraries ##
		core\load::libraries( self::load() );

	}

    /**
    * Load Libraries
    *
    * @since        4.1.0
    */
    public static function load()
    {

		return $array = [

			// acf field handler ##
			'field' => h::get( 'render/context/field.php', 'return', 'path' ), 

			// acf field groups ##
			'group' => h::get( 'render/context/group.php', 'return', 'path' ),

			// post objects content, title, excerpt etc ##
			'post' => h::get( 'render/context/post.php', 'return', 'path' ),

			// navigation items ##
			'navigation' => h::get( 'render/context/navigation.php', 'return', 'path' ),

			// media items ##
			'media' => h::get( 'render/context/media.php', 'return', 'path' ),

			// taxonomies ##
			'taxonomy' => h::get( 'render/context/taxonomy.php', 'return', 'path' ),

			// extension ##
			'extension' => h::get( 'render/context/extension.php', 'return', 'path' ),

			// widgets ##
			'widget' => h::get( 'render/context/widget.php', 'return', 'path' ),

			// ui render methods - open, close.. etc ##
			'ui' => h::get( 'render/context/ui.php', 'return', 'path' ),

			// block renders, such as post_meta ##
			// 'block' => h::get( 'render/context/block.php', 'return', 'path' ),

			// perhaps type css ##
			// perhaps type js ##
			// perhaps type font ##

		];

	}
	

}
