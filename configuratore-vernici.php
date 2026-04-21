<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://acquistasitoweb.com
 * @since             1.0.0
 * @package           Configuratore_Vernici
 *
 * @wordpress-plugin
 * Plugin Name:       Configuratore Vernici
 * Plugin URI:        https://acquistasitoweb.com/configuratore
 * Description:       Questo è un strumento di leadgeneration per i venditori di vernici industriali. si tratta di uno strumento che permette all'utente finale, di capire quanta vernice serve per un progetto
 * Version:           1.0.0
 * Author:            Acquistasitoweb
 * Author URI:        https://acquistasitoweb.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       configuratore-vernici
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
define( 'CONFIGURATORE_VERNICI_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-configuratore-vernici-activator.php
 */
function activate_configuratore_vernici() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-configuratore-vernici-activator.php';
	Configuratore_Vernici_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-configuratore-vernici-deactivator.php
 */
function deactivate_configuratore_vernici() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-configuratore-vernici-deactivator.php';
	Configuratore_Vernici_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_configuratore_vernici' );
register_deactivation_hook( __FILE__, 'deactivate_configuratore_vernici' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-configuratore-vernici.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_configuratore_vernici() {

	$plugin = new Configuratore_Vernici();
	$plugin->run();

}
run_configuratore_vernici();
