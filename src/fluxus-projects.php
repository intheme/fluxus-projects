<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://inthe.me/
 * @since             1.0.0
 * @package           Fluxus_Projects
 *
 * @wordpress-plugin
 * Plugin Name:       Fluxus Projects
 * Plugin URI:        https://github.com/intheme/fluxus-projects
 * Description:       Portfolio projects manager for your Fluxus WordPress Theme
 * Version:           1.0.0
 * Author:            inTheme
 * Author URI:        https://inthe.me/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fluxus-projects
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'FLUXUS_PROJECTS_VERSION', '1.0.0' );
define( 'FLUXUS_PROJECTS_MAJOR_VERSION', 1 );

define( 'FLUXUS_PROJECTS_PLUGIN_NAME', 'fluxus-projects' );

/**
 * Path to current plugin
 */
define( 'FLUXUS_PROJECTS_PATH', plugin_dir_path( __FILE__ ) );
define( 'FLUXUS_PROJECTS_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_fluxus_projects() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fluxus-projects-activator.php';
	Fluxus_Projects_Activator::on_activate();
}

/**
 * The code that runs during plugin deactivation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-fluxus-projects-deactivator.php';
function deactivate_fluxus_projects() {
	Fluxus_Projects_Deactivator::on_deactivate();
}

register_activation_hook( __FILE__, 'activate_fluxus_projects' );
register_deactivation_hook( __FILE__, 'deactivate_fluxus_projects' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-fluxus-projects.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_fluxus_projects() {

	$plugin = new Fluxus_Projects();
	$plugin->run();

}
run_fluxus_projects();
