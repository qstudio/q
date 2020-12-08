<?php

/*
 * WordPress Framework
 *
 * @package         q
 * @author          Q Studio <social@qstudio.us>
 * @license         GPL-3.0+
 * @link            http://qstudio.us/
 * @copyright       2020 Q Studio
 *
 * @wordpress-plugin
 * Plugin Name:     Q
 * Plugin URI:      https://www.qstudio.us
 * Description:     Q is a Development Framework that provides an API to manage libraries, themes, plugins and modules.
 * Version:         6.0.0
 * Author:          Q Studio
 * Author URI:      https://www.qstudio.us
 * License:         GPL
 * Requires PHP:    7.0 
 * Copyright:       Q Studio
 * Class:           Q
 * Text Domain:     q
 * Domain Path:     /languages
 * GitHub Plugin URI: qstudio/q
*/

// namespace plugin ##
namespace q;

// import ##
use q;
use q\plugin;

// If this file is called directly, Bulk!
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

// plugin activation hook to store current application and plugin state ##
\register_activation_hook( __FILE__, [ '\\Q\\plugin', 'activation_hook' ] );

// plugin deactivation hook - clear stored data ##
\register_deactivation_hook( __FILE__, [ '\\Q\\plugin', 'deactivation_hook' ] );

// required bits to get set-up ##
require_once __DIR__ . '/library/api/function.php';
// require_once __DIR__ . '/autoload.php'; // @TODO -- need to consier if this is a good idea with priority h::get loader also ...
require_once __DIR__ . '/plugin.php';

// get plugin instance ##
$q = plugin::get_instance();

// validate instance ##
if( ! ( $q instanceof q\plugin ) ) {

	error_log( 'Error in Q plugin instance' );

	// nothing else to do here ##
	return;

}

// fire hooks - build log, helper and config objects and translations ## 
\add_action( 'plugins_loaded', function() use( $q ){

	// kick off config and store object ##
	// $config = new q\core\config( $q );
	// $config->hooks();
	// $plugin->set( 'config', $config );

	// build factory objects ##
	// $q->factory( $q );
	$q->load_libraries();

	// set text domain on init hook ##
	\add_action( 'init', [ $q, 'load_plugin_textdomain' ], 1 );
	
	// check debug settings ##
	// \add_action( 'plugins_loaded', [ $q, 'debug' ], 11 );

}, 0 );
