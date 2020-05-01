<?php

namespace q\theme;

use q\core\core as core;
use q\core\helper as helper;
// use q\core\options as options;

// load it up ##
\q\theme\template::run();

class template {

    // Array of custom templates to add ##
    protected static $custom_templates = [
        /*
        'test.php'      => [ 
                                'name'      => 'Theme : TEST',
                                'class      => '', // if empty, checks in Q 'theme/template' path, else indicates plugin class ##
                                'template'  => 'test.php'
                        ],
        */
    ];

    // Array of Native templates to override ##
    protected static $native_templates = [
        /*
        'single-post'   => [
                                'function'  => 'is_singular',
                                'argument'  => 'post',
                                'template'  => 'single-post.php',
                                'class'     => '', // if empty, checks in Q 'theme/template' path, else indicates plugin class ##
                        ],
        */
    ];

    // default page template ##
    protected static $default_template = 'index.php';

    // tracker ##
    protected static $template_tracker = null;

    /**
    * Kick things off
    */
    public static function run()
    {

        // we need to filter $custom_templates to allow plugins to inject extra templates ##

        // Add a filter to the attributes metabox to inject template into the cache.
        if ( version_compare( floatval( \get_bloginfo( 'version' ) ), '4.7', '<' ) ) {

            // 4.6 and older
            \add_filter(
                'page_attributes_dropdown_pages_args',
                array( get_class(), 'register_custom_templates' )
            );

        } else {

            // Add a filter to the wp 4.7 version attributes metabox
            \add_filter(
                'theme_page_templates', array( get_class(), 'add_custom_templates' )
            );

        }

        // Add a filter to the save post to inject our template into the page cache
        \add_filter(
            'wp_insert_post_data', 
            array( get_class(), 'register_custom_templates' ) 
        );

        // Add a filter to the template include to determine if the page has our 
        // template assigned and return it's path
        \add_filter(
            'template_include', 
            array( get_class(), 'check_custom_template' ), 3, 1 
        );

        // override native templates ##
        \add_filter( 
            'template_include', 
            array( get_class(), 'add_native_templates' ), 2, 1
        );

    }


    /**
     * Global getter
     * 
     * 
     */
    public static function get() 
    {

        if( ! isset( $GLOBALS['q_template'] ) ) {

            // helper::log( 'Page template empty' );
            
            return false;

        } else {

            // helper::log( 'Page template: '.$GLOBALS['q_template'] );

            return str_replace( '.php', '', $GLOBALS['q_template'] );        

        }

    }



    /**
     * Tracking Method
     */
    public static function track( $option = 'status', $template = null ){

        switch ( $option ) {

            case "status" :

                // helper::log( 'Checking Tracker: '.( ! is_null( self::$template_tracker ) ? self::$template_tracker : 'null' ) );

                // check on tracker status ##
                return ( true === self::$template_tracker ) ? true : false ;

            break ;

            case "reset" :

                // helper::log( 'Reset Tracker' );

                // empty stored template ##
                return self::$template_tracker = null ;

            break ;

            case "set" :

                // helper::log( 'Set Tracker: '.$template );

                // set stored template ##
                return self::$template_tracker = $template;

            break ;

            case "get" :

                // helper::log( 'Get Tracker: '.self::$template_tracker );

                // returned stored template ##
                return self::$template_tracker;

            break ;


        }


    }



    

    /**
     * Add custom templates defined in external plugins
     */
    public static function filter_custom_templates( $array, $templates = null, $class = null ){

        // let's check if we have any items to add, and format them as required ##
        if ( 
            ! isset( $templates ) 
            || ! is_array( $templates ) 
            || count( array_filter( $templates ) ) == 0
        ) {

            // helper::log( 'No custom templates to add to array' );

            return $array;

        }

        // helper::log( 'we have '.count( $templates ).' custom templates to add' );
            
        // define a new array ##
        $new_array = [];

        // loop over each item ##
        foreach( $templates as $key => $value ){

            $new_array[$key] = [
                'name'      => $value,
                'class'     => $class, // q_theme -- works as this class extends the parent implicitly ##
                'template'  => $key
            ];

        }

        // we are passed an array, let's peek at it ##
        // helper::log( $array );

        // test ##
        // helper::log( $new_array );

        // merge array - adding new items to the end and overwritting shared keys ##
        $return_array = array_merge( $array, $new_array );

        // test ##
        // helper::log( $return_array );

        // kick back ##
        return $return_array;

    }


    /**
     * Add native templates defined in external plugins
     */
    public static function filter_native_templates( $array, $templates = null, $class = null ){

        // let's check if we have any items to add, and format them as required ##
        if ( 
            ! isset( $templates ) 
            || ! is_array( $templates ) 
            || count( array_filter( $templates ) ) == 0
        ) {

            // helper::log( 'No native templates to add to array' );

            return $array;

        }

        // helper::log( 'we have '.count( self::$native_templates ).' native templates to add' );
            
        // define a new array ##
        $new_array = $templates;

        // loop over each item -- add class identifier ##
        foreach( $templates as $key => $value ){

            $new_array[$key]['class'] = $class; // q_theme -- works as this class extends the parent implicitly ##

        }

        // we are passed an array, let's peek at it ##
        // helper::log( $array );

        // test ##
        // helper::log( $new_array );

        // merge array - adding new items to the end and overwritting shared keys ##
        $return_array = array_merge( $array, $new_array );

        // test ##
        // helper::log( $return_array );

        // kick back ##
        return $return_array;

    }



    /**
     * Default template
     * 
     * 
     */
    public static function get_default_template( $template )
    {
        
        // look for default template - in q_theme
        $template =  
            helper::get( 'theme/template/'.self::$default_template, 'return', 'path' ) ? 
            self::$default_template = helper::get( 'theme/template/'.self::$default_template, 'return', 'path' ) : 
            $template ;

        // filter ##
        $template = \apply_filters( 'q/template/default', $template );

        // return ##
        return $template;

    }


    public static function format_custom_templates( Array $array = null ){

        // sanity ##
        if ( 
            is_null( $array ) 
            // might require additional checks ##
        ) {

            helper::log( 'Error in passed array' );

            return false;

        }

        // loop over each item and return in required format ##
        // [ 'file.php' => 'Name', ];
        $return_array = [];
        foreach( $array as $key => $value ) {

            $return_array[ $key ] = $value['name'];

        }

        // test ##
        // helper::log( $return_array );

        // kick it back ##
        return $return_array;

    }



    /**
	 * Adds our template to the WP admin dropdown for v4.7+
	 *
     * @since       0.1.0
     * @return      Array
	 */
	public static function add_custom_templates( $templates ) 
    {

        // filter in external custom templates ##
        // We also need to format the templates to what WP expects [ 'file.php' => 'Name', ]; ##
        self::$custom_templates = 
            self::format_custom_templates( \apply_filters( 'q/templates/custom', self::$custom_templates )
        );
        
        // merge into known list ##
    	$templates = array_merge( $templates, self::$custom_templates );
        
        // return ##
        return $templates;

	}


    /**
    * Register extra templates to the WP cache
    *
    * @since        0.1.0
    * @return       Array
    */
    public static function register_custom_templates( $atts ) {

        // Create the key used for the themes cache
        $cache_key = 'page_templates-' . md5( \get_theme_root() . '/' . \get_stylesheet() );

        // Retrieve the cache list. 
        // If it doesn't exist, or it's empty prepare an array
        $templates = wp_get_theme()->get_page_templates();
        if ( empty( $templates ) ) {
            $templates = array();
        } 

        // New cache, therefore remove the old one
        \wp_cache_delete( $cache_key , 'themes');

        // filter in external custom templates ##
        // We also need to format the templates to what WP expects [ 'file.php' => 'Name', ]; ##
        self::$custom_templates = 
            self::format_custom_templates( \apply_filters( 'q/templates/custom', self::$custom_templates )
        );

        // Now add our template to the list of templates by merging our templates
        // with the existing templates array from the cache.
        $templates = array_merge( $templates, self::$custom_templates );

        // Add the modified cache to allow WordPress to pick it up for listing
        // available templates
        \wp_cache_add( $cache_key, $templates, 'themes', 1800 );

        // kick it back ##
        return $atts;

    }



    /**
    * Checks if the template is assigned to the page
    *
    * @since        0.1.0
    * @return       String
    */
    public static function check_custom_template( $template ) {
        
        // force default template --- @TESTING ##
        // $template = self::get_default_template( $template );

        // check tracker ##
        if ( self::track() ) {

            // helper::log( 'Template already defined: '.self::track('get') );

            return self::track('get');

        }

        // test ##
        // helper::log( 'Template at start: '.$template );

        // Get global post
        global $post;

        // Return template if post is empty
        if ( 
            ! $post 
            || \is_search()
            || \is_404()
        ) {

            // helper::log( 'No post, is_search or is_404 matched - using '.$template );

            return $template;

        }

        // get template ##
        if ( ! $_wp_page_template = \get_post_meta( $post->ID, '_wp_page_template', true ) ) {

            // helper::log( 'No stored match in _wp_page_template - using: '.$template );

            return $template;

        }

        // we need to check if this is empty ##
        // helper::log( '$_wp_page_template: '.$_wp_page_template );

        // filter in external custom templates ##
        // remember that the format has changed ##
        self::$custom_templates = \apply_filters( 'q/templates/custom', self::$custom_templates );

        // Return default template if we don't have a custom one defined
        if ( ! isset( self::$custom_templates[ $_wp_page_template ] ) ) {

            // helper::log( 'kicking back template: '.$template );

            return $template;

        } 

        // we need to include the 'class' parameter, if the templates has this defined by an extended plugin ##
        $class = 
            (
                isset( self::$custom_templates[ $_wp_page_template ]['class'] )
                && ! is_null( self::$custom_templates[ $_wp_page_template ]['class'] )
            ) ?
            self::$custom_templates[ $_wp_page_template ]['class'] :
            null ;

        // look for file - fallback to default if not found ##
        if ( 
            $file = helper::get( 
                'theme/template/'.$_wp_page_template, 
                'return', 
                'path',
                'library/', // standard base library path ##
                $class // variable class ##
            ) 
        ) {

            // helper::log( 'template file found and set to: '.$file );

            // add global ##
            $GLOBALS['q_template'] = $_wp_page_template;

            // update tracker ##
            self::track( 'set', $file );

            // kick it back ##
            return $file;

        }

        // test ##
        // helper::log( 'custom file not found, kicking back default template: '.$template );

        // return ##
        return $template;

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
    public static function add_native_templates( $template ) {

        // check tracker ##
        if ( self::track() ) {

            // helper::log( 'Template already defined: '.self::track('get') );

            return self::track('get');

        }
        
        // force default template ##
        $template = self::get_default_template( $template );

        // filter in external native templates ##
        // remember that the format has changed ##
        self::$native_templates = \apply_filters( 'q/templates/native', self::$native_templates );

        if ( 
            // ! is_array( self::$native_templates )
            ! array_filter( self::$native_templates ) 
        ) {

            // helper::log( 'not filtering any native templates.' );

            return $template;

        }

        foreach( self::$native_templates as $key => $item ) {

            // we need to include the 'class' parameter, if the templates has this defined by an extended plugin ##
            $class = 
            (
                isset( $item['class'] )
                && ! is_null( $item['class'] )
            ) ?
            $item['class'] :
            null ;
             
            // helper::log( 'template: '.$item["template"].' / rule: '.$item["function"].' / class: '.$class );

            if ( function_exists( $item["function"] ) ) {

                // helper::log( 'function exists: '.$item['function'] );

                if ( TRUE === call_user_func_array( $item["function"], array( $item["argument"] ) ) ) {

                    // helper::log( 'function matched: '.$item["function"] );

                    if ( 
                        $template = helper::get( 'theme/template/'.$item["template"], 
                            'return', 
                            'path',
                            'library/', // standard base library path ##
                            $class // variable class ##
                        ) ) {
                        
                        // $template = helper::get( 'theme/template/'.$item["template"], 'return', 'path' );

                        // helper::log( 'New template loaded: '.$item["template"] );

                        // add global ##
                        $GLOBALS['q_template'] = $item["template"];

                        // update tracker ##
                        self::track( 'set', $template );

                        // kick it back ##
                        return $template;

                    }

                }

            }

        }

        // helper::log( 'return default template: '.$template );

        // nothing cooking -- kick back orginal ##
        return $template;

    }

}