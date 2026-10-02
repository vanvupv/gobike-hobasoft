<?php
/**
 * The Template for displaying all single products.
 *
 * Override of flatsome/woocommerce/single-product.php
 * THAY ĐỔI DUY NHẤT: get_footer('shop') → get_footer()
 * để tránh conflict khi tìm footer-shop.php trên server này.
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 1.6.4
 * @flatsome-version 3.16.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'shop' );

do_action( 'flatsome_before_product_page' );

?>

	<?php
		/**
		 * woocommerce_before_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action( 'woocommerce_before_main_content' );
	?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php
			if ( flatsome_product_block( get_the_ID() ) ) {
				wc_get_template_part( 'content', 'single-product-custom' );
			} else {
				wc_get_template_part( 'content', 'single-product' );
			}
			?>

		<?php endwhile; // end of the loop. ?>

	<?php
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>

<?php

do_action( 'flatsome_after_product_page' );

// CHANGED: get_footer('shop') → get_footer()
// get_footer('shop') gặp lỗi trên server (không tìm được footer-shop.php
// trước khi fallback về footer.php). get_footer() tìm footer.php trực tiếp.
get_footer();

?>
