<?php
/**
 * ACF Field Group for Video Reviews
 * 
 * @package Flatsome-Child
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('acf/init', 'gobike_register_video_review_acf_fields');
function gobike_register_video_review_acf_fields()
{
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_video_review_info',
            'title' => 'Thông tin Video Review',
            'fields' => array(
                array(
                    'key' => 'field_vr_video_url',
                    'label' => 'Đường dẫn Video YouTube / Shorts',
                    'name' => 'video_url',
                    'type' => 'url',
                    'instructions' => 'Dán link YouTube (ví dụ: https://www.youtube.com/watch?v=... hoặc https://www.youtube.com/shorts/...)',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_vr_thumbnail',
                    'label' => 'Ảnh bìa tùy chỉnh (Thumbnail)',
                    'name' => 'video_thumbnail',
                    'type' => 'image',
                    'instructions' => 'Tải lên ảnh bìa đẹp (khuyên dùng tỉ lệ 16:9). Nếu để trống, hệ thống sẽ tự động lấy ảnh bìa HD từ YouTube.',
                    'return_format' => 'url',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ),
                array(
                    'key' => 'field_vr_duration',
                    'label' => 'Thời lượng Video',
                    'name' => 'video_duration',
                    'type' => 'text',
                    'instructions' => 'Ví dụ: 10:24, 06:12, 05:38',
                    'default_value' => '05:00',
                ),
                array(
                    'key' => 'field_vr_is_featured',
                    'label' => 'Đặt làm Video nổi bật nhất (Khung lớn bên trái)?',
                    'name' => 'is_featured',
                    'type' => 'true_false',
                    'instructions' => 'Bật tùy chọn này để đưa video vào vị trí nổi bật lớn nhất của Section.',
                    'default_value' => 0,
                    'ui' => 1,
                ),
                array(
                    'key' => 'field_vr_views_text',
                    'label' => 'Lượt xem & Thời gian đăng',
                    'name' => 'video_views_text',
                    'type' => 'text',
                    'instructions' => 'Ví dụ: 32K lượt xem • 2 tuần trước',
                    'default_value' => '25K lượt xem • 1 tuần trước',
                ),
                array(
                    'key' => 'field_vr_badge_tag',
                    'label' => 'Nhãn thẻ Video (Badge)',
                    'name' => 'video_badge_tag',
                    'type' => 'text',
                    'instructions' => 'Ví dụ: Review từ người thật, Test thực tế, Đánh giá người dùng, Hướng dẫn chi tiết',
                    'default_value' => 'Review từ người thật',
                ),
                array(
                    'key' => 'field_vr_desc',
                    'label' => 'Mô tả ngắn (Dành cho Video nổi bật)',
                    'name' => 'video_desc',
                    'type' => 'textarea',
                    'instructions' => 'Đoạn tóm tắt cảm nhận thực tế dành cho video lớn bên trái.',
                    'rows' => 3,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'video_review',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ));
    }
}
