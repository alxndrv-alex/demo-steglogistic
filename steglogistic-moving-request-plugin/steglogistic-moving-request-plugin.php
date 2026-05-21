<?php
/**
 * Plugin Name:  Steglogistic Moving Request plugin
 * Version:      2.3
 * Description:  Adds a moving request forms and logic
 * Author:       FastDev AB
 * Author URI:   https://fastdev.com
 * Requires PHP: 8.1
 * Requires at least: 6.6.1
 * Requires Plugins: advanced-custom-fields-pro
 */

define( 'FD_SMR_PLUGIN_DIR_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'FD_SMR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'ENV_TYPE' ) ) {
	define( 'ENV_TYPE', 'dev' );
}

require 'plugin-update-checker/plugin-update-checker.php';
require 'includes/helpers.php';
require 'includes/autoload.php';

FD_Shortcodes::init();
FD_Hooks::init();
