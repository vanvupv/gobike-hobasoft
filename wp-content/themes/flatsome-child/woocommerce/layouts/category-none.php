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

		if ( woocommerce_product_loop() ) {

			/**
			 * Hook: woocommerce_before_shop_loop.
			 *
			 * @hooked wc_print_notices - 10
			 * @hooked woocommerce_result_count - 20 (FL removed)
			 * @hooked woocommerce_catalog_ordering - 30 (FL removed)
			 */
			do_action( 'woocommerce_before_shop_loop' );
			

			// Đảm bảo HUSKY filter luôn hiển thị kể cả khi chưa cấu hình hook tự động
			if ( shortcode_exists( 'woof' ) && ! did_action( 'woof_before_filter' ) ) {
				echo do_shortcode( '[woof autohide="0" autosubmit="1" is_ajax="1"]' );
			}
			?>
			</div>
			<?php
			// GỌI THANH SẮP XẾP RADIO CHUẨN MẪU GOBIKE
			if ( function_exists( 'gobike_render_custom_sorting_toolbar' ) ) {
				gobike_render_custom_sorting_toolbar();
			} else {
				$current_orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'date';
			?>
				<div class="gobike-custom-sorting-toolbar container">
					<span class="sort-label">Xếp theo:</span>
					<div class="sort-options">
						<label class="sort-item">
							<input type="radio" name="gobike_sort_radio" value="title-asc" <?php checked( $current_orderby, 'title-asc' ); ?>>
							<span>Tên A-Z</span>
						</label>
						<label class="sort-item">
							<input type="radio" name="gobike_sort_radio" value="title-desc" <?php checked( $current_orderby, 'title-desc' ); ?>>
							<span>Tên Z-A</span>
						</label>
						<label class="sort-item">
							<input type="radio" name="gobike_sort_radio" value="date" <?php checked( $current_orderby, 'date' ); ?>>
							<span>Hàng mới</span>
						</label>
						<label class="sort-item">
							<input type="radio" name="gobike_sort_radio" value="price" <?php checked( $current_orderby, 'price' ); ?>>
							<span>Giá thấp đến cao</span>
						</label>
						<label class="sort-item">
							<input type="radio" name="gobike_sort_radio" value="price-desc" <?php checked( $current_orderby, 'price-desc' ); ?>>
							<span>Giá cao xuống thấp</span>
						</label>
					</div>
				</div>
				<?php
			}
			
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
