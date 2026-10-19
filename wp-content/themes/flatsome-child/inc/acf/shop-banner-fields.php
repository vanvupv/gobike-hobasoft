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
                'instructions' => 'Tải lên hình ảnh banner lớn hiển thị đầu trang sản phẩm (kích thước khuyên dùng: 1200x380px hoặc 1920x500px). Nếu để trống sẽ sử dụng banner mặc định của GoBike.',
                'return_format' => 'url',
                'preview_size' => 'medium',
                'library' => 'all',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_hero_banner_link',
                'label' => 'Liên kết toàn Banner (Tùy chọn)',
                'name' => 'shop_hero_banner_link',
                'type' => 'text',
                'placeholder' => 'https://...',
                'instructions' => 'Đường dẫn chuyển tiếp khi khách hàng click vào ảnh banner (để trống nếu không cần click chuyển trang).',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_hero_banner_type',
                'label' => 'Kiểu hiển thị Banner',
                'name' => 'shop_hero_banner_type',
                'type' => 'select',
                'instructions' => 'Chọn chế độ hiển thị phù hợp:',
                'choices' => array(
                    'graphic' => 'Banner ảnh nguyên bản (Chuẩn thiết kế GoBike, hiển thị trọn vẹn ảnh sắc nét)',
                    'custom_text' => 'Banner có chữ & nút bấm phủ lên (Tiêu đề, Slogan, Nút xem sản phẩm & Hotline)',
                ),
                'default_value' => 'graphic',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_hero_banner_hotline',
                'label' => 'Hotline tư vấn nhanh',
                'name' => 'shop_hero_banner_hotline',
                'type' => 'text',
                'instructions' => 'Số hotline nhận cuộc gọi tư vấn',
                'default_value' => '0944 988 699',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_hero_banner_title',
                'label' => 'Tiêu đề Banner (Kiểu chữ phủ)',
                'name' => 'shop_hero_banner_title',
                'type' => 'text',
                'instructions' => 'Mặc định: XE ĐẠP TRỢ LỰC ĐIỆN',
                'default_value' => 'XE ĐẠP TRỢ LỰC ĐIỆN',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_shop_hero_banner_type',
                            'operator' => '==',
                            'value' => 'custom_text',
                        ),
                    ),
                ),
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_hero_banner_slogan',
                'label' => 'Slogan phụ (Kiểu chữ phủ)',
                'name' => 'shop_hero_banner_slogan',
                'type' => 'text',
                'instructions' => 'Mặc định: Thiết kế thông minh • Gọn nhẹ • Đồng hành mọi hành trình',
                'default_value' => 'Thiết kế thông minh • Gọn nhẹ • Đồng hành mọi hành trình',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'field_shop_hero_banner_type',
                            'operator' => '==',
                            'value' => 'custom_text',
                        ),
                    ),
                ),
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
                'key' => 'field_shop_trust_badges_note',
                'label' => 'Thông tin 4 khối nổi bật',
                'name' => '',
                'type' => 'message',
                'message' => '<em>Dải 4 khối nổi bật nằm ngay dưới banner lớn ở trang sản phẩm (Ví dụ: Chính hãng 100%, Bảo hành 12 - 24 tháng, Trả góp 0%, Giao hàng toàn quốc). Nếu để trống không nhập, khối này sẽ được ẩn hoàn toàn trên website.</em>',
            ),
            array(
                'key' => 'field_shop_trust_badges',
                'label' => 'Danh sách 4 Khối nổi bật / Cam kết',
                'name' => 'shop_trust_badges',
                'type' => 'repeater',
                'instructions' => 'Thêm hoặc tùy chỉnh các mục cam kết. Mỗi mục gồm Icon, Tiêu đề và Mô tả ngắn.',
                'layout' => 'table',
                'button_label' => '+ Thêm khối nổi bật',
                'sub_fields' => array(
                    array(
                        'key' => 'field_shop_trust_badge_icon',
                        'label' => 'Icon / Ảnh (PNG, SVG)',
                        'name' => 'icon',
                        'type' => 'image',
                        'instructions' => 'Để trống sẽ tự dùng icon SVG mặc định',
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
                'instructions' => 'Tải lên hình ảnh banner 1 (khuyên dùng ~ 600x120px)',
                'return_format' => 'url',
                'preview_size' => 'medium',
                'library' => 'all',
            ),
            array(
                'key' => 'field_shop_banner_link_1',
                'label' => 'Liên kết Banner 1',
                'name' => 'shop_banner_link_1',
                'type' => 'text',
                'instructions' => 'Đường dẫn khi click vào banner 1',
                'placeholder' => '#',
                'default_value' => '',
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
                'type' => 'text',
                'instructions' => 'Đường dẫn khi click vào banner 2',
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
                'instructions' => 'Nhập tiêu đề khối nội dung cuối trang (Để trống nếu không muốn hiển thị)',
                'default_value' => '',
            ),
            array(
                'key' => 'field_shop_bottom_content',
                'label' => 'Nội dung chi tiết (Editor)',
                'name' => 'shop_bottom_content',
                'type' => 'wysiwyg',
                'instructions' => 'Nội dung giới thiệu chính sách, bảo hành cuối trang (Để trống nếu không muốn hiển thị)',
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
                'key' => 'field_shop_why_choose_note',
                'label' => 'Lưu ý hiển thị khối Vì sao chọn GoBike',
                'name' => '',
                'type' => 'message',
                'message' => '<em>Khối "Vì sao chọn GoBike" hiển thị bên dưới danh sách sản phẩm. <strong>Nếu để trống không nhập danh sách lý do bên dưới, khối này sẽ KHÔNG hiển thị trên website (Không dữ liệu mẫu).</strong></em>',
            ),
            array(
                'key' => 'field_shop_why_choose_title',
                'label' => 'Tiêu đề khối',
                'name' => 'shop_why_choose_title',
                'type' => 'text',
                'placeholder' => 'Vd: Vì sao chọn GoBike khi mua xe {brand}?',
                'instructions' => 'Nhập tiêu đề khối. Dùng {brand} để tự động thay bằng tên danh mục/thương hiệu (Ví dụ: Vì sao chọn GoBike khi mua xe {brand}?). Để trống sẽ tự nhận diện theo thương hiệu.',
                'default_value' => '',
            ),
            array(
                'key' => 'field_shop_why_choose_items',
                'label' => 'Danh sách lý do chọn GoBike',
                'name' => 'shop_why_choose_items',
                'type' => 'repeater',
                'instructions' => 'Thêm các mục lý do chọn GoBike (Chuẩn thiết kế gồm 4 thẻ: Chính hãng 100%, Tư vấn đúng nhu cầu, Lái thử tại showroom, Bảo trì hậu mãi). Nếu để trống, khối này sẽ ẩn.',
                'layout' => 'table',
                'button_label' => '+ Thêm lý do',
                'sub_fields' => array(
                    array(
                        'key' => 'field_shop_why_choose_icon',
                        'label' => 'Icon / Ảnh (PNG, SVG)',
                        'name' => 'icon',
                        'type' => 'image',
                        'instructions' => 'Tùy chọn. Để trống sẽ tự dùng icon SVG mặc định theo thứ tự',
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
                'key' => 'field_shop_showrooms_note',
                'label' => 'Lưu ý hiển thị khối Hệ thống Showroom',
                'name' => '',
                'type' => 'message',
                'message' => '<em>Khối "Hệ thống showroom" hiển thị danh sách showroom và hộp hotline tư vấn. <strong>Nếu để trống không nhập danh sách showroom bên dưới, khối này sẽ KHÔNG hiển thị trên website (Không dữ liệu mẫu).</strong></em>',
            ),
            array(
                'key' => 'field_shop_showroom_title',
                'label' => 'Tiêu đề khối Showroom',
                'name' => 'shop_showroom_title',
                'type' => 'text',
                'placeholder' => 'Vd: Hệ thống showroom GoBike',
                'instructions' => 'Nhập tiêu đề khối showroom (Để trống sẽ mặc định là "Hệ thống showroom GoBike").',
                'default_value' => '',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_showroom_subtitle',
                'label' => 'Mô tả phụ Showroom',
                'name' => 'shop_showroom_subtitle',
                'type' => 'text',
                'placeholder' => 'Vd: Đến trải nghiệm trực tiếp các mẫu xe {brand} tại cửa hàng gần bạn',
                'instructions' => 'Dòng mô tả bên dưới tiêu đề. Có thể dùng {brand} để tự động thay bằng tên danh mục/thương hiệu.',
                'default_value' => '',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_showroom_list',
                'label' => 'Danh sách Showroom cửa hàng',
                'name' => 'shop_showroom_list',
                'type' => 'repeater',
                'instructions' => 'Thêm các cửa hàng showroom (Chuẩn thiết kế gồm 3 showroom: Hà Nội, Phú Thọ, Bắc Ninh). Nếu để trống, khối này sẽ ẩn hoàn toàn.',
                'layout' => 'table',
                'button_label' => '+ Thêm showroom',
                'sub_fields' => array(
                    array(
                        'key' => 'field_shop_showroom_img',
                        'label' => 'Ảnh mặt tiền showroom',
                        'name' => 'image',
                        'type' => 'image',
                        'instructions' => 'Tải lên hình ảnh mặt tiền cửa hàng',
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
                'key' => 'field_shop_showroom_hotline_msg',
                'label' => 'Hộp Hotline tư vấn & Tìm đường (Thẻ bên phải showroom)',
                'name' => '',
                'type' => 'message',
                'message' => '<em>Cấu hình thẻ liên hệ thứ 4 nằm cạnh các showroom (Hotline tư vấn và Nút chỉ đường):</em>',
            ),
            array(
                'key' => 'field_shop_showroom_hotline_label',
                'label' => 'Nhãn Hotline',
                'name' => 'shop_showroom_hotline_label',
                'type' => 'text',
                'placeholder' => 'Hotline tư vấn',
                'instructions' => 'Để trống sẽ mặc định là "Hotline tư vấn"',
                'default_value' => '',
                'wrapper' => array('width' => '33'),
            ),
            array(
                'key' => 'field_shop_showroom_hotline',
                'label' => 'Số Hotline',
                'name' => 'shop_showroom_hotline',
                'type' => 'text',
                'placeholder' => '0944 988 699',
                'instructions' => 'Để trống sẽ mặc định là "0944 988 699"',
                'default_value' => '',
                'wrapper' => array('width' => '33'),
            ),
            array(
                'key' => 'field_shop_showroom_hotline_time',
                'label' => 'Thời gian tư vấn Hotline',
                'name' => 'shop_showroom_hotline_time',
                'type' => 'text',
                'placeholder' => '8:00 - 22:00 (Tất cả các ngày)',
                'instructions' => 'Để trống sẽ mặc định là "8:00 - 22:00 (Tất cả các ngày)"',
                'default_value' => '',
                'wrapper' => array('width' => '34'),
            ),
            array(
                'key' => 'field_shop_showroom_btn_text',
                'label' => 'Chữ trên nút chỉ đường',
                'name' => 'shop_showroom_btn_text',
                'type' => 'text',
                'placeholder' => 'Tìm đường đến cửa hàng ➔',
                'instructions' => 'Để trống sẽ mặc định là "Tìm đường đến cửa hàng ➔"',
                'default_value' => '',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_showroom_btn_link',
                'label' => 'Liên kết nút chỉ đường',
                'name' => 'shop_showroom_btn_link',
                'type' => 'text',
                'placeholder' => '/he-thong-cua-hang/',
                'instructions' => 'Đường dẫn đến trang hệ thống cửa hàng / Google Maps',
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
                'key' => 'field_shop_utility_banners_note',
                'label' => 'Lưu ý hiển thị khối 3 Banner tiện ích',
                'name' => '',
                'type' => 'message',
                'message' => '<em>Khối gồm 3 banner tiện ích (Video review xe, So sánh nhanh, Chọn đúng xe) nằm trực tiếp dưới phân trang sản phẩm. Nếu để trống danh sách, hệ thống sẽ tự động hiển thị 3 banner chuẩn thiết kế với hình ảnh sắc nét.</em>',
            ),
            array(
                'key' => 'field_shop_utility_banners_enable',
                'label' => 'Bật / Tắt khối 3 Banner tiện ích',
                'name' => 'shop_utility_banners_enable',
                'type' => 'true_false',
                'instructions' => 'Gạt bật để hiển thị khối 3 banner này trên trang danh mục sản phẩm.',
                'default_value' => 1,
                'ui' => 1,
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_shop_utility_banners_list',
                'label' => 'Danh sách Banner tiện ích tùy chỉnh',
                'name' => 'shop_utility_banners_list',
                'type' => 'repeater',
                'instructions' => 'Thêm các banner tiện ích (Khuyên dùng chuẩn 3 banner). Để trống sẽ tự động lấy 3 banner chuẩn mẫu GoBike.',
                'layout' => 'block',
                'button_label' => '+ Thêm banner tiện ích',
                'sub_fields' => array(
                    array(
                        'key' => 'field_shop_ub_img',
                        'label' => 'Ảnh nền banner',
                        'name' => 'image',
                        'type' => 'image',
                        'instructions' => 'Tải lên hình ảnh nền (tỷ lệ 3:2 hoặc 16:9)',
                        'return_format' => 'url',
                        'preview_size' => 'medium',
                        'library' => 'all',
                        'wrapper' => array('width' => '25'),
                    ),
                    array(
                        'key' => 'field_shop_ub_play',
                        'label' => 'Nút Play video?',
                        'name' => 'has_play_icon',
                        'type' => 'true_false',
                        'instructions' => 'Hiển thị nút tròn Play video ở góc trái banner',
                        'ui' => 1,
                        'default_value' => 0,
                        'wrapper' => array('width' => '15'),
                    ),
                    array(
                        'key' => 'field_shop_ub_title',
                        'label' => 'Tiêu đề banner',
                        'name' => 'title',
                        'type' => 'text',
                        'placeholder' => 'Vd: Video review {brand}',
                        'instructions' => 'Dùng {brand} hoặc {name} để tự động thay bằng tên danh mục/thương hiệu.',
                        'wrapper' => array('width' => '20'),
                    ),
                    array(
                        'key' => 'field_shop_ub_desc',
                        'label' => 'Mô tả phụ',
                        'name' => 'subtitle',
                        'type' => 'textarea',
                        'rows' => 2,
                        'placeholder' => "Trải nghiệm thực tế\nTừ người dùng thật",
                        'instructions' => 'Có thể nhấn Enter để xuống dòng',
                        'wrapper' => array('width' => '20'),
                    ),
                    array(
                        'key' => 'field_shop_ub_btn',
                        'label' => 'Chữ nút bấm',
                        'name' => 'btn_text',
                        'type' => 'text',
                        'placeholder' => 'Xem ngay ➔',
                        'default_value' => 'Xem ngay ➔',
                        'wrapper' => array('width' => '10'),
                    ),
                    array(
                        'key' => 'field_shop_ub_link',
                        'label' => 'Đường dẫn (Link)',
                        'name' => 'link',
                        'type' => 'text',
                        'placeholder' => '/video-review-gobike/',
                        'wrapper' => array('width' => '10'),
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
