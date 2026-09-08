<?php
/**
 * Admin class: menu page, enqueue scripts/styles.
 *
 * @package HBWeb_FlashSale
 */

if ( ! defined( 'WPINC' ) ) die;

class HBFS_Admin {

	public function __construct() {
		add_action( 'admin_menu',             [ $this, 'register_menu' ] );
		add_action( 'admin_enqueue_scripts',  [ $this, 'enqueue_assets' ] );
	}

	public function register_menu() {
		$icon = HBFS_URL . '';
		add_menu_page(
			__( 'HBWeb Flash Sale', 'hbweb-flashsale' ),
			'HBWeb Flash Sale',
			'manage_options',
			'hbweb-flashsale',
			[ $this, 'render_admin_page' ],
			$icon,
			56.1
		);
	}

	public function render_admin_page() {
		require_once HBFS_PATH . 'admin/partials/hbfs-admin-display.php';
	}

	public function enqueue_assets( $hook ) {
		// Chỉ load asset trong trang của plugin
		if ( strpos( $hook, 'hbweb-flashsale' ) === false ) {
			return;
		}

		// Cho phép wp.media (upload ảnh banner)
		wp_enqueue_media();
	}
}
