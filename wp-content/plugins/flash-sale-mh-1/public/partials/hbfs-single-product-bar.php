<?php
/**
 * Template: Flash Sale bar on single product page.
 * Variables injected from HBFS_Public::render_single_product_bar():
 *   $item     - row from hbfs_products
 *   $campaign - row from hbfs_campaigns
 *   $sold     - int
 *   $qty      - int
 *   $percent  - int (0-100)
 *   $time_end - string "YYYY-MM-DD HH:mm:ss"
 */
if ( ! defined( 'WPINC' ) ) die;

if ( ! isset( $campaign ) ) {
    $campaign = isset( $item ) ? $item : [];
}
$campaign_data = ( is_array( $campaign ) && isset( $campaign['data_json'] ) ) ? $campaign['data_json'] : [];
$json           = is_array( $campaign_data ) ? $campaign_data : [];
$frame_img      = ! empty( $json['frame_image'] ) ? $json['frame_image'] : '';
$flash_price    = floatval( $item['flash_sale_price'] );
$regular_price  = floatval( $item['regular_price'] );
$discount_pct   = ( $flash_price > 0 && $regular_price > 0 )
                  ? round( 100 - $flash_price / $regular_price * 100 )
                  : 0;

// Lấy ảnh sản phẩm cho preview
$product_obj = wc_get_product( intval( $item['product_id'] ) );
$thumb_url   = '';
if ( $product_obj ) {
    $thumb_id  = $product_obj->get_image_id();
    $thumb_url = $thumb_id ? wp_get_attachment_image_url( $thumb_id, 'woocommerce_thumbnail' ) : '';
}
if ( ! $thumb_url && ! empty( $item['post_thumbnail'] ) ) {
    $thumb_url = $item['post_thumbnail'];
} elseif ( ! $thumb_url && ! empty( $item['image'] ) ) {
    $thumb_url = $item['image'];
}

$bar_style = hbfs_get_bar_style( $campaign );
?>
<div class="hbfs-product-bar" style="<?php echo esc_attr( $bar_style ); ?>">
    <div class="hbfs-product-bar__header">
        <div class="hbfs-product-bar__title">
            <svg viewBox="0 0 24 24"><path d="M7 2v11h3v9l7-12h-4l4-8z"/></svg>
            <?php esc_html_e( 'FLASH SALE', 'hbweb-flashsale' ); ?>
            <?php if ( $discount_pct > 0 ) : ?>
                <span class="hbfs-badge">-<?php echo $discount_pct; ?>%</span>
            <?php endif; ?>
        </div>
        <div class="hbfs-countdown" data-hbfs-countdown="<?php echo esc_attr( $time_end ); ?>">
            <span class="hbfs-countdown__block">--<small>ngày</small></span>
            <span class="hbfs-countdown__sep">:</span>
            <span class="hbfs-countdown__block">--<small>giờ</small></span>
            <span class="hbfs-countdown__sep">:</span>
            <span class="hbfs-countdown__block">--<small>phút</small></span>
            <span class="hbfs-countdown__sep">:</span>
            <span class="hbfs-countdown__block">--<small>giây</small></span>
        </div>
    </div>

    <?php if ( $qty > 0 ) : ?>
    <div class="hbfs-progress-wrap">
        <div class="hbfs-progress-bar" style="width:<?php echo $percent; ?>%"></div>
    </div>
    <div class="hbfs-progress-label">
        🔥 <span class="hbfs-sold-label"><?php printf( __( 'Đã bán %d/%d', 'hbweb-flashsale' ), $sold, $qty ); ?></span>
    </div>
    <?php endif; ?>

    <?php if ( $frame_img ) : ?>
    <div class="hbfs-product-bar__frame"
         style="background-image: url('<?php echo esc_url( $frame_img ); ?>'); background-size: cover; background-repeat: no-repeat; background-position: center; position: absolute; inset: 0; pointer-events: none; border-radius: inherit; z-index: 1;">
    </div>
    <?php endif; ?>
</div>
