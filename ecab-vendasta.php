<?php
/*
Plugin Name: eCab Vendasta
Description: A plugin to manage cab schedules for employees.
Version: 1.0
Author: Rajan Vijayan
Requires at least: 6.0+
Tested up to: 6.2+
Requires PHP: 8.0+
License: MIT
*/

// Prevent direct access to the file
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Autoload dependencies
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * Initializes the plugin components.
 */
add_action( 'plugins_loaded', function() {
    new EcabVendasta\Plugin();
});