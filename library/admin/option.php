<?php

namespace q\admin;

use q\core;
use q\core\helper as h;
use q\plugin; 
use q\get;

// date ##
use \Datetime;

// load it up ##
\q\admin\option::run();

class option extends \Q {

    // store db query ##
    public static $query = false;

    /**
    * Class Constructor
    * 
    * @since       1.0
    * @return      void
    */
    public static function run()
    {

        // add acf options page ##
        self::acf_add_options_page();
        
		// add ACF fields
        \add_action( 'acf/init', function() { plugin\acf::add_field_groups( self::add_field_groups() ); }, 1 );

        // example how to inject extra options in libraries select API ##
        // \add_filter( 'acf/load_field/name=q_option_library', [ get_class(), 'filter_acf_library' ], 10, 1 );

        // add link to view library from
		\add_filter( 'acf/load_field/name=q_option_library', [ get_class(), 'filter_acf_library' ], 100, 1 );

		// add direct link to Q settings to admin bar ##
		\add_action( 'admin_bar_menu', [ get_class(), 'admin_bar_menu' ], 999, 1 );
		
		// run action on acf options save ##
		\add_action( 'acf/save_post', [ get_class(), '_save_modules' ], 20 );
        
	}
	

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
     * API to add fields to Q settings
     * 
     * @since 2.3.0
     */
    public static function _save_modules( $post_id )
    {

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
			$q_modules['js'] = [];

			if( 
				$values 
				&& is_array( $values )
				&& array_key_exists( 'q_option_module', $values ) // individual modules ##
				&& array_key_exists( 'q_option_module_asset', $values ) // ui asset controllers - css || js in field 'q_option_module_asset'
			){

				// h::log( $values );

				// check theme get method ##
				if( 
					! class_exists( 'q_theme' )
					|| ! method_exists( 'q_theme', 'get_parent_theme_path' )
					|| ! is_callable( [ 'q_theme', 'get_parent_theme_path' ]  )  
				){

					h::log( 'e:>q_theme::get_parent_theme_path() is not available' );

					return false;

				}

				// list of parent and child theme requirements for check ##
				$options = [

					// css / scss ##
					'scss'				=> in_array( 'css', $values['q_option_module_asset'] ), // get from passed values ##
					'source_scss'		=> self::get_plugin_path( 'library/_source/scss/module/' ),
					'destination_scss'	=> \q_theme::get_parent_theme_path( '/library/_source/scss/module/' ),
					'file_scss'			=> 'index.scss',

					// js ##
					'js'				=> in_array( 'js', $values['q_option_module_asset'] ), // get from passed values ##
					'source_js'			=> self::get_plugin_path( 'library/_source/js/module/' ),
					'destination_js'	=> \q_theme::get_parent_theme_path( '/library/_source/js/module/' ),
					'file_js'			=> 'readme.md',
					
				];

				// h::log( $options );

				// we can also store active module list, for use later when enqueing JS assets ##
				// $js_modules = [];

				// date for comments ##
				$now = new \DateTime();

				// ---- CSS first ## 

				// destination index.scss ##
				$file_scss = $options['destination_scss'].$options['file_scss'];

				// if user has disabled css - we need to delete all scss modules ##
				if ( ! $options['scss'] ) {
					
					// empty scss destination folder ##
					\q\admin\method::empty_directory( $options['destination_scss'] );

					// datestamp the index.scss file ##
					$list = "/* Q Studio ~ SCSS Modules : EMPTIED --> ".$now->format('Y-m-d H:i:s')." */\r\n";

					// write to file ##
					file_put_contents( $file_scss, $list );

				// user has activated scss modules ##
				} else {

					// check for theme/xx/modules/index.scss
					if( 
						! file_exists( $file_scss ) 
					){

						h::log( 'd:>'.$options['destination_scss'].$options['file_scss'].' file NOT found - it will be created' );

					}

					// datestamp the index.scss file ##
					$list = "/* Q Studio ~ SCSS Modules --> ".$now->format('Y-m-d H:i:s')." */\r\n";

					// copy scss files from q/_source/scss/module/ -> q_theme/_source/scss/modules/ ##
					// note, only copies over files which exist from active module list ##
				
					// add "_$FILE.js" to each module ##
					$scss_modules = array_map( function ($module) { return "_$module.scss"; }, $values['q_option_module'] );
				
					// h::log( $scss_modules );
					\q\admin\method::copy_files([
						'source' 		=> $options['source_scss'],
						'destination' 	=> $options['destination_scss'],
						'files'			=> $scss_modules
					]);

					// loop over active modules ##
					foreach( $values['q_option_module'] as $module ){

						// check for each module file in theme/xx/_source/scss/modules/_$module.scss
						// h::log( 'Checking module: '.$module );

						// check for theme/xx/modules/index.scss
						$scss_module = $options['destination_scss'].'_'.$module.'.scss';
						if( 
							$scss_module
							&& file_exists( $scss_module ) 
						){

							// h::log( $options['destination_scss'].'_'.$module.'.scss ~ file exists' );

							// store ##
							$q_modules['scss'][] = $module;

							// if module found, add name to list to write to index.scss
							$list .= "@forward '".$module."';\r\n";

						}

					}

					// write to file ##
					file_put_contents( $file_scss, $list );

				}

				// --- now JS ##

				// destination index.scss ##
				$file_js = $options['destination_js'].$options['file_js'];

				// if user has disabled css - we need to delete all scss modules ##
				if ( ! $options['js'] ) {

					// h::log('e:>JS disabled, so empty _source');
					
					// empty destination folder ##
					\q\admin\method::empty_directory( $options['destination_js'] );

					// datestamp the index.scss file ##
					$list = "### Q Studio ~ JS Modules\r\n";
					$list .= "Emptied: ".$now->format('Y-m-d H:i:s')."\r\n";

					// write to file ##
					file_put_contents( $file_js, $list );

				// user has activated scss modules ##
				} else {

					// check for theme/xx/modules/index.scss
					if( 
						! file_exists( $file_js ) 
					){

						h::log( 'd:>'.$options['destination_js'].$options['file_js'].' file NOT found - it will be created' );

					}

					// datestamp the readme.md file ##
					$list = "### Q Studio ~ JS Modules\r\n";
					$list .= "Updated: ".$now->format('Y-m-d H:i:s')."\r\n";
					$list .= "JS Modules should not be edited directly, as changes will be lost the next time you update Q Settings.\r\n";

					// copy scss files from q/_source/scss/module/ -> q_theme/_source/scss/modules/ ##
					// note, only copies over files which exist from active module list ##
				
					// add "$FILE.js" to each module ##
					$js_modules = array_map( function ($module) { return "$module.js"; }, $values['q_option_module'] );
				
					// h::log( $scss_modules );
					\q\admin\method::copy_files([
						'source' 		=> $options['source_js'],
						'destination' 	=> $options['destination_js'],
						'files'			=> $js_modules
					]);

					// loop over active modules ##
					foreach( $values['q_option_module'] as $module ){

						// check for each module file in theme/xx/_source/scss/modules/_$module.scss
						// h::log( 'Checking module: '.$module );

						// check for theme/xx/modules/index.scss
						$js_module = $options['destination_js'].$module.'.js';
						if( 
							$js_module
							&& file_exists( $js_module ) 
						){

							// h::log( $options['destination_js'].$module.'.js ~ file exists' );

							// store ##
							$q_modules['js'][] = $module;

						}

					}

					// write to file ##
					file_put_contents( $file_js, $list );

				}

				h::log( $q_modules );

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
		if ( get\theme::is_child() ){

			// all assets are loading from parent
			// h::log( 'e:>current theme is child.' );

		}

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
			$min = self::$debug ? '' : '.min' ;

			// complete file ##
			$file_lookup = "vendor/".$type_dir."/".$type[1].$min.".".$type_ext;

			// h::log( 'd:>File lookup: '.$file_lookup );

			// default ##
			$file = false;

			// look for minified library -- only if debugging ##
            // if ( self::$debug ) {
			
			// 	$file = h::get( "asset/".$type_dir."/vendor/".$type[1].".min.".$type_ext, 'return' );

			// }

            // if not debugging, check if we can find a non-min version ##
            // if ( 
				$file = h::get( $file_lookup, 'return' ) ;
                // ! $file
                // ||
                // (
                    // self::$debug 
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
			if ( false === strpos( $file, 'q-parent' ) ) { $location = 'Child'; }

            // h::log( 'd:>Adding library: '.$handle.' with file: '.$file.' as type: '.$type_ext );

            // Add link to view ##
            $array[$key] = '<strong>'.$value.'</strong> from '.$location.' ( <a href="'.$file.'" target="_blank">view</a> )';

        }

        // replace array ##
        $field['choices'] = $array;

        // kick it all back ##
        return $field;

    }




    public static function acf_add_options_page()
    {

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
                        'instructions' => 'Assets are generated by <a href="#">theme modules</a>, and can be extended from a child theme.<br />JS and SCSS assets are copied to the parent theme for compilation and inclusion.',
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
					array(
                        'key' => 'field_q_option_theme_child',
                        'label' => 'Child Theme -> '.\wp_get_theme(),
                        'name' => 'q_option_theme_child',
                        'type' => 'checkbox',
                        'instructions' => 'Valid, if using a <a href="https://developer.wordpress.org/themes/advanced-topics/child-themes/" target="_blank">child theme</a>',
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
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                // 'description' => 'Control how Q loads assets from the theme or plugins.',
            ), 

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

            'q_option_extension'   => array(
                'key' => 'group_q_option_extension',
                'title' => 'Extensions',
                'fields' => array(
                    array(
                        'key' => 'field_q_option_extension',
                        'label' => 'Feature Extensions',
                        'name' => 'q_option_extension',
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
                'menu_order' => 2,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => '',
			),

			'q_option_module'   => array(
				'key' => 'group_q_option_module',
				'title' => 'Modules',
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
				'menu_order' => 2,
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



}
