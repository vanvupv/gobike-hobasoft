<?php
/**
 * Template Name: Người Thật Xe Thật - Trải Nghiệm Thật
 * Template Post Type: page
 * 
 * Trang Cộng Đồng GoBike: Trải Nghiệm Thực Tế Từ Người Dùng
 * Tuân thủ nghiêm ngặt quy chuẩn:
 * - Font-weight tối đa 700.
 * - Mũi tên chống rớt dòng (white-space: nowrap !important).
 * - Khung hình card dọc tỷ lệ Shorts chuẩn, không méo ảnh.
 * - Không dùng style inline bừa bãi.
 * 
 * @package Flatsome-Child
 */

get_header();

// 1. Lấy dữ liệu bài viết từ CPT customer_experience (nếu có)
$db_experiences = array();
$ce_query = new WP_Query(array(
    'post_type'      => 'customer_experience',
    'posts_per_page' => 12,
    'post_status'    => 'publish',
));

if ($ce_query->have_posts()) {
    while ($ce_query->have_posts()) {
        $ce_query->the_post();
        $p_id = get_the_ID();
        $thumb = get_the_post_thumbnail_url($p_id, 'large') ?: 'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=600&auto=format&fit=crop&q=80';
        $v_url = get_post_meta($p_id, 'video_url', true) ?: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
        $v_views = get_post_meta($p_id, 'video_views', true) ?: '10K lượt xem';
        $c_name = get_post_meta($p_id, 'customer_name', true) ?: 'Khách hàng GoBike';
        $c_city = get_post_meta($p_id, 'customer_city', true) ?: 'Hà Nội';
        $v_quote = get_post_meta($p_id, 'quote_text', true) ?: get_the_title();
        
        $db_experiences[] = array(
            'id'       => $p_id,
            'title'    => get_the_title(),
            'quote'    => $v_quote,
            'location' => $c_city,
            'views'    => $v_views,
            'thumb'    => $thumb,
            'video_url'=> $v_url,
            'cat'      => 'all dilam',
        );
    }
    wp_reset_postdata();
}

// 2. Dữ liệu mẫu chuẩn 100% theo ảnh thiết kế
$default_stories = array(
    array(
        'quote'    => 'Đi làm mỗi ngày nhẹ nhàng hơn',
        'title'    => 'Đi làm mỗi ngày nhẹ nhàng hơn rất nhiều',
        'location' => 'Hà Nội',
        'views'    => '125K',
        'thumb'    => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=600&auto=format&fit=crop&q=80',
        'video_url'=> 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'cats'     => 'dilam',
    ),
    array(
        'quote'    => '60 Tuổi vẫn thấy mình trẻ',
        'title'    => 'Tuổi già chỉ là con số',
        'location' => 'Đà Nẵng',
        'views'    => '98K',
        'thumb'    => 'https://images.unsplash.com/photo-1507035895480-2b3156c31fc8?w=600&auto=format&fit=crop&q=80',
        'video_url'=> 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'cats'     => 'nguoilon',
    ),
    array(
        'quote'    => 'Cùng nhau đi xa hơn',
        'title'    => 'Hai vợ chồng và những chuyến đi cuối tuần',
        'location' => 'Nha Trang',
        'views'    => '43K',
        'thumb'    => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=600&auto=format&fit=crop&q=80',
        'video_url'=> 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'cats'     => 'giadinh dulich',
    ),
    array(
        'quote'    => 'Đến trường không còn là áp lực',
        'title'    => 'Đón niềm vui chiều xe màu xám sành điệu',
        'location' => 'TP. Hồ Chí Minh',
        'views'    => '45K',
        'thumb'    => 'https://images.unsplash.com/photo-1532298229144-0ec0c57515c7?w=600&auto=format&fit=crop&q=80',
        'video_url'=> 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'cats'     => 'hocsinh',
    ),
    array(
        'quote'    => 'Tự do là có thật',
        'title'    => 'Một mình, một cung đường',
        'location' => 'Đà Lạt',
        'views'    => '115K',
        'thumb'    => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=600&auto=format&fit=crop&q=80',
        'video_url'=> 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'cats'     => 'dulich thethao',
    ),
    array(
        'quote'    => 'Những buổi chiều thảnh thơi',
        'title'    => 'Đưa con đi học mỗi ngày',
        'location' => 'Hải Phòng',
        'views'    => '77K',
        'thumb'    => 'https://images.unsplash.com/photo-1576435728678-68d0fbf94e91?w=600&auto=format&fit=crop&q=80',
        'video_url'=> 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'cats'     => 'giadinh dilam',
    ),
    array(
        'quote'    => 'Thử thách để mạnh mẽ hơn',
        'title'    => 'Chinh phục đỉnh đèo cùng GoBike',
        'location' => 'Hà Giang',
        'views'    => '65K',
        'thumb'    => 'https://images.unsplash.com/photo-1517649763962-0c623266ddc0?w=600&auto=format&fit=crop&q=80',
        'video_url'=> 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'cats'     => 'thethao dulich',
    ),
    array(
        'quote'    => 'Sống xanh để lưu lại hôm nay',
        'title'    => 'Chọn GoBike, chọn lối sống xanh',
        'location' => 'Cần Thơ',
        'views'    => '52K',
        'thumb'    => 'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=600&auto=format&fit=crop&q=80',
        'video_url'=> 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'cats'     => 'dilam',
    ),
    array(
        'quote'    => 'Không phải điểm đến mà là hành trình',
        'title'    => 'Vì những cung đường chưa qua trước!',
        'location' => 'Quảng Bình',
        'views'    => '39K',
        'thumb'    => 'https://images.unsplash.com/photo-1502680390469-be75c86b636f?w=600&auto=format&fit=crop&q=80',
        'video_url'=> 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'cats'     => 'dulich',
    ),
    array(
        'quote'    => 'Xe tốt, cuộc sống tốt hơn',
        'title'    => 'Cảm ơn GoBike đã đồng hành',
        'location' => 'Huế',
        'views'    => '58K',
        'thumb'    => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=600&auto=format&fit=crop&q=80',
        'video_url'=> 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'cats'     => 'nguoilon',
    ),
    array(
        'quote'    => 'Bạn bè trên mỗi hành trình đẹp',
        'title'    => 'Hành trình cùng những người bạn tuyệt vời',
        'location' => 'Mộc Châu',
        'views'    => '88K',
        'thumb'    => 'https://images.unsplash.com/photo-1517649763962-0c623266ddc0?w=600&auto=format&fit=crop&q=80',
        'video_url'=> 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'cats'     => 'thethao dulich',
    ),
    array(
        'quote'    => 'Việt Nam đẹp hơn trên yên xe',
        'title'    => 'Khám phá Việt Nam theo cách riêng',
        'location' => 'Hòa Bình',
        'views'    => '87K',
        'thumb'    => 'https://images.unsplash.com/photo-1501555088652-021faa106b9b?w=600&auto=format&fit=crop&q=80',
        'video_url'=> 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'cats'     => 'dulich',
    ),
);

// Nếu có bài trong database thì đưa lên đầu, sau đó ghép dữ liệu mẫu để đủ 12 cards
$stories_to_display = array();
if (!empty($db_experiences)) {
    $stories_to_display = $db_experiences;
    $remain = 12 - count($stories_to_display);
    if ($remain > 0) {
        $stories_to_display = array_merge($stories_to_display, array_slice($default_stories, 0, $remain));
    }
} else {
    $stories_to_display = $default_stories;
}
?>

<div class="gb-ce-page-wrapper">

    <!-- ==========================================================================
         SECTION 1: HERO BANNER FULL-WIDTH (CỘNG ĐỒNG GOBIKE)
         ========================================================================== -->
    <section class="gb-ce-hero-section">
        <!-- Ảnh nền Hero toàn cảnh trải rộng 100% -->
        <div class="gb-ce-hero-bg-wrap">
            <img class="gb-ce-hero-bg-img" src="https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=1920&auto=format&fit=crop&q=85" alt="Cộng đồng GoBike - Người thật, xe thật">
            <div class="gb-ce-hero-bg-overlay"></div>
        </div>

        <!-- Inner container căn giữa 1240px để thẳng lề với trang bên dưới -->
        <div class="gb-ce-hero-inner">
            
            <!-- Breadcrumb nằm góc trên bên trái của Hero -->
            <nav class="gb-ce-breadcrumb-wrap" aria-label="Breadcrumb">
                <ol class="gb-ce-breadcrumb">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a></li>
                    <li class="sep">&rsaquo;</li>
                    <li class="current">Người thật, xe thật</li>
                </ol>
            </nav>

            <!-- Nội dung chính của Hero bên trái -->
            <div class="gb-ce-hero-content">
                <div class="gb-ce-tag-pill">
                    <span>&lt;&gt;</span> CỘNG ĐỒNG GOBIKE
                </div>
                <h1 class="gb-ce-hero-title">
                    Người thật, xe thật
                    <span class="gb-ce-highlight">Trải nghiệm thật</span>
                </h1>
                <p class="gb-ce-hero-desc">
                    Những câu chuyện đời thường, hành trình thật và cảm xúc chân thật từ cộng đồng GoBike trên khắp Việt Nam.
                </p>

                <!-- 3 Chỉ số thống kê (Stats Counter) -->
                <div class="gb-ce-stats-row">
                    <div class="gb-ce-stat-item">
                        <div class="gb-ce-stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div class="gb-ce-stat-info">
                            <span class="gb-ce-stat-number">1.200+</span>
                            <span class="gb-ce-stat-label">Câu chuyện đã chia sẻ</span>
                        </div>
                    </div>

                    <div class="gb-ce-stat-item">
                        <div class="gb-ce-stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </div>
                        <div class="gb-ce-stat-info">
                            <span class="gb-ce-stat-number">63</span>
                            <span class="gb-ce-stat-label">Tỉnh thành</span>
                        </div>
                    </div>

                    <div class="gb-ce-stat-item">
                        <div class="gb-ce-stat-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                        </div>
                        <div class="gb-ce-stat-info">
                            <span class="gb-ce-stat-number">Triệu+</span>
                            <span class="gb-ce-stat-label">Cảm hứng mỗi ngày</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dòng chữ viết tay nghệ thuật phía bên phải banner -->
            <div class="gb-ce-hero-quote-art">
                Một hành trình, một câu chuyện
            </div>

        </div>
    </section>

    <!-- CONTAINER CHO CÁC SECTION BÊN DƯỚI -->
    <div class="gb-ce-container">

        <!-- ==========================================================================
             SECTION 2: BỘ LỌC CHỦ ĐỀ & SẮP XẾP (FILTER BAR)
             ========================================================================== -->
        <section class="gb-ce-filter-section">
            <div class="gb-ce-filter-bar">
                
                <!-- Danh mục nút bấm (Filter Tabs) -->
                <div class="gb-ce-filter-tabs" id="gbCeFilterTabs">
                    <button class="gb-ce-tab-btn active" data-filter="all">Tất cả</button>
                    <button class="gb-ce-tab-btn" data-filter="dilam">Đi làm hàng ngày</button>
                    <button class="gb-ce-tab-btn" data-filter="dulich">Du lịch</button>
                    <button class="gb-ce-tab-btn" data-filter="thethao">Thể thao</button>
                    <button class="gb-ce-tab-btn" data-filter="giadinh">Gia đình</button>
                    <button class="gb-ce-tab-btn" data-filter="hocsinh">Học sinh - sinh viên</button>
                    <button class="gb-ce-tab-btn" data-filter="nguoilon">Người lớn tuổi</button>
                </div>

                <!-- Dropdown Sắp xếp -->
                <div class="gb-ce-sort-dropdown-wrap">
                    <select id="gbCeSortSelect" aria-label="Sắp xếp danh sách câu chuyện">
                        <option value="newest">Mới nhất ▾</option>
                        <option value="views">Xem nhiều nhất</option>
                        <option value="featured">Nổi bật nhất</option>
                    </select>
                </div>

            </div>
        </section>

        <!-- ==========================================================================
             SECTION 3: LƯỚI CARD CÂU CHUYỆN NỔI BẬT (6 CỘT - SHORTS STYLE)
             ========================================================================== -->
        <section class="gb-ce-stories-section">
            
            <!-- Tiêu đề Section & Link Xem tất cả (Quy chuẩn chống rớt dòng) -->
            <div class="gb-ce-sec-header">
                <div class="gb-ce-sec-title-wrap">
                    <span class="gb-ce-sec-icon-leaf">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17 8C8 10 5.9 16.17 3.82 21.34L5.71 22l1-2.3A4.49 4.49 0 0 0 8 20C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/>
                        </svg>
                    </span>
                    <h2 class="gb-ce-sec-title">Câu chuyện nổi bật</h2>
                </div>
                <a href="<?php echo esc_url(home_url('/trai-nghiem-khach-hang/')); ?>" class="gb-ce-link-all">
                    Xem tất cả &rarr;
                </a>
            </div>

            <!-- Grid 6 Cột x 2 Hàng = 12 Cards -->
            <div class="gb-ce-stories-grid" id="gbCeStoriesGrid">
                <?php foreach ($stories_to_display as $story): ?>
                    <div class="gb-ce-card" data-cats="<?php echo esc_attr(isset($story['cats']) ? $story['cats'] : 'dilam'); ?>" data-video="<?php echo esc_url($story['video_url']); ?>" data-title="<?php echo esc_attr($story['title']); ?>" data-location="<?php echo esc_attr($story['location']); ?>">
                        
                        <!-- Khung ảnh dọc Shorts 9:16 -->
                        <div class="gb-ce-card-thumb-wrap">
                            <img class="gb-ce-card-img" src="<?php echo esc_url($story['thumb']); ?>" alt="<?php echo esc_attr($story['title']); ?>" loading="lazy">
                            <div class="gb-ce-card-overlay"></div>

                            <!-- Câu quote viết tay nghệ thuật phía trên -->
                            <div class="gb-ce-card-quote">
                                <?php echo esc_html($story['quote']); ?>
                            </div>

                            <!-- Menu 3 chấm -->
                            <span class="gb-ce-card-dots" title="Tùy chọn">&#8942;</span>

                            <!-- Nút Play tròn ở giữa -->
                            <div class="gb-ce-card-play-btn">
                                <svg viewBox="0 0 24 24" fill="currentColor">
                                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                </svg>
                            </div>

                            <!-- Badge lượt xem góc dưới -->
                            <div class="gb-ce-card-views">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                <span><?php echo esc_html($story['views']); ?></span>
                            </div>
                        </div>

                        <!-- Thông tin chữ bên dưới card -->
                        <div class="gb-ce-card-info">
                            <h3 class="gb-ce-card-title"><?php echo esc_html($story['title']); ?></h3>
                            <div class="gb-ce-card-location">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <span><?php echo esc_html($story['location']); ?></span>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Nút Xem thêm câu chuyện ở giữa đáy -->
            <div class="gb-ce-loadmore-wrap">
                <button class="gb-ce-loadmore-btn" id="gbCeLoadMoreBtn">
                    <span>Xem thêm câu chuyện</span> &rarr;
                </button>
            </div>

        </section>

        <!-- ==========================================================================
             SECTION 4: COMMUNITY CALLOUT BANNER (KHỐI LAN TỎA HÀNH TRÌNH THẬT)
             ========================================================================== -->
        <section class="gb-ce-community-section">
            <div class="gb-ce-comm-box">
                
                <!-- Cột 1: Trích dẫn & Social Proof -->
                <div class="gb-ce-comm-quote-col">
                    <p class="gb-ce-comm-quote-text">
                        “Mỗi người một hành trình, nhưng tất cả đều gặp nhau ở niềm vui được di chuyển.”
                    </p>
                    <div class="gb-ce-comm-quote-author">— Cộng đồng GoBike</div>
                    
                    <div class="gb-ce-comm-avatars-wrap">
                        <div class="gb-ce-comm-avatar-group">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80" alt="Thành viên GoBike 1">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80" alt="Thành viên GoBike 2">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&auto=format&fit=crop&q=80" alt="Thành viên GoBike 3">
                            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&auto=format&fit=crop&q=80" alt="Thành viên GoBike 4">
                        </div>
                        <span class="gb-ce-comm-avatar-caption">
                            Hơn 1.200+ câu chuyện đã được chia sẻ và truyền cảm hứng mỗi ngày sống!
                        </span>
                    </div>
                </div>

                <!-- Cột 2: 3 bước tham gia & Nút CTA to -->
                <div class="gb-ce-comm-steps-col">
                    <div class="gb-ce-comm-steps-row">
                        
                        <div class="gb-ce-comm-step-item">
                            <div class="gb-ce-comm-step-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M23 7l-7 5 7 5V7z"></path>
                                    <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                                </svg>
                            </div>
                            <span class="gb-ce-comm-step-text">Quay video đơn giản</span>
                        </div>

                        <div class="gb-ce-comm-step-item">
                            <div class="gb-ce-comm-step-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="17 8 12 3 7 8"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                            </div>
                            <span class="gb-ce-comm-step-text">Gửi về GoBike</span>
                        </div>

                        <div class="gb-ce-comm-step-item">
                            <div class="gb-ce-comm-step-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                            </div>
                            <span class="gb-ce-comm-step-text">Xuất hiện trên website và Fanpage</span>
                        </div>

                    </div>

                    <a href="#gb-ce-share" class="gb-ce-comm-cta-btn">
                        <span>Chia sẻ câu chuyện của bạn</span> &rarr;
                    </a>
                </div>

                <!-- Cột 3: Ảnh Polaroid collage & Chữ ký nghệ thuật -->
                <div class="gb-ce-comm-polaroid-col">
                    <div class="gb-ce-polaroid-stack">
                        <div class="gb-ce-polaroid-item">
                            <img src="https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=200&auto=format&fit=crop&q=80" alt="GoBike Memory 1">
                        </div>
                        <div class="gb-ce-polaroid-item">
                            <img src="https://images.unsplash.com/photo-1507035895480-2b3156c31fc8?w=200&auto=format&fit=crop&q=80" alt="GoBike Memory 2">
                        </div>
                        <div class="gb-ce-polaroid-item">
                            <img src="https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=200&auto=format&fit=crop&q=80" alt="GoBike Memory 3">
                        </div>
                    </div>
                    <div class="gb-ce-comm-polaroid-art">
                        Cùng GoBike lan tỏa những hành trình THẬT 🌿
                    </div>
                </div>

            </div>
        </section>

        <!-- ==========================================================================
             SECTION 5: BOTTOM CTA BANNER (BẠN CŨNG CÓ CÂU CHUYỆN ĐỂ KỂ?)
             ========================================================================== -->
        <section class="gb-ce-bottom-cta-section" id="gb-ce-share">
            <div class="gb-ce-bottom-banner">
                <img class="gb-ce-bottom-banner-bg" src="https://images.unsplash.com/photo-1502680390469-be75c86b636f?w=1400&auto=format&fit=crop&q=80" alt="Con đường đẹp GoBike">
                <div class="gb-ce-bottom-banner-overlay"></div>
                
                <div class="gb-ce-bottom-banner-inner">
                    <div class="gb-ce-bottom-banner-text">
                        <h2 class="gb-ce-bottom-banner-title">Bạn cũng có câu chuyện để kể?</h2>
                        <p class="gb-ce-bottom-banner-desc">
                            Dù là hành trình nhỏ hay chuyến đi lớn, mọi câu chuyện đều xứng đáng được lắng nghe.
                        </p>
                    </div>
                    <a href="https://zalo.me" target="_blank" rel="noopener" class="gb-ce-bottom-share-btn">
                        <span>Chia sẻ ngay</span> &rarr;
                    </a>
                </div>
            </div>
        </section>

    </div>
</div>

<!-- ==========================================================================
     VIDEO MODAL POPUP (XEM VIDEO YOUTUBE)
     ========================================================================== -->
<div class="gb-ce-modal-overlay" id="gbCeVideoModal">
    <div class="gb-ce-modal-content">
        <button class="gb-ce-modal-close" id="gbCeModalClose" aria-label="Đóng popup">&times;</button>
        <div class="gb-ce-modal-video-box">
            <iframe id="gbCeIframe" src="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
        <div class="gb-ce-modal-info">
            <div>
                <h3 id="gbCeModalTitle">Câu chuyện trải nghiệm</h3>
                <p id="gbCeModalLocation">📍 Khách hàng GoBike</p>
            </div>
            <a href="https://zalo.me" target="_blank" rel="noopener" class="gb-ce-comm-cta-btn">
                <span>Gửi câu chuyện của bạn</span> &rarr;
            </a>
        </div>
    </div>
</div>

<!-- SCRIPT BỘ LỌC TABS & POPUP VIDEO -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Bộ lọc Tabs
    const tabButtons = document.querySelectorAll('.gb-ce-tab-btn');
    const cards = document.querySelectorAll('.gb-ce-card');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            tabButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filter = this.getAttribute('data-filter');
            cards.forEach(card => {
                const cats = card.getAttribute('data-cats') || '';
                if (filter === 'all' || cats.includes(filter)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // 2. Video Modal Popup
    const modal = document.getElementById('gbCeVideoModal');
    const iframe = document.getElementById('gbCeIframe');
    const modalClose = document.getElementById('gbCeModalClose');
    const modalTitle = document.getElementById('gbCeModalTitle');
    const modalLocation = document.getElementById('gbCeModalLocation');

    // Chuyển link YouTube sang định dạng nhúng embed
    function getYoutubeEmbed(url) {
        if (!url) return '';
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        const match = url.match(regExp);
        if (match && match[2].length === 11) {
            return 'https://www.youtube.com/embed/' + match[2] + '?autoplay=1&rel=0';
        }
        return url;
    }

    cards.forEach(card => {
        card.addEventListener('click', function(e) {
            // Không mở modal nếu bấm nút 3 chấm
            if (e.target.closest('.gb-ce-card-dots')) {
                e.stopPropagation();
                alert('Tính năng chia sẻ đang được phát triển.');
                return;
            }

            const rawVideo = this.getAttribute('data-video');
            const title = this.getAttribute('data-title');
            const loc = this.getAttribute('data-location');

            iframe.src = getYoutubeEmbed(rawVideo);
            modalTitle.textContent = title;
            modalLocation.textContent = '📍 ' + loc;

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    function closeModal() {
        modal.classList.remove('active');
        iframe.src = '';
        document.body.style.overflow = '';
    }

    if (modalClose) {
        modalClose.addEventListener('click', closeModal);
    }

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });

    // 3. Nút Xem thêm
    const loadMoreBtn = document.getElementById('gbCeLoadMoreBtn');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            alert('Bạn đã xem tất cả 12 câu chuyện nổi bật mới nhất!');
        });
    }
});
</script>

<?php get_footer(); ?>
