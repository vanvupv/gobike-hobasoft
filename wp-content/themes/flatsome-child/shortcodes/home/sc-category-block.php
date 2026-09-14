<?php
/**
 * Shortcode: Khối danh mục Xe đạp trợ lực điện chuẩn giao diện GOBIKE
 * Cú pháp dùng trong Flatsome: [gobike_category_block cat="slug-danh-muc" title="TIÊU ĐỀ" subcat="Tên danh mục phụ" subcat_link="#" view_all="#"]
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
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
        return '<div style="padding:15px;background:#fff3cd;color:#856404;border:1px solid #ffeeba;margin:10px 0;">'
            . '⚠️ <strong>Chưa nhập slug danh mục!</strong>'
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
            <div class="gobike-mobile-banner show-for-small">
                <a href="<?php echo esc_url($view_all_link); ?>">
                    <img src="<?php echo esc_url($atts['mobile_banner']); ?>" alt="<?php echo esc_attr($atts['title']); ?>" />
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
                $image_url = get_the_post_thumbnail_url(get_the_ID(), 'medium_large');
                if (!$image_url) {
                    $image_url = wc_placeholder_img_src();
                }

                // Xử lý giá
                $regular_price = $product->get_regular_price();
                $sale_price = $product->get_sale_price();
                $price_custom_html = '';
                $sale_badge_html = '';

                if ($product->is_on_sale() && !empty($regular_price) && !empty($sale_price)) {
                    $percentage = round((($regular_price - $sale_price) / $regular_price) * 100);
                    $sale_badge_html = '<span class="sale-badge-pill">Giảm ' . $percentage . '%</span>';
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
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" />
                            </a>
                            <?php echo $sale_badge_html; ?>
                        </div>
                        <div class="info-box">
                            <a href="<?php echo esc_url($permalink); ?>">
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
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" />
                            </a>
                            <?php echo $sale_badge_html; ?>
                        </div>
                        <a href="<?php echo esc_url($permalink); ?>">
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
