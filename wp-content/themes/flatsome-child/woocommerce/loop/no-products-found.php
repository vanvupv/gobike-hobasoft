<?php
/**
 * Hiển thị thông báo khi không có sản phẩm nào khớp với bộ lọc
 * Chuẩn giao diện GOBIKE (hộp alert màu vàng nhạt kèm nút đóng ✕)
 *
 * @package WooCommerce/Templates
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="woocommerce-no-products-found gobike-no-products-wrapper">
	<div class="gobike-no-products-alert" role="alert">
		<span class="gobike-alert-text">Không có sản phẩm nào trong danh mục này.</span>
		<button type="button" class="gobike-alert-dismiss" onclick="this.closest('.gobike-no-products-alert').style.display='none';" aria-label="Đóng">&times;</button>
	</div>
</div>
