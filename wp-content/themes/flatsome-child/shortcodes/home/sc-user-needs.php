<?php
/**
 * Shortcode: Khối Phân Loại Theo Nhu Cầu Sử Dụng (GoBike User Needs)
 * Dữ liệu được quản lý động qua ACF tại Trang Chủ.
 * Cú pháp dùng trong Flatsome UX Builder: [gobike_user_needs]
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. Đăng ký nhóm trường ACF cho Trang Chủ
 */
add_action('acf/init', 'gobike_register_user_needs_acf_fields');
function gobike_register_user_needs_acf_fields()
{
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    $front_page_id = get_option('page_on_front');

    $location = array(
        array(
            array(
                'param'    => 'page_type',
                'operator' => '==',
                'value'    => 'front_page',
            ),
        ),
    );

    if (!empty($front_page_id)) {
        $location[] = array(
            array(
                'param'    => 'page',
                'operator' => '==',
                'value'    => strval($front_page_id),
            ),
        );
    }

    acf_add_local_field_group(array(
        'key'                   => 'group_gobike_user_needs',
        'title'                 => '[Trang chủ] Danh mục xe theo Nhu cầu sử dụng',
        'fields'                => array(
            array(
                'key'          => 'field_home_user_needs',
                'label'        => 'Danh sách nhu cầu sử dụng',
                'name'         => 'home_user_needs',
                'type'         => 'repeater',
                'instructions' => 'Thêm các khối nhu cầu sử dụng hiển thị trên trang chủ (khuyên dùng 5 khối). Hiển thị 5 cột trên Máy tính và dạng Slide trượt trên Mobile.',
                'button_label' => '+ Thêm nhu cầu',
                'layout'       => 'table',
                'sub_fields'   => array(
                    array(
                        'key'           => 'field_user_need_image',
                        'label'         => 'Ảnh / Icon đại diện',
                        'name'          => 'image',
                        'type'          => 'image',
                        'instructions'  => 'Chọn ảnh vuông hoặc icon',
                        'return_format' => 'url',
                        'preview_size'  => 'thumbnail',
                        'library'       => 'all',
                        'wrapper'       => array('width' => '25'),
                    ),
                    array(
                        'key'          => 'field_user_need_title',
                        'label'        => 'Tiêu đề',
                        'name'         => 'title',
                        'type'         => 'text',
                        'instructions' => 'Ví dụ: Đi làm hằng ngày, Học sinh - Sinh viên...',
                        'placeholder'  => 'Tiêu đề nhu cầu...',
                        'required'     => 1,
                        'wrapper'      => array('width' => '30'),
                    ),
                    array(
                        'key'          => 'field_user_need_desc',
                        'label'        => 'Mô tả phụ',
                        'name'         => 'desc',
                        'type'         => 'text',
                        'instructions' => 'Ví dụ: Gọn nhẹ, linh hoạt...',
                        'placeholder'  => 'Mô tả ngắn...',
                        'wrapper'      => array('width' => '25'),
                    ),
                    array(
                        'key'          => 'field_user_need_link',
                        'label'        => 'Đường dẫn liên kết',
                        'name'         => 'link',
                        'type'         => 'text',
                        'instructions' => 'Link danh mục sản phẩm khi click vào',
                        'placeholder'  => 'https://... hoặc /danh-muc/...',
                        'wrapper'      => array('width' => '20'),
                    ),
                ),
            ),
        ),
        'location'              => $location,
        'menu_order'            => 5,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
    ));
}

/**
 * 2. Render Shortcode [gobike_user_needs]
 */
function gobike_render_user_needs_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'class' => '',
    ), $atts, 'gobike_user_needs');

    $front_page_id = get_option('page_on_front');
    $rows = false;

    if (function_exists('get_field')) {
        if (!empty($front_page_id)) {
            $rows = get_field('home_user_needs', $front_page_id);
        }
        if (empty($rows)) {
            $rows = get_field('home_user_needs');
        }
        if (empty($rows)) {
            $rows = get_field('home_user_needs', 'option');
        }
    }

    // Bỏ hết dữ liệu mẫu theo yêu cầu: nếu chưa có dữ liệu thì không hiển thị
    if (empty($rows) || !is_array($rows)) {
        if (current_user_can('manage_options') && is_user_logged_in()) {
            return '<div style="padding:15px; background:#fff; border:1px dashed #f59e0b; border-radius:8px; margin:15px 0; text-align:center; color:#b45309; font-size:13px;">'
                . '<strong>[GoBike User Needs]</strong> Chưa có dữ liệu. Vui lòng vào <em>Trang quản trị > Trang > Chỉnh sửa Trang Chủ</em> và nhập dữ liệu trong mục <strong>[Trang chủ] Danh mục xe theo Nhu cầu sử dụng</strong>.'
                . '</div>';
        }
        return '';
    }

    ob_start();
    ?>
    <div class="gobike-user-needs-container <?php echo esc_attr($atts['class']); ?>">
        <div class="gobike-needs-track">
            <?php foreach ($rows as $item):
                $title = !empty($item['title']) ? trim($item['title']) : '';
                if (empty($title)) {
                    continue;
                }
                $desc = !empty($item['desc']) ? trim($item['desc']) : '';
                $link = !empty($item['link']) ? trim($item['link']) : '#';
                $img  = !empty($item['image']) ? $item['image'] : '';

                // Xử lý nếu ACF trả về ID hoặc Array
                if (is_array($img) && isset($img['url'])) {
                    $img = $img['url'];
                } elseif (is_numeric($img)) {
                    $img = wp_get_attachment_image_url($img, 'thumbnail');
                }
                ?>
                <a href="<?php echo esc_url($link); ?>" class="gobike-need-card">
                    <?php if (!empty($img)): ?>
                        <div class="gobike-need-thumb">
                            <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" />
                        </div>
                    <?php endif; ?>
                    <div class="gobike-need-info">
                        <h4 class="gobike-need-title"><?php echo esc_html($title); ?></h4>
                        <?php if (!empty($desc)): ?>
                            <span class="gobike-need-desc"><?php echo esc_html($desc); ?></span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('gobike_user_needs', 'gobike_render_user_needs_shortcode');
