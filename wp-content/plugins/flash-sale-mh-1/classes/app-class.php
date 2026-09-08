<?php
/**
 * FSMH_APP — Base class cho Admin AJAX handlers.
 *
 * Lưu ý: Kết nối MysqliDb riêng đã bị loại bỏ — toàn bộ DB query
 * trong plugin đã chuyển sang sử dụng $wpdb (WordPress native).
 *
 * @package HBWeb_FlashSale
 */

if ( ! defined( 'WPINC' ) ) die;

// Load MysqliDb chỉ khi thực sự cần (dùng bởi HBFS_Campaign, FSMH_Product)
if ( ! class_exists( 'MysqliDb' ) ) {
	include_once HBFS_PATH . 'helpers/database.php';
}

class FSMH_APP {
	public $settings     = null;
	static $db           = null;
	public $prefix       = 'aw';
	static $configs      = null;
	static $pageLimit    = 20;
	public $root_configs = null;

	/**
	 * @param MysqliDb|null $db — optional, để backward compat với code cũ.
	 */
	public function __construct( $db = null ) {
		if ( $db !== null ) {
			self::$db = $db;
		}
	}
}

/**
 * Khởi tạo MysqliDb một lần duy nhất, dùng cho Campaign/Product class.
 * Bảo vệ bằng guard để tránh khởi tạo lại nếu file bị include nhiều lần.
 */
if ( ! isset( $GLOBALS['HBFS_MH_DB_INITIALIZED'] ) ) {
	$GLOBALS['HBFS_MH_DB_INITIALIZED'] = true;
	global $wpdb;
	// Dùng thông tin kết nối từ WordPress thay vì hardcode
	$MH_DB    = new MysqliDb( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
	$FSMH_APP = new FSMH_APP( $MH_DB );
}
