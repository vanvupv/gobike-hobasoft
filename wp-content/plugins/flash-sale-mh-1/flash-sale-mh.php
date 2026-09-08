<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://hbweb.vn/
 * @since             1.0.0
 * @package           HBWeb_FlashSale
 *
 * @wordpress-plugin
 * Plugin Name:       HBWeb Flash Sale
 * Plugin URI:        https://hbweb.vn/
 * Description:       Tạo chiến dịch Flash Sale cho WooCommerce. Hỗ trợ sản phẩm đơn và sản phẩm biến thể.
 * Version:           1.7.5
 * Author:            HBWeb
 * Author URI:        https://hbweb.vn/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hbweb-flashsale
 * Domain Path:       /languages
 * Requires Plugins:  woocommerce
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// --- Plugin Constants ---
define( 'HBFS_VERSION',     '1.7.5' );
define( 'HBFS_PLUGIN_FILE', __FILE__ );
define( 'HBFS_URL',         plugin_dir_url( __FILE__ ) );
define( 'HBFS_PATH',        plugin_dir_path( __FILE__ ) );
define( 'HBFS_BASENAME',    plugin_basename( __FILE__ ) );

// Keep old constants for backward compat with existing classes that still use FSMH_*
if ( ! defined( 'FSMH_URL' ) )  define( 'FSMH_URL',  HBFS_URL );
if ( ! defined( 'FSMH_PATH' ) ) define( 'FSMH_PATH', HBFS_PATH );

// --- Core Helpers & Classes ---
require_once HBFS_PATH . 'helpers/functions.php';
require_once HBFS_PATH . 'helpers/hbfs-functions.php';
require_once HBFS_PATH . 'classes/app-class.php';
require_once HBFS_PATH . 'classes/hbfs-campaign-class.php'; // Campaign class mới (hbfs_*)
require_once HBFS_PATH . 'classes/product-class.php';

// --- Admin ---
require_once HBFS_PATH . 'admin/class-hbfs-admin.php';
require_once HBFS_PATH . 'admin/ajax-admin.php';
require_once HBFS_PATH . 'admin/class-hbfs-rest.php'; // REST API (bypass Cloudflare)

// --- Public / Frontend ---
require_once HBFS_PATH . 'public/class-hbfs-public.php';
require_once HBFS_PATH . 'public/class-hbfs-promotion.php';

// --- Activation / Deactivation Hooks ---
register_activation_hook( __FILE__, 'hbfs_activate' );
register_deactivation_hook( __FILE__, 'hbfs_deactivate' );

function hbfs_activate() {
	require_once HBFS_PATH . 'includes/class-hbfs-activator.php';
	HBFS_Activator::activate();
}

function hbfs_deactivate() {
	$timestamp = wp_next_scheduled( 'hbfs_cron_job' );
	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, 'hbfs_cron_job' );
	}
}

/**
 * Kick off the plugin after all plugins are loaded.
 */
function hbfs_run() {
	if ( is_admin() ) {
		new HBFS_Admin();
	}
	// Frontend: price filters, shortcodes, product page bars, cart badges
	new HBFS_Public();
	new HBFS_Promotion();

	// Tự động flush rewrite rules khi version thay đổi (đăng ký REST routes mới)
	$saved_ver = get_option( 'hbfs_version_flushed', '' );
	if ( $saved_ver !== HBFS_VERSION ) {
		flush_rewrite_rules( false );
		update_option( 'hbfs_version_flushed', HBFS_VERSION );
	}
}
add_action( 'plugins_loaded', 'hbfs_run' );
