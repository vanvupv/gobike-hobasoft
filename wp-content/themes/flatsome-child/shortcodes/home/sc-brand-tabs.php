<?php
/**
 * Shortcode: Khối Xe Theo Thương Hiệu (Brand Tabs)
 * Cú pháp dùng trong Flatsome: [gobike_home_brand_tabs title="THƯƠNG HIỆU XE ĐẠP ĐIỆN NỔI BẬT"]
 */

if (!defined('ABSPATH')) {
    exit;
}

function gobike_render_home_brand_tabs($atts)
{
    $atts = shortcode_atts(array(
        'title' => 'THƯƠNG HIỆU XE ĐIỆN HÀNG ĐẦU',
        'limit' => 8,
    ), $atts, 'gobike_home_brand_tabs');

    // Danh sách thương hiệu (slug của danh mục con trong Xe đạp trợ lực điện)
    $brands = array(
        array('slug' => 'xe-dap-tro-luc-dien-ado', 'name' => 'ADO E-Bike'),
        array('slug' => 'xe-phoenix', 'name' => 'PHOENIX'),
        array('slug' => 'xe-zhengbu', 'name' => 'ZHENGBU'),
        array('slug' => 'xe-dap-tro-luc-dien-ouxi', 'name' => 'OUXI'),
    );

    ob_start();
    ?>
    <div class="gobike-home-brand-tabs-block">
        <div class="gobike-block-header">
            <h2 class="block-title"><?php echo esc_html($atts['title']); ?></h2>
        </div>

        <div class="gobike-brand-tabs-nav">
            <?php foreach ($brands as $index => $brand): ?>
                <button type="button" class="brand-tab-btn <?php echo $index === 0 ? 'active' : ''; ?>" data-brand="<?php echo esc_attr($brand['slug']); ?>">
                    <?php echo esc_html($brand['name']); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div class="gobike-brand-tabs-content">
            <!-- Content render qua category block hoặc ajax -->
            <?php
            // Mặc định load thương hiệu đầu tiên
            echo do_shortcode('[gobike_category_block cat="' . esc_attr($brands[0]['slug']) . '" title="' . esc_attr($brands[0]['name']) . '" limit="' . intval($atts['limit']) . '"]');
            ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('gobike_home_brand_tabs', 'gobike_render_home_brand_tabs');
