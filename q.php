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
 * Version:         6.0.1
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

// STOP ##
return;

// import ##
use q;
use q\plugin;
use q\core;

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
require_once __DIR__ . '/library/core/log.php';
require_once __DIR__ . '/plugin.php';

// fire up log
$log = new \q\core\log();
$log->hooks();

// get plugin instance ##
$q = plugin::get_instance();

// validate instance ##
if( ! ( $q instanceof q\plugin ) ) {

	$log::set( 'Error in Q plugin instance' );

	// nothing else to do here ##
	return;

}

// object controllers ##
require_once __DIR__ . '/library.php';
require_once __DIR__ . '/factory.php';

// set text domain on init hook ##
\add_action( 'init', [ $q, 'load_plugin_textdomain' ], 1 );

// fire hooks - build log, helper and config objects and translations ## 
\add_action( 'plugins_loaded', function(){

	// build library object ##
	$library = new q\library();
	$library->load();

	// build factory ##
	$factory = new q\factory();

	// core hooks ##
	$factory->core();

	// view hooks ##
	$factory->view();

	// asset hooks ##
	$factory->asset();

	// global hooks ##
	$factory->hook();

	// module hooks ##
	$factory->module();

	// admin hooks ##
	$factory->admin();

	// plugin hooks ##
	$factory->plugins();

	// test hooks ##
	$factory->test();

}, 0 );
