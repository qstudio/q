<?php

namespace q\plugin;

use q\core\core as core;
use q\core\helper as helper;
use q\core\wordpress as wordpress;

// load it up ##
\q\plugin\acf::run();

class acf extends \Q {

    public static function run()
    {

        // add fields ##
        \add_action( 'acf/init', array( get_class(), 'add_fields' ), 1 );

        // filter q/tab/special/script ##
        \add_filter( 'q/tab/special/script', [ get_class(), 'tab_special_script' ], 10, 2 );

        // permalinks from post objects ##
        \add_filter( 'q/meta/cta/generic_cta_url_1', array( get_class(), 'meta_post_object_permalink' ), 2, 10 );
        // \add_filter( 'q/meta/cta/generic_cta_url_2', array( get_class(), 'meta_post_object_permalink' ), 2, 10 );

    }



    /**
    * Add ACF Fields
    *
    * @since    2.0.0
    */
    public static function add_fields()
    {

        // get all field groups ##
        $groups = self::get_fields();

        if ( 
            ! $groups 
            || ! is_array( $groups )
        ) {

            helper::log( 'No groups to load.' );

            return false;

        }

        // loop over gruops ##
        foreach( $groups as $group ) {

            // load them all up ##
            \acf_add_local_field_group( $group );

        }

    }


    /**
    * Define field groups
    *
    * @since    2.0.0
    * @return   Mixed
    */
    public static function get_fields( $group = null )
    {

        // define field groups - exported from ACF ##
        $groups = array (

            'cta' => array (
                'key' => 'group_594c3d796eafc',
                'title' => 'Call to Action',
                'fields' => array (
                    array (
                        'key' => 'field_594c3d798a748',
                        'label' => 'Settings',
                        'name' => 'generic_cta_enable',
                        'type' => 'radio',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array (
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array (
                            0 => 'Disabled',
                            1 => 'Enabled',
                        ),
                        'allow_null' => 0,
                        'other_choice' => 0,
                        'save_other_choice' => 0,
                        'default_value' => 0,
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                    ),
                    array (
                        'key' => 'field_594c3d798ab48',
                        'label' => 'Title',
                        'name' => 'generic_cta_title',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => array (
                            array (
                                array (
                                    'field' => 'field_594c3d798a748',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                        'wrapper' => array (
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => 200,
                    ),
                    array (
                        'key' => 'field_594c3d798bb00',
                        'label' => 'Button Text',
                        'name' => 'generic_cta_button_1',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => array (
                            array (
                                array (
                                    'field' => 'field_594c3d798a748',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                        'wrapper' => array (
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => 50,
                    ),
                    array (
                        'key' => 'field_cta_button_one_radio',
                        'label' => 'URL Type',
                        'name' => 'cta_button_one_radio',
                        'type' => 'radio',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => array (
                            array (
                                array (
                                    'field' => 'field_594c3d798a748',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                        'wrapper' => array (
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array (
                            0 => 'Internal',
                            1 => 'External',
                        ),
                        'allow_null' => 0,
                        'other_choice' => 0,
                        'save_other_choice' => 0,
                        'default_value' => 0,
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                    ),
                    array (
                        'key' => 'field_generic_cta_url_1_internal',
                        'label' => 'URL',
                        'name' => 'generic_cta_url_1',
                        'type' => 'post_object',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => array (
                            array (
                                array (
                                    'field' => 'field_cta_button_one_radio',
                                    'operator' => '==',
                                    'value' => '0',
                                ),
                            ),
                        ),
                        'wrapper' => array (
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'post_type' => array (
                            0 => 'page',
                            1 => 'program',
                            // 2 => 'impact'
                        ),
                        'taxonomy' => array (
                        ),
                        'allow_null' => 0,
                        'multiple' => 0,
                        'return_format' => 'object',
                        'ui' => 1,
                    ),
                    array (
                        'key' => 'field_generic_cta_url_1_external',
                        'label' => 'URL',
                        'name' => 'generic_cta_url_1',
                        'type' => 'url',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => array (
                            array (
                                array (
                                    'field' => 'field_cta_button_one_radio',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                        'wrapper' => array (
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                    ),
                    /*
                    array (
                        'key' => 'field_594c3d798c319',
                        'label' => 'Button Text',
                        'name' => 'generic_cta_button_2',
                        'type' => 'text',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => array (
                            array (
                                array (
                                    'field' => 'field_594c3d798a748',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                        'wrapper' => array (
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => 50,
                    ),
                    array (
                        'key' => 'field_cta_button_two_radio',
                        'label' => 'URL Type',
                        'name' => 'cta_button_two_radio',
                        'type' => 'radio',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => array (
                            array (
                                array (
                                    'field' => 'field_594c3d798a748',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                        'wrapper' => array (
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array (
                            0 => 'Internal',
                            1 => 'External',
                        ),
                        'allow_null' => 0,
                        'other_choice' => 0,
                        'save_other_choice' => 0,
                        'default_value' => 0,
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                    ),
                    array (
                        'key' => 'field_generic_cta_url_2_internal',
                        'label' => 'URL',
                        'name' => 'generic_cta_url_2',
                        'type' => 'post_object',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => array (
                            array (
                                array (
                                    'field' => 'field_cta_button_two_radio',
                                    'operator' => '==',
                                    'value' => '0',
                                ),
                            ),
                        ),
                        'wrapper' => array (
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'post_type' => array (
                            0 => 'page',
                            1 => 'ezine',
                            2 => 'impact'
                        ),
                        'taxonomy' => array (
                        ),
                        'allow_null' => 0,
                        'multiple' => 0,
                        'return_format' => 'object',
                        'ui' => 1,
                    ),
                    array (
                        'key' => 'field_generic_cta_url_2_external',
                        'label' => 'URL',
                        'name' => 'generic_cta_url_2',
                        'type' => 'url',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => array (
                            array (
                                array (
                                    'field' => 'field_cta_button_two_radio',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                        'wrapper' => array (
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                    ),
                    */
                ),
                'location' => array (
                    array (
                        array (
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'post',
                        ),
                    ),
                    array (
                        array (
                            'param' => 'page_template',
                            'operator' => '==',
                            'value' => 'page.php',
                        ),
                    ),
                    array (
                        array (
                            'param' => 'page_template',
                            'operator' => '==',
                            'value' => 'resource.php',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => 1,
                'description' => '',
            ),

            'tab' => array (
                'key' => 'group_q_tab',
                'title' => 'Tabs',
                'fields' => array(
                    array(
                        'key' => 'field_q_tab_enable',
                        'label' => 'Settings',
                        'name' => 'q_tab_enable',
                        'type' => 'radio',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            0 => 'Disabled',
                            1 => 'Enabled',
                        ),
                        'allow_null' => 0,
                        'other_choice' => 0,
                        'save_other_choice' => 0,
                        'default_value' => 0,
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                    ),
                    array(
                        'key' => 'field_q_tab',
                        'label' => 'Tabs',
                        'name' => 'q_tab',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => array (
                            array (
                                array (
                                    'field' => 'field_q_tab_enable',
                                    'operator' => '==',
                                    'value' => '1',
                                ),
                            ),
                        ),
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => 'field_q_tab_title',
                        'min' => 0,
                        'max' => 20,
                        'layout' => 'row',
                        'button_label' => 'Add Tab',
                        'sub_fields' => array(
                            array(
                                'key' => 'field_q_tab_type',
                                'label' => 'Content Type',
                                'name' => 'type',
                                'type' => 'radio',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'choices' => array(
                                    'text' => 'Text',
                                    'faq' => 'FAQ',
                                    // 'blog' => 'Blog',
                                    'gallery' => 'Gallery',
                                    'special' => 'Special',
                                ),
                                'allow_null' => 0,
                                'other_choice' => 0,
                                'save_other_choice' => 0,
                                'default_value' => 'text',
                                'layout' => 'horizontal',
                                'return_format' => 'value',
                            ),
                            array(
                                'key' => 'field_q_tab_options',
                                'label' => 'Special',
                                'name' => 'special',
                                'type' => 'select',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_q_tab_type',
                                            'operator' => '==',
                                            'value' => 'special',
                                        ),
                                    ),
                                ),
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'choices' => array(
                                    'script'    => 'Script',
                                    // 'job'       => 'Job Fairs',
                                ),
                                'default_value' => array(
                                ),
                                'allow_null' => 0,
                                'multiple' => 0,
                                'ui' => 0,
                                'ajax' => 0,
                                'return_format' => 'value',
                                'placeholder' => '',
                            ),
                            array(
                                'key' => 'field_q_tab_title',
                                'label' => 'Title',
                                'name' => 'title',
                                'type' => 'text',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => 30,
                            ),
                            array(
                                'key' => 'field_q_tab_text',
                                'label' => 'Content',
                                'name' => 'text',
                                'type' => 'wysiwyg',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_q_tab_type',
                                            'operator' => '==',
                                            'value' => 'text',
                                        ),
                                    ),
                                    array(
                                        array(
                                            'field' => 'field_q_tab_type',
                                            'operator' => '==',
                                            'value' => 'gallery',
                                        ),
                                    ),
                                    array(
                                        array(
                                            'field' => 'field_q_tab_type',
                                            'operator' => '==',
                                            'value' => 'special',
                                        ),
                                    ),
                                ),
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'tabs' => 'all',
                                'toolbar' => 'full',
                                'media_upload' => 1,
                                'delay' => 1,
                                'display_word_limit' => 1,
                                'word_limit' => '',
                            ),
                            array(
                                'key' => 'field_q_tab_faq',
                                'label' => 'FAQ',
                                'name' => 'faq',
                                'type' => 'repeater',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_q_tab_type',
                                            'operator' => '==',
                                            'value' => 'faq',
                                        ),
                                    ),
                                ),
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'collapsed' => 'field_q_tab_faq_title',
                                'min' => 2,
                                'max' => 30,
                                'layout' => 'table',
                                'button_label' => 'Add FAQ',
                                'sub_fields' => array(
                                    array(
                                        'key' => 'field_q_tab_faq_title',
                                        'label' => 'Title',
                                        'name' => 'title',
                                        'type' => 'text',
                                        'instructions' => '',
                                        'required' => 1,
                                        'conditional_logic' => 0,
                                        'wrapper' => array(
                                            'width' => '',
                                            'class' => '',
                                            'id' => '',
                                        ),
                                        'default_value' => '',
                                        'placeholder' => '',
                                        'prepend' => '',
                                        'append' => '',
                                        'maxlength' => 200,
                                    ),
                                    array(
                                        'key' => 'field_q_tab_faq_content',
                                        'label' => 'Content',
                                        'name' => 'content',
                                        'type' => 'wysiwyg',
                                        'instructions' => '',
                                        'required' => 1,
                                        'conditional_logic' => 0,
                                        'wrapper' => array(
                                            'width' => '',
                                            'class' => '',
                                            'id' => '',
                                        ),
                                        'default_value' => '',
                                        'tabs' => 'all',
                                        'toolbar' => 'basic',
                                        'media_upload' => 0,
                                        'delay' => 1,
                                        'display_word_limit' => 1,
                                        'word_limit' => '',
                                    ),
                                ),
                            ),
                            array(
                                'key' => 'field_q_tab_special_gallery',
                                'label' => 'Images',
                                'name' => 'tab_special_gallery',
                                'type' => 'gallery',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => array(
                                    array(
                                        array(
                                            'field' => 'field_q_tab_type',
                                            'operator' => '==',
                                            'value' => 'gallery',
                                        ),
                                    ),
                                ),
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'min' => 3,
                                'max' => 12,
                                'insert' => 'append',
                                'library' => 'all',
                                'min_width' => 300,
                                'min_height' => 300,
                                'min_size' => '',
                                'max_width' => '',
                                'max_height' => '',
                                'max_size' => 8,
                                'mime_types' => 'jpg',
                            ),
                            array (
                                'key' => 'field_q_tab_special_script',
                                'label' => 'Embed Script',
                                'name' => 'tab_special_script',
                                'type' => 'textarea',
                                'instructions' => '',
                                'required' => 1,
                                'conditional_logic' => array (
                                    array (
                                        array (
                                            'field' => 'field_q_tab_options',
                                            'operator' => '==',
                                            'value' => 'script',
                                        ),
                                    ),
                                ),
                                'wrapper' => array (
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '',
                                'maxlength' => '',
                                'rows' => 4,
                                'new_lines' => '',
                            ),
                        ),
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'page',
                        ),
                    ),
                    array (
                        array (
                            'param' => 'page_template',
                            'operator' => '==',
                            'value' => 'page.php',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => array(
                    // 0 => 'the_content',
                ),
                'active' => 1,
                'description' => '',
            ),

        );


        // check if we ar returning a single set or all groups ##
        if ( is_null( $group ) ) {

            #helper::log( 'Returning all groups.' );

            return $groups;

        } elseif ( 
            isset( $group ) 
            && is_array( $groups )
            && array_key_exists( $group, $groups )
            && array_key_exists( 'fields', $groups[$group] )
        ) {

            #helper::log( 'returning fields in group: '.$group );

            return $groups[$group]['fields'];

        }

        // nothing cooking ##
        return false;

    }


    /**
    * Handler for meta permalink
    *
    * @since    2.0.0
    */
    public static function meta_post_object_permalink( $value = null, $array = null, $args = null )
    {

        // helper::log( 'post_object_permalink' );
        
        if ( ! $value || is_null( $value ) ) {

            return false;

        }

        if ( is_numeric( $value ) ) {

            // helper::log( 'ID int passed: '.$value );

            if ( $permalink = \get_permalink( $value ) ) {

                // kick it back ##
                return $permalink;

            }

        } 

        if ( is_string( $value ) ) {

            // helper::log( 'Predefined string URL: '.$value );

            return $value;   

        }

        // or nada ##
        return false;

    }


    
    
    /**
     * Tab Filter - script - wysiwyg + textarea with script
     * Note: filters run via seperate class method, filters to only run on this current template
     *
     * @since   2.0.0
     * @return  String
     */
    public static function tab_special_script( $args, $tabs )
    {

        if (
            ! $the_post = wordpress::the_post() 
        ) {

            helper::log( 'No post object...' );

            return false;

        }

        // start with nada ##
        $content = '';

        // helper::log( $args );

        // get markup ##
        $content = $args['markup']['script'];

        // kill ##
        $content = 
            $tabs['text'] ? 
            str_replace( '%string%', $tabs['text'], $content ) : 
            false ;
        
        $content = 
            $tabs['tab_special_script'] ? 
            str_replace( '%script%', $tabs['tab_special_script'], $content ) : 
            false ;

        // test ##
        // helper::log( $content );

        // kick it back ##
        return $content;
 
    }


}