<?php
/**
 * ACF Field Groups for Shop Banners, SEO Content & Support Section
 * 
 * @package Flatsome-Child
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('acf/init', 'gobike_register_shop_page_acf_fields');
function gobike_register_shop_page_acf_fields()
{
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
            'key' => 'group_6a9f83f739c54',
            'title' => 'Banner Sản phẩm',
            'fields' => array(

                // TAB 1: Khối 1
                array(
                    'key' => 'field_tab_shop_banners',
                    'label' => 'Khối 1: Cặp Banner Tiện Ích Đầu Trang',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ),
                array(
                    'key' => 'field_shop_banner_image_1',
                    'label' => 'Hình ảnh Banner 1 (Trái)',
                    'name' => 'shop_banner_image_1',
                    'type' => 'image',
                    'instructions' => 'Tải lên hình ảnh banner 1 (khuyên dùng ~ 600x120px)',
                    'return_format' => 'url',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ),
                array(
                    'key' => 'field_shop_banner_link_1',
                    'label' => 'Liên kết Banner 1',
                    'name' => 'shop_banner_link_1',
                    'type' => 'url',
                    'instructions' => 'Đường dẫn khi click vào banner 1',
                    'default_value' => '#',
                ),
                array(
                    'key' => 'field_shop_banner_image_2',
                    'label' => 'Hình ảnh Banner 2 (Phải)',
                    'name' => 'shop_banner_image_2',
                    'type' => 'image',
                    'instructions' => 'Tải lên hình ảnh banner 2 (khuyên dùng ~ 600x120px)',
                    'return_format' => 'url',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ),
                array(
                    'key' => 'field_shop_banner_link_2',
                    'label' => 'Liên kết Banner 2',
                    'name' => 'shop_banner_link_2',
                    'type' => 'url',
                    'instructions' => 'Đường dẫn khi click vào banner 2',
                    'default_value' => '#',
                ),

                // TAB 2: Khối 2
                array(
                    'key' => 'field_tab_shop_bottom_content',
                    'label' => 'Khối 2: Nội dung cuối trang (SEO)',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ),
                array(
                    'key' => 'field_shop_bottom_title',
                    'label' => 'Tiêu đề nội dung chân trang',
                    'name' => 'shop_bottom_title',
                    'type' => 'text',
                    'instructions' => 'Nhập tiêu đề khối nội dung cuối trang',
                    'default_value' => 'Hệ thống cửa hàng bán lẻ xe đạp trợ lực điện Aimos',
                ),
                array(
                    'key' => 'field_shop_bottom_content',
                    'label' => 'Nội dung chi tiết (Editor)',
                    'name' => 'shop_bottom_content',
                    'type' => 'wysiwyg',
                    'instructions' => 'Nội dung giới thiệu chính sách, bảo hành cuối trang',
                    'tabs' => 'all',
                    'toolbar' => 'full',
                    'media_upload' => 1,
                    'default_value' => "Giá rẻ nhất Việt Nam\nTrả góp 0% qua thẻ tín dụng\nBảo hành 12 tháng\nHỗ trợ bảo trì trọn đời - Mua phụ tùng xe với giá gốc trong 5 năm\nCông ty chịu mọi rủi ro trong quá trình vận chuyển\nShip hàng COD Toàn Quốc Quý khách nhận hàng, kiểm tra và thu tiền tại nhà, an tâm tuyệt đối.",
                ),

                // TAB 3: Khối 3
                array(
                    'key' => 'field_tab_shop_single_support',
                    'label' => 'Khối 3: Hỗ trợ khách hàng (Trang chi tiết)',
                    'name' => '',
                    'type' => 'tab',
                    'placement' => 'top',
                ),
                array(
                    'key' => 'field_shop_single_support_title',
                    'label' => 'Tiêu đề khối hỗ trợ',
                    'name' => 'shop_single_support_title',
                    'type' => 'textarea',
                    'instructions' => 'Tiêu đề khối hỗ trợ (có thể xuống dòng để hiển thị đẹp)',
                    'default_value' => "CHÚNG TÔI LUÔN SẴN SÀNG\nĐỂ GIÚP ĐỠ BẠN",
                    'rows' => 2,
                    'new_lines' => 'br',
                ),
                array(
                    'key' => 'field_shop_single_support_image',
                    'label' => 'Hình ảnh nhân viên / Đội ngũ hỗ trợ',
                    'name' => 'shop_single_support_image',
                    'type' => 'image',
                    'instructions' => 'Tải lên hình ảnh đội ngũ tư vấn hoặc showroom',
                    'return_format' => 'url',
                    'preview_size' => 'medium',
                    'library' => 'all',
                ),
                array(
                    'key' => 'field_shop_single_support_call_text',
                    'label' => 'Dòng chữ trước số điện thoại',
                    'name' => 'shop_single_support_call_text',
                    'type' => 'text',
                    'instructions' => 'Ví dụ: Để được hỗ trợ tốt nhất. Hãy gọi',
                    'default_value' => 'Để được hỗ trợ tốt nhất. Hãy gọi',
                ),
                array(
                    'key' => 'field_shop_single_support_phone',
                    'label' => 'Số điện thoại Hotline',
                    'name' => 'shop_single_support_phone',
                    'type' => 'text',
                    'instructions' => 'Số điện thoại Hotline hiển thị nổi bật',
                    'default_value' => '0944 988 699',
                ),
                array(
                    'key' => 'field_shop_single_support_chat_text',
                    'label' => 'Dòng chữ giới thiệu Chat',
                    'name' => 'shop_single_support_chat_text',
                    'type' => 'text',
                    'instructions' => 'Ví dụ: Chat hỗ trợ trực tuyến',
                    'default_value' => 'Chat hỗ trợ trực tuyến',
                ),
                array(
                    'key' => 'field_shop_single_support_chat_btn_text',
                    'label' => 'Chữ trên nút Chat',
                    'name' => 'shop_single_support_chat_btn_text',
                    'type' => 'text',
                    'instructions' => 'Ví dụ: CHAT VỚI CHÚNG TÔI',
                    'default_value' => 'CHAT VỚI CHÚNG TÔI',
                ),
                array(
                    'key' => 'field_shop_single_support_chat_link',
                    'label' => 'Đường dẫn nút Chat',
                    'name' => 'shop_single_support_chat_link',
                    'type' => 'text',
                    'instructions' => 'Đường dẫn Zalo, Messenger hoặc link chat (Ví dụ: https://zalo.me/0944988699)',
                    'default_value' => 'https://zalo.me/0944988699',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'options_page',
                        'operator' => '==',
                        'value' => 'theme-general-settings',
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
