<?php
/**
 * HBFS_Promotion — Stub (đã được di chuyển sang hbweb-promo)
 *
 * Tính năng Volume Discount, Gift per product, Upbill và lock_gift_quantity
 * đã được chuyển toàn bộ sang plugin hbweb-promo (HBPR_Public) để tránh xung đột.
 *
 * Class này được giữ lại dạng stub để không gây lỗi fatal nếu có code bên ngoài
 * vẫn tham chiếu đến nó. Không đăng ký bất kỳ hook WooCommerce nào ở đây.
 *
 * @package HBWeb_FlashSale
 * @deprecated Sử dụng hbweb-promo (HBPR_Public) thay thế.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class HBFS_Promotion {

	public function __construct() {
		// Không đăng ký hook nào — tránh xung đột với HBPR_Public (hbweb-promo).
	}
}
