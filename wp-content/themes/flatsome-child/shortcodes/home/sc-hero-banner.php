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

    // 4 cam kết/dịch vụ chuẩn theo mẫu thiết kế (Chỉ giữ tiêu đề, bỏ mô tả)
    $service_items = array(
        array(
            'title' => 'XE ĐẠP THỂ THAO',
            'svg'   => '<svg width="28" height="28" viewBox="0 0 640 512" fill="#0d6e2e"><path d="M400 96a48 48 0 1 0 -96 0 48 48 0 1 0 96 0zm-122.3 84.1c-10.7-18.4-30.8-29.8-52.2-29.8h-48.4c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h40.4l37.2 64H176.6c-13.4-56.1-64.2-96-123.6-96-69.5 0-126 56.5-126 126s56.5 126 126 126c59.4 0 110.2-39.9 123.6-96h111.9l46.7 80.8c-10.9 20.3-17.2 43.4-17.2 68.2 0 79.5 64.5 144 144 144s144-64.5 144-144c0-77.9-61.9-141.5-139.2-143.8l-40.4-69.8 45.4-37.4 34.2 34.2c6.2 6.2 14.7 9.8 23.6 9.8h48c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16h-38.6l-47.5-47.5c-15-15-37.8-19.7-57.5-11.8l-87.3 35.1-23.7-40.9zM128 352a64 64 0 1 1 -128 0 64 64 0 1 1 128 0zm384 64a64 64 0 1 1 0-128 64 64 0 1 1 0 128z"/></svg>'
        ),
        array(
            'title' => 'PHỤ KIỆN XE ĐẠP',
            'svg'   => '<svg width="26" height="26" viewBox="0 0 512 512" fill="#0d6e2e"><path d="M487.4 315.7l-42.6-24.6c4.3-23.2 4.3-47 0-70.2l42.6-24.6c4.9-2.8 7.1-8.6 5.5-14-11.1-35.6-30-67.8-54.7-94.6-3.8-4.1-10-5.1-14.8-2.3L380.8 110c-17.9-15.4-38.5-27.3-60.8-35.1V25.8c0-5.6-3.9-10.5-9.4-11.7-36.7-8.2-74.7-8.2-111.4 0-5.5 1.2-9.4 6.1-9.4 11.7V75c-22.2 7.9-42.8 19.8-60.8 35.1L86.5 85.5c-4.9-2.8-11-1.9-14.8 2.3-24.8 26.7-43.6 58.9-54.7 94.6-1.7 5.4.6 11.2 5.5 14L65 221c-4.3 23.2-4.3 47 0 70.2l-42.6 24.6c-4.9 2.8-7.1 8.6-5.5 14 11.1 35.6 30 67.8 54.7 94.6 3.8 4.1 10 5.1 14.8 2.3l42.6-24.6c17.9 15.4 38.5 27.3 60.8 35.1v49.2c0 5.6 3.9 10.5 9.4 11.7 36.7 8.2 74.7 8.2 111.4 0 5.5-1.2 9.4-6.1 9.4-11.7v-49.2c22.2-7.9 42.8-19.8 60.8-35.1l42.6 24.6c4.9 2.8 11 1.9 14.8-2.3 24.8-26.7 43.6-58.9 54.7-94.6 1.6-5.5-.6-11.3-5.5-14.1zM256 336c-44.1 0-80-35.9-80-80s35.9-80 80-80 80 35.9 80 80-35.9 80-80 80z"/></svg>'
        ),
        array(
            'title' => 'XE ĐẠP TRẺ EM',
            'svg'   => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0d6e2e" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="18.5" cy="17.5" r="3.5"/><path d="M5.5 17.5L9 8h2.5"/><path d="M18.5 17.5L15 8h-3.5"/><path d="M12 17.5V11"/><circle cx="12" cy="5.5" r="1.5" fill="#0d6e2e"/></svg>'
        ),
        array(
            'title' => 'DỊCH VỤ BẢO DƯỠNG',
            'svg'   => '<svg width="26" height="26" viewBox="0 0 24 24" fill="#0d6e2e"><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.5 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/><path d="M1.3 19l9.1-9.1c-.9-2.3-.4-5 1.5-6.9 2-2 5-2.4 7.4-1.3L15 6l3 3 4.4-4.3c1.1 2.4.7 5.4-1.3 7.4-1.9 1.9-4.6 2.4-6.9 1.5L5.1 22.7c-.4.4-1 .4-1.4 0L1.4 20.4c-.5-.4-.5-1.1-.1-1.4z"/></svg>'
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
            <div class="row vp-row-custom gobike-hero-row">
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
                                            <span class="thumb-title"><?php echo esc_html($item['title']); ?></span>
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
