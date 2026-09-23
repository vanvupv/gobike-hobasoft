<?php
/**
 * The Template for displaying product archives (Shop / Category / Brand)
 * GoBike 2-Column Layout matching Image 4 (Sidebar Filter + 4-Col Products Grid)
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_archive_description' );
?>

<div class="gobike-shop-page-container container">
	<?php
	do_action('flatsome_products_before');
	do_action( 'woocommerce_before_main_content' );
	?>

	<!-- 1. HERO BANNER THƯƠNG HIỆU / DANH MỤC (ẢNH 4) -->
	<?php
	if ( function_exists( 'gobike_render_shop_brand_banner' ) ) {
		gobike_render_shop_brand_banner();
	}
	?>

	<!-- 2. DẢI 4 CAM KẾT VÀNG (ẢNH 4) -->
	<?php
	if ( function_exists( 'gobike_render_shop_trust_badges' ) ) {
		gobike_render_shop_trust_badges();
	}
	?>

	<!-- 3. BỐ CỤC 2 CỘT: CỘT BỘ LỌC SIDEBAR (TRÁI) + CỘT LƯỚI SẢN PHẨM (PHẢI) -->
	<div class="row category-page-row gobike-shop-layout-2col" id="products-grid">
		
		<!-- CỘT TRÁI: BỘ LỌC SẢN PHẨM SIDEBAR (~25%) -->
		<div class="col large-3 medium-4 small-12 gobike-sidebar-col">
			<div class="col-inner">
				<?php
				if ( function_exists( 'gobike_render_shop_sidebar_filter' ) ) {
					gobike_render_shop_sidebar_filter();
				}
				?>
			</div>
		</div>

		<!-- CỘT PHẢI: LƯỚI SẢN PHẨM & THANH CÔNG CỤ (~75%) -->
		<div class="col large-9 medium-8 small-12 gobike-products-main-col">
			<div class="col-inner">
				
				<?php
				global $wp_query;
				$total   = $wp_query->found_posts;
				$current_orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
				
				// Xác định tên danh mục hiển thị
				$cat_heading = 'Sản phẩm';
				if ( is_product_category() ) {
					$cat_obj = get_queried_object();
					if ( $cat_obj && ! empty( $cat_obj->name ) ) {
						$cat_heading = 'Sản phẩm ' . $cat_obj->name;
					}
				}
				?>

				<!-- 3.1. TIÊU ĐỀ & NÚT XEM TẤT CẢ DÀNH CHO TABLET & MOBILE (ẢNH 1) -->
				<div class="gobike-shop-mobile-header">
					<h2 class="mobile-cat-title">
						<?php echo esc_html( $cat_heading ); ?> 
						<span class="count">(<?php echo esc_html( $total ); ?>)</span>
					</h2>
					<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="mobile-view-all-link">
						Xem tất cả <span class="arrow">&rsaquo;</span>
					</a>
				</div>

				<!-- 3.2. THANH CÔNG CỤ 2 NÚT BỘ LỌC & SẮP XẾP DÀNH CHO TABLET & MOBILE (ẢNH 1) -->
				<div class="gobike-shop-mobile-toolbar">
					<!-- Nút 1: Bộ lọc (Mở Bottom Sheet) -->
					<button type="button" class="mobile-toolbar-btn mobile-filter-btn" id="gobikeMobileFilterBtn" aria-label="Mở bộ lọc">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<line x1="4" y1="6" x2="20" y2="6"></line>
							<circle cx="8" cy="6" r="2"></circle>
							<line x1="4" y1="12" x2="20" y2="12"></line>
							<circle cx="16" cy="12" r="2"></circle>
							<line x1="4" y1="18" x2="20" y2="18"></line>
							<circle cx="10" cy="18" r="2"></circle>
						</svg>
						<span>Bộ lọc</span>
					</button>

					<!-- Nút 2: Sắp xếp (Native select) -->
					<div class="mobile-toolbar-btn mobile-sort-btn-wrap">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<polyline points="7 15 12 20 17 15"></polyline>
							<polyline points="7 9 12 4 17 9"></polyline>
						</svg>
						<span>Sắp xếp</span>
						<svg class="sort-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
							<polyline points="6 9 12 15 18 9"></polyline>
						</svg>

						<select name="orderby" class="mobile-native-sort-select" onchange="gobikeApplyMobileSort(this.value)">
							<option value="date" <?php selected($current_orderby, 'date'); ?>>Hàng mới</option>
							<option value="price" <?php selected($current_orderby, 'price'); ?>>Giá thấp đến cao</option>
							<option value="price-desc" <?php selected($current_orderby, 'price-desc'); ?>>Giá cao xuống thấp</option>
							<option value="title-asc" <?php selected($current_orderby, 'title-asc'); ?>>Tên A-Z</option>
							<option value="title-desc" <?php selected($current_orderby, 'title-desc'); ?>>Tên Z-A</option>
						</select>
					</div>
				</div>

				<!-- 3.3. THANH TOOLBAR DESKTOP KẾT QUẢ & SẮP XẾP -->
				<div class="gobike-shop-toolbar">
					<div class="toolbar-left">
						<?php
						$per_page = $wp_query->get('posts_per_page');
						$paged   = max(1, get_query_var('paged'));
						$first   = ($paged - 1) * $per_page + 1;
						$last    = min($total, $paged * $per_page);
						if ($total > 0) {
							echo '<span class="toolbar-result-count">Hiển thị <strong>' . $first . ' - ' . $last . '</strong> của <strong>' . $total . '</strong> kết quả</span>';
						} else {
							echo '<span class="toolbar-result-count">0 kết quả</span>';
						}
						?>
					</div>
					
					<div class="toolbar-right">
						<span class="sort-prefix-label">Sắp xếp theo:</span>
						<form class="gobike-ordering-form" method="get">
							<div class="sort-select-wrapper">
								<?php
								$current_orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
								?>
								<select name="orderby" class="orderby" aria-label="Sắp xếp sản phẩm" onchange="this.form.submit()">
									<option value="date" <?php selected($current_orderby, 'date'); ?>>Hàng mới</option>
									<option value="price" <?php selected($current_orderby, 'price'); ?>>Giá thấp đến cao</option>
									<option value="price-desc" <?php selected($current_orderby, 'price-desc'); ?>>Giá cao xuống thấp</option>
									<option value="title-asc" <?php selected($current_orderby, 'title-asc'); ?>>Tên A-Z</option>
									<option value="title-desc" <?php selected($current_orderby, 'title-desc'); ?>>Tên Z-A</option>
								</select>
								<span class="sort-select-arrow">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
								</span>
							</div>
							<?php
							// Giữ lại các tham số lọc nếu có
							foreach ($_GET as $key => $val) {
								if ('orderby' === $key || 'submit' === $key) continue;
								if (is_array($val)) {
									foreach ($val as $innerVal) {
										echo '<input type="hidden" name="' . esc_attr($key) . '[]" value="' . esc_attr($innerVal) . '" />';
									}
								} else {
									echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '" />';
								}
							}
							?>
						</form>
					</div>
				</div>


				<?php
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
				remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
				do_action( 'woocommerce_before_shop_loop' );

				if ( woocommerce_product_loop() ) {
					woocommerce_product_loop_start();

					if ( wc_get_loop_prop( 'total' ) ) {
						while ( have_posts() ) {
							the_post();
							do_action( 'woocommerce_shop_loop' );
							wc_get_template_part( 'content', 'product' );
						}
					}

					woocommerce_product_loop_end();

					do_action( 'woocommerce_after_shop_loop' );
				} else {
					do_action( 'woocommerce_no_products_found' );
				}
				?>

			</div>
		</div>

	</div> <!-- /gobike-shop-layout-2col -->

	<!-- 4. KHỐI 3 BANNER TÍNH NĂNG + VÌ SAO CHỌN GOBIKE + SHOWROOM (ẢNH 4) -->
	<?php
	if ( function_exists( 'gobike_render_shop_bottom_features' ) ) {
		gobike_render_shop_bottom_features();
	}

	// Nội dung SEO cuối trang
	if ( function_exists( 'gobike_render_shop_bottom_content' ) ) {
		echo gobike_render_shop_bottom_content();
	}

	do_action( 'flatsome_products_after' );
	do_action( 'woocommerce_after_main_content' );
	?>
</div>
