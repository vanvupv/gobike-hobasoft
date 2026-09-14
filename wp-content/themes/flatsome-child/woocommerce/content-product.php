<?php
/**
 * The template for displaying product content within loops
 * GoBike custom product card matching Image 2
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( fl_woocommerce_version_check( '4.4.0' ) ) {
	if ( empty( $product ) || false === wc_get_loop_product_visibility( $product->get_id() ) || ! $product->is_visible() ) {
		return;
	}
} else {
	if ( empty( $product ) || ! $product->is_visible() ) {
		return;
	}
}

// Check stock status.
$out_of_stock = ! $product->is_in_stock();

// Extra post classes.
$classes   = array();
$classes[] = 'product-small';
$classes[] = 'col';
$classes[] = 'has-hover';
$classes[] = 'gobike-catalog-card-col';

if ( $out_of_stock ) $classes[] = 'out-of-stock';

$pid           = $product->get_id();
$permalink     = get_permalink($pid);
$title         = get_the_title($pid);
$regular_price = (float) $product->get_regular_price();
$sale_price    = (float) $product->get_sale_price();
$current_price = (float) $product->get_price();

// Tính % giảm giá
$discount_percent = 0;
if ($product->is_on_sale() && $regular_price > $sale_price && $sale_price > 0) {
    $discount_percent = round((($regular_price - $sale_price) / $regular_price) * 100);
} elseif ($regular_price > $current_price && $current_price > 0) {
    $discount_percent = round((($regular_price - $current_price) / $regular_price) * 100);
}

// Subtitle / Mô tả ngắn
$excerpt = get_the_excerpt($pid);
if (empty($excerpt)) {
    $excerpt = 'Nhỏ gọn, linh hoạt, phù hợp cuộc sống đô thị';
} else {
    $excerpt = wp_trim_words(wp_strip_all_tags($excerpt), 10, '...');
}

// 3 Thông số kỹ thuật (Quãng đường, Trọng lượng, Công suất)
$specs = function_exists('gobike_extract_product_specs') ? gobike_extract_product_specs($product) : array('range' => '100 km', 'weight' => '18 kg', 'power' => '250 W');
?>

<div <?php wc_product_class( $classes, $product ); ?>>
	<div class="col-inner">
		<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
		
		<div class="gobike-product-card-v2">
			<!-- HUY HIỆU BADGE (TOP-LEFT CHUẨN ẢNH 2) -->
			<div class="card-badge-wrap">
				<span class="badge-hot">Bán chạy</span>
			</div>

			<!-- ẢNH ĐẠI DIỆN SẢN PHẨM -->
			<div class="card-image-wrap">
				<a href="<?php echo esc_url($permalink); ?>" title="<?php echo esc_attr($title); ?>">
					<?php
					if (has_post_thumbnail($pid)) {
						echo get_the_post_thumbnail($pid, 'woocommerce_thumbnail', array('class' => 'bike-thumb-img', 'loading' => 'lazy'));
					} else {
						echo '<img src="' . esc_url(wc_placeholder_img_src()) . '" alt="' . esc_attr($title) . '" class="bike-thumb-img" />';
					}
					?>
				</a>
			</div>

			<!-- THÔNG TIN SẢN PHẨM -->
			<div class="card-info-wrap">
				<!-- TÊN SẢN PHẨM -->
				<h4 class="card-product-title">
					<a href="<?php echo esc_url($permalink); ?>" title="<?php echo esc_attr($title); ?>">
						<?php echo esc_html($title); ?>
					</a>
				</h4>

				<!-- DÒNG MÔ TẢ NGẮN (ẢNH 2) -->
				<p class="card-product-subtitle"><?php echo esc_html($excerpt); ?></p>

				<!-- GIÁ BÁN & GIÁ GỐC & % GIẢM (ẢNH 2) -->
				<div class="card-price-row">
					<?php if ($current_price > 0): ?>
						<span class="price-current"><?php echo wc_price($current_price); ?></span>
						<?php if ($regular_price > $current_price): ?>
							<span class="price-old"><?php echo wc_price($regular_price); ?></span>
							<?php if ($discount_percent > 0): ?>
								<span class="discount-pill">-<?php echo $discount_percent; ?>%</span>
							<?php endif; ?>
						<?php endif; ?>
					<?php else: ?>
						<span class="price-current price-contact">Liên hệ</span>
					<?php endif; ?>
				</div>

				<!-- 3 THÔNG SỐ KỸ THUẬT KÈM ICON CHUẨN ẢNH 2 -->
				<div class="card-specs-row">
					<!-- Quãng đường (Odometer / Pin) -->
					<div class="spec-col" title="Quãng đường di chuyển">
						<svg class="spec-icon" viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
							<circle cx="12" cy="12" r="9"/>
							<polyline points="12 7 12 12 15 15"/>
						</svg>
						<span class="spec-value"><?php echo esc_html($specs['range']); ?></span>
					</div>

					<!-- Trọng lượng (Scale / Cân) -->
					<div class="spec-col" title="Trọng lượng">
						<svg class="spec-icon" viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2">
							<path d="M6 3h12l2 4v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7l2-4z"/>
							<circle cx="12" cy="14" r="3"/>
							<path d="M12 11v3"/>
						</svg>
						<span class="spec-value"><?php echo esc_html($specs['weight']); ?></span>
					</div>

					<!-- Công suất động cơ (Tia sét) -->
					<div class="spec-col" title="Công suất động cơ">
						<svg class="spec-icon" viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
							<path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
						</svg>
						<span class="spec-value"><?php echo esc_html($specs['power']); ?></span>
					</div>
				</div>

				<!-- CỤM 2 NÚT HÀNH ĐỘNG CHUẨN ẢNH 2 -->
				<div class="card-actions-row">
					<a href="<?php echo esc_url($permalink); ?>" class="btn-detail-action">
						Xem chi tiết
					</a>
					<a href="<?php echo esc_url($product->add_to_cart_url()); ?>" 
					   data-quantity="1" 
					   data-product_id="<?php echo esc_attr($pid); ?>" 
					   class="btn-cart-action ajax_add_to_cart add_to_cart_button" 
					   aria-label="Thêm <?php echo esc_attr($title); ?> vào giỏ hàng"
					   title="Thêm vào giỏ hàng">
						<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
							<circle cx="9" cy="21" r="1"></circle>
							<circle cx="20" cy="21" r="1"></circle>
							<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
						</svg>
					</a>
				</div>
			</div>
		</div>

		<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
	</div>
</div>
