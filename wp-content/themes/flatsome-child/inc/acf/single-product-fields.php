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
            // TAB 1: THÔNG SỐ KỸ THUẬT
            // =================================================================
            array(
                'key' => 'field_sp_tab_specs',
                'label' => '1. Thông số kỹ thuật',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_sp_highlight_specs',
                'label' => 'Thông số nổi bật (Hiển thị dưới ảnh sản phẩm)',
                'name' => 'sp_highlight_specs',
                'type' => 'repeater',
                'instructions' => 'Thêm các huy hiệu thông số nổi bật hiển thị ngay dưới ảnh lớn sản phẩm. Mỗi mục gồm Hình ảnh/Icon, Tiêu đề (vd: 90 – 120 km) và Mô tả ngắn (vd: Quãng đường trợ lực). Nếu để trống, khối này sẽ tự động ẩn hoàn toàn.',
                'layout' => 'table',
                'button_label' => '+ Thêm thông số nổi bật',
                'sub_fields' => array(
                    array(
                        'key' => 'field_sp_highlight_spec_image',
                        'label' => 'Hình ảnh / Icon',
                        'name' => 'image',
                        'type' => 'image',
                        'instructions' => 'Tải lên icon/ảnh biểu tượng (PNG, SVG hoặc JPG)',
                        'return_format' => 'url',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                        'wrapper' => array('width' => '30'),
                    ),
                    array(
                        'key' => 'field_sp_highlight_spec_title',
                        'label' => 'Tiêu đề thông số *',
                        'name' => 'title',
                        'type' => 'text',
                        'placeholder' => 'Vd: 90 – 120 km, 48V1000W...',
                        'required' => 1,
                        'wrapper' => array('width' => '35'),
                    ),
                    array(
                        'key' => 'field_sp_highlight_spec_desc',
                        'label' => 'Mô tả ngắn',
                        'name' => 'desc',
                        'type' => 'text',
                        'placeholder' => 'Vd: Quãng đường trợ lực, Động cơ...',
                        'wrapper' => array('width' => '35'),
                    ),
                ),
            ),
            array(
                'key' => 'field_sp_specs_filter_note',
                'label' => 'Các thông số chính (Dùng cho Bảng thông số & Bộ lọc)',
                'name' => '',
                'type' => 'message',
                'message' => '<em>Các trường bên dưới là các thông số cốt lõi, được dùng cho bảng thông số kỹ thuật và bộ lọc tìm kiếm.</em>',
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
                'key' => 'field_sp_weight',
                'label' => 'Trọng lượng xe',
                'name' => 'trong_luong',
                'type' => 'text',
                'instructions' => 'Vd: 28 kg (kể cả pin)',
            ),
            array(
                'key' => 'field_sp_warranty',
                'label' => 'Thời gian bảo hành',
                'name' => 'thoi_gian_bao_hanh',
                'type' => 'text',
                'instructions' => 'Vd: 24 tháng Khung, 12 tháng Pin & Động cơ',
            ),
            array(
                'key' => 'field_sp_specs_custom_html',
                'label' => 'Bảng thông số kỹ thuật tùy chỉnh (HTML / Soạn thảo)',
                'name' => 'sp_specs_custom_html',
                'type' => 'wysiwyg',
                'instructions' => 'Dán mã HTML bảng thông số hoặc soạn thảo trực tiếp vào đây. <strong>Lưu ý:</strong> Nếu xóa nhầm hoặc để trống ô này, hệ thống sẽ <em>tự động hiển thị Bảng thông số chuẩn</em> từ các trường phía trên mà không sợ mất dữ liệu.',
                'tabs' => 'all',
                'toolbar' => 'full',
                'media_upload' => 1,
                'delay' => 0,
            ),
            array(
                'key' => 'field_sp_specs_sample_template',
                'label' => 'Mẫu HTML Bảng thông số (Dùng để khôi phục nhanh)',
                'name' => '',
                'type' => 'message',
                'message' => '<details style="background:#f8fafc;padding:12px 16px;border:1px solid #cbd5e1;border-radius:6px;cursor:pointer;"><summary style="font-weight:600;color:#044b25;">📋 Nhấp vào đây để xem và sao chép Mẫu HTML Bảng thông số kỹ thuật</summary><div style="margin-top:10px;"><p style="margin:0 0 6px;font-size:12px;color:#475569;">Bấm vào khung bên dưới để tự động chọn toàn bộ, nhấn <strong>Ctrl + C</strong> để sao chép rồi dán vào ô soạn thảo ở trên:</p><textarea readonly style="width:100%;height:160px;font-family:monospace;font-size:12px;background:#ffffff;border:1px solid #cbd5e1;padding:8px;border-radius:4px;" onclick="this.select();">&lt;table class=&quot;gobike-specs-custom-table&quot; style=&quot;width:100%;border-collapse:collapse;&quot;&gt;
  &lt;tbody&gt;
    &lt;tr&gt;&lt;td style=&quot;padding:10px;border-bottom:1px solid #e2e8f0;font-weight:600;width:35%;&quot;&gt;Động cơ&lt;/td&gt;&lt;td style=&quot;padding:10px;border-bottom:1px solid #e2e8f0;&quot;&gt;500W – Động cơ không chổi than&lt;/td&gt;&lt;/tr&gt;
    &lt;tr&gt;&lt;td style=&quot;padding:10px;border-bottom:1px solid #e2e8f0;font-weight:600;&quot;&gt;Dung lượng Pin&lt;/td&gt;&lt;td style=&quot;padding:10px;border-bottom:1px solid #e2e8f0;&quot;&gt;48V 15Ah Pin Lithium tháo rời&lt;/td&gt;&lt;/tr&gt;
    &lt;tr&gt;&lt;td style=&quot;padding:10px;border-bottom:1px solid #e2e8f0;font-weight:600;&quot;&gt;Quãng đường trợ lực&lt;/td&gt;&lt;td style=&quot;padding:10px;border-bottom:1px solid #e2e8f0;&quot;&gt;80 – 120 km&lt;/td&gt;&lt;/tr&gt;
    &lt;tr&gt;&lt;td style=&quot;padding:10px;border-bottom:1px solid #e2e8f0;font-weight:600;&quot;&gt;Tốc độ tối đa&lt;/td&gt;&lt;td style=&quot;padding:10px;border-bottom:1px solid #e2e8f0;&quot;&gt;35 km/h&lt;/td&gt;&lt;/tr&gt;
    &lt;tr&gt;&lt;td style=&quot;padding:10px;border-bottom:1px solid #e2e8f0;font-weight:600;&quot;&gt;Trọng lượng xe&lt;/td&gt;&lt;td style=&quot;padding:10px;border-bottom:1px solid #e2e8f0;&quot;&gt;28 kg (kể cả pin)&lt;/td&gt;&lt;/tr&gt;
    &lt;tr&gt;&lt;td style=&quot;padding:10px;border-bottom:1px solid #e2e8f0;font-weight:600;&quot;&gt;Thời gian bảo hành&lt;/td&gt;&lt;td style=&quot;padding:10px;border-bottom:1px solid #e2e8f0;&quot;&gt;24 tháng Khung, 12 tháng Pin &amp; Động cơ&lt;/td&gt;&lt;/tr&gt;
  &lt;/tbody&gt;
&lt;/table&gt;</textarea></div></details>',
            ),

            // =================================================================
            // TAB 2: HÌNH ẢNH & VIDEO CHI TIẾT
            // =================================================================
            array(
                'key' => 'field_sp_tab_media',
                'label' => '2. Hình ảnh & Video',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_sp_media_photos_title',
                'label' => 'Tiêu đề lưới ảnh chi tiết',
                'name' => 'sp_media_photos_title',
                'type' => 'text',
                'placeholder' => 'HÌNH ẢNH CHI TIẾT BỘ PHẬN',
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
                'placeholder' => 'VIDEO TRẢI NGHIỆM THỰC TẾ',
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
            // TAB 3: HỎI ĐÁP (FAQ)
            // =================================================================
            array(
                'key' => 'field_sp_tab_faq',
                'label' => '3. Hỏi đáp (FAQ)',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_sp_faq_slogan',
                'label' => 'Dòng mô tả phụ Hỏi đáp',
                'name' => 'sp_faq_slogan',
                'type' => 'text',
                'placeholder' => 'Những câu hỏi thường gặp về sản phẩm',
            ),
            array(
                'key' => 'field_sp_faq_zalo_link',
                'label' => 'Link Đặt câu hỏi (Zalo / Hotline)',
                'name' => 'sp_faq_zalo_link',
                'type' => 'text',
                'placeholder' => 'https://zalo.me/0944988699',
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

            // =================================================================
            // TAB 4: CAM KẾT QUYỀN LỢI (TRUST BADGES)
            // =================================================================
            array(
                'key' => 'field_sp_tab_trust_badges',
                'label' => '4. Cam kết (Trust Badges)',
                'name' => '',
                'type' => 'tab',
                'placement' => 'top',
            ),
            array(
                'key' => 'field_sp_trust_badges',
                'label' => 'Danh sách Cam kết quyền lợi (Trust Badges)',
                'name' => 'sp_trust_badges',
                'type' => 'repeater',
                'instructions' => 'Thêm các huy hiệu cam kết hiển thị ngay bên dưới nút Mua hàng (để trống sẽ sử dụng cam kết chung từ Theme Settings; nếu cả 2 nơi đều trống thì khối này sẽ tự động ẩn).',
                'layout' => 'table',
                'button_label' => '+ Thêm cam kết',
                'sub_fields' => array(
                    array(
                        'key' => 'field_sp_trust_badge_image',
                        'label' => 'Hình ảnh / Biểu tượng',
                        'name' => 'image',
                        'type' => 'image',
                        'instructions' => 'Tải lên icon/ảnh biểu tượng (PNG, SVG hoặc JPG vuông)',
                        'return_format' => 'url',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                        'wrapper' => array('width' => '35'),
                    ),
                    array(
                        'key' => 'field_sp_trust_badge_title',
                        'label' => 'Tiêu đề cam kết',
                        'name' => 'title',
                        'type' => 'text',
                        'instructions' => 'Ví dụ: Bảo hành chính hãng, Giao hàng toàn quốc...',
                        'placeholder' => 'Bảo hành chính hãng',
                        'required' => 1,
                        'wrapper' => array('width' => '65'),
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
        'description' => 'Bộ trường dữ liệu quản trị chi tiết cho các tab sản phẩm GoBike',
    ));

    // Đăng ký cấu hình Cam kết chung trong Theme Settings
    acf_add_local_field_group(array(
        'key' => 'group_gobike_global_trust_badges',
        'title' => 'Cài đặt Cam kết Sản phẩm chung (Trust Badges)',
        'fields' => array(
            array(
                'key' => 'field_sp_global_trust_badges',
                'label' => 'Cam kết Sản phẩm mặc định (Áp dụng cho tất cả sản phẩm)',
                'name' => 'sp_trust_badges',
                'type' => 'repeater',
                'instructions' => 'Cấu hình các huy hiệu cam kết quyền lợi hiển thị chung cho tất cả các sản phẩm (nếu sản phẩm nào có cấu hình riêng ở Tab 5 thì sẽ ưu tiên theo sản phẩm đó).',
                'layout' => 'table',
                'button_label' => '+ Thêm cam kết chung',
                'sub_fields' => array(
                    array(
                        'key' => 'field_sp_global_trust_badge_image',
                        'label' => 'Hình ảnh / Biểu tượng',
                        'name' => 'image',
                        'type' => 'image',
                        'instructions' => 'Tải lên icon/ảnh biểu tượng',
                        'return_format' => 'url',
                        'preview_size' => 'thumbnail',
                        'library' => 'all',
                        'wrapper' => array('width' => '35'),
                    ),
                    array(
                        'key' => 'field_sp_global_trust_badge_title',
                        'label' => 'Tiêu đề cam kết',
                        'name' => 'title',
                        'type' => 'text',
                        'placeholder' => 'Bảo hành chính hãng',
                        'required' => 1,
                        'wrapper' => array('width' => '65'),
                    ),
                ),
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
        'menu_order' => 20,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
    ));
}
