<?php
/**
 * Shortcode: Khối Hero Banner Trang Chủ (Hero Banner Slider & News)
 * Cú pháp dùng trong Flatsome: [gobike_home_hero_banner] hoặc [gobike_banner_home]
 * Ghi chú: CSS của khối này được quản lý tập trung tại: home-styles.php (Nạp qua hook wp_head)
 */

if (!defined('ABSPATH')) {
    exit;
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

    // 4 cam kết/dịch vụ chuẩn theo mẫu thiết kế Ảnh 1 (Đã loại bỏ mục Khuyến mãi)
    $service_items = array(
        array(
            'icon'  => content_url('/uploads/2026/09/icon_menu_2.webp'),
            'line1' => 'XE ĐẠP THỂ THAO',
            'line2' => 'MỚI',
            'svg'   => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0d6e2e" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="18.5" cy="17.5" r="3.5"/><path d="M15 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-3 11.5L9 6H5m7 11.5 3.5-7.5L12 6h3l3.5 4"/></svg>'
        ),
        array(
            'icon'  => content_url('/uploads/2026/09/icon_menu_3.webp'),
            'line1' => 'PHỤ KIỆN XE ĐẠP',
            'line2' => 'CHÍNH HÃNG',
            'svg'   => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0d6e2e" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>'
        ),
        array(
            'icon'  => content_url('/uploads/2026/09/icon_menu_4.webp'),
            'line1' => 'XE ĐẠP TRẺ EM',
            'line2' => 'AN TOÀN',
            'svg'   => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0d6e2e" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="18" r="3"/><circle cx="18" cy="18" r="3"/><path d="M12 18V8l3-3h3M9 13h6"/></svg>'
        ),
        array(
            'icon'  => content_url('/uploads/2026/09/icon_menu_5.webp'),
            'line1' => 'DỊCH VỤ BẢO DƯỠNG',
            'line2' => 'MIỄN PHÍ',
            'svg'   => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0d6e2e" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>'
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

    // Fallback nếu không có slide nào sau khi lọc
    if (empty($slides)) {
        $slides = array(
            array(
                'image_slide' => array('url' => content_url('/uploads/2026/08/0ecfa5aa-9eb2-4477-92eb-44f613a12080.webp')),
                'link_url'    => home_url('/san-pham/'),
                'title_slide' => 'XE ĐẠP THỂ THAO MỚI',
            ),
            array(
                'image_slide' => array('url' => content_url('/uploads/2026/08/7a0ac4d6-ac79-4358-a397-9e78a2d3a304.webp')),
                'link_url'    => home_url('/san-pham/'),
                'title_slide' => 'PHỤ KIỆN XE ĐẠP CHÍNH HÃNG',
            ),
            array(
                'image_slide' => array('url' => content_url('/uploads/2026/08/b5654596-d2fe-4d68-920c-b88feed92d95.webp')),
                'link_url'    => home_url('/san-pham/'),
                'title_slide' => 'XE ĐẠP TRẺ EM AN TOÀN',
            ),
            array(
                'image_slide' => array('url' => content_url('/uploads/2026/08/14968be5-0f73-4635-8a75-4c165843f7dd.webp')),
                'link_url'    => home_url('/san-pham/'),
                'title_slide' => 'DỊCH VỤ BẢO DƯỠNG MIỄN PHÍ',
            ),
        );
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
    $left_banner_file = $theme_dir . '/assets/images/banner-left-vert.png';
    $v_left = file_exists($left_banner_file) ? filemtime($left_banner_file) : time();
    $left_banner_img = file_exists($left_banner_file)
        ? $theme_uri . '/assets/images/banner-left-vert.png?v=' . $v_left
        : content_url('/uploads/2026/09/banner-left-vert.png?v=' . $v_left);

    $showroom_file = $theme_dir . '/assets/images/banner-showroom.png';
    $v_show = file_exists($showroom_file) ? filemtime($showroom_file) : time();
    $showroom_img = file_exists($showroom_file)
        ? $theme_uri . '/assets/images/banner-showroom.png?v=' . $v_show
        : content_url('/uploads/2026/09/banner-showroom.png?v=' . $v_show);

    ob_start();
    ?>
    <div class="banner-home gobike-hero-banner-section">
        <div class="container">
            <div class="row gobike-hero-row">
                <!-- 1. CỘT TRÁI: BANNER DỌC CHẤT LƯỢNG THẬT BỀN VẠN NĂM -->
                <div class="col hide-for-medium box_left gobike-hero-box-left">
                    <div class="gobike-vert-banner-card">
                        <a href="<?php echo esc_url(home_url('/san-pham/')); ?>" title="GoBike - Chất lượng thật để bền vạn năm">
                            <img src="<?php echo esc_url($left_banner_img); ?>" alt="GoBike - Chất lượng thật để bền vạn năm" />
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
                                    content_url('/uploads/2026/08/0ecfa5aa-9eb2-4477-92eb-44f613a12080.webp'),
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

                        <!-- Swiper Thumbs (4 cam kết dưới slide - Bỏ Khuyến Mãi) -->
                        <div class="mySwiper gobike-service-thumbs">
                            <div class="swiper-wrapper">
                                <?php foreach ($service_items as $index => $item): ?>
                                    <div class="swiper-slide service-thumb-item">
                                        <div class="thumb-icon-wrap">
                                            <?php echo $item['svg']; ?>
                                        </div>
                                        <div class="thumb-text-wrap">
                                            <span class="thumb-title"><?php echo esc_html($item['line1']); ?></span>
                                            <span class="thumb-desc"><?php echo esc_html($item['line2']); ?></span>
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
                            <a href="<?php echo esc_url(home_url('/lien-he/')); ?>" title="Trải nghiệm thực tế tại Hệ thống Showroom GOBIKE">
                                <img src="<?php echo esc_url($showroom_img); ?>" alt="Hệ thống Showroom GOBIKE" />
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
            var serviceSwiper = new Swiper(".mySwiper", {
                spaceBetween: 0,
                slidesPerView: 4,
                freeMode: false,
                allowTouchMove: false,
                watchSlidesVisibility: true,
                watchSlidesProgress: true,
                observer: true,
                observeParents: true,
            });
            var bannerSwiper = new Swiper(".mySwiper2", {
                spaceBetween: 0,
                loop: true,
                observer: true,
                observeParents: true,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                pagination: {
                    el: ".banner-home-pagination",
                    clickable: true,
                },
                thumbs: {
                    swiper: serviceSwiper,
                },
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
