<?php
/**
 * Single product sale flash - GoBike Child Theme Override
 * Fixes PHP 8 DivisionByZeroError when a product is marked on-sale but has 0 or empty regular price
 *
 * @package Flatsome
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product;

if ( ! $product || ! $product->is_on_sale() ) {
	return;
}

$badge_style = get_theme_mod( 'bubble_style', 'circle' );
if ( $badge_style === 'style1' ) $badge_style = 'circle';
if ( $badge_style === 'style2' ) $badge_style = 'square';
if ( $badge_style === 'style3' ) $badge_style = 'frame';
?>

<div class="badge-container is-larger absolute left top z-1">
<?php if ( get_theme_mod( 'sale_bubble', 1 ) ) :
	$custom_text = get_theme_mod( 'sale_bubble_text' );
	$text        = $custom_text ? $custom_text : __( 'Sale!', 'woocommerce' );

	if ( get_theme_mod( 'sale_bubble_percentage' ) ) {
		$reg_price = (float) $product->get_regular_price();
		if ( $reg_price > 0 && function_exists( 'flatsome_presentage_bubble' ) ) {
			try {
				$text = flatsome_presentage_bubble( $product, $text );
			} catch ( \Throwable $e ) {
				// Fallback to text on error
			}
		}
	}
	echo apply_filters( 'woocommerce_sale_flash', '<div class="callout badge badge-' . esc_attr( $badge_style ) . '"><div class="badge-inner secondary on-sale"><span class="onsale">' .  $text . '</span></div></div>', $post, $product );
endif; ?>

<?php echo apply_filters( 'flatsome_product_labels', '', $post, $product, $badge_style ); ?>
</div>
