<?php
/**
 * Shortcode: Khối danh mục Xe đạp trợ lực điện chuẩn giao diện GOBIKE (1 Lớn + 8 Nhỏ)
 * Cú pháp dùng trong Flatsome: [gobike_category_block cat="slug-danh-muc" title="TIÊU ĐỀ" subcat="Tên danh mục phụ" subcat_link="#" view_all="#"]
 * Lưu ý: File CSS được nhúng trực tiếp trong shortcode để quản lý trọn gói độc lập.
 */

if (!defined('ABSPATH')) {
    exit;
}

function gobike_render_category_block($atts)
{
    $atts = shortcode_atts(array(
        'cat' => '',
        'title' => 'XE ĐẠP TRỢ LỰC ĐIỆN',
        'subcat' => '',
        'subcat_link' => '#',
        'view_all' => '',
        'limit' => 9,
        'mobile_banner' => ''
    ), $atts, 'gobike_category_block');

    if (empty($atts['cat'])) {
        return '<div style="padding:15px;background:#fff3cd;color:#856404;border:1px solid #ffeeba;margin:10px 0;border-radius:6px;">'
            . '⚠️ <strong>Chưa nhập slug danh mục!</strong> Ví dụ: <code>[gobike_category_block cat="xe-dap-tro-luc-dien" title="XE ZHENGBU"]</code>'
            . '</div>';
    }

    $term = get_term_by('slug', $atts['cat'], 'product_cat');
    if (!$term && is_numeric($atts['cat'])) {
        $term = get_term_by('id', (int) $atts['cat'], 'product_cat');
    }

    $view_all_link = !empty($atts['view_all']) ? $atts['view_all'] : ($term ? get_term_link($term) : '#');
    if (is_wp_error($view_all_link))
        $view_all_link = '#';

    $tax_field = is_numeric($atts['cat']) ? 'term_id' : 'slug';
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => intval($atts['limit']),
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => $tax_field,
                'terms' => $atts['cat'],
                'include_children' => true,
            ),
        ),
    );

    $query = new WP_Query($args);

    ob_start();
    ?>
    <!-- CSS NHÚNG TRỰC TIẾP TRONG SHORTCODE -->
    <style>
        .gobike-category-block-wrapper {
            max-width: 1230px;
            margin: 0 auto 30px auto;
            padding: 0 10px;
            box-sizing: border-box;
            font-family: inherit;
        }
        .gobike-block-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-bottom: 2px solid #149d29;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .gobike-block-header .header-left {
            display: flex;
            align-items: baseline;
            gap: 15px;
            flex-wrap: wrap;
        }
        .gobike-block-header .block-title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .gobike-block-header .block-title a {
            color: #149d29;
            text-decoration: none;
            transition: color 0.2s;
        }
        .gobike-block-header .block-title a:hover {
            color: #0e7a1e;
        }
        .gobike-block-header .subcat-link {
            font-size: 14px;
            color: #666;
            text-decoration: none;
            transition: color 0.2s;
        }
        .gobike-block-header .subcat-link:hover {
            color: #149d29;
            text-decoration: underline;
        }
        .gobike-block-header .view-all-link {
            font-size: 13px;
            font-weight: 600;
            color: #149d29;
            text-decoration: none;
            white-space: nowrap;
        }
        .gobike-block-header .view-all-link:hover {
            text-decoration: underline;
        }
        .gobike-block-grid {
            display: grid;
            grid-template-columns: 320px repeat(4, 1fr);
            gap: 12px;
        }
        .gobike-big-item {
            grid-row: span 2;
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: all 0.3s ease;
        }
        .gobike-big-item:hover,
        .gobike-small-item:hover {
            border-color: #149d29;
            box-shadow: 0 4px 15px rgba(20, 157, 41, 0.12);
        }
        .gobike-big-item .img-box {
            position: relative;
            text-align: center;
            margin-bottom: 12px;
            overflow: hidden;
            border-radius: 6px;
        }
        .gobike-big-item .img-box img {
            max-width: 100%;
            height: auto;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        .gobike-big-item:hover .img-box img {
            transform: scale(1.03);
        }
        .gobike-small-item {
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: all 0.3s ease;
        }
        .gobike-small-item .img-wrap {
            position: relative;
            text-align: center;
            margin-bottom: 10px;
            overflow: hidden;
            border-radius: 6px;
        }
        .gobike-small-item .img-wrap img {
            max-width: 100%;
            height: 150px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        .gobike-small-item:hover .img-wrap img {
            transform: scale(1.04);
        }
        .sale-badge-pill {
            position: absolute;
            top: 8px;
            left: 8px;
            background: #d90429;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 12px;
            line-height: 1.4;
            z-index: 2;
        }
        .product-title,
        .item-title {
            font-size: 13px;
            font-weight: 600;
            line-height: 1.4;
            color: #333;
            margin: 0 0 8px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-decoration: none;
            min-height: 36px;
        }
        .product-title:hover,
        .item-title:hover {
            color: #149d29;
        }
        .gobike-price-box {
            margin-bottom: 8px;
            display: flex;
            align-items: baseline;
            flex-wrap: wrap;
            gap: 6px;
        }
        .gobike-price-box .price-current {
            color: #d90429;
            font-weight: 700;
            font-size: 15px;
        }
        .gobike-price-box .price-old {
            color: #999;
            font-size: 12px;
            text-decoration: line-through;
        }
        .spec-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            color: #555;
            margin-top: 10px;
        }
        .spec-table td {
            padding: 4px 0;
            border-bottom: 1px dashed #eee;
        }
        .spec-table td span {
            color: #888;
        }
        .gobike-mobile-viewmore {
            display: none;
            margin: 15px auto 0;
            padding: 9px 20px;
            border: 1px solid #149d29;
            border-radius: 25px;
            color: #149d29;
            font-weight: 600;
            font-size: 13px;
            text-align: center;
            text-decoration: none;
            background: #fff;
        }
        .gobike-mobile-viewmore:hover {
            background: #149d29;
            color: #fff;
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .gobike-block-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            .gobike-big-item {
                grid-row: auto;
                grid-column: span 3;
            }
        }
        @media (max-width: 768px) {
            .gobike-block-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            .gobike-big-item {
                grid-column: span 2;
            }
            .gobike-block-header .header-right {
                display: none;
            }
            .gobike-mobile-viewmore {
                display: block;
            }
        }
    </style>

    <div class="gobike-category-block-wrapper">
        <!-- HEADER CỦA KHỐI -->
        <div class="gobike-block-header">
            <div class="header-left">
                <h2 class="block-title">
                    <a href="<?php echo esc_url($view_all_link); ?>"><?php echo esc_html($atts['title']); ?></a>
                </h2>
                <?php if (!empty($atts['subcat'])): ?>
                    <a href="<?php echo esc_url($atts['subcat_link']); ?>"
                        class="subcat-link"><?php echo esc_html($atts['subcat']); ?></a>
                <?php endif; ?>
            </div>
            <div class="header-right hide-for-small">
                <a href="<?php echo esc_url($view_all_link); ?>" class="view-all-link">Xem tất cả ></a>
            </div>
        </div>

        <?php if (!empty($atts['mobile_banner'])): ?>
            <div class="gobike-mobile-banner show-for-small" style="margin-bottom:12px;">
                <a href="<?php echo esc_url($view_all_link); ?>">
                    <img src="<?php echo esc_url($atts['mobile_banner']); ?>" alt="<?php echo esc_attr($atts['title']); ?>" style="width:100%;border-radius:8px;" />
                </a>
            </div>
        <?php endif; ?>

        <!-- LƯỚI SẢN PHẨM: 1 SẢN PHẨM TO BÊN TRÁI + 8 SẢN PHẨM NHỎ BÊN PHẢI -->
        <div class="gobike-block-grid">
            <?php
            $count = 0;
            while ($query->have_posts()):
                $query->the_post();
                $count++;
                global $product;
                if (!is_a($product, 'WC_Product')) {
                    $product = wc_get_product(get_the_ID());
                }

                $permalink = get_permalink();
                $title = get_the_title();
                $image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium_large') ?: wc_placeholder_img_src();

                // Xử lý giá tiền & % Giảm
                $regular_price = (float) $product->get_regular_price();
                $sale_price = (float) $product->get_sale_price();
                $price_custom_html = '';
                $sale_badge_html = '';

                if ($product->is_on_sale() && $regular_price > $sale_price && $sale_price > 0) {
                    $percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
                    $sale_badge_html = '<span class="sale-badge-pill">-' . $percentage . '%</span>';
                    $price_custom_html = '<span class="price-current">' . wc_price($sale_price) . '</span>'
                        . '<span class="price-old">' . wc_price($regular_price) . '</span>';
                } else {
                    $price_custom_html = '<span class="price-current">' . $product->get_price_html() . '</span>';
                }

                if ($count === 1):
                    // 1. SẢN PHẨM TO ĐẦU TIÊN
                    ?>
                    <div class="gobike-big-item">
                        <div class="img-box">
                            <a href="<?php echo esc_url($permalink); ?>">
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" />
                            </a>
                            <?php echo $sale_badge_html; ?>
                        </div>
                        <div class="info-box">
                            <a href="<?php echo esc_url($permalink); ?>" class="product-title-link">
                                <h3 class="product-title"><?php echo esc_html($title); ?></h3>
                            </a>
                            <div class="gobike-price-box">
                                <?php echo $price_custom_html; ?>
                            </div>
                            <div class="spec-table">
                                <?php
                                $excerpt = get_the_excerpt();
                                if (!empty($excerpt)) {
                                    echo do_shortcode($excerpt);
                                } else {
                                    $attributes = $product->get_attributes();
                                    if (!empty($attributes)) {
                                        echo '<table>';
                                        foreach ($attributes as $attribute) {
                                            $name = wc_attribute_label($attribute->get_name());
                                            $values = array();
                                            if ($attribute->is_taxonomy()) {
                                                $attribute_values = wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'names'));
                                                $values = $attribute_values;
                                            } else {
                                                $values = $attribute->get_options();
                                            }
                                            echo '<tr><td><span>' . esc_html($name) . ':</span></td><td>' . esc_html(implode(', ', $values)) . '</td></tr>';
                                        }
                                        echo '</table>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php else:
                    // 2. TÁM SẢN PHẨM NHỎ
                    ?>
                    <div class="gobike-small-item">
                        <div class="img-wrap">
                            <a href="<?php echo esc_url($permalink); ?>">
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" />
                            </a>
                            <?php echo $sale_badge_html; ?>
                        </div>
                        <a href="<?php echo esc_url($permalink); ?>" class="item-title-link">
                            <h3 class="item-title"><?php echo esc_html($title); ?></h3>
                        </a>
                        <div class="gobike-price-box">
                            <?php echo $price_custom_html; ?>
                        </div>
                    </div>
                <?php
                endif;
            endwhile;
            wp_reset_postdata();
            ?>
        </div>

        <a href="<?php echo esc_url($view_all_link); ?>" class="gobike-mobile-viewmore">Xem tất cả
            <?php echo esc_html($atts['title']); ?></a>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('gobike_category_block', 'gobike_render_category_block');
