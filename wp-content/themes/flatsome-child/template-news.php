<?php
/**
 * Template Name: Trang Tin Tức - GoBike
 * Template Post Type: page
 * 
 * @package Flatsome-Child
 */

get_header();

$theme_uri = get_stylesheet_directory_uri();
$bg_cyclist = $theme_uri . '/assets/images/cyclist-mountain-road.jpg';

// Xử lý tham số tìm kiếm và chuyên mục lọc nếu có
$search_query = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : (isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '');
$current_cat  = isset($_GET['news_cat']) ? sanitize_text_field($_GET['news_cat']) : '';
$paged        = (get_query_var('paged')) ? get_query_var('paged') : ((get_query_var('page')) ? get_query_var('page') : 1);

// 1. QUERY BÀI VIẾT NỔI BẬT (FEATURED POSTS - 3 BÀI)
$featured_args = array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 3,
    'ignore_sticky_posts' => 1,
);
if (!empty($current_cat)) {
    $featured_args['category_name'] = $current_cat;
}
$featured_query = new WP_Query($featured_args);
$featured_posts = array();

if ($featured_query->have_posts()) {
    while ($featured_query->have_posts()) {
        $featured_query->the_post();
        $pid = get_the_ID();
        $cats = get_the_category($pid);
        $cat_name = !empty($cats) ? $cats[0]->name : 'Kinh nghiệm';
        $thumb = get_the_post_thumbnail_url($pid, 'large') ?: $bg_cyclist;
        
        $featured_posts[] = array(
            'id'      => $pid,
            'title'   => get_the_title(),
            'url'     => get_permalink(),
            'thumb'   => $thumb,
            'cat'     => $cat_name,
            'date'    => get_the_date('d \T\h\á\n\g m Y'),
            'date_short' => get_the_date('d/m/Y'),
            'excerpt' => wp_trim_words(get_the_excerpt(), 22, '...'),
        );
    }
    wp_reset_postdata();
}

// Fallback dữ liệu mẫu bài viết nổi bật chuẩn 100% thiết kế nếu web chưa có đủ bài
$fallback_featured = array(
    array(
        'id'      => 0,
        'title'   => 'Top 5 cung đường đạp xe đẹp nhất miền Bắc không thể bỏ lỡ',
        'url'     => '#',
        'thumb'   => $bg_cyclist,
        'cat'     => 'Kinh nghiệm',
        'date'    => '28 Tháng 08 2026',
        'date_short' => '28/08/2026',
        'excerpt' => 'Khám phá những cung đường tuyệt đẹp với cảnh quan ngoạn mục dành cho dân mê xê dịch.',
    ),
    array(
        'id'      => 0,
        'title'   => 'Hướng dẫn chọn xe đạp trợ lực cho nhu cầu xê dịch',
        'url'     => '#',
        'thumb'   => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=800&q=80',
        'cat'     => 'Hướng dẫn',
        'date'    => '28 Tháng 08 2026',
        'date_short' => '28/08/2026',
        'excerpt' => 'Tiêu chí chọn xe đạp trợ lực tối ưu pin, trọng lượng và độ bền.',
    ),
    array(
        'id'      => 0,
        'title'   => 'Đạp xe mỗi ngày: 7 lợi ích bất ngờ cho sức khỏe',
        'url'     => '#',
        'thumb'   => 'https://images.unsplash.com/photo-1517649763962-0c623266ddc0?auto=format&fit=crop&w=800&q=80',
        'cat'     => 'Kiến thức',
        'date'    => '28 Tháng 08 2026',
        'date_short' => '28/08/2026',
        'excerpt' => 'Cải thiện thể lực toàn diện và tinh thần sảng khoái với xe trợ lực.',
    ),
);

for ($i = 0; $i < 3; $i++) {
    if (!isset($featured_posts[$i])) {
        $featured_posts[$i] = $fallback_featured[$i];
    }
}

// 2. QUERY BÀI VIẾT MỚI NHẤT (LATEST POSTS - 6 BÀI / TRANG)
$latest_args = array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 6,
    'paged'          => $paged,
);
if (!empty($search_query)) {
    $latest_args['s'] = $search_query;
}
if (!empty($current_cat)) {
    $latest_args['category_name'] = $current_cat;
}
$latest_query = new WP_Query($latest_args);
$latest_posts = array();

if ($latest_query->have_posts()) {
    while ($latest_query->have_posts()) {
        $latest_query->the_post();
        $pid = get_the_ID();
        $cats = get_the_category($pid);
        $cat_list = array();
        if (!empty($cats)) {
            foreach ($cats as $c) {
                $cat_list[] = $c->name;
            }
        }
        if (empty($cat_list)) {
            $cat_list = array('Tin tức', 'Kinh nghiệm');
        }
        $thumb = get_the_post_thumbnail_url($pid, 'medium_large') ?: $bg_cyclist;

        $latest_posts[] = array(
            'id'      => $pid,
            'title'   => get_the_title(),
            'url'     => get_permalink(),
            'thumb'   => $thumb,
            'cats'    => $cat_list,
            'date'    => get_the_date('d/m/Y'),
            'excerpt' => wp_trim_words(get_the_excerpt(), 24, '...'),
        );
    }
    wp_reset_postdata();
}

// Fallback danh sách 6 bài mới nhất chuẩn 100% ảnh mẫu
$fallback_latest = array(
    array(
        'title'   => 'Kinh nghiệm du lịch Mộc Châu bằng xe đạp trợ lực',
        'url'     => '#',
        'thumb'   => $bg_cyclist,
        'cats'    => array('Du lịch', 'Kinh nghiệm'),
        'date'    => '14/09/2026',
        'excerpt' => 'Những cung đường tuyệt đẹp qua đồi chè xanh mướt và bản làng hoang sơ mang lại trải nghiệm vô cùng đáng nhớ.',
    ),
    array(
        'title'   => 'Cách bảo dưỡng pin xe đạp trợ lực lâu chai & an toàn',
        'url'     => '#',
        'thumb'   => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=800&q=80',
        'cats'    => array('Bảo dưỡng', 'Kỹ thuật'),
        'date'    => '14/09/2026',
        'excerpt' => 'Hướng dẫn sạc đúng cách, bảo quản trong mùa nóng và cách kéo dài tuổi thọ cell pin hiệu quả.',
    ),
    array(
        'title'   => 'Xe đạp trợ lực có phù hợp cho người lớn tuổi?',
        'url'     => '#',
        'thumb'   => 'https://images.unsplash.com/photo-1502744688674-c619d1586c9e?auto=format&fit=crop&w=800&q=80',
        'cats'    => array('Sức khỏe', 'Kiến thức'),
        'date'    => '13/09/2026',
        'excerpt' => 'Giải pháp rèn luyện sức khỏe an toàn, giảm áp lực lên khớp gối và tăng cường vận động mỗi ngày.',
    ),
    array(
        'title'   => 'So sánh xe đạp trợ lực và xe máy điện thông thường',
        'url'     => '#',
        'thumb'   => 'https://images.unsplash.com/photo-1532298229144-0ec0c57515c7?auto=format&fit=crop&w=800&q=80',
        'cats'    => array('Kiến thức', 'Xu hướng'),
        'date'    => '12/09/2026',
        'excerpt' => 'Phân tích chi phí vận hành, tính cơ động và trải nghiệm rèn luyện giữa hai phương tiện xanh.',
    ),
    array(
        'title'   => 'Gợi ý hành trình 2 ngày 1 đêm quanh Hà Nội',
        'url'     => '#',
        'thumb'   => 'https://images.unsplash.com/photo-1507035895480-2b3156c31fc8?auto=format&fit=crop&w=800&q=80',
        'cats'    => array('Du lịch', 'Trải nghiệm'),
        'date'    => '10/09/2026',
        'excerpt' => 'Cung đường ven sông Hồng và làng cổ Đường Lâm thanh bình, địa điểm lý tưởng cho chuyến dã ngoại cuối tuần.',
    ),
    array(
        'title'   => 'Công nghệ cảm biến lực trợ lực: Những điều bạn nên biết',
        'url'     => '#',
        'thumb'   => 'https://images.unsplash.com/photo-1571068316344-75bc76f77890?auto=format&fit=crop&w=800&q=80',
        'cats'    => array('Công nghệ', 'Xu hướng'),
        'date'    => '09/09/2026',
        'excerpt' => 'Sự khác biệt giữa cảm biến vòng tua và cảm biến mô-men xoắn trong việc hỗ trợ lực đạp tự nhiên.',
    ),
);

if (empty($latest_posts)) {
    $latest_posts = $fallback_latest;
}

// 3. DANH MỤC 8 CHỦ ĐỀ CHUẨN MẪU (ẢNH 1)
$news_categories = array(
    array('slug' => '', 'name_top' => 'Tất cả', 'name_bottom' => '', 'icon' => 'grid'),
    array('slug' => 'huong-dan-su-dung', 'name_top' => 'Hướng dẫn', 'name_bottom' => 'sử dụng', 'icon' => 'book'),
    array('slug' => 'kinh-nghiem-di-xe', 'name_top' => 'Kinh nghiệm', 'name_bottom' => 'đi xe', 'icon' => 'bike'),
    array('slug' => 'suc-khoe-loi-song', 'name_top' => 'Sức khỏe', 'name_bottom' => '& Lối sống', 'icon' => 'heart'),
    array('slug' => 'du-lich-kham-pha', 'name_top' => 'Du lịch', 'name_bottom' => '& Khám phá', 'icon' => 'mountain'),
    array('slug' => 'bao-duong-ky-thuat', 'name_top' => 'Bảo dưỡng', 'name_bottom' => '& Kỹ thuật', 'icon' => 'wrench'),
    array('slug' => 'xu-huong-cong-nghe', 'name_top' => 'Xu hướng', 'name_bottom' => '& Công nghệ', 'icon' => 'cpu'),
    array('slug' => 'cau-chuyen-gobike', 'name_top' => 'Câu chuyện', 'name_bottom' => 'GoBike', 'icon' => 'users'),
);
?>

<div class="gobike-news-page-wrapper">

    <!-- 1. HERO BANNER: ẢNH NÚI & NGƯỜI ĐẠP XE + TÌM KIẾM + TYPOGRAPHY NGHỆ THUẬT -->
    <section class="gobike-news-hero" style="background-image: url('<?php echo esc_url($bg_cyclist); ?>');">
        <div class="hero-overlay"></div>
        <div class="container hero-container">
            
            <div class="hero-content">
                <!-- Breadcrumb -->
                <div class="hero-breadcrumb">
                    <span class="arrow">&rarr;</span>
                    <a href="<?php echo esc_url(get_permalink()); ?>">Tin tức & Cẩm nang</a>
                </div>

                <!-- Tiêu đề lớn H1 -->
                <h1 class="hero-heading">Cùng GoBike<br>khám phá thế giới<br>bằng một cách khác</h1>

                <!-- Mô tả phụ -->
                <p class="hero-subtext">Chia sẻ kiến thức, kinh nghiệm và những câu chuyện truyền cảm hứng từ cộng đồng yêu xe đạp trợ lực.</p>

                <!-- Thanh tìm kiếm bài viết -->
                <form class="hero-search-form" method="get" action="<?php echo esc_url(get_permalink()); ?>">
                    <input type="text" name="s" placeholder="Tìm kiếm bài viết, chủ đề..." value="<?php echo esc_attr($search_query); ?>" autocomplete="off" />
                    <button type="submit" aria-label="Tìm kiếm bài viết">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- Typography nghệ thuật bên phải (Chuẩn ảnh mẫu) -->
            <div class="hero-art-quote">
                <p class="quote-line-1">Mỗi hành trình bạn vượt qua</p>
                <p class="quote-line-2">Hãy sống trọn từng khoảnh khắc</p>
                <p class="quote-line-3">Sắc diện của bạn</p>
                <div class="quote-leaf-decor">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66l.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/>
                    </svg>
                </div>
            </div>

        </div>
    </section>

    <!-- 2. KHỐI 8 THẺ DANH MỤC / CHỦ ĐỀ ĐỘC LẬP (CHUẨN 100% ẢNH MẪU 1) -->
    <section class="gobike-news-categories-section">
        <div class="container">
            <div class="news-categories-grid">
                <?php foreach ($news_categories as $item): 
                    $is_active = ($current_cat === $item['slug']);
                    $cat_link  = empty($item['slug']) ? get_permalink() : add_query_arg('news_cat', $item['slug'], get_permalink());
                ?>
                    <a href="<?php echo esc_url($cat_link); ?>" class="news-cat-card <?php echo $is_active ? 'active' : ''; ?>">
                        <div class="cat-card-icon-box">
                            <?php if ($item['icon'] === 'grid'): ?>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="7" cy="7" r="3.2"></circle>
                                    <circle cx="17" cy="7" r="3.2"></circle>
                                    <circle cx="17" cy="17" r="3.2"></circle>
                                    <circle cx="7" cy="17" r="3.2"></circle>
                                </svg>
                            <?php elseif ($item['icon'] === 'book'): ?>
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path>
                                    <path d="M9 7h6M9 11h5"></path>
                                </svg>
                            <?php elseif ($item['icon'] === 'bike'): ?>
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="5.5" cy="17.5" r="3.5"></circle>
                                    <circle cx="18.5" cy="17.5" r="3.5"></circle>
                                    <path d="M15 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-3 11.5L9 9l4.5-3 3 5 4-1"></path>
                                </svg>
                            <?php elseif ($item['icon'] === 'heart'): ?>
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            <?php elseif ($item['icon'] === 'mountain'): ?>
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m8 3 4 8 5-5 5 15H2L8 3z"></path>
                                </svg>
                            <?php elseif ($item['icon'] === 'wrench'): ?>
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                                </svg>
                            <?php elseif ($item['icon'] === 'cpu'): ?>
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="4" y="4" width="16" height="16" rx="2"></rect>
                                    <rect x="9" y="9" width="6" height="6"></rect>
                                    <line x1="9" y1="1" x2="9" y2="4"></line>
                                    <line x1="15" y1="1" x2="15" y2="4"></line>
                                    <line x1="9" y1="20" x2="9" y2="23"></line>
                                    <line x1="15" y1="20" x2="15" y2="23"></line>
                                    <line x1="20" y1="9" x2="23" y2="9"></line>
                                    <line x1="20" y1="14" x2="23" y2="14"></line>
                                    <line x1="1" y1="9" x2="4" y2="9"></line>
                                    <line x1="1" y1="14" x2="4" y2="14"></line>
                                </svg>
                            <?php elseif ($item['icon'] === 'users'): ?>
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            <?php endif; ?>
                        </div>
                        <div class="cat-card-text">
                            <span class="line-1"><?php echo esc_html($item['name_top']); ?></span>
                            <?php if (!empty($item['name_bottom'])): ?>
                                <span class="line-2"><?php echo esc_html($item['name_bottom']); ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- 3. KHỐI BÀI VIẾT NỔI BẬT (🌿 Bài viết nổi bật - Xem tất cả >) -->
    <section class="gobike-featured-news-section">
        <div class="container">
            
            <div class="section-header-row">
                <h2 class="section-title">
                    <span class="leaf-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="#0d7030">
                            <path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66l.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/>
                        </svg>
                    </span>
                    Bài viết nổi bật
                </h2>
                <a href="<?php echo esc_url(get_permalink()); ?>" class="view-all-link">
                    Xem tất cả <span class="arrow">&rsaquo;</span>
                </a>
            </div>

            <div class="featured-grid">
                
                <!-- BÀI VIẾT LỚN BÊN TRÁI (60%) -->
                <?php $big_post = $featured_posts[0]; ?>
                <div class="featured-big-card" style="background-image: url('<?php echo esc_url($big_post['thumb']); ?>');">
                    <a href="<?php echo esc_url($big_post['url']); ?>" class="card-overlay-link" aria-label="<?php echo esc_attr($big_post['title']); ?>"></a>
                    <div class="big-card-content">
                        <div class="meta-row">
                            <span class="badge-tag"><?php echo esc_html($big_post['cat']); ?></span>
                            <span class="meta-date"><?php echo esc_html($big_post['date']); ?></span>
                        </div>
                        <h3 class="big-card-title">
                            <a href="<?php echo esc_url($big_post['url']); ?>"><?php echo esc_html($big_post['title']); ?></a>
                        </h3>
                        <p class="big-card-excerpt"><?php echo esc_html($big_post['excerpt']); ?></p>
                        <a href="<?php echo esc_url($big_post['url']); ?>" class="read-more-link">
                            Đọc ngay <span class="arrow">&rarr;</span>
                        </a>
                    </div>
                </div>

                <!-- 2 BÀI VIẾT XẾP CHỒNG BÊN PHẢI (40%) -->
                <div class="featured-side-stack">
                    
                    <!-- Bài nhỏ 1 -->
                    <?php $side1 = $featured_posts[1]; ?>
                    <div class="side-card">
                        <div class="side-card-info">
                            <div class="meta-row">
                                <span class="badge-tag green-alt"><?php echo esc_html($side1['cat']); ?></span>
                                <span class="meta-date"><?php echo esc_html($side1['date_short']); ?></span>
                            </div>
                            <h4 class="side-card-title">
                                <a href="<?php echo esc_url($side1['url']); ?>"><?php echo esc_html($side1['title']); ?></a>
                            </h4>
                            <p class="side-card-excerpt"><?php echo esc_html($side1['excerpt']); ?></p>
                            <a href="<?php echo esc_url($side1['url']); ?>" class="read-more-link">
                                Đọc ngay <span class="arrow">&rarr;</span>
                            </a>
                        </div>
                        <div class="side-card-thumb">
                            <a href="<?php echo esc_url($side1['url']); ?>">
                                <img src="<?php echo esc_url($side1['thumb']); ?>" alt="<?php echo esc_attr($side1['title']); ?>" loading="lazy" />
                            </a>
                        </div>
                    </div>

                    <!-- Bài nhỏ 2 -->
                    <?php $side2 = $featured_posts[2]; ?>
                    <div class="side-card">
                        <div class="side-card-info">
                            <div class="meta-row">
                                <span class="badge-tag green-subtle"><?php echo esc_html($side2['cat']); ?></span>
                                <span class="meta-date"><?php echo esc_html($side2['date_short']); ?></span>
                            </div>
                            <h4 class="side-card-title">
                                <a href="<?php echo esc_url($side2['url']); ?>"><?php echo esc_html($side2['title']); ?></a>
                            </h4>
                            <p class="side-card-excerpt"><?php echo esc_html($side2['excerpt']); ?></p>
                            <a href="<?php echo esc_url($side2['url']); ?>" class="read-more-link">
                                Đọc ngay <span class="arrow">&rarr;</span>
                            </a>
                        </div>
                        <div class="side-card-thumb">
                            <a href="<?php echo esc_url($side2['url']); ?>">
                                <img src="<?php echo esc_url($side2['thumb']); ?>" alt="<?php echo esc_attr($side2['title']); ?>" loading="lazy" />
                            </a>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </section>

    <!-- 4. BỐ CỤC CHÍNH 2 CỘT: BÀI VIẾT MỚI NHẤT (TRÁI) + SIDEBAR 3 WIDGET (PHẢI) -->
    <section class="gobike-main-news-section">
        <div class="container">
            <div class="row news-layout-row">

                <!-- CỘT TRÁI (8 CỘT - ~68%): BÀI VIẾT MỚI NHẤT -->
                <div class="col large-8 medium-12 small-12 news-content-col">
                    
                    <div class="section-header-row mb-20">
                        <h2 class="section-title">
                            <span class="leaf-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="#0d7030">
                                    <path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66l.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/>
                                </svg>
                            </span>
                            Bài viết mới nhất
                        </h2>
                    </div>

                    <!-- DANH SÁCH THẺ NGANG (HORIZONTAL CARDS) -->
                    <div class="latest-posts-list">
                        <?php foreach ($latest_posts as $post_item): ?>
                            <article class="latest-post-horizontal-card">
                                
                                <div class="card-thumb-wrap">
                                    <a href="<?php echo esc_url($post_item['url']); ?>">
                                        <img src="<?php echo esc_url($post_item['thumb']); ?>" alt="<?php echo esc_attr($post_item['title']); ?>" loading="lazy" />
                                    </a>
                                </div>

                                <div class="card-body-wrap">
                                    <div class="card-meta-top">
                                        <span class="card-date"><?php echo esc_html($post_item['date']); ?></span>
                                    </div>

                                    <h3 class="card-post-title">
                                        <a href="<?php echo esc_url($post_item['url']); ?>"><?php echo esc_html($post_item['title']); ?></a>
                                    </h3>

                                    <p class="card-post-excerpt"><?php echo esc_html($post_item['excerpt']); ?></p>

                                    <div class="card-footer-meta">
                                        <div class="card-tags-list">
                                            <?php foreach ($post_item['cats'] as $tag): ?>
                                                <span class="tag-pill"><?php echo esc_html($tag); ?></span>
                                            <?php endforeach; ?>
                                        </div>

                                        <a href="<?php echo esc_url($post_item['url']); ?>" class="card-read-more">
                                            Đọc thêm <span class="arrow">&rarr;</span>
                                        </a>
                                    </div>
                                </div>

                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- PHÂN TRANG PAGINATION CHUẨN GOBIKE -->
                    <div class="gobike-news-pagination">
                        <?php
                        if ($latest_query->max_num_pages > 1) {
                            echo paginate_links(array(
                                'total'        => $latest_query->max_num_pages,
                                'current'      => $paged,
                                'prev_text'    => '&lsaquo;',
                                'next_text'    => '&rsaquo;',
                                'type'         => 'list',
                            ));
                        } else {
                            // Mockup phân trang trực quan theo thiết kế
                            ?>
                            <ul class="page-numbers">
                                <li><span class="prev page-numbers">&lsaquo;</span></li>
                                <li><span aria-current="page" class="page-numbers current">1</span></li>
                                <li><a class="page-numbers" href="#">2</a></li>
                                <li><a class="page-numbers" href="#">3</a></li>
                                <li><span class="page-numbers dots">&hellip;</span></li>
                                <li><a class="page-numbers" href="#">9</a></li>
                                <li><a class="next page-numbers" href="#">&rsaquo;</a></li>
                            </ul>
                            <?php
                        }
                        ?>
                    </div>

                </div>

                <!-- CỘT PHẢI (4 CỘT - ~32%): SIDEBAR 3 WIDGET CHUẨN MẪU -->
                <div class="col large-4 medium-12 small-12 news-sidebar-col">
                    <aside class="gobike-news-sidebar">

                        <!-- WIDGET 1: BÀI VIẾT ĐƯỢC QUAN TÂM (TOP 1..5) -->
                        <div class="sidebar-widget widget-popular-posts">
                            <h3 class="widget-title">Bài viết được quan tâm</h3>
                            
                            <ol class="popular-posts-list">
                                <li class="popular-item">
                                    <span class="rank-badge">1</span>
                                    <div class="popular-content">
                                        <h4 class="popular-title">
                                            <a href="#">Top 5 cung đường đạp xe đẹp nhất miền Bắc</a>
                                        </h4>
                                        <span class="views-count">12.854 lượt xem</span>
                                    </div>
                                </li>
                                <li class="popular-item">
                                    <span class="rank-badge">2</span>
                                    <div class="popular-content">
                                        <h4 class="popular-title">
                                            <a href="#">Hướng dẫn chọn xe đạp trợ lực</a>
                                        </h4>
                                        <span class="views-count">10.412 lượt xem</span>
                                    </div>
                                </li>
                                <li class="popular-item">
                                    <span class="rank-badge">3</span>
                                    <div class="popular-content">
                                        <h4 class="popular-title">
                                            <a href="#">Đạp xe mỗi ngày: 7 lợi ích cho sức khỏe</a>
                                        </h4>
                                        <span class="views-count">8.920 lượt xem</span>
                                    </div>
                                </li>
                                <li class="popular-item">
                                    <span class="rank-badge">4</span>
                                    <div class="popular-content">
                                        <h4 class="popular-title">
                                            <a href="#">Kinh nghiệm du lịch Mộc Châu bằng xe đạp trợ lực</a>
                                        </h4>
                                        <span class="views-count">7.340 lượt xem</span>
                                    </div>
                                </li>
                                <li class="popular-item">
                                    <span class="rank-badge">5</span>
                                    <div class="popular-content">
                                        <h4 class="popular-title">
                                            <a href="#">Cách bảo dưỡng pin xe đạp an toàn</a>
                                        </h4>
                                        <span class="views-count">6.110 lượt xem</span>
                                    </div>
                                </li>
                            </ol>
                        </div>

                        <!-- WIDGET 2: NHẬN TIN TỨC & MẸO HAY TỪ GOBIKE (NEWSLETTER) -->
                        <div class="sidebar-widget widget-newsletter">
                            <div class="newsletter-icon-wrap">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#0d7030" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                            </div>
                            <h3 class="newsletter-title">Nhận tin tức & mẹo hay từ GoBike</h3>
                            <p class="newsletter-desc">Đăng ký để nhận những bài viết hay nhất, xu hướng mới và các mẹo sử dụng xe hữu ích.</p>
                            
                            <form class="newsletter-form" onsubmit="event.preventDefault(); alert('Cảm ơn bạn đã đăng ký nhận tin từ GoBike!');">
                                <div class="input-wrap">
                                    <input type="email" placeholder="Nhập email của bạn" required />
                                </div>
                                <button type="submit" class="newsletter-submit-btn">
                                    Đăng ký <span class="arrow">&rarr;</span>
                                </button>
                            </form>
                        </div>

                        <!-- WIDGET 3: MỖI HÀNH TRÌNH LÀ MỘT CÂU CHUYỆN (COMMUNITY CTA) -->
                        <div class="sidebar-widget widget-story-cta" style="background-image: linear-gradient(180deg, rgba(4, 75, 37, 0.88) 0%, rgba(4, 75, 37, 0.95) 100%), url('<?php echo esc_url($bg_cyclist); ?>');">
                            <h3 class="story-title">
                                Mỗi hành trình<br>là một câu chuyện 🍃
                            </h3>
                            <p class="story-desc">Chia sẻ câu chuyện của bạn và nhận quà tặng từ cộng đồng GoBike.</p>
                            <a href="<?php echo esc_url(home_url('/trai-nghiem-khach-hang/')); ?>" class="story-submit-btn">
                                Gửi bài viết của bạn <span class="arrow">&rarr;</span>
                            </a>
                            <div class="story-bottom-image">
                                <img src="<?php echo esc_url($bg_cyclist); ?>" alt="GoBike community" loading="lazy" />
                            </div>
                        </div>

                    </aside>
                </div>

            </div>
        </div>
    </section>

    <!-- 5. BANNER CTA ĐÁY TRANG: "ĐI XA HƠN MỖI NGÀY" (CHUẨN ẢNH MẪU) -->
    <section class="gobike-news-bottom-cta" style="background-image: url('<?php echo esc_url($bg_cyclist); ?>');">
        <div class="cta-overlay"></div>
        <div class="container cta-container">
            <div class="cta-content">
                <h2 class="cta-title">Đi xa hơn mỗi ngày</h2>
                <p class="cta-subtitle">Viết nên câu chuyện mới ở những cung đường mới</p>
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="cta-button">
                    Khám phá ngay <span class="arrow">&rarr;</span>
                </a>
            </div>
        </div>
    </section>

</div>

<?php
get_footer();
