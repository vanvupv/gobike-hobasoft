<?php
/**
 * Shortcode: [gobike_experience_videos]
 * 
 * Section: NGƯỜI THẬT - XE THẬT - TRẢI NGHIỆM THẬT (Phong cách YouTube Shorts & CellphoneS)
 * Chức năng:
 * 1. Hiển thị 4 thẻ Video Shorts dọc 9:16 có chữ nghệ thuật + Lượt xem + Badge Shorts
 * 2. Gắn kèm Mini Card sản phẩm WooCommerce bên dưới (Ảnh, Tên, Danh mục, Giá đỏ, Nút giỏ hàng)
 * 3. Popup phát Video Shorts định dạng dọc chuẩn điện thoại kèm thông tin mua hàng
 * 4. Tự động liên kết 2 chiều giữa CPT video_review và WooCommerce Product
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
function gobike_render_experience_videos_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'title'    => 'NGƯỜI THẬT - XE THẬT - TRẢI NGHIỆM THẬT',
        'view_all' => get_post_type_archive_link('video_review') ?: home_url('/video-review/'),
        'limit'    => 4,
    ), $atts, 'gobike_experience_videos');

    // Query 4 bài viết CPT video_review mới nhất
    $query_args = array(
        'post_type'      => 'video_review',
        'posts_per_page' => intval($atts['limit']),
        'post_status'    => 'publish',
    );
    $v_query = new WP_Query($query_args);

    // Dữ liệu mẫu chuẩn 100% theo ảnh thiết kế (Khi chưa có bài viết)
    $demo_shorts = array(
        array(
            'video_title'   => 'Đi làm mỗi ngày thật nhẹ nhàng!',
            'views'         => '125K lượt xem',
            'thumb'         => 'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=600&auto=format&fit=crop&q=80',
            'yt_id'         => 'dQw4w9WgXcQ',
            'prod_id'       => 0,
            'prod_name'     => 'PHOENIX M3',
            'prod_cat'      => 'Xe đạp trợ lực điện',
            'prod_price'    => '16.990.000đ',
            'prod_thumb'    => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?w=200&auto=format&fit=crop&q=80',
            'prod_url'      => home_url('/cua-hang/'),
        ),
        array(
            'video_title'   => 'Khám phá thành phố theo cách riêng!',
            'views'         => '98K lượt xem',
            'thumb'         => 'https://images.unsplash.com/photo-1507035895480-2b3156c31fc8?w=600&auto=format&fit=crop&q=80',
            'yt_id'         => 'dQw4w9WgXcQ',
            'prod_id'       => 0,
            'prod_name'     => 'SHENGMILO S600',
            'prod_cat'      => 'Xe đạp trợ lực điện',
            'prod_price'    => '22.900.000đ',
            'prod_thumb'    => 'https://images.unsplash.com/photo-1532298229144-0ec0c57515c7?w=200&auto=format&fit=crop&q=80',
            'prod_url'      => home_url('/cua-hang/'),
        ),
        array(
            'video_title'   => 'Nhỏ gọn đồng hành mọi hành trình!',
            'views'         => '76K lượt xem',
            'thumb'         => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?w=600&auto=format&fit=crop&q=80',
            'yt_id'         => 'dQw4w9WgXcQ',
            'prod_id'       => 0,
            'prod_name'     => 'RAPIDX P1',
            'prod_cat'      => 'Xe gấp trợ lực điện',
            'prod_price'    => '18.500.000đ',
            'prod_thumb'    => 'https://images.unsplash.com/photo-1571068316344-75bc76f77890?w=200&auto=format&fit=crop&q=80',
            'prod_url'      => home_url('/cua-hang/'),
        ),
        array(
            'video_title'   => 'Thêm năng lượng cho những chuyến đi xa!',
            'views'         => '62K lượt xem',
            'thumb'         => 'https://images.unsplash.com/photo-1511994298241-608e28f14fde?w=600&auto=format&fit=crop&q=80',
            'yt_id'         => 'dQw4w9WgXcQ',
            'prod_id'       => 0,
            'prod_name'     => 'TRƯỜNG VƯƠNG X1',
            'prod_cat'      => 'Xe đạp trợ lực điện',
            'prod_price'    => '19.990.000đ',
            'prod_thumb'    => 'https://images.unsplash.com/photo-1507035895480-2b3156c31fc8?w=200&auto=format&fit=crop&q=80',
            'prod_url'      => home_url('/cua-hang/'),
        ),
    );

    // Tự động tìm sản phẩm thực tế trong WooCommerce nếu chưa có bài CPT
    if (!$v_query->have_posts() && function_exists('wc_get_products')) {
        $real_products = wc_get_products(array(
            'limit'   => 4,
            'status'  => 'publish',
            'orderby' => 'date',
            'order'   => 'DESC',
        ));
        if (!empty($real_products)) {
            foreach ($real_products as $idx => $rp) {
                if (isset($demo_shorts[$idx])) {
                    $demo_shorts[$idx]['prod_id']    = $rp->get_id();
                    $demo_shorts[$idx]['prod_name']  = $rp->get_name();
                    $demo_shorts[$idx]['prod_price'] = $rp->get_price_html();
                    $demo_shorts[$idx]['prod_url']   = $rp->get_permalink();
                    $demo_shorts[$idx]['prod_thumb'] = wp_get_attachment_image_url($rp->get_image_id(), 'thumbnail') ?: (get_the_post_thumbnail_url($rp->get_id(), 'thumbnail') ?: $demo_shorts[$idx]['prod_thumb']);
                    $demo_shorts[$idx]['prod_cat']   = strip_tags(wc_get_product_category_list($rp->get_id(), ', ', '', ''));
                    $demo_shorts[$idx]['is_variable']= $rp->is_type('variable');
                }
            }
        }
    }

    ob_start();
    ?>
    <!-- KHỐI SHORTCODE: NGƯỜI THẬT - XE THẬT - TRẢI NGHIỆM THẬT -->
    <div class="gobike-experience-videos-wrap">
        <!-- HEADER -->
        <div class="gev-header">
            <div class="gev-title-box">
                <span class="gev-bar-prefix">|</span>
                <h2 class="gev-main-title"><?php echo esc_html($atts['title']); ?></h2>
            </div>
            <a href="<?php echo esc_url($atts['view_all']); ?>" class="gev-view-all">
                <span>Xem thêm video</span>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
            </a>
        </div>

        <!-- LƯỚI 4 CARD SHORTS (KIỂU CELLPHONES) -->
        <div class="gev-grid">
            <?php
            if ($v_query->have_posts()) {
                while ($v_query->have_posts()) {
                    $v_query->the_post();
                    $vid_id       = get_the_ID();
                    $video_url    = get_field('video_url', $vid_id);
                    $yt_info      = function_exists('gobike_extract_youtube_info') ? gobike_extract_youtube_info($video_url) : array('id' => '', 'embed_url' => '', 'thumbnail' => '');
                    $thumb        = get_field('video_thumbnail', $vid_id) ?: $yt_info['thumbnail'];
                    $overlay_txt  = get_field('video_title_overlay', $vid_id) ?: get_the_title();
                    $views_txt    = get_field('video_views', $vid_id) ?: (get_field('video_views_text', $vid_id) ?: '100K lượt xem');
                    $embed_src    = 'https://www.youtube.com/embed/' . $yt_info['id'] . '?autoplay=1&playsinline=1&rel=0&modestbranding=1';

                    // Lấy sản phẩm WooCommerce liên kết
                    $rel_prod_id  = get_field('related_product', $vid_id);
                    $product_obj  = $rel_prod_id ? wc_get_product($rel_prod_id) : null;

                    if ($product_obj) {
                        $p_id    = $product_obj->get_id();
                        $p_name  = $product_obj->get_name();
                        $p_thumb = wp_get_attachment_image_url($product_obj->get_image_id(), 'thumbnail') ?: (get_the_post_thumbnail_url($p_id, 'thumbnail') ?: wc_placeholder_img_src());
                        $p_price = $product_obj->get_price_html();
                        $p_url   = $product_obj->get_permalink();
                        $p_cats  = wc_get_product_category_list($p_id, ', ', '', '');
                        $p_cart  = $product_obj->add_to_cart_url();
                        $p_is_var= $product_obj->is_type('variable');
                    } else {
                        $p_id    = 0;
                        $p_name  = 'XE ĐẠP GOBIKE';
                        $p_thumb = $thumb;
                        $p_price = '18.990.000đ';
                        $p_url   = home_url('/cua-hang/');
                        $p_cats  = 'Xe đạp trợ lực điện';
                        $p_cart  = home_url('/cua-hang/');
                        $p_is_var= false;
                    }

                    $btn_add_to_cart_url = $p_id ? add_query_arg('add-to-cart', $p_id, $p_url) : $p_url;
                    ?>
                    <div class="gev-card">
                        <!-- PHẦN TRÊN: VIDEO SHORTS 9:16 -->
                        <div class="gev-shorts-box js-open-gev-video" 
                             data-video-src="<?php echo esc_attr($embed_src); ?>"
                             data-video-title="<?php echo esc_attr($overlay_txt); ?>"
                             data-prod-name="<?php echo esc_attr($p_name); ?>"
                             data-prod-url="<?php echo esc_attr($p_url); ?>"
                             data-prod-price="<?php echo esc_attr(strip_tags($p_price)); ?>"
                             data-prod-thumb="<?php echo esc_attr($p_thumb); ?>">
                            
                            <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($overlay_txt); ?>" class="gev-shorts-img">
                            
                            <!-- Chữ nổi nghệ thuật trên video -->
                            <div class="gev-overlay-top">
                                <span class="gev-quote-text"><?php echo esc_html($overlay_txt); ?></span>
                            </div>

                            <!-- Icon Play Hover -->
                            <div class="gev-play-center">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                </svg>
                            </div>

                            <!-- Dải thông tin dưới video -->
                            <div class="gev-overlay-bottom">
                                <div class="gev-views">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <span><?php echo esc_html($views_txt); ?></span>
                                </div>
                                <div class="gev-shorts-badge">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="#ffffff">
                                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                    </svg>
                                    <span>Shorts</span>
                                </div>
                            </div>
                        </div>

                        <!-- PHẦN DƯỚI: MINI CARD SẢN PHẨM -->
                        <div class="gev-product-box">
                            <a href="<?php echo esc_url($p_url); ?>" class="gev-prod-img-link">
                                <img src="<?php echo esc_url($p_thumb); ?>" alt="<?php echo esc_attr($p_name); ?>">
                            </a>
                            <div class="gev-prod-info">
                                <h4 class="gev-prod-name">
                                    <a href="<?php echo esc_url($p_url); ?>"><?php echo esc_html($p_name); ?></a>
                                </h4>
                                <span class="gev-prod-cat"><?php echo strip_tags($p_cats); ?></span>
                                <div class="gev-prod-price"><?php echo $p_price; ?></div>
                            </div>
                            <a href="<?php echo esc_url($btn_add_to_cart_url); ?>" 
                               class="gev-prod-cart-btn button product_type_simple add_to_cart_button ajax_add_to_cart <?php echo $p_is_var ? 'quick-view' : ''; ?>" 
                               data-product_id="<?php echo esc_attr($p_id); ?>"
                               data-prod="<?php echo esc_attr($p_id); ?>"
                               data-product_name="<?php echo esc_attr($p_name); ?>"
                               data-quantity="1"
                               aria-label="Thêm <?php echo esc_attr($p_name); ?> vào giỏ hàng" 
                               title="Thêm vào giỏ hàng"
                               rel="nofollow">
                                <svg class="gev-cart-icon-default" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                <svg class="gev-cart-icon-loading" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                    <path d="M12 2v4m0 12v4m-7-7H1m22 0h-4m-2.93-7.07l2.83-2.83M6.1 17.9l2.83-2.83m0-8.97L6.1 6.1m11.8 11.8l-2.83-2.83"/>
                                </svg>
                                <svg class="gev-cart-icon-added" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <?php
                }
                wp_reset_postdata();
            } else {
                // RENDER DỮ LIỆU MẪU ĐÚNG 100% ẢNH THIẾT KẾ
                foreach ($demo_shorts as $ds) {
                    $embed_src = 'https://www.youtube.com/embed/' . $ds['yt_id'] . '?autoplay=1&playsinline=1&rel=0&modestbranding=1';
                    $d_prod_id = isset($ds['prod_id']) ? $ds['prod_id'] : 0;
                    $d_cart_url = $d_prod_id ? add_query_arg('add-to-cart', $d_prod_id, $ds['prod_url']) : $ds['prod_url'];
                    $d_is_var  = !empty($ds['is_variable']);
                    ?>
                    <div class="gev-card">
                        <!-- PHẦN TRÊN: VIDEO SHORTS 9:16 -->
                        <div class="gev-shorts-box js-open-gev-video" 
                             data-video-src="<?php echo esc_attr($embed_src); ?>"
                             data-video-title="<?php echo esc_attr($ds['video_title']); ?>"
                             data-prod-name="<?php echo esc_attr($ds['prod_name']); ?>"
                             data-prod-url="<?php echo esc_attr($ds['prod_url']); ?>"
                             data-prod-price="<?php echo esc_attr(strip_tags($ds['prod_price'])); ?>"
                             data-prod-thumb="<?php echo esc_attr($ds['prod_thumb']); ?>">
                            
                            <img src="<?php echo esc_url($ds['thumb']); ?>" alt="<?php echo esc_attr($ds['video_title']); ?>" class="gev-shorts-img">
                            
                            <!-- Chữ nổi nghệ thuật trên video -->
                            <div class="gev-overlay-top">
                                <span class="gev-quote-text"><?php echo esc_html($ds['video_title']); ?></span>
                            </div>

                            <!-- Icon Play Hover -->
                            <div class="gev-play-center">
                                <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                </svg>
                            </div>

                            <!-- Dải thông tin dưới video -->
                            <div class="gev-overlay-bottom">
                                <div class="gev-views">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <span><?php echo esc_html($ds['views']); ?></span>
                                </div>
                                <div class="gev-shorts-badge">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="#ffffff">
                                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                                    </svg>
                                    <span>Shorts</span>
                                </div>
                            </div>
                        </div>

                        <!-- PHẦN DƯỚI: MINI CARD SẢN PHẨM -->
                        <div class="gev-product-box">
                            <a href="<?php echo esc_url($ds['prod_url']); ?>" class="gev-prod-img-link">
                                <img src="<?php echo esc_url($ds['prod_thumb']); ?>" alt="<?php echo esc_attr($ds['prod_name']); ?>">
                            </a>
                            <div class="gev-prod-info">
                                <h4 class="gev-prod-name">
                                    <a href="<?php echo esc_url($ds['prod_url']); ?>"><?php echo esc_html($ds['prod_name']); ?></a>
                                </h4>
                                <span class="gev-prod-cat"><?php echo esc_html($ds['prod_cat']); ?></span>
                                <div class="gev-prod-price"><?php echo $ds['prod_price']; ?></div>
                            </div>

                            <a href="<?php echo esc_url($d_cart_url); ?>" 
                               class="gev-prod-cart-btn button product_type_simple add_to_cart_button ajax_add_to_cart <?php echo $d_is_var ? 'quick-view' : ''; ?>" 
                               data-product_id="<?php echo esc_attr($d_prod_id); ?>"
                               data-prod="<?php echo esc_attr($d_prod_id); ?>"
                               data-product_name="<?php echo esc_attr($ds['prod_name']); ?>"
                               data-quantity="1"
                               aria-label="Thêm <?php echo esc_attr($ds['prod_name']); ?> vào giỏ hàng" 
                               title="Thêm vào giỏ hàng"
                               rel="nofollow">
                                <svg class="gev-cart-icon-default" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                <svg class="gev-cart-icon-loading" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                                    <path d="M12 2v4m0 12v4m-7-7H1m22 0h-4m-2.93-7.07l2.83-2.83M6.1 17.9l2.83-2.83m0-8.97L6.1 6.1m11.8 11.8l-2.83-2.83"/>
                                </svg>
                                <svg class="gev-cart-icon-added" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </a>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}



/* ============================================================================
 * 2. RENDER CSS VÀ SCRIPT PHÁT VIDEO TRỰC TIẾP TRÊN CARD (INLINE PLAYER)
 * ============================================================================
 */
add_action('wp_footer', 'gobike_render_experience_shorts_modal_footer', 9998);
function gobike_render_experience_shorts_modal_footer()
{
    ?>
    <!-- CSS GIAO DIỆN SHORTS VÀ INLINE PLAYER -->
    <style>
    .gobike-experience-videos-wrap {
        width: 100%;
        margin: 30px 0 25px 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    }

    /* HEADER */
    .gev-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
    }
    .gev-title-box {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .gev-bar-prefix {
        color: #149d29;
        font-size: 24px;
        font-weight: 700;
        line-height: 1;
    }
    .gev-main-title {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .gev-view-all {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #149d29;
        font-size: 14.5px;
        font-weight: 700;
        text-decoration: none;
        transition: transform 0.2s, color 0.2s;
    }
    .gev-view-all:hover {
        color: #149d29;
        transform: translateX(3px);
    }

    /* GRID 4 CỘT CHUẨN CELLPHONES */
    .gev-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    /* CARD KHUNG VIỀN */
    .gev-card {
        background: #ffffff;
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #f1f5f9;
        box-shadow: none !important;
        display: flex;
        flex-direction: column;
        transition: transform 0.25s ease;
    }
    .gev-card:hover {
        transform: translateY(-4px);
        box-shadow: none !important;
    }

    /* PHẦN TRÊN: VIDEO SHORTS 9:16 */
    .gev-shorts-box,
    .gev-video-box {
        position: relative;
        width: 100%;
        padding-top: 155%; /* Tỉ lệ dọc đẹp như Shorts */
        overflow: hidden;
        cursor: pointer;
        background: #0f172a;
        border-radius: 14px 14px 0 0;
    }
    .gev-shorts-img,
    .gev-video-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .gev-card:hover .gev-shorts-img,
    .gev-card:hover .gev-video-img {
        transform: scale(1.04);
    }

    /* Chữ nổi nghệ thuật trên video */
    .gev-overlay-top,
    .gev-video-top {
        position: absolute;
        top: 14px;
        left: 14px;
        right: 14px;
        z-index: 2;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }
    .gev-quote-text {
        color: #ffffff;
        font-size: 15px;
        font-weight: 700;
        line-height: 1.35;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.8), 0 1px 3px rgba(0,0,0,0.9);
        display: inline-block;
        letter-spacing: 0.2px;
    }

    /* Play Icon Center */
    .gev-play-center,
    .gev-play-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.6);
        border: 2px solid rgba(255, 255, 255, 0.85);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
        transition: transform 0.2s, background 0.2s, opacity 0.2s ease;
    }
    .gev-card:hover .gev-play-center,
    .gev-card:hover .gev-play-icon {
        transform: translate(-50%, -50%) scale(1.12);
        background: #ef4444;
        border-color: #ef4444;
    }

    /* Dải thông tin dưới video Shorts */
    .gev-overlay-bottom,
    .gev-video-bottom {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 25px 12px 10px 12px;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0) 100%);
        display: flex;
        align-items: center;
        justify-content: space-between;
        z-index: 2;
        color: #ffffff;
        transition: opacity 0.2s ease;
    }
    .gev-views,
    .gev-views-tag {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 11.5px;
        font-weight: 600;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
    }
    .gev-shorts-badge,
    .gev-shorts-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: #ef4444;
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 4px;
        box-shadow: none !important;
    }

    /* INLINE VIDEO PLAYER (CHẠY TRỰC TIẾP TRÊN CARD) */
    .gev-inline-iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
        z-index: 10;
        background: #000000;
        border-radius: 14px 14px 0 0;
    }
    .gev-inline-close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.75);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        font-weight: 700;
        line-height: 1;
        cursor: pointer;
        z-index: 20;
        transition: all 0.2s ease;
        padding: 0;
    }
    .gev-inline-close-btn:hover {
        background: #ef4444;
        border-color: #ef4444;
        transform: scale(1.1);
    }
    /* Ẩn các layer tĩnh khi đang phát video */
    .js-open-gev-video.is-playing .gev-shorts-img,
    .js-open-gev-video.is-playing .gev-video-img,
    .js-open-gev-video.is-playing .gev-overlay-top,
    .js-open-gev-video.is-playing .gev-video-top,
    .js-open-gev-video.is-playing .gev-play-center,
    .js-open-gev-video.is-playing .gev-play-icon,
    .js-open-gev-video.is-playing .gev-overlay-bottom,
    .js-open-gev-video.is-playing .gev-video-bottom {
        opacity: 0 !important;
        pointer-events: none !important;
    }

    /* PHẦN DƯỚI: MINI CARD SẢN PHẨM */
    .gev-product-box {
        padding: 12px 14px;
        background: #ffffff;
        display: flex;
        align-items: center;
        gap: 12px;
        border-top: 1px solid #f1f5f9;
        margin-top: auto;
    }
    .gev-prod-img-link {
        width: 52px;
        height: 52px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }
    .gev-prod-img-link img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .gev-prod-info {
        flex: 1;
        min-width: 0;
    }
    .gev-prod-name {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 2px 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-transform: uppercase;
    }
    .gev-prod-name a {
        color: #0f172a;
        text-decoration: none;
    }
    .gev-prod-name a:hover {
        color: #149d29;
    }
    .gev-prod-cat {
        font-size: 11px;
        color: #64748b;
        display: block;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .gev-prod-price {
        font-size: 13.5px;
        font-weight: 700;
        color: #dc2626;
        line-height: 1.3;
        display: flex;
        align-items: baseline;
        gap: 6px;
        flex-wrap: wrap;
    }
    .gev-prod-price ins {
        color: #dc2626;
        text-decoration: none;
        font-weight: 700;
        font-size: 13.5px;
    }
    .gev-prod-price del {
        color: #94a3b8;
        font-size: 11px;
        font-weight: normal;
        text-decoration: line-through;
        opacity: 0.85;
    }
    .gev-prod-price del span {
        font-weight: normal;
    }
    .gev-prod-price bdi {
        font-weight: inherit;
    }

    .gev-prod-cart-btn {
        position: relative;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #149d29;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s ease;
        cursor: pointer;
        padding: 0 !important;
        text-decoration: none;
    }
    .gev-prod-cart-btn:hover {
        background: #149d29;
        color: #ffffff;
        border-color: #149d29;
        transform: scale(1.08);
    }
    .gev-prod-cart-btn .gev-cart-icon-loading,
    .gev-prod-cart-btn .gev-cart-icon-added {
        display: none;
    }
    .gev-prod-cart-btn.loading {
        pointer-events: none;
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #64748b !important;
    }
    .gev-prod-cart-btn.loading .gev-cart-icon-default {
        display: none !important;
    }
    .gev-prod-cart-btn.loading .gev-cart-icon-loading {
        display: block !important;
        animation: gevSpin 0.7s linear infinite;
    }
    .gev-prod-cart-btn.added {
        background: #149d29 !important;
        border-color: #149d29 !important;
        color: #ffffff !important;
    }
    .gev-prod-cart-btn.added .gev-cart-icon-default {
        display: none !important;
    }
    .gev-prod-cart-btn.added .gev-cart-icon-added {
        display: block !important;
    }
    @keyframes gevSpin {
        100% { transform: rotate(360deg); }
    }

    /* TOAST THÔNG BÁO THÊM GIỎ HÀNG THÀNH CÔNG */
    .gev-toast-notice {
        position: fixed;
        bottom: 24px;
        right: 24px;
        background: #149d29;
        color: #ffffff;
        padding: 12px 18px;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        box-shadow: none !important;
        z-index: 9999999;
        transform: translateY(100px);
        opacity: 0;
        transition: transform 0.3s ease, opacity 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
        pointer-events: none;
    }
    .gev-toast-notice.show {
        transform: translateY(0);
        opacity: 1;
        pointer-events: auto;
    }
    .gev-toast-notice svg {
        flex-shrink: 0;
        color: #ffffff;
    }
    @media (max-width: 600px) {
        .gev-toast-notice {
            bottom: 75px;
            right: 15px;
            left: 15px;
            font-size: 12.5px;
            padding: 10px 14px;
        }
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
        .gev-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
    }
    @media (max-width: 600px) {
        .gev-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .gev-main-title {
            font-size: 17px;
        }
        .gev-quote-text {
            font-size: 12.5px;
        }
        .gev-product-box {
            padding: 8px 10px;
            gap: 8px;
        }
        .gev-prod-img-link {
            width: 40px;
            height: 40px;
        }
        .gev-prod-name {
            font-size: 11.5px;
        }
        .gev-prod-cat {
            display: none;
        }
        .gev-prod-price {
            font-size: 12px;
        }
    }
    </style>

    <!-- SCRIPT PHÁT VIDEO TRỰC TIẾP TRÊN CARD (INLINE RUNNER) & AJAX ADD TO CART -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hàm dừng video đang phát trên 1 card
        function stopInlineVideo(box) {
            if (!box) return;
            box.classList.remove('is-playing');
            var existingIframe = box.querySelector('.gev-inline-iframe');
            if (existingIframe) existingIframe.remove();
            var existingBtn = box.querySelector('.gev-inline-close-btn');
            if (existingBtn) existingBtn.remove();
        }

        document.addEventListener('click', function(e) {
            // 1. Nhấp nút đóng video để quay lại thumbnail
            var closeBtn = e.target.closest('.gev-inline-close-btn');
            if (closeBtn) {
                e.preventDefault();
                e.stopPropagation();
                var currentBox = closeBtn.closest('.js-open-gev-video');
                stopInlineVideo(currentBox);
                return;
            }

            // 2. Nhấp vào card video để phát trực tiếp tại chỗ
            var trigger = e.target.closest('.js-open-gev-video');
            if (trigger) {
                // Nếu video này đang phát thì giữ nguyên cho người dùng xem/thao tác
                if (trigger.classList.contains('is-playing')) {
                    return;
                }

                e.preventDefault();

                // Dừng tất cả các card video khác đang phát
                document.querySelectorAll('.js-open-gev-video.is-playing').forEach(function(otherBox) {
                    stopInlineVideo(otherBox);
                });

                var videoSrc = trigger.getAttribute('data-video-src');
                if (videoSrc) {
                    // Đảm bảo video tự phát (autoplay=1) và phát nội tuyến trên mobile (playsinline=1)
                    if (videoSrc.indexOf('autoplay=') === -1) {
                        videoSrc += (videoSrc.indexOf('?') === -1 ? '?' : '&') + 'autoplay=1&playsinline=1';
                    } else if (videoSrc.indexOf('playsinline=') === -1) {
                        videoSrc += '&playsinline=1';
                    }

                    trigger.classList.add('is-playing');

                    // Nhúng Iframe phát trực tiếp
                    var iframe = document.createElement('iframe');
                    iframe.className = 'gev-inline-iframe';
                    iframe.src = videoSrc;
                    iframe.setAttribute('frameborder', '0');
                    iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
                    iframe.setAttribute('allowfullscreen', 'true');

                    // Thêm nút Đóng video ở góc trên để quay lại ảnh bìa khi cần
                    var stopBtn = document.createElement('button');
                    stopBtn.className = 'gev-inline-close-btn';
                    stopBtn.type = 'button';
                    stopBtn.innerHTML = '&times;';
                    stopBtn.setAttribute('title', 'Đóng video');
                    stopBtn.setAttribute('aria-label', 'Đóng video');

                    trigger.appendChild(iframe);
                    trigger.appendChild(stopBtn);
                }
            }
        });

        // 3. Xử lý Thêm vào giỏ hàng AJAX mượt mà
        function showGevToast(msg) {
            var toast = document.getElementById('js-gev-toast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'js-gev-toast';
                toast.className = 'gev-toast-notice';
                document.body.appendChild(toast);
            }
            toast.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg><span>' + msg + '</span>';
            toast.classList.add('show');
            clearTimeout(window.gevToastTimeout);
            window.gevToastTimeout = setTimeout(function() {
                toast.classList.remove('show');
            }, 3500);
        }

        if (window.jQuery) {
            jQuery(document).on('click', '.gev-prod-cart-btn', function(e) {
                var $btn = jQuery(this);
                var productId = $btn.data('product_id');
                var prodName = $btn.data('product_name') || 'Sản phẩm';

                if (!productId || productId == '0') {
                    // Nếu chưa gắn ID sản phẩm thật (chế độ demo)
                    var href = $btn.attr('href');
                    if (href && href !== '#' && href.indexOf('?add-to-cart=0') === -1) {
                        return; // Chuyển trang bình thường
                    }
                    e.preventDefault();
                    showGevToast('Đang chuyển đến danh mục sản phẩm...');
                    setTimeout(function() {
                        window.location.href = '<?php echo esc_url(home_url("/cua-hang/")); ?>';
                    }, 500);
                    return;
                }

                // Nếu là variable product và đang có trigger quick-view thì để Flatsome mở popup chọn phiên bản
                if ($btn.hasClass('quick-view')) {
                    return;
                }

                e.preventDefault();
                e.stopPropagation();

                if ($btn.hasClass('loading')) return;

                $btn.addClass('loading');

                var ajaxUrl = (window.wc_add_to_cart_params && wc_add_to_cart_params.wc_ajax_url)
                    ? wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart')
                    : '<?php echo esc_url(home_url("/?wc-ajax=add_to_cart")); ?>';

                jQuery.ajax({
                    type: 'POST',
                    url: ajaxUrl,
                    data: {
                        product_id: productId,
                        quantity: 1
                    },
                    dataType: 'json',
                    success: function(response) {
                        $btn.removeClass('loading').addClass('added');

                        // Kích hoạt event WooCommerce & Flatsome để mở giỏ hàng dropdown / drawer
                        if (response && response.fragments) {
                            jQuery(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $btn]);
                        } else {
                            jQuery(document.body).trigger('added_to_cart', ['', '', $btn]);
                        }
                        jQuery(document.body).trigger('wc_fragment_refresh');

                        showGevToast('Đã thêm <strong>' + prodName + '</strong> vào giỏ hàng!');

                        setTimeout(function() {
                            $btn.removeClass('added');
                        }, 3000);
                    },
                    error: function() {
                        $btn.removeClass('loading');
                        // Nếu AJAX lỗi, fallback chuyển qua link thêm giỏ URL
                        var fallbackUrl = $btn.attr('href');
                        if (fallbackUrl && fallbackUrl !== '#') {
                            window.location.href = fallbackUrl;
                        }
                    }
                });
            });
        }
    });
    </script>
    <?php
}

/* ============================================================================
 * 3. HÀM HIỂN THỊ VIDEO TRẢI NGHIỆM TRÊN TRANG CHI TIẾT SẢN PHẨM (SINGLE PRODUCT)
 * ============================================================================
 */
function gobike_render_single_product_videos($product_id = null)
{
    if (!$product_id) {
        global $product;
        if ($product) {
            $product_id = $product->get_id();
        } else {
            $product_id = get_the_ID();
        }
    }

    if (!$product_id) {
        return '';
    }

    $videos = array();

    // 1. Kiểm tra video nhập trực tiếp trong ACF của Sản phẩm
    $direct_url = function_exists('get_field') ? get_field('product_video_url', $product_id) : '';
    if (!empty($direct_url)) {
        $yt_id = gobike_get_youtube_id_from_url($direct_url);
        $title = function_exists('get_field') ? get_field('product_video_title_overlay', $product_id) : '';
        $views = function_exists('get_field') ? get_field('product_video_views', $product_id) : '';
        
        $videos[] = array(
            'video_title' => !empty($title) ? $title : ('Trải nghiệm thực tế ' . get_the_title($product_id)),
            'views'       => !empty($views) ? $views : '50K lượt xem',
            'thumb'       => 'https://img.youtube.com/vi/' . $yt_id . '/hqdefault.jpg',
            'yt_id'       => $yt_id,
            'is_shorts'   => (strpos($direct_url, '/shorts/') !== false),
        );
    }

    // 2. Kiểm tra các bài CPT Video Review được gán trực tiếp trong ACF Product
    $linked_review_ids = function_exists('get_field') ? get_field('product_linked_reviews', $product_id) : array();
    if (!empty($linked_review_ids) && is_array($linked_review_ids)) {
        foreach ($linked_review_ids as $rev_id) {
            $v_url = function_exists('get_field') ? get_field('video_url', $rev_id) : '';
            if ($v_url) {
                $yt_id = gobike_get_youtube_id_from_url($v_url);
                $custom_thumb = function_exists('get_field') ? get_field('video_thumbnail', $rev_id) : '';
                $thumb = $custom_thumb ? $custom_thumb : ('https://img.youtube.com/vi/' . $yt_id . '/hqdefault.jpg');
                $v_type = function_exists('get_field') ? get_field('video_type', $rev_id) : 'shorts';
                $v_overlay = function_exists('get_field') ? get_field('video_title_overlay', $rev_id) : '';
                $v_views = function_exists('get_field') ? get_field('video_views_text', $rev_id) : '45K lượt xem';

                $videos[] = array(
                    'video_title' => !empty($v_overlay) ? $v_overlay : get_the_title($rev_id),
                    'views'       => $v_views,
                    'thumb'       => $thumb,
                    'yt_id'       => $yt_id,
                    'is_shorts'   => ($v_type === 'shorts'),
                );
            }
        }
    }

    // 3. Query ngược từ CPT video_review có related_product = $product_id
    $cpt_query = new WP_Query(array(
        'post_type'      => 'video_review',
        'posts_per_page' => 4,
        'post_status'    => 'publish',
        'meta_query'     => array(
            array(
                'key'     => 'related_product',
                'value'   => $product_id,
                'compare' => '=',
            ),
        ),
    ));

    if ($cpt_query->have_posts()) {
        while ($cpt_query->have_posts()) {
            $cpt_query->the_post();
            $rev_id = get_the_ID();
            $v_url = function_exists('get_field') ? get_field('video_url', $rev_id) : '';
            if ($v_url) {
                $yt_id = gobike_get_youtube_id_from_url($v_url);
                $custom_thumb = function_exists('get_field') ? get_field('video_thumbnail', $rev_id) : '';
                $thumb = $custom_thumb ? $custom_thumb : ('https://img.youtube.com/vi/' . $yt_id . '/hqdefault.jpg');
                $v_type = function_exists('get_field') ? get_field('video_type', $rev_id) : 'shorts';
                $v_overlay = function_exists('get_field') ? get_field('video_title_overlay', $rev_id) : '';
                $v_views = function_exists('get_field') ? get_field('video_views_text', $rev_id) : '50K lượt xem';

                // Tránh trùng lặp video
                $exists = false;
                foreach ($videos as $v_item) {
                    if ($v_item['yt_id'] === $yt_id) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $videos[] = array(
                        'video_title' => !empty($v_overlay) ? $v_overlay : get_the_title($rev_id),
                        'views'       => $v_views,
                        'thumb'       => $thumb,
                        'yt_id'       => $yt_id,
                        'is_shorts'   => ($v_type === 'shorts'),
                    );
                }
            }
        }
        wp_reset_postdata();
    }

    // Nếu không có video nào cho sản phẩm này thì không hiển thị
    if (empty($videos)) {
        return '';
    }

    $product_obj = wc_get_product($product_id);
    $prod_name = $product_obj ? $product_obj->get_name() : get_the_title($product_id);
    $prod_url = get_permalink($product_id);
    $prod_price = $product_obj ? $product_obj->get_price_html() : '';
    $prod_thumb = get_the_post_thumbnail_url($product_id, 'thumbnail');

    ob_start();
    ?>
    <div class="gobike-single-product-videos-wrap container">
        <div class="gev-header">
            <div class="gev-title-box">
                <span class="gev-bar-prefix">|</span>
                <h2 class="gev-main-title">VIDEO TRẢI NGHIỆM THỰC TẾ</h2>
            </div>
        </div>

        <div class="gev-grid gev-product-grid-wrap">
            <?php foreach ($videos as $v): 
                $video_embed_url = 'https://www.youtube.com/embed/' . $v['yt_id'] . '?autoplay=1&rel=0&modestbranding=1';
            ?>
                <div class="gev-card">
                    <div class="gev-video-box js-open-gev-video"
                         data-video-src="<?php echo esc_url($video_embed_url); ?>"
                         data-prod-name="<?php echo esc_attr(wp_strip_all_tags($prod_name)); ?>"
                         data-prod-url="<?php echo esc_url($prod_url); ?>"
                         data-prod-price="<?php echo esc_attr(wp_strip_all_tags($prod_price)); ?>"
                         data-prod-thumb="<?php echo esc_url($prod_thumb); ?>">
                        
                        <img src="<?php echo esc_url($v['thumb']); ?>" alt="<?php echo esc_attr($v['video_title']); ?>" class="gev-video-img" loading="lazy">
                        
                        <div class="gev-video-top">
                            <span class="gev-quote-text"><?php echo esc_html($v['video_title']); ?></span>
                        </div>

                        <div class="gev-play-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="#ffffff"><path d="M8 5v14l11-7z"/></svg>
                        </div>

                        <div class="gev-video-bottom">
                            <div class="gev-views-tag">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                <span><?php echo esc_html($v['views']); ?></span>
                            </div>
                            <?php if ($v['is_shorts']): ?>
                            <div class="gev-shorts-tag">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M10 14.65v-5.3L15 12l-5 2.65zm7.8-7.05c-.95-.95-2.23-1.48-3.58-1.48-.68 0-1.34.13-1.95.39L14 3.73C13.2 2.65 11.95 2 10.6 2c-.68 0-1.34.17-1.93.49L3.5 5.56C1.94 6.42 1 8.08 1 9.87c0 1.34.54 2.63 1.48 3.58.26.26.54.49.85.67L2 14.89c-.8 1.08-1.07 2.47-.73 3.78.33 1.3 1.25 2.37 2.48 2.89.65.28 1.33.42 2.03.42.7 0 1.39-.17 2.02-.5l5.17-3.07c1.56-.86 2.5-2.52 2.5-4.31 0-1.34-.54-2.63-1.48-3.58-.26-.26-.54-.49-.85-.67l1.33-.77c1.64-.95 2.52-2.82 2.03-4.57z"/></svg>
                                <span>Shorts</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('gobike_product_videos', 'gobike_render_single_product_videos');

