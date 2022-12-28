<?php
/**
 * Plugin Name: WC Marketing Submissions
 * Plugin URI: https://deedy.uk
 * Description: Add option to opt-in customers into marketing lists emails.
 * Version: 1.0
 * Author: Ahmad Wael
 * Author URI: https://bbioon.com
 * Text Domain: wms
 * Domain Path: /languages/
 */

define( 'WMS_PLUGIN_VERSION', '1.0' );
define( 'WMS_DIR', plugin_dir_path( __FILE__ ) );
define( 'WMS_URI', plugin_dir_url( __FILE__ ) );

/**
 * Classes autoloader
 */
spl_autoload_register(
	function ( $class_name ) {
		$classes_dir = WMS_DIR . 'Modules' . DIRECTORY_SEPARATOR;
		$class_file  = str_replace( 'WMS\Modules\\', '', $class_name ) . '.php';
		$class       = $classes_dir . str_replace( '\\', '/', $class_file );
		if ( file_exists( $class ) ) {
			require_once $class;
		}

		return false;
	}
);

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wms_plugin() {

	$plugin = new \WMS\Modules\Loader();
	$plugin->load_modules();
	$plugin->run();

}
run_wms_plugin();