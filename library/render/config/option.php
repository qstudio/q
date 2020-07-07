<?php

namespace q\render\config;

use q\core;
use q\core\helper as h;
use q\plugin; 
// use q\get;
use q\render;

// load it up ##
\q\render\config\option::run();

class option extends render\config {

    // store db query ##
	public static 
		$contexts = [],
		$tasks = []
	;

    /**
    * Class Constructor
    * 
    * @since       1.0
    * @return      void
    */
    public static function run()
    {

		// only on admin ##
		if ( ! \is_admin() ){ return false; }

        // add acf options page ##
        self::acf_add_options_page();
        
		// add ACF fields
        \add_action( 'acf/init', function() { plugin\acf::add_field_groups( self::add_field_groups() ); }, 1 );

		// collapse repeaters ##
		// \add_action( 'acf/input/admin_head', [ get_class(), 'acf_repeater_collapse' ] );

		// add data reset meta box ##
		// \add_action( 'acf/input/admin_head', [ get_class(), 'add_meta_box' ], 10 );

	}
	

	/**
     * Adds the meta box.
	 * https://gist.github.com/RadGH/a1473a24782e93435951ef0f390deb2e
     */
    public static function add_meta_box() {

		// Verify the screen ID
		// if ( ! \acf_is_screen( 'options-general.php?page=q_config' ) ) return;

        \add_meta_box(
            'q-config-meta-box',
            __( 'Data Reset', 'q-textdomain' ),
            array( get_class(), 'meta_box' ),
            'q_config', // acf_options_page
            'advanced',
            'default'
		);
		
    }
 
    /**
     * Renders the meta box.
     */
    public static function meta_box( $post ) {

		h::log( 'Here??' );

		// Add nonce for security and authentication.
		// \wp_nonce_field( 'q_config_nonce_action', 'q_config_nonce' );
		
		echo 'RESET';

    }
 


	/**
	 * Hacky collapse repeaters on load ..
	 * 
	 * @since 4.1.0
	*/
	public static function acf_repeater_collapse() {
?>
		<style id="rdsn-acf-repeater-collapse">.acf-repeater .acf-table {display:none;}</style>
		<script type="text/javascript">
			jQuery(function($) {
				$('.acf-repeater .acf-row').addClass('-collapsed');
				$('#rdsn-acf-repeater-collapse').detach();
			});
		</script>
<?php
	}



	public static function acf_add_options_page()
    {

        if ( ! function_exists( 'acf_add_options_page' ) ) {

            h::log( 'e:>ACF Plugin Missing, please install or activate...' );

            return false;

        }

        // h::log( 'Adding ACF settings page...' );

        \acf_add_options_page( array(
            'page_title' 	=> 'Q : Willow Settings',
            'menu_title'	=> 'Q : Willow',
            'menu_slug' 	=> 'q_config',
            'capability'	=> 'manage_options',
            'parent'        => 'options-general.php',
            'redirect'		=> false
        ));

	}
	


    /**
    * Define field groups
    *
    * @since    2.0.0
    * @return   Mixed
    */
    public static function add_field_groups( $option = 'q_config' )
    {

        // define field groups - exported from ACF ##
        $groups = array (

            array(
				'key' => 'group_'.$option,
				'title' => 'Context Settings for Willow Template Engine',
				'fields' => self::fields_one( $option ), 
				'location' => array(
					array(
						array(
							'param' => 'options_page',
							'operator' => '==',
							'value' => 'q_config',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			)
		);
		

		// logic to get all or single group ##
		return $groups;

    }


	public static function filter_load_value_one( $value, $post_id, $field ) {

		// h::log( $value );

		// get field name ##
		$field = str_replace( 'q_config_', '', $field['name'] );

		if (
			$value
			&& is_array( $value )
		){

			// h::log( 'd:>value already set, so return db value for: '.$field );

			return $value;

		}

		// h::log( 'd:>field: '.$field );

		// check if field name can be exploded ##
		if( strpos( $field, '__' ) === false ){

			h::log( 'd:>field name does not include "__" -> '.$field );

			return $value;

		}

		$field_explode = explode( '__', $field );
		// h::log( $field_explode );

		// load config value ##
		$array = get::value_one( $field_explode[0], $field_explode[1] );
		// h::log( $value );

		// last validate ##
		if( ! $array || ! is_array( $array ) ){

			h::log( 'd:>error in array for __'.$field );

			return $value;

		}

		return $array;

	}



	public static function fields_one( $option = null ){

		// @todo - sanity ##
		if(
			is_null( $option )
		){

			return false;

		}

		// empty array ##
		$array = [];

		// loop over loaded contexts ##
		foreach( get::context() as $context ) {

			// add CONTEXT tab ##
			$array[] = [
				'key' => 'field_'.$option.'_tab_'.$context,
				'label' => strtoupper( $context ),
				'name' => $option.'_tab_'.$context,
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '10',
					'class' => '',
					'id' => '',
				),
				'placement' => 'top',
				'endpoint' => 0,
			];

			// loop over tasks in context ##
			foreach( get::task( $context ) as $task => $value ) {

				// h::log( 'task: '.$task );
				// h::log( 'value: '.$value );

				// filter repeater values ##
				add_filter('acf/load_value/name=q_config_'.$context.'__'.$value, [ get_class(), 'filter_load_value_one' ], 10, 3 );

				$array[] = array(
					'key' => 'field_'.$option.'_'.$context.'__'.$value,
					'label' => strtoupper( $value ) .' ( <a href="#'.$context.'__'.$value.'">Help</a> )',
					'name' => $option.'_'.$context.'__'.$value,
					'type' => 'repeater',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'collapsed' => 'field_key',
					'min' => 0,
					'max' => 0,
					'layout' => 'table',
					'button_label' => 'Add '.ucwords($value) .' Property',
					'sub_fields' => array(
						array(
							'key' => 'field_key',
							'label' => 'Key',
							'name' => 'key',
							'type' => 'text',
							'default_value'	=> '',
							'instructions' => '',
							'required' => 1,
							'placeholder' => 'Task Name',
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '10',
								'class' => '',
								'id' => '',
							),
							'return_format' => 'value',
						),
						array(
							'key' => 'field_value',
							'label' => 'Values',
							'name' => 'field_value',
							'type' => 'repeater',
							'instructions' => '',
							'required' => 1,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '90',
								'class' => '',
								'id' => '',
							),
							'collapsed' => 'field_sub_key',
							'min' => 0,
							'max' => 0,
							'layout' => 'row',
							'button_label' => 'Add Value Property',
							'sub_fields' => array(
								array(
									'key' => 'field_sub_key',
									'label' => 'Key',
									'name' => 'sub_key',
									'type' => 'text',
									'default_value'	=> '',
									'instructions' => '',
									'required' => 1,
									'placeholder'	=> 'Task Key',
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'return_format' => 'value',
								),
								array(
									'key' => 'field_sub_type',
									'label' => 'Type',
									'name' => 'sub_type',
									'type' => 'select',
									'instructions' => '',
									'required' => 1,
									'conditional_logic' => 0,
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'choices' => array(
										'code' => 'Markup',
										'text' => 'Text',
										'boolean' => 'Boolean',
									),
									'default_value' => 'text',
									'allow_null' => 0,
									'multiple' => 0,
									'ui' => 0,
									'return_format' => 'value',
									'ajax' => 0,
									'placeholder' => '',
								),
								array(
									'key' => 'field_sub_value_text',
									'label' => 'Value',
									'name' => 'sub_value_text',
									'type' => 'text',
									'default_value'	=> '',
									'instructions' => '',
									'required' => 1,
									'placeholder'	=> 'Sub Task Value',
									'conditional_logic' => array(
										array(
											array(
												'field' => 'field_sub_type',
												'operator' => '==',
												'value' => 'text',
											),
										),
									),
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'return_format' => 'value',
								),
								array(
									'key' => 'field_sub_value_boolean',
									'label' => 'Value',
									'name' => 'sub_value_boolean',
									'type' => 'true_false',
									'instructions' => '',
									'required' => 0,
									'conditional_logic' => array(
										array(
											array(
												'field' => 'field_sub_type',
												'operator' => '==',
												'value' => 'boolean',
											),
										),
									),
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'message' => 'Set Configuration',
									'default_value' => 1,
									'ui' => 1,
									'ui_on_text' => 'True',
									'ui_off_text' => 'False',
								),
								array(
									'key' => 'field_sub_value_code',
									'label' => 'Value',
									'name' => 'sub_value_code',
									'type' => 'acf_code_field',
									'instructions' => '',
									'required' => 1,
									// 'conditional_logic' => 0,
									'conditional_logic' => array(
										array(
											array(
												'field' => 'field_sub_type',
												'operator' => '==',
												'value' => 'code',
											),
										),
									),
									'wrapper' => array(
										'width' => '',
										'class' => '',
										'id' => '',
									),
									'default_value' => '',
									'placeholder' => 'Sub Task Value',
									'mode' => 'htmlmixed',
									'theme' => 'monokai',
									'return_format' => 'value',
								),
							)

						)
					)

				);

			}

			$array[] = array(
				'key' => 'field_'.$option.'_'.$context.'__NEW_TASK',
				'label' => 'NEW TASK',
				'name' => $option.'_'.$context.'__NEW_TASK',
				'type' => 'repeater',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'collapsed' => 'field_key',
				'min' => 0,
				'max' => 0,
				'layout' => 'table',
				'button_label' => 'Add NEW TASK Property',
				'sub_fields' => array(
					array(
						'key' => 'field_key',
						'label' => 'Key',
						'name' => 'key',
						'type' => 'text',
						'default_value'	=> '',
						'instructions' => '',
						'required' => 1,
						'placeholder' => 'Task Name',
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '10',
							'class' => '',
							'id' => '',
						),
						'return_format' => 'value',
					),
					array(
						'key' => 'field_value',
						'label' => 'Values',
						'name' => 'field_value',
						'type' => 'repeater',
						'instructions' => '',
						'required' => 1,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '90',
							'class' => '',
							'id' => '',
						),
						'collapsed' => 'field_sub_key',
						'min' => 0,
						'max' => 0,
						'layout' => 'row',
						'button_label' => 'Add Value Property',
						'sub_fields' => array(
							array(
								'key' => 'field_sub_key',
								'label' => 'Key',
								'name' => 'sub_key',
								'type' => 'text',
								'default_value'	=> '',
								'instructions' => '',
								'required' => 1,
								'placeholder'	=> 'Task Key',
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'return_format' => 'value',
							),
							array(
								'key' => 'field_sub_type',
								'label' => 'Type',
								'name' => 'sub_type',
								'type' => 'select',
								'instructions' => '',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'choices' => array(
									'code' => 'Markup',
									'text' => 'Text',
									'boolean' => 'Boolean',
								),
								'default_value' => 'code',
								'allow_null' => 0,
								'multiple' => 0,
								'ui' => 0,
								'return_format' => 'value',
								'ajax' => 0,
								'placeholder' => '',
							),
							array(
								'key' => 'field_sub_value_text',
								'label' => 'Value',
								'name' => 'sub_value_text',
								'type' => 'text',
								'default_value'	=> '',
								'instructions' => '',
								'required' => 1,
								'placeholder'	=> 'Sub Task Value',
								'conditional_logic' => array(
									array(
										array(
											'field' => 'field_sub_type',
											'operator' => '==',
											'value' => 'text',
										),
									),
								),
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'return_format' => 'value',
							),
							array(
								'key' => 'field_sub_value_boolean',
								'label' => 'Value',
								'name' => 'sub_value_boolean',
								'type' => 'true_false',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => array(
									array(
										array(
											'field' => 'field_sub_type',
											'operator' => '==',
											'value' => 'boolean',
										),
									),
								),
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'message' => 'Set Configuration',
								'default_value' => 1,
								'ui' => 1,
								'ui_on_text' => 'True',
								'ui_off_text' => 'False',
							),
							array(
								'key' => 'field_sub_value_code',
								'label' => 'Value',
								'name' => 'sub_value_code',
								'type' => 'acf_code_field',
								'instructions' => '',
								'required' => 1,
								// 'conditional_logic' => 0,
								'conditional_logic' => array(
									array(
										array(
											'field' => 'field_sub_type',
											'operator' => '==',
											'value' => 'code',
										),
									),
								),
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => 'Sub Task Value',
								'mode' => 'htmlmixed',
								'theme' => 'monokai',
								'return_format' => 'value',
							),
						)

					)
				)

			);

		}

		// h::log( $array );

		return $array;

	}


}
