<?php
/**
 * Shortcode: Khối Phân Loại Theo Nhu Cầu Sử Dụng (GoBike User Needs)
 * Dữ liệu được quản lý động qua ACF tại Trang Chủ.
 * - Desktop: 5 card dàn ngang 1 hàng (chuẩn Ảnh 1)
 * - Mobile & Tablet: Swiper Slider card dọc có ảnh trên, vòm icon + chữ dưới, dots (chuẩn Ảnh 2)
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
                'key'           => 'field_needs_section_title_mobile',
                'label'         => 'Tiêu đề khối trên Mobile',
                'name'          => 'needs_section_title_mobile',
                'type'          => 'text',
                'default_value' => 'CHỌN XE THEO NHU CẦU',
                'instructions'  => 'Tiêu đề hiển thị ở đầu khối khi xem trên điện thoại (mặc định: CHỌN XE THEO NHU CẦU)',
                'wrapper'       => array('width' => '50'),
            ),
            array(
                'key'           => 'field_needs_section_viewall_link',
                'label'         => 'Link "Xem tất cả" trên Mobile',
                'name'          => 'needs_section_viewall_link',
                'type'          => 'text',
                'default_value' => '/danh-muc-san-pham/xe-dap-tro-luc-dien/',
                'instructions'  => 'Đường dẫn khi click "Xem tất cả →"',
                'wrapper'       => array('width' => '50'),
            ),
            array(
                'key'          => 'field_home_user_needs',
                'label'        => 'Danh sách nhu cầu sử dụng',
                'name'         => 'home_user_needs',
                'type'         => 'repeater',
                'instructions' => 'Thêm các nhu cầu xe (Desktop hiển thị 5 card ngang, Mobile tự động thành Swiper slide dọc).',
                'button_label' => '+ Thêm nhu cầu',
                'layout'       => 'row',
                'sub_fields'   => array(
                    array(
                        'key'          => 'field_user_need_title',
                        'label'        => 'Tiêu đề',
                        'name'         => 'title',
                        'type'         => 'text',
                        'instructions' => 'Ví dụ: Đi làm hàng ngày, Học sinh - Sinh viên, Thể thao khám phá...',
                        'placeholder'  => 'Đi làm hàng ngày...',
                        'required'     => 1,
                        'wrapper'      => array('width' => '30'),
                    ),
                    array(
                        'key'          => 'field_user_need_desc',
                        'label'        => 'Mô tả phụ (Desktop)',
                        'name'         => 'desc',
                        'type'         => 'text',
                        'instructions' => 'Ví dụ: Gọn nhẹ, linh hoạt...',
                        'placeholder'  => 'Mô tả ngắn...',
                        'wrapper'      => array('width' => '30'),
                    ),
                    array(
                        'key'          => 'field_user_need_link',
                        'label'        => 'Đường dẫn liên kết',
                        'name'         => 'link',
                        'type'         => 'text',
                        'instructions' => 'Link danh mục sản phẩm khi click vào',
                        'placeholder'  => 'https://... hoặc /danh-muc/...',
                        'wrapper'      => array('width' => '40'),
                    ),
                    array(
                        'key'           => 'field_user_need_image',
                        'label'         => 'Ảnh icon đại diện (Desktop)',
                        'name'          => 'image',
                        'type'          => 'image',
                        'instructions'  => 'Ảnh vuông hoặc icon nhỏ cho Desktop (~60x60px)',
                        'return_format' => 'url',
                        'preview_size'  => 'thumbnail',
                        'library'       => 'all',
                        'wrapper'       => array('width' => '50'),
                    ),
                    array(
                        'key'           => 'field_user_need_image_mobile',
                        'label'         => 'Ảnh người đi xe dọc (Mobile)',
                        'name'          => 'image_mobile',
                        'type'          => 'image',
                        'instructions'  => 'Ảnh chụp dọc người đạp xe hiển thị nửa trên của card Mobile (khuyên dùng tỉ lệ 3:4 hoặc 4:5)',
                        'return_format' => 'url',
                        'preview_size'  => 'medium',
                        'library'       => 'all',
                        'wrapper'       => array('width' => '50'),
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
 * Hàm hỗ trợ lấy SVG icon mặc định theo từ khóa tiêu đề (Dùng cho phần vòm cong trên Mobile)
 */
function gobike_get_need_icon_svg($title)
{
    $t = mb_strtolower($title, 'UTF-8');
    // 1. Đi làm / công sở
    if (strpos($t, 'làm') !== false || strpos($t, 'công sở') !== false) {
        return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0d6e2e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>';
    }
    // 2. Học sinh / sinh viên / đi học
    if (strpos($t, 'học') !== false || strpos($t, 'sinh viên') !== false) {
        return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0d6e2e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>';
    }
    // 3. Thể thao / leo núi / khám phá
    if (strpos($t, 'thể thao') !== false || strpos($t, 'khám phá') !== false || strpos($t, 'địa hình') !== false) {
        return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0d6e2e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3l4 8 5-5 5 15H2L8 3z"/></svg>';
    }
    // 4. Gia đình / người lớn tuổi / du lịch
    if (strpos($t, 'gia đình') !== false || strpos($t, 'người lớn') !== false || strpos($t, 'du lịch') !== false || strpos($t, 'dã ngoại') !== false) {
        return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0d6e2e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>';
    }
    // Fallback: icon xe đạp
    return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#0d6e2e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="18.5" cy="17.5" r="3.5"/><path d="M5.5 17.5L9 8h2.5"/><path d="M18.5 17.5L15 8h-3.5"/><path d="M12 17.5V11"/><circle cx="12" cy="5.5" r="1.5" fill="#0d6e2e"/></svg>';
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
    $mobile_title = 'CHỌN XE THEO NHU CẦU';
    $mobile_viewall = home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/');

    if (function_exists('get_field')) {
        if (!empty($front_page_id)) {
            $rows = get_field('home_user_needs', $front_page_id);
            $custom_title = get_field('needs_section_title_mobile', $front_page_id);
            if (!empty($custom_title)) $mobile_title = $custom_title;
            $custom_link = get_field('needs_section_viewall_link', $front_page_id);
            if (!empty($custom_link)) $mobile_viewall = $custom_link;
        }
        if (empty($rows)) {
            $rows = get_field('home_user_needs');
        }
        if (empty($rows)) {
            $rows = get_field('home_user_needs', 'option');
        }
    }

    // Bỏ hết dữ liệu mẫu theo yêu cầu: nếu chưa có dữ liệu thì thông báo cho admin
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
    <div class="gobike-user-needs-main-section <?php echo esc_attr($atts['class']); ?>">
        
        <!-- =================================================================
             1. GIAO DIỆN DESKTOP: 5 CỘT DÀN ĐỀU TRÊN 1 HÀNG (CHUẨN ẢNH 1)
             ================================================================= -->
        <div class="gobike-needs-desktop-layout">
            <div class="gobike-needs-desktop-grid">
                <?php foreach ($rows as $item):
                    $title = !empty($item['title']) ? trim($item['title']) : '';
                    if (empty($title)) continue;
                    $desc  = !empty($item['desc']) ? trim($item['desc']) : '';
                    $link  = !empty($item['link']) ? trim($item['link']) : '#';
                    $img   = !empty($item['image']) ? $item['image'] : (!empty($item['image_mobile']) ? $item['image_mobile'] : '');
                    if (is_array($img) && isset($img['url'])) $img = $img['url'];
                    elseif (is_numeric($img)) $img = wp_get_attachment_image_url($img, 'thumbnail');
                    ?>
                    <a href="<?php echo esc_url($link); ?>" class="gobike-need-card-desktop">
                        <div class="need-thumb-desktop">
                            <?php if (!empty($img)): ?>
                                <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" />
                            <?php else: ?>
                                <?php echo gobike_get_need_icon_svg($title); ?>
                            <?php endif; ?>
                        </div>
                        <div class="need-info-desktop">
                            <h4 class="need-title-desktop"><?php echo esc_html($title); ?></h4>
                            <?php if (!empty($desc)): ?>
                                <span class="need-desc-desktop"><?php echo esc_html($desc); ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- =================================================================
             2. GIAO DIỆN MOBILE & TABLET: SWIPER SLIDER CARD DỌC (CHUẨN ẢNH 2)
             ================================================================= -->
        <div class="gobike-needs-mobile-layout">
            <div class="gobike-needs-mobile-header">
                <h3 class="needs-mobile-main-title"><?php echo esc_html($mobile_title); ?></h3>
                <a href="<?php echo esc_url($mobile_viewall); ?>" class="needs-mobile-viewall-link">
                    Xem tất cả <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="swiper-container gobike-needs-swiper-mobile">
                <div class="swiper-wrapper">
                    <?php foreach ($rows as $item):
                        $title = !empty($item['title']) ? trim($item['title']) : '';
                        if (empty($title)) continue;
                        $link  = !empty($item['link']) ? trim($item['link']) : '#';
                        $img_m = !empty($item['image_mobile']) ? $item['image_mobile'] : (!empty($item['image']) ? $item['image'] : '');
                        if (is_array($img_m) && isset($img_m['url'])) $img_m = $img_m['url'];
                        elseif (is_numeric($img_m)) $img_m = wp_get_attachment_image_url($img_m, 'medium');

                        $svg_icon = gobike_get_need_icon_svg($title);
                        ?>
                        <div class="swiper-slide gobike-needs-slide-mobile">
                            <a href="<?php echo esc_url($link); ?>" class="gobike-need-card-mobile">
                                <div class="need-photo-top-mobile">
                                    <?php if (!empty($img_m)): ?>
                                        <img src="<?php echo esc_url($img_m); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" />
                                    <?php else: ?>
                                        <div class="need-photo-placeholder"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="need-content-bottom-mobile">
                                    <div class="need-icon-bubble-mobile">
                                        <?php echo $svg_icon; ?>
                                    </div>
                                    <h4 class="need-title-mobile"><?php echo nl2br(esc_html($title)); ?></h4>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- Pagination Dots Swiper -->
                <div class="swiper-pagination gobike-needs-pagination"></div>
            </div>
        </div>

    </div>

    <!-- Script khởi tạo Swiper độc lập mượt mà -->
    <script type="text/javascript">
        (function($) {
            function initGobikeNeedsSwiper() {
                if ($('.gobike-needs-swiper-mobile').length === 0) return;
                
                function runSwiper() {
                    if (typeof Swiper === 'undefined') return;
                    $('.gobike-needs-swiper-mobile').each(function() {
                        var $el = $(this);
                        if ($el.data('swiper-initialized')) return;
                        
                        var needsSwiper = new Swiper($el[0], {
                            slidesPerView: 2.2,
                            spaceBetween: 12,
                            speed: 400,
                            grabCursor: true,
                            pagination: {
                                el: $el.find('.gobike-needs-pagination')[0] || '.gobike-needs-pagination',
                                clickable: true,
                            },
                            breakpoints: {
                                550: {
                                    slidesPerView: 3.2,
                                    spaceBetween: 14,
                                },
                                768: {
                                    slidesPerView: 4,
                                    spaceBetween: 16,
                                }
                            }
                        });
                        $el.data('swiper-initialized', true);
                    });
                }

                if (typeof Swiper === 'undefined') {
                    $.getScript('https://unpkg.com/swiper/swiper-bundle.min.js', function() {
                        runSwiper();
                    });
                } else {
                    runSwiper();
                }
            }

            $(document).ready(function() {
                initGobikeNeedsSwiper();
                setTimeout(initGobikeNeedsSwiper, 300);
            });
            $(window).on('load resize', function() {
                initGobikeNeedsSwiper();
            });
        })(jQuery);
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('gobike_user_needs', 'gobike_render_user_needs_shortcode');
