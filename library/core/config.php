<?php

namespace q\core;

use q\core\core as core;
use q\core\helper as helper;

// Q Theme Config ##
use q\theme\core\config as theme_config;

// load it up ##
\q\core\config::run();

class config extends \Q {

    public static function run()
    {

        // filter intermediate image sizes ##
        \add_filter( 'intermediate_image_sizes_advanced', [ get_class(), 'intermediate_image_sizes_advanced' ] );

        // add_image_sizes for all themes ##
        \add_action( 'init', [ get_class(), 'add_image_sizes' ], 1 );

        if ( \is_admin() ) {


		} else {

            // load template properties ##
            // \add_action( 'wp', [ get_class(), "load_properties" ] );

        }

        // make sure properties are loaded when AJAX requests run ##
        if ( \wp_doing_ajax() ) {

            // self::load_properties();

        }

    }



	
    /**
     * Load settings for use in templates
     *
     * @since       1.0.3
     * @return      string
     */
    public static function properties(){

		// comments are ok ##
		$array['allow_comments'] = true;

        // holder images ##
        $array['the_holder']  = [
            'the_posts'             => helper::get( "theme/css/images/holder/desktop_the_posts.svg", 'return' ),
            'the_avatar'            => helper::get( "theme/css/images/holder/desktop_the_avatar.svg", 'return' ),
            'template_header'       => helper::get_device() == 'desktop' ?
                                        helper::get( "theme/css/images/holder/desktop_header.svg", 'return' ) :
                                        helper::get( "theme/css/images/holder/handheld_header.svg", 'return' )
		];

        // get_posts() ##
        $array['the_posts']  = [
			'post_type'				=> [ 'post' ],
            'posts_per_page'        => \get_option( "posts_per_page", 10 ),// per page ##
			'limit'                 => \get_option( "posts_per_page", 10 ), // posts to load ##
			'wrap'					=> '<div class="the-posts row">%the_posts%<div>',
            'template'              => 'post', // used to make get_$template_loop method call ##
            'pagination'            => true, // next / back links ##
            'query_vars'            => false,
            'search'				=> false, // ? include in search ?? ##
            'holder'                => array ( 
                                        'desktop'   => helper::get( "theme/css/images/holder/desktop-list-post.svg", 'return' ),
                                        'handheld'  => helper::get( "theme/css/images/holder/handheld-list-post.svg", 'return' )
                                    ),
            'handle'                => array( 
                                        'desktop'   => 'desktop-list-post',
                                        'handheld'  => 'handheld-list-post'
                                    ),
            'length'                => '200', // return limit for excerpt ##
			'date_format'           => 'F j, Y',
			'allow_comments'        => $array['allow_comments'], // allow comments ##
        ];

        // search ---------
        $array['the_search']  = [
            'post_type'             => 'question',
            'posts_per_page'        => 10,// per page ##
            'limit'					=> 10, // total results ##
            'pagination'            => true, // next / back links ##
            'thumbnail'             => false, // show featured image ##
        ];

        // the_post_single() ##
        $array['the_post_single']  = [
            'allow_comments'        => $array['allow_comments'], // allow comments ##
            'pagination'            => ( helper::get_device() == 'desktop' ) ? false : true, // next / home / back links ##
        ];

        // the_post_meta() ##
        $array['the_post_meta']  = [
            'allow_comments'        => $array['allow_comments'], // allow comments ##
        ];

        // // get_loop() ##
        // $array['the_loop']  = [
        //     'excerpt_length'        => 200, // excerpt length ##
        //     'title_length'          => 60, // title length ##
        //     'holder'                => $array['the_holder']['the_posts'], // holder image - same as $the_posts
        //     'image_handle'          => 'search' // image size to use ##
        // ];

        // title ##
        $array['the_title']  = [
			'markup'                => '<h1 class="the-title col-12 text-uppercase">%title%</h1>',
		];

        // parent ##
        $array['the_parent']  = [
            'markup'                => '<h4 class="the-parent col-12"><a href="%permalink%">%title%</a></h4>',
        ];

        // the_excerpt() ##
        $array['the_excerpt']  = [
			'markup'                => '<div class="col-12 the-excerpt">%content%</div>',
            'limit'                 => 300, // default excerpt length ##
        ];

        // the_content() ##
        $array['the_content']  = [
			'markup'                => '<div class="col-12 the-content">%content%</div>',
        ];

        // the_content_open() ##
        $array['the_content_open']  = [
			'markup'				=> '<main class="container %classes%">'
		];
		
		// the_content_close() ##
        $array['the_content_close']  = [
			'markup'				=> '</main>'
        ];

        // the_avatar() ##
        $array['the_avatar']  = [
			'markup'				=> '<div class="the-avatar">%src%</div>',
            'style'                 => 'post', // single post OR post category ## ??
            'holder'                => $array['the_holder']["the_avatar"], // holder image ##
        ];

        // get_post_by_meta() ##
        $array['get_post_by_meta'] = [
            'meta_key'              => 'page_name',
            'post_type'             => 'page',
            'posts_per_page'        => 1,
            'order'					=> 'DESC',
            'orderby'				=> 'date'
        ];

        // navigation ---------
        $array['the_navigation']  = [
            'post_type'             => 'page',
            'add_parent'            => false,
            'posts_per_page'        => 10
        ];

        // navigation ---------
        $array['the_nav_menu']  = [
            // no wrapping ##
            'items_wrap'        => '%3$s',
            // do not fall back to first non-empty menu
            'theme_location'    => '__no_such_location',
            // do not fall back to wp_page_menu()
            'fallback_cb'       => false,
            'container'         => false,
        ];

        // landing ---------
        $array['the_landing']  = [
            'post_type'             => 'page',
            #'add_parent'            => false,
            'link_parents'          => false, // should we allow parents with children to be clickable ##
            'posts_per_page'        => -1,
            'class'                 => 'the-landing'
        ];

		// ordered_posts ---------
        $array['ordered_posts']  = [
            'post_parent'           => false, // if true, grab posts with parent of current post ##
            'show'                  => 2, // number of extra items to show ( x before and x after current item ) ##
            'direction'             => 'next', // next or back ##
            'title'                 => true, // show titles below src_small ##
            'order_by'              => 'menu_order',
            'find'                  => 'first' // middle or first ##
        ];


        // forcing the post ##
        $array['set_force_post'] = [
            'post_parent'           => true
        ];

        // the_related_posts ##
        $array['the_related_posts']  = [
            'title_length'          => 60, // title length ##
            'holder'                => $array['the_holder']["the_posts"], // holder image - same as $the_posts
            'handle'                => 'thumbnail' // image size to use -- @todo make device aware with self::get_device() ##
        ];

        // the_page ##
        $array['the_page']  = [
            'holder'                => ( helper::get_device() == 'desktop' ) ? '1440x480' : '1440x480', // holder image ##
            'handle'                => helper::get_device().'-single' // image size to use ##
		];
		
		// return ##
		return $array;

	}
	


	/**
	 * Get stored config setting, merging in any new of changed settings from \q_theme::$config ##
	 */
	public static function get( $field = null ) {

		if ( is_null( $field ) ) {

			return false;

		}

		// get all config data ##
		$config = self::properties();

		// filter all config early ##
		$filter_config = \apply_filters( 'q/config/get/all', $config );

		// check if data has changed via filter ##
		// @todo -- 

		// merge filtered data into default data ##
		$config = core::parse_args( $filter_config, $config );

		// now, check if we are looking for a specific field ##
		if ( 
			is_null( $field ) 
		) {

			// helper::log( 'Getting all data' );

			// kick back ##
			return $data;

		}

		// helper::log( 'Looking for specific Field: "'.$field.'"' );

		// check if field is set ##
		if ( 
			! isset( $config[$field] ) 
		){

			helper::log( 'No matching config found for Field: "'.$field.'"' );

			return false;

		}

		// helper::log( 'Returning config data for Field: "'.$field.'"' );

		// get field data ##
		$field_data = $config[$field];

		// filter specific field data ##
		// $filter_field_data = \apply_filters( 'q/config/get/field', [ 'field' => $field, 'data' => $field_data ] );

		// merge filtered data into default data ##
		// $field_data = core::parse_args( $filter_field_data, $field_data );

		// helper::log( $field_data );

		// // filter via q_theme, if set - and merge is new and different values ##
		// if ( \class_exists( 'q\theme\core\config' ) ) {

		// 	$theme_config = theme_config::get( $field ) ;

		// 	// helper::log( $theme_config );

		// 	// merge ##
		// 	$data = core::parse_args( $theme_config, $data );

		// }

		// merge ##
		// $data = core::parse_args( $theme_config, $data );

		// helper::log( $data );

		// final filter ##
		// $data = \apply_filters( 'q/config/get/'.$field, $data );

		// kick back ##
		return $field_data;

	}



    /**
     * Remove standard image sizes so that these sizes are not
     * created during the Media Upload process
     *
     * Tested with WP 3.2.1
     *
     * Hooked to intermediate_image_sizes_advanced filter
     * See wp_generate_attachment_metadata( $attachment_id, $file ) in wp-admin/includes/image.php
     *
     * @param $sizes, array of default and added image sizes
     * @return $sizes, modified array of image sizes
     * @author http://www.wpmayor.com/code/remove-image-sizes-in-wordpress/
     */
    public static function intermediate_image_sizes_advanced( $sizes)
    {

        unset( $sizes['slides']);
        unset( $sizes['slides-small']);
        unset( $sizes['home']);
        unset( $sizes['new-photos']);
        unset( $sizes['hero']);

        return $sizes;

    }



    /**
     * Add image sizes for all devices - so that all device images sizes are prepared when files are uploaded
     * Note: Tablet uses desktop sized images
     *
     * @since       0.1
     * @return      void
     */
    public static function add_image_sizes()
    {

        // generic ##
        \add_image_size( 'icon', 80, 80, false ); // icon ##
        \add_image_size( 'thumb', 270, 9999, false ); // small thumb ##

        // generic ##
        \add_image_size( 'thumb', 194, 97, true ); // small thumb ##

    }



}