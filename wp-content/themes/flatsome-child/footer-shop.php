<?php
/**
 * Footer template cho WooCommerce pages (Shop, Archive, Single Product)
 *
 * Được gọi bởi get_footer('shop') trong:
 * - woocommerce/archive-product.php
 * - woocommerce/single-product.php
 *
 * Trước đây WordPress fallback sang flatsome/footer.php, nhưng có thể
 * xảy ra conflict với cơ chế template loader. File này đảm bảo
 * WooCommerce pages LUÔN có footer đầy đủ từ child theme.
 *
 * @package Flatsome-Child
 */

global $flatsome_opt;
?>

</main>

<footer id="footer" class="footer-wrapper">

	<?php
	/**
	 * Render full footer template (sidebar-footer-1, sidebar-footer-2, footer-absolute)
	 * Bypass flatsome_page_footer() để tránh _footer meta và footer_block issues
	 */
	get_template_part( 'template-parts/footer/footer' );
	?>

</footer>

</div>

<?php wp_footer(); ?>

</body>
</html>
