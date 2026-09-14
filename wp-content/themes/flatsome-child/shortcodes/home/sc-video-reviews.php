<?php
/**
 * Shortcode: [gobike_home_video_reviews]
 * 
 * Section: VIDEO REVIEW THỰC TẾ & TẠI SAO NÊN XEM?
 * Chức năng:
 * 1. Quản lý CPT video_review & Taxonomy video_category
 * 2. Hỗ trợ link YouTube & YouTube Shorts (không lưu video trên server)
 * 3. Bố cục 1 Video nổi bật lớn bên trái + 4 Thẻ ngang bên phải
 * 4. Tab lọc danh mục tương tác nhanh không tải lại trang
 * 5. Popup phát Video YouTube nhúng qua wp_footer, chống đè layer và siêu nhẹ
 * 6. Khối Marketing "Tại sao nên xem?" chuẩn 100% thiết kế
 * 
 * @package Flatsome-Child
 */

if (!defined('ABSPATH')) {
    exit;
}

/* ============================================================================
 * 1. ĐĂNG KÝ CPT "VIDEO_REVIEW" VÀ TAXONOMY "VIDEO_CATEGORY"
 * ============================================================================
 */
add_action('init', 'gobike_register_video_review_cpt');
function gobike_register_video_review_cpt()
{
    // 1.1 Custom Taxonomy: Danh mục Video
    $tax_labels = array(
        'name'              => 'Danh mục Video',
        'singular_name'     => 'Danh mục Video',
        'search_items'      => 'Tìm danh mục',
        'all_items'         => 'Tất cả danh mục',
        'parent_item'       => 'Danh mục cha',
        'parent_item_colon' => 'Danh mục cha:',
        'edit_item'         => 'Chỉnh sửa danh mục',
        'update_item'       => 'Cập nhật danh mục',
        'add_new_item'      => 'Thêm danh mục mới',
        'new_item_name'     => 'Tên danh mục mới',
        'menu_name'         => 'Danh mục Video',
    );
    register_taxonomy('video_category', array('video_review'), array(
        'hierarchical'      => true,
        'labels'            => $tax_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'danh-muc-video'),
    ));

    // 1.2 Custom Post Type: Video Reviews
    $cpt_labels = array(
        'name'               => 'Video Reviews',
        'singular_name'      => 'Video Review',
        'menu_name'          => 'Video Reviews',
        'name_admin_bar'     => 'Video Review',
        'add_new'            => 'Thêm video mới',
        'add_new_item'       => 'Thêm Video Review mới',
        'new_item'           => 'Video Review mới',
        'edit_item'          => 'Chỉnh sửa Video Review',
        'view_item'          => 'Xem Video',
        'all_items'          => 'Tất cả Video Reviews',
        'search_items'       => 'Tìm kiếm Video',
        'not_found'          => 'Chưa có video nào.',
        'not_found_in_trash' => 'Không có video trong thùng rác.',
    );

    register_post_type('video_review', array(
        'labels'             => $cpt_labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'video-review'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 27,
        'menu_icon'          => 'dashicons-video-alt3',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
    ));
}

// Thêm cột thông tin trong bảng danh sách Admin
add_filter('manage_video_review_posts_columns', 'gobike_video_review_columns');
function gobike_video_review_columns($columns)
{
    $new_cols = array(
        'cb'            => $columns['cb'],
        'vr_thumb'      => 'Ảnh bìa',
        'title'         => 'Tiêu đề Video',
        'vr_duration'   => 'Thời lượng',
        'vr_featured'   => 'Nổi bật',
        'taxonomy-video_category' => 'Danh mục',
        'date'          => 'Ngày đăng',
    );
    return $new_cols;
}

add_action('manage_video_review_posts_custom_column', 'gobike_video_review_column_data', 10, 2);
function gobike_video_review_column_data($column, $post_id)
{
    switch ($column) {
        case 'vr_thumb':
            $thumb = get_field('video_thumbnail', $post_id);
            if (!$thumb && has_post_thumbnail($post_id)) {
                $thumb = get_the_post_thumbnail_url($post_id, 'thumbnail');
            }
            if (!$thumb) {
                $url = get_field('video_url', $post_id);
                $yt_data = gobike_extract_youtube_info($url);
                $thumb = $yt_data['thumbnail'];
            }
            if ($thumb) {
                echo '<img src="' . esc_url($thumb) . '" style="width:70px; height:45px; object-fit:cover; border-radius:4px;">';
            } else {
                echo '—';
            }
            break;

        case 'vr_duration':
            $dur = get_field('video_duration', $post_id);
            echo $dur ? '<span style="background:#f1f5f9; padding:2px 6px; border-radius:4px; font-weight:600;">' . esc_html($dur) . '</span>' : '—';
            break;

        case 'vr_featured':
            $feat = get_field('is_featured', $post_id);
            echo $feat ? '<strong style="color:#149d29;">★ Nổi bật</strong>' : '—';
            break;
    }
}


/* ============================================================================
 * 2. HÀM HELPER: TRÍCH XUẤT YOUTUBE ID & THUMBNAIL
 * ============================================================================
 */
function gobike_extract_youtube_info($url)
{
    $info = array(
        'id'        => '',
        'embed_url' => '',
        'thumbnail' => '',
    );

    if (empty($url)) {
        return $info;
    }

    // Pattern bắt YouTube watch, youtu.be, shorts, embed
    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/|youtube\.com\/shorts\/)([^"&?\/\s]{11})/i';
    if (preg_match($pattern, $url, $matches)) {
        $id = $matches[1];
        $info['id']        = $id;
        $info['embed_url'] = 'https://www.youtube.com/embed/' . $id . '?autoplay=1&rel=0';
        $info['thumbnail'] = 'https://img.youtube.com/vi/' . $id . '/maxresdefault.jpg';
    }

    return $info;
}


/* ============================================================================
 * 3. HÀM RENDER SHORTCODE [gobike_home_video_reviews]
 * ============================================================================
 */
add_shortcode('gobike_home_video_reviews', 'gobike_render_home_video_reviews');
function gobike_render_home_video_reviews($atts)
{
    $atts = shortcode_atts(array(
        'title'       => 'VIDEO REVIEW THỰC TẾ',
        'subtitle'    => 'Trải nghiệm thật • Đánh giá thật • Giúp bạn chọn đúng xe',
        'view_all'    => '#',
        'limit'       => 5,
    ), $atts, 'gobike_home_video_reviews');

    // 3.1 Lấy danh mục taxonomy
    $categories = get_terms(array(
        'taxonomy'   => 'video_category',
        'hide_empty' => false,
    ));

    // 3.2 Query Video Nổi Bật (Featured)
    $featured_query = new WP_Query(array(
        'post_type'      => 'video_review',
        'posts_per_page' => 1,
        'meta_query'     => array(
            array(
                'key'     => 'is_featured',
                'value'   => '1',
                'compare' => '=',
            ),
        ),
    ));

    $featured_post = null;
    $exclude_id = 0;
    if ($featured_query->have_posts()) {
        $featured_query->the_post();
        $featured_post = get_post();
        $exclude_id = $featured_post->ID;
        wp_reset_postdata();
    }

    // 3.3 Query các video con
    $side_args = array(
        'post_type'      => 'video_review',
        'posts_per_page' => 4,
        'post__not_in'   => $exclude_id ? array($exclude_id) : array(),
    );
    $side_query = new WP_Query($side_args);

    // Chuẩn bị danh sách video
    $has_real_data = ($featured_post || $side_query->have_posts());

    // Nếu database chưa có bài, sử dụng dữ liệu mẫu hoàn hảo theo đúng ảnh thiết kế
    $demo_featured = array(
        'id'          => 'c200-featured',
        'title'       => 'GoBike C200 – Đạp nhẹ hơn. Đi xa hơn.',
        'desc'        => 'Trải nghiệm thực tế GoBike C200 sau 1 tháng sử dụng: thiết kế, cảm giác lái, khả năng tăng tốc, quãng đường, pin và những điều bạn cần biết trước khi mua!',
        'duration'    => '10:24',
        'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'youtube_id'  => 'dQw4w9WgXcQ',
        'thumb'       => 'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=800&auto=format&fit=crop&q=80',
        'category'    => 'nguoi-dung-that',
    );

    $demo_items = array(
        array(
            'title'    => 'Trải nghiệm đi làm 15km mỗi ngày',
            'desc'     => 'GoBike có thật sự tiện cho dân đi làm hằng ngày? Xem trải nghiệm thực tế sau 2 tuần!',
            'duration' => '06:12',
            'views'    => '32K lượt xem • 2 tuần trước',
            'badge'    => 'Review từ người thật',
            'thumb'    => 'https://images.unsplash.com/photo-1507035895480-2b3156c31fc8?w=500&auto=format&fit=crop&q=80',
            'category' => 'di-lam',
            'yt_id'    => 'dQw4w9WgXcQ',
        ),
        array(
            'title'    => 'Test leo dốc cùng GoBike',
            'desc'     => 'Khả năng leo dốc, mô-tơ và sức mạnh thật sự của GoBike khi gặp địa hình khó!',
            'duration' => '05:38',
            'views'    => '24K lượt xem • 3 tuần trước',
            'badge'    => 'Test thực tế',
            'thumb'    => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=500&auto=format&fit=crop&q=80',
            'category' => 'tour-leo-doc',
            'yt_id'    => 'dQw4w9WgXcQ',
        ),
        array(
            'title'    => 'Người dùng nói gì về GoBike?',
            'desc'     => 'Những chia sẻ chân thật từ khách hàng sau nhiều tháng sử dụng. Có đáng mua không?',
            'duration' => '04:50',
            'views'    => '18K lượt xem • 1 tháng trước',
            'badge'    => 'Đánh giá từ người dùng',
            'thumb'    => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=500&auto=format&fit=crop&q=80',
            'category' => 'nguoi-dung-that',
            'yt_id'    => 'dQw4w9WgXcQ',
        ),
        array(
            'title'    => 'Hướng dẫn sử dụng và bảo quản xe',
            'desc'     => 'Hướng dẫn chi tiết từ A-Z để xe luôn bền bỉ và vận hành tốt nhất. Xem ngay!',
            'duration' => '07:29',
            'views'    => '12K lượt xem • 3 tuần trước',
            'badge'    => 'Hướng dẫn chi tiết',
            'thumb'    => 'https://images.unsplash.com/photo-1511994298241-608e28f14fde?w=500&auto=format&fit=crop&q=80',
            'category' => 'huong-dan',
            'yt_id'    => 'dQw4w9WgXcQ',
        ),
    );

    ob_start();
    ?>
    <!-- SECTION VIDEO REVIEW THỰC TẾ -->
    <div class="gobike-video-review-section">
        <!-- 1. HEADER & FILTER TABS -->
        <div class="gvr-header-wrap">
            <div class="gvr-title-box">
                <h2 class="gvr-main-title">
                    VIDEO REVIEW <span class="text-highlight">THỰC TẾ</span>
                </h2>
                <p class="gvr-sub-title"><?php echo esc_html($atts['subtitle']); ?></p>
            </div>

            <!-- Tab Lọc Danh Mục -->
            <div class="gvr-filter-tabs">
                <button type="button" class="gvr-tab-btn active" data-cat="all">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    <span>Tất cả</span>
                </button>
                <button type="button" class="gvr-tab-btn" data-cat="di-lam">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                    <span>Đi làm</span>
                </button>
                <button type="button" class="gvr-tab-btn" data-cat="tour-leo-doc">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m8 3 4 8 5-5 5 15H2L8 3z"></path>
                    </svg>
                    <span>Tour, leo dốc</span>
                </button>
                <button type="button" class="gvr-tab-btn" data-cat="nguoi-dung-that">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>Người dùng thật</span>
                </button>
                <button type="button" class="gvr-tab-btn" data-cat="huong-dan">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                    </svg>
                    <span>Hướng dẫn</span>
                </button>
            </div>

            <!-- Nút Xem Tất Cả -->
            <a href="<?php echo esc_url($atts['view_all']); ?>" class="gvr-view-all-link">
                <span>Xem tất cả video</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>

        <!-- 2. MAIN GRID: 1 FEATURED LỚN (TRÁI) + 4 VIDEO CARD NGANG (PHẢI) -->
        <div class="gvr-main-grid">
            <?php
            // A. RENDER VIDEO NỔI BẬT (BÊN TRÁI)
            if ($featured_post) {
                $f_id        = $featured_post->ID;
                $f_title     = get_the_title($f_id);
                $f_desc      = get_field('video_desc', $f_id) ?: get_the_excerpt($f_id);
                $f_duration  = get_field('video_duration', $f_id) ?: '10:00';
                $f_url       = get_field('video_url', $f_id);
                $f_yt_info   = gobike_extract_youtube_info($f_url);
                $f_thumb     = get_field('video_thumbnail', $f_id);
                if (!$f_thumb) $f_thumb = $f_yt_info['thumbnail'];
                $f_cat_terms = get_the_terms($f_id, 'video_category');
                $f_cat_slug  = (!empty($f_cat_terms) && !is_wp_error($f_cat_terms)) ? $f_cat_terms[0]->slug : 'all';
                $f_embed     = $f_yt_info['embed_url'];
            } else {
                // Fallback Demo
                $f_title     = $demo_featured['title'];
                $f_desc      = $demo_featured['desc'];
                $f_duration  = $demo_featured['duration'];
                $f_thumb     = $demo_featured['thumb'];
                $f_cat_slug  = $demo_featured['category'];
                $f_embed     = 'https://www.youtube.com/embed/' . $demo_featured['youtube_id'] . '?autoplay=1&rel=0';
            }
            ?>
            <div class="gvr-featured-card gvr-filterable-item" data-cat="<?php echo esc_attr($f_cat_slug); ?>">
                <div class="gvr-featured-media js-open-gvr-video" data-video-src="<?php echo esc_attr($f_embed); ?>" data-video-title="<?php echo esc_attr($f_title); ?>">
                    <img src="<?php echo esc_url($f_thumb); ?>" alt="<?php echo esc_attr($f_title); ?>" class="gvr-featured-img">
                    <span class="gvr-badge-featured">★ VIDEO NỔI BẬT NHẤT</span>
                    
                    <!-- Nút Play Tròn Nổi Bật -->
                    <div class="gvr-play-button-wrap">
                        <div class="gvr-play-btn">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                                <polygon points="5 3 19 12 5 21 5 3"></polygon>
                            </svg>
                        </div>
                    </div>
                    
                    <span class="gvr-duration-badge"><?php echo esc_html($f_duration); ?></span>
                </div>

                <div class="gvr-featured-body">
                    <h3 class="gvr-featured-title js-open-gvr-video" data-video-src="<?php echo esc_attr($f_embed); ?>" data-video-title="<?php echo esc_attr($f_title); ?>">
                        <?php echo esc_html($f_title); ?>
                    </h3>
                    <p class="gvr-featured-desc"><?php echo esc_html($f_desc); ?></p>

                    <!-- 3 Điểm Cam Kết Review -->
                    <div class="gvr-featured-tags">
                        <div class="gvr-ft-tag">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#149d29" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            </svg>
                            <span>Review từ người dùng thật</span>
                        </div>
                        <div class="gvr-ft-tag">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#149d29" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                            </svg>
                            <span>Cảm giác lái sau thời gian dài</span>
                        </div>
                        <div class="gvr-ft-tag">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#149d29" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                            <span>Đánh giá ưu nhược điểm khách quan nhất</span>
                        </div>

                        <button type="button" class="gvr-btn-watch js-open-gvr-video" data-video-src="<?php echo esc_attr($f_embed); ?>" data-video-title="<?php echo esc_attr($f_title); ?>">
                            <span>Xem video ngay</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- B. LƯỚI 4 VIDEO CON BÊN PHẢI (2x2 GRID DẠNG THẺ NGANG) -->
            <div class="gvr-side-list">
                <?php
                if ($has_real_data && $side_query->have_posts()) {
                    while ($side_query->have_posts()) {
                        $side_query->the_post();
                        $p_id       = get_the_ID();
                        $p_title    = get_the_title();
                        $p_desc     = get_field('video_desc', $p_id) ?: get_the_excerpt();
                        $p_dur      = get_field('video_duration', $p_id) ?: '05:00';
                        $p_views    = get_field('video_views_text', $p_id) ?: '25K lượt xem • 1 tuần trước';
                        $p_badge    = get_field('video_badge_tag', $p_id) ?: 'Review từ người thật';
                        $p_url      = get_field('video_url', $p_id);
                        $p_yt_info  = gobike_extract_youtube_info($p_url);
                        $p_thumb    = get_field('video_thumbnail', $p_id);
                        if (!$p_thumb) $p_thumb = $p_yt_info['thumbnail'];
                        $p_cat_terms= get_the_terms($p_id, 'video_category');
                        $p_cat_slug = (!empty($p_cat_terms) && !is_wp_error($p_cat_terms)) ? $p_cat_terms[0]->slug : 'all';
                        $p_embed    = $p_yt_info['embed_url'];
                        ?>
                        <div class="gvr-card-horizontal gvr-filterable-item js-open-gvr-video" data-cat="<?php echo esc_attr($p_cat_slug); ?>" data-video-src="<?php echo esc_attr($p_embed); ?>" data-video-title="<?php echo esc_attr($p_title); ?>">
                            <div class="gvr-ch-thumb">
                                <img src="<?php echo esc_url($p_thumb); ?>" alt="<?php echo esc_attr($p_title); ?>">
                                <div class="gvr-ch-play"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg></div>
                                <span class="gvr-ch-duration"><?php echo esc_html($p_dur); ?></span>
                            </div>
                            <div class="gvr-ch-info">
                                <h4 class="gvr-ch-title"><?php echo esc_html($p_title); ?></h4>
                                <p class="gvr-ch-desc"><?php echo wp_trim_words($p_desc, 14, '...'); ?></p>
                                <div class="gvr-ch-meta">
                                    <span class="gvr-ch-views"><?php echo esc_html($p_views); ?></span>
                                    <span class="gvr-ch-badge"><?php echo esc_html($p_badge); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    wp_reset_postdata();
                } else {
                    // Render 4 thẻ demo
                    foreach ($demo_items as $item) {
                        $p_embed = 'https://www.youtube.com/embed/' . $item['yt_id'] . '?autoplay=1&rel=0';
                        ?>
                        <div class="gvr-card-horizontal gvr-filterable-item js-open-gvr-video" data-cat="<?php echo esc_attr($item['category']); ?>" data-video-src="<?php echo esc_attr($p_embed); ?>" data-video-title="<?php echo esc_attr($item['title']); ?>">
                            <div class="gvr-ch-thumb">
                                <img src="<?php echo esc_url($item['thumb']); ?>" alt="<?php echo esc_attr($item['title']); ?>">
                                <div class="gvr-ch-play"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg></div>
                                <span class="gvr-ch-duration"><?php echo esc_html($item['duration']); ?></span>
                            </div>
                            <div class="gvr-ch-info">
                                <h4 class="gvr-ch-title"><?php echo esc_html($item['title']); ?></h4>
                                <p class="gvr-ch-desc"><?php echo esc_html($item['desc']); ?></p>
                                <div class="gvr-ch-meta">
                                    <span class="gvr-ch-views"><?php echo esc_html($item['views']); ?></span>
                                    <span class="gvr-ch-badge"><?php echo esc_html($item['badge']); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>

        <!-- 3. KHỐI MARKETING DƯỚI: "TẠI SAO NÊN XEM?" -->
        <div class="gvr-why-watch-wrap">
            <div class="gvr-ww-header">
                <div class="gvr-ww-title-box">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#149d29" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <h3>Tại sao nên xem?</h3>
                </div>
                <div class="gvr-ww-cta-box">
                    <span class="gvr-ww-slogan">Xem trước để mua đúng – không hối hận! ➔</span>
                    <a href="<?php echo esc_url($atts['view_all']); ?>" class="gvr-ww-btn">Khám phá review →</a>
                </div>
            </div>

            <div class="gvr-ww-grid">
                <!-- Thẻ 1 -->
                <div class="gvr-ww-card">
                    <div class="gvr-ww-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#149d29" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="gvr-ww-content">
                        <h4>Trải <span class="highlight">nghiệm thật</span><br>sau thời gian sử dụng</h4>
                        <p>Không kịch bản, không quảng cáo – chỉ có trải nghiệm thực tế.</p>
                        <span class="gvr-ww-tag">“Có đáng tiền không?”</span>
                    </div>
                </div>

                <!-- Thẻ 2 -->
                <div class="gvr-ww-card">
                    <div class="gvr-ww-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#149d29" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <circle cx="12" cy="12" r="6"></circle>
                            <circle cx="12" cy="12" r="2"></circle>
                        </svg>
                    </div>
                    <div class="gvr-ww-content">
                        <h4>Biết xe có <span class="highlight">hợp nhu cầu</span><br>của bạn không</h4>
                        <p>Xem người dùng có nhu cầu giống bạn chia sẻ cảm nhận thật.</p>
                        <span class="gvr-ww-tag">“Đi làm hằng ngày có tiện?”</span>
                    </div>
                </div>

                <!-- Thẻ 3 -->
                <div class="gvr-ww-card">
                    <div class="gvr-ww-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#149d29" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                        </svg>
                    </div>
                    <div class="gvr-ww-content">
                        <h4>Xem <span class="highlight">ưu nhược điểm</span><br>trước khi mua</h4>
                        <p>Đánh giá khách quan, giúp bạn có cái nhìn toàn diện.</p>
                        <span class="gvr-ww-tag">“Nên mua hay chưa?”</span>
                    </div>
                </div>

                <!-- Thẻ 4 -->
                <div class="gvr-ww-card">
                    <div class="gvr-ww-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#149d29" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                        </svg>
                    </div>
                    <div class="gvr-ww-content">
                        <h4>Khám phá <span class="highlight">cảm giác lái,</span><br>pin và quãng đường thực tế</h4>
                        <p>Từ đường phố, leo dốc đến quãng đường thực tế – tất cả đều có trong video.</p>
                        <span class="gvr-ww-tag">“Pin đi được bao xa thực tế?”</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}


/* ============================================================================
 * 4. RENDER MODAL POPUP XEM VIDEO TẠI WP_FOOTER
 * ============================================================================
 */
add_action('wp_footer', 'gobike_render_video_player_modal_footer', 9999);
function gobike_render_video_player_modal_footer()
{
    ?>
    <!-- MODAL XEM VIDEO YOUTUBE POPUP TOÀN MÀN HÌNH -->
    <div class="gvr-video-modal-overlay" id="js-gvr-video-modal" style="display: none;">
        <div class="gvr-vm-backdrop"></div>
        <div class="gvr-vm-dialog">
            <button type="button" class="gvr-vm-close" id="js-gvr-vm-close" aria-label="Đóng">&times;</button>
            <div class="gvr-vm-content">
                <div class="gvr-vm-iframe-wrap">
                    <iframe id="js-gvr-iframe" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
                <div class="gvr-vm-title-bar" id="js-gvr-vm-title"></div>
            </div>
        </div>
    </div>

    <!-- CSS SECTION VIDEO REVIEW & POPUP -->
    <style>
    /* SECTION CONTAINER */
    .gobike-video-review-section {
        width: 100%;
        margin: 35px 0 25px 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    }

    /* HEADER & TABS */
    .gvr-header-wrap {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 22px;
        flex-wrap: nowrap;
    }
    .gvr-title-box {
        flex-shrink: 0;
    }
    .gvr-main-title {
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px 0;
        letter-spacing: 0.5px;
    }
    .gvr-main-title .text-highlight {
        color: #149d29;
    }
    .gvr-sub-title {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    /* Tabs Filter: Tự động cuộn ngang mượt mà khi có nhiều tab, không đẩy nút hay tràn vỡ layout */
    .gvr-filter-tabs {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        flex: 1 1 auto;
        min-width: 0;
        padding: 4px 2px;
        scrollbar-width: none;
        -ms-overflow-style: none;
        -webkit-overflow-scrolling: touch;
        scroll-behavior: smooth;
    }
    .gvr-filter-tabs::-webkit-scrollbar {
        display: none;
    }
    .gvr-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 30px;
        padding: 6px 14px;
        margin: 0 !important;
        color: #334155;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: none !important;
        outline: none;
        flex-shrink: 0;
        white-space: nowrap;
    }
    .gvr-tab-btn svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        display: block;
        stroke: currentColor;
    }
    .gvr-tab-btn span {
        font-size: 14px;
        font-weight: 500;
        line-height: 1.2;
    }
    .gvr-tab-btn:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #0f172a;
    }
    .gvr-tab-btn.active {
        background: #149d29;
        border-color: #149d29;
        color: #ffffff;
        box-shadow: none !important;
    }
    .gvr-tab-btn.active svg {
        stroke: #ffffff;
    }
    .gvr-tab-btn.active span {
        color: #ffffff;
    }

    .gvr-view-all-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 14px;
        font-weight: 700;
        color: #149d29;
        text-decoration: none;
        transition: color 0.15s;
        margin-left: auto;
        flex-shrink: 0;
        white-space: nowrap;
    }
    .gvr-view-all-link:hover {
        color: #149d29;
        text-decoration: underline;
    }

    /* MAIN GRID: 1 FEATURED + 4 CARDS */
    .gvr-main-grid {
        display: grid;
        grid-template-columns: 1.15fr 1fr;
        gap: 8px;
        margin-bottom: 25px;
    }

    /* FEATURED CARD LỚN */
    .gvr-featured-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: none !important;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s;
    }
    .gvr-featured-card:hover {
        box-shadow: none !important;
    }
    .gvr-featured-media {
        position: relative;
        width: 100%;
        padding-top: 56.25%; /* 16:9 */
        overflow: hidden;
        cursor: pointer;
    }
    .gvr-featured-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.35s ease;
    }
    .gvr-featured-card:hover .gvr-featured-img {
        transform: scale(1.03);
    }
    .gvr-badge-featured {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #149d29;
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        letter-spacing: 0.3px;
        box-shadow: none !important;
    }
    .gvr-duration-badge {
        position: absolute;
        bottom: 12px;
        right: 12px;
        background: rgba(15, 23, 42, 0.85);
        color: #ffffff;
        font-size: 11.5px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
    }

    /* Nút Play Tròn */
    .gvr-play-button-wrap {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
    }
    .gvr-play-btn {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.65);
        border: 2px solid rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        box-shadow: none !important;
        transition: transform 0.2s, background 0.2s;
    }
    .gvr-featured-card:hover .gvr-play-btn {
        transform: scale(1.1);
        background: #149d29;
        border-color: #149d29;
    }

    .gvr-featured-body {
        padding: 18px 20px 20px 20px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .gvr-featured-title {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 8px 0;
        line-height: 1.35;
        cursor: pointer;
        transition: color 0.15s;
    }
    .gvr-featured-title:hover {
        color: #149d29;
    }
    .gvr-featured-desc {
        font-size: 13px;
        color: #475569;
        line-height: 1.5;
        margin: 0 0 14px 0;
    }

    /* 3 Cam kết Tags */
    .gvr-featured-tags {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-top: auto;
    }
    .gvr-ft-tag {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 4px 10px;
        font-size: 11.5px;
        color: #334155;
        font-weight: 600;
    }
    .gvr-btn-watch {
        margin-left: auto;
        background: #149d29;
        color: #ffffff;
        font-size: 12.5px;
        font-weight: 700;
        padding: 7px 14px;
        border-radius: 20px;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s;
    }
    .gvr-btn-watch:hover {
        background: #149d29;
    }

    /* LƯỚI 4 THẺ NGANG BÊN PHẢI */
    .gvr-side-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    .gvr-card-horizontal {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        cursor: pointer;
        box-shadow: none !important;
        transition: transform 0.2s;
    }
    .gvr-card-horizontal:hover {
        transform: translateY(-2px);
        box-shadow: none !important;
    }
    .gvr-ch-thumb {
        position: relative;
        width: 100%;
        padding-top: 56.25%;
        overflow: hidden;
        background: #0f172a;
    }
    .gvr-ch-thumb img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    .gvr-card-horizontal:hover .gvr-ch-thumb img {
        transform: scale(1.05);
    }
    .gvr-ch-play {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.65);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid rgba(255, 255, 255, 0.7);
        transition: background 0.2s;
    }
    .gvr-card-horizontal:hover .gvr-ch-play {
        background: #149d29;
        border-color: #149d29;
    }
    .gvr-ch-duration {
        position: absolute;
        bottom: 6px;
        right: 6px;
        background: rgba(15, 23, 42, 0.85);
        color: #ffffff;
        font-size: 10px;
        font-weight: 700;
        padding: 1px 6px;
        border-radius: 4px;
    }
    .gvr-ch-info {
        padding: 10px 12px 12px 12px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    .gvr-ch-title {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
        margin: 0 0 4px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .gvr-card-horizontal:hover .gvr-ch-title {
        color: #149d29;
    }
    .gvr-ch-desc {
        font-size: 12px;
        color: #64748b;
        line-height: 1.4;
        margin: 0 0 8px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .gvr-ch-meta {
        margin-top: auto;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
        width: 100%;
    }
    .gvr-ch-views {
        font-size: 12px;
        color: #94a3b8;
        line-height: 1.3;
    }
    .gvr-ch-badge {
        background: #ecfdf5;
        color: #149d29;
        font-size: 12px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 4px;
        line-height: 1.3;
        display: inline-block;
    }

    /* KHỐI "TẠI SAO NÊN XEM?" */
    .gvr-why-watch-wrap {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px 22px 20px 22px;
    }
    .gvr-ww-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .gvr-ww-title-box {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .gvr-ww-title-box h3 {
        font-size: 19px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .gvr-ww-cta-box {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .gvr-ww-slogan {
        font-size: 13.5px;
        font-style: italic;
        color: #149d29;
        font-weight: 600;
    }
    .gvr-ww-btn {
        background: #149d29;
        color: #ffffff;
        font-size: 13px;
        font-weight: 700;
        padding: 8px 16px;
        border-radius: 20px;
        text-decoration: none;
        transition: background 0.2s;
    }
    .gvr-ww-btn:hover {
        background: #149d29;
        color: #ffffff;
    }
    .gvr-ww-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
    }
    .gvr-ww-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        box-shadow: none !important;
    }
    .gvr-ww-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #ecfdf5;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .gvr-ww-content {
        flex: 1;
    }
    .gvr-ww-content h4 {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
        margin: 0 0 4px 0;
    }
    .gvr-ww-content h4 .highlight {
        color: #149d29;
    }
    .gvr-ww-content p {
        font-size: 11.5px;
        color: #64748b;
        line-height: 1.4;
        margin: 0 0 6px 0;
    }
    .gvr-ww-tag {
        display: inline-block;
        background: #f1f5f9;
        color: #334155;
        font-size: 11px;
        font-style: italic;
        padding: 2px 8px;
        border-radius: 12px;
    }

    /* MODAL POPUP */
    .gvr-video-modal-overlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        z-index: 999999999 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .gvr-vm-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }
    .gvr-vm-dialog {
        position: relative;
        z-index: 1000000000;
        width: 100%;
        max-width: 850px;
        background: #000000;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: none !important;
        animation: gvrModalPop 0.25s ease-out;
    }
    @keyframes gvrModalPop {
        from { transform: scale(0.92); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .gvr-vm-close {
        position: absolute;
        top: 10px;
        right: 14px;
        background: rgba(0, 0, 0, 0.6);
        border: none;
        color: #ffffff;
        font-size: 28px;
        line-height: 1;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        cursor: pointer;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }
    .gvr-vm-close:hover {
        background: #ef4444;
    }
    .gvr-vm-iframe-wrap {
        position: relative;
        padding-top: 56.25%; /* 16:9 */
        background: #000000;
    }
    .gvr-vm-iframe-wrap iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    .gvr-vm-title-bar {
        background: #0f172a;
        color: #ffffff;
        font-size: 14px;
        font-weight: 600;
        padding: 12px 18px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .gvr-main-grid {
            grid-template-columns: 1fr;
        }
        .gvr-ww-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 640px) {
        .gvr-side-list {
            grid-template-columns: 1fr;
        }
        .gvr-ww-grid {
            grid-template-columns: 1fr;
        }
        .gvr-header-wrap {
            flex-direction: column;
            align-items: flex-start;
        }
        .gvr-view-all-link {
            margin-left: 0;
        }
        .gvr-featured-title {
            font-size: 16px;
        }
        .gvr-main-title {
            font-size: 20px;
        }
        .gvr-ww-cta-box {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }
    }
    </style>

    <!-- SCRIPT CHUYỂN TAB DANH MỤC & MỞ POPUP VIDEO -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('js-gvr-video-modal');
        var iframe = document.getElementById('js-gvr-iframe');
        var titleBar = document.getElementById('js-gvr-vm-title');
        var closeBtn = document.getElementById('js-gvr-vm-close');
        var backdrop = modal ? modal.querySelector('.gvr-vm-backdrop') : null;

        // Đảm bảo modal nằm ở root document.body
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        // 1. MỞ VIDEO POPUP
        document.addEventListener('click', function(e) {
            var trigger = e.target.closest('.js-open-gvr-video');
            if (trigger) {
                e.preventDefault();
                var videoSrc = trigger.getAttribute('data-video-src');
                var videoTitle = trigger.getAttribute('data-video-title');

                if (videoSrc && iframe && modal) {
                    iframe.src = videoSrc;
                    if (titleBar) titleBar.textContent = videoTitle || 'Video Review GoBike';
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            }
        });

        // 2. ĐÓNG VIDEO POPUP (Dừng phát âm thanh ngay lập tức)
        function closeVideoModal() {
            if (modal && iframe) {
                modal.style.display = 'none';
                iframe.src = '';
                document.body.style.overflow = '';
            }
        }

        if (closeBtn) closeBtn.addEventListener('click', closeVideoModal);
        if (backdrop) backdrop.addEventListener('click', closeVideoModal);

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
                closeVideoModal();
            }
        });

        // 3. TAB FILTER DANH MỤC
        var tabBtns = document.querySelectorAll('.gvr-tab-btn');
        var filterItems = document.querySelectorAll('.gvr-filterable-item');

        tabBtns.forEach(function(btn) {
            btn.addEventListener('click', function() {
                tabBtns.forEach(function(b) { b.classList.remove('active'); });
                btn.classList.add('active');

                var selectedCat = btn.getAttribute('data-cat');

                filterItems.forEach(function(item) {
                    var itemCat = item.getAttribute('data-cat');
                    if (selectedCat === 'all' || itemCat === selectedCat) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });

        // 4. CUỘN NGANG DANH SÁCH TAB BẰNG CON LĂN CHUỘT
        var filterTabs = document.querySelector('.gvr-filter-tabs');
        if (filterTabs) {
            filterTabs.addEventListener('wheel', function(e) {
                if (e.deltaY !== 0) {
                    e.preventDefault();
                    filterTabs.scrollLeft += e.deltaY;
                }
            }, { passive: false });
        }
    });
    </script>
    <?php
}
