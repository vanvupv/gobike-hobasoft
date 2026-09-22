<?php
/**
 * Template Name: Trang Video Review - GoBike
 * Template Post Type: page, video_review
 * 
 * @package Flatsome-Child
 */

get_header();

// Enqueue file JS tương tác Video Review
wp_enqueue_script(
    'gobike-video-review-js',
    get_stylesheet_directory_uri() . '/assets/video-review.js',
    array('jquery'),
    '1.0.0',
    true
);

// 1. QUERY DANH SÁCH VIDEO REVIEW (CPT: video_review)
$video_query = new WP_Query(array(
    'post_type'      => 'video_review',
    'post_status'    => 'publish',
    'posts_per_page' => 12,
    'orderby'        => 'date',
    'order'          => 'DESC',
));

$videos = array();
if ($video_query->have_posts()) {
    while ($video_query->have_posts()) {
        $video_query->the_post();
        $pid = get_the_ID();
        
        $v_url = get_field('video_url', $pid) ?: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
        $v_duration = get_field('video_duration', $pid) ?: '08:15';
        $v_views = get_field('video_views_text', $pid) ?: '12.5K lượt xem • 3 ngày trước';
        $v_thumb = get_the_post_thumbnail_url($pid, 'large');
        if (!$v_thumb) {
            $acf_thumb = get_field('video_thumbnail', $pid);
            if ($acf_thumb) {
                $v_thumb = is_array($acf_thumb) ? $acf_thumb['url'] : $acf_thumb;
            }
        }
        if (!$v_thumb) {
            $v_thumb = 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg';
        }

        $prod_id = get_field('related_product', $pid);
        $prod_url = $prod_id ? get_permalink($prod_id) : home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/');

        $terms = get_the_terms($pid, 'video_category');
        $cat_slugs = '';
        if (!empty($terms) && !is_wp_error($terms)) {
            foreach ($terms as $t) {
                $cat_slugs .= $t->slug . ' ';
            }
        }

        $videos[] = array(
            'id'        => $pid,
            'title'     => get_the_title(),
            'url'       => $v_url,
            'duration'  => $v_duration,
            'views'     => $v_views,
            'thumb'     => $v_thumb,
            'date'      => get_the_date('d/m/Y'),
            'prod_url'  => $prod_url,
            'cat_slugs' => trim($cat_slugs),
        );
    }
    wp_reset_postdata();
}

// Fallback dữ liệu mẫu đẹp mắt nếu CPT chưa có đủ bài viết
if (count($videos) < 6) {
    $fallback_samples = array(
        array(
            'title'    => 'Trải nghiệm thực tế Phoenix C200: Đạp nhẹ hơn, đi xa hơn',
            'url'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration' => '08:15',
            'views'    => '12.5K lượt xem • 3 ngày trước',
            'thumb'    => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
            'date'     => '12/09/2025',
            'prod_url' => home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/'),
            'cat_slugs'=> 'trai-nghiem-thuc-te',
        ),
        array(
            'title'    => 'So sánh Phoenix C200 và Phoenix S1 – Nên chọn màu nào?',
            'url'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration' => '12:04',
            'views'    => '24K lượt xem • 7 ngày trước',
            'thumb'    => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
            'date'     => '05/09/2025',
            'prod_url' => home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/'),
            'cat_slugs'=> 'so-sanh-xe',
        ),
        array(
            'title'    => 'Hướng dẫn lắp đặt xe đạp trợ lực tại nhà',
            'url'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration' => '06:42',
            'views'    => '8.5K lượt xem • 10 ngày trước',
            'thumb'    => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
            'date'     => '01/09/2025',
            'prod_url' => home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/'),
            'cat_slugs'=> 'huong-dan-su-dung',
        ),
        array(
            'title'    => 'Leo dốc cùng ADO A20 – Không còn là nỗi lo',
            'url'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration' => '07:21',
            'views'    => '15K lượt xem • 2 tuần trước',
            'thumb'    => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
            'date'     => '25/08/2025',
            'prod_url' => home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/'),
            'cat_slugs'=> 'trai-nghiem-thuc-te',
        ),
        array(
            'title'    => '5 lưu ý quan trọng khi mua xe đạp trợ lực điện',
            'url'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration' => '09:18',
            'views'    => '18K lượt xem • 2 tuần trước',
            'thumb'    => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
            'date'     => '20/08/2025',
            'prod_url' => home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/'),
            'cat_slugs'=> 'kinh-nghiem-meo-hay',
        ),
        array(
            'title'    => 'Đánh giá chi tiết Phoenix C200: Hiệu năng vượt mong đợi',
            'url'      => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration' => '10:24',
            'views'    => '28K lượt xem • 2 tuần trước',
            'thumb'    => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
            'date'     => '15/08/2025',
            'prod_url' => home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/'),
            'cat_slugs'=> 'trai-nghiem-thuc-te',
        ),
    );
    while (count($videos) < 6) {
        $sample = $fallback_samples[count($videos) % count($fallback_samples)];
        $sample['id'] = 9999 + count($videos);
        $videos[] = $sample;
    }
}

// Video chính (Hero)
$hero_video = $videos[0];
$up_next_videos = array_slice($videos, 1, 5);
$related_videos = array_slice($videos, 0, 6);
?>

<div id="content" role="main" class="content-area gobike-video-review-page">
    <div class="row">
        <div class="col large-12">
            <div class="col-inner">

                <!-- ===================================================================
                     MOBILE HEADER & PILLS SLIDER (ẢNH MOBILE)
                     =================================================================== -->
                <div class="gb-vr-mobile-header">
                    <h1 class="gb-vr-mobile-title">Video Review</h1>
                    <p class="gb-vr-mobile-subtitle">Người thật • Trải nghiệm thật • Đánh giá thật</p>
                    <div class="gb-vr-mobile-pills">
                        <a class="gb-vr-pill-item active" data-cat-slug="all">Tất cả</a>
                        <a class="gb-vr-pill-item" data-cat-slug="trai-nghiem-thuc-te">Trải nghiệm thực tế</a>
                        <a class="gb-vr-pill-item" data-cat-slug="huong-dan-su-dung">Hướng dẫn</a>
                        <a class="gb-vr-pill-item" data-cat-slug="so-sanh-xe">So sánh</a>
                        <a class="gb-vr-pill-item" data-cat-slug="phu-kien">Phụ kiện</a>
                    </div>
                </div>

        <!-- ===================================================================
             SECTION 1: HERO VIDEO PLAYER + SIDEBAR MENU + VIDEO TIẾP THEO (ẢNH 1)
             =================================================================== -->
        <section class="gb-vr-hero-section">
            <div class="gb-vr-hero-grid">

                <!-- 1.1 Cột trái: Sidebar Menu Danh mục Lọc Video (Chỉ hiện Desktop) -->
                <aside class="gb-vr-sidebar">
                    <h2 class="gb-vr-sidebar-title">Video</h2>
                    <nav class="gb-vr-category-nav">
                        <a class="gb-vr-cat-item active" data-cat-slug="all">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                            <span>Tất cả video</span>
                        </a>
                        <a class="gb-vr-cat-item" data-cat-slug="trai-nghiem-thuc-te">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="15 14 20 9 15 4"></polyline><path d="M4 20v-7a4 4 0 0 1 4-4h12"></path></svg>
                            <span>Trải nghiệm thực tế</span>
                        </a>
                        <a class="gb-vr-cat-item" data-cat-slug="huong-dan-su-dung">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                            <span>Hướng dẫn sử dụng</span>
                        </a>
                        <a class="gb-vr-cat-item" data-cat-slug="so-sanh-xe">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                            <span>So sánh xe</span>
                        </a>
                        <a class="gb-vr-cat-item" data-cat-slug="kinh-nghiem-meo-hay">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>
                            <span>Kinh nghiệm – Mẹo hay</span>
                        </a>
                        <a class="gb-vr-cat-item" data-cat-slug="cau-chuyen-khach-hang">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                            <span>Câu chuyện khách hàng</span>
                        </a>
                    </nav>
                </aside>

                <!-- 1.2 Cột giữa: Video Player chính -->
                <main class="gb-vr-main-player">
                    <!-- Breadcrumb (Desktop) -->
                    <div class="gb-vr-breadcrumb hide-for-small">
                        <a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a> &gt; 
                        <a href="<?php echo esc_url(home_url('/video-review/')); ?>">Video</a> &gt; 
                        <span class="current">Trải nghiệm thực tế</span>
                    </div>

                    <!-- Khung Video Player 16:9 -->
                    <div class="gb-vr-player-box">
                        <div class="gb-vr-player-cover" 
                             style="background-image: url('<?php echo esc_url($hero_video['thumb']); ?>');"
                             data-video-url="<?php echo esc_attr($hero_video['url']); ?>">
                            <!-- Badge Nổi Bật (Ảnh Mobile) -->
                            <div class="gb-vr-featured-badge-mobile">NỔI BẬT</div>
                            
                            <div class="gb-vr-big-play-btn">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                            </div>

                            <!-- Overlay tiêu đề và kênh nổi bật góc dưới trên Mobile -->
                            <div class="gb-vr-hero-overlay-mobile">
                                <h2 class="gb-vr-hero-title-mobile"><?php echo esc_html($hero_video['title']); ?></h2>
                                <div class="gb-vr-hero-channel-mobile">
                                    <span class="gb-vr-channel-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg></span>
                                    <span>GoBike Việt Nam • <?php echo esc_html($hero_video['views']); ?></span>
                                </div>
                            </div>

                            <span class="gb-vr-duration-badge-mobile"><?php echo esc_html($hero_video['duration']); ?></span>
                        </div>
                    </div>

                    <!-- Thông tin video chính -->
                    <div class="gb-vr-player-info">
                        <h1 class="gb-vr-player-title"><?php echo esc_html($hero_video['title']); ?></h1>
                        
                        <!-- Thanh Actions bar: Ngày đăng, Lượt xem, Nút Lưu, Nút Chia sẻ, Nút Xem sản phẩm -->
                        <div class="gb-vr-player-actions-bar">
                            <div class="gb-vr-player-meta">
                                <span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    <span class="gb-vr-player-date"><?php echo esc_html($hero_video['date']); ?></span>
                                </span>
                                <span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <span class="gb-vr-player-views"><?php echo esc_html($hero_video['views']); ?></span>
                                </span>
                            </div>

                            <div class="gb-vr-player-btns">
                                <a href="javascript:void(0);" role="button" class="gb-vr-btn-secondary gb-vr-btn-save" data-saved="0">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                    <span>Lưu video</span>
                                </a>
                                <a href="javascript:void(0);" role="button" class="gb-vr-btn-secondary gb-vr-btn-share">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                                    <span>Chia sẻ</span>
                                </a>
                                <a href="<?php echo esc_url($hero_video['prod_url']); ?>" class="gb-vr-btn-primary gb-vr-btn-view-prod">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                    <span>Xem sản phẩm &rarr;</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </main>

                <!-- 1.3 Cột phải: Video Tiếp Theo -->
                <aside class="gb-vr-up-next">
                    <div class="gb-vr-up-next-header">
                        <h3>Video tiếp theo</h3>
                        <label class="gb-vr-autoplay-toggle">
                            <span>Tự động phát</span>
                            <span class="gb-vr-switch">
                                <input type="checkbox" checked>
                                <span class="gb-vr-slider"></span>
                            </span>
                        </label>
                    </div>

                    <div class="gb-vr-up-next-list">
                        <?php foreach ($up_next_videos as $idx => $v): ?>
                            <a href="<?php echo esc_url($v['url']); ?>" 
                               class="gb-vr-up-next-item gb-vr-clickable-video <?php echo $idx === 0 ? 'active' : ''; ?>"
                               data-video-url="<?php echo esc_attr($v['url']); ?>"
                               data-title="<?php echo esc_attr($v['title']); ?>"
                               data-date="<?php echo esc_attr($v['date']); ?>"
                               data-views="<?php echo esc_attr($v['views']); ?>"
                               data-prod-url="<?php echo esc_attr($v['prod_url']); ?>"
                               data-cat-slug="<?php echo esc_attr($v['cat_slugs']); ?>">
                                <div class="gb-vr-up-next-thumb">
                                    <img src="<?php echo esc_url($v['thumb']); ?>" alt="<?php echo esc_attr($v['title']); ?>" loading="lazy">
                                    <span class="gb-vr-duration-badge"><?php echo esc_html($v['duration']); ?></span>
                                </div>
                                <div class="gb-vr-up-next-info">
                                    <h4 class="gb-vr-up-next-title"><?php echo esc_html($v['title']); ?></h4>
                                    <div class="gb-vr-up-next-channel">GoBike</div>
                                    <div class="gb-vr-up-next-meta"><?php echo esc_html($v['views']); ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </aside>

            </div>
        </section>

        <!-- ===================================================================
             SECTION 2: VIDEO LIÊN QUAN (ẢNH 2 - PHẦN TRÊN - 6 CỘT)
             =================================================================== -->
        <section class="gb-vr-related-section">
            <div class="gb-vr-section-header">
                <div class="gb-vr-header-left">
                    <h2 class="gb-vr-sec-title">Video liên quan</h2>
                </div>
                <a href="<?php echo esc_url(home_url('/video-review/')); ?>" class="gb-vr-view-all">Xem tất cả &rarr;</a>
            </div>

            <div class="gb-vr-related-grid">
                <?php foreach ($related_videos as $v): ?>
                    <div class="gb-vr-video-card gb-vr-clickable-video"
                         data-video-url="<?php echo esc_attr($v['url']); ?>"
                         data-title="<?php echo esc_attr($v['title']); ?>"
                         data-date="<?php echo esc_attr($v['date']); ?>"
                         data-views="<?php echo esc_attr($v['views']); ?>"
                         data-prod-url="<?php echo esc_attr($v['prod_url']); ?>"
                         data-cat-slug="<?php echo esc_attr($v['cat_slugs']); ?>">
                        <div class="gb-vr-video-thumb-wrap">
                            <img src="<?php echo esc_url($v['thumb']); ?>" alt="<?php echo esc_attr($v['title']); ?>" loading="lazy">
                            <span class="gb-vr-duration-badge"><?php echo esc_html($v['duration']); ?></span>
                            <div class="gb-vr-play-overlay">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                            </div>
                        </div>
                        <div class="gb-vr-card-info">
                            <h3 class="gb-vr-card-title"><?php echo esc_html($v['title']); ?></h3>
                            <div class="gb-vr-card-channel">GoBike</div>
                            <div class="gb-vr-card-meta"><?php echo esc_html($v['views']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ===================================================================
             SECTION 3: DANH SÁCH PHÁT NỔI BẬT (ẢNH 2 - PHẦN DƯỚI - 5 CỘT)
             =================================================================== -->
        <section class="gb-vr-playlist-section">
            <div class="gb-vr-section-header">
                <div class="gb-vr-header-left">
                    <h2 class="gb-vr-sec-title">Danh sách phát nổi bật</h2>
                    <p class="gb-vr-sec-subtitle">Khám phá các chủ đề video được nhiều người quan tâm nhất về xe đạp điện GoBike.</p>
                </div>
                <a href="<?php echo esc_url(home_url('/video-review/')); ?>" class="gb-vr-view-all">Xem tất cả &rarr;</a>
            </div>

            <div class="gb-vr-playlist-grid">
                <?php
                $playlists = array(
                    array(
                        'title' => 'Trải nghiệm thực tế',
                        'desc'  => 'Những hành trình thật, cảm xúc thật cùng xe đạp điện GoBike',
                        'count' => '12 video',
                        'thumb' => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
                        'slug'  => 'trai-nghiem-thuc-te',
                    ),
                    array(
                        'title' => 'Hướng dẫn sử dụng',
                        'desc'  => 'Hướng dẫn chi tiết từ A-Z giúp bạn sử dụng xe hiệu quả và an toàn',
                        'count' => '8 video',
                        'thumb' => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
                        'slug'  => 'huong-dan-su-dung',
                    ),
                    array(
                        'title' => 'So sánh xe',
                        'desc'  => 'So sánh chi tiết các dòng xe GoBike để chọn lựa phù hợp nhất',
                        'count' => '10 video',
                        'thumb' => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
                        'slug'  => 'so-sanh-xe',
                    ),
                    array(
                        'title' => 'Kinh nghiệm – Mẹo hay',
                        'desc'  => 'Mẹo vặt, kinh nghiệm hữu ích cho người yêu xe đạp điện',
                        'count' => '7 video',
                        'thumb' => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
                        'slug'  => 'kinh-nghiem-meo-hay',
                    ),
                    array(
                        'title' => 'Câu chuyện khách hàng',
                        'desc'  => 'Chia sẻ thật từ những khách hàng đã trải nghiệm GoBike',
                        'count' => '6 video',
                        'thumb' => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
                        'slug'  => 'cau-chuyen-khach-hang',
                    ),
                );

                foreach ($playlists as $pl):
                ?>
                    <div class="gb-vr-playlist-card">
                        <div class="gb-vr-playlist-thumb">
                            <img src="<?php echo esc_url($pl['thumb']); ?>" alt="<?php echo esc_attr($pl['title']); ?>" loading="lazy">
                            <span class="gb-vr-playlist-count-badge">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M19 15V9a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2zM3 13h2v-2H3v2zm18 0h-2v-2h2v2z"></path></svg>
                                <?php echo esc_html($pl['count']); ?>
                            </span>
                        </div>
                        <div class="gb-vr-playlist-body">
                            <h3 class="gb-vr-playlist-title"><?php echo esc_html($pl['title']); ?></h3>
                            <p class="gb-vr-playlist-desc"><?php echo esc_html($pl['desc']); ?></p>
                            <a href="<?php echo esc_url(home_url('/video-review/?cat=' . $pl['slug'])); ?>" class="gb-vr-playlist-btn">Xem danh sách &rarr;</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- ===================================================================
             SECTION 4: SẢN PHẨM NỔI BẬT TRONG VIDEO (ẢNH 3 - 4 CỘT)
             =================================================================== -->
        <section class="gb-vr-products-section">
            <div class="gb-vr-section-header">
                <div class="gb-vr-header-left">
                    <h2 class="gb-vr-sec-title">Sản phẩm nổi bật trong video</h2>
                    <p class="gb-vr-sec-subtitle">Khám phá ngay những mẫu xe đạp điện được giới thiệu trong các video của GoBike.</p>
                </div>
                <a href="<?php echo esc_url(home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/')); ?>" class="gb-vr-view-all">Xem tất cả sản phẩm &rarr;</a>
            </div>

            <div class="gb-vr-products-grid">
                <?php
                // Query 4 sản phẩm WooCommerce
                $prod_query = new WP_Query(array(
                    'post_type'      => 'product',
                    'post_status'    => 'publish',
                    'posts_per_page' => 4,
                    'orderby'        => 'menu_order title',
                    'order'          => 'ASC',
                ));

                $sample_specs = array(
                    array('slogan' => 'Đạp nhẹ hơn – Đi xa hơn', 'km' => 'Lên tới 100km', 'pin' => '48V - 15Ah', 'discount' => '-12%'),
                    array('slogan' => 'Thành phố trong tầm tay', 'km' => 'Lên tới 80km', 'pin' => '36V - 12Ah', 'discount' => '-11%'),
                    array('slogan' => 'Nhỏ gọn – Mạnh mẽ', 'km' => 'Lên tới 70km', 'pin' => '36V - 10Ah', 'discount' => '-17%'),
                    array('slogan' => 'Bứt phá mọi hành trình', 'km' => 'Lên tới 120km', 'pin' => '48V - 17.5Ah', 'discount' => '-17%'),
                );

                $prod_idx = 0;
                if ($prod_query->have_posts()):
                    while ($prod_query->have_posts()): $prod_query->the_post();
                        global $product;
                        $spec = $sample_specs[$prod_idx % 4];
                        $price_sale = $product->get_price_html();
                        $regular_price = wc_price($product->get_regular_price());
                        $sale_price = wc_price($product->get_sale_price() ?: $product->get_price());
                        $discount_badge = $product->is_on_sale() && $product->get_regular_price() ? '-' . round((($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100) . '%' : $spec['discount'];
                ?>
                        <div class="gb-vr-product-card">
                            <div class="gb-vr-prod-thumb-wrap">
                                <?php echo woocommerce_get_product_thumbnail('medium'); ?>
                            </div>
                            <h3 class="gb-vr-prod-title"><?php the_title(); ?></h3>
                            <div class="gb-vr-prod-slogan"><?php echo esc_html($spec['slogan']); ?></div>

                            <!-- 2 Thông số kỹ thuật -->
                            <div class="gb-vr-prod-specs">
                                <div class="gb-vr-spec-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <div class="gb-vr-spec-text">
                                        <span class="gb-vr-spec-label">Quãng đường</span>
                                        <span class="gb-vr-spec-val"><?php echo esc_html($spec['km']); ?></span>
                                    </div>
                                </div>
                                <div class="gb-vr-spec-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="16" height="12" rx="2"></rect><line x1="22" y1="11" x2="22" y2="15"></line></svg>
                                    <div class="gb-vr-spec-text">
                                        <span class="gb-vr-spec-label">Pin lithium</span>
                                        <span class="gb-vr-spec-val"><?php echo esc_html($spec['pin']); ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Giá bán -->
                            <div class="gb-vr-prod-pricing">
                                <span class="gb-vr-price-current"><?php echo $sale_price; ?></span>
                                <?php if ($product->is_on_sale()): ?>
                                    <span class="gb-vr-price-old"><?php echo $regular_price; ?></span>
                                <?php endif; ?>
                                <span class="gb-vr-discount-badge"><?php echo esc_html($discount_badge); ?></span>
                            </div>

                            <a href="<?php the_permalink(); ?>" class="gb-vr-btn-view-product">Xem chi tiết &rarr;</a>
                        </div>
                <?php
                        $prod_idx++;
                    endwhile;
                    wp_reset_postdata();
                else:
                    // Fallback sản phẩm chuẩn ảnh 3
                    $fallback_products = array(
                        array('title' => 'Phoenix C200', 'slogan' => 'Đạp nhẹ hơn – Đi xa hơn', 'km' => 'Lên tới 100km', 'pin' => '48V - 15Ah', 'price' => '17.990.000₫', 'old' => '20.490.000₫', 'disc' => '-12%'),
                        array('title' => 'Phoenix S1', 'slogan' => 'Thành phố trong tầm tay', 'km' => 'Lên tới 80km', 'pin' => '36V - 12Ah', 'price' => '16.990.000₫', 'old' => '18.990.000₫', 'disc' => '-11%'),
                        array('title' => 'ADO A20', 'slogan' => 'Nhỏ gọn – Mạnh mẽ', 'km' => 'Lên tới 70km', 'pin' => '36V - 10Ah', 'price' => '14.990.000₫', 'old' => '17.990.000₫', 'disc' => '-17%'),
                        array('title' => 'Shengmilo MX06', 'slogan' => 'Bứt phá mọi hành trình', 'km' => 'Lên tới 120km', 'pin' => '48V - 17.5Ah', 'price' => '22.990.000₫', 'old' => '29.990.000₫', 'disc' => '-17%'),
                    );
                    foreach ($fallback_products as $fp):
                ?>
                        <div class="gb-vr-product-card">
                            <div class="gb-vr-prod-thumb-wrap">
                                <img src="https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg" alt="<?php echo esc_attr($fp['title']); ?>">
                            </div>
                            <h3 class="gb-vr-prod-title"><?php echo esc_html($fp['title']); ?></h3>
                            <div class="gb-vr-prod-slogan"><?php echo esc_html($fp['slogan']); ?></div>

                            <div class="gb-vr-prod-specs">
                                <div class="gb-vr-spec-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <div class="gb-vr-spec-text">
                                        <span class="gb-vr-spec-label">Quãng đường</span>
                                        <span class="gb-vr-spec-val"><?php echo esc_html($fp['km']); ?></span>
                                    </div>
                                </div>
                                <div class="gb-vr-spec-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="16" height="12" rx="2"></rect><line x1="22" y1="11" x2="22" y2="15"></line></svg>
                                    <div class="gb-vr-spec-text">
                                        <span class="gb-vr-spec-label">Pin lithium</span>
                                        <span class="gb-vr-spec-val"><?php echo esc_html($fp['pin']); ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="gb-vr-prod-pricing">
                                <span class="gb-vr-price-current"><?php echo esc_html($fp['price']); ?></span>
                                <span class="gb-vr-price-old"><?php echo esc_html($fp['old']); ?></span>
                                <span class="gb-vr-discount-badge"><?php echo esc_html($fp['disc']); ?></span>
                            </div>

                            <a href="<?php echo esc_url(home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/')); ?>" class="gb-vr-btn-view-product">Xem chi tiết &rarr;</a>
                        </div>
                <?php
                    endforeach;
                endif;
                ?>
            </div>
        </section>

        <!-- ===================================================================
             SECTION 5: HERO BANNER ĐẶT LỊCH + NGƯỜI THẬT - VIỆC THẬT (ẢNH 4)
             =================================================================== -->
        <section class="gb-vr-booking-experience-section">
            <!-- 5.1 Hero Banner Đặt Lịch Lái Thử -->
            <div class="gb-vr-booking-banner">
                <div class="gb-vr-booking-left">
                    <div class="gb-vr-booking-icon-wrap">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    </div>
                    <div class="gb-vr-booking-info">
                        <h3>Đặt lịch lái thử tại showroom GoBike</h3>
                        <p>Trải nghiệm thực tế – Cảm nhận khác biệt</p>
                        <div class="gb-vr-booking-checklist">
                            <div class="gb-vr-check-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Trải nghiệm miễn phí</span>
                            </div>
                            <div class="gb-vr-check-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Nhiều mẫu xe để lựa chọn</span>
                            </div>
                            <div class="gb-vr-check-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Được tư vấn 1-1</span>
                            </div>
                            <div class="gb-vr-check-item">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                <span>Hỗ trợ đặt lịch nhanh chóng</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="gb-vr-booking-center-img">
                    <img src="https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg" alt="Đặt lịch lái thử GoBike" loading="lazy">
                </div>

                <div class="gb-vr-booking-right">
                    <a href="<?php echo esc_url(home_url('/dat-lich-lai-thu/')); ?>" class="gb-vr-btn-booking">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        <span>Đặt lịch ngay &rarr;</span>
                    </a>
                    <div class="gb-vr-booking-hotline">
                        Hoặc gọi ngay <strong>0944 988 699</strong>
                    </div>
                </div>
            </div>

            <!-- 5.2 Khối CPT: Người thật - Xe thật - Trải nghiệm thật -->
            <div class="gb-vr-experience-block">
                <div class="gb-vr-section-header">
                    <div class="gb-vr-header-left">
                        <h2 class="gb-vr-sec-title">Người thật - việc thật - trải nghiệm thật</h2>
                        <p class="gb-vr-sec-subtitle">Cùng lắng nghe chia sẻ từ những khách hàng đã và đang sử dụng xe đạp điện GoBike.</p>
                    </div>
                    <a href="<?php echo esc_url(home_url('/trai-nghiem-khach-hang/')); ?>" class="gb-vr-view-all">Xem tất cả &rarr;</a>
                </div>

                <div class="gb-vr-experience-grid">
                    <?php
                    // Query CPT customer_experience
                    $exp_query = new WP_Query(array(
                        'post_type'      => 'customer_experience',
                        'post_status'    => 'publish',
                        'posts_per_page' => 4,
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                    ));

                    if ($exp_query->have_posts()):
                        while ($exp_query->have_posts()): $exp_query->the_post();
                            $epid = get_the_ID();
                            $e_url = get_field('video_url', $epid) ?: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
                            $e_duration = get_field('video_duration', $epid) ?: '08:20';
                            $e_name = get_field('customer_name', $epid) ?: 'Khách hàng GoBike';
                            $e_city = get_field('customer_city', $epid) ?: 'Hà Nội';
                            $e_views = get_field('video_views', $epid) ?: '12K lượt xem • 1 tháng trước';
                            $e_thumb = get_the_post_thumbnail_url($epid, 'medium_large') ?: 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg';
                    ?>
                            <div class="gb-vr-exp-card gb-vr-clickable-video"
                                 data-video-url="<?php echo esc_attr($e_url); ?>"
                                 data-title="<?php echo esc_attr(get_the_title()); ?>"
                                 data-date="<?php echo esc_attr(get_the_date('d/m/Y')); ?>"
                                 data-views="<?php echo esc_attr($e_views); ?>"
                                 data-prod-url="<?php echo esc_url(home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/')); ?>">
                                <div class="gb-vr-video-thumb-wrap">
                                    <img src="<?php echo esc_url($e_thumb); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                                    <span class="gb-vr-duration-badge"><?php echo esc_html($e_duration); ?></span>
                                    <div class="gb-vr-play-overlay">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                    </div>
                                </div>
                                <div class="gb-vr-exp-info">
                                    <h3 class="gb-vr-exp-title"><?php the_title(); ?></h3>
                                    <div class="gb-vr-exp-author"><?php echo esc_html($e_name . ' – ' . $e_city); ?></div>
                                    <div class="gb-vr-exp-meta"><?php echo esc_html($e_views); ?></div>
                                </div>
                            </div>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    else:
                        // Fallback dữ liệu mẫu chuẩn Ảnh 4
                        $fallback_exp = array(
                            array('title' => 'Hành trình thay đổi cuộc sống nhờ xe đạp điện GoBike', 'author' => 'Anh Minh – Hà Nội', 'duration' => '09:12', 'views' => '12K lượt xem • 1 tháng trước'),
                            array('title' => 'Đi làm mỗi ngày với GoBike – Tiết kiệm và khỏe hơn', 'author' => 'Chị Lan – TP.HCM', 'duration' => '06:46', 'views' => '9.5K lượt xem • 1 tháng trước'),
                            array('title' => 'Từ ô tô sang xe đạp điện – Quyết định đúng đắn', 'author' => 'Anh Hoàng – Đà Nẵng', 'duration' => '08:20', 'views' => '15K lượt xem • 2 tháng trước'),
                            array('title' => 'GoBike – Người bạn đồng hành tuyệt vời', 'author' => 'Chị Hương – Hải Phòng', 'duration' => '07:36', 'views' => '11K lượt xem • 2 tháng trước'),
                        );
                        foreach ($fallback_exp as $fe):
                    ?>
                            <div class="gb-vr-exp-card gb-vr-clickable-video"
                                 data-video-url="https://www.youtube.com/watch?v=dQw4w9WgXcQ"
                                 data-title="<?php echo esc_attr($fe['title']); ?>"
                                 data-date="10/08/2025"
                                 data-views="<?php echo esc_attr($fe['views']); ?>"
                                 data-prod-url="<?php echo esc_url(home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/')); ?>">
                                <div class="gb-vr-video-thumb-wrap">
                                    <img src="https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg" alt="<?php echo esc_attr($fe['title']); ?>" loading="lazy">
                                    <span class="gb-vr-duration-badge"><?php echo esc_html($fe['duration']); ?></span>
                                    <div class="gb-vr-play-overlay">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                    </div>
                                </div>
                                <div class="gb-vr-exp-info">
                                    <h3 class="gb-vr-exp-title"><?php echo esc_html($fe['title']); ?></h3>
                                    <div class="gb-vr-exp-author"><?php echo esc_html($fe['author']); ?></div>
                                    <div class="gb-vr-exp-meta"><?php echo esc_html($fe['views']); ?></div>
                                </div>
                            </div>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>
        </section>

        <!-- ===================================================================
             SECTION 6: HỆ THỐNG SHOWROOM GOBIKE (ẢNH 5 - 4 CARD + 1 BẢN ĐỒ)
             =================================================================== -->
        <section class="gb-vr-showroom-section">
            <div class="gb-vr-section-header">
                <div class="gb-vr-header-left">
                    <h2 class="gb-vr-sec-title">Hệ thống showroom GoBike</h2>
                    <p class="gb-vr-sec-subtitle">Trải nghiệm trực tiếp sản phẩm tại các showroom trên toàn quốc.</p>
                </div>
                <a href="<?php echo esc_url(home_url('/lien-he/')); ?>" class="gb-vr-view-all">Xem tất cả showroom &rarr;</a>
            </div>

            <div class="gb-vr-showroom-grid">
                <!-- Showroom 1: Hà Nội -->
                <div class="gb-vr-showroom-card">
                    <div class="gb-vr-sr-header">
                        <svg class="gb-vr-sr-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <div>
                            <h3 class="gb-vr-sr-name">GoBike Hà Nội</h3>
                            <p class="gb-vr-sr-addr">Số 123 Đường Láng, Q. Đống Đa, Hà Nội</p>
                        </div>
                    </div>
                    <div class="gb-vr-sr-details">
                        <div class="gb-vr-sr-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            <span>0944 988 699</span>
                        </div>
                        <div class="gb-vr-sr-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>8:00 - 21:00 (Thứ 2 - Chủ nhật)</span>
                        </div>
                    </div>
                </div>

                <!-- Showroom 2: TP.HCM -->
                <div class="gb-vr-showroom-card">
                    <div class="gb-vr-sr-header">
                        <svg class="gb-vr-sr-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <div>
                            <h3 class="gb-vr-sr-name">GoBike TP.HCM</h3>
                            <p class="gb-vr-sr-addr">Số 456 Nguyễn Thị Minh Khai, Q.3, TP.HCM</p>
                        </div>
                    </div>
                    <div class="gb-vr-sr-details">
                        <div class="gb-vr-sr-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            <span>0933 123 678</span>
                        </div>
                        <div class="gb-vr-sr-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>8:00 - 21:00 (Thứ 2 - Chủ nhật)</span>
                        </div>
                    </div>
                </div>

                <!-- Showroom 3: Đà Nẵng -->
                <div class="gb-vr-showroom-card">
                    <div class="gb-vr-sr-header">
                        <svg class="gb-vr-sr-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <div>
                            <h3 class="gb-vr-sr-name">GoBike Đà Nẵng</h3>
                            <p class="gb-vr-sr-addr">Số 789 Nguyễn Văn Linh, Q. Hải Châu, Đà Nẵng</p>
                        </div>
                    </div>
                    <div class="gb-vr-sr-details">
                        <div class="gb-vr-sr-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            <span>0905 456 879</span>
                        </div>
                        <div class="gb-vr-sr-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>8:00 - 21:00 (Thứ 2 - Chủ nhật)</span>
                        </div>
                    </div>
                </div>

                <!-- Showroom 4: Hải Phòng -->
                <div class="gb-vr-showroom-card">
                    <div class="gb-vr-sr-header">
                        <svg class="gb-vr-sr-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        <div>
                            <h3 class="gb-vr-sr-name">GoBike Hải Phòng</h3>
                            <p class="gb-vr-sr-addr">Số 321 Lê Hồng Phong, Q. Ngô Quyền, Hải Phòng</p>
                        </div>
                    </div>
                    <div class="gb-vr-sr-details">
                        <div class="gb-vr-sr-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            <span>0912 345 678</span>
                        </div>
                        <div class="gb-vr-sr-item">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>8:00 - 21:00 (Thứ 2 - Chủ nhật)</span>
                        </div>
                    </div>
                </div>

                <!-- Card thứ 5: Bản đồ toàn quốc -->
                <div class="gb-vr-showroom-map-card">
                    <img class="gb-vr-map-bg" src="https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg" alt="Bản đồ showroom GoBike" loading="lazy">
                    <a href="<?php echo esc_url(home_url('/lien-he/')); ?>" class="gb-vr-map-overlay-btn">
                        <span>Xem bản đồ toàn quốc &rarr;</span>
                    </a>
                </div>
            </div>
        </section>

            </div><!-- .col-inner -->
        </div><!-- .col.large-12 -->
    </div><!-- .row -->
</div><!-- #content.content-area -->

<?php get_footer(); ?>

