<?php
/**
 * Shortcode: Khối Hero Banner Trang Chủ (Hero Banner Slider & News)
 * Cú pháp dùng trong Flatsome: [gobike_home_hero_banner] hoặc [gobike_banner_home]
 * Ghi chú: CSS của khối này được quản lý tập trung tại: home-styles.php (Nạp qua hook wp_head)
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. Đăng ký nhóm trường ACF cho Banner Trang Chủ (Hero Banner: Cột Trái & Showroom)
 */
add_action('acf/init', 'gobike_register_hero_banner_acf_fields');
function gobike_register_hero_banner_acf_fields()
{
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    $front_page_id = get_option('page_on_front');

    $location = array(
        array(
            array(
                'param' => 'page_type',
                'operator' => '==',
                'value' => 'front_page',
            ),
        ),
    );

    if (!empty($front_page_id)) {
        $location[] = array(
            array(
                'param' => 'page',
                'operator' => '==',
                'value' => strval($front_page_id),
            ),
        );
    }

    acf_add_local_field_group(array(
        'key' => 'group_gobike_hero_banners',
        'title' => '[Trang chủ] Banner Hero Section (Cột Trái & Showroom)',
        'fields' => array(
            // --- TAB 1: BANNER DỌC CỘT TRÁI ---
            array(
                'key' => 'field_banner_left_tab',
                'label' => 'Banner Dọc Cột Trái',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_banner_left_image',
                'label' => 'Ảnh Banner Dọc Bên Trái',
                'name' => 'banner_left_image',
                'type' => 'image',
                'instructions' => 'Tải lên ảnh banner dọc bên trái (.banner-home .box_left.gobike-hero-box-left). Kích thước chuẩn: ~240x520px hoặc tỉ lệ dọc tương đương. Nếu để trống hệ thống sẽ dùng ảnh mặc định.',
                'required' => 0,
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_banner_left_link',
                'label' => 'Link liên kết Banner Trái',
                'name' => 'banner_left_link',
                'type' => 'text',
                'instructions' => 'Đường dẫn khi nhấp vào banner (mặc định: /san-pham/)',
                'required' => 0,
                'default_value' => '/san-pham/',
                'placeholder' => '/san-pham/',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_banner_left_title',
                'label' => 'Tiêu đề / Thẻ Alt (SEO)',
                'name' => 'banner_left_title',
                'type' => 'text',
                'instructions' => 'Tiêu đề tooltip và thẻ alt cho banner dọc bên trái.',
                'required' => 0,
                'default_value' => 'GoBike - Chất lượng thật để bền vạn năm',
                'placeholder' => 'GoBike - Chất lượng thật để bền vạn năm',
                'wrapper' => array('width' => '100'),
            ),

            // --- TAB 2: BANNER SHOWROOM CỘT PHẢI ---
            array(
                'key' => 'field_banner_showroom_tab',
                'label' => 'Banner Showroom Cột Phải',
                'type' => 'tab',
                'placement' => 'top',
                'endpoint' => 0,
            ),
            array(
                'key' => 'field_banner_showroom_image',
                'label' => 'Ảnh Banner Showroom',
                'name' => 'banner_showroom_image',
                'type' => 'image',
                'instructions' => 'Tải lên ảnh banner Showroom ở cột bên phải (.gobike-showroom-banner-box). Nếu để trống sẽ dùng ảnh mặc định.',
                'required' => 0,
                'return_format' => 'array',
                'preview_size' => 'medium',
                'library' => 'all',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_banner_showroom_link',
                'label' => 'Link liên kết Showroom',
                'name' => 'banner_showroom_link',
                'type' => 'text',
                'instructions' => 'Đường dẫn khi nhấp vào banner Showroom (mặc định: /lien-he/)',
                'required' => 0,
                'default_value' => '/lien-he/',
                'placeholder' => '/lien-he/',
                'wrapper' => array('width' => '50'),
            ),
            array(
                'key' => 'field_banner_showroom_title',
                'label' => 'Tiêu đề / Thẻ Alt Showroom',
                'name' => 'banner_showroom_title',
                'type' => 'text',
                'instructions' => 'Tiêu đề chú thích và thẻ alt cho banner Showroom.',
                'required' => 0,
                'default_value' => 'Trải nghiệm thực tế tại Hệ thống Showroom GOBIKE',
                'placeholder' => 'Trải nghiệm thực tế tại Hệ thống Showroom GOBIKE',
                'wrapper' => array('width' => '100'),
            ),
        ),
        'location' => $location,
        'menu_order' => 1,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
    ));
}

function gobike_render_home_hero_banner($atts = array())
{
    $atts = shortcode_atts(array(
        'news_limit' => 3,
    ), $atts, 'gobike_home_hero_banner');

    $front_page_id = get_option('page_on_front');
    $rows = function_exists('get_field') ? get_field('slider_top', $front_page_id) : false;
    if (!$rows && function_exists('get_field')) {
        $rows = get_field('slider_top');
    }

    // 4 cam kết/dịch vụ chuẩn theo mẫu thiết kế (Bộ icon SVG thông dụng, chuẩn UI hiện đại)
    $service_items = array(
        array(
            'line1' => 'XE ĐẠP THỂ THAO',
            'line2' => 'MỚI',
            'title' => 'XE ĐẠP THỂ THAO MỚI',
            'svg'   => '<svg class="gb-service-svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="18.5" cy="17.5" r="3.5"/><path d="M15 6a1.2 1.2 0 1 0 0-2.4 1.2 1.2 0 0 0 0 2.4zm-3 11.5L9 9l3-3 3 4.5 3.5 1"/></svg>'
        ),
        array(
            'line1' => 'PHỤ KIỆN XE ĐẠP',
            'line2' => 'CHÍNH HÃNG',
            'title' => 'PHỤ KIỆN XE ĐẠP CHÍNH HÃNG',
            'svg'   => '<svg class="gb-service-svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>'
        ),
        array(
            'line1' => 'XE ĐẠP TRẺ EM',
            'line2' => 'AN TOÀN',
            'title' => 'XE ĐẠP TRẺ EM AN TOÀN',
            'svg'   => '<svg class="gb-service-svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="18" r="3"/><circle cx="19" cy="18" r="3"/><path d="M12 18V7l3-3h3M8 18l3-8"/></svg>'
        ),
        array(
            'line1' => 'DỊCH VỤ BẢO DƯỠNG',
            'line2' => 'MIỄN PHÍ',
            'title' => 'DỊCH VỤ BẢO DƯỠNG MIỄN PHÍ',
            'svg'   => '<svg class="gb-service-svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>'
        )
    );

    // Lọc bỏ slide "Khuyến mãi" nếu tồn tại trong ACF repeater
    $slides = array();
    if (!empty($rows) && is_array($rows)) {
        foreach ($rows as $row) {
            $title = isset($row['title_slide']) ? trim($row['title_slide']) : '';
            if (mb_stripos($title, 'khuyến mãi') !== false || mb_stripos($title, 'khuyen mai') !== false) {
                continue;
            }
            $slides[] = $row;
        }
    }

    // Ảnh mặc định chỉ dùng khi người dùng chưa cấu hình bất kỳ slide nào trong ACF
    $default_banner_images = array(
        content_url('/uploads/2026/08/7a0ac4d6-ac79-4358-a397-9e78a2d3a304.webp'),
        content_url('/uploads/2026/08/b5654596-d2fe-4d68-920c-b88feed92d95.webp'),
        content_url('/uploads/2026/08/14968be5-0f73-4635-8a75-4c165843f7dd.webp'),
    );

    if (empty($slides)) {
        foreach ($default_banner_images as $idx => $def_url) {
            $slides[] = array(
                'image_slide' => array('url' => $def_url),
                'link_url'    => home_url('/san-pham/'),
                'title_slide' => isset($service_items[$idx]['title']) ? $service_items[$idx]['title'] : 'GoBike',
            );
        }
    }

    // Query 3 tin tức mới nhất
    $news_query = new WP_Query(array(
        'post_type'      => 'post',
        'posts_per_page' => intval($atts['news_limit']),
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC'
    ));

    $theme_uri = get_stylesheet_directory_uri();
    $theme_dir = get_stylesheet_directory();

    // 1. CỘT TRÁI: BANNER DỌC (Lấy từ ACF 'banner_left_image', fallback các tên phụ và file ảnh gốc)
    $left_banner_img_val = function_exists('get_field') ? get_field('banner_left_image', $front_page_id) : false;
    if (empty($left_banner_img_val) && function_exists('get_field')) {
        $left_banner_img_val = get_field('banner_left_image');
    }
    if (empty($left_banner_img_val) && function_exists('get_field')) {
        $left_banner_img_val = get_field('banner_left', $front_page_id) ?: get_field('left_banner', $front_page_id) ?: get_field('left_banner_image', $front_page_id);
    }

    $left_banner_url = '';
    if (!empty($left_banner_img_val)) {
        if (is_array($left_banner_img_val)) {
            if (!empty($left_banner_img_val['url'])) {
                $left_banner_url = $left_banner_img_val['url'];
            } elseif (!empty($left_banner_img_val['ID'])) {
                $left_banner_url = wp_get_attachment_image_url($left_banner_img_val['ID'], 'full');
            }
        } elseif (is_numeric($left_banner_img_val)) {
            $left_banner_url = wp_get_attachment_image_url($left_banner_img_val, 'full');
        } elseif (is_string($left_banner_img_val)) {
            $left_banner_url = $left_banner_img_val;
        }
    }

    $left_banner_file = $theme_dir . '/assets/images/banner-left-vert.png';
    $v_left = file_exists($left_banner_file) ? filemtime($left_banner_file) : time();
    $left_banner_fallback = file_exists($left_banner_file)
        ? $theme_uri . '/assets/images/banner-left-vert.png?v=' . $v_left
        : content_url('/uploads/2026/09/banner-left-vert.png?v=' . $v_left);

    $left_banner_img = !empty($left_banner_url) ? $left_banner_url : $left_banner_fallback;

    $left_banner_link = function_exists('get_field') ? get_field('banner_left_link', $front_page_id) : '';
    if (empty($left_banner_link) && function_exists('get_field')) {
        $left_banner_link = get_field('banner_left_link');
    }
    if (empty($left_banner_link)) {
        $left_banner_link = home_url('/san-pham/');
    }

    $left_banner_title = function_exists('get_field') ? get_field('banner_left_title', $front_page_id) : '';
    if (empty($left_banner_title) && function_exists('get_field')) {
        $left_banner_title = get_field('banner_left_title');
    }
    if (empty($left_banner_title)) {
        $left_banner_title = 'GoBike - Chất lượng thật để bền vạn năm';
    }

    // 2. CỘT PHẢI: BANNER SHOWROOM (Lấy từ ACF 'banner_showroom_image', fallback sang file ảnh gốc)
    $showroom_img_val = function_exists('get_field') ? get_field('banner_showroom_image', $front_page_id) : false;
    if (empty($showroom_img_val) && function_exists('get_field')) {
        $showroom_img_val = get_field('banner_showroom_image');
    }
    $showroom_url = '';
    if (!empty($showroom_img_val)) {
        if (is_array($showroom_img_val)) {
            if (!empty($showroom_img_val['url'])) {
                $showroom_url = $showroom_img_val['url'];
            } elseif (!empty($showroom_img_val['ID'])) {
                $showroom_url = wp_get_attachment_image_url($showroom_img_val['ID'], 'full');
            }
        } elseif (is_numeric($showroom_img_val)) {
            $showroom_url = wp_get_attachment_image_url($showroom_img_val, 'full');
        } elseif (is_string($showroom_img_val)) {
            $showroom_url = $showroom_img_val;
        }
    }

    $showroom_file = $theme_dir . '/assets/images/banner-showroom.png';
    $v_show = file_exists($showroom_file) ? filemtime($showroom_file) : time();
    $showroom_fallback = file_exists($showroom_file)
        ? $theme_uri . '/assets/images/banner-showroom.png?v=' . $v_show
        : content_url('/uploads/2026/09/banner-showroom.png?v=' . $v_show);

    $showroom_img = !empty($showroom_url) ? $showroom_url : $showroom_fallback;

    $showroom_link = function_exists('get_field') ? get_field('banner_showroom_link', $front_page_id) : '';
    if (empty($showroom_link) && function_exists('get_field')) {
        $showroom_link = get_field('banner_showroom_link');
    }
    if (empty($showroom_link)) {
        $showroom_link = home_url('/lien-he/');
    }

    $showroom_title = function_exists('get_field') ? get_field('banner_showroom_title', $front_page_id) : '';
    if (empty($showroom_title) && function_exists('get_field')) {
        $showroom_title = get_field('banner_showroom_title');
    }
    if (empty($showroom_title)) {
        $showroom_title = 'Trải nghiệm thực tế tại Hệ thống Showroom GOBIKE';
    }

    ob_start();
    ?>
    <div class="banner-home gobike-hero-banner-section">
        <div class="container">
            <div class="row vp-row-custom row-collapse">
                <!-- 1. CỘT TRÁI: BANNER DỌC CHẤT LƯỢNG THẬT BỀN VẠN NĂM -->
                <div class="col hide-for-medium box_left gobike-hero-box-left">
                    <div class="gobike-vert-banner-card">
                        <a href="<?php echo esc_url($left_banner_link); ?>" title="<?php echo esc_attr($left_banner_title); ?>">
                            <img src="<?php echo esc_url($left_banner_img); ?>" alt="<?php echo esc_attr($left_banner_title); ?>" />
                        </a>
                    </div>
                </div>

                <!-- 2. CỘT GIỮA: SLIDER CHÍNH + THANH 4 DỊCH VỤ CAM KẾT -->
                <div class="col slider-top box_center gobike-hero-box-center">
                    <div class="swiper-container">
                        <!-- Swiper chính (Ảnh to) -->
                        <div class="mySwiper2">
                            <div class="swiper-wrapper">
                                <?php 
                                $fallback_images = array(
                                    content_url('/uploads/2026/08/7a0ac4d6-ac79-4358-a397-9e78a2d3a304.webp'),
                                    content_url('/uploads/2026/08/b5654596-d2fe-4d68-920c-b88feed92d95.webp'),
                                    content_url('/uploads/2026/08/14968be5-0f73-4635-8a75-4c165843f7dd.webp'),
                                );
                                foreach ($slides as $slide_idx => $slide): 
                                    $img_url = '';
                                    if (!empty($slide['image_slide'])) {
                                        if (is_array($slide['image_slide'])) {
                                            if (!empty($slide['image_slide']['url'])) {
                                                $img_url = $slide['image_slide']['url'];
                                            } elseif (!empty($slide['image_slide']['ID'])) {
                                                $img_url = wp_get_attachment_image_url($slide['image_slide']['ID'], 'full');
                                            }
                                        } elseif (is_numeric($slide['image_slide'])) {
                                            $img_url = wp_get_attachment_image_url($slide['image_slide'], 'full');
                                        } elseif (is_string($slide['image_slide'])) {
                                            $img_url = $slide['image_slide'];
                                        }
                                    }
                                    if (empty($img_url) && !empty($slide['image'])) {
                                        if (is_array($slide['image']) && !empty($slide['image']['url'])) {
                                            $img_url = $slide['image']['url'];
                                        } elseif (is_string($slide['image'])) {
                                            $img_url = $slide['image'];
                                        }
                                    }
                                    if (empty($img_url)) {
                                        $img_url = isset($fallback_images[$slide_idx % 4]) ? $fallback_images[$slide_idx % 4] : $fallback_images[0];
                                    }
                                    $link_url = !empty($slide['link_url']) ? $slide['link_url'] : home_url('/san-pham/');
                                ?>
                                    <div class="swiper-slide">
                                        <a href="<?php echo esc_url($link_url); ?>" style="display:block; width:100%; height:100%;">
                                            <img src="<?php echo esc_url($img_url); ?>" alt="Banner GoBike" loading="eager" />
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>

                        <div class="swiper-pagination banner-home-pagination"></div>

                        <!-- Swiper Thumbs (4 cam kết dưới slide - Bỏ Khuyến Mãi - Ẩn trên tablet & mobile) -->
                        <div class="mySwiper gobike-service-thumbs hide-for-medium">
                            <div class="swiper-wrapper">
                                <?php foreach ($service_items as $index => $item): ?>
                                    <div class="swiper-slide service-thumb-item">
                                        <div class="thumb-icon-wrap">
                                            <?php echo $item['svg']; ?>
                                        </div>
                                        <div class="thumb-text-wrap">
                                            <span class="thumb-title"><?php echo esc_html(!empty($item['line1']) ? $item['line1'] : $item['title']); ?></span>
                                            <?php if (!empty($item['line2'])): ?>
                                                <span class="thumb-desc"><?php echo esc_html($item['line2']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. CỘT PHẢI: TIN TỨC MỚI NHẤT & BANNER SHOWROOM -->
                <div class="col hide-for-medium box_right gobike-hero-box-right">
                    <div class="news-home-box gobike-news-card">
                        <!-- Phần trên: Tin tức mới nhất -->
                        <div class="gobike-news-header">
                            <h3 class="news-header-title">TIN TỨC MỚI NHẤT</h3>
                            <a href="<?php echo esc_url(home_url('/tin-tuc/')); ?>" class="news-header-more">
                                Xem tất cả &rarr;
                            </a>
                        </div>

                        <div class="news-list gobike-news-list">
                            <?php if ($news_query->have_posts()) : ?>
                                <?php while ($news_query->have_posts()) : $news_query->the_post(); 
                                    $thumb_url = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') : content_url('/uploads/2026/08/2375.jpg');
                                ?>
                                    <div class="gobike-news-item">
                                        <a href="<?php the_permalink(); ?>" class="news-item-thumb">
                                            <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
                                        </a>
                                        <div class="news-item-body">
                                            <h4 class="news-item-title">
                                                <a href="<?php the_permalink(); ?>">
                                                    <?php echo wp_trim_words(get_the_title(), 12, '...'); ?>
                                                </a>
                                            </h4>
                                            <span class="news-item-date">
                                                <?php echo get_the_date('d/m/Y'); ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endwhile; wp_reset_postdata(); ?>
                            <?php endif; ?>
                        </div>

                        <!-- Phần dưới: Banner Showroom GOBIKE -->
                        <div class="gobike-showroom-banner-box">
                            <a href="<?php echo esc_url($showroom_link); ?>" title="<?php echo esc_attr($showroom_title); ?>">
                                <img src="<?php echo esc_url($showroom_img); ?>" alt="<?php echo esc_attr($showroom_title); ?>" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
    (function() {
        function initHeroSwiper() {
            if (typeof Swiper === 'undefined') {
                setTimeout(initHeroSwiper, 100);
                return;
            }
            var heroSection = document.querySelector('.gobike-hero-banner-section');
            if (!heroSection) return;

            var serviceSwiper = new Swiper(heroSection.querySelector('.mySwiper'), {
                spaceBetween: 0,
                slidesPerView: 4,
                freeMode: false,
                slideToClickedSlide: true,
                watchSlidesVisibility: true,
                watchSlidesProgress: true,
                observer: true,
                observeParents: true,
            });

            var bannerSwiper = new Swiper(heroSection.querySelector('.mySwiper2'), {
                spaceBetween: 0,
                loop: true,
                observer: true,
                observeParents: true,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                },
                navigation: {
                    nextEl: heroSection.querySelector('.swiper-button-next'),
                    prevEl: heroSection.querySelector('.swiper-button-prev'),
                },
                pagination: {
                    el: heroSection.querySelector('.banner-home-pagination'),
                    clickable: true,
                },
                thumbs: {
                    swiper: serviceSwiper,
                },
            });

            var totalSlides = <?php echo count($slides); ?>;
            var thumbItems = heroSection.querySelectorAll('.mySwiper .service-thumb-item');
            function syncActiveThumb(realIdx) {
                var activeThumbIdx = (thumbItems.length > 0) ? (realIdx % thumbItems.length) : 0;
                thumbItems.forEach(function(el, i) {
                    if (i === activeThumbIdx) {
                        el.classList.add('swiper-slide-thumb-active');
                    } else {
                        el.classList.remove('swiper-slide-thumb-active');
                    }
                });
            }

            thumbItems.forEach(function(item, idx) {
                item.style.cursor = 'pointer';
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (bannerSwiper) {
                        var targetSlide = totalSlides > 0 ? (idx % totalSlides) : 0;
                        if (typeof bannerSwiper.slideToLoop === 'function') {
                            bannerSwiper.slideToLoop(targetSlide);
                        } else {
                            bannerSwiper.slideTo(targetSlide);
                        }
                        syncActiveThumb(idx);
                    }
                });
            });

            bannerSwiper.on('slideChange', function() {
                var realIdx = (typeof bannerSwiper.realIndex !== 'undefined') ? bannerSwiper.realIndex : (bannerSwiper.activeIndex % (totalSlides || 1));
                syncActiveThumb(realIdx);
            });
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initHeroSwiper);
        } else {
            initHeroSwiper();
        }
    })();
    </script>
    <?php
    return ob_get_clean();
}

add_shortcode('gobike_home_hero_banner', 'gobike_render_home_hero_banner');
add_shortcode('gobike_banner_home', 'gobike_render_home_hero_banner');
add_shortcode('banner_home', 'gobike_render_home_hero_banner');
add_shortcode('banner-home', 'gobike_render_home_hero_banner');
