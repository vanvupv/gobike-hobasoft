<?php
/**
 * Shortcode: Khối Phân Loại Theo Nhu Cầu Sử Dụng (GoBike User Needs)
 * Dữ liệu được quản lý động qua ACF tại Trang Chủ.
 * Duy nhất 1 khối HTML dùng Swiper:
 * - Desktop: 5 card ngang (Ảnh trái, Chữ phải)
 * - Mobile: Card dọc (Ảnh trên, Vòm icon dưới, trượt Swiper)
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
                        'instructions' => 'Ví dụ: Đi làm hằng ngày, Học sinh, sinh viên, Thể thao, khám phá...',
                        'placeholder'  => 'Đi làm hằng ngày...',
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
                        'label'         => 'Ảnh đại diện',
                        'name'          => 'image',
                        'type'          => 'image',
                        'instructions'  => 'Chọn ảnh đại diện cho nhu cầu này',
                        'return_format' => 'url',
                        'preview_size'  => 'medium',
                        'library'       => 'all',
                        'wrapper'       => array('width' => '100'),
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
        return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0d6e2e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>';
    }
    // 2. Học sinh / sinh viên / đi học
    if (strpos($t, 'học') !== false || strpos($t, 'sinh viên') !== false) {
        return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0d6e2e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>';
    }
    // 3. Thể thao / leo núi / khám phá
    if (strpos($t, 'thể thao') !== false || strpos($t, 'khám phá') !== false || strpos($t, 'địa hình') !== false) {
        return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0d6e2e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3l4 8 5-5 5 15H2L8 3z"/></svg>';
    }
    // 4. Gia đình / người lớn tuổi / du lịch
    if (strpos($t, 'gia đình') !== false || strpos($t, 'người lớn') !== false || strpos($t, 'du lịch') !== false || strpos($t, 'dã ngoại') !== false) {
        return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0d6e2e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>';
    }
    // Fallback: icon xe đạp
    return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0d6e2e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="18.5" cy="17.5" r="3.5"/><path d="M5.5 17.5L9 8h2.5"/><path d="M18.5 17.5L15 8h-3.5"/><path d="M12 17.5V11"/><circle cx="12" cy="5.5" r="1.5" fill="#0d6e2e"/></svg>';
}

/**
 * 2. Render Shortcode [gobike_user_needs] (CHỈ 1 KHỐI DUY NHẤT)
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
    <!-- CHỈ 1 KHỐI DUY NHẤT: TỰ ĐỘNG CHUYỂN ĐỔI LAYOUT THEO THIẾT BỊ -->
    <div class="gobike-user-needs-unified <?php echo esc_attr($atts['class']); ?>">
        
        <!-- Header: Chỉ hiển thị trên Mobile/Tablet -->
        <div class="gobike-needs-header">
            <h3 class="needs-main-title"><?php echo esc_html($mobile_title); ?></h3>
            <a href="<?php echo esc_url($mobile_viewall); ?>" class="needs-viewall-link">
                Xem tất cả <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>

        <!-- Slider Swiper duy nhất -->
        <div class="swiper-container gobike-needs-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($rows as $item):
                    $title = !empty($item['title']) ? trim($item['title']) : '';
                    if (empty($title)) continue;
                    $desc  = !empty($item['desc']) ? trim($item['desc']) : '';
                    $link  = !empty($item['link']) ? trim($item['link']) : '#';
                    $img   = !empty($item['image']) ? $item['image'] : '';
                    if (is_array($img) && isset($img['url'])) $img = $img['url'];
                    elseif (is_numeric($img)) $img = wp_get_attachment_image_url($img, 'medium');

                    $svg_icon = gobike_get_need_icon_svg($title);
                    ?>
                    <div class="swiper-slide gobike-need-slide-item">
                        <a href="<?php echo esc_url($link); ?>" class="gobike-need-unified-card">
                            <div class="need-media-wrap">
                                <?php if (!empty($img)): ?>
                                    <img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" />
                                <?php else: ?>
                                    <div class="need-media-empty"></div>
                                <?php endif; ?>
                            </div>
                            <div class="need-body-wrap">
                                <div class="need-icon-bubble">
                                    <?php echo $svg_icon; ?>
                                </div>
                                <div class="need-text-info">
                                    <h4 class="need-title"><?php echo esc_html($title); ?></h4>
                                    <?php if (!empty($desc)): ?>
                                        <span class="need-desc"><?php echo esc_html($desc); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Pagination Dots chỉ hiển thị trên Mobile -->
            <div class="swiper-pagination gobike-needs-dots"></div>
        </div>

        <!-- CSS ĐÓNG GÓI TRỰC TIẾP TRONG SHORTCODE ĐẢM BẢO ĂN 100% KHÔNG BAO GIỜ BỊ ĐÈ -->
        <style>
            .gobike-user-needs-unified {
                max-width: 1230px;
                margin: 20px auto 30px auto;
                width: 100%;
                box-sizing: border-box;
                padding: 0 15px;
            }

            .gobike-needs-header {
                display: none; /* Ẩn trên desktop */
                justify-content: space-between;
                align-items: center;
                margin-bottom: 14px;
            }

            .needs-main-title {
                font-size: 17px !important;
                font-weight: 700 !important;
                color: #0d6e2e !important;
                margin: 0 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.2px !important;
            }

            .needs-viewall-link {
                font-size: 13.5px !important;
                font-weight: 600 !important;
                color: #0d6e2e !important;
                display: inline-flex !important;
                align-items: center !important;
                gap: 4px !important;
                text-decoration: none !important;
            }

            .gobike-needs-swiper {
                position: relative;
                width: 100%;
            }

            .gobike-need-unified-card {
                text-decoration: none !important;
                box-sizing: border-box !important;
            }

            /* =================================================================
               1. GIAO DIỆN DESKTOP (MÀN HÌNH >= 850px): CHUẨN ẢNH 1
               Card ngang 5 cột, ảnh vuông 48px bên trái, chữ bên phải
               ================================================================= */
            @media screen and (min-width: 850px) {
                .gobike-needs-swiper .swiper-wrapper {
                    display: grid !important;
                    grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
                    gap: 12px !important;
                    transform: none !important;
                    width: 100% !important;
                    box-sizing: border-box !important;
                }

                .gobike-need-slide-item {
                    width: 100% !important;
                    margin: 0 !important;
                }

                .gobike-need-unified-card {
                    background: #ffffff !important;
                    border: 1px solid #e2e8f0 !important;
                    border-radius: 12px !important;
                    padding: 10px 14px !important;
                    display: flex !important;
                    flex-direction: row !important;
                    align-items: center !important;
                    gap: 12px !important;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
                    transition: border-color 0.25s ease, box-shadow 0.25s ease !important;
                    height: 100% !important;
                    width: 100% !important;
                }

                .gobike-need-unified-card:hover {
                    border-color: #149d29 !important;
                    box-shadow: 0 4px 14px rgba(20, 157, 41, 0.12) !important;
                }

                .need-media-wrap {
                    width: 48px !important;
                    min-width: 48px !important;
                    max-width: 48px !important;
                    height: 48px !important;
                    min-height: 48px !important;
                    max-height: 48px !important;
                    border-radius: 8px !important;
                    overflow: hidden !important;
                    background: #f8fafc !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    flex-shrink: 0 !important;
                }

                .need-media-wrap img {
                    width: 48px !important;
                    height: 48px !important;
                    max-width: 48px !important;
                    max-height: 48px !important;
                    object-fit: contain !important;
                    border-radius: 8px !important;
                    display: block !important;
                }

                .need-body-wrap {
                    flex: 1 1 auto !important;
                    min-width: 0 !important;
                    text-align: left !important;
                }

                .need-icon-bubble {
                    display: none !important; /* Ẩn icon tròn trên Desktop */
                }

                .need-title {
                    font-size: 14px !important;
                    font-weight: 700 !important;
                    color: #0f172a !important;
                    margin: 0 0 2px 0 !important;
                    line-height: 1.3 !important;
                    white-space: nowrap !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                    text-align: left !important;
                }

                .gobike-need-unified-card:hover .need-title {
                    color: #149d29 !important;
                }

                .need-desc {
                    font-size: 12px !important;
                    color: #64748b !important;
                    margin: 0 !important;
                    line-height: 1.3 !important;
                    display: block !important;
                    white-space: nowrap !important;
                    overflow: hidden !important;
                    text-overflow: ellipsis !important;
                    text-align: left !important;
                }

                .gobike-needs-dots {
                    display: none !important;
                }
            }

            /* =================================================================
               2. GIAO DIỆN MOBILE & TABLET (< 850px): CHUẨN ẢNH 2
               Swiper trượt card dọc có ảnh trên, vòm icon dưới, dots
               ================================================================= */
            @media screen and (max-width: 849px) {
                .gobike-needs-header {
                    display: flex !important;
                }

                .gobike-needs-swiper {
                    padding-bottom: 26px !important;
                    overflow: visible !important;
                }

                .gobike-need-unified-card {
                    background: #ffffff !important;
                    border-radius: 18px !important;
                    overflow: hidden !important;
                    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.07) !important;
                    display: flex !important;
                    flex-direction: column !important;
                    border: 1px solid #edf2f7 !important;
                    height: 100% !important;
                }

                .need-media-wrap {
                    position: relative !important;
                    width: 100% !important;
                    padding-top: 100% !important;
                    background: #e2e8f0 !important;
                    overflow: hidden !important;
                }

                .need-media-wrap img {
                    position: absolute !important;
                    top: 0 !important;
                    left: 0 !important;
                    width: 100% !important;
                    height: 100% !important;
                    object-fit: cover !important;
                    display: block !important;
                }

                .need-media-empty {
                    position: absolute !important;
                    top: 0 !important;
                    left: 0 !important;
                    width: 100% !important;
                    height: 100% !important;
                    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%) !important;
                }

                .need-body-wrap {
                    background: #ffffff !important;
                    border-radius: 22px 22px 0 0 !important;
                    margin-top: -22px !important;
                    position: relative !important;
                    z-index: 2 !important;
                    padding: 8px 6px 14px 6px !important;
                    text-align: center !important;
                    display: flex !important;
                    flex-direction: column !important;
                    align-items: center !important;
                    min-height: 76px !important;
                    box-shadow: 0 -3px 8px rgba(0, 0, 0, 0.03) !important;
                }

                .need-icon-bubble {
                    width: 36px !important;
                    height: 36px !important;
                    border-radius: 50% !important;
                    background: #f0fdf4 !important;
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    margin: -20px auto 6px auto !important;
                    box-shadow: 0 2px 6px rgba(13, 110, 46, 0.12) !important;
                    border: 1px solid #dcfce7 !important;
                    flex-shrink: 0 !important;
                }

                .need-title {
                    font-size: 13.5px !important;
                    font-weight: 700 !important;
                    color: #0f172a !important;
                    margin: 0 !important;
                    line-height: 1.35 !important;
                    text-align: center !important;
                    display: -webkit-box !important;
                    -webkit-line-clamp: 2 !important;
                    -webkit-box-orient: vertical !important;
                    overflow: hidden !important;
                }

                .need-desc {
                    display: none !important; /* Ẩn mô tả trên mobile theo đúng ảnh mẫu 2 */
                }

                .gobike-needs-dots {
                    position: absolute !important;
                    bottom: 0 !important;
                    left: 0 !important;
                    width: 100% !important;
                    display: flex !important;
                    justify-content: center !important;
                    align-items: center !important;
                    gap: 6px !important;
                }

                .gobike-needs-dots .swiper-pagination-bullet {
                    width: 7px !important;
                    height: 7px !important;
                    margin: 0 !important;
                    background: #cbd5e1 !important;
                    opacity: 1 !important;
                    border-radius: 50% !important;
                    transition: all 0.25s ease !important;
                }

                .gobike-needs-dots .swiper-pagination-bullet-active {
                    background: #0d6e2e !important;
                    width: 18px !important;
                    border-radius: 5px !important;
                }
            }
        </style>
    </div>

    <!-- Script Swiper chỉ chạy trên màn hình Mobile/Tablet (< 850px) -->
    <script type="text/javascript">
        (function($) {
            function initGobikeNeedsSwiper() {
                if ($(window).width() >= 850) return; // Desktop không chạy slider, dùng Grid 5 cột
                if ($('.gobike-needs-swiper').length === 0) return;
                
                function runSwiper() {
                    if (typeof Swiper === 'undefined') return;
                    $('.gobike-needs-swiper').each(function() {
                        var $el = $(this);
                        if ($el.data('swiper-initialized')) return;
                        
                        var needsSwiper = new Swiper($el[0], {
                            slidesPerView: 2.2,
                            spaceBetween: 12,
                            speed: 400,
                            grabCursor: true,
                            pagination: {
                                el: $el.find('.gobike-needs-dots')[0] || '.gobike-needs-dots',
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
