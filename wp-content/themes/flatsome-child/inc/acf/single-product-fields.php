<?php
/**
 * ACF / SCF Field Group cho Trang Chi Tiết Sản Phẩm (Single Product)
 * Bao gồm đầy đủ 5 Tab nội dung nhập liệu:
 * 1. Mô tả sản phẩm & Điểm nổi bật
 * 2. Thông số kỹ thuật chi tiết
 * 3. Hình ảnh chi tiết bộ phận & Video review thực tế
 * 4. Đánh giá khách hàng & Điểm số nổi bật
 * 5. Hỏi đáp thường gặp (FAQ)
 *
 * @package Flatsome-Child
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('acf/init', 'gobike_register_single_product_acf_fields');
function gobike_register_single_product_acf_fields()
{
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_gobike_single_product_details',
        'title' => 'Cấu hình Chi Tiết Sản Phẩm (GoBike Single Product)',
        'fields' => array(

            // =================================================================
            // TAB 1: MÔ TẢ SẢN PHẨM & ĐIỂM NỔI BẬT
            // =================================================================
            array(
                'key' => 'field_sp_tab_desc',
                'label' => '1. Mô tả & Điểm nổi bật',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_sp_desc_subtitle',
                'label' => 'Tiêu đề phụ mô tả',
                'name' => 'sp_desc_subtitle',
                'type' => 'text',
                'instructions' => 'Dòng tiêu đề lớn phía trên nội dung mô tả (vd: Khám phá thế giới theo cách của bạn)',
                'default_value' => 'Khám phá thế giới theo cách của bạn',
            ),
            array(
                'key' => 'field_sp_features',
                'label' => '4 Điểm nổi bật (Icon box)',
                'name' => 'sp_features',
                'type' => 'repeater',
                'instructions' => 'Thêm các đặc điểm nổi bật bên dưới nội dung mô tả (khuyên dùng 4 mục)',
                'layout' => 'table',
                'button_label' => 'Thêm điểm nổi bật',
                'sub_fields' => array(
                    array(
                        'key' => 'field_sp_feature_icon_type',
                        'label' => 'Biểu tượng',
                        'name' => 'icon_type',
                        'type' => 'select',
                        'choices' => array(
                            'terrain'  => 'Địa hình (Khối lập phương)',
                            'assist'   => 'Trợ lực (Quả địa cầu / Bánh xe)',
                            'design'   => 'Thiết kế (Ngôi sao / Vương miện)',
                            'eco'      => 'Thân thiện môi trường (Chiếc lá)',
                            'battery'  => 'Pin điện / Sạc nhanh',
                            'motor'    => 'Động cơ mạnh mẽ',
                            'brake'    => 'An toàn / Phanh đĩa',
                        ),
                        'default_value' => 'terrain',
                    ),
                    array(
                        'key' => 'field_sp_feature_title',
                        'label' => 'Tiêu đề',
                        'name' => 'title',
                        'type' => 'text',
                        'instructions' => 'Vd: Chinh phục mọi địa hình',
                    ),
                    array(
                        'key' => 'field_sp_feature_desc',
                        'label' => 'Mô tả ngắn',
                        'name' => 'desc',
                        'type' => 'textarea',
                        'rows' => 2,
                        'instructions' => 'Vd: Vận hành mạnh mẽ, an tâm trên cả đường phố và đồi dốc.',
                    ),
                ),
            ),
            array(
                'key' => 'field_sp_lifestyle_hero_img',
                'label' => 'Ảnh Lifestyle Banner lớn (Cột phải)',
                'name' => 'sp_lifestyle_hero_img',
                'type' => 'image',
                'instructions' => 'Ảnh chụp xe thực tế phong cách sống (kích thước đề xuất: 800x500px)',
                'return_format' => 'url',
                'preview_size' => 'medium',
            ),
            array(
                'key' => 'field_sp_lifestyle_hero_quote',
                'label' => 'Câu trích dẫn chữ nghệ thuật',
                'name' => 'sp_lifestyle_hero_quote',
                'type' => 'text',
                'instructions' => 'Chữ viết tay đè lên ảnh lớn (vd: "Đi xa hơn mỗi ngày")',
                'default_value' => '"Đi xa hơn mỗi ngày"',
            ),
            array(
                'key' => 'field_sp_lifestyle_cards',
                'label' => 'Các thẻ giới thiệu phụ (Cột phải)',
                'name' => 'sp_lifestyle_cards',
                'type' => 'repeater',
                'instructions' => 'Các thẻ nhỏ giới thiệu sức mạnh, khung xe... hiển thị bên dưới banner lớn (khuyên dùng 2 thẻ)',
                'layout' => 'block',
                'button_label' => 'Thêm thẻ giới thiệu',
                'sub_fields' => array(
                    array(
                        'key' => 'field_sp_card_img',
                        'label' => 'Hình ảnh',
                        'name' => 'image',
                        'type' => 'image',
                        'return_format' => 'url',
                        'preview_size' => 'thumbnail',
                    ),
                    array(
                        'key' => 'field_sp_card_title',
                        'label' => 'Tiêu đề thẻ',
                        'name' => 'title',
                        'type' => 'text',
                        'instructions' => 'Vd: Sức mạnh trên mọi cung đường',
                    ),
                    array(
                        'key' => 'field_sp_card_desc',
                        'label' => 'Mô tả thẻ',
                        'name' => 'desc',
                        'type' => 'textarea',
                        'rows' => 2,
                    ),
                ),
            ),

            // =================================================================
            // TAB 2: THÔNG SỐ KỸ THUẬT
            // =================================================================
            array(
                'key' => 'field_sp_tab_specs',
                'label' => '2. Thông số kỹ thuật',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_sp_specs_custom_html',
                'label' => 'Bảng thông số kỹ thuật tùy chỉnh (HTML / Soạn thảo)',
                'name' => 'sp_specs_custom_html',
                'type' => 'wysiwyg',
                'instructions' => 'Dán mã HTML bảng thông số hoặc soạn thảo trực tiếp vào đây. Nếu nhập ô này, trang web sẽ ưu tiên hiển thị bảng HTML này. Nếu để trống, hệ thống sẽ tự động hiển thị bảng thông số chuẩn từ các trường bên dưới.',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
            array(
                'key' => 'field_sp_specs_filter_note',
                'label' => 'Các thông số có cấu trúc (Dùng cho Bộ lọc Sidebar & Hiển thị tóm tắt)',
                'name' => '',
                'type' => 'message',
                'message' => '<em>Các trường bên dưới được dùng cho tính năng lọc sản phẩm theo thông số và hiển thị nhanh ở đầu trang hoặc bảng thông số tự động.</em>',
            ),
            array(
                'key' => 'field_sp_brand',
                'label' => 'Thương hiệu',
                'name' => 'thuong_hieu',
                'type' => 'text',
                'instructions' => 'Để trống sẽ tự lấy theo thuộc tính sản phẩm pa_thuong-hieu (vd: SAMEBIKE, PHOENIX, ADO)',
            ),
            array(
                'key' => 'field_sp_motor',
                'label' => 'Động cơ',
                'name' => 'dong_co',
                'type' => 'text',
                'instructions' => 'Vd: 500W – Động cơ không chổi than mạnh mẽ',
            ),
            array(
                'key' => 'field_sp_battery',
                'label' => 'Dung lượng Pin',
                'name' => 'dung_luong_pin',
                'type' => 'text',
                'instructions' => 'Vd: 48V 15Ah Pin Lithium tháo rời',
            ),
            array(
                'key' => 'field_sp_range',
                'label' => 'Quãng đường trợ lực',
                'name' => 'quang_duong',
                'type' => 'text',
                'instructions' => 'Vd: 80 – 120 km (chế độ trợ lực)',
            ),
            array(
                'key' => 'field_sp_speed',
                'label' => 'Tốc độ tối đa',
                'name' => 'toc_do_toi_da',
                'type' => 'text',
                'instructions' => 'Vd: 35 km/h',
            ),
            array(
                'key' => 'field_sp_frame',
                'label' => 'Khung xe',
                'name' => 'chat_lieu_khung',
                'type' => 'text',
                'instructions' => 'Vd: Hợp kim nhôm 6061 siêu nhẹ, có khớp gập',
            ),
            array(
                'key' => 'field_sp_brake',
                'label' => 'Hệ thống phanh',
                'name' => 'he_thong_phanh',
                'type' => 'text',
                'instructions' => 'Vd: Phanh đĩa dầu thủy lực trước và sau',
            ),
            array(
                'key' => 'field_sp_suspension',
                'label' => 'Giảm xóc',
                'name' => 'giam_xoc',
                'type' => 'text',
                'instructions' => 'Vd: Phuộc nhún trước khóa hành trình + nhún lò xo sau',
            ),
            array(
                'key' => 'field_sp_tire',
                'label' => 'Kích thước lốp xe',
                'name' => 'kich_thuoc_lop',
                'type' => 'text',
                'instructions' => 'Vd: 20 x 4.0 inch lốp béo địa hình CST',
            ),
            array(
                'key' => 'field_sp_weight',
                'label' => 'Trọng lượng xe',
                'name' => 'trong_luong',
                'type' => 'text',
                'instructions' => 'Vd: 28 kg (kể cả pin)',
            ),
            array(
                'key' => 'field_sp_payload',
                'label' => 'Tải trọng tối đa',
                'name' => 'tai_trong_toi_da',
                'type' => 'text',
                'instructions' => 'Vd: 150 kg',
            ),
            array(
                'key' => 'field_sp_dimensions',
                'label' => 'Kích thước (DxRxC)',
                'name' => 'kich_thuoc_xe',
                'type' => 'text',
                'instructions' => 'Vd: 1720 x 650 x 1100 mm (Gập gọn: 980 x 480 x 750 mm)',
            ),
            array(
                'key' => 'field_sp_warranty',
                'label' => 'Thời gian bảo hành',
                'name' => 'thoi_gian_bao_hanh',
                'type' => 'text',
                'instructions' => 'Vd: 24 tháng Khung, 12 tháng Pin & Động cơ',
            ),
            array(
                'key' => 'field_sp_extra_specs',
                'label' => 'Thông số kỹ thuật bổ sung (nếu có)',
                'name' => 'thong_so_bo_sung',
                'type' => 'repeater',
                'instructions' => 'Thêm bất kỳ thông số nào khác vào bảng thông số (vd: Bộ truyền động, Màn hình hiển thị, Đèn chiếu sáng...)',
                'layout' => 'table',
                'button_label' => 'Thêm thông số khác',
                'sub_fields' => array(
                    array(
                        'key' => 'field_sp_extra_spec_name',
                        'label' => 'Tên thông số',
                        'name' => 'spec_name',
                        'type' => 'text',
                        'instructions' => 'Vd: Bộ truyền động',
                    ),
                    array(
                        'key' => 'field_sp_extra_spec_val',
                        'label' => 'Giá trị thông số',
                        'name' => 'spec_value',
                        'type' => 'text',
                        'instructions' => 'Vd: Shimano 7 tốc độ (Nhật Bản)',
                    ),
                ),
            ),

            // =================================================================
            // TAB 3: HÌNH ẢNH & VIDEO CHI TIẾT
            // =================================================================
            array(
                'key' => 'field_sp_tab_media',
                'label' => '3. Hình ảnh & Video',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_sp_media_photos_title',
                'label' => 'Tiêu đề lưới ảnh chi tiết',
                'name' => 'sp_media_photos_title',
                'type' => 'text',
                'default_value' => 'HÌNH ẢNH CHI TIẾT BỘ PHẬN',
            ),
            array(
                'key' => 'field_sp_detail_photos',
                'label' => 'Bộ sưu tập ảnh chi tiết bộ phận (Slider + Phóng to khi bấm)',
                'name' => 'sp_detail_photos',
                'type' => 'repeater',
                'instructions' => 'Tải lên ảnh cận cảnh chi tiết (khung, pin, động cơ, phanh, giảm xóc, líp...) kèm chú thích. Giao diện hiển thị slide trượt mượt mà và hỗ trợ bấm phóng to.',
                'layout' => 'table',
                'button_label' => 'Thêm ảnh chi tiết',
                'sub_fields' => array(
                    array(
                        'key' => 'field_sp_photo_img',
                        'label' => 'Hình ảnh',
                        'name' => 'image',
                        'type' => 'image',
                        'return_format' => 'url',
                        'preview_size' => 'thumbnail',
                    ),
                    array(
                        'key' => 'field_sp_photo_caption',
                        'label' => 'Chú thích bộ phận',
                        'name' => 'caption',
                        'type' => 'text',
                        'instructions' => 'Vd: Khung hợp kim nhôm, Pin Lithium 48V, Động cơ 500W...',
                    ),
                ),
            ),
            array(
                'key' => 'field_sp_video_title',
                'label' => 'Tiêu đề khu vực Video Review',
                'name' => 'sp_video_title',
                'type' => 'text',
                'default_value' => 'VIDEO TRẢI NGHIỆM THỰC TẾ',
            ),
            array(
                'key' => 'field_sp_detail_videos',
                'label' => 'Danh sách Video Review (Dạng Slide trượt)',
                'name' => 'sp_detail_videos',
                'type' => 'repeater',
                'instructions' => 'Thêm các video review / shorts trải nghiệm thực tế xe để hiển thị thành Slide trượt chuyên nghiệp.',
                'layout' => 'table',
                'button_label' => 'Thêm Video',
                'sub_fields' => array(
                    array(
                        'key' => 'field_sp_v_url',
                        'label' => 'Link YouTube / Shorts',
                        'name' => 'video_url',
                        'type' => 'text',
                        'instructions' => 'https://www.youtube.com/watch?v=... hoặc https://youtu.be/...',
                    ),
                    array(
                        'key' => 'field_sp_v_title',
                        'label' => 'Tiêu đề video',
                        'name' => 'video_title',
                        'type' => 'text',
                        'instructions' => 'Vd: Trải nghiệm thực tế khi đi phố và leo dốc',
                    ),
                    array(
                        'key' => 'field_sp_v_thumb',
                        'label' => 'Ảnh bìa tùy chỉnh (để trống tự lấy từ YouTube)',
                        'name' => 'video_thumb',
                        'type' => 'image',
                        'return_format' => 'url',
                        'preview_size' => 'thumbnail',
                    ),
                ),
            ),
            array(
                'key' => 'field_sp_video_url',
                'label' => 'Hoặc nhập nhanh 1 Link YouTube đơn lẻ',
                'name' => 'sp_video_url',
                'type' => 'text',
                'instructions' => 'Dành cho trường hợp chỉ có 1 video duy nhất và không muốn dùng repeater ở trên.',
            ),
            array(
                'key' => 'field_sp_linked_reviews',
                'label' => 'Hoặc chọn các bài viết từ CPT "Video Review"',
                'name' => 'product_linked_reviews',
                'type' => 'relationship',
                'post_type' => array('video_review'),
                'instructions' => 'Chọn một hoặc nhiều bài video review từ CPT Video Review để tự động đưa vào slide video của sản phẩm.',
                'filters' => array('search'),
                'return_format' => 'id',
            ),

            // =================================================================
            // TAB 4: HỎI ĐÁP (FAQ)
            // =================================================================
            array(
                'key' => 'field_sp_tab_faq',
                'label' => '4. Hỏi đáp (FAQ)',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_sp_faq_slogan',
                'label' => 'Dòng mô tả phụ Hỏi đáp',
                'name' => 'sp_faq_slogan',
                'type' => 'text',
                'default_value' => 'Những câu hỏi thường gặp về sản phẩm',
            ),
            array(
                'key' => 'field_sp_faq_zalo_link',
                'label' => 'Link Đặt câu hỏi (Zalo / Hotline)',
                'name' => 'sp_faq_zalo_link',
                'type' => 'text',
                'default_value' => 'https://zalo.me/0944988699',
            ),
            array(
                'key' => 'field_sp_faqs',
                'label' => 'Danh sách câu hỏi thường gặp',
                'name' => 'sp_faqs',
                'type' => 'repeater',
                'instructions' => 'Thêm các câu hỏi & câu trả lời (giao diện sẽ tự động chia đều 2 cột theo thiết kế)',
                'layout' => 'row',
                'button_label' => 'Thêm câu hỏi',
                'sub_fields' => array(
                    array(
                        'key' => 'field_sp_faq_q',
                        'label' => 'Câu hỏi',
                        'name' => 'question',
                        'type' => 'text',
                        'instructions' => 'Vd: Thời gian sạc đầy pin là bao lâu?',
                    ),
                    array(
                        'key' => 'field_sp_faq_a',
                        'label' => 'Câu trả lời',
                        'name' => 'answer',
                        'type' => 'textarea',
                        'rows' => 3,
                        'instructions' => 'Vd: Thời gian sạc đầy pin Lithium dao động từ 4 – 6 giờ...',
                    ),
                ),
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
        'menu_order' => 10,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => 'Bộ trường dữ liệu quản trị chi tiết cho 5 tab sản phẩm GoBike',
    ));
}
