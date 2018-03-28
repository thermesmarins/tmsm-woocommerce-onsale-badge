<?php

/**
 * @link              https://github.com/nicomollet
 * @since             1.0.0
 * @package           Tmsm_Woocommerce_Onsale_Badge
 *
 * @wordpress-plugin
 * Plugin Name:       TMSM WooCommerce On Sale Badge
 * Plugin URI:        https://github.com/thermesmarins/tmsm-woocommerce-onsale-badge
 * Description:       WooCommerce on sale badge and alert compatible with Dynamic Pricing & Discounts plugin
 * Version:           1.0.0
 * Author:            Nicolas Mollet
 * Author URI:        https://github.com/nicomollet
 * Requires PHP:      5.6
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tmsm-woocommerce-onsale-badge
 * Domain Path:       /languages
 * Github Plugin URI: https://github.com/thermesmarins/tmsm-woocommerce-onsale-badge
 * Github Branch:     master
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
define( 'TMSM_WOOCOMMERCE_ONSALE_BADGE_VERSION', '1.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tmsm-woocommerce-onsale-badge-activator.php
 */
function activate_tmsm_woocommerce_onsale_badge() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tmsm-woocommerce-onsale-badge-activator.php';
	Tmsm_Woocommerce_Onsale_Badge_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tmsm-woocommerce-onsale-badge-deactivator.php
 */
function deactivate_tmsm_woocommerce_onsale_badge() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-tmsm-woocommerce-onsale-badge-deactivator.php';
	Tmsm_Woocommerce_Onsale_Badge_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_tmsm_woocommerce_onsale_badge' );
register_deactivation_hook( __FILE__, 'deactivate_tmsm_woocommerce_onsale_badge' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tmsm-woocommerce-onsale-badge.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tmsm_woocommerce_onsale_badge() {

	$plugin = new Tmsm_Woocommerce_Onsale_Badge();
	$plugin->run();

}
run_tmsm_woocommerce_onsale_badge();
