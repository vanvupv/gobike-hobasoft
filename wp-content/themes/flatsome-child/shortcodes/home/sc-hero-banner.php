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

    // 4 cam kết/dịch vụ chuẩn theo mẫu thiết kế (Icon SVG 32px mạnh mẽ, văn bản 2 dòng)
    $service_items = array(
        array(
            'line1' => 'XE ĐẠP THỂ THAO',
            'line2' => 'MỚI',
            'title' => 'XE ĐẠP THỂ THAO MỚI',
            'svg'   => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3.5"/><circle cx="19" cy="17" r="3.5"/><circle cx="5" cy="17" r="1.2" fill="currentColor"/><circle cx="19" cy="17" r="1.2" fill="currentColor"/><path d="M5 17h6l-2-8h6.5l3.5 8"/><path d="M5 17l4-8"/><path d="M11 17l4.5-8"/><path d="M7 9h4"/><path d="M14.5 6.5h2.5a1.5 1.5 0 0 1 1.5 1.5"/></svg>'
        ),
        array(
            'line1' => 'PHỤ KIỆN XE ĐẠP',
            'line2' => 'CHÍNH HÃNG',
            'title' => 'PHỤ KIỆN XE ĐẠP CHÍNH HÃNG',
            'svg'   => '<svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.3 2.1c.7-.1 1.4-.1 2.1 0 .6.1 1.1.5 1.3 1.1l.4 1.2c.5.2 1 .5 1.5.8l1.2-.5c.6-.2 1.2 0 1.7.4.5.5.9 1 1.3 1.6.3.5.3 1.1 0 1.6l-.6 1.1c.3.5.5 1 .7 1.5l1.2.4c.6.2 1 .7 1.1 1.3.1.7.1 1.4 0 2.1-.1.6-.5 1.1-1.1 1.3l-1.2.4c-.2.5-.5 1-.8 1.5l.5 1.2c.2.6 0 1.2-.4 1.7-.5.5-1 .9-1.6 1.3-.5.3-1.1.3-1.6 0l-1.1-.6c-.5.3-1 .5-1.5.7l-.4 1.2c-.2.6-.7 1-1.3 1.1-.7.1-1.4.1-2.1 0-.6-.1-1.1-.5-1.3-1.1l-.4-1.2c-.5-.2-1-.5-1.5-.8l-1.2.5c-.6.2-1.2 0-1.7-.4-.5-.5-.9-1-1.3-1.6-.3-.5-.3-1.1 0-1.6l.6-1.1c-.3-.5-.5-1-.7-1.5l-1.2-.4c-.6-.2-1-.7-1.1-1.3-.1-.7-.1-1.4 0-2.1.1-.6.5-1.1 1.1-1.3l1.2-.4c.2-.5.5-1 .8-1.5l-.5-1.2c-.2-.6 0-1.2.4-1.7.5-.5 1-.9 1.6-1.3.5-.3 1.1-.3 1.6 0l1.1.6c.5-.3 1-.5 1.5-.7l.4-1.2c.2-.6.7-1 1.3-1.1zm1.7 6.4a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7z"/></svg>'
        ),
        array(
            'line1' => 'XE ĐẠP TRẺ EM',
            'line2' => 'AN TOÀN',
            'title' => 'XE ĐẠP TRẺ EM AN TOÀN',
            'svg'   => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="16.5" cy="15.5" r="4"/><circle cx="5.5" cy="17" r="3"/><circle cx="16.5" cy="15.5" r="1.2" fill="currentColor"/><circle cx="5.5" cy="17" r="1" fill="currentColor"/><path d="M5.5 17l5-6.5h4l2 5"/><path d="M9.5 10.5V8.5h3.5"/><path d="M14.5 10.5V6m-3 0h5"/><path d="M16.5 13.5v4"/></svg>'
        ),
        array(
            'line1' => 'DỊCH VỤ BẢO DƯỠNG',
            'line2' => 'MIỄN PHÍ',
            'title' => 'DỊCH VỤ BẢO DƯỠNG MIỄN PHÍ',
            'svg'   => '<svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M22.7 19l-9.1-9.1c.9-2.3.4-5-1.5-6.9-2-2-5-2.4-7.4-1.3L9 6 6 9 1.6 4.7C.5 7.1.9 10.1 2.9 12.1c1.9 1.9 4.6 2.4 6.9 1.5l9.1 9.1c.4.4 1 .4 1.4 0l2.3-2.3c.5-.4.5-1.1.1-1.4z"/><path d="M1.3 19l9.1-9.1c-.9-2.3-.4-5 1.5-6.9 2-2 5-2.4 7.4-1.3L15 6l3 3 4.4-4.3c1.1 2.4.7 5.4-1.3 7.4-1.9 1.9-4.6 2.4-6.9 1.5L5.1 22.7c-.4.4-1 .4-1.4 0L1.4 20.4c-.5-.4-.5-1.1-.1-1.4z"/></svg>'
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
            <div class="row vp-row-custom row-collapse">
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
