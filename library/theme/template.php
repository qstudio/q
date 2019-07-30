<?php

namespace q\theme;

use q\core\core as core;
use q\core\helper as helper;
// use q\core\options as options;

// load it up ##
\q\theme\template::run();

class template {

    /**
    * The array of templates ##
    */
    protected static $templates = array(
        // 'geo.php'           => 'Theme : GEO',
    );


    
    /*
    Native templates to override ##
    */
    protected static $native_templates = array(
        // 'page'          => array( 
        //     'function'  => 'is_singular',
        //     'argument'  => 'page',
        //     'template'  => 'page.php'
        // ),
    );

    // default page template ##
    protected static $default_template = 'index.php';

    /**
    * Kick things off
    */
    public static function run()
    {

        // we need to filter $templates to allow plugins to inject extra templates ##

        // Add a filter to the attributes metabox to inject template into the cache.
        if ( version_compare( floatval( \get_bloginfo( 'version' ) ), '4.7', '<' ) ) {

            // 4.6 and older
            \add_filter(
                'page_attributes_dropdown_pages_args',
                array( get_class(), 'register_project_templates' )
            );

        } else {

            // Add a filter to the wp 4.7 version attributes metabox
            \add_filter(
                'theme_page_templates', array( get_class(), 'add_new_template' )
            );

        }

        // Add a filter to the save post to inject our template into the page cache
        \add_filter(
            'wp_insert_post_data', 
            array( get_class(), 'register_project_templates' ) 
        );

        // Add a filter to the template include to determine if the page has our 
        // template assigned and return it's path
        \add_filter(
            'template_include', 
            array( get_class(), 'view_project_template' ), 2, 1 
        );

        // override native templates ##
        \add_filter( 
            'template_include', 
            array( get_class(), 'native_template_include' ), 1, 1
        );

    }


    public static function get() 
    {

        if( ! isset( $GLOBALS['q_theme_template'] ) ) {

            // helper::log( 'Page template empty' );
            
            return false;

        } else {

            // helper::log( 'Page template: '.$GLOBALS['q_theme_template'] );

            return str_replace( '.php', '', $GLOBALS['q_theme_template'] );        

        }

    }


    public static function get_default_template( $template )
    {

        return 
            helper::get( 'theme/template/'.self::$default_template, 'return', 'path' ) ? 
            self::$default_template = helper::get( 'theme/template/'.self::$default_template, 'return', 'path' ) : 
            $template ;

    }


    /**
	 * Adds our template to the page dropdown for v4.7+
	 *
     * @since       0.1.0
     * @return      Array
	 */
	public static function add_new_template( $posts_templates ) 
    {
	
    	$posts_templates = array_merge( $posts_templates, self::$templates );
		
        return $posts_templates;

	}


    /**
    * Register extra templates to the WP cache
    *
    * @since        0.1.0
    * @return       Array
    */
    public static function register_project_templates( $atts ) {

        // Create the key used for the themes cache
        $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

        // Retrieve the cache list. 
        // If it doesn't exist, or it's empty prepare an array
        $templates = wp_get_theme()->get_page_templates();
        if ( empty( $templates ) ) {
            $templates = array();
        } 

        // New cache, therefore remove the old one
        wp_cache_delete( $cache_key , 'themes');

        // Now add our template to the list of templates by merging our templates
        // with the existing templates array from the cache.
        $templates = array_merge( $templates, self::$templates );

        // Add the modified cache to allow WordPress to pick it up for listing
        // available templates
        wp_cache_add( $cache_key, $templates, 'themes', 1800 );

        return $atts;

    }



    /**
    * Checks if the template is assigned to the page
    *
    * @since        0.1.0
    * @return       String
    */
    public static function view_project_template( $template ) {
        
        // Get global post
        global $post;

        // Return template if post is empty
        if ( 
            ! $post 
            || \is_search()
            || \is_404()
        ) {
            return $template;
        }

        // get template ##
        $_wp_page_template = \get_post_meta( $post->ID, '_wp_page_template', true );
        helper::log( $_wp_page_template );

        // Return default template if we don't have a custom one defined
        if ( ! isset( self::$templates[ $_wp_page_template ] ) ) {

            helper::log( 'kicking back template: '.$template );

            return $template;

        } 

        $file = helper::get( 'theme/template/'.$_wp_page_template, 'return', 'path' );

        helper::log( 'template file set to: '.$_wp_page_template );

        // add global ##
        $GLOBALS['q_theme_template'] = $_wp_page_template;

        // kick it back ##
        return $file;

    }


    /**
    * Template loader.
    *
    * The template loader will check if WP is loading a template
    * for a specific Post Type and will try to load the template
    * from out 'templates' directory.
    *
    * @since 1.0.0
    *
    * @param	string	$template	Template file that is being loaded.
    * @return	string				Template file that should be loaded.
    */
    public static function native_template_include( $template ) {
        
        // if ( ! core::is_site( "public" ) ) {
            
        //     return $template;
            
        // }

        $template = self::get_default_template( $template );

        if ( 
            // ! is_array( self::$native_templates )
            ! array_filter( self::$native_templates ) 
        ) {

            helper::log( 'not filtering any native templates.' );

            return $template;

        }

        foreach( self::$native_templates as $key => $rule ) {
             
            helper::log( 'template: '.$rule["template"].' / rule: '.$rule["function"] );

            if ( function_exists( $rule["function"] ) ) {

                helper::log( 'function exists: '.$rule['function'] );

                if ( TRUE === call_user_func_array( $rule["function"], array( $rule["argument"] ) ) ) {

                    helper::log( 'function matched: '.$rule["function"] );

                    if ( helper::get( 'theme/template/'.$rule["template"], 'return', 'path' ) ) {
                        
                        $template = helper::get( 'theme/template/'.$rule["template"], 'return', 'path' );

                        helper::log( 'New template loaded: '.$rule["template"] );

                        // add global ##
                        $GLOBALS['q_theme_template'] = $rule["template"];

                        return $template;

                    }

                }

            }

        }

        helper::log( 'return detault template: '.$template );

        // nothing cooking -- kick back orginal ##
        return $template;

    }

}