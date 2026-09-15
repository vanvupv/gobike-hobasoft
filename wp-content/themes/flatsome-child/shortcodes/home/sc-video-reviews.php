<?php
/**
 * Shortcode: [gobike_home_video_reviews] & [gobike_video_reviews]
 * Section: VIDEO REVIEW THỰC TẾ (Dạng Swiper Slider chuẩn Ảnh 2)
 */

if (!defined('ABSPATH')) {
    exit;
}

// 1. Đăng ký CPT Video Reviews nếu chưa có
add_action('init', 'gobike_register_cpt_video_reviews_modern', 0);
function gobike_register_cpt_video_reviews_modern()
{
    if (post_type_exists('video_review')) {
        return;
    }

    $labels = array(
        'name'               => 'Video Reviews',
        'singular_name'      => 'Video Review',
        'menu_name'          => 'Video Reviews',
        'name_admin_bar'     => 'Video Review',
        'add_new'            => 'Thêm Video mới',
        'add_new_item'       => 'Thêm Video Review mới',
        'new_item'           => 'Video Review mới',
        'edit_item'          => 'Chỉnh sửa Video Review',
        'view_item'          => 'Xem Video Review',
        'all_items'          => 'Tất cả Video Reviews',
        'search_items'       => 'Tìm kiếm Video Review',
        'not_found'          => 'Không tìm thấy Video nào',
        'not_found_in_trash' => 'Không có Video nào trong thùng rác',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'video-review'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-video-alt3',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest'       => true,
    );

    register_post_type('video_review', $args);

    // Taxonomy chuyên mục video
    if (!taxonomy_exists('video_category')) {
        register_taxonomy('video_category', array('video_review'), array(
            'hierarchical'      => true,
            'labels'            => array(
                'name'          => 'Chuyên mục Video',
                'singular_name' => 'Chuyên mục Video',
                'search_items'  => 'Tìm kiếm chuyên mục',
                'all_items'     => 'Tất cả chuyên mục',
                'edit_item'     => 'Chỉnh sửa chuyên mục',
                'update_item'   => 'Cập nhật chuyên mục',
                'add_new_item'  => 'Thêm chuyên mục mới',
                'new_item_name' => 'Tên chuyên mục mới',
                'menu_name'     => 'Chuyên mục Video',
            ),
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'video-category'),
            'show_in_rest'      => true,
        ));
    }
}

// 2. Hàm trích xuất thông tin YouTube
if (!function_exists('gobike_extract_youtube_info')) {
    function gobike_extract_youtube_info($url)
    {
        $id = '';
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/|youtube\.com\/shorts\/)([^"&?\/ ]{11})/i', $url, $match)) {
            $id = $match[1];
        }

        return array(
            'id'        => $id,
            'embed_url' => $id ? 'https://www.youtube.com/embed/' . $id . '?autoplay=1&playsinline=1&rel=0&modestbranding=1' : '',
            'thumbnail' => $id ? 'https://img.youtube.com/vi/' . $id . '/hqdefault.jpg' : '',
        );
    }
}

// 3. Đăng ký Shortcode [gobike_home_video_reviews] & [gobike_video_reviews]
add_shortcode('gobike_home_video_reviews', 'gobike_render_home_video_reviews');
add_shortcode('gobike_video_reviews', 'gobike_render_home_video_reviews');

function gobike_render_home_video_reviews($atts)
{
    $atts = shortcode_atts(array(
        'title'    => 'VIDEO REVIEW THỰC TẾ',
        'subtitle' => 'Trải nghiệm thật • Đánh giá thật • Giúp bạn chọn đúng xe',
        'view_all' => home_url('/video-review/'),
        'limit'    => 10,
    ), $atts, 'gobike_home_video_reviews');

    // Query các bài viết video
    $v_args = array(
        'post_type'      => 'video_review',
        'post_status'    => 'publish',
        'posts_per_page' => intval($atts['limit']) > 0 ? intval($atts['limit']) : 10,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    $v_query = new WP_Query($v_args);

    $video_items = array();

    if ($v_query->have_posts()) {
        while ($v_query->have_posts()) {
            $v_query->the_post();
            $vid_id    = get_the_ID();
            $title     = get_the_title();
            $url       = get_field('video_url', $vid_id);
            $yt_info   = gobike_extract_youtube_info($url);
            $thumb     = get_field('video_thumbnail', $vid_id) ?: (get_the_post_thumbnail_url($vid_id, 'large') ?: $yt_info['thumbnail']);
            $duration  = get_field('video_duration', $vid_id) ?: '08:24';
            $subtitle  = get_field('video_subtitle', $vid_id) ?: (get_field('video_desc', $vid_id) ?: 'Di chuyển xanh - Cuộc sống tốt hơn');
            $embed     = !empty($yt_info['embed_url']) ? $yt_info['embed_url'] : ($url ? 'https://www.youtube.com/embed/' . $url . '?autoplay=1&rel=0' : '');

            $video_items[] = array(
                'id'       => $vid_id,
                'title'    => $title,
                'subtitle' => $subtitle,
                'duration' => $duration,
                'thumb'    => $thumb ?: 'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=800&auto=format&fit=crop&q=80',
                'embed'    => $embed,
            );
        }
        wp_reset_postdata();
    }

    // Dữ liệu mẫu hoàn hảo nếu database chưa có bài viết
    if (empty($video_items)) {
        $video_items = array(
            array(
                'id'       => 'demo-1',
                'title'    => 'Trải nghiệm thực tế GoBike City 1',
                'subtitle' => 'Di chuyển xanh - Cuộc sống tốt hơn',
                'duration' => '8:24',
                'thumb'    => 'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=800&auto=format&fit=crop&q=80',
                'embed'    => 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&rel=0',
            ),
            array(
                'id'       => 'demo-2',
                'title'    => 'Review GOBIKE F200 gấp gọn',
                'subtitle' => 'Tiện lợi đi phố và mang theo tàu điện',
                'duration' => '6:15',
                'thumb'    => 'https://images.unsplash.com/photo-1507035895480-2b3156c31fc8?w=800&auto=format&fit=crop&q=80',
                'embed'    => 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&rel=0',
            ),
            array(
                'id'       => 'demo-3',
                'title'    => 'Test leo dốc GoBike M3 cùng người dùng',
                'subtitle' => 'Khám phá sức mạnh động cơ 500W',
                'duration' => '9:40',
                'thumb'    => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=800&auto=format&fit=crop&q=80',
                'embed'    => 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&rel=0',
            ),
            array(
                'id'       => 'demo-4',
                'title'    => 'Đánh giá chi tiết pin Lithium GoBike',
                'subtitle' => 'Chạy được bao xa sau 1 lần sạc đầy?',
                'duration' => '7:50',
                'thumb'    => 'https://images.unsplash.com/photo-1511994298241-608e28f14fde?w=800&auto=format&fit=crop&q=80',
                'embed'    => 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&rel=0',
            ),
            array(
                'id'       => 'demo-5',
                'title'    => 'Người dùng chia sẻ sau 6 tháng',
                'subtitle' => 'Tiết kiệm chi phí và nâng cao sức khỏe',
                'duration' => '5:30',
                'thumb'    => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=800&auto=format&fit=crop&q=80',
                'embed'    => 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&rel=0',
            ),
        );
    }

    $unique_id = 'gvr_' . substr(md5(rand(100, 999) . time()), 0, 8);

    ob_start();
    ?>
    <!-- SECTION VIDEO REVIEW THỰC TẾ (SWIPER SLIDE CHUẨN ẢNH 2) -->
    <div class="gobike-video-review-section" id="<?php echo esc_attr($unique_id); ?>">
        <!-- 1. HEADER KHỐI (Chuẩn Ảnh 2: Tiêu đề xanh bên trái, link Xem tất cả bên phải) -->
        <div class="gvr-header-wrap">
            <div class="gvr-title-box">
                <h2 class="gvr-main-title"><?php echo esc_html($atts['title']); ?></h2>
            </div>
            <a href="<?php echo esc_url($atts['view_all']); ?>" class="gvr-view-all-link">
                <span>Xem tất cả</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>

        <!-- 2. SWIPER SLIDER CONTAINER -->
        <div class="gvr-slider-container">
            <div class="swiper gvr-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($video_items as $vid): ?>
                        <div class="swiper-slide gvr-slide">
                            <div class="gvr-card js-open-gvr-video" 
                                 data-video-src="<?php echo esc_attr($vid['embed']); ?>" 
                                 data-video-title="<?php echo esc_attr($vid['title']); ?>">
                                
                                <!-- Thumbnail 16:9 với góc bo tròn 12px -->
                                <div class="gvr-thumb-box">
                                    <img src="<?php echo esc_url($vid['thumb']); ?>" alt="<?php echo esc_attr($vid['title']); ?>" class="gvr-thumb-img" loading="lazy" />
                                    
                                    <!-- Dải chữ nổi góc trên: Logo GoBike + Tiêu đề -->
                                    <div class="gvr-thumb-overlay">
                                        <span class="gvr-thumb-brand">GoBike</span>
                                        <span class="gvr-thumb-title"><?php echo esc_html($vid['title']); ?></span>
                                    </div>

                                    <!-- Nút Play tròn ở chính giữa (Chuẩn Ảnh 2) -->
                                    <div class="gvr-play-circle" role="button" aria-label="Phát video">
                                        <svg viewBox="0 0 24 24" width="20" height="20" fill="#ffffff">
                                            <polygon points="6 3 20 12 6 21 6 3"></polygon>
                                        </svg>
                                    </div>

                                    <!-- Badge thời lượng góc dưới phải (Chuẩn Ảnh 2) -->
                                    <span class="gvr-duration-badge"><?php echo esc_html($vid['duration']); ?></span>
                                </div>

                                <!-- Cụm thông tin dưới thumbnail: Tiêu đề + Mô tả phụ + Icon 3 chấm dọc -->
                                <div class="gvr-meta-row">
                                    <div class="gvr-meta-text">
                                        <h3 class="gvr-video-name" title="<?php echo esc_attr($vid['title']); ?>">
                                            <?php echo esc_html($vid['title']); ?>
                                        </h3>
                                        <p class="gvr-video-desc" title="<?php echo esc_attr($vid['subtitle']); ?>">
                                            <?php echo esc_html($vid['subtitle']); ?>
                                        </p>
                                    </div>
                                    <button type="button" class="gvr-dots-btn" aria-label="Tùy chọn">
                                        <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                            <circle cx="12" cy="5" r="2"></circle>
                                            <circle cx="12" cy="12" r="2"></circle>
                                            <circle cx="12" cy="19" r="2"></circle>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Nút Next/Prev Swiper hình tròn trắng nổi trên slider (Chuẩn Ảnh 2) -->
            <button type="button" class="gvr-swiper-arrow gvr-swiper-prev" aria-label="Slide trước">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            <button type="button" class="gvr-swiper-arrow gvr-swiper-next" aria-label="Slide tiếp theo">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>

            <!-- Chấm phân trang mobile -->
            <div class="swiper-pagination gvr-swiper-pagination"></div>
        </div>

        <!-- 3. MODAL POPUP PHÁT VIDEO LIGHTBOX -->
        <div id="js-gvr-video-modal" class="gvr-video-modal" style="display:none;" role="dialog" aria-modal="true">
            <div class="gvr-vm-backdrop"></div>
            <div class="gvr-vm-dialog">
                <div class="gvr-vm-content">
                    <div class="gvr-vm-header">
                        <span id="js-gvr-vm-title" class="gvr-vm-title">Video Review</span>
                        <button type="button" id="js-gvr-vm-close" class="gvr-vm-close" aria-label="Đóng video">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <div class="gvr-vm-body">
                        <div class="gvr-vm-iframe-wrap">
                            <iframe id="js-gvr-iframe" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPT KHỞI TẠO SWIPER & XỬ LÝ POPUP VIDEO -->
    <script>
        (function() {
            function initGvrSection_<?php echo esc_js($unique_id); ?>() {
                var root = document.getElementById('<?php echo esc_js($unique_id); ?>');
                if (!root) return;

                // Khởi tạo Swiper
                function runSwiper() {
                    if (typeof Swiper === 'undefined') {
                        setTimeout(runSwiper, 100);
                        return;
                    }
                    var swiperEl = root.querySelector('.gvr-swiper');
                    if (swiperEl && !swiperEl.swiper) {
                        new Swiper(swiperEl, {
                            slidesPerView: 1.25,
                            spaceBetween: 12,
                            watchOverflow: true,
                            navigation: {
                                nextEl: root.querySelector('.gvr-swiper-next'),
                                prevEl: root.querySelector('.gvr-swiper-prev'),
                            },
                            pagination: {
                                el: root.querySelector('.gvr-swiper-pagination'),
                                clickable: true,
                            },
                            breakpoints: {
                                550: {
                                    slidesPerView: 2,
                                    spaceBetween: 14,
                                },
                                850: {
                                    slidesPerView: 2.5,
                                    spaceBetween: 16,
                                },
                                1025: {
                                    slidesPerView: 3.2,
                                    spaceBetween: 18,
                                },
                                1280: {
                                    slidesPerView: 3.25,
                                    spaceBetween: 20,
                                }
                            }
                        });
                    }
                }
                runSwiper();

                // Quản lý Modal Popup Video
                var modal    = document.getElementById('js-gvr-video-modal');
                var iframe   = document.getElementById('js-gvr-iframe');
                var titleBar = document.getElementById('js-gvr-vm-title');
                var closeBtn = document.getElementById('js-gvr-vm-close');
                var backdrop = modal ? modal.querySelector('.gvr-vm-backdrop') : null;

                if (modal && modal.parentElement !== document.body) {
                    document.body.appendChild(modal);
                }

                function openModal(src, title) {
                    if (!modal || !iframe || !src) return;
                    iframe.src = src;
                    if (titleBar) titleBar.textContent = title || 'Video Review GoBike';
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }

                function closeModal() {
                    if (!modal || !iframe) return;
                    modal.style.display = 'none';
                    iframe.src = '';
                    document.body.style.overflow = '';
                }

                root.addEventListener('click', function(e) {
                    var card = e.target.closest('.js-open-gvr-video');
                    if (card && !e.target.closest('.gvr-dots-btn')) {
                        e.preventDefault();
                        var src   = card.getAttribute('data-video-src');
                        var title = card.getAttribute('data-video-title');
                        openModal(src, title);
                    }
                });

                if (closeBtn) closeBtn.onclick = closeModal;
                if (backdrop) backdrop.onclick = closeModal;

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
                        closeModal();
                    }
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initGvrSection_<?php echo esc_js($unique_id); ?>);
            } else {
                initGvrSection_<?php echo esc_js($unique_id); ?>();
            }
        })();
    </script>
    <?php
    return ob_get_clean();
}
