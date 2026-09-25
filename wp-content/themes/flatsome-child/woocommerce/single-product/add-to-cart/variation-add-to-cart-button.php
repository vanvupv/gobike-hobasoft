<?php
/**
 * Single variation cart button
 *
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined('ABSPATH') || exit;

global $product;
?>
<div class="woocommerce-variation-add-to-cart variations_button">
    <!-- 1. Hàng Số lượng & Tình trạng kho -->
    <div class="gobike-qty-stock-row">
        <span class="gobike-qty-label">Số lượng</span>
        
        <?php
        do_action('woocommerce_before_add_to_cart_quantity');

        woocommerce_quantity_input(
            array(
                'min_value'   => apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product),
                'max_value'   => apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product),
                'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product->get_min_purchase_quantity(),
            )
        );

        do_action('woocommerce_after_add_to_cart_quantity');
        ?>

        <div class="gobike-stock-badge">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="9 12 11 14 15 10"/></svg>
            <span>Còn hàng</span>
        </div>
    </div>

    <!-- 2. Hàng Hai nút hành động: Thêm vào giỏ & Mua ngay (Side-by-side) -->
    <div class="gobike-action-btns-row">
        <button type="submit" class="single_add_to_cart_button button alt btn-gobike-add-cart">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            <span>Thêm vào giỏ hàng</span>
        </button>

        <button type="button" class="button alt btn-gobike-buy-now">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            <span>Mua ngay</span>
        </button>
    </div>

    <input type="hidden" name="add-to-cart" value="<?php echo absint($product->get_id()); ?>" />
    <input type="hidden" name="product_id" value="<?php echo absint($product->get_id()); ?>" />
    <input type="hidden" name="variation_id" class="variation_id" value="0" />
</div>
