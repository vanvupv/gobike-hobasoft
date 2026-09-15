<?php
/**
 * Shortcode: [gobike_experience_videos] & [gobike_product_videos]
 * 
 * Section: NGƯỜI THẬT - XE THẬT - TRẢI NGHIỆM THẬT (Dạng Swiper Slider chuẩn Ảnh 2)
 * 
 * @package Flatsome-Child
 */

if (!defined('ABSPATH')) {
    exit;
}

/* ============================================================================
 * 1. HÀM RENDER SHORTCODE [gobike_experience_videos]
 * ============================================================================
 */
add_shortcode('gobike_experience_videos', 'gobike_render_experience_videos_shortcode');
add_shortcode('gobike_product_videos', 'gobike_render_experience_videos_shortcode');

function gobike_render_experience_videos_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'title'    => 'NGƯỜI THẬT - XE THẬT - TRẢI NGHIỆM THẬT',
        'view_all' => get_post_type_archive_link('video_review') ?: home_url('/video-review/'),
        'limit'    => 8,
    ), $atts, 'gobike_experience_videos');

    // Query các bài viết CPT video_review
    $query_args = array(
        'post_type'      => 'video_review',
        'posts_per_page' => intval($atts['limit']),
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    );
    $v_query = new WP_Query($query_args);

    $items = array();

    if ($v_query->have_posts()) {
        while ($v_query->have_posts()) {
            $v_query->the_post();
            $vid_id      = get_the_ID();
            $video_url   = get_field('video_url', $vid_id);
            $yt_info     = function_exists('gobike_extract_youtube_info') ? gobike_extract_youtube_info($video_url) : array('id' => '', 'embed_url' => '', 'thumbnail' => '');
            $thumb       = get_field('video_thumbnail', $vid_id) ?: (get_the_post_thumbnail_url($vid_id, 'large') ?: $yt_info['thumbnail']);
            $title       = get_the_title();
            $duration    = get_field('video_duration', $vid_id) ?: '08:24';
            $embed_src   = !empty($yt_info['embed_url']) ? $yt_info['embed_url'] : ($video_url ? 'https://www.youtube.com/embed/' . $video_url . '?autoplay=1&rel=0' : '');

            // Lấy sản phẩm WooCommerce liên kết nếu có
            $rel_prod_id = get_field('related_product', $vid_id);
            $subtitle    = 'Di chuyển xanh - Cuộc sống tốt hơn';
            if ($rel_prod_id && function_exists('wc_get_product')) {
                $p = wc_get_product($rel_prod_id);
                if ($p) {
                    $subtitle = $p->get_name() . ' • ' . strip_tags($p->get_price_html());
                }
            } else {
                $custom_sub = get_field('video_subtitle', $vid_id) ?: get_field('video_desc', $vid_id);
                if ($custom_sub) $subtitle = $custom_sub;
            }

            $items[] = array(
                'id'       => $vid_id,
                'title'    => $title,
                'subtitle' => $subtitle,
                'duration' => $duration,
                'thumb'    => $thumb ?: 'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=800&auto=format&fit=crop&q=80',
                'embed'    => $embed_src,
            );
        }
        wp_reset_postdata();
    }

    // Dữ liệu mẫu hoàn hảo nếu database chưa có bài viết
    if (empty($items)) {
        $items = array(
            array(
                'id'       => 'exp-1',
                'title'    => 'Trải nghiệm thực tế GoBike City 1',
                'subtitle' => 'PHOENIX M3 • 16.990.000₫',
                'duration' => '8:24',
                'thumb'    => 'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=800&auto=format&fit=crop&q=80',
                'embed'    => 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&rel=0',
            ),
            array(
                'id'       => 'exp-2',
                'title'    => 'Review GOBIKE F200 gấp gọn',
                'subtitle' => 'SHENGMILO S600 • 22.900.000₫',
                'duration' => '6:15',
                'thumb'    => 'https://images.unsplash.com/photo-1507035895480-2b3156c31fc8?w=800&auto=format&fit=crop&q=80',
                'embed'    => 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&rel=0',
            ),
            array(
                'id'       => 'exp-3',
                'title'    => 'Test leo dốc GoBike M3 cùng người dùng',
                'subtitle' => 'RAPIDX P1 • 18.500.000₫',
                'duration' => '9:40',
                'thumb'    => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=800&auto=format&fit=crop&q=80',
                'embed'    => 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&rel=0',
            ),
            array(
                'id'       => 'exp-4',
                'title'    => 'Đánh giá chi tiết pin Lithium GoBike',
                'subtitle' => 'TRƯỜNG VƯƠNG X1 • 19.990.000₫',
                'duration' => '7:50',
                'thumb'    => 'https://images.unsplash.com/photo-1511994298241-608e28f14fde?w=800&auto=format&fit=crop&q=80',
                'embed'    => 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&rel=0',
            ),
            array(
                'id'       => 'exp-5',
                'title'    => 'Người dùng chia sẻ sau 6 tháng',
                'subtitle' => 'Tiết kiệm chi phí và nâng cao sức khỏe',
                'duration' => '5:30',
                'thumb'    => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=800&auto=format&fit=crop&q=80',
                'embed'    => 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&rel=0',
            ),
        );
    }

    $unique_id = 'gev_' . substr(md5(rand(100, 999) . time()), 0, 8);

    ob_start();
    ?>
    <!-- KHỐI SHORTCODE: NGƯỜI THẬT - XE THẬT - TRẢI NGHIỆM THẬT (SWIPER SLIDER CHUẨN ẢNH 2) -->
    <div class="gobike-experience-videos-wrap" id="<?php echo esc_attr($unique_id); ?>">
        <!-- 1. HEADER (Chuẩn Ảnh 2) -->
        <div class="gev-header">
            <div class="gev-title-box">
                <h2 class="gev-main-title"><?php echo esc_html($atts['title']); ?></h2>
            </div>
            <a href="<?php echo esc_url($atts['view_all']); ?>" class="gev-view-all">
                <span>Xem tất cả</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>

        <!-- 2. SWIPER SLIDER CONTAINER -->
        <div class="gev-slider-container">
            <div class="swiper gev-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($items as $item): ?>
                        <div class="swiper-slide gev-slide">
                            <div class="gvr-card js-open-gev-video" 
                                 data-video-src="<?php echo esc_attr($item['embed']); ?>" 
                                 data-video-title="<?php echo esc_attr($item['title']); ?>">
                                
                                <!-- Thumbnail 16:9 với góc bo tròn 12px -->
                                <div class="gvr-thumb-box">
                                    <img src="<?php echo esc_url($item['thumb']); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="gvr-thumb-img" loading="lazy" />
                                    
                                    <!-- Dải chữ nổi góc trên: Logo GoBike + Tiêu đề -->
                                    <div class="gvr-thumb-overlay">
                                        <span class="gvr-thumb-brand">GoBike</span>
                                        <span class="gvr-thumb-title"><?php echo esc_html($item['title']); ?></span>
                                    </div>

                                    <!-- Nút Play tròn ở chính giữa (Chuẩn Ảnh 2) -->
                                    <div class="gvr-play-circle" role="button" aria-label="Phát video">
                                        <svg viewBox="0 0 24 24" width="20" height="20" fill="#ffffff">
                                            <polygon points="6 3 20 12 6 21 6 3"></polygon>
                                        </svg>
                                    </div>

                                    <!-- Badge thời lượng góc dưới phải (Chuẩn Ảnh 2) -->
                                    <span class="gvr-duration-badge"><?php echo esc_html($item['duration']); ?></span>
                                </div>

                                <!-- Cụm thông tin dưới thumbnail: Tiêu đề + Mô tả phụ + Icon 3 chấm dọc -->
                                <div class="gvr-meta-row">
                                    <div class="gvr-meta-text">
                                        <h3 class="gvr-video-name" title="<?php echo esc_attr($item['title']); ?>">
                                            <?php echo esc_html($item['title']); ?>
                                        </h3>
                                        <p class="gvr-video-desc" title="<?php echo esc_attr($item['subtitle']); ?>">
                                            <?php echo esc_html($item['subtitle']); ?>
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
            <button type="button" class="gvr-swiper-arrow gev-swiper-prev" aria-label="Slide trước">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>
            <button type="button" class="gvr-swiper-arrow gev-swiper-next" aria-label="Slide tiếp theo">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>

            <!-- Chấm phân trang mobile -->
            <div class="swiper-pagination gev-swiper-pagination"></div>
        </div>

        <!-- 3. MODAL POPUP PHÁT VIDEO LIGHTBOX -->
        <div id="js-gev-video-modal" class="gvr-video-modal" style="display:none;" role="dialog" aria-modal="true">
            <div class="gvr-vm-backdrop"></div>
            <div class="gvr-vm-dialog">
                <div class="gvr-vm-content">
                    <div class="gvr-vm-header">
                        <span id="js-gev-vm-title" class="gvr-vm-title">Video Trải Nghiệm</span>
                        <button type="button" id="js-gev-vm-close" class="gvr-vm-close" aria-label="Đóng video">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>
                    <div class="gvr-vm-body">
                        <div class="gvr-vm-iframe-wrap">
                            <iframe id="js-gev-iframe" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPT KHỞI TẠO SWIPER & XỬ LÝ POPUP VIDEO -->
    <script>
        (function() {
            function initGevSection_<?php echo esc_js($unique_id); ?>() {
                var root = document.getElementById('<?php echo esc_js($unique_id); ?>');
                if (!root) return;

                // Khởi tạo Swiper
                function runSwiper() {
                    if (typeof Swiper === 'undefined') {
                        setTimeout(runSwiper, 100);
                        return;
                    }
                    var swiperEl = root.querySelector('.gev-swiper');
                    if (swiperEl && !swiperEl.swiper) {
                        new Swiper(swiperEl, {
                            slidesPerView: 1.25,
                            spaceBetween: 12,
                            watchOverflow: true,
                            navigation: {
                                nextEl: root.querySelector('.gev-swiper-next'),
                                prevEl: root.querySelector('.gev-swiper-prev'),
                            },
                            pagination: {
                                el: root.querySelector('.gev-swiper-pagination'),
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
                var modal    = document.getElementById('js-gev-video-modal');
                var iframe   = document.getElementById('js-gev-iframe');
                var titleBar = document.getElementById('js-gev-vm-title');
                var closeBtn = document.getElementById('js-gev-vm-close');
                var backdrop = modal ? modal.querySelector('.gvr-vm-backdrop') : null;

                if (modal && modal.parentElement !== document.body) {
                    document.body.appendChild(modal);
                }

                function openModal(src, title) {
                    if (!modal || !iframe || !src) return;
                    iframe.src = src;
                    if (titleBar) titleBar.textContent = title || 'Video Trải Nghiệm GoBike';
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
                    var card = e.target.closest('.js-open-gev-video');
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
                document.addEventListener('DOMContentLoaded', initGevSection_<?php echo esc_js($unique_id); ?>);
            } else {
                initGevSection_<?php echo esc_js($unique_id); ?>();
            }
        })();
    </script>
    <?php
    return ob_get_clean();
}
