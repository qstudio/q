<?php

/**
 * Forked from:
 * @author   Darren Cooney
 * @link     https://github.com/dcooney/wordpress-plugin-installer
 */

namespace q\api\plugin;

// security check ##
if ( ! defined( 'ABSPATH' ) ) exit;

use q\core;
use q\core\helper as h;

// load it up ##
\q\api\plugin\install::run();

class install extends \Q {

	public function run(){

		\add_action( 'admin_enqueue_scripts', [ get_class(), 'enqueue_scripts' ] ); // Enqueue scripts and Localize

		\add_action( 'wp_ajax_api_install', [ get_class(), 'install' ] ); // Install plugin

		\add_action( 'wp_ajax_api_activate', [ get_class(), 'activate' ] ); // Activate plugin

	}


	/*
	* init
	* Initialize the display of the plugins.
	*
	*
	* @param $plugin            Array - plugin data
	*
	* @since 1.0
	*/
	public static function init( $plugins ){ ?>

		<div class="q-plugin-install">
<?php

		require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		foreach($plugins as $plugin) :

			$button_classes = 'install button';
			$button_text = __('Install Now', 'q-textdomain');

			$api = plugins_api( 'plugin_information',
				array(
					'slug' => sanitize_file_name($plugin['slug']),
					'fields' => array(
						'short_description' => true,
						'sections' => false,
						'requires' => false,
						'downloaded' => true,
						'last_updated' => false,
						'added' => false,
						'tags' => false,
						'compatibility' => false,
						'homepage' => false,
						'donate_link' => false,
						'icons' => true,
						'banners' => true,
					),
				)
			);

			// h::log($api);

			if ( ! \is_wp_error( $api ) ) { // confirm error free

				// Get main plugin file ##
				$main_plugin_file = self::get_plugin_file( $plugin['slug'] );

				// echo $main_plugin_file; ##
				if( self::check_file_extension( $main_plugin_file ) ){ // check file extension

					if( \is_plugin_active( $main_plugin_file ) ){

	      	            // plugin activation, confirmed!
	                  	$button_classes = 'button disabled';
						$button_text = __('Activated', 'q-textdomain' );

					} else {

						// It's installed, let's activate it
	                  	$button_classes = 'activate button button-primary';
						$button_text = __('Activate', 'q-textdomain' );

					}

				}

				// Send plugin data to template
				self::render_template( $plugin, $api, $button_text, $button_classes );

			}

		endforeach;

?>
		</div>
<?php
	}




	/*
	* render_template
	* Render display template for each plugin.
	*
	*
	* @param $plugin            Array - Original data passed to init()
	* @param $api               Array - Results from plugins_api
	* @param $button_text       String - text for the button
	* @param $button_classes    String - classnames for the button
	*
	* @since 1.0
	*/
	public static function render_template($plugin, $api, $button_text, $button_classes){

?>
		<div class="plugin">
			<div class="plugin-wrap">
				<img src="<?php echo $api->icons['1x']; ?>" alt="">
			<h2><?php echo $api->name; ?></h2>
			<p><?php echo $api->short_description; ?></p>
			<p class="plugin-author"><?php _e('By', 'q-textdomain'); ?> <?php echo $api->author; ?></p>
			</div>
			<ul class="activation-row">
				<li>
					<a
						class="<?php echo $button_classes; ?>"
						data-slug="<?php echo $api->slug; ?>"
						data-name="<?php echo $api->name; ?>"
						href="<?php echo \get_admin_url(); ?>/update.php?action=install-plugin&amp;plugin=<?php echo $api->slug; ?>&amp;_wpnonce=<?php echo \wp_create_nonce( 'install-plugin_'. $api->slug ); ?>">
						<?php echo $button_text; ?>
					</a>
				</li>
				<li>
					<a href="https://wordpress.org/plugins/<?php echo $api->slug; ?>/" target="_blank">
						<?php _e('More Details', 'q-textdomain'); ?>
					</a>
				</li>
			</ul>
		</div>
<?php

	}




	/*
	* install
	* An Ajax method for installing plugin.
	*
	* @return $json
	*
	* @since 1.0
	*/
	public function install(){

		if ( ! \current_user_can( 'install_plugins' ) ) {
			\wp_die( __( 'Sorry, you are not allowed to install plugins on this site.', 'q-textdomain' ) );
		}

		$nonce = $_POST["nonce"];
		$plugin = $_POST["plugin"];

		// Check our nonce, if they don't match then bounce!
		if ( ! \wp_verify_nonce( $nonce, 'q_install_nonce' )) {
			\wp_die( __( 'Error - unable to verify nonce, please try again.', 'q-textdomain') );
		}

		// Include required libs for installation
		require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
		require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		require_once( ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php' );
		require_once( ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php' );

		// Get Plugin Info
		$api = \plugins_api( 'plugin_information',
			array(
				'slug' => $plugin,
				'fields' => array(
					'short_description' => false,
					'sections' => false,
					'requires' => false,
					'rating' => false,
					'ratings' => false,
					'downloaded' => false,
					'last_updated' => false,
					'added' => false,
					'tags' => false,
					'compatibility' => false,
					'homepage' => false,
					'donate_link' => false,
				),
			)
		);

		$skin     = new \WP_Ajax_Upgrader_Skin();
		$upgrader = new \Plugin_Upgrader( $skin );
		$upgrader->install( $api->download_link );

		if( $api->name ){

			$status = 'success';
			$msg = $api->name .' successfully installed.';

		} else {

			$status = 'failed';
			$msg = 'There was an error installing '. $api->name .'.';

		}

		$json = array(
			'status' => $status,
			'msg' => $msg,
		);

		\wp_send_json($json);

	}



	/*
	* activate
	* Activate plugin via Ajax.
	*
	* @return $json
	*
	* @since 1.0
	*/
	public static function activate(){

		if ( ! \current_user_can('install_plugins') ) {
			\wp_die( __( 'Sorry, you are not allowed to activate plugins on this site.', 'q-textdomain' ) );
		}

		$nonce = $_POST["nonce"];
		$plugin = $_POST["plugin"];

		// Check our nonce, if they don't match then bounce!
		if ( ! \wp_verify_nonce( $nonce, 'q_install_nonce' )) {
			die( __( 'Error - unable to verify nonce, please try again.', 'q-textdomain' ) );
		}

		// Include required libs for activation
		require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
		require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		require_once( ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php' );

		// Get Plugin Info
		$api = \plugins_api( 'plugin_information',
			array(
				'slug' => $plugin,
				'fields' => array(
					'short_description' => false,
					'sections' => false,
					'requires' => false,
					'rating' => false,
					'ratings' => false,
					'downloaded' => false,
					'last_updated' => false,
					'added' => false,
					'tags' => false,
					'compatibility' => false,
					'homepage' => false,
					'donate_link' => false,
				),
			)
		);


		if( $api->name ){

			$main_plugin_file = self::get_plugin_file( $plugin );
			$status = 'success';
			if($main_plugin_file){
				\activate_plugin($main_plugin_file);
				$msg = $api->name .' successfully activated.';
			}

		} else {

			$status = 'failed';
			$msg = 'There was an error activating '. $api->name .'.';

		}

		$json = array(
			'status' => $status,
			'msg' => $msg,
		);

		\wp_send_json($json);

	}




	/*
	* get_plugin_file
	* A method to get the main plugin file.
	*
	*
	* @param  $plugin_slug    String - The slug of the plugin
	* @return $plugin_file
	*
	* @since 1.0
	*/
	public static function get_plugin_file( $plugin_slug ) {

		require_once( ABSPATH . '/wp-admin/includes/plugin.php' ); // Load plugin lib
		$plugins = \get_plugins();

         foreach( $plugins as $plugin_file => $plugin_info ) {

			// Get the basename of the plugin e.g. [askismet]/askismet.php
			$slug = dirname( \plugin_basename( $plugin_file ) );

			if( $slug ){
	            if ( $slug == $plugin_slug ) {
	               return $plugin_file; // If $slug = $plugin_name
	            }
            }
		}
		return null;

	}


	/*
	* check_file_extension
	* A helper to check file extension
	*
	*
	* @param $filename    String - The filename of the plugin
	* @return boolean
	*
	* @since 1.0
	*/
	public static function check_file_extension( $filename ) {

		if( substr( strrchr($filename, '.' ), 1 ) === 'php' ){
			// has .php exension
			return true;
		} else {
			// ./wp-content/plugins
			return false;
		}

	}


	/*
	* enqueue_scripts
	* Enqueue admin scripts and scripts localization
	*
	*
	* @since 1.0
	*/
	public static function enqueue_scripts(){

		\wp_enqueue_script( 'q-plugin-install', self::get_plugin_url( 'library/api/plugin/assets/install.js' ), array( 'jquery' ));

		\wp_localize_script( 'q-plugin-install', 'q_api_plugin', array(
			'ajax_url' => \admin_url('admin-ajax.php'),
			'admin_nonce' => \wp_create_nonce('q_install_nonce'),
			'install_now' => __('Are you sure you want to install this plugin?', 'q-textdomain'),
			'install_btn' => __('Install Now', 'q-textdomain'),
			'activate_btn' => __('Activate', 'q-textdomain'),
			'installed_btn' => __('Activated', 'q-textdomain')
		));

		\wp_enqueue_style( 'q-plugin-install', self::get_plugin_url( 'library/api/plugin/assets/install.css') );

	}

}
