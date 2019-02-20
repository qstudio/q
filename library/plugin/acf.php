<?php

namespace q\plugin;

use q\core\core as core;
use q\core\helper as helper;

// load it up ##
\q\plugin\acf::run();

class acf extends \Q {

    public static function run()
    {

        // add fields ##
        \add_action( 'acf/init', array( get_class(), 'add_fields' ), 1 );

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

            /*
            'generic' => array (
                'key' => 'group_595ebc43b8cc9',
                'title' => 'Generic',
                'fields' => array (
                    array (
                        'key' => 'field_595ebc7b56b64',
                        'label' => 'Parent',
                        'name' => 'generic_parent',
                        'type' => 'post_object',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array (
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'post_type' => array (
                            0 => 'page',
                        ),
                        'taxonomy' => array (
                        ),
                        'allow_null' => 0,
                        'multiple' => 0,
                        'return_format' => 'id',
                        'ui' => 1,
                    ),
                ),
                'location' => array (
                    array (
                        array (
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'page',
                        ),
                    ),
                    array( 
                        array (
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'grant',
                        ),
                    )
                ),
                'menu_order' => 0,
                'position' => 'side',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => 1,
                'description' => '',
            )
            */

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

}