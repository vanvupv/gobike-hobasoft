<?php
/**
 * Hook: woocommerce_archive_description.
 *
 * @hooked woocommerce_taxonomy_archive_description - 10
 * @hooked woocommerce_product_archive_description - 10
 */
do_action( 'woocommerce_archive_description' );
?>
<!--  -->

<div class="row category-page-row">
		<div class="col large-12">
		<?php
		do_action('flatsome_products_before');

		/**
		* Hook: woocommerce_before_main_content.
		*
		* @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		* @hooked woocommerce_breadcrumb - 20 (FL removed)
		* @hooked WC_Structured_Data::generate_website_data() - 30
		*/
		do_action( 'woocommerce_before_main_content' );

		?>

		<?php
		// =========================================================================
		// KHỐI 1: CẶP BANNER TIỆN ÍCH CỬA HÀNG (Nằm dưới breadcrumb, trên bộ lọc)
		// =========================================================================
		if ( function_exists( 'gobike_render_shop_top_banners' ) ) {
			echo gobike_render_shop_top_banners();
		}
		?>

		<h1 class="gobike-shop-page-title"><?php woocommerce_page_title(); ?></h1>

		<?php
		// =========================================================================
		// BỘ LỌC 3 TẦNG GOBIKE (Luôn hiển thị kể cả khi danh mục không có sản phẩm)
		// =========================================================================

		// 1. TẦNG 1: BỘ LỌC NGANG "Tìm theo:"
		if ( shortcode_exists( 'woof' ) && ! did_action( 'woof_before_filter' ) ) {
			echo do_shortcode( '[woof autohide="0" autosubmit="1" is_ajax="1"]' );
		}

		// 2. TẦNG 3: THANH SẮP XẾP RADIO "Xếp theo:"
		if ( function_exists( 'gobike_render_custom_sorting_toolbar' ) ) {
			gobike_render_custom_sorting_toolbar();
		}

		/**
		 * Hook: woocommerce_before_shop_loop.
		 * HUSKY sẽ render TẦNG 2 (Dải Badges & Nút Bỏ hết) tại đây
		 *
		 * @hooked wc_print_notices - 10
		 * @hooked woocommerce_result_count - 20 (FL removed)
		 * @hooked woocommerce_catalog_ordering - 30 (FL removed)
		 */
		do_action( 'woocommerce_before_shop_loop' );

		if ( woocommerce_product_loop() ) {
			
			woocommerce_product_loop_start();

			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ) {
					the_post();

					/**
					 * Hook: woocommerce_shop_loop.
					 *
					 * @hooked WC_Structured_Data::generate_product_data() - 10
					 */
					do_action( 'woocommerce_shop_loop' );

					wc_get_template_part( 'content', 'product' );
				}
			}

			woocommerce_product_loop_end();

			/**
			 * Hook: woocommerce_after_shop_loop.
			 *
			 * @hooked woocommerce_pagination - 10
			 */
			do_action( 'woocommerce_after_shop_loop' );
		} else {
			/**
			 * Hook: woocommerce_no_products_found.
			 *
			 * @hooked wc_no_products_found - 10
			 */
			do_action( 'woocommerce_no_products_found' );
		}

		// =========================================================================
		// KHỐI 2: NỘI DUNG SEO CUỐI TRANG (Tiêu đề + Editor)
		// =========================================================================
		if ( function_exists( 'gobike_render_shop_bottom_content' ) ) {
			echo gobike_render_shop_bottom_content();
		}
		?>

		<?php
		/**
		 * Hook: flatsome_products_after.
		 *
		 * @hooked flatsome_products_footer_content - 10
		 */
		do_action( 'flatsome_products_after' );
		/**
		 * Hook: woocommerce_after_main_content.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
		?>

		</div>
</div>
