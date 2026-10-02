<?php
/**
 * The Template for displaying product archives, including the main shop page.
 *
 * Override of flatsome/woocommerce/archive-product.php
 * THAY ĐỔI DUY NHẤT: get_footer('shop') → get_footer()
 * để tránh conflict khi tìm footer-shop.php trên server này.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 8.8.0
 * @flatsome-version 3.18.7
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

get_header( 'shop' );

if (
	is_shop()
	&& get_theme_mod( 'html_shop_page_content' )
	&& ! $wp_query->is_search()
	&& $wp_query->query_vars['paged'] < 1
) {
	echo do_shortcode( '<div class="shop-page-content">' . get_theme_mod( 'html_shop_page_content' ) . '</div>' );
} else {
	wc_get_template_part( 'layouts/category', get_theme_mod( 'category_sidebar', 'left-sidebar' ) );
}

// CHANGED: get_footer('shop') → get_footer()
// get_footer('shop') gặp lỗi trên server (không tìm được footer-shop.php
// trước khi fallback về footer.php). get_footer() tìm footer.php trực tiếp.
get_footer();
