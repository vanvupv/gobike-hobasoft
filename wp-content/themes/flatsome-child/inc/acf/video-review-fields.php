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
                array(
                    'key' => 'field_vr_type',
                    'label' => 'Định dạng Video',
                    'name' => 'video_type',
                    'type' => 'select',
                    'choices' => array(
                        'shorts'  => 'YouTube Shorts (Video dọc 9:16 - CellphoneS style)',
                        'youtube' => 'YouTube Chuẩn (Video ngang 16:9)',
                    ),
                    'default_value' => 'shorts',
                    'ui' => 1,
                ),
                array(
                    'key' => 'field_vr_title_overlay',
                    'label' => 'Dòng chữ nghệ thuật nổi trên Video Shorts',
                    'name' => 'video_title_overlay',
                    'type' => 'text',
                    'instructions' => 'Ví dụ: Đi làm mỗi ngày thật nhẹ nhàng!, Khám phá thành phố theo cách riêng!',
                    'placeholder' => 'Đi làm mỗi ngày thật nhẹ nhàng!',
                ),
                array(
                    'key' => 'field_vr_related_product',
                    'label' => 'Sản phẩm liên kết (WooCommerce Product)',
                    'name' => 'related_product',
                    'type' => 'post_object',
                    'post_type' => array('product'),
                    'instructions' => 'Chọn chiếc xe liên quan đến video này (sẽ hiển thị thẻ sản phẩm bên dưới video).',
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

        // Field Group dành riêng cho chi tiết Sản phẩm (WooCommerce Product)
        acf_add_local_field_group(array(
            'key' => 'group_product_video_experience',
            'title' => 'Video Trải Nghiệm & Đánh Giá Xe',
            'fields' => array(
                array(
                    'key' => 'field_prod_video_url',
                    'label' => 'Link YouTube / Shorts Trực Tiếp',
                    'name' => 'product_video_url',
                    'type' => 'url',
                    'instructions' => 'Nhập link YouTube hoặc YouTube Shorts trải nghiệm chiếc xe này (ví dụ: https://www.youtube.com/shorts/... hoặc https://www.youtube.com/watch?v=...)',
                    'required' => 0,
                    'placeholder' => 'https://www.youtube.com/shorts/...',
                ),
                array(
                    'key' => 'field_prod_video_title_overlay',
                    'label' => 'Dòng chữ mô tả nổi trên Video Shorts',
                    'name' => 'product_video_title_overlay',
                    'type' => 'text',
                    'instructions' => 'Ví dụ: Trải nghiệm đi làm hàng ngày cực êm ái',
                    'placeholder' => 'Trải nghiệm thực tế xe...',
                ),
                array(
                    'key' => 'field_prod_video_views',
                    'label' => 'Số lượt xem hiển thị',
                    'name' => 'product_video_views',
                    'type' => 'text',
                    'instructions' => 'Ví dụ: 85K lượt xem',
                    'default_value' => '50K lượt xem',
                ),
                array(
                    'key' => 'field_prod_linked_reviews',
                    'label' => 'Hoặc chọn các bài viết từ CPT "Video Review"',
                    'name' => 'product_linked_reviews',
                    'type' => 'relationship',
                    'post_type' => array('video_review'),
                    'instructions' => 'Chọn một hoặc nhiều bài video review từ CPT Video Review để hiển thị ở tab / khối video của sản phẩm này.',
                    'filters' => array('search'),
                    'return_format' => 'id',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'product',
                    ),
                ),
            ),
            'menu_order' => 15,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ));
    }
}

