<?php
/**
 * Custom Post Type: "NGƯỜI THẬT - XE THẬT - TRẢI NGHIỆM THẬT"
 * 
 * @package Flatsome-Child
 */

if (!defined('ABSPATH')) {
    exit;
}

// 1. Đăng ký CPT customer_experience
add_action('init', 'gobike_register_customer_experience_cpt');
function gobike_register_customer_experience_cpt() {
    $labels = array(
        'name'                  => 'Người Thật - Xe Thật',
        'singular_name'         => 'Trải Nghiệm Thực Tế',
        'menu_name'             => 'Người Thật - Xe Thật',
        'name_admin_bar'        => 'Trải Nghiệm Khách Hàng',
        'archives'              => 'Kho Trải Nghiệm Khách Hàng',
        'attributes'            => 'Thuộc tính',
        'all_items'             => 'Tất cả Trải Nghiệm',
        'add_new_item'          => 'Thêm Video Trải Nghiệm Mới',
        'add_new'               => 'Thêm Mới',
        'new_item'              => 'Bài Mới',
        'edit_item'             => 'Chỉnh Sửa Trải Nghiệm',
        'update_item'           => 'Cập Nhật Trải Nghiệm',
        'view_item'             => 'Xem Trải Nghiệm',
        'view_items'            => 'Xem Danh Sách',
        'search_items'          => 'Tìm Kiếm Trải Nghiệm',
        'not_found'             => 'Không tìm thấy bài trải nghiệm nào',
        'not_found_in_trash'    => 'Thùng rác trống',
    );

    $args = array(
        'label'                 => 'Người Thật - Xe Thật',
        'description'           => 'Video đánh giá thực tế từ khách hàng sử dụng xe đạp điện GoBike',
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'excerpt'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 21,
        'menu_icon'             => 'dashicons-groups',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'show_in_rest'          => true,
        'rewrite'               => array('slug' => 'trai-nghiem-khach-hang', 'with_front' => false),
    );

    register_post_type('customer_experience', $args);
}

// 2. Thêm cột tùy chỉnh trong trang quản trị
add_filter('manage_customer_experience_posts_columns', function($columns) {
    $new_cols = array();
    foreach ($columns as $key => $title) {
        $new_cols[$key] = $title;
        if ($key === 'title') {
            $new_cols['customer_info'] = 'Khách Hàng & Nơi Ở';
            $new_cols['video_duration'] = 'Thời Lượng';
            $new_cols['video_views'] = 'Lượt Xem';
        }
    }
    return $new_cols;
});

add_action('manage_customer_experience_posts_custom_column', function($column, $post_id) {
    if ($column === 'customer_info') {
        $name = get_post_meta($post_id, 'customer_name', true) ?: 'Khách hàng GoBike';
        $city = get_post_meta($post_id, 'customer_city', true) ?: 'Việt Nam';
        echo '<strong>' . esc_html($name) . '</strong> - ' . esc_html($city);
    } elseif ($column === 'video_duration') {
        echo esc_html(get_post_meta($post_id, 'video_duration', true) ?: '05:00');
    } elseif ($column === 'video_views') {
        echo esc_html(get_post_meta($post_id, 'video_views', true) ?: '10K lượt xem');
    }
}, 10, 2);

// 3. Thêm ACF Field Group cho customer_experience
add_action('acf/init', function() {
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_customer_experience_fields',
            'title' => 'Thông Tin Video Khách Hàng Trải Nghiệm',
            'fields' => array(
                array(
                    'key' => 'field_ce_video_url',
                    'label' => 'Đường dẫn Video YouTube',
                    'name' => 'video_url',
                    'type' => 'url',
                    'instructions' => 'Dán link YouTube (ví dụ: https://www.youtube.com/watch?v=...)',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_ce_duration',
                    'label' => 'Thời lượng Video',
                    'name' => 'video_duration',
                    'type' => 'text',
                    'instructions' => 'Ví dụ: 09:12, 06:46, 08:20, 07:36',
                    'default_value' => '07:30',
                ),
                array(
                    'key' => 'field_ce_customer_name',
                    'label' => 'Tên khách hàng',
                    'name' => 'customer_name',
                    'type' => 'text',
                    'instructions' => 'Ví dụ: Anh Minh, Chị Lan, Anh Hoàng, Chị Hương',
                    'default_value' => 'Khách hàng',
                ),
                array(
                    'key' => 'field_ce_customer_city',
                    'label' => 'Khu vực / Tỉnh thành',
                    'name' => 'customer_city',
                    'type' => 'text',
                    'instructions' => 'Ví dụ: Hà Nội, TP.HCM, Đà Nẵng, Hải Phòng',
                    'default_value' => 'Hà Nội',
                ),
                array(
                    'key' => 'field_ce_views',
                    'label' => 'Số lượt xem hiển thị',
                    'name' => 'video_views',
                    'type' => 'text',
                    'instructions' => 'Ví dụ: 12K lượt xem • 1 tháng trước',
                    'default_value' => '10K lượt xem • 1 tháng trước',
                ),
                array(
                    'key' => 'field_ce_related_product',
                    'label' => 'Mẫu xe khách hàng sử dụng',
                    'name' => 'related_product',
                    'type' => 'post_object',
                    'post_type' => array('product'),
                    'return_format' => 'id',
                    'allow_null' => 1,
                    'multiple' => 0,
                    'ui' => 1,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'customer_experience',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
        ));
    }
});
