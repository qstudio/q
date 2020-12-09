<?php

namespace q\admin;

use q\plugin as q;
use q\core;
use q\core\helper as h;
use q\get;

// date ##
use \Datetime;

class option {

    // store db query ##
	public static $query = false;
	
	function __construct(){}

    /**
    * Class Constructor
    * 
    * @since       1.0
    * @return      void
    */
    function hooks(){

        // add acf options page ##
        self::acf_add_options_page();
        
		// add ACF fields
        \add_action( 'acf/init', function() { \q\plugins\acf::add_field_groups( self::add_field_groups() ); }, 1 );

        // example how to inject extra options in libraries select API ##
        // \add_filter( 'acf/load_field/name=q_option_library', [ get_class(), 'filter_acf_library' ], 10, 1 );

        // add link to view library from
		\add_filter( 'acf/load_field/name=q_option_library', [ get_class(), 'filter_acf_library' ], 100, 1 );

		// add direct link to Q settings to admin bar ##
		\add_action( 'admin_bar_menu', [ get_class(), 'admin_bar_menu' ], 999, 1 );
		
		// run action on acf options save ##
		\add_action( 'acf/save_post', [ get_class(), '_save_modules' ], 20 );

		// Apply to all fields.
		// \add_action( 'acf/render_field/key=group_q_option_module', [ get_class(), '_filter_modules' ], 10, 1 );

	}



	/**
	 * Add Q Settings to main WP admin bar menu
	 * 
	*/
	public static function admin_bar_menu($admin_bar) {       

		$args = array(
			'parent' => 'site-name',
			'id'     => 'q',
			'title'  => 'Q ~ Settings',
			'href'   => \esc_url( \admin_url( 'options-general.php?page=q' ) ),
			'meta'   => false
		);
		$admin_bar->add_node( $args );       

	}



    /**
     * API run when Q settings options are saved
     * 
     * @since 2.3.0
     */
    public static function _save_modules( $post_id ){

		// admin only ##
		if( 
			! \is_admin() 
			|| ! function_exists( 'get_current_screen' )
		){
			
			return false;

		}

		// get current screen ##
		$screen = \get_current_screen();
		
		// h::log( $screen );
		if ( false !== strpos( $screen->id, "settings_page_q")  ) {

			// h::log( 'e:>Saving Q options' );

			// Get newly saved values.
			$values = \get_fields( $post_id );
			// h::log( $values );

			// store modules ##
			$q_module = [];
			$q_modules['scss'] = [];
			$q_modules['scss_path'] = [];
			$q_modules['javascript'] = [];
			$q_modules['javascript_path'] = [];

			if( 
				$values 
				&& is_array( $values )
				&& array_key_exists( 'q_option_module', $values ) // individual modules ##
				&& array_key_exists( 'q_option_module_asset', $values ) // ui asset controllers - css || js in field 'q_option_module_asset'
			){

				h::log( $values );

				// check theme get method ##
				if( 
					! class_exists( 'q_theme' )
					|| ! method_exists( 'q_theme', 'get_parent_theme_path' )
					|| ! is_callable( [ 'q_theme', 'get_parent_theme_path' ]  )  
				){

					h::log( 'e:>q_theme::get_parent_theme_path() is not available' );

					return false;

				}

				// list of parent and child theme requirements to check ##
				$options = [

					// css / scss ##
					'scss'				=> in_array( 'css', $values['q_option_module_asset'] ), // get from passed values ##
					'child_scss'		=> \q_theme::get_child_theme_path( '/library/_source/scss/module/' ),
					'parent_scss'		=> \q_theme::get_parent_theme_path( '/library/_source/scss/module/' ),
					'parent_path'		=> 'q-theme-parent/library/_source/scss/module/',
					'file_scss'			=> 'index.scss',

					// js -- rename modules which are inactive to "_FILENAME" ##
					'js'				=> in_array( 'js', $values['q_option_module_asset'] ), // get from passed values ##
					'source_js'			=> [ 
						'child'			=> \q_theme::get_child_theme_path( '/library/_source/js/module/' ),
						'parent'		=> \q_theme::get_parent_theme_path( '/library/_source/js/module/' )
					],
					'asset_js'			=> \q_theme::get_parent_theme_path( '/library/asset/js/module/' ),
					'file_module'		=> 'q.module.json'
					
				];

				// h::log( $options );

				// date for comments ##
				$now = new \DateTime();

				// ---- SCSS first ## 

				// tracker ##
				$modules_scss_added = [];

				// datestamp the index.scss file ##
				$list = "/* Q Studio ~ SCSS Modules --> ".$now->format('Y-m-d H:i:s')." */\r\n";
				
				// _source/scss/module/index.scss ##
				$path_child = $options['child_scss'];
				$path_parent = $options['parent_scss'];
				$file_scss = $path_child.$options['file_scss'];

				// if user has disabled css - we need to delete all scss module references ##
				if ( ! $options['scss'] ) {
					
					// datestamp the index.scss file ##
					$list = "/* Q Studio ~ SCSS Modules : EMPTIED --> ".$now->format('Y-m-d H:i:s')." */\r\n";

					// write to file ##
					file_put_contents( $file_scss, $list );

				// user has activated scss modules ##
				} else {

					// check for theme/xx/_source/scss/module/index.scss
					if( 
						! file_exists( $file_scss ) 
					){

						h::log( 'd:>'.$file_scss.' file NOT found - it will be created' );

					}

					// check for each module file in themes/q-theme-child/library/_source/scss/modules/_$module.scss
					// saving module/index.scss in child, but listing relative paths to parent files
					foreach( $values['q_option_module'] as $module ){

						// check for themes/CHILD/_source/scss/module/_MODULE.scss
						$scss_module = $path_child.'_'.$module.'.scss';
						// h::log( 'Checking CHILD module: '.$scss_module );

						if( 
							$scss_module
							&& file_exists( $scss_module ) 
						){

							// h::log( $path_child.$module.'.scss ~ file in CHILD theme' );

							// avoid duplicate values in child / parent keys ##
							if( in_array( $module, $modules_scss_added ) ){

								// h::log( $path.'_'.$module.'.scss ~ already added, skipping' );

								continue;

							}

							// store ##
							$q_modules['scss'][] = $module;
							$q_modules['scss_path'][] = $path_child.'_'.$module.'.scss';

							// if module found, add name to list to write to index.scss
							$list .= "@forward '".$module."'; // child module \r\n";

							// track ##
							$modules_scss_added[] = $module;

							// go to next ...
							continue;

						}

						// check for themes/PARENT/_source/scss/module/_MODULE.scss
						$scss_module = $path_parent.'_'.$module.'.scss';
						// h::log( 'Checking PARENT module: '.$scss_module );

						if( 
							$scss_module
							&& file_exists( $scss_module ) 
						){

							// h::log( $path_parent.$module.'.scss ~ file in PARENT theme' );

							// avoid duplicate values in child / parent keys ##
							if( in_array( $module, $modules_scss_added ) ){

								// h::log( $path.'_'.$module.'.scss ~ already added, skipping' );

								continue;

							}

							// store ##
							$q_modules['scss'][] = $module;
							$q_modules['scss_path'][] = $path_parent.'_'.$module.'.scss';

							// if module found, add name to list to write to index.scss
							$list .= "@forward '".$options['parent_path'].$module."'; // parent module \r\n";

							// track ##
							$modules_scss_added[] = $module;

							// go to next ...
							continue;

						}

					}

					// write to index.scss ##
					file_put_contents( $file_scss, $list );

				}

				// --- now JS ##

				// push in localize script ##
				$q_modules['javascript'][] = '__q';
				$q_modules['javascript_path'][] = \esc_html( \q_theme::get_parent_theme_path( '/library/_source/js/module/__q.js' ) );

				// if user has disabled js - nothing to do... ##
				if ( ! $options['js'] ) {


				// user has activated scss modules ##
				} else {

					// tracker ##
					$modules_js_added = [];

					// we have to loop over child + parent settings - saving module/index.scss in each
					foreach( $options['source_js'] as $source => $path ){

						// h::log( $copy_log );

						// loop over active modules ##
						foreach( $values['q_option_module'] as $module ){

							// check for each module file in theme/xx/_source/scss/modules/_$module.scss
							// h::log( 'Checking module: '.$path.$module.'.js' );

							// check for theme/xx/asset/js/module/_MODULE.js
							$js_module = $path.$module.'.js';
							if( 
								$js_module
								&& file_exists( $js_module ) 
							){

								// h::log( $path.$module.'.js ~ file exists' );

								// avoid duplicate values in child / parent keys ##
								if( in_array( $module, $modules_js_added ) ){

									// h::log( $path.$module.'.js ~ already added, skipping' );

									continue;

								}

								// store ##
								$q_modules['javascript'][] = $module;
								$q_modules['javascript_path'][] = \esc_html( $path.$module.'.js' );

								// track ##
								$modules_js_added[] = $module;

							}

						}

					}

				}

				// h::log( $q_modules );

				// module list in MD format "* module.js"
				$q_modules_json = $q_modules;
				// h::log( $q_modules_json );

				// write to q.modules.json ##
				$q_modules_json = json_encode( $q_modules_json );

				// h::log( $q_modules_json );

				// save to child theme, as this where Grunt will load it for task runners ##
				file_put_contents( \q_theme::get_child_theme_path('/q.module.json' ), $q_modules_json );

				// store active modules list ##
				core\method::add_update_option( 'q_modules', $q_modules, '', 'yes' );

			}

		}

    }



    /**
     * API to add fields to Q settings
     * 
     * @since 2.3.0
     */
    public static function api( Array $args = null )
    {

        // @todo ... but should be a wrapper to: 
        // \add_filter( 'acf/load_field/name=q_option_library', [ get_class(), 'filter_acf_library' ], 1000000, 1 );

    }



    
    /**
     * Add view to link assets from backend
     * 
     * @since 2.3.0
     */
    public static function filter_acf_library( $field )
    {

		// h::log( 'd:>here..' );
		// h::log( $field['choices'] );
		
		// we need to check for the assets in child and parent theme ##
		// so check we have access to q_theme
		if ( ! class_exists( "q_theme") ) {

			h::log( 'e:>q_theme not available.' );

			return false;

		}

		// check if there is a child theme active ##
		// if ( get\theme::is_child() ){

			// all assets are loading from parent
			// h::log( 'e:>current theme is child.' );

		// }

        // empty array ##
        $array = [];

        foreach( $field['choices'] as $key => $value ) {

			// h::log( 'working: '.$key );
			$location = "Parent";

            // add to array ##
            $array[$key] = $value; 

            // CSS or JS
            $type = explode( "_" , $key );

            // if no type - skip ##
            if ( 
                ! is_array( $type ) 
                || 2 > count( $type )
            ) {

                // h::log( 'Skipping: '.$key );

                continue;

            }

            $type_dir = ( 'css' == $type[0] ) ? 'css' : 'js' ;
            $type_ext = ( 'css' == $type[0] ) ? 'css' : 'js' ;

            // give it a handle ##
			$handle = 'q-'.$key;

			// min if debugging ##
			$min = q::$_debug ? '' : '.min' ;

			// complete file ##
			$file_lookup = "vendor/".$type_dir."/".$type[1].$min.".".$type_ext;

			// h::log( 'd:>File lookup: '.$file_lookup );

			// default ##
			$file = false;

			// look for minified library -- only if debugging ##
            // if ( q::$_debug ) {
			
			// 	$file = h::get( "asset/".$type_dir."/vendor/".$type[1].".min.".$type_ext, 'return' );

			// }

            // if not debugging, check if we can find a non-min version ##
            // if ( 
				$file = h::get( $file_lookup, 'return' ) ;
                // ! $file
                // ||
                // (
                    // q::$_debug 
                    // && h::get( "asset/".$type_dir."/vendor/".$type[1].".".$type_ext, 'return' )
                // )
            // ) {

                // $file = h::get( $file_lookup, 'return' ) ;

            // }

            // if no type - skip ##
            if ( ! $file ) {

                h::log( 'd:>Skipping: '.$file_lookup.' - File missing...' );

                continue;

			}
			
			// find location ##
			if ( false === strpos( $file, 'q-theme-parent' ) ) { $location = 'Child'; }

            // h::log( 'd:>Adding library: '.$handle.' with file: '.$file.' as type: '.$type_ext );

            // Add link to view ##
            $array[$key] = '<strong>'.$value.'</strong> from '.$location.' ( <a href="'.$file.'" target="_blank">view</a> )';

        }

        // replace array ##
        $field['choices'] = $array;

        // kick it all back ##
        return $field;

    }




    public static function acf_add_options_page(){

        if ( ! function_exists( 'acf_add_options_page' ) ) {

            h::log( 'e:>ACF Plugin Missing, please install or activate...' );

            return false;

        }

        // h::log( 'Adding ACF settings page...' );

        \acf_add_options_page( array(
            'page_title' 	=> 'Q ~ Settings',
            'menu_title'	=> 'Q',
            'menu_slug' 	=> 'q',
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
    public static function add_field_groups()
    {

        // define field groups - exported from ACF ##
        $groups = array (

            'q_option_analytics' => array(
                'key' => 'group_q_option_analytics',
                'title' => 'Analytics and Marketing',
                'fields' => array(
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'q',
                        ),
                    ),
                ),
                'menu_order' => 3,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
            ),

            'q_option_ui' => array(
                'key' => 'group_q_option_ui',
                'title' => 'Asset Inclusion',
                'fields' => array(
					array(
                        'key' => 'field_q_option_module_asset',
                        'label' => 'Module Assets',
                        'name' => 'q_option_module_asset',
                        'type' => 'checkbox',
                        'instructions' => 'Assets are generated by <a href="#">theme modules</a>, and can be extended from a child theme by copying to the child.',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'css' => 'CSS',
                            'js' => 'JS',
                        ),
                        'allow_null' => 0,
                        'other_choice' => 0,
                        'default_value' => array(
							0 => 'css',
							1 => 'js'
						),
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                        'save_other_choice' => 0,
					),
					/*
					array(
                        'key' => 'field_q_option_theme_parent',
                        'label' => 'Parent Theme -> '.\wp_get_theme()->parent(),
                        'name' => 'q_option_theme_parent',
                        'type' => 'checkbox',
                        'instructions' => 'Parent themes define features and UI, which can be extended by Child Themes',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'css' => 'CSS',
                            'js' => 'JS',
                        ),
                        'allow_null' => 0,
                        'other_choice' => 0,
                        'default_value' => array(
							0 => 'css',
							1 => 'js'
						),
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                        'save_other_choice' => 0,
					),
					*/
					array(
                        'key' => 'field_q_option_theme_child',
                        'label' => 'Child Theme -> '.\wp_get_theme(),
                        'name' => 'q_option_theme_child',
                        'type' => 'checkbox',
                        'instructions' => 'Valid when using a <a href="https://developer.wordpress.org/themes/advanced-topics/child-themes/" target="_blank">child theme</a>',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'css' => 'CSS',
                            'js' => 'JS',
                        ),
                        'allow_null' => 0,
                        'other_choice' => 0,
                        'default_value' => array(
							0 => 'css',
							1 => 'js'
						),
                        'layout' => 'horizontal',
                        'return_format' => 'value',
                        'save_other_choice' => 0,
					),


					array(
                        'key' => 'field_q_option_library',
                        'label' => 'Local',
                        'name' => 'q_option_library',
                        'type' => 'checkbox',
                        'instructions' => 'Learn to add additional <a href="#" target="_blank">Local Assets</a>',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(),
                        'allow_custom' => 0,
                        'default_value' => array(
                            1 => 'js_q.global',
                        ),
                        'layout' => 'vertical',
                        'toggle' => 1,
                        'return_format' => 'value',
                        'save_custom' => 0,
                    ),

                    array(
                        'key' => 'field_q_option_external',
                        'label' => 'External',
                        'name' => 'q_option_external',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => 'field_q_option_external_title',
                        'min' => 0,
                        'max' => 10,
                        'layout' => 'block',
                        'button_label' => 'ADD',
                        'sub_fields' => array(
                            
                            array(
                                'key' => 'field_q_option_external_type',
                                'label' => 'Type',
                                'name' => 'type',
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
                                    'css'   => 'CSS',
                                    'js'    => 'Javascript',
                                    // 'font'  => 'Font',
                                ),
                                'default_value' => 'css',
                                'allow_null' => 0,
                                'multiple' => 0,
                                'ui' => 0,
                                'ajax' => 0,
                                'placeholder' => '',
                                'disabled' => 0,
                                'readonly' => 0,
                                'return_format' => 'value',
                            ),

                            array(
                                'key' => 'field_q_option_external_title',
                                'label' => 'Title',
                                'name' => 'title',
                                'type' => 'text',
                                // 'instructions' => 'File Handle',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => 'Font Awesome',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                                'readonly' => 0,
                                'disabled' => 0,
                            ),

                            array(
                                'key' => 'field_q_option_external_version',
                                'label' => 'Version',
                                'name' => 'version',
                                'type' => 'text',
                                // 'instructions' => 'File Handle',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '5.5.0',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                                'readonly' => 0,
                                'disabled' => 0,
                            ),

                            array(
                                'key' => 'field_q_option_external_url',
                                'label' => 'URL',
                                'name' => 'url',
                                'type' => 'url',
                                // 'instructions' => 'Enter Full URL',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => 'https://use.fontawesome.com/releases/v5.5.0/css/all.css',
                                'placeholder' => '',
                            ),

                        ),

                    ),

                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'q',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'side',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                // 'description' => 'Control how Q loads assets from the theme or plugins.',
            ), 

			/*
            'q_option_library' => array(
                'key' => 'group_q_option_library',
                'title' => 'Assets',
                'fields' => array(
                    array(
                        'key' => 'field_q_option_library',
                        'label' => 'Local',
                        'name' => 'q_option_library',
                        'type' => 'checkbox',
                        'instructions' => 'Learn to add additional <a href="#" target="_blank">Local Assets</a>',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(),
                        'allow_custom' => 0,
                        'default_value' => array(
                            1 => 'js_q.global',
                        ),
                        'layout' => 'vertical',
                        'toggle' => 1,
                        'return_format' => 'value',
                        'save_custom' => 0,
                    ),

                    array(
                        'key' => 'field_q_option_external',
                        'label' => 'External',
                        'name' => 'q_option_external',
                        'type' => 'repeater',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => 'field_q_option_external_title',
                        'min' => 0,
                        'max' => 10,
                        'layout' => 'block',
                        'button_label' => 'ADD',
                        'sub_fields' => array(
                            
                            array(
                                'key' => 'field_q_option_external_type',
                                'label' => 'Type',
                                'name' => 'type',
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
                                    'css'   => 'CSS',
                                    'js'    => 'Javascript',
                                    // 'font'  => 'Font',
                                ),
                                'default_value' => 'css',
                                'allow_null' => 0,
                                'multiple' => 0,
                                'ui' => 0,
                                'ajax' => 0,
                                'placeholder' => '',
                                'disabled' => 0,
                                'readonly' => 0,
                                'return_format' => 'value',
                            ),

                            array(
                                'key' => 'field_q_option_external_title',
                                'label' => 'Title',
                                'name' => 'title',
                                'type' => 'text',
                                // 'instructions' => 'File Handle',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => 'Font Awesome',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                                'readonly' => 0,
                                'disabled' => 0,
                            ),

                            array(
                                'key' => 'field_q_option_external_version',
                                'label' => 'Version',
                                'name' => 'version',
                                'type' => 'text',
                                // 'instructions' => 'File Handle',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '5.5.0',
                                'placeholder' => '',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                                'readonly' => 0,
                                'disabled' => 0,
                            ),

                            array(
                                'key' => 'field_q_option_external_url',
                                'label' => 'URL',
                                'name' => 'url',
                                'type' => 'url',
                                // 'instructions' => 'Enter Full URL',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => 'https://use.fontawesome.com/releases/v5.5.0/css/all.css',
                                'placeholder' => '',
                            ),

                        ),

                    ),


                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'options_page',
                            'operator' => '==',
                            'value' => 'q',
                        ),
                    ),
                ),
                'menu_order' => 2,
                'position' => 'side',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
			),
			*/

			'q_option_module'   => array(
				'key' => 'group_q_option_module',
				'title' => 'Modules & Plugins',
				'fields' => array(
					array(
						'key' => 'field_q_option_module',
						'label' => 'UI Modules',
						'name' => 'q_option_module',
						'type' => 'checkbox',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'choices' => array(),
						'allow_custom' => 0,
						'default_value' => array(),
						'layout' => 'vertical',
						'toggle' => 0,
						'return_format' => 'value',
						'save_custom' => 0,
					),
	
				),
				'location' => array(
					array(
						array(
							'param' => 'options_page',
							'operator' => '==',
							'value' => 'q',
						),
					),
				),
				'menu_order' => 1,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => 'Extend applications with modules and plugins, integrated into Willow',
			)
			
		);


		// logic to get all or single group ##
		return $groups;

    }



}
