<?php
/**
 * Template: Flash Sale overlay cho product loop (Flatsome product box)
 *
 * Variables injected từ HBFS_Public::loop_item_render_overlay():
 *   $item       - array product flash sale (sold, qty, flash_sale_price, ...)
 *   $json       - array campaign data_json (frame_image, color, ...)
 *   $frame_img  - string URL ảnh khung
 *   $sold       - int số đã bán
 *   $qty        - int tổng slot
 *   $percent    - int % đã bán
 *   $time_end   - string datetime kết thúc campaign (đã inject từ caller, KHÔNG query DB ở đây)
 *   $product    - WC_Product (global)
 */
if ( ! defined( 'WPINC' ) ) die;

// $color và $time_end đã được tính sẵn ở loop_item_render_overlay() — không cần query DB
$color = ! empty( $json['color'] ) ? $json['color'] : '#f35627';
?>

<div class="hbfs-loop-overlay">

    <?php /* Frame ảnh — overlay toàn bộ phần ảnh */ ?>
    <?php if ( $frame_img ) : ?>
    <div class="hbfs-loop-overlay__frame">
        <img src="<?php echo esc_url( $frame_img ); ?>" alt="">
    </div>
    <?php endif; ?>

    <?php /* Badge Flash Sale */ ?>
    <div class="hbfs-loop-overlay__badge"
         style="background:<?php echo esc_attr( $color ); ?>">
        ⚡ Flash Sale
    </div>



</div><!-- .hbfs-loop-overlay -->
