<?php

namespace q\module;

// use q\core\core as core;
use q\core\helper as helper;
// use q\core\config as config;

// use q\module\field\core as core;
// use q\module\field\validate as validate;
// use q\module\field\filter as filter;
use q\module\field\ui as ui;
// use q\module\field\format as format;
// use q\module\field\fields as fields;
// use q\module\field\output as output;

// load it up ##
\q\module\field::run();

class field extends \Q {

    protected static

        // default args to merge with passed array ##
        $args = [
            'config'            => [
                'run'           => true, // don't run this item ##
                'debug'         => false, // don't debug this item ##
                'return'        => 'echo' // default to echo return string ##
            ],
        ],

        // frontend pre-processor callbacks ##
        $callbacks = [
            'get_posts'         => [ // standard WP get_posts()
                'class'         => 'global', // global scope to allow for namespacing ##
                'method'        => '\get_posts()',
                'args'          => [] // default - can be edited via global and specific filters ##
            ]
        ],

        // value formatters ##
        $formats = [
            'array'             => [ // Arrays could be collection of WP Post Objects OR text - so check ##
                'type'          => 'is_array',
                'method'        => 'format_array'
            ],
            'post_object'       => [
                'type'          => 'is_object',
                'method'        => 'format_object'
            ],
            'integer'           => [
                'type'          => 'is_int',
                'method'        => 'format_integer'
            ],
            'string'            => [
                'type'          => 'is_string',
                'method'        => 'format_text',
            ],
        ],

        // standard fields to add from wp_post objects
        $wp_post_fields = [
            'ID',
            'post_title',
            'post_content',
            'post_excerpt',

            // required additional lookup ##
            'permalink', 
            'category_name', 
            // 'category_permalink',
            
            // requires additional lookup and handle ##
            'img', 
        ],

        $output = null, // return string ##
        $fields = null, // field names and values ##
        $markup = null, // store local version of passed markup ##
        $log = null, // tracking array for feedback ##
        // $debug = null, // debugging option ##
        $acf_fields = null // fields grabbed by acf function ##

    ;
    
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

        require_once self::get_plugin_path( 'library/module/field/filter.php' );
        require_once self::get_plugin_path( 'library/module/field/core.php' );
        require_once self::get_plugin_path( 'library/module/field/get.php' );
        require_once self::get_plugin_path( 'library/module/field/format.php' );
        require_once self::get_plugin_path( 'library/module/field/markup.php' );
        require_once self::get_plugin_path( 'library/module/field/ui.php' );
        require_once self::get_plugin_path( 'library/module/field/output.php' );
        require_once self::get_plugin_path( 'library/module/field/log.php' );

    }



    public static function render( Array $args = null ){

        ui::render( $args );

    }


}