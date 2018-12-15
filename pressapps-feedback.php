<?php

/**
 * Plugin Name:       PressApps Feedback
 * Plugin URI:        http://pressapps.co/products/feedback/
 * Description:       Create engaging, responsive surveys in minutes and view your results graphically
 * Version:           1.0.0
 * Author:            PressApps
 * Author URI:        http://pressapps.co/
 * Text Domain:       pressapps-feedback
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Skelet Config Path
 */
$skelet_paths[] = array(
    'prefix'      => 'pafb',
    'dir'         => wp_normalize_path(  plugin_dir_path( __FILE__ ).'/admin/' ),
    'uri'         => plugin_dir_url( __FILE__ ).'/admin/skelet',
);

/**
 * Load Skelet Framework
 */
if ( ! class_exists( 'Skelet_LoadConfig' ) ) {
	include_once dirname( __FILE__ ) .'/admin/skelet/skelet.php';
}

global $skelet; $skelet = new Skelet("pafb");

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pressapps-feedback-activator.php
 */
function activate_pressapps_feedback() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pressapps-feedback-activator.php';
	Pressapps_Feedback_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pressapps-feedback-deactivator.php
 */
function deactivate_pressapps_feedback() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pressapps-feedback-deactivator.php';
	Pressapps_Feedback_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pressapps_feedback' );
register_deactivation_hook( __FILE__, 'deactivate_pressapps_feedback' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pressapps-feedback.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pressapps_feedback() {

	$plugin = new Pressapps_Feedback();
	$plugin->run();

}
run_pressapps_feedback();
