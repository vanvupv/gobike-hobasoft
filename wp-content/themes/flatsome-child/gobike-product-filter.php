<?php
/**
 * ============================================================================
 * GOBIKE PRODUCT FILTER & SORTING MODULE (CHỨC NĂNG BỘ LỌC TẬP TRUNG TOÀN DIỆN)
 * ============================================================================
 * Tất cả mã nguồn PHP, CSS và Javascript của bộ lọc đều nằm trọn trong file này.
 * 
 * Chức năng bao gồm:
 * 1. Thanh "Tìm theo:" ngang dạng Dropdown kèm checkbox, khoảng giá & nút Reset.
 * 2. Thanh "Xếp theo:" Radio button (Tên A-Z, Tên Z-A, Hàng mới, Giá thấp-cao, Giá cao-thấp).
 * 3. Dải hiển thị Active Filter Badges màu sắc rực rỡ chuẩn mẫu và nút Bỏ hết màu đỏ.
 * 4. Tự động chuyển đổi chuỗi 'price range' sang tiếng Việt tương ứng với khoảng giá đã chọn.
 * ============================================================================
 */

if (!defined('ABSPATH')) {
    exit; // Chặn truy cập trực tiếp
}

/* ============================================================================
 * 1. PHP LOGIC & QUERY HOOKS
 * ============================================================================
 */

// 1.1 Thêm các tùy chọn sắp xếp mới vào WooCommerce catalog
add_filter('woocommerce_catalog_orderby', 'gobike_filter_custom_catalog_orderby', 20);
function gobike_filter_custom_catalog_orderby($sortby)
{
    $custom_sortby = array(
        'title-asc' => 'Tên A-Z',
        'title-desc' => 'Tên Z-A',
        'date' => 'Hàng mới',
        'price' => 'Giá thấp đến cao',
        'price-desc' => 'Giá cao xuống thấp',
    );
    return array_merge($custom_sortby, $sortby);
}

// 1.2 Xử lý truy vấn WooCommerce khi sắp xếp theo tên A-Z / Z-A
add_filter('woocommerce_get_catalog_ordering_args', 'gobike_filter_custom_ordering_args', 20, 3);
function gobike_filter_custom_ordering_args($args, $orderby, $order)
{
    if ('title-asc' === $orderby) {
        $args['orderby'] = 'title';
        $args['order'] = 'ASC';
    } elseif ('title-desc' === $orderby) {
        $args['orderby'] = 'title';
        $args['order'] = 'DESC';
    }
    return $args;
}

// 1.3 Tương thích sắp xếp với HUSKY (WOOF AJAX)
add_filter('woof_order_catalog', 'gobike_filter_husky_order_catalog', 20);
function gobike_filter_husky_order_catalog($sort_args)
{
    if (isset($_GET['orderby'])) {
        $orderby = sanitize_text_field($_GET['orderby']);
        if ('title-asc' === $orderby) {
            $sort_args['orderby'] = 'title';
            $sort_args['order'] = 'ASC';
        } elseif ('title-desc' === $orderby) {
            $sort_args['orderby'] = 'title';
            $sort_args['order'] = 'DESC';
        } elseif ('price' === $orderby) {
            $sort_args['orderby'] = 'meta_value_num';
            $sort_args['order'] = 'ASC';
            $sort_args['meta_key'] = '_price';
        } elseif ('price-desc' === $orderby) {
            $sort_args['orderby'] = 'meta_value_num';
            $sort_args['order'] = 'DESC';
            $sort_args['meta_key'] = '_price';
        } elseif ('date' === $orderby) {
            $sort_args['orderby'] = 'date';
            $sort_args['order'] = 'DESC';
        }
    }
    return $sort_args;
}

// 1.4 Dịch các chuỗi HUSKY sang tiếng Việt (giải quyết triệt để vấn đề "price range")
add_filter('gettext', 'gobike_filter_translate_strings', 20, 3);
function gobike_filter_translate_strings($translated_text, $text, $domain)
{
    $text_lower = strtolower(trim($text));

    if ('price range' === $text_lower) {
        if (isset($_GET['min_price']) || isset($_GET['max_price'])) {
            $min = isset($_GET['min_price']) ? (float) $_GET['min_price'] : 0;
            $max = isset($_GET['max_price']) ? (float) $_GET['max_price'] : 0;

            if ($min <= 0 && $max > 0 && $max <= 5000000) {
                return 'Giá dưới 5.000.000đ';
            } elseif ($min >= 20000000 && ($max >= 100000000 || $max <= 0)) {
                return 'Giá trên 20.000.000đ';
            } elseif ($min > 0 && $max > 0) {
                return number_format($min, 0, ',', '.') . 'đ - ' . number_format($max, 0, ',', '.') . 'đ';
            }
        }
        return 'Khoảng giá';
    }

    if ('clear all' === $text_lower) {
        return 'Bỏ hết';
    }

    if ('reset' === $text_lower) {
        return 'Reset';
    }

    return $translated_text;
}

// 1.5 Hàm render thanh radio sắp xếp chuẩn mẫu GOBIKE
add_action('woocommerce_before_shop_loop', 'gobike_render_custom_sorting_toolbar', 35);
function gobike_render_custom_sorting_toolbar()
{
    static $rendered = false;
    if ($rendered) {
        return;
    }
    $rendered = true;

    $current_orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
    if ('menu_order' === $current_orderby || 'popularity' === $current_orderby || empty($current_orderby)) {
        $current_orderby = 'date';
    }
    ?>
    <div class="gobike-custom-sorting-toolbar container">
        <span class="sort-label">Xếp theo:</span>
        <div class="sort-options">
            <label class="sort-item">
                <input type="radio" name="gobike_sort_radio" value="title-asc" <?php checked($current_orderby, 'title-asc'); ?>>
                <span>Tên A-Z</span>
            </label>
            <label class="sort-item">
                <input type="radio" name="gobike_sort_radio" value="title-desc" <?php checked($current_orderby, 'title-desc'); ?>>
                <span>Tên Z-A</span>
            </label>
            <label class="sort-item">
                <input type="radio" name="gobike_sort_radio" value="date" <?php checked($current_orderby, 'date'); ?>>
                <span>Hàng mới</span>
            </label>
            <label class="sort-item">
                <input type="radio" name="gobike_sort_radio" value="price" <?php checked($current_orderby, 'price'); ?>>
                <span>Giá thấp đến cao</span>
            </label>
            <label class="sort-item">
                <input type="radio" name="gobike_sort_radio" value="price-desc" <?php checked($current_orderby, 'price-desc'); ?>>
                <span>Giá cao xuống thấp</span>
            </label>
        </div>
    </div>
    <?php
}

// 1.6 Shortcode thanh sắp xếp: [gobike_sorting_toolbar]
add_shortcode('gobike_sorting_toolbar', 'gobike_sorting_toolbar_shortcode');
function gobike_sorting_toolbar_shortcode()
{
    ob_start();
    gobike_render_custom_sorting_toolbar();
    return ob_get_clean();
}

// 1.7 Shortcode toàn bộ bộ lọc & thanh sắp xếp: [gobike_full_filter]
// (Dùng được ở mọi nơi: Trang tạo bằng Pages, UX Builder, Widget hoặc Template)
add_shortcode('gobike_full_filter', 'gobike_full_filter_shortcode');
function gobike_full_filter_shortcode()
{
    ob_start();
    ?>
    <div class="gobike-full-filter-wrapper container" style="margin: 10px 0 15px 0;">
        <?php
        if (shortcode_exists('woof')) {
            echo do_shortcode('[woof autohide="0" autosubmit="1" is_ajax="1"]');
        }
        gobike_render_custom_sorting_toolbar();
        ?>
    </div>
    <?php
    return ob_get_clean();
}


/* ============================================================================
 * 2. CSS STYLING (TOÀN BỘ CSS CỦA BỘ LỌC, SẮP XẾP & BADGES)
 * ============================================================================
 */
add_action('wp_head', 'gobike_filter_enqueue_styles', 99);
function gobike_filter_enqueue_styles()
{
    ?>
    <style id="gobike-filter-custom-styles" type="text/css">
        /* 1. Reset & bảo vệ bố cục Flatsome */
        .shop-page-title .woocommerce-result-count,
        .shop-page-title form.woocommerce-ordering,
        .woocommerce-result-count,
        form.woocommerce-ordering {
            display: none !important;
        }

        .shop-page-title .breadcrumbs,
        .shop-page-title .woocommerce-breadcrumb {
            display: block !important;
            visibility: visible !important;
        }

        /* 2. Thanh lọc HUSKY hàng ngang (Dòng 'Tìm theo:') */
        .woof_redraw_zone {
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 10px 12px !important;
            background: #fff !important;
            padding: 10px 0 !important;
            width: 100% !important;
            position: relative !important;
        }

        .woof_redraw_zone::before {
            content: "Tìm theo:";
            font-weight: 700;
            color: #d0021b;
            font-size: 14px;
            margin-right: 4px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
        }

        /* Từng khối lọc thành 1 nút dropdown */
        .woof_redraw_zone .woof_container {
            display: inline-flex !important;
            align-items: center !important;
            width: auto !important;
            float: none !important;
            clear: none !important;
            margin: 0 !important;
            padding: 0 !important;
            position: relative !important;
        }

        .woof_redraw_zone .woof_container_product_visibility {
            display: none !important;
        }

        /* Nút trigger dropdown của từng thuộc tính */
        .woof_redraw_zone .gobike-dropdown-btn,
        .woof_redraw_zone .woof_container h4 {
            margin: 0 !important;
            padding: 7px 16px !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            color: #333 !important;
            background: #fff !important;
            border: 1px solid #dcdcdc !important;
            border-radius: 6px !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            line-height: 1.3 !important;
            transition: all 0.2s ease !important;
            user-select: none !important;
        }

        .woof_redraw_zone .gobike-dropdown-btn:hover,
        .woof_redraw_zone .woof_container:hover>.gobike-dropdown-btn,
        .woof_redraw_zone .woof_container.active>.gobike-dropdown-btn {
            border-color: #d0021b !important;
            color: #d0021b !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06) !important;
        }

        .woof_redraw_zone .gobike-dropdown-btn::after {
            content: "";
            display: inline-block;
            width: 0;
            height: 0;
            margin-left: 4px;
            vertical-align: middle;
            border-top: 4px solid #666;
            border-right: 4px solid transparent;
            border-left: 4px solid transparent;
            transition: transform 0.2s ease;
        }

        .woof_redraw_zone .woof_container.active>.gobike-dropdown-btn::after {
            transform: rotate(180deg);
            border-top-color: #d0021b;
        }

        /* Popup danh sách lựa chọn bên dưới */
        .woof_redraw_zone .woof_container>.woof_container_inner {
            display: none;
            position: absolute !important;
            top: calc(100% + 6px) !important;
            left: 0 !important;
            min-width: 200px !important;
            max-width: 320px !important;
            background: #fff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            padding: 12px 14px !important;
            z-index: 99999 !important;
            max-height: 280px !important;
            overflow-y: auto !important;
        }

        .woof_redraw_zone .woof_container.active>.woof_container_inner {
            display: block !important;
        }

        /* Reset container inner lồng nhau của HUSKY Search by Price */
        .woof_redraw_zone .woof_container_inner .woof_container_inner {
            position: static !important;
            display: block !important;
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            max-height: none !important;
            overflow: visible !important;
        }

        /* Danh sách checkbox / radio bên trong popup */
        .woof_redraw_zone .woof_list {
            margin: 0 !important;
            padding: 0 !important;
            list-style: none !important;
        }

        .woof_redraw_zone .woof_list li {
            margin: 6px 0 !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            font-size: 13.5px !important;
            color: #333 !important;
        }

        .woof_redraw_zone .woof_list li label {
            cursor: pointer !important;
            margin: 0 0 0 6px !important;
            color: #333 !important;
            font-size: 13.5px !important;
            font-weight: 400 !important;
        }

        .woof_redraw_zone .woof_list li:hover label {
            color: #d0021b !important;
        }

        /* Ẩn hoàn toàn nút Reset màu xanh ở đuôi dòng 'Tìm theo:', chỉ giữ lại duy nhất nút 'Bỏ hết ✕' màu đỏ ở dải Badges */
        .woof_redraw_zone .woof_submit_search_form_container,
        .woof_redraw_zone .woof_reset_search_form_container,
        .woof_redraw_zone .woof_reset_search_form,
        .woof_redraw_zone button[data-name="reset"],
        .woof_redraw_zone .woof_btn_default {
            display: none !important;
        }

        /* 3. Thanh sắp xếp Radio (Dòng 'Xếp theo:') */
        .gobike-custom-sorting-toolbar {
            width: 100% !important;
            margin: 14px 0 18px 0 !important;
            padding: 12px 0 !important;
            border-top: 1px solid #eee !important;
            border-bottom: 1px solid #eee !important;
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            clear: both !important;
        }

        .gobike-custom-sorting-toolbar .sort-label {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #000 !important;
            white-space: nowrap !important;
            margin-right: 20px !important;
            line-height: 1 !important;
        }

        .gobike-custom-sorting-toolbar .sort-options {
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 16px 24px !important;
        }

        .gobike-custom-sorting-toolbar .sort-item {
            display: inline-flex !important;
            align-items: center !important;
            gap: 7px !important;
            margin: 0 !important;
            cursor: pointer !important;
            font-size: 14px !important;
            color: #111 !important;
            font-weight: 400 !important;
            user-select: none !important;
            line-height: 1 !important;
            transition: color 0.2s ease !important;
        }

        .gobike-custom-sorting-toolbar .sort-item:hover {
            color: #d0021b !important;
        }

        /* Nút tròn Radio tùy biến viền xám -> viền đỏ chấm đỏ ở tâm */
        .gobike-custom-sorting-toolbar .sort-item input[type="radio"] {
            appearance: none !important;
            -webkit-appearance: none !important;
            width: 16px !important;
            height: 16px !important;
            min-width: 16px !important;
            min-height: 16px !important;
            border: 2px solid #ccc !important;
            border-radius: 50% !important;
            background-color: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
            cursor: pointer !important;
            display: inline-grid !important;
            place-content: center !important;
            vertical-align: middle !important;
            transition: border-color 0.2s ease !important;
            box-shadow: none !important;
            outline: none !important;
        }

        .gobike-custom-sorting-toolbar .sort-item:hover input[type="radio"] {
            border-color: #d0021b !important;
        }

        .gobike-custom-sorting-toolbar .sort-item input[type="radio"]:checked {
            border-color: #d0021b !important;
            background-color: #fff !important;
        }

        .gobike-custom-sorting-toolbar .sort-item input[type="radio"]:checked::before {
            content: "" !important;
            width: 8px !important;
            height: 8px !important;
            border-radius: 50% !important;
            background-color: #d0021b !important;
            display: block !important;
        }

        /* 4. Dải Active Filter Badges màu sắc rực rỡ & nút Bỏ hết màu đỏ */
        .woof_products_top_panel,
        .woof_products_top_panel_ul {
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 8px 10px !important;
            margin: 10px 0 14px 0 !important;
            padding: 0 !important;
            list-style: none !important;
            width: 100% !important;
            clear: both !important;
        }

        .woof_products_top_panel li {
            margin: 0 !important;
            padding: 0 !important;
            list-style: none !important;
            display: inline-flex !important;
            align-items: center !important;
        }

        /* Ẩn các text nhãn thô lồng nhau như 'Thương hiệu:', 'Danh mục sản phẩm:' */
        .woof_products_top_panel ul li ul li:first-child {
            display: none !important;
        }

        .woof_products_top_panel ul li ul {
            display: inline-flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
            margin: 0 !important;
            padding: 0 !important;
            list-style: none !important;
        }

        /* Nút Bỏ hết (Clear all) - Nút màu đỏ nổi bật có dấu ✕ */
        .woof_products_top_panel .woof_reset_button_2,
        .woof_products_top_panel a.woof_clear_all {
            background-color: #d0021b !important;
            border: none !important;
            color: #fff !important;
            padding: 6px 14px !important;
            border-radius: 5px !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            line-height: 1.4 !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08) !important;
            transition: opacity 0.2s ease, transform 0.2s ease, background-color 0.2s ease !important;
        }

        .woof_products_top_panel .woof_reset_button_2:hover,
        .woof_products_top_panel a.woof_clear_all:hover {
            opacity: 0.9 !important;
            transform: translateY(-1px) !important;
            background-color: #b50217 !important;
            color: #fff !important;
        }

        .woof_products_top_panel .woof_reset_button_2::after {
            content: "✕" !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            margin-left: 4px !important;
        }

        /* Các badge bộ lọc đang chọn: Hiển thị đầy đủ chữ và icon */
        .woof_products_top_panel li a,
        .woof_redraw_zone .woof_crud_link {
            display: inline-flex !important;
            align-items: center !important;
            padding: 6px 14px !important;
            border-radius: 5px !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #fff !important;
            text-decoration: none !important;
            line-height: 1.4 !important;
            transition: opacity 0.2s ease, transform 0.2s ease !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08) !important;
        }

        /* Khôi phục hiển thị toàn bộ chữ nhãn giá trị badge bên trong span */
        .woof_products_top_panel span.woof_remove_ppi {
            display: inline !important;
            color: #fff !important;
            font-size: 13.5px !important;
            font-weight: 600 !important;
            line-height: inherit !important;
        }

        .woof_products_top_panel li a::after,
        .woof_redraw_zone .woof_crud_link::after {
            content: "✕" !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            margin-left: 6px !important;
            display: inline-block !important;
            opacity: 0.9 !important;
        }

        .woof_products_top_panel li a:hover,
        .woof_redraw_zone .woof_crud_link:hover {
            opacity: 0.9 !important;
            transform: translateY(-1px) !important;
            color: #fff !important;
        }

        /* Bảng màu rực rỡ luân phiên cho từng badge chuẩn ảnh mẫu */
        .woof_products_top_panel li a {
            background-color: #0099e6 !important;
        }

        .woof_products_top_panel li:nth-child(6n+1) a {
            background-color: #0099e6 !important;
        }

        /* Xanh dương */
        .woof_products_top_panel li:nth-child(6n+2) a {
            background-color: #e63946 !important;
        }

        /* San hô đỏ */
        .woof_products_top_panel li:nth-child(6n+3) a {
            background-color: #28a745 !important;
        }

        /* Xanh lá */
        .woof_products_top_panel li:nth-child(6n+4) a {
            background-color: #f59e0b !important;
        }

        /* Vàng cam */
        .woof_products_top_panel li:nth-child(6n+5) a {
            background-color: #d9048e !important;
        }

        /* Hồng magenta */
        .woof_products_top_panel li:nth-child(6n+6) a {
            background-color: #7209b7 !important;
        }

        /* Tím */

        /* Ẩn tag orderby trong top panel nếu lọt vào */
        .woof_redraw_zone .woof_crud_link[data-name="orderby"],
        .woof_products_top_panel li a[data-name="orderby"] {
            display: none !important;
        }
    </style>
    <?php
}


/* ============================================================================
 * 3. JAVASCRIPT LOGIC (ĐỒNG BỘ THỨ TỰ, RADIO, DROPDOWN, THAY THẾ 'PRICE RANGE')
 * ============================================================================
 */
add_action('wp_footer', 'gobike_filter_enqueue_scripts', 99);
function gobike_filter_enqueue_scripts()
{
    ?>
    <script id="gobike-filter-custom-scripts" type="text/javascript">
        jQuery(document).ready(function ($) {
            'use strict';

            // 3.0 Cố định thứ tự hiển thị chuẩn: 1. Tìm theo: -> 2. Badges & Bỏ hết -> 3. Xếp theo:
            function reorderGobikeFilterElements() {
                // Xóa triệt để các bản sao thừa nếu xuất hiện nhiều hơn 1 panel
                if ($('.woof_products_top_panel').length > 1) {
                    $('.woof_products_top_panel').not(':first').remove();
                }
                if ($('.woof_products_top_panel_content').length > 1) {
                    $('.woof_products_top_panel_content').not(':first').remove();
                }

                var $redrawZone = $('.woof_redraw_zone').first();
                var $topPanel = $('.woof_products_top_panel').first();
                var $sortingToolbar = $('.gobike-custom-sorting-toolbar').first();

                if ($redrawZone.length) {
                    // 1. Đảm bảo .woof_products_top_panel duy nhất luôn nằm NGAY DƯỚI .woof_redraw_zone ("Tìm theo:")
                    if ($topPanel.length) {
                        $topPanel.insertAfter($redrawZone);
                    }

                    // 2. Đảm bảo .gobike-custom-sorting-toolbar luôn nằm DƯỚI .woof_products_top_panel (hoặc dưới .woof_redraw_zone)
                    if ($sortingToolbar.length) {
                        if ($topPanel.length && $topPanel.is(':visible') && $topPanel.find('li').length > 0) {
                            $sortingToolbar.insertAfter($topPanel);
                        } else {
                            $sortingToolbar.insertAfter($redrawZone);
                        }
                    }
                }

                // Ẩn các text nhãn thô
                $('.woof_products_top_panel ul[data-container] > li').each(function () {
                    if (!$(this).find('a, button').length) {
                        $(this).hide();
                    }
                });

                // Ẩn triệt để nút Reset/Submit xanh ở dòng 'Tìm theo:'
                $('.woof_redraw_zone').find('.woof_submit_search_form_container, .woof_reset_search_form_container, .woof_reset_search_form').hide();
            }

            // 3.1 Khởi tạo các nút Dropdown cho từng khối lọc HUSKY
            function initHuskyDropdownButtons() {
                $('.woof_redraw_zone .woof_container').each(function () {
                    var $container = $(this);
                    if ($container.hasClass('woof_container_product_visibility')) return;

                    // Nếu chưa có nút dropdown trigger thì chèn thêm
                    if (!$container.children('.gobike-dropdown-btn').length) {
                        var title = '';
                        var $h4 = $container.find('h4').first();
                        if ($h4.length) {
                            title = $h4.text().trim();
                            $h4.hide();
                        } else if ($container.hasClass('woof_price_filter')) {
                            title = 'Khoảng giá';
                        } else if ($container.hasClass('woof_container_pa_thuong-hieu') || $container.hasClass('woof_container_pa_brand')) {
                            title = 'Thương hiệu';
                        } else if ($container.hasClass('woof_container_product_cat')) {
                            title = 'Danh mục sản phẩm';
                        } else if ($container.hasClass('woof_container_product_tag')) {
                            title = 'Thẻ sản phẩm';
                        } else {
                            title = 'Bộ lọc';
                        }

                        $container.prepend('<div class="gobike-dropdown-btn">' + title + '</div>');
                    }
                });
            }

            // Bật/tắt dropdown khi click vào nút
            $(document).on('click', '.gobike-dropdown-btn', function (e) {
                e.stopPropagation();
                var $parent = $(this).closest('.woof_container');
                var wasActive = $parent.hasClass('active');

                // Đóng tất cả dropdown khác
                $('.woof_redraw_zone .woof_container').removeClass('active');

                // Toggle dropdown hiện tại
                if (!wasActive) {
                    $parent.addClass('active');
                }
            });

            // Ngăn chặn đóng popup khi click bên trong popup
            $(document).on('click', '.woof_container_inner', function (e) {
                e.stopPropagation();
            });

            // Đóng dropdown khi click ra ngoài màn hình
            $(document).on('click', function () {
                $('.woof_redraw_zone .woof_container').removeClass('active');
            });

            // 3.2 Đồng bộ sự kiện chọn Radio button sắp xếp với WooCommerce ordering
            $(document).on('change', 'input[name="gobike_sort_radio"]', function () {
                var selectedVal = $(this).val();
                var $defaultForm = $('form.woocommerce-ordering');
                var $defaultSelect = $defaultForm.find('select.orderby');

                if ($defaultSelect.length) {
                    if (!$defaultSelect.find('option[value="' + selectedVal + '"]').length) {
                        $defaultSelect.append('<option value="' + selectedVal + '">' + selectedVal + '</option>');
                    }
                    $defaultSelect.val(selectedVal).trigger('change');
                    $defaultForm.submit();
                } else {
                    var url = new URL(window.location.href);
                    url.searchParams.set('orderby', selectedVal);
                    window.location.href = url.toString();
                }
            });

            // 3.3 Giải quyết triệt để vấn đề: Thay thế chữ "price range" thành khoảng giá tiếng Việt chính xác
            function formatHuskyPriceBadge() {
                // Lấy nhãn khoảng giá thực tế từ radio đang chọn hoặc từ URL
                var currentPriceLabel = '';
                var $checkedRadio = $('input[name="woof_price_radio"]:checked');
                if ($checkedRadio.length) {
                    currentPriceLabel = $checkedRadio.closest('li').find('label').text().trim();
                }

                if (!currentPriceLabel) {
                    var urlParams = new URLSearchParams(window.location.search);
                    var min = parseFloat(urlParams.get('min_price')) || 0;
                    var max = parseFloat(urlParams.get('max_price')) || 0;
                    if (min <= 0 && max > 0 && max <= 5000000) {
                        currentPriceLabel = 'Giá dưới 5.000.000đ';
                    } else if (min >= 20000000 && (max >= 100000000 || max <= 0)) {
                        currentPriceLabel = 'Giá trên 20.000.000đ';
                    } else if (min > 0 && max > 0) {
                        currentPriceLabel = min.toLocaleString('vi-VN') + 'đ - ' + max.toLocaleString('vi-VN') + 'đ';
                    }
                }

                if (!currentPriceLabel) {
                    currentPriceLabel = 'Khoảng giá';
                }

                // Quét toàn bộ các badge trong top panel và crud links
                $('.woof_products_top_panel li a, .woof_redraw_zone .woof_crud_link').each(function () {
                    var $link = $(this);
                    var $span = $link.find('.woof_remove_ppi');
                    var text = $span.length ? $span.text().trim() : $link.text().trim();

                    // Nếu chứa chữ 'price range' hoặc rỗng thì gán nhãn giá chính xác
                    if (/price\s*range/i.test(text) || text === '') {
                        if ($span.length) {
                            $span.text(currentPriceLabel);
                        } else {
                            $link.text(currentPriceLabel);
                        }
                    }
                });

                // Đảm bảo nút Clear all đổi thành 'Bỏ hết'
                $('.woof_products_top_panel a.woof_clear_all, .woof_products_top_panel .woof_reset_button_2').each(function () {
                    var $clear = $(this);
                    if (/clear\s*all/i.test($clear.text())) {
                        $clear.contents().each(function () {
                            if (this.nodeType === 3 && /clear\s*all/i.test(this.nodeValue)) {
                                this.nodeValue = 'Bỏ hết';
                            }
                        });
                    }
                });
            }

            // Chạy lần đầu khi DOM sẵn sàng
            reorderGobikeFilterElements();
            initHuskyDropdownButtons();
            formatHuskyPriceBadge();

            // Chạy lại sau mỗi lần HUSKY AJAX hoàn tất
            $(document).on('woof_ajax_done', function () {
                reorderGobikeFilterElements();
                initHuskyDropdownButtons();
                formatHuskyPriceBadge();
            });

            document.addEventListener('woof-ajax-form-redrawing', function () {
                setTimeout(function () {
                    reorderGobikeFilterElements();
                    initHuskyDropdownButtons();
                    formatHuskyPriceBadge();
                }, 50);
            });
        });
    </script>
    <?php
}
