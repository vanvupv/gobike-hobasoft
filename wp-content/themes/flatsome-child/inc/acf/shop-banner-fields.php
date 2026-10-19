<?php
/**
 * ACF Field Groups for Shop Banners, Trust Badges, SEO Content & Support Section
 * 
 * @package Flatsome-Child
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('acf/init', 'gobike_register_shop_page_acf_fields');
function gobike_register_shop_page_acf_fields()
{
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    $shop_page_id = get_option('woocommerce_shop_page_id');
    $location_rules = array(
        array(
            array(
                'param'    => 'options_page',
                'operator' => '==',
                'value'    => 'theme-general-settings',
            ),
        ),
    );

    if (!empty($shop_page_id)) {
        $location_rules[] = array(
            array(
                'param'    => 'page',
                'operator' => '==',
                'value'    => (string) $shop_page_id,
            ),
        );
    }

    acf_add_local_field_group(array(
        'key' => 'group_6a9f83f739c54',
        'title' => 'Cấu hình Trang Sản Phẩm (Banner, Cam kết & Nội dung)',
        'fields' => array(

            // =================================================================
            // TAB 1: BANNER ĐẦU TRANG SẢN PHẨM (HERO BANNER)
            // =================================================================
            array(
                'key' => 'field_tab_shop_hero_banner',
                'label' => '1. Banner Đầu Trang (Hero Banner)',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_shop_hero_banner_image',
                'label' => 'Hình ảnh Banner lớn',
                'name' => 'shop_hero_banner_image',
                'type' => 'image',
                'return_format' => 'url',
                'preview_size' => 'medium',
                'library' => 'all',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_hero_banner_link',
                'label' => 'Liên kết toàn Banner',
                'name' => 'shop_hero_banner_link',
                'type' => 'text',
                'placeholder' => 'https://...',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_hero_banner_type',
                'label' => 'Kiểu hiển thị Banner',
                'name' => 'shop_hero_banner_type',
                'type' => 'select',
                'choices' => array(
                    'custom_text' => 'Banner có khối nội dung col-6 chữ & nút bấm phủ lên (Mặc định - Chuẩn thiết kế)',
                    'graphic_only' => 'Banner ảnh thuần (Chỉ hiển thị ảnh nền, không có khối chữ phủ)',
                ),
                'default_value' => 'custom_text',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_hero_banner_hotline',
                'label' => 'Hotline tư vấn nhanh',
                'name' => 'shop_hero_banner_hotline',
                'type' => 'text',
                'default_value' => '0944 988 699',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_hero_banner_title_prefix',
                'label' => 'Tiêu đề dòng 1 (Tiền tố)',
                'name' => 'shop_hero_banner_title_prefix',
                'type' => 'text',
                'default_value' => 'XE ĐẠP TRỢ LỰC ĐIỆN',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_hero_banner_brand',
                'label' => 'Tên thương hiệu dòng 2 (Chữ to nổi bật)',
                'name' => 'shop_hero_banner_brand',
                'type' => 'text',
                'default_value' => 'ADO',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_hero_banner_slogan',
                'label' => 'Slogan phụ',
                'name' => 'shop_hero_banner_slogan',
                'type' => 'text',
                'default_value' => 'Thiết kế thông minh • Gọn nhẹ • Đồng hành mọi hành trình',
                'wrapper' => array('width' => '100'),
            ),
            array(
                'key' => 'field_shop_hero_banner_desc',
                'label' => 'Đoạn văn bản mô tả giới thiệu (col-6)',
                'name' => 'shop_hero_banner_desc',
                'type' => 'textarea',
                'default_value' => 'ADO là thương hiệu xe đạp trợ lực điện được yêu thích trên toàn cầu với thiết kế hiện đại, công nghệ thông minh và trải nghiệm lái mượt mà. Lựa chọn hoàn hảo cho cuộc sống năng động và xanh hơn mỗi ngày.',
                'rows' => 3,
                'wrapper' => array('width' => '100'),
            ),
            array(
                'key' => 'field_shop_hero_banner_btn_text',
                'label' => 'Chữ trên nút chính',
                'name' => 'shop_hero_banner_btn_text',
                'type' => 'text',
                'default_value' => 'Xem tất cả sản phẩm',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_hero_banner_btn_link',
                'label' => 'Đường dẫn nút chính',
                'name' => 'shop_hero_banner_btn_link',
                'type' => 'text',
                'default_value' => '#products-grid',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_hero_banner_hotline_text',
                'label' => 'Chữ trên nút tư vấn phụ',
                'name' => 'shop_hero_banner_hotline_text',
                'type' => 'text',
                'default_value' => 'Nhận tư vấn nhanh',
                'wrapper' => array('width' => '50'),
            ),

            // =================================================================
            // TAB 2: BỐN KHỐI NỔI BẬT (CAM KẾT TRANG SẢN PHẨM)
            // =================================================================
            array(
                'key' => 'field_tab_shop_trust_badges',
                'label' => '2. 4 Khối Nổi Bật (Cam Kết)',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_shop_trust_badges',
                'label' => 'Danh sách 4 Khối nổi bật / Cam kết',
                'name' => 'shop_trust_badges',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => '+ Thêm khối nổi bật',
                'sub_fields' => array(
                    array(
                        'key' => 'field_shop_trust_badge_icon',
                        'label' => 'Icon / Ảnh (PNG, SVG)',
                        'name' => 'icon',
                        'type' => 'image',
                        'return_format' => 'url',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                        'wrapper' => array('width' => '18'),
                    ),
                    array(
                        'key' => 'field_shop_trust_badge_title',
                        'label' => 'Tiêu đề',
                        'name' => 'title',
                        'type' => 'text',
                        'placeholder' => 'Vd: Chính hãng 100%',
                        'wrapper' => array('width' => '25'),
                    ),
                    array(
                        'key' => 'field_shop_trust_badge_desc',
                        'label' => 'Mô tả ngắn',
                        'name' => 'desc',
                        'type' => 'text',
                        'placeholder' => 'Vd: Đầy đủ CO, CQ',
                        'wrapper' => array('width' => '27'),
                    ),
                    array(
                        'key' => 'field_shop_trust_badge_link',
                        'label' => 'Đường dẫn liên kết',
                        'name' => 'link',
                        'type' => 'text',
                        'placeholder' => 'https://...',
                        'wrapper' => array('width' => '30'),
                    ),
                ),
            ),

            // =================================================================
            // TAB 3: CẶP BANNER TIỆN ÍCH PHỤ
            // =================================================================
            array(
                'key' => 'field_tab_shop_banners',
                'label' => '3. Cặp Banner Tiện Ích Phụ',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_shop_banner_image_1',
                'label' => 'Hình ảnh Banner 1 (Trái)',
                'name' => 'shop_banner_image_1',
                'type' => 'image',
                'return_format' => 'url',
                'preview_size' => 'medium',
                'library' => 'all',
            ),
            array(
                'key' => 'field_shop_banner_link_1',
                'label' => 'Liên kết Banner 1',
                'name' => 'shop_banner_link_1',
                'type' => 'text',
                'placeholder' => '#',
                'default_value' => '',
            ),
            array(
                'key' => 'field_shop_banner_image_2',
                'label' => 'Hình ảnh Banner 2 (Phải)',
                'name' => 'shop_banner_image_2',
                'type' => 'image',
                'return_format' => 'url',
                'preview_size' => 'medium',
                'library' => 'all',
            ),
            array(
                'key' => 'field_shop_banner_link_2',
                'label' => 'Liên kết Banner 2',
                'name' => 'shop_banner_link_2',
                'type' => 'text',
                'placeholder' => '#',
                'default_value' => '',
            ),

            // =================================================================
            // TAB 4: NỘI DUNG CUỐI TRANG (SEO)
            // =================================================================
            array(
                'key' => 'field_tab_shop_bottom_content',
                'label' => '4. Nội dung cuối trang (SEO)',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_shop_bottom_title',
                'label' => 'Tiêu đề nội dung chân trang',
                'name' => 'shop_bottom_title',
                'type' => 'text',
                'default_value' => '',
            ),
            array(
                'key' => 'field_shop_bottom_content',
                'label' => 'Nội dung chi tiết (Editor)',
                'name' => 'shop_bottom_content',
                'type' => 'wysiwyg',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'default_value' => '',
            ),

            // =================================================================
            // TAB 5: HỖ TRỢ KHÁCH HÀNG
            // =================================================================
            array(
                'key' => 'field_tab_shop_single_support',
                'label' => '5. Hỗ trợ khách hàng',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_shop_single_support_title',
                'label' => 'Tiêu đề khối hỗ trợ',
                'name' => 'shop_single_support_title',
                'type' => 'textarea',
                'default_value' => "CHÚNG TÔI LUÔN SẴN SÀNG\nĐỂ GIÚP ĐỠ BẠN",
                'rows' => 2,
                'new_lines' => 'br',
            ),
            array(
                'key' => 'field_shop_single_support_image',
                'label' => 'Hình ảnh nhân viên / Đội ngũ hỗ trợ',
                'name' => 'shop_single_support_image',
                'type' => 'image',
                'return_format' => 'url',
                'preview_size' => 'medium',
                'library' => 'all',
            ),
            array(
                'key' => 'field_shop_single_support_call_text',
                'label' => 'Dòng chữ trước số điện thoại',
                'name' => 'shop_single_support_call_text',
                'type' => 'text',
                'default_value' => 'Để được hỗ trợ tốt nhất. Hãy gọi',
            ),
            array(
                'key' => 'field_shop_single_support_phone',
                'label' => 'Số điện thoại Hotline',
                'name' => 'shop_single_support_phone',
                'type' => 'text',
                'default_value' => '0944 988 699',
            ),
            array(
                'key' => 'field_shop_single_support_chat_text',
                'label' => 'Dòng chữ giới thiệu Chat',
                'name' => 'shop_single_support_chat_text',
                'type' => 'text',
                'default_value' => 'Chat hỗ trợ trực tuyến',
            ),
            array(
                'key' => 'field_shop_single_support_chat_btn_text',
                'label' => 'Chữ trên nút Chat',
                'name' => 'shop_single_support_chat_btn_text',
                'type' => 'text',
                'default_value' => 'CHAT VỚI CHÚNG TÔI',
            ),
            array(
                'key' => 'field_shop_single_support_chat_link',
                'label' => 'Đường dẫn nút Chat',
                'name' => 'shop_single_support_chat_link',
                'type' => 'text',
                'default_value' => 'https://zalo.me/0944988699',
            ),

            // =================================================================
            // TAB 6: VÌ SAO CHỌN GOBIKE (CHUẨN ẢNH THIẾT KẾ)
            // =================================================================
            array(
                'key' => 'field_tab_shop_why_choose',
                'label' => '6. Vì sao chọn GoBike',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_shop_why_choose_title',
                'label' => 'Tiêu đề khối',
                'name' => 'shop_why_choose_title',
                'type' => 'text',
                'placeholder' => 'Vd: Vì sao chọn GoBike khi mua xe {brand}?',
                'default_value' => '',
            ),
            array(
                'key' => 'field_shop_why_choose_items',
                'label' => 'Danh sách lý do chọn GoBike',
                'name' => 'shop_why_choose_items',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => '+ Thêm lý do',
                'sub_fields' => array(
                    array(
                        'key' => 'field_shop_why_choose_icon',
                        'label' => 'Icon / Ảnh (PNG, SVG)',
                        'name' => 'icon',
                        'type' => 'image',
                        'return_format' => 'url',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                        'wrapper' => array('width' => '18'),
                    ),
                    array(
                        'key' => 'field_shop_why_choose_title_item',
                        'label' => 'Tiêu đề',
                        'name' => 'title',
                        'type' => 'text',
                        'placeholder' => 'Vd: Chính hãng 100%',
                        'wrapper' => array('width' => '25'),
                    ),
                    array(
                        'key' => 'field_shop_why_choose_desc',
                        'label' => 'Mô tả ngắn',
                        'name' => 'desc',
                        'type' => 'text',
                        'placeholder' => 'Vd: Sản phẩm chính hãng, đầy đủ CO, CQ, xuất VAT',
                        'wrapper' => array('width' => '35'),
                    ),
                    array(
                        'key' => 'field_shop_why_choose_link',
                        'label' => 'Liên kết (Tùy chọn)',
                        'name' => 'link',
                        'type' => 'text',
                        'placeholder' => 'https://...',
                        'wrapper' => array('width' => '22'),
                    ),
                ),
            ),

            // =================================================================
            // TAB 7: HỆ THỐNG SHOWROOM
            // =================================================================
            array(
                'key' => 'field_tab_shop_showrooms',
                'label' => '7. Hệ thống Showroom',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_shop_showroom_title',
                'label' => 'Tiêu đề khối Showroom',
                'name' => 'shop_showroom_title',
                'type' => 'text',
                'placeholder' => 'Vd: Hệ thống showroom GoBike',
                'default_value' => '',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_showroom_subtitle',
                'label' => 'Mô tả phụ Showroom',
                'name' => 'shop_showroom_subtitle',
                'type' => 'text',
                'placeholder' => 'Vd: Đến trải nghiệm trực tiếp các mẫu xe {brand} tại cửa hàng gần bạn',
                'default_value' => '',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_showroom_list',
                'label' => 'Danh sách Showroom cửa hàng',
                'name' => 'shop_showroom_list',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => '+ Thêm showroom',
                'sub_fields' => array(
                    array(
                        'key' => 'field_shop_showroom_img',
                        'label' => 'Ảnh mặt tiền showroom',
                        'name' => 'image',
                        'type' => 'image',
                        'return_format' => 'url',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                        'wrapper' => array('width' => '20'),
                    ),
                    array(
                        'key' => 'field_shop_showroom_badge',
                        'label' => 'Huy hiệu',
                        'name' => 'badge',
                        'type' => 'text',
                        'placeholder' => 'Vd: CS1',
                        'wrapper' => array('width' => '10'),
                    ),
                    array(
                        'key' => 'field_shop_showroom_city',
                        'label' => 'Tỉnh / Thành phố',
                        'name' => 'city',
                        'type' => 'text',
                        'placeholder' => 'Vd: Hà Nội',
                        'wrapper' => array('width' => '15'),
                    ),
                    array(
                        'key' => 'field_shop_showroom_address',
                        'label' => 'Địa chỉ chi tiết',
                        'name' => 'address',
                        'type' => 'text',
                        'placeholder' => 'Vd: 71 Trần Đăng Ninh, Hà Nội',
                        'wrapper' => array('width' => '25'),
                    ),
                    array(
                        'key' => 'field_shop_showroom_hours',
                        'label' => 'Giờ mở cửa',
                        'name' => 'hours',
                        'type' => 'text',
                        'placeholder' => 'Vd: 8:00 - 21:00 hàng ngày',
                        'wrapper' => array('width' => '15'),
                    ),
                    array(
                        'key' => 'field_shop_showroom_link',
                        'label' => 'Link bản đồ / Chi tiết',
                        'name' => 'link',
                        'type' => 'text',
                        'placeholder' => 'https://...',
                        'wrapper' => array('width' => '15'),
                    ),
                ),
            ),
            array(
                'key' => 'field_shop_showroom_hotline_label',
                'label' => 'Nhãn Hotline',
                'name' => 'shop_showroom_hotline_label',
                'type' => 'text',
                'placeholder' => 'Hotline tư vấn',
                'default_value' => '',
                'wrapper' => array('width' => '33'),
            ),
            array(
                'key' => 'field_shop_showroom_hotline',
                'label' => 'Số Hotline',
                'name' => 'shop_showroom_hotline',
                'type' => 'text',
                'placeholder' => '0944 988 699',
                'default_value' => '',
                'wrapper' => array('width' => '33'),
            ),
            array(
                'key' => 'field_shop_showroom_hotline_time',
                'label' => 'Thời gian tư vấn Hotline',
                'name' => 'shop_showroom_hotline_time',
                'type' => 'text',
                'placeholder' => '8:00 - 22:00 (Tất cả các ngày)',
                'default_value' => '',
                'wrapper' => array('width' => '34'),
            ),
            array(
                'key' => 'field_shop_showroom_btn_text',
                'label' => 'Chữ trên nút chỉ đường',
                'name' => 'shop_showroom_btn_text',
                'type' => 'text',
                'placeholder' => 'Tìm đường đến cửa hàng ➔',
                'default_value' => '',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_showroom_btn_link',
                'label' => 'Liên kết nút chỉ đường',
                'name' => 'shop_showroom_btn_link',
                'type' => 'text',
                'placeholder' => '/cua-hang/',
                'default_value' => '',
                'wrapper' => array('width' => '50'),
            ),

            // =================================================================
            // TAB 8: 3 BANNER TIỆN ÍCH DƯỚI PHÂN TRANG (UTILITY BANNERS)
            // =================================================================
            array(
                'key' => 'field_tab_shop_utility_banners',
                'label' => '8. 3 Banner Tiện Ích (Dưới Phân Trang)',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_shop_utility_banners_list',
                'label' => 'Danh sách Banner tiện ích',
                'name' => 'shop_utility_banners_list',
                'type' => 'repeater',
                'layout' => 'table',
                'button_label' => '+ Thêm banner tiện ích',
                'sub_fields' => array(
                    array(
                        'key' => 'field_shop_ub_img',
                        'label' => 'Ảnh nền banner',
                        'name' => 'image',
                        'type' => 'image',
                        'return_format' => 'url',
                        'preview_size' => 'medium',
                        'library' => 'all',
                        'wrapper' => array('width' => '25'),
                    ),
                    array(
                        'key' => 'field_shop_ub_title',
                        'label' => 'Tiêu đề banner',
                        'name' => 'title',
                        'type' => 'text',
                        'placeholder' => 'Vd: Video review xe',
                        'wrapper' => array('width' => '25'),
                    ),
                    array(
                        'key' => 'field_shop_ub_desc',
                        'label' => 'Mô tả phụ',
                        'name' => 'subtitle',
                        'type' => 'textarea',
                        'rows' => 2,
                        'placeholder' => "Trải nghiệm thực tế\nTừ người dùng thật",
                        'wrapper' => array('width' => '25'),
                    ),
                    array(
                        'key' => 'field_shop_ub_link',
                        'label' => 'Nút bấm / Đường dẫn',
                        'name' => 'link',
                        'type' => 'link',
                        'return_format' => 'array',
                        'wrapper' => array('width' => '25'),
                    ),
                ),
            ),
        ),
        'location' => $location_rules,
        'menu_order' => 15,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
    ));
}
