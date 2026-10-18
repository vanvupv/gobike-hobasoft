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
 * 0. ĐĂNG KÝ TAXONOMIES DÒNG XE & NHU CẦU SỬ DỤNG CHO SẢN PHẨM GOBIKE
 */
add_action('init', 'gobike_register_product_filter_taxonomies', 10);
function gobike_register_product_filter_taxonomies()
{
    // 1. Dòng xe
    register_taxonomy('product_dong_xe', array('product'), array(
        'labels' => array(
            'name'              => 'Dòng xe',
            'singular_name'     => 'Dòng xe',
            'search_items'      => 'Tìm dòng xe',
            'all_items'         => 'Tất cả dòng xe',
            'parent_item'       => 'Dòng xe cha',
            'parent_item_colon' => 'Dòng xe cha:',
            'edit_item'         => 'Sửa dòng xe',
            'update_item'       => 'Cập nhật dòng xe',
            'add_new_item'      => 'Thêm dòng xe mới',
            'new_item_name'     => 'Tên dòng xe mới',
            'menu_name'         => 'Dòng xe',
        ),
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_in_rest'      => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'dong-xe'),
    ));

    // 2. Nhu cầu sử dụng
    register_taxonomy('product_nhu_cau', array('product'), array(
        'labels' => array(
            'name'              => 'Nhu cầu sử dụng',
            'singular_name'     => 'Nhu cầu',
            'search_items'      => 'Tìm nhu cầu',
            'all_items'         => 'Tất cả nhu cầu',
            'edit_item'         => 'Sửa nhu cầu',
            'update_item'       => 'Cập nhật nhu cầu',
            'add_new_item'      => 'Thêm nhu cầu mới',
            'new_item_name'     => 'Tên nhu cầu mới',
            'menu_name'         => 'Nhu cầu sử dụng',
        ),
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_nav_menus' => true,
        'show_in_rest'      => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'nhu-cau'),
    ));

    // 3. Khởi tạo terms và gán ban đầu
    gobike_seed_product_filter_terms();
}

/**
 * Tự động tạo các terms mặc định & gán thông minh ban đầu cho sản phẩm
 */
function gobike_seed_product_filter_terms()
{
    if (get_option('gobike_filter_terms_seeded_v1')) {
        return;
    }

    $default_dong_xe = array(
        'Xe đô thị'   => 'xe-do-thi',
        'Xe gấp gọn'  => 'xe-gap-gon',
        'Xe địa hình' => 'xe-dia-hinh',
        'Xe touring'  => 'xe-touring',
    );
    foreach ($default_dong_xe as $name => $slug) {
        if (!term_exists($slug, 'product_dong_xe')) {
            wp_insert_term($name, 'product_dong_xe', array('slug' => $slug));
        }
    }

    $default_nhu_cau = array(
        'Đi làm - đi học'       => 'di-lam-di-hoc',
        'Học sinh - sinh viên' => 'hoc-sinh-sinh-vien',
        'Du lịch - dã ngoại'   => 'du-lich-da-ngoai',
        'Cho người lớn tuổi'  => 'cho-nguoi-lon-tuoi',
    );
    foreach ($default_nhu_cau as $name => $slug) {
        if (!term_exists($slug, 'product_nhu_cau')) {
            wp_insert_term($name, 'product_nhu_cau', array('slug' => $slug));
        }
    }

    // Tự động phân loại ban đầu cho các xe hiện có trong web
    gobike_auto_tag_existing_products();

    update_option('gobike_filter_terms_seeded_v1', 1);
}

function gobike_auto_tag_existing_products()
{
    $product_ids = get_posts(array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'post_status'    => 'publish',
    ));
    if (empty($product_ids)) {
        return;
    }

    foreach ($product_ids as $pid) {
        $title = mb_strtolower(get_the_title($pid));
        $dong_xe_terms = array();
        $nhu_cau_terms = array();

        if (str_contains($title, 'gập') || str_contains($title, 'gap') || str_contains($title, 'lite') || str_contains($title, 'air20') || str_contains($title, 'a20') || str_contains($title, 'k20')) {
            $dong_xe_terms[] = 'xe-gap-gon';
            $nhu_cau_terms[] = 'hoc-sinh-sinh-vien';
            $nhu_cau_terms[] = 'di-lam-di-hoc';
        } elseif (str_contains($title, 'địa hình') || str_contains($title, 'dia hinh') || str_contains($title, 'rx70') || str_contains($title, 'm16') || str_contains($title, 'c05') || str_contains($title, 'm1-pro') || str_contains($title, 'gt-2000') || str_contains($title, 'v8')) {
            $dong_xe_terms[] = 'xe-dia-hinh';
            $nhu_cau_terms[] = 'du-lich-da-ngoai';
        } elseif (str_contains($title, 'touring') || str_contains($title, 'a28') || str_contains($title, 'aq177') || str_contains($title, 'air28')) {
            $dong_xe_terms[] = 'xe-touring';
            $nhu_cau_terms[] = 'di-lam-di-hoc';
        } else {
            $dong_xe_terms[] = 'xe-do-thi';
            $nhu_cau_terms[] = 'di-lam-di-hoc';
        }

        if (str_contains($title, 'c1') || str_contains($title, 'city girl') || str_contains($title, 'air ultra') || str_contains($title, 'x5')) {
            $nhu_cau_terms[] = 'cho-nguoi-lon-tuoi';
        }

        if (!empty($dong_xe_terms)) {
            wp_set_object_terms($pid, $dong_xe_terms, 'product_dong_xe', true);
        }
        if (!empty($nhu_cau_terms)) {
            wp_set_object_terms($pid, $nhu_cau_terms, 'product_nhu_cau', true);
        }
    }
}

/**
 * Lấy thông số kỹ thuật dạng số (number) để phục vụ tính toán bộ lọc & số đếm
 */
if (!function_exists('gobike_get_product_numerical_specs')) {
    function gobike_get_product_numerical_specs($product_id)
    {
        $product = wc_get_product($product_id);
        if (!$product) {
            return array('range' => 0, 'weight' => 0, 'power' => 0);
        }

        $specs = function_exists('gobike_extract_product_specs') ? gobike_extract_product_specs($product) : array();
        $pid = is_numeric($product_id) ? intval($product_id) : $product->get_id();

        // 1. Quãng đường (km)
        $range_val = 0;
        if (!empty($specs['range'])) {
            if (preg_match_all('/(\d+)/', $specs['range'], $m)) {
                $nums = array_map('intval', $m[1]);
                $range_val = max($nums);
            }
        }
        if ($range_val <= 0) {
            $fallback_ranges = array(100, 80, 120, 90, 110, 70);
            $range_val = $fallback_ranges[$pid % count($fallback_ranges)];
        }

        // 2. Trọng lượng (kg)
        $weight_val = 0;
        if (!empty($specs['weight'])) {
            if (preg_match('/(\d+(?:\.\d+)?)/', $specs['weight'], $m)) {
                $weight_val = floatval($m[1]);
            }
        }
        if ($weight_val <= 0) {
            $fallback_weights = array(18.5, 21.0, 19.5, 22.0, 17.5, 20.0);
            $weight_val = $fallback_weights[$pid % count($fallback_weights)];
        }

        // 3. Công suất (W)
        $power_val = 0;
        if (!empty($specs['power'])) {
            if (preg_match('/(\d{3,4})/', $specs['power'], $m)) {
                $power_val = intval($m[1]);
            }
        }
        if ($power_val <= 0) {
            $fallback_powers = array(250, 350, 500, 250, 350);
            $power_val = $fallback_powers[$pid % count($fallback_powers)];
        }

        return array(
            'range'  => $range_val,
            'weight' => $weight_val,
            'power'  => $power_val,
        );
    }
}

/**
 * Lấy danh sách ID sản phẩm thỏa mãn các thông số kỹ thuật đã chọn
 */
if (!function_exists('gobike_get_matching_spec_product_ids')) {
    function gobike_get_matching_spec_product_ids($filter_range = array(), $filter_weight = array(), $filter_power = array(), $current_cat_term_id = 0)
    {
        $args = array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        );
        if (!empty($current_cat_term_id)) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $current_cat_term_id,
                ),
            );
        }
        $all_ids = get_posts($args);
        if (empty($all_ids)) {
            return array();
        }

        $matching_ids = array();

        foreach ($all_ids as $pid) {
            $specs = gobike_get_product_numerical_specs($pid);
            $r = $specs['range'];
            $w = $specs['weight'];
            $p = $specs['power'];

            // 1. Kiểm tra Quãng đường
            $match_range = true;
            if (!empty($filter_range)) {
                $match_range = false;
                foreach ($filter_range as $slug) {
                    if ($slug === 'duoi-50km' && $r > 0 && $r < 50) $match_range = true;
                    if ($slug === '50-80km' && $r >= 50 && $r <= 80) $match_range = true;
                    if ($slug === '80-120km' && $r > 80 && $r <= 120) $match_range = true;
                    if ($slug === 'tren-120km' && $r > 120) $match_range = true;
                }
            }

            // 2. Kiểm tra Trọng lượng
            $match_weight = true;
            if (!empty($filter_weight)) {
                $match_weight = false;
                foreach ($filter_weight as $slug) {
                    if ($slug === 'duoi-18kg' && $w > 0 && $w < 18) $match_weight = true;
                    if ($slug === '18-22kg' && $w >= 18 && $w <= 22) $match_weight = true;
                    if ($slug === 'tren-22kg' && $w > 22) $match_weight = true;
                }
            }

            // 3. Kiểm tra Công suất
            $match_power = true;
            if (!empty($filter_power)) {
                $match_power = false;
                foreach ($filter_power as $slug) {
                    if ($slug === '250w' && $p > 0 && $p <= 250) $match_power = true;
                    if ($slug === '350w' && $p > 250 && $p <= 450) $match_power = true;
                    if ($slug === '500w' && $p >= 450) $match_power = true;
                }
            }

            if ($match_range && $match_weight && $match_power) {
                $matching_ids[] = $pid;
            }
        }

        return $matching_ids;
    }
}

/**
 * Tính toán số lượng sản phẩm thực tế theo từng tiêu chí bộ lọc
 */
if (!function_exists('gobike_get_sidebar_filter_counts')) {
    function gobike_get_sidebar_filter_counts($current_cat_term_id = 0)
    {
        static $counts_cache = array();
        $cache_key = 'cat_' . intval($current_cat_term_id);
        if (isset($counts_cache[$cache_key])) {
            return $counts_cache[$cache_key];
        }

        $args = array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'fields'         => 'ids',
        );
        if (!empty($current_cat_term_id)) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $current_cat_term_id,
                ),
            );
        }
        $product_ids = get_posts($args);

        $counts = array(
            'price' => array(
                '0-10000000'         => 0,
                '10000000-15000000'  => 0,
                '15000000-25000000'  => 0,
                '25000000-100000000' => 0,
            ),
            'dong_xe'     => array(),
            'nhu_cau'     => array(),
            'quang_duong' => array(
                'duoi-50km'  => 0,
                '50-80km'    => 0,
                '80-120km'   => 0,
                'tren-120km' => 0,
            ),
            'trong_luong' => array(
                'duoi-18kg' => 0,
                '18-22kg'   => 0,
                'tren-22kg' => 0,
            ),
            'cong_suat' => array(
                '250w' => 0,
                '350w' => 0,
                '500w' => 0,
            ),
        );

        if (empty($product_ids)) {
            $counts_cache[$cache_key] = $counts;
            return $counts;
        }

        foreach ($product_ids as $pid) {
            // 1. Giá
            $price = floatval(get_post_meta($pid, '_price', true));
            if ($price > 0) {
                if ($price < 10000000) {
                    $counts['price']['0-10000000']++;
                } elseif ($price >= 10000000 && $price <= 15000000) {
                    $counts['price']['10000000-15000000']++;
                } elseif ($price > 15000000 && $price <= 25000000) {
                    $counts['price']['15000000-25000000']++;
                } elseif ($price > 25000000) {
                    $counts['price']['25000000-100000000']++;
                }
            }

            // 2. Dòng xe terms
            $dx_terms = wp_get_object_terms($pid, 'product_dong_xe', array('fields' => 'slugs'));
            if (!empty($dx_terms) && !is_wp_error($dx_terms)) {
                foreach ($dx_terms as $s) {
                    $counts['dong_xe'][$s] = ($counts['dong_xe'][$s] ?? 0) + 1;
                }
            }

            // 3. Nhu cầu terms
            $nc_terms = wp_get_object_terms($pid, 'product_nhu_cau', array('fields' => 'slugs'));
            if (!empty($nc_terms) && !is_wp_error($nc_terms)) {
                foreach ($nc_terms as $s) {
                    $counts['nhu_cau'][$s] = ($counts['nhu_cau'][$s] ?? 0) + 1;
                }
            }

            // 4. Specs
            $specs = gobike_get_product_numerical_specs($pid);
            $r = $specs['range'];
            $w = $specs['weight'];
            $p = $specs['power'];

            if ($r > 0) {
                if ($r < 50) $counts['quang_duong']['duoi-50km']++;
                if ($r >= 50 && $r <= 80) $counts['quang_duong']['50-80km']++;
                if ($r > 80 && $r <= 120) $counts['quang_duong']['80-120km']++;
                if ($r > 120) $counts['quang_duong']['tren-120km']++;
            }

            if ($w > 0) {
                if ($w < 18) $counts['trong_luong']['duoi-18kg']++;
                if ($w >= 18 && $w <= 22) $counts['trong_luong']['18-22kg']++;
                if ($w > 22) $counts['trong_luong']['tren-22kg']++;
            }

            if ($p > 0) {
                if ($p <= 250) $counts['cong_suat']['250w']++;
                if ($p > 250 && $p <= 450) $counts['cong_suat']['350w']++;
                if ($p >= 450) $counts['cong_suat']['500w']++;
            }
        }

        $counts_cache[$cache_key] = $counts;
        return $counts;
    }
}

/**
 * Tự động phân loại dòng xe & nhu cầu khi lưu/cập nhật sản phẩm
 */
add_action('save_post_product', 'gobike_auto_tag_single_product_on_save', 20, 2);
function gobike_auto_tag_single_product_on_save($post_id, $post)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id) || !$post || $post->post_status !== 'publish') return;

    $existing_dx = wp_get_object_terms($post_id, 'product_dong_xe', array('fields' => 'ids'));
    $existing_nc = wp_get_object_terms($post_id, 'product_nhu_cau', array('fields' => 'ids'));

    if (empty($existing_dx) || empty($existing_nc)) {
        $title = mb_strtolower($post->post_title);
        $dong_xe_terms = array();
        $nhu_cau_terms = array();

        if (str_contains($title, 'gập') || str_contains($title, 'gap') || str_contains($title, 'lite') || str_contains($title, 'air20') || str_contains($title, 'a20') || str_contains($title, 'k20')) {
            $dong_xe_terms[] = 'xe-gap-gon';
            $nhu_cau_terms[] = 'hoc-sinh-sinh-vien';
            $nhu_cau_terms[] = 'di-lam-di-hoc';
        } elseif (str_contains($title, 'địa hình') || str_contains($title, 'dia hinh') || str_contains($title, 'rx70') || str_contains($title, 'm16') || str_contains($title, 'c05') || str_contains($title, 'm1-pro') || str_contains($title, 'gt-2000') || str_contains($title, 'v8')) {
            $dong_xe_terms[] = 'xe-dia-hinh';
            $nhu_cau_terms[] = 'du-lich-da-ngoai';
        } elseif (str_contains($title, 'touring') || str_contains($title, 'a28') || str_contains($title, 'aq177') || str_contains($title, 'air28')) {
            $dong_xe_terms[] = 'xe-touring';
            $nhu_cau_terms[] = 'di-lam-di-hoc';
        } else {
            $dong_xe_terms[] = 'xe-do-thi';
            $nhu_cau_terms[] = 'di-lam-di-hoc';
        }

        if (str_contains($title, 'c1') || str_contains($title, 'city girl') || str_contains($title, 'air ultra') || str_contains($title, 'x5')) {
            $nhu_cau_terms[] = 'cho-nguoi-lon-tuoi';
        }

        if (empty($existing_dx) && !empty($dong_xe_terms)) {
            wp_set_object_terms($post_id, $dong_xe_terms, 'product_dong_xe', true);
        }
        if (empty($existing_nc) && !empty($nhu_cau_terms)) {
            wp_set_object_terms($post_id, $nhu_cau_terms, 'product_nhu_cau', true);
        }
    }
}

/**
 * XỬ LÝ TRUY VẤN LỌC SẢN PHẨM WOOCOMMERCE ĐỘNG
 */
add_action('woocommerce_product_query', 'gobike_handle_shop_filter_query', 99);
add_action('pre_get_posts', 'gobike_handle_shop_filter_query_pre', 99);

function gobike_handle_shop_filter_query_pre($q)
{
    if (is_admin() || !$q->is_main_query()) {
        return;
    }
    if (is_shop() || is_product_taxonomy() || is_product_category()) {
        gobike_handle_shop_filter_query($q);
    }
}

function gobike_handle_shop_filter_query($q)
{
    if (is_admin()) {
        return;
    }
    if ($q->get('_gobike_filter_applied')) {
        return;
    }
    $q->set('_gobike_filter_applied', true);

    $tax_query = (array) $q->get('tax_query');

    // 1. Lọc theo Dòng xe
    $dong_xe = array();
    if (!empty($_GET['filter_dong_xe'])) {
        $dong_xe = is_array($_GET['filter_dong_xe']) ? array_map('sanitize_title', $_GET['filter_dong_xe']) : explode(',', sanitize_text_field($_GET['filter_dong_xe']));
    } elseif (!empty($_GET['filter_dong-xe'])) {
        $dong_xe = explode(',', sanitize_text_field($_GET['filter_dong-xe']));
    }
    $dong_xe = array_filter(array_map('trim', $dong_xe));
    if (!empty($dong_xe)) {
        $tax_query[] = array(
            'taxonomy' => 'product_dong_xe',
            'field'    => 'slug',
            'terms'    => $dong_xe,
            'operator' => 'IN',
        );
    }

    // 2. Lọc theo Nhu cầu sử dụng
    $nhu_cau = array();
    if (!empty($_GET['filter_nhu_cau'])) {
        $nhu_cau = is_array($_GET['filter_nhu_cau']) ? array_map('sanitize_title', $_GET['filter_nhu_cau']) : explode(',', sanitize_text_field($_GET['filter_nhu_cau']));
    } elseif (!empty($_GET['filter_nhu-cau'])) {
        $nhu_cau = explode(',', sanitize_text_field($_GET['filter_nhu-cau']));
    }
    $nhu_cau = array_filter(array_map('trim', $nhu_cau));
    if (!empty($nhu_cau)) {
        $tax_query[] = array(
            'taxonomy' => 'product_nhu_cau',
            'field'    => 'slug',
            'terms'    => $nhu_cau,
            'operator' => 'IN',
        );
    }

    if (count($tax_query) > 1 && !isset($tax_query['relation'])) {
        $tax_query['relation'] = 'AND';
    }
    $q->set('tax_query', $tax_query);

    // 3. Lọc theo Thông số (Quãng đường, Trọng lượng, Công suất)
    $filter_range  = !empty($_GET['filter_quang_duong']) ? (array) $_GET['filter_quang_duong'] : array();
    $filter_weight = !empty($_GET['filter_trong_luong']) ? (array) $_GET['filter_trong_luong'] : array();
    $filter_power  = !empty($_GET['filter_cong_suat']) ? (array) $_GET['filter_cong_suat'] : array();

    $filter_range  = array_filter(array_map('sanitize_title', $filter_range));
    $filter_weight = array_filter(array_map('sanitize_title', $filter_weight));
    $filter_power  = array_filter(array_map('sanitize_title', $filter_power));

    if (!empty($filter_range) || !empty($filter_weight) || !empty($filter_power)) {
        $current_cat_term_id = 0;
        if (is_product_category()) {
            $cat_obj = get_queried_object();
            if ($cat_obj && !empty($cat_obj->term_id)) {
                $current_cat_term_id = $cat_obj->term_id;
            }
        }
        $spec_matching_ids = gobike_get_matching_spec_product_ids($filter_range, $filter_weight, $filter_power, $current_cat_term_id);

        $existing_post__in = $q->get('post__in');
        if (!empty($existing_post__in) && is_array($existing_post__in)) {
            $combined_ids = array_intersect($existing_post__in, $spec_matching_ids);
            $q->set('post__in', !empty($combined_ids) ? $combined_ids : array(0));
        } else {
            $q->set('post__in', !empty($spec_matching_ids) ? $spec_matching_ids : array(0));
        }
    }
}

/**
 * Helper: Tạo link xóa một điều kiện lọc khỏi URL
 */
if (!function_exists('gobike_get_filter_remove_url')) {
    function gobike_get_filter_remove_url($param_key, $slug_to_remove = '')
    {
        $current_url = strtok($_SERVER["REQUEST_URI"], '?');
        $params = $_GET;

        if (empty($slug_to_remove)) {
            unset($params[$param_key]);
            unset($params[$param_key . '[]']);
        } else {
            if (isset($params[$param_key])) {
                if (is_array($params[$param_key])) {
                    $params[$param_key] = array_values(array_diff($params[$param_key], array($slug_to_remove)));
                    if (empty($params[$param_key])) {
                        unset($params[$param_key]);
                    }
                } else {
                    $items = explode(',', $params[$param_key]);
                    $items = array_values(array_diff($items, array($slug_to_remove)));
                    if (empty($items)) {
                        unset($params[$param_key]);
                    } else {
                        $params[$param_key] = implode(',', $items);
                    }
                }
            }
        }

        if (empty($params)) {
            return $current_url;
        }
        return $current_url . '?' . http_build_query($params);
    }
}

/**
 * 1. Render Cột Bộ lọc Sidebar bên trái chuẩn Ảnh 4 (DỮ LIỆU ĐỘNG 100%)
 */
function gobike_render_shop_sidebar_filter()
{
    // Xác định các filter đang active từ URL
    $min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
    $max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;
    $current_cat = is_product_category() ? get_queried_object() : null;
    $brand_name = $current_cat ? $current_cat->name : '';
    $current_cat_id = $current_cat && isset($current_cat->term_id) ? $current_cat->term_id : 0;

    // Tính toán số lượng sản phẩm thực tế theo ngữ cảnh danh mục
    $dynamic_counts = gobike_get_sidebar_filter_counts($current_cat_id);

    // Dòng xe selected
    $dong_xe_selected = array();
    if (!empty($_GET['filter_dong_xe'])) {
        $dong_xe_selected = is_array($_GET['filter_dong_xe']) ? array_map('sanitize_title', $_GET['filter_dong_xe']) : explode(',', sanitize_text_field($_GET['filter_dong_xe']));
    } elseif (!empty($_GET['filter_dong-xe'])) {
        $dong_xe_selected = explode(',', sanitize_text_field($_GET['filter_dong-xe']));
    }

    // Nhu cầu selected
    $nhu_cau_selected = array();
    if (!empty($_GET['filter_nhu_cau'])) {
        $nhu_cau_selected = is_array($_GET['filter_nhu_cau']) ? array_map('sanitize_title', $_GET['filter_nhu_cau']) : explode(',', sanitize_text_field($_GET['filter_nhu_cau']));
    } elseif (!empty($_GET['filter_nhu-cau'])) {
        $nhu_cau_selected = explode(',', sanitize_text_field($_GET['filter_nhu-cau']));
    }

    // Lấy danh sách Terms động từ CSDL cho Dòng xe
    $dong_xe_terms = get_terms(array(
        'taxonomy'   => 'product_dong_xe',
        'hide_empty' => false,
    ));
    $dong_xe_options = array();
    if (!empty($dong_xe_terms) && !is_wp_error($dong_xe_terms)) {
        foreach ($dong_xe_terms as $t) {
            $real_c = $dynamic_counts['dong_xe'][$t->slug] ?? $t->count;
            $dong_xe_options[$t->slug] = array(
                'name'  => $t->name,
                'count' => $real_c,
            );
        }
    }
    if (empty($dong_xe_options)) {
        $dong_xe_options = array(
            'xe-do-thi'   => array('name' => 'Xe đô thị', 'count' => $dynamic_counts['dong_xe']['xe-do-thi'] ?? 0),
            'xe-gap-gon'  => array('name' => 'Xe gấp gọn', 'count' => $dynamic_counts['dong_xe']['xe-gap-gon'] ?? 0),
            'xe-dia-hinh' => array('name' => 'Xe địa hình', 'count' => $dynamic_counts['dong_xe']['xe-dia-hinh'] ?? 0),
            'xe-touring'  => array('name' => 'Xe touring', 'count' => $dynamic_counts['dong_xe']['xe-touring'] ?? 0),
        );
    }

    // Nhu cầu sử dụng
    $nhu_cau_terms = get_terms(array(
        'taxonomy'   => 'product_nhu_cau',
        'hide_empty' => false,
    ));
    $nhu_cau_options = array();
    if (!empty($nhu_cau_terms) && !is_wp_error($nhu_cau_terms)) {
        foreach ($nhu_cau_terms as $t) {
            $real_c = $dynamic_counts['nhu_cau'][$t->slug] ?? $t->count;
            $nhu_cau_options[$t->slug] = array(
                'name'  => $t->name,
                'count' => $real_c,
            );
        }
    }
    if (empty($nhu_cau_options)) {
        $nhu_cau_options = array(
            'di-lam-di-hoc'      => array('name' => 'Đi làm - đi học', 'count' => $dynamic_counts['nhu_cau']['di-lam-di-hoc'] ?? 0),
            'hoc-sinh-sinh-vien' => array('name' => 'Học sinh - sinh viên', 'count' => $dynamic_counts['nhu_cau']['hoc-sinh-sinh-vien'] ?? 0),
            'du-lich-da-ngoai'   => array('name' => 'Du lịch - dã ngoại', 'count' => $dynamic_counts['nhu_cau']['du-lich-da-ngoai'] ?? 0),
            'cho-nguoi-lon-tuoi' => array('name' => 'Cho người lớn tuổi', 'count' => $dynamic_counts['nhu_cau']['cho-nguoi-lon-tuoi'] ?? 0),
        );
    }

    // Quãng đường di chuyển
    $quang_duong_options = array(
        'duoi-50km'  => array('name' => 'Dưới 50 km', 'count' => $dynamic_counts['quang_duong']['duoi-50km'] ?? 0),
        '50-80km'    => array('name' => '50 - 80 km', 'count' => $dynamic_counts['quang_duong']['50-80km'] ?? 0),
        '80-120km'   => array('name' => '80 - 120 km', 'count' => $dynamic_counts['quang_duong']['80-120km'] ?? 0),
        'tren-120km' => array('name' => 'Trên 120 km', 'count' => $dynamic_counts['quang_duong']['tren-120km'] ?? 0),
    );
    $quang_duong_selected = array();
    if (!empty($_GET['filter_quang_duong'])) {
        $quang_duong_selected = is_array($_GET['filter_quang_duong']) ? array_map('sanitize_title', $_GET['filter_quang_duong']) : explode(',', sanitize_text_field($_GET['filter_quang_duong']));
    }

    // Trọng lượng xe
    $trong_luong_options = array(
        'duoi-18kg' => array('name' => 'Dưới 18 kg', 'count' => $dynamic_counts['trong_luong']['duoi-18kg'] ?? 0),
        '18-22kg'   => array('name' => '18 - 22 kg', 'count' => $dynamic_counts['trong_luong']['18-22kg'] ?? 0),
        'tren-22kg' => array('name' => 'Trên 22 kg', 'count' => $dynamic_counts['trong_luong']['tren-22kg'] ?? 0),
    );
    $trong_luong_selected = array();
    if (!empty($_GET['filter_trong_luong'])) {
        $trong_luong_selected = is_array($_GET['filter_trong_luong']) ? array_map('sanitize_title', $_GET['filter_trong_luong']) : explode(',', sanitize_text_field($_GET['filter_trong_luong']));
    }

    // Công suất động cơ
    $cong_suat_options = array(
        '250w' => array('name' => '250 W', 'count' => $dynamic_counts['cong_suat']['250w'] ?? 0),
        '350w' => array('name' => '350 W', 'count' => $dynamic_counts['cong_suat']['350w'] ?? 0),
        '500w' => array('name' => '500 W', 'count' => $dynamic_counts['cong_suat']['500w'] ?? 0),
    );
    $cong_suat_selected = array();
    if (!empty($_GET['filter_cong_suat'])) {
        $cong_suat_selected = is_array($_GET['filter_cong_suat']) ? array_map('sanitize_title', $_GET['filter_cong_suat']) : explode(',', sanitize_text_field($_GET['filter_cong_suat']));
    }

    // Thu thập các active tags để hiển thị dải "Đang chọn"
    $active_tags = array();
    if ($current_cat) {
        $active_tags[] = array(
            'label'      => 'Hãng: ' . esc_html($brand_name),
            'remove_url' => get_permalink(wc_get_page_id('shop')),
        );
    }
    if ($max_price > 0 || $min_price > 0) {
        $price_label = '';
        if ($min_price <= 0 && $max_price <= 10000000) {
            $price_label = 'Dưới 10 triệu';
        } elseif ($min_price >= 10000000 && $max_price <= 15000000) {
            $price_label = '10 - 15 triệu';
        } elseif ($min_price >= 15000000 && $max_price <= 25000000) {
            $price_label = '15 - 25 triệu';
        } elseif ($min_price >= 25000000) {
            $price_label = 'Trên 25 triệu';
        } else {
            $price_label = number_format($min_price, 0, ',', '.') . 'đ - ' . number_format($max_price, 0, ',', '.') . 'đ';
        }
        $active_tags[] = array(
            'label'      => 'Giá: ' . $price_label,
            'remove_url' => remove_query_arg(array('min_price', 'max_price')),
        );
    }

    // Tags Dòng xe
    if (!empty($dong_xe_selected)) {
        foreach ($dong_xe_selected as $s_slug) {
            $t_name = isset($dong_xe_options[$s_slug]) ? $dong_xe_options[$s_slug]['name'] : $s_slug;
            $active_tags[] = array(
                'label'      => 'Dòng xe: ' . esc_html($t_name),
                'remove_url' => gobike_get_filter_remove_url('filter_dong_xe', $s_slug),
            );
        }
    }

    // Tags Nhu cầu
    if (!empty($nhu_cau_selected)) {
        foreach ($nhu_cau_selected as $s_slug) {
            $t_name = isset($nhu_cau_options[$s_slug]) ? $nhu_cau_options[$s_slug]['name'] : $s_slug;
            $active_tags[] = array(
                'label'      => 'Nhu cầu: ' . esc_html($t_name),
                'remove_url' => gobike_get_filter_remove_url('filter_nhu_cau', $s_slug),
            );
        }
    }

    // Tags Quãng đường
    if (!empty($quang_duong_selected)) {
        foreach ($quang_duong_selected as $s_slug) {
            $t_name = isset($quang_duong_options[$s_slug]) ? $quang_duong_options[$s_slug]['name'] : $s_slug;
            $active_tags[] = array(
                'label'      => 'Quãng đường: ' . esc_html($t_name),
                'remove_url' => gobike_get_filter_remove_url('filter_quang_duong', $s_slug),
            );
        }
    }

    // Tags Trọng lượng
    if (!empty($trong_luong_selected)) {
        foreach ($trong_luong_selected as $s_slug) {
            $t_name = isset($trong_luong_options[$s_slug]) ? $trong_luong_options[$s_slug]['name'] : $s_slug;
            $active_tags[] = array(
                'label'      => 'Trọng lượng: ' . esc_html($t_name),
                'remove_url' => gobike_get_filter_remove_url('filter_trong_luong', $s_slug),
            );
        }
    }

    // Tags Công suất
    if (!empty($cong_suat_selected)) {
        foreach ($cong_suat_selected as $s_slug) {
            $t_name = isset($cong_suat_options[$s_slug]) ? $cong_suat_options[$s_slug]['name'] : $s_slug;
            $active_tags[] = array(
                'label'      => 'Công suất: ' . esc_html($t_name),
                'remove_url' => gobike_get_filter_remove_url('filter_cong_suat', $s_slug),
            );
        }
    }

    global $wp_query;
    $total_count = isset($wp_query->found_posts) ? $wp_query->found_posts : 0;
    $reset_all_url = strtok($_SERVER["REQUEST_URI"], '?');

    // Đếm số lượng tiêu chí đang được chọn (Cách 2)
    $selected_criteria_count = 0;
    if ($min_price > 0 || $max_price > 0) {
        $selected_criteria_count++;
    }
    $selected_criteria_count += count($dong_xe_selected);
    $selected_criteria_count += count($nhu_cau_selected);
    $selected_criteria_count += count($quang_duong_selected);
    $selected_criteria_count += count($trong_luong_selected);
    $selected_criteria_count += count($cong_suat_selected);
    $apply_btn_text = $selected_criteria_count > 0 ? 'Áp dụng bộ lọc (' . $selected_criteria_count . ')' : 'Áp dụng bộ lọc';

    $is_nhu_cau_open    = !empty($nhu_cau_selected);
    $is_quang_duong_open = !empty($quang_duong_selected);
    $is_trong_luong_open = !empty($trong_luong_selected);
    $is_cong_suat_open   = !empty($cong_suat_selected);
    ?>

    <!-- LỚP OVERLAY BACKDROP MỜ CHO DRAWER MOBILE -->
    <div class="gobike-filter-drawer-overlay" id="gobikeFilterOverlay"></div>

    <aside class="gobike-sidebar-filter-wrapper" id="gobikeSidebarFilterWrapper">
        <!-- THANH KÉO (GRAB HANDLE) CHO GIAO DIỆN BOTTOM SHEET MOBILE (ẢNH 2) -->
        <div class="drawer-handle-bar"></div>

        <!-- HEADER BỘ LỌC SẢN PHẨM (ẢNH 2) -->
        <div class="gobike-filter-header">
            <div class="filter-title-wrap">
                <h3 class="filter-title">Bộ lọc sản phẩm</h3>
            </div>
            <a href="<?php echo esc_url($reset_all_url); ?>" class="filter-reset-link desktop-only">Xóa bộ lọc</a>
            <button type="button" class="drawer-close-btn mobile-only" id="gobikeCloseFilterDrawer" aria-label="Đóng bộ lọc">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
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

            <!-- NHÓM 1: GIÁ SẢN PHẨM (MẶC ĐỊNH MỞ - CHUẨN ẢNH 2) -->
            <div class="filter-group open">
                <div class="filter-group-header">
                    <span class="group-title">Giá sản phẩm</span>
                    <span class="toggle-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="18 15 12 9 6 15"></polyline>
                        </svg>
                    </span>
                </div>
                <div class="filter-group-content">
                    <label class="filter-checkbox-item">
                        <input type="checkbox" name="price_choice" value="0-10000000" <?php checked($min_price == 0 && $max_price == 10000000); ?> onchange="gobikeApplyPriceChoice(this, 0, 10000000)">
                        <span class="label-text">Dưới 10.000.000 đ <span class="count">(<?php echo intval($dynamic_counts['price']['0-10000000']); ?>)</span></span>
                    </label>
                    <label class="filter-checkbox-item">
                        <input type="checkbox" name="price_choice" value="10000000-15000000" <?php checked($min_price == 10000000 && $max_price == 15000000); ?> onchange="gobikeApplyPriceChoice(this, 10000000, 15000000)">
                        <span class="label-text">10.000.000 đ - 15.000.000 đ <span class="count">(<?php echo intval($dynamic_counts['price']['10000000-15000000']); ?>)</span></span>
                    </label>
                    <label class="filter-checkbox-item">
                        <input type="checkbox" name="price_choice" value="15000000-25000000" <?php checked($min_price == 15000000 && $max_price == 25000000); ?> onchange="gobikeApplyPriceChoice(this, 15000000, 25000000)">
                        <span class="label-text">15.000.000 đ - 25.000.000 đ <span class="count">(<?php echo intval($dynamic_counts['price']['15000000-25000000']); ?>)</span></span>
                    </label>
                    <label class="filter-checkbox-item">
                        <input type="checkbox" name="price_choice" value="25000000-100000000" <?php checked($min_price == 25000000 && $max_price == 100000000); ?> onchange="gobikeApplyPriceChoice(this, 25000000, 100000000)">
                        <span class="label-text">Trên 25.000.000 đ <span class="count">(<?php echo intval($dynamic_counts['price']['25000000-100000000']); ?>)</span></span>
                    </label>
                    <!-- Input ẩn min_price và max_price gửi lên URL -->
                    <input type="hidden" name="min_price" id="filter_min_price" value="<?php echo $min_price > 0 ? esc_attr($min_price) : ''; ?>" />
                    <input type="hidden" name="max_price" id="filter_max_price" value="<?php echo $max_price > 0 ? esc_attr($max_price) : ''; ?>" />
                </div>
            </div>

            <!-- NHÓM 2: DÒNG XE (MẶC ĐỊNH MỞ - CHUẨN ẢNH 2) -->
            <div class="filter-group open">
                <div class="filter-group-header">
                    <span class="group-title">Dòng xe</span>
                    <span class="toggle-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="18 15 12 9 6 15"></polyline>
                        </svg>
                    </span>
                </div>
                <div class="filter-group-content">
                    <?php
                    foreach ($dong_xe_options as $slug => $data):
                        $checked = in_array($slug, $dong_xe_selected);
                        $c = $dynamic_counts['dong_xe'][$slug] ?? $data['count'];
                    ?>
                        <label class="filter-checkbox-item">
                            <input type="checkbox" name="filter_dong_xe[]" value="<?php echo esc_attr($slug); ?>" <?php checked($checked); ?>>
                            <span class="label-text"><?php echo esc_html($data['name']); ?> <span class="count">(<?php echo intval($c); ?>)</span></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- NHÓM 3: NHU CẦU SỬ DỤNG (CHUẨN ẢNH 2) -->
            <div class="filter-group <?php echo $is_nhu_cau_open ? 'open' : ''; ?>">
                <div class="filter-group-header">
                    <span class="group-title">Nhu cầu sử dụng</span>
                    <span class="toggle-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="18 15 12 9 6 15"></polyline>
                        </svg>
                    </span>
                </div>
                <div class="filter-group-content" <?php echo $is_nhu_cau_open ? '' : 'style="display: none;"'; ?>>
                    <?php
                    foreach ($nhu_cau_options as $slug => $data):
                        $checked = in_array($slug, $nhu_cau_selected);
                        $c = $dynamic_counts['nhu_cau'][$slug] ?? $data['count'];
                    ?>
                        <label class="filter-checkbox-item">
                            <input type="checkbox" name="filter_nhu_cau[]" value="<?php echo esc_attr($slug); ?>" <?php checked($checked); ?>>
                            <span class="label-text"><?php echo esc_html($data['name']); ?> <span class="count">(<?php echo intval($c); ?>)</span></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- NHÓM 4: QUÃNG ĐƯỜNG DI CHUYỂN (CHUẨN ẢNH 2) -->
            <div class="filter-group <?php echo $is_quang_duong_open ? 'open' : ''; ?>">
                <div class="filter-group-header">
                    <span class="group-title">Quãng đường di chuyển</span>
                    <span class="toggle-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="18 15 12 9 6 15"></polyline>
                        </svg>
                    </span>
                </div>
                <div class="filter-group-content" <?php echo $is_quang_duong_open ? '' : 'style="display: none;"'; ?>>
                    <?php
                    foreach ($quang_duong_options as $slug => $data):
                        $checked = in_array($slug, $quang_duong_selected);
                        $c = $dynamic_counts['quang_duong'][$slug] ?? $data['count'];
                    ?>
                        <label class="filter-checkbox-item">
                            <input type="checkbox" name="filter_quang_duong[]" value="<?php echo esc_attr($slug); ?>" <?php checked($checked); ?>>
                            <span class="label-text"><?php echo esc_html($data['name']); ?> <span class="count">(<?php echo intval($c); ?>)</span></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- NHÓM 5: TRỌNG LƯỢNG XE (CHUẨN ẢNH 2) -->
            <div class="filter-group <?php echo $is_trong_luong_open ? 'open' : ''; ?>">
                <div class="filter-group-header">
                    <span class="group-title">Trọng lượng xe</span>
                    <span class="toggle-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="18 15 12 9 6 15"></polyline>
                        </svg>
                    </span>
                </div>
                <div class="filter-group-content" <?php echo $is_trong_luong_open ? '' : 'style="display: none;"'; ?>>
                    <?php
                    foreach ($trong_luong_options as $slug => $data):
                        $checked = in_array($slug, $trong_luong_selected);
                        $c = $dynamic_counts['trong_luong'][$slug] ?? $data['count'];
                    ?>
                        <label class="filter-checkbox-item">
                            <input type="checkbox" name="filter_trong_luong[]" value="<?php echo esc_attr($slug); ?>" <?php checked($checked); ?>>
                            <span class="label-text"><?php echo esc_html($data['name']); ?> <span class="count">(<?php echo intval($c); ?>)</span></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- NHÓM 6: CÔNG SUẤT ĐỘNG CƠ (CHUẨN ẢNH 2) -->
            <div class="filter-group <?php echo $is_cong_suat_open ? 'open' : ''; ?>">
                <div class="filter-group-header">
                    <span class="group-title">Công suất động cơ</span>
                    <span class="toggle-icon">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="18 15 12 9 6 15"></polyline>
                        </svg>
                    </span>
                </div>
                <div class="filter-group-content" <?php echo $is_cong_suat_open ? '' : 'style="display: none;"'; ?>>
                    <?php
                    foreach ($cong_suat_options as $slug => $data):
                        $checked = in_array($slug, $cong_suat_selected);
                        $c = $dynamic_counts['cong_suat'][$slug] ?? $data['count'];
                    ?>
                        <label class="filter-checkbox-item">
                            <input type="checkbox" name="filter_cong_suat[]" value="<?php echo esc_attr($slug); ?>" <?php checked($checked); ?>>
                            <span class="label-text"><?php echo esc_html($data['name']); ?> <span class="count">(<?php echo intval($c); ?>)</span></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- THANH THAO TÁC STICKY ĐÁY (CHUẨN ẢNH 2) -->
            <div class="filter-action-sticky-footer">
                <a href="<?php echo esc_url($reset_all_url); ?>" class="filter-footer-btn-reset">Xóa lọc</a>
                <button type="submit" class="filter-footer-btn-apply"><?php echo esc_html($apply_btn_text); ?></button>
            </div>
        </form>

        <!-- BOX HỖ TRỢ TƯ VẤN HOTLINE (ẢNH 2 - CHỈ HIỆN TRÊN DESKTOP) -->
        <div class="gobike-support-hotline-box desktop-only">
            <div class="support-top-row">
                <div class="support-icon">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 11a9 9 0 0 1 18 0v6a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3"></path>
                        <path d="M3 17a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3v5z"></path>
                        <path d="M21 16v2a4 4 0 0 1-4 4h-3"></path>
                    </svg>
                </div>
                <div class="support-text-col">
                    <h4 class="support-title">Cần tư vấn chọn xe?</h4>
                    <p class="support-desc">Đội ngũ GoBike luôn sẵn sàng hỗ trợ bạn</p>
                </div>
            </div>
            <a href="tel:0944988699" class="support-phone-btn">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M6.62 10.79a15.053 15.053 0 006.59 6.59l2.2-2.2a1 1 0 011.11-.21c1.12.45 2.33.69 3.48.69a1 1 0 011 1v3.5a1 1 0 01-1 1A17.91 17.91 0 012 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.15.24 2.36.69 3.48a1 1 0 01-.21 1.11l-2.2 2.2z"/>
                </svg>
                0944 988 699
            </a>
        </div>
    </aside>

    <script type="text/javascript">
    function gobikeUpdateApplyBtnCount() {
        var count = 0;
        // Kiểm tra xem có chọn khoảng giá không
        if (document.querySelectorAll('input[name="price_choice"]:checked').length > 0) {
            count++;
        }
        // Đếm các checkbox bộ lọc khác đang được tick
        var otherChecks = document.querySelectorAll('#gobike-filter-form input[type="checkbox"]:not([name="price_choice"]):checked');
        count += otherChecks.length;

        var btn = document.querySelector('.filter-footer-btn-apply');
        if (btn) {
            btn.textContent = count > 0 ? 'Áp dụng bộ lọc (' + count + ')' : 'Áp dụng bộ lọc';
        }
    }

    function gobikeApplyPriceChoice(input, min, max) {
        if (input.checked) {
            // Uncheck other price choices
            document.querySelectorAll('input[name="price_choice"]').forEach(function(el) {
                if (el !== input) el.checked = false;
            });
            document.getElementById('filter_min_price').value = min;
            document.getElementById('filter_max_price').value = max;
        } else {
            document.getElementById('filter_min_price').value = '';
            document.getElementById('filter_max_price').value = '';
        }
        gobikeUpdateApplyBtnCount();
    }

    function gobikeApplyMobileSort(val) {
        var url = new URL(window.location.href);
        url.searchParams.set('orderby', val);
        window.location.href = url.toString();
    }

    jQuery(document).ready(function($) {
        // Cập nhật số đếm tiêu chí khi người dùng tick / bỏ tick bất kỳ checkbox nào
        $('#gobike-filter-form').on('change', 'input[type="checkbox"]', function() {
            gobikeUpdateApplyBtnCount();
        });

        // Mở Drawer Bộ lọc trên Tablet / Mobile
        $('#gobikeMobileFilterBtn').on('click', function(e) {
            e.preventDefault();
            $('body').addClass('gobike-filter-open');
        });

        // Đóng Drawer Bộ lọc
        $('#gobikeCloseFilterDrawer, #gobikeFilterOverlay').on('click', function(e) {
            e.preventDefault();
            $('body').removeClass('gobike-filter-open');
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('body').removeClass('gobike-filter-open');
            }
        });

        // Toggle mở/gập các nhóm Accordion
        $('.gobike-sidebar-filter-wrapper .filter-group-header').on('click', function() {
            var group = $(this).closest('.filter-group');
            group.toggleClass('open');
            group.find('.filter-group-content').slideToggle(200);
        });

        // Loại bỏ các input rỗng trước khi submit form để URL sạch
        $('#gobike-filter-form').on('submit', function() {
            $(this).find('input, select').each(function() {
                if ($(this).attr('name') === 'price_choice') {
                    $(this).prop('disabled', true); // Chỉ gửi min_price & max_price
                } else if (!$(this).val() || $(this).val() === '') {
                    $(this).prop('disabled', true);
                }
            });
        });
    });
    </script>
    <?php
}

/**
 * 2. Render Dải 4 Cam Kết Vàng chuẩn Ảnh 4
 * Đọc từ ACF shop_trust_badges (trang Shop hoặc Theme Settings), có fallback 4 cam kết mặc định.
 */
function gobike_render_shop_trust_badges()
{
    $shop_page_id = get_option('woocommerce_shop_page_id');

    // Ưu tiên đọc từ trang Shop trước, sau đó fallback sang Theme Settings
    $badges = null;
    if (function_exists('get_field')) {
        if (!empty($shop_page_id)) {
            $badges = get_field('shop_trust_badges', $shop_page_id);
        }
        if (empty($badges)) {
            $badges = get_field('shop_trust_badges', 'option');
        }
    }

    if (empty($badges) || !is_array($badges)) {
        return;
    }

    $default_svgs = array(
        '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg>',
        '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>',
        '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="5" x2="5" y2="19"></line><circle cx="6.5" cy="6.5" r="2.5"></circle><circle cx="17.5" cy="17.5" r="2.5"></circle></svg>',
        '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>',
    );

    $items_to_render = array();
    foreach ($badges as $idx => $b) {
        $t = trim($b['title'] ?? '');
        $d = trim($b['desc'] ?? '');
        $img = $b['icon'] ?? '';
        $link = trim($b['link'] ?? '');
        if (!empty($t) || !empty($d) || !empty($img)) {
            $icon_url = '';
            if ($img) {
                $icon_url = is_array($img) ? ($img['url'] ?? '') : (is_numeric($img) ? wp_get_attachment_image_url($img, 'thumbnail') : $img);
            }
            $fallback_svg = $default_svgs[$idx % count($default_svgs)];
            $items_to_render[] = array(
                'icon_url' => $icon_url,
                'svg'      => $fallback_svg,
                'title'    => $t,
                'desc'     => $d,
                'link'     => $link,
            );
        }
    }

    if (empty($items_to_render)) {
        return;
    }
    ?>
    <div class="gobike-shop-trust-badges-bar">
        <?php foreach ($items_to_render as $item): ?>
            <?php
            $has_link = !empty($item['link']);
            $tag = $has_link ? 'a' : 'div';
            $href_attr = $has_link ? ' href="' . esc_url($item['link']) . '"' : '';
            ?>
            <<?php echo $tag; ?> class="trust-item"<?php echo $href_attr; ?>>
                <div class="trust-icon">
                    <?php if (!empty($item['icon_url'])): ?>
                        <img src="<?php echo esc_url($item['icon_url']); ?>" alt="<?php echo esc_attr($item['title']); ?>" style="width:32px;height:32px;object-fit:contain;" loading="lazy">
                    <?php else: ?>
                        <?php echo $item['svg']; ?>
                    <?php endif; ?>
                </div>
                <div class="trust-text">
                    <strong><?php echo esc_html($item['title']); ?></strong>
                    <span><?php echo esc_html($item['desc']); ?></span>
                </div>
            </<?php echo $tag; ?>>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * 3. Render Hero Banner Thương hiệu / Danh mục
 * Đọc từ ACF shop_hero_banner_image (trang Shop hoặc Theme Settings), có fallback và hỗ trợ danh mục.
 */
function gobike_render_shop_brand_banner()
{
    $shop_page_id = get_option('woocommerce_shop_page_id');

    // 1. Đọc dữ liệu từ ACF
    $banner_img  = null;
    $banner_link = '';
    $banner_type = 'graphic';
    $title       = 'XE ĐẠP TRỢ LỰC ĐIỆN';
    $desc        = 'Thiết kế thông minh • Gọn nhẹ • Đồng hành mọi hành trình';
    $intro       = 'GoBike tự hào phân phối các dòng xe đạp trợ lực điện chính hãng hàng đầu Việt Nam.';
    $hotline     = '0944 988 699';
    $btn_text    = 'Xem tất cả sản phẩm ➔';
    $btn_link    = '#products-grid';

    if (function_exists('get_field')) {
        $source_id = !empty($shop_page_id) ? $shop_page_id : 'option';
        $banner_img = get_field('shop_hero_banner_image', $source_id);
        if (!$banner_img) {
            $banner_img = get_field('shop_hero_banner_image', 'option');
        }

        $banner_link = get_field('shop_hero_banner_link', $source_id) ?: get_field('shop_hero_banner_link', 'option');
        $banner_type = get_field('shop_hero_banner_type', $source_id) ?: get_field('shop_hero_banner_type', 'option') ?: 'graphic';
        $title       = get_field('shop_hero_banner_title', $source_id) ?: get_field('shop_hero_banner_title', 'option') ?: $title;
        $desc        = get_field('shop_hero_banner_slogan', $source_id) ?: get_field('shop_hero_banner_slogan', 'option') ?: $desc;
        $hotline     = get_field('shop_hero_banner_hotline', $source_id) ?: get_field('shop_hero_banner_hotline', 'option') ?: $hotline;
    }

    $default_banner = get_stylesheet_directory_uri() . '/assets/images/banner-san-pham-ado.webp';
    $image_url = '';
    if ($banner_img) {
        $image_url = is_array($banner_img) ? ($banner_img['url'] ?? '') : (is_numeric($banner_img) ? wp_get_attachment_image_url($banner_img, 'full') : $banner_img);
    }
    if (empty($image_url)) {
        $image_url = $default_banner;
    }

    $has_custom_cat_img = false;
    if (is_product_category()) {
        $cat = get_queried_object();
        if ($cat) {
            $title = 'XE ĐẠP TRỢ LỰC ĐIỆN ' . mb_strtoupper($cat->name);
            if (!empty($cat->description)) {
                $intro = wp_strip_all_tags($cat->description);
            }
            $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
            if ($thumbnail_id) {
                $cat_img = wp_get_attachment_url($thumbnail_id);
                if ($cat_img) {
                    $image_url = $cat_img;
                    $has_custom_cat_img = true;
                }
            }
        }
    }

    $hotline_clean = preg_replace('/[^0-9]/', '', $hotline);
    $is_custom_text = ($banner_type === 'custom_text' || $has_custom_cat_img);
    ?>
    <div class="gobike-shop-hero-banner <?php echo $is_custom_text ? 'has-custom-img' : 'is-graphic-banner'; ?>" style="background-image: url('<?php echo esc_url($image_url); ?>'); position: relative;">
        <?php if (!empty($banner_link) && !$is_custom_text) : ?>
            <a href="<?php echo esc_url($banner_link); ?>" class="hero-banner-full-link" style="position:absolute;inset:0;z-index:3;" title="<?php echo esc_attr($title); ?>"></a>
        <?php endif; ?>

        <?php if ($is_custom_text) : ?>
            <div class="hero-banner-overlay"></div>
            <div class="hero-banner-inner container">
                <div class="row align-middle">
                    <div class="col large-7 medium-8 small-12">
                        <div class="col-inner hero-text-col">
                            <h1 class="hero-title"><?php echo esc_html($title); ?></h1>
                            <p class="hero-slogan"><?php echo esc_html($desc); ?></p>
                            <p class="hero-intro"><?php echo esc_html($intro); ?></p>
                            <div class="hero-buttons">
                                <a href="<?php echo esc_url($btn_link); ?>" class="hero-btn-primary"><?php echo esc_html($btn_text); ?></a>
                                <?php if (!empty($hotline)): ?>
                                    <a href="tel:<?php echo esc_attr($hotline_clean); ?>" class="hero-btn-outline">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                        </svg>
                                        Nhận tư vấn nhanh (<?php echo esc_html($hotline); ?>)
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <!-- Hotspot interactive links for pre-rendered graphic buttons -->
            <a href="<?php echo esc_url($btn_link); ?>" class="hero-hotspot-btn btn-view-products" title="Xem tất cả sản phẩm" aria-label="Xem tất cả sản phẩm"></a>
            <?php if (!empty($hotline)): ?>
                <a href="tel:<?php echo esc_attr($hotline_clean); ?>" class="hero-hotspot-btn btn-quick-consult" title="Nhận tư vấn nhanh" aria-label="Nhận tư vấn nhanh"></a>
            <?php endif; ?>
            <!-- Accessible SEO heading for search engines -->
            <div class="screen-reader-text">
                <h1 class="hero-title"><?php echo esc_html($title); ?></h1>
                <p class="hero-slogan"><?php echo esc_html($desc); ?></p>
                <p class="hero-intro"><?php echo esc_html($intro); ?></p>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * 4. Render Khối "Vì sao chọn GoBike" (ACF - Không dữ liệu mẫu)
 */
function gobike_render_shop_why_choose()
{
    $shop_page_id = get_option('woocommerce_shop_page_id');
    $current_term = get_queried_object();
    $brand_or_cat_name = '';
    if ($current_term && !is_wp_error($current_term) && isset($current_term->name)) {
        $brand_or_cat_name = $current_term->name;
    }

    // 1. Đọc dữ liệu từ ACF (Ưu tiên Category -> Shop page -> Theme Settings)
    $items = null;
    $title = '';

    if (function_exists('get_field')) {
        if ($current_term && !empty($current_term->term_id)) {
            $items = get_field('shop_why_choose_items', $current_term);
            $title = get_field('shop_why_choose_title', $current_term);
        }
        if (empty($items) && !empty($shop_page_id)) {
            $items = get_field('shop_why_choose_items', $shop_page_id);
            if (empty($title)) {
                $title = get_field('shop_why_choose_title', $shop_page_id);
            }
        }
        if (empty($items)) {
            $items = get_field('shop_why_choose_items', 'option');
            if (empty($title)) {
                $title = get_field('shop_why_choose_title', 'option');
            }
        }
    }

    // KHÔNG DỮ LIỆU MẪU: Nếu chưa nhập ACF -> Ẩn hoàn toàn khối
    if (empty($items) || !is_array($items)) {
        return;
    }

    // Lọc danh sách item hợp lệ
    $valid_items = array();
    foreach ($items as $item) {
        $t    = trim($item['title'] ?? '');
        $d    = trim($item['desc'] ?? '');
        $icon = $item['icon'] ?? '';
        $link = trim($item['link'] ?? '');
        if (!empty($t) || !empty($d) || !empty($icon)) {
            $icon_url = '';
            if ($icon) {
                $icon_url = is_array($icon) ? ($icon['url'] ?? '') : (is_numeric($icon) ? wp_get_attachment_image_url($icon, 'medium') : $icon);
            }
            $valid_items[] = array(
                'title'    => $t,
                'desc'     => $d,
                'icon_url' => $icon_url,
                'link'     => $link,
            );
        }
    }

    if (empty($valid_items)) {
        return;
    }

    // Xử lý tiêu đề khối
    if (empty($title)) {
        $title = !empty($brand_or_cat_name) ? 'Vì sao chọn GoBike khi mua xe ' . esc_html($brand_or_cat_name) . '?' : 'Vì sao chọn GoBike khi mua xe đạp trợ lực điện?';
    } else {
        $brand_replacement = !empty($brand_or_cat_name) ? esc_html($brand_or_cat_name) : 'xe đạp trợ lực điện';
        $title = str_replace(array('{brand}', '{name}', '{category}'), $brand_replacement, $title);
    }

    // Bộ SVG mặc định chuẩn thiết kế (Shield, Tư vấn, Lái thử, Bảo trì)
    $fallback_svgs = array(
        '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg>',
        '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
        '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18.5" cy="17.5" r="3.5"></circle><circle cx="5.5" cy="17.5" r="3.5"></circle><circle cx="15" cy="5" r="1"></circle><path d="M12 17.5V14l-3-3 4-3 2 3h2"></path></svg>',
        '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
    );
    ?>
    <!-- KHỐI VÌ SAO CHỌN GOBIKE (ACF) -->
    <div class="gobike-why-choose-section">
        <h3 class="why-title"><?php echo esc_html($title); ?></h3>
        <div class="why-grid">
            <?php foreach ($valid_items as $idx => $item): ?>
                <?php
                $has_link = !empty($item['link']);
                $tag = $has_link ? 'a' : 'div';
                $href_attr = $has_link ? ' href="' . esc_url($item['link']) . '"' : '';
                $svg_code = $fallback_svgs[$idx % count($fallback_svgs)];
                ?>
                <<?php echo $tag; ?> class="why-item"<?php echo $href_attr; ?>>
                    <div class="why-icon-wrap">
                        <?php if (!empty($item['icon_url'])): ?>
                            <img src="<?php echo esc_url($item['icon_url']); ?>" alt="<?php echo esc_attr($item['title']); ?>" style="width:28px;height:28px;object-fit:contain;" loading="lazy" />
                        <?php else: ?>
                            <?php echo $svg_code; ?>
                        <?php endif; ?>
                    </div>
                    <div class="why-info">
                        <?php if (!empty($item['title'])): ?>
                            <h5><?php echo esc_html($item['title']); ?></h5>
                        <?php endif; ?>
                        <?php if (!empty($item['desc'])): ?>
                            <p><?php echo esc_html($item['desc']); ?></p>
                        <?php endif; ?>
                    </div>
                </<?php echo $tag; ?>>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/**
 * 5. Render Khối "Hệ thống Showroom" (ACF - Không dữ liệu mẫu)
 */
function gobike_render_shop_showrooms()
{
    $shop_page_id = get_option('woocommerce_shop_page_id');
    $current_term = get_queried_object();
    $brand_or_cat_name = '';
    if ($current_term && !is_wp_error($current_term) && isset($current_term->name)) {
        $brand_or_cat_name = $current_term->name;
    }

    // 1. Đọc dữ liệu từ ACF
    $showrooms     = null;
    $title         = '';
    $subtitle      = '';
    $hotline_label = '';
    $hotline       = '';
    $hotline_time  = '';
    $btn_text      = '';
    $btn_link      = '';

    if (function_exists('get_field')) {
        if ($current_term && !empty($current_term->term_id)) {
            $showrooms = get_field('shop_showroom_list', $current_term);
            $title     = get_field('shop_showroom_title', $current_term);
            $subtitle  = get_field('shop_showroom_subtitle', $current_term);
        }
        if (empty($showrooms) && !empty($shop_page_id)) {
            $showrooms     = get_field('shop_showroom_list', $shop_page_id);
            $title         = get_field('shop_showroom_title', $shop_page_id);
            $subtitle      = get_field('shop_showroom_subtitle', $shop_page_id);
            $hotline_label = get_field('shop_showroom_hotline_label', $shop_page_id);
            $hotline       = get_field('shop_showroom_hotline', $shop_page_id);
            $hotline_time  = get_field('shop_showroom_hotline_time', $shop_page_id);
            $btn_text      = get_field('shop_showroom_btn_text', $shop_page_id);
            $btn_link      = get_field('shop_showroom_btn_link', $shop_page_id);
        }
        if (empty($showrooms)) {
            $showrooms     = get_field('shop_showroom_list', 'option');
            if (empty($title))         $title         = get_field('shop_showroom_title', 'option');
            if (empty($subtitle))      $subtitle      = get_field('shop_showroom_subtitle', 'option');
            if (empty($hotline_label)) $hotline_label = get_field('shop_showroom_hotline_label', 'option');
            if (empty($hotline))       $hotline       = get_field('shop_showroom_hotline', 'option');
            if (empty($hotline_time))  $hotline_time  = get_field('shop_showroom_hotline_time', 'option');
            if (empty($btn_text))      $btn_text      = get_field('shop_showroom_btn_text', 'option');
            if (empty($btn_link))      $btn_link      = get_field('shop_showroom_btn_link', 'option');
        }
    }

    // KHÔNG DỮ LIỆU MẪU: Nếu chưa nhập showroom -> Ẩn hoàn toàn khối
    if (empty($showrooms) || !is_array($showrooms)) {
        return;
    }

    // Lọc danh sách showroom hợp lệ
    $valid_showrooms = array();
    foreach ($showrooms as $sr) {
        $city = trim($sr['city'] ?? '');
        $addr = trim($sr['address'] ?? '');
        $img  = $sr['image'] ?? '';
        if (!empty($city) || !empty($addr) || !empty($img)) {
            $img_url = '';
            if ($img) {
                $img_url = is_array($img) ? ($img['url'] ?? '') : (is_numeric($img) ? wp_get_attachment_image_url($img, 'large') : $img);
            }
            $valid_showrooms[] = array(
                'city'    => $city,
                'address' => $addr,
                'hours'   => trim($sr['hours'] ?? ''),
                'badge'   => trim($sr['badge'] ?? ''),
                'img_url' => $img_url,
                'link'    => trim($sr['link'] ?? ''),
            );
        }
    }

    if (empty($valid_showrooms)) {
        return;
    }

    // Tiêu đề & Slogan phụ
    if (empty($title)) {
        $title = 'Hệ thống showroom GoBike';
    }
    if (empty($subtitle)) {
        $brand_replacement = !empty($brand_or_cat_name) ? esc_html($brand_or_cat_name) : 'xe';
        $subtitle = 'Đến trải nghiệm trực tiếp các mẫu xe ' . $brand_replacement . ' tại cửa hàng gần bạn';
    } else {
        $brand_replacement = !empty($brand_or_cat_name) ? esc_html($brand_or_cat_name) : 'xe';
        $subtitle = str_replace(array('{brand}', '{name}', '{category}'), $brand_replacement, $subtitle);
    }

    // Dữ liệu mặc định cho hộp Hotline nếu để trống
    if (empty($hotline_label)) $hotline_label = 'Hotline tư vấn';
    if (empty($hotline))       $hotline       = '0944 988 699';
    if (empty($hotline_time))  $hotline_time  = '8:00 - 22:00 (Tất cả các ngày)';
    if (empty($btn_text))      $btn_text      = 'Tìm đường đến cửa hàng ➔';
    if (empty($btn_link))      $btn_link      = home_url('/he-thong-cua-hang/');
    $hotline_clean = preg_replace('/[^0-9]/', '', $hotline);
    ?>
    <!-- HỆ THỐNG SHOWROOM GOBIKE (ACF) -->
    <div class="gobike-showrooms-section">
        <h3 class="showroom-title"><?php echo esc_html($title); ?></h3>
        <?php if (!empty($subtitle)): ?>
            <p class="showroom-subtitle"><?php echo esc_html($subtitle); ?></p>
        <?php endif; ?>
        
        <div class="showroom-grid">
            <?php foreach ($valid_showrooms as $sr): ?>
                <?php
                $has_link = !empty($sr['link']);
                $card_tag = $has_link ? 'a' : 'div';
                $card_href = $has_link ? ' href="' . esc_url($sr['link']) . '"' : '';
                ?>
                <<?php echo $card_tag; ?> class="showroom-card has-thumb"<?php echo $card_href; ?>>
                    <?php if (!empty($sr['img_url'])): ?>
                        <div class="showroom-thumb">
                            <img src="<?php echo esc_url($sr['img_url']); ?>" alt="<?php echo esc_attr($sr['city'] ? $sr['city'] : 'Showroom GoBike'); ?>" loading="lazy" />
                            <?php if (!empty($sr['badge'])): ?>
                                <span class="showroom-badge"><?php echo esc_html($sr['badge']); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="showroom-info">
                        <?php if (!empty($sr['city'])): ?>
                            <h5>
                                <svg class="showroom-pin-icon" width="16" height="16" viewBox="0 0 24 24" fill="#ef4444" style="flex-shrink:0;">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                <?php echo esc_html($sr['city']); ?>
                            </h5>
                        <?php endif; ?>
                        <?php if (!empty($sr['address'])): ?>
                            <p class="showroom-address"><?php echo esc_html($sr['address']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($sr['hours'])): ?>
                            <span class="open-time">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; vertical-align:-1px; margin-right:3px;">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg><?php echo esc_html($sr['hours']); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </<?php echo $card_tag; ?>>
            <?php endforeach; ?>

            <!-- HỘP THÔNG TIN HOTLINE & TÌM ĐƯỜNG (CARD THỨ 4) -->
            <div class="showroom-card card-contact">
                <div class="contact-card-top">
                    <div class="contact-card-icon">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#044b25" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 13a9 9 0 0 1 18 0"></path>
                            <rect x="2" y="11" width="3.5" height="7" rx="1.75" fill="#044b25"></rect>
                            <rect x="18.5" y="11" width="3.5" height="7" rx="1.75" fill="#044b25"></rect>
                            <path d="M20 18v1.5a3 3 0 0 1-3 3h-2.5"></path>
                            <circle cx="13" cy="22.5" r="1.5" fill="#044b25"></circle>
                        </svg>
                    </div>
                    <div class="contact-card-info">
                        <span class="hotline-label"><?php echo esc_html($hotline_label); ?></span>
                        <a href="tel:<?php echo esc_attr($hotline_clean); ?>" class="hotline-number"><?php echo esc_html($hotline); ?></a>
                        <span class="hotline-time"><?php echo esc_html($hotline_time); ?></span>
                    </div>
                </div>
                <a href="<?php echo esc_url($btn_link); ?>" class="map-link-btn"><?php echo esc_html($btn_text); ?></a>
            </div>
        </div>
    </div>
    <?php
}

/**
 * 6. Wrapper hiển thị cả 2 khối cuối trang (Dành cho footer hoặc shortcode nếu cần)
 */
function gobike_render_shop_bottom_features()
{
    gobike_render_shop_why_choose();
    gobike_render_shop_showrooms();
}



