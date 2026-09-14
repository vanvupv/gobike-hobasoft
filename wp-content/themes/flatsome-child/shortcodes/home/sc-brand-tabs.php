<?php
/**
 * Shortcode: Khối Xe Theo Thương Hiệu (Brand Tabs)
 * Cú pháp dùng trong Flatsome: [gobike_home_brand_tabs title="THƯƠNG HIỆU XE ĐẠP ĐIỆN NỔI BẬT"]
 * Lưu ý: File CSS được nhúng trực tiếp trong shortcode.
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

    $brands = array(
        array('slug' => 'xe-dap-tro-luc-dien-ado', 'name' => 'ADO E-Bike'),
        array('slug' => 'xe-phoenix', 'name' => 'PHOENIX'),
        array('slug' => 'xe-zhengbu', 'name' => 'ZHENGBU'),
        array('slug' => 'xe-dap-tro-luc-dien-ouxi', 'name' => 'OUXI'),
    );

    ob_start();
    ?>
    <!-- CSS NHÚNG TRỰC TIẾP TRONG SHORTCODE -->
    <style>
        .gobike-home-brand-tabs-block {
            max-width: 1230px;
            margin: 0 auto 30px auto;
            padding: 0 10px;
            box-sizing: border-box;
        }
        .gobike-brand-tabs-nav {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .brand-tab-btn {
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            border-radius: 25px;
            padding: 8px 20px;
            font-size: 14px;
            font-weight: 600;
            color: #555;
            cursor: pointer;
            transition: all 0.25s ease;
            outline: none;
        }
        .brand-tab-btn:hover {
            border-color: #149d29;
            color: #149d29;
            background: #f0fbf2;
        }
        .brand-tab-btn.active {
            background: #149d29;
            color: #fff;
            border-color: #149d29;
            box-shadow: 0 4px 10px rgba(20, 157, 41, 0.25);
        }
    </style>

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
            <?php
            echo do_shortcode('[gobike_category_block cat="' . esc_attr($brands[0]['slug']) . '" title="' . esc_attr($brands[0]['name']) . '" limit="' . intval($atts['limit']) . '"]');
            ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('gobike_home_brand_tabs', 'gobike_render_home_brand_tabs');
