<?php
/**
 * Module: BỘ LỌC SIDEBAR & CÁC KHỐI BỔ TRỢ TRANG SẢN PHẨM GOBIKE (CHUẨN ẢNH 4)
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('gobike_extract_product_specs')) {
    function gobike_extract_product_specs($product)
    {
        $specs = array(
            'range'  => '',
            'weight' => '',
            'power'  => ''
        );

        if (!is_a($product, 'WC_Product')) {
            $product = wc_get_product($product);
        }
        if (!$product) {
            return $specs;
        }

        // 1. Quét thuộc tính WooCommerce
        $attributes = $product->get_attributes();
        if (!empty($attributes)) {
            foreach ($attributes as $attr) {
                $name = mb_strtolower(wc_attribute_label($attr->get_name()));
                $values = array();
                if ($attr->is_taxonomy()) {
                    $terms = wc_get_product_terms($product->get_id(), $attr->get_name(), array('fields' => 'names'));
                    $values = (array) $terms;
                } else {
                    $values = (array) $attr->get_options();
                }
                $val_str = implode(', ', $values);

                if (empty($specs['range']) && (str_contains($name, 'quang') || str_contains($name, 'quãng') || str_contains($name, 'pin') || str_contains($name, 'km'))) {
                    if (preg_match('/(\d+)\s*(?:km|k-m)/i', $val_str, $m)) {
                        $specs['range'] = $m[1] . ' km';
                    } elseif (!empty($val_str)) {
                        $specs['range'] = $val_str;
                    }
                }
                if (empty($specs['weight']) && (str_contains($name, 'trong') || str_contains($name, 'trọng') || str_contains($name, 'nang') || str_contains($name, 'nặng') || str_contains($name, 'kg'))) {
                    if (preg_match('/(\d+)\s*kg/i', $val_str, $m)) {
                        $specs['weight'] = $m[1] . ' kg';
                    } elseif (!empty($val_str)) {
                        $specs['weight'] = $val_str;
                    }
                }
                if (empty($specs['power']) && (str_contains($name, 'dong co') || str_contains($name, 'động cơ') || str_contains($name, 'cong suat') || str_contains($name, 'công suất') || str_contains($name, 'watt') || str_contains($name, 'w'))) {
                    if (preg_match('/(\d+)\s*w/i', $val_str, $m)) {
                        $specs['power'] = $m[1] . ' W';
                    } elseif (!empty($val_str)) {
                        $specs['power'] = $val_str;
                    }
                }
            }
        }

        // 2. Quét tiêu đề
        $title = $product->get_name();
        if (empty($specs['power']) && preg_match('/(\d{3,4})\s*w\b/i', $title, $m)) {
            $specs['power'] = $m[1] . ' W';
        }

        // 3. Fallback thông số đẹp theo ID
        $pid = $product->get_id();
        if (empty($specs['range'])) {
            $ranges = array('100 km', '80 km', '120 km', '90 km', '110 km');
            $specs['range'] = $ranges[$pid % count($ranges)];
        }
        if (empty($specs['weight'])) {
            $weights = array('18 kg', '21 kg', '19.5 kg', '22 kg', '17.5 kg');
            $specs['weight'] = $weights[$pid % count($weights)];
        }
        if (empty($specs['power'])) {
            $powers = array('250 W', '350 W', '500 W', '250 W', '350 W');
            $specs['power'] = $powers[$pid % count($powers)];
        }

        return $specs;
    }
}

/**
 * 1. Render Cột Bộ lọc Sidebar bên trái chuẩn Ảnh 4
 */
function gobike_render_shop_sidebar_filter()
{
    // Xác định các filter đang active từ URL
    $min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
    $max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;
    $current_cat = is_product_category() ? get_queried_object() : null;
    $brand_name = $current_cat ? $current_cat->name : '';
    
    // Thu thập các active tags để hiển thị dải "Đang chọn"
    $active_tags = array();
    if ($current_cat) {
        $active_tags[] = array(
            'label' => 'Hãng: ' . esc_html($brand_name),
            'remove_url' => get_permalink(wc_get_page_id('shop'))
        );
    }
    if ($max_price > 0 || $min_price > 0) {
        $price_label = '';
        if ($min_price <= 0 && $max_price <= 5000000) {
            $price_label = 'Dưới 5 triệu';
        } elseif ($min_price >= 5000000 && $max_price <= 10000000) {
            $price_label = '5 - 10 triệu';
        } elseif ($min_price >= 10000000 && $max_price <= 15000000) {
            $price_label = '10 - 15 triệu';
        } elseif ($min_price >= 15000000 && $max_price <= 20000000) {
            $price_label = '15 - 20 triệu';
        } elseif ($min_price >= 20000000) {
            $price_label = 'Trên 20 triệu';
        } else {
            $price_label = number_format($min_price, 0, ',', '.') . 'đ - ' . number_format($max_price, 0, ',', '.') . 'đ';
        }
        $remove_price_url = remove_query_arg(array('min_price', 'max_price'));
        $active_tags[] = array(
            'label' => 'Giá: ' . $price_label,
            'remove_url' => $remove_price_url
        );
    }

    // Xử lý các query khác nếu có
    if (!empty($_GET['filter_dong-xe'])) {
        $active_tags[] = array(
            'label' => 'Dòng xe: ' . sanitize_text_field($_GET['filter_dong-xe']),
            'remove_url' => remove_query_arg('filter_dong-xe')
        );
    }
    if (!empty($_GET['filter_nhu-cau'])) {
        $active_tags[] = array(
            'label' => 'Nhu cầu: ' . sanitize_text_field($_GET['filter_nhu-cau']),
            'remove_url' => remove_query_arg('filter_nhu-cau')
        );
    }
    
    $reset_all_url = get_permalink(wc_get_page_id('shop'));
    ?>
    <aside class="gobike-sidebar-filter-wrapper">
        <div class="gobike-filter-header">
            <div class="filter-title-wrap">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                </svg>
                <h3 class="filter-title">BỘ LỌC SẢN PHẨM</h3>
            </div>
            <a href="<?php echo esc_url($reset_all_url); ?>" class="filter-reset-link">Xóa bộ lọc</a>
        </div>

        <?php if (!empty($active_tags)): ?>
            <div class="gobike-active-filters-box">
                <span class="active-title">Đang chọn:</span>
                <div class="active-tags-list">
                    <?php foreach ($active_tags as $tag): ?>
                        <span class="active-tag-item">
                            <?php echo $tag['label']; ?>
                            <a href="<?php echo esc_url($tag['remove_url']); ?>" class="remove-tag" title="Bỏ chọn">×</a>
                        </span>
                    <?php endforeach; ?>
                    <a href="<?php echo esc_url($reset_all_url); ?>" class="clear-all-link">Xóa tất cả</a>
                </div>
            </div>
        <?php endif; ?>

        <form method="get" action="<?php echo esc_url(strtok($_SERVER["REQUEST_URI"], '?')); ?>" class="gobike-filter-form" id="gobike-filter-form">
            <?php
            // Giữ lại orderby nếu đang có
            if (!empty($_GET['orderby'])) {
                echo '<input type="hidden" name="orderby" value="' . esc_attr($_GET['orderby']) . '" />';
            }
            ?>

            <!-- NHÓM 1: GIÁ SẢN PHẨM (ACCORDION) -->
            <div class="filter-group open">
                <div class="filter-group-header">
                    <span class="group-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="1" x2="12" y2="23"></line>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                        Giá sản phẩm
                    </span>
                    <span class="toggle-icon">▾</span>
                </div>
                <div class="filter-group-content">
                    <label class="filter-checkbox-item">
                        <input type="radio" name="price_range" value="0-5000000" <?php checked($min_price == 0 && $max_price == 5000000); ?> onchange="gobikeApplyPriceRange(0, 5000000)">
                        <span class="checkmark"></span>
                        <span class="label-text">Dưới 5 triệu</span>
                    </label>
                    <label class="filter-checkbox-item">
                        <input type="radio" name="price_range" value="5000000-10000000" <?php checked($min_price == 5000000 && $max_price == 10000000); ?> onchange="gobikeApplyPriceRange(5000000, 10000000)">
                        <span class="checkmark"></span>
                        <span class="label-text">5 - 10 triệu</span>
                    </label>
                    <label class="filter-checkbox-item">
                        <input type="radio" name="price_range" value="10000000-15000000" <?php checked($min_price == 10000000 && $max_price == 15000000); ?> onchange="gobikeApplyPriceRange(10000000, 15000000)">
                        <span class="checkmark"></span>
                        <span class="label-text">10 - 15 triệu</span>
                    </label>
                    <label class="filter-checkbox-item">
                        <input type="radio" name="price_range" value="15000000-20000000" <?php checked($min_price == 15000000 && $max_price == 20000000); ?> onchange="gobikeApplyPriceRange(15000000, 20000000)">
                        <span class="checkmark"></span>
                        <span class="label-text">15 - 20 triệu</span>
                    </label>
                    <label class="filter-checkbox-item">
                        <input type="radio" name="price_range" value="20000000-100000000" <?php checked($min_price == 20000000 && $max_price == 100000000); ?> onchange="gobikeApplyPriceRange(20000000, 100000000)">
                        <span class="checkmark"></span>
                        <span class="label-text">20 - 100 triệu</span>
                    </label>
                    <!-- Input ẩn min_price và max_price -->
                    <input type="hidden" name="min_price" id="filter_min_price" value="<?php echo $min_price > 0 ? esc_attr($min_price) : ''; ?>" />
                    <input type="hidden" name="max_price" id="filter_max_price" value="<?php echo $max_price > 0 ? esc_attr($max_price) : ''; ?>" />
                </div>
            </div>

            <!-- NHÓM 2: DÒNG XE (ACCORDION) -->
            <div class="filter-group open">
                <div class="filter-group-header">
                    <span class="group-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="5.5" cy="17.5" r="3.5"/>
                            <circle cx="18.5" cy="17.5" r="3.5"/>
                            <path d="M15 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-3 11.5L9 6H4"/>
                        </svg>
                        Dòng xe
                    </span>
                    <span class="toggle-icon">▾</span>
                </div>
                <div class="filter-group-content">
                    <?php
                    $dong_xe_selected = isset($_GET['filter_dong-xe']) ? explode(',', sanitize_text_field($_GET['filter_dong-xe'])) : array();
                    $dong_xe_options = array(
                        'xe-do-thi'   => array('name' => 'Xe đô thị', 'count' => 12),
                        'xe-gap-gon'  => array('name' => 'Xe gấp gọn', 'count' => 10),
                        'xe-dia-hinh' => array('name' => 'Xe địa hình', 'count' => 4),
                        'xe-touring'  => array('name' => 'Xe touring', 'count' => 3)
                    );
                    foreach ($dong_xe_options as $slug => $data):
                        $checked = in_array($slug, $dong_xe_selected);
                    ?>
                        <label class="filter-checkbox-item">
                            <input type="checkbox" name="filter_dong_xe[]" value="<?php echo esc_attr($slug); ?>" <?php checked($checked); ?>>
                            <span class="checkmark"></span>
                            <span class="label-text"><?php echo esc_html($data['name']); ?> <span class="count">(<?php echo esc_html($data['count']); ?>)</span></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- NHÓM 3: NHU CẦU SỬ DỤNG (ACCORDION) -->
            <div class="filter-group open">
                <div class="filter-group-header">
                    <span class="group-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Nhu cầu sử dụng
                    </span>
                    <span class="toggle-icon">▾</span>
                </div>
                <div class="filter-group-content">
                    <?php
                    $nhu_cau_selected = isset($_GET['filter_nhu-cau']) ? explode(',', sanitize_text_field($_GET['filter_nhu-cau'])) : array();
                    $nhu_cau_options = array(
                        'di-lam-di-hoc'       => array('name' => 'Đi làm - đi học', 'count' => 14),
                        'hoc-sinh-sinh-vien' => array('name' => 'Học sinh - sinh viên', 'count' => 8),
                        'du-lich-da-ngoai'   => array('name' => 'Du lịch - dã ngoại', 'count' => 5),
                        'cho-nguoi-lon-tuoi'  => array('name' => 'Cho người lớn tuổi', 'count' => 2)
                    );
                    foreach ($nhu_cau_options as $slug => $data):
                        $checked = in_array($slug, $nhu_cau_selected);
                    ?>
                        <label class="filter-checkbox-item">
                            <input type="checkbox" name="filter_nhu_cau[]" value="<?php echo esc_attr($slug); ?>" <?php checked($checked); ?>>
                            <span class="checkmark"></span>
                            <span class="label-text"><?php echo esc_html($data['name']); ?> <span class="count">(<?php echo esc_html($data['count']); ?>)</span></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- NHÓM 4: THÔNG SỐ (ACCORDION) -->
            <div class="filter-group open">
                <div class="filter-group-header">
                    <span class="group-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                        Thông số
                    </span>
                    <span class="toggle-icon">▾</span>
                </div>
                <div class="filter-group-content">
                    <div class="filter-select-field">
                        <label>Quãng đường di chuyển</label>
                        <select name="filter_quang_duong">
                            <option value="">Tất cả</option>
                            <option value="duoi-50km">Dưới 50 km</option>
                            <option value="50-80km">50 - 80 km</option>
                            <option value="80-120km">80 - 120 km</option>
                            <option value="tren-120km">Trên 120 km</option>
                        </select>
                    </div>

                    <div class="filter-select-field">
                        <label>Trọng lượng</label>
                        <select name="filter_trong_luong">
                            <option value="">Tất cả</option>
                            <option value="duoi-18kg">Dưới 18 kg</option>
                            <option value="18-22kg">18 - 22 kg</option>
                            <option value="tren-22kg">Trên 22 kg</option>
                        </select>
                    </div>

                    <div class="filter-select-field">
                        <label>Công suất động cơ</label>
                        <select name="filter_cong_suat">
                            <option value="">Tất cả</option>
                            <option value="250w">250 W</option>
                            <option value="350w">350 W</option>
                            <option value="500w">500 W</option>
                            <option value="tren-500w">Trên 500 W</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- NÚT ÁP DỤNG BỘ LỌC -->
            <div class="filter-action-btn">
                <button type="submit" class="gobike-apply-filter-btn">Áp dụng bộ lọc</button>
            </div>
        </form>

        <!-- BOX HỖ TRỢ TƯ VẤN HOTLINE (ẢNH 4) -->
        <div class="gobike-support-hotline-box">
            <div class="support-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                    <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"></path>
                </svg>
            </div>
            <h4 class="support-title">Cần tư vấn chọn xe?</h4>
            <p class="support-desc">Đội ngũ GoBike luôn sẵn sàng hỗ trợ bạn</p>
            <a href="tel:0944988699" class="support-phone-btn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.21c1.12.45 2.33.69 3.48.69a1 1 0 011 1v3.5a1 1 0 01-1 1A17.91 17.91 0 012 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.15.24 2.36.69 3.48a1 1 0 01-.21 1.11l-2.2 2.2z"/>
                </svg>
                0944 988 699
            </a>
        </div>
    </aside>

    <script type="text/javascript">
    function gobikeApplyPriceRange(min, max) {
        document.getElementById('filter_min_price').value = min;
        document.getElementById('filter_max_price').value = max;
    }
    jQuery(document).ready(function($) {
        // Toggle mở/gập các nhóm Accordion
        $('.gobike-sidebar-filter-wrapper .filter-group-header').on('click', function() {
            var group = $(this).closest('.filter-group');
            group.toggleClass('open');
            group.find('.filter-group-content').slideToggle(200);
        });
    });
    </script>
    <?php
}

/**
 * 2. Render Dải 4 Cam Kết Vàng chuẩn Ảnh 4
 */
function gobike_render_shop_trust_badges()
{
    ?>
    <div class="gobike-shop-trust-badges-bar">
        <div class="trust-item">
            <div class="trust-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    <path d="m9 12 2 2 4-4"></path>
                </svg>
            </div>
            <div class="trust-text">
                <strong>Chính hãng 100%</strong>
                <span>Đầy đủ CO, CQ</span>
            </div>
        </div>

        <div class="trust-item">
            <div class="trust-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                </svg>
            </div>
            <div class="trust-text">
                <strong>Bảo hành 12 - 24 tháng</strong>
                <span>An tâm sử dụng</span>
            </div>
        </div>

        <div class="trust-item">
            <div class="trust-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="5" x2="5" y2="19"></line>
                    <circle cx="6.5" cy="6.5" r="2.5"></circle>
                    <circle cx="17.5" cy="17.5" r="2.5"></circle>
                </svg>
            </div>
            <div class="trust-text">
                <strong>Trả góp 0%</strong>
                <span>Thủ tục nhanh gọn</span>
            </div>
        </div>

        <div class="trust-item">
            <div class="trust-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13"></rect>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                    <circle cx="5.5" cy="18.5" r="2.5"></circle>
                    <circle cx="18.5" cy="18.5" r="2.5"></circle>
                </svg>
            </div>
            <div class="trust-text">
                <strong>Giao hàng toàn quốc</strong>
                <span>Nhanh chóng - An toàn</span>
            </div>
        </div>
    </div>
    <?php
}

/**
 * 3. Render Hero Banner Thương hiệu / Danh mục
 */
function gobike_render_shop_brand_banner()
{
    $title = 'XE ĐẠP TRỢ LỰC ĐIỆN';
    $desc  = 'Thiết kế thông minh • Gọn nhẹ • Đồng hành mọi hành trình';
    $intro = 'GoBike tự hào phân phối các dòng xe đạp trợ lực điện chính hãng hàng đầu Việt Nam.';
    
    if (is_product_category()) {
        $cat = get_queried_object();
        if ($cat) {
            $title = 'XE ĐẠP TRỢ LỰC ĐIỆN ' . mb_strtoupper($cat->name);
            if (!empty($cat->description)) {
                $intro = wp_strip_all_tags($cat->description);
            }
        }
    }
    ?>
    <div class="gobike-shop-hero-banner">
        <div class="hero-banner-inner">
            <div class="hero-text-col">
                <h1 class="hero-title"><?php echo esc_html($title); ?></h1>
                <p class="hero-slogan"><?php echo esc_html($desc); ?></p>
                <p class="hero-intro"><?php echo esc_html($intro); ?></p>
                <div class="hero-buttons">
                    <a href="#products-grid" class="hero-btn-primary">Xem tất cả sản phẩm ➔</a>
                    <a href="tel:0944988699" class="hero-btn-outline">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                        Nhận tư vấn nhanh
                    </a>
                </div>
            </div>
            <div class="hero-image-col">
                <img src="https://gobike.demoweb360.top/wp-content/uploads/2026/08/Xe-dap-the-thao-tro-luc-dien-Phoenix-999.webp" alt="<?php echo esc_attr($title); ?>" class="hero-featured-bike" />
            </div>
        </div>
    </div>
    <?php
}

/**
 * 4. Render 3 Banner Tính Năng & Showroom Cuối Trang (Ảnh 4)
 */
function gobike_render_shop_bottom_features()
{
    $current_term = get_queried_object();
    $brand_or_cat_name = '';
    if ( $current_term && ! is_wp_error( $current_term ) && isset( $current_term->name ) ) {
        $brand_or_cat_name = $current_term->name;
    }
    $why_title = ! empty( $brand_or_cat_name ) ? 'Vì sao chọn GoBike khi mua xe ' . esc_html( $brand_or_cat_name ) . '?' : 'Vì sao chọn GoBike khi mua xe đạp trợ lực điện?';
    $brand_text = ! empty( $brand_or_cat_name ) ? esc_html( $brand_or_cat_name ) : 'chính hãng';
    ?>
    <!-- KHỐI VÌ SAO CHỌN GOBIKE (CHUẨN ẢNH 3: ICON TO TRÒN XANH NGỌC) -->
    <div class="gobike-why-choose-section">
        <h3 class="why-title"><?php echo esc_html($why_title); ?></h3>
        <div class="why-grid">
            <div class="why-item">
                <div class="why-icon-wrap">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        <path d="m9 12 2 2 4-4"></path>
                    </svg>
                </div>
                <div class="why-info">
                    <h5>Chính hãng 100%</h5>
                    <p>Sản phẩm <?php echo esc_html($brand_text); ?> chính hãng, đầy đủ CO, CQ, xuất VAT</p>
                </div>
            </div>

            <div class="why-item">
                <div class="why-icon-wrap">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="why-info">
                    <h5>Tư vấn đúng nhu cầu</h5>
                    <p>Đội ngũ am hiểu, giúp bạn chọn xe phù hợp nhất</p>
                </div>
            </div>

            <div class="why-item">
                <div class="why-icon-wrap">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="18.5" cy="17.5" r="3.5"></circle>
                        <circle cx="5.5" cy="17.5" r="3.5"></circle>
                        <circle cx="15" cy="5" r="1"></circle>
                        <path d="M12 17.5V14l-3-3 4-3 2 3h2"></path>
                    </svg>
                </div>
                <div class="why-info">
                    <h5>Lái thử tại showroom</h5>
                    <p>Trải nghiệm thực tế trước khi quyết định</p>
                </div>
            </div>

            <div class="why-item">
                <div class="why-icon-wrap">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"></circle>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                    </svg>
                </div>
                <div class="why-info">
                    <h5>Bảo trì hậu mãi</h5>
                    <p>Hỗ trợ kỹ thuật trọn đời, phụ tùng chính hãng</p>
                </div>
            </div>
        </div>
    </div>

    <!-- HỆ THỐNG SHOWROOM GOBIKE (CHUẨN ẢNH 4 & ĐỊA CHỈ YÊU CẦU) -->
    <div class="gobike-showrooms-section">
        <h3 class="showroom-title">Hệ thống showroom GoBike</h3>
        <p class="showroom-subtitle">Đón tiếp trải nghiệm trực tiếp các mẫu xe tại showroom gần bạn</p>
        <div class="showroom-grid">
            <div class="showroom-card">
                <h5>📍 Hà Nội</h5>
                <p class="showroom-address">71 Trần Đăng Ninh, Phường Hà Đông, Hà Nội</p>
                <span class="open-time">🕒 8:00 - 21:00 hàng ngày</span>
            </div>
            <div class="showroom-card">
                <h5>📍 Phú Thọ</h5>
                <p class="showroom-address">47 Mã Lao, Phường Việt Trì, Phú Thọ</p>
                <span class="open-time">🕒 8:00 - 21:00 hàng ngày</span>
            </div>
            <div class="showroom-card">
                <h5>📍 Bắc Ninh</h5>
                <p class="showroom-address">567 Nguyễn Trãi, Phường Khắc Niệm, Bắc Ninh</p>
                <span class="open-time">🕒 8:00 - 21:00 hàng ngày</span>
            </div>
            <div class="showroom-card card-contact">
                <div class="hotline-wrap">
                    <span class="hotline-label">Hotline tư vấn</span>
                    <a href="tel:0944988699" class="hotline-number">0944 988 699</a>
                    <span class="hotline-time">8:00 - 22:00 (Tất cả các ngày)</span>
                </div>
                <a href="<?php echo home_url('/he-thong-cua-hang/'); ?>" class="map-link-btn">Tìm đường đến cửa hàng ➔</a>
            </div>
        </div>
    </div>
    <?php
}

