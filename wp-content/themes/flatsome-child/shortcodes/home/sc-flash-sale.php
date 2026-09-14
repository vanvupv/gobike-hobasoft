<?php
/**
 * Shortcode: Khối Flash Sale Trang Chủ
 * Cú pháp dùng trong Flatsome: [gobike_home_flash_sale limit="5" title="⚡ GIỜ VÀNG FLASH SALE"]
 */

if (!defined('ABSPATH')) {
    exit;
}

function gobike_render_home_flash_sale($atts)
{
    $atts = shortcode_atts(array(
        'title' => '⚡ GIỜ VÀNG FLASH SALE',
        'limit' => 5,
        'view_all' => '/san-pham/?on_sale=1',
    ), $atts, 'gobike_home_flash_sale');

    // Query sản phẩm đang giảm giá
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => intval($atts['limit']),
        'post__in'       => wc_get_product_ids_on_sale(),
    );

    // Fallback nếu chưa có sản phẩm on_sale thì lấy sản phẩm mới nhất
    if (empty($args['post__in'])) {
        unset($args['post__in']);
    }

    $query = new WP_Query($args);
    if (!$query->have_posts()) {
        return '';
    }

    ob_start();
    ?>
    <div class="gobike-home-flashsale-block">
        <div class="gobike-block-header flashsale-header">
            <div class="header-left">
                <h2 class="block-title flashsale-title"><?php echo esc_html($atts['title']); ?></h2>
            </div>
            <div class="header-right">
                <a href="<?php echo esc_url(home_url($atts['view_all'])); ?>" class="view-all-link">Xem tất cả ></a>
            </div>
        </div>

        <div class="gobike-flashsale-grid">
            <?php
            while ($query->have_posts()):
                $query->the_post();
                global $product;
                if (!is_a($product, 'WC_Product')) {
                    $product = wc_get_product(get_the_ID());
                }
                $permalink = get_permalink();
                $image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: wc_placeholder_img_src();
                $regular_price = $product->get_regular_price();
                $sale_price = $product->get_sale_price();
                $percentage = ($product->is_on_sale() && $regular_price && $sale_price) ? round((($regular_price - $sale_price) / $regular_price) * 100) : 0;
                ?>
                <div class="gobike-flashsale-item">
                    <div class="item-thumb">
                        <a href="<?php echo esc_url($permalink); ?>">
                            <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy" />
                        </a>
                        <?php if ($percentage > 0): ?>
                            <span class="sale-badge-pill">-<?php echo $percentage; ?>%</span>
                        <?php endif; ?>
                    </div>
                    <div class="item-details">
                        <a href="<?php echo esc_url($permalink); ?>">
                            <h3 class="item-title"><?php echo esc_html(get_the_title()); ?></h3>
                        </a>
                        <div class="item-price">
                            <?php if ($sale_price): ?>
                                <span class="price-current"><?php echo wc_price($sale_price); ?></span>
                                <span class="price-old"><?php echo wc_price($regular_price); ?></span>
                            <?php else: ?>
                                <span class="price-current"><?php echo $product->get_price_html(); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php
            endwhile;
            wp_reset_postdata();
            ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('gobike_home_flash_sale', 'gobike_render_home_flash_sale');
