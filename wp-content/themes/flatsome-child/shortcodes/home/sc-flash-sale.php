<?php
/**
 * Shortcode: Khối Flash Sale Trang Chủ
 * Cú pháp dùng trong Flatsome: [gobike_home_flash_sale limit="5" title="⚡ GIỜ VÀNG FLASH SALE"]
 * Lưu ý: File CSS được nhúng trực tiếp trong shortcode.
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

    if (empty($args['post__in'])) {
        unset($args['post__in']);
    }

    $query = new WP_Query($args);
    if (!$query->have_posts()) {
        return '';
    }

    ob_start();
    ?>
    <!-- CSS NHÚNG TRỰC TIẾP TRONG SHORTCODE -->
    <style>
        .gobike-home-flashsale-block {
            max-width: 1230px;
            margin: 0 auto 30px auto;
            padding: 16px;
            background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%);
            border: 1px solid #ffd6dc;
            border-radius: 12px;
            box-sizing: border-box;
        }
        .flashsale-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #d90429;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .flashsale-title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: #d90429;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .flashsale-header .view-all-link {
            font-size: 13px;
            font-weight: 600;
            color: #d90429;
            text-decoration: none;
        }
        .flashsale-header .view-all-link:hover {
            text-decoration: underline;
        }
        .gobike-flashsale-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
        }
        .gobike-flashsale-item {
            background: #fff;
            border: 1px solid #f0f0f0;
            border-radius: 8px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: all 0.3s ease;
        }
        .gobike-flashsale-item:hover {
            border-color: #d90429;
            box-shadow: 0 4px 15px rgba(217, 4, 41, 0.15);
            transform: translateY(-2px);
        }
        .gobike-flashsale-item .item-thumb {
            position: relative;
            text-align: center;
            margin-bottom: 10px;
            overflow: hidden;
            border-radius: 6px;
        }
        .gobike-flashsale-item .item-thumb img {
            max-width: 100%;
            height: 160px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        .gobike-flashsale-item:hover .item-thumb img {
            transform: scale(1.05);
        }
        .gobike-flashsale-item .sale-badge-pill {
            position: absolute;
            top: 6px;
            left: 6px;
            background: #d90429;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 12px;
            z-index: 2;
        }
        .gobike-flashsale-item .item-title {
            font-size: 13px;
            font-weight: 600;
            line-height: 1.4;
            color: #333;
            margin: 0 0 8px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 36px;
        }
        .gobike-flashsale-item .item-title:hover {
            color: #d90429;
        }
        .gobike-flashsale-item .item-price {
            display: flex;
            align-items: baseline;
            flex-wrap: wrap;
            gap: 6px;
        }
        .gobike-flashsale-item .price-current {
            color: #d90429;
            font-weight: 700;
            font-size: 15px;
        }
        .gobike-flashsale-item .price-old {
            color: #999;
            font-size: 12px;
            text-decoration: line-through;
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .gobike-flashsale-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 768px) {
            .gobike-flashsale-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            .flashsale-title {
                font-size: 16px;
            }
        }
    </style>

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
                $regular_price = (float) $product->get_regular_price();
                $sale_price = (float) $product->get_sale_price();
                $percentage = ($product->is_on_sale() && $regular_price > $sale_price && $sale_price > 0) ? round((($regular_price - $sale_price) / $regular_price) * 100) : 0;
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
                        <a href="<?php echo esc_url($permalink); ?>" style="text-decoration:none;">
                            <h3 class="item-title"><?php echo esc_html(get_the_title()); ?></h3>
                        </a>
                        <div class="item-price">
                            <?php if ($sale_price > 0): ?>
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
