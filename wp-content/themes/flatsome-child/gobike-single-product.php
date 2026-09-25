<?php
/**
 * GOBIKE SINGLE PRODUCT MODULE
 * Xử lý toàn bộ logic dữ liệu động và render giao diện chuẩn cho Trang Chi Tiết Sản Phẩm
 * Phù hợp 100% với bản thiết kế Ảnh 1 (Desktop) và Ảnh 3 (Mobile)
 * 
 * @package Flatsome-Child
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. Helper: Lấy thông số kỹ thuật động của sản phẩm
 */
function gobike_get_single_product_specs($product_id) {
    $product = wc_get_product($product_id);
    if (!$product) return array();

    // Lấy thông số từ thuộc tính WooCommerce hoặc ACF
    $pin = $product->get_attribute('pa_pin');
    if (!$pin) $pin = get_field('dung_luong_pin', $product_id) ?: '48V 15Ah (Lithium)';

    $dong_co = $product->get_attribute('pa_dong-co');
    if (!$dong_co) $dong_co = get_field('dong_co', $product_id) ?: '250W – Trợ lực thông minh';

    $quang_duong = $product->get_attribute('pa_quang-duong');
    if (!$quang_duong) $quang_duong = get_field('quang_duong', $product_id) ?: '80 – 120 km (tùy điều kiện)';

    $toc_do = $product->get_attribute('pa_toc-do');
    if (!$toc_do) $toc_do = get_field('toc_do_toi_da', $product_id) ?: '25 km/h (trợ lực)';

    $lop_xe = $product->get_attribute('pa_kich-thuoc-lop');
    if (!$lop_xe) $lop_xe = get_field('kich_thuoc_lop', $product_id) ?: '27.5 inch – chống trượt';

    $trong_luong = $product->get_weight() ? $product->get_weight() . ' kg' : (get_field('trong_luong', $product_id) ?: '27 kg');
    $bao_hanh = get_field('thoi_gian_bao_hanh', $product_id) ?: '24 tháng (Khung), 12 tháng (Động cơ, pin)';

    // Thương hiệu
    $brand_terms = get_the_terms($product_id, 'pa_thuong-hieu');
    $brand = 'PHOENIX';
    if (!empty($brand_terms) && !is_wp_error($brand_terms)) {
        $brand = $brand_terms[0]->name;
    } else {
        $brand_acf = get_field('thuong_hieu', $product_id);
        if ($brand_acf) $brand = $brand_acf;
    }

    // Danh mục chính (Lọc bỏ các danh mục tiện ích không liên quan)
    $cats = get_the_terms($product_id, 'product_cat');
    $cat_name = 'Xe đạp trợ lực địa hình';
    $cat_link = home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/');
    $ignore_slugs = array('uncategorized', 'kiem-tra-don-hang', 'tra-cuu', 'don-hang', 'chua-phan-loai');
    
    if (!empty($cats) && !is_wp_error($cats)) {
        foreach ($cats as $c) {
            if (!in_array($c->slug, $ignore_slugs)) {
                $cat_name = $c->name;
                $cat_link = get_term_link($c);
                break;
            }
        }
    }

    return array(
        'brand'          => $brand,
        'cat_name'       => $cat_name,
        'cat_link'       => $cat_link,
        'pin'            => $pin,
        'dong_co'        => $dong_co,
        'quang_duong'    => $quang_duong,
        'toc_do'         => $toc_do,
        'lop_xe'         => $lop_xe,
        'trong_luong'    => $trong_luong,
        'bao_hanh'       => $bao_hanh,
        'khung_xe'       => get_field('chat_lieu_khung', $product_id) ?: 'Hợp kim nhôm 6061',
        'phanh'          => get_field('he_thong_phanh', $product_id) ?: 'Phanh dầu thủy lực',
        'giam_xoc'       => get_field('giam_xoc', $product_id) ?: 'Phuộc trước khóa hành trình',
        'tai_trong'      => get_field('tai_trong_toi_da', $product_id) ?: '120 kg',
        'kich_thuoc'     => get_field('kich_thuoc_xe', $product_id) ?: '1780 x 680 x 1050 mm',
    );
}

/**
 * 2. Render Cột Gallery ảnh (Swiper) + Dải 6 Thông số nhanh
 * Hiển thị thuần túy hình ảnh sản phẩm (không phủ text) chuẩn 100% Ảnh 2
 */
function gobike_render_single_product_gallery($product) {
    $product_id = $product->get_id();
    $image_ids = array();
    
    // Ảnh đại diện
    if ($product->get_image_id()) {
        $image_ids[] = $product->get_image_id();
    }
    // Gallery ảnh WooCommerce
    $gallery_ids = $product->get_gallery_image_ids();
    if (!empty($gallery_ids)) {
        $image_ids = array_merge($image_ids, $gallery_ids);
    }

    // Ảnh biến thể (nếu là variable product)
    if ($product->is_type('variable')) {
        $variations = $product->get_available_variations();
        if (!empty($variations)) {
            foreach ($variations as $var) {
                if (!empty($var['image_id'])) {
                    $image_ids[] = $var['image_id'];
                }
            }
        }
    }

    // Ảnh ACF chi tiết nếu có
    $acf_gallery = get_field('gallery_san_pham', $product_id);
    if (!empty($acf_gallery) && is_array($acf_gallery)) {
        foreach ($acf_gallery as $item) {
            $id = is_array($item) ? ($item['ID'] ?? 0) : $item;
            if ($id) $image_ids[] = $id;
        }
    }

    $image_ids = array_values(array_unique(array_filter($image_ids)));

    $specs = gobike_get_single_product_specs($product_id);
    $video_url = get_field('video_url', $product_id) ?: 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
    ?>
    <div class="gobike-single-gallery-container">
        <!-- Main Slider + Vertical Thumbs Wrapper -->
        <div class="gobike-gallery-layout">
            <!-- Cột Thumbs bên trái -->
            <div class="gobike-gallery-thumbs-col">
                <div class="swiper-container gobike-gallery-thumbs-swiper">
                    <div class="swiper-wrapper">
                        <?php if (!empty($image_ids)): ?>
                            <?php foreach ($image_ids as $index => $img_id): 
                                $thumb_url = wp_get_attachment_image_url($img_id, 'thumbnail') ?: wp_get_attachment_image_url($img_id, 'medium');
                            ?>
                                <div class="swiper-slide thumb-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" loading="lazy" />
                                    <?php if ($index === 1 && !empty($video_url)): ?>
                                        <div class="thumb-play-overlay">▶</div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="swiper-slide thumb-item active">
                                <img src="<?php echo esc_url(wc_placeholder_img_src()); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" />
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Khung Slide chính bên phải (Thuần ảnh sản phẩm, không chèn text) -->
            <div class="gobike-gallery-main-col">
                <div class="swiper-container gobike-gallery-main-swiper">
                    <div class="swiper-wrapper">
                        <?php if (!empty($image_ids)): ?>
                            <?php foreach ($image_ids as $img_id): 
                                $large_url = wp_get_attachment_image_url($img_id, 'large') ?: wp_get_attachment_image_url($img_id, 'full');
                            ?>
                                <div class="swiper-slide main-slide-item">
                                    <div class="slide-img-box">
                                        <img src="<?php echo esc_url($large_url); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" />
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="swiper-slide main-slide-item">
                                <div class="slide-img-box">
                                    <img src="<?php echo esc_url(wc_placeholder_img_src()); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" />
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Cụm Nút Nổi Góc Dưới Ảnh: Xem video & Xem 360° -->
                    <div class="gobike-gallery-floating-actions">
                        <button type="button" class="btn-float-action btn-open-video" data-video="<?php echo esc_url($video_url); ?>">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                            <span>Xem video</span>
                        </button>
                        <button type="button" class="btn-float-action btn-open-360">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                            <span>Xem 360°</span>
                        </button>
                    </div>

                    <!-- Điều hướng Swiper -->
                    <div class="swiper-button-prev gobike-gal-prev"></div>
                    <div class="swiper-button-next gobike-gal-next"></div>
                    <div class="swiper-pagination gobike-gal-pagination"></div>
                </div>
            </div>
        </div>

        <!-- Dải 6 Thông Số Nhanh Dưới Gallery (Quick Spec Badges) -->
        <div class="gobike-quick-spec-badges">
            <div class="spec-badge-item">
                <div class="badge-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="badge-info">
                    <strong class="badge-val"><?php echo esc_html(explode('–', $specs['quang_duong'])[0]); ?></strong>
                    <span class="badge-label">Quãng đường trợ lực</span>
                </div>
            </div>

            <div class="spec-badge-item">
                <div class="badge-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                </div>
                <div class="badge-info">
                    <strong class="badge-val"><?php echo esc_html(explode('–', $specs['dong_co'])[0]); ?></strong>
                    <span class="badge-label">Động cơ mạnh mẽ</span>
                </div>
            </div>

            <div class="spec-badge-item">
                <div class="badge-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="6" width="18" height="12" rx="2"/><line x1="23" y1="13" x2="23" y2="11"/></svg>
                </div>
                <div class="badge-info">
                    <strong class="badge-val"><?php echo esc_html(explode('(', $specs['pin'])[0]); ?></strong>
                    <span class="badge-label">Pin Lithium</span>
                </div>
            </div>

            <div class="spec-badge-item">
                <div class="badge-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
                <div class="badge-info">
                    <strong class="badge-val"><?php echo esc_html(explode('–', $specs['lop_xe'])[0]); ?></strong>
                    <span class="badge-label">Lốp xe đa địa hình</span>
                </div>
            </div>

            <div class="spec-badge-item">
                <div class="badge-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                </div>
                <div class="badge-info">
                    <strong class="badge-val"><?php echo esc_html($specs['trong_luong']); ?></strong>
                    <span class="badge-label">Trọng lượng</span>
                </div>
            </div>

            <div class="spec-badge-item">
                <div class="badge-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="badge-info">
                    <strong class="badge-val"><?php echo esc_html(explode('(', $specs['bao_hanh'])[0]); ?></strong>
                    <span class="badge-label">Phanh dầu thủy lực</span>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * 3. Render Cột Thông Tin Sản Phẩm & Mua Hàng (Right Column)
 * Chuẩn 100% theo bản thiết kế Ảnh 2
 */
function gobike_render_single_product_info($product) {
    $product_id = $product->get_id();
    $specs = gobike_get_single_product_specs($product_id);

    // Tính giá và giảm giá
    $regular_price = $product->get_regular_price();
    $sale_price = $product->get_sale_price();
    $current_price = $product->get_price();

    $discount_percent = 0;
    if (!empty($regular_price) && !empty($sale_price) && $regular_price > $sale_price) {
        $discount_percent = round((($regular_price - $sale_price) / $regular_price) * 100);
    }
    ?>
    <div class="gobike-single-info-container">
        <!-- Hàng 1: Brand Tag + Cat Badge + Wishlist + Share -->
        <div class="info-top-row">
            <div class="top-left-badges">
                <span class="brand-tag-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#b91c1c"><polygon points="12 2 2 22 22 22 12 2"/></svg>
                    <strong><?php echo esc_html($specs['brand']); ?></strong>
                </span>
                <a href="<?php echo esc_url($specs['cat_link']); ?>" class="cat-tag-badge">
                    <?php echo esc_html($specs['cat_name']); ?>
                </a>
            </div>
            <div class="top-right-actions">
                <button type="button" class="btn-top-action btn-wishlist" title="Yêu thích">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    <span>Yêu thích</span>
                </button>
                <button type="button" class="btn-top-action btn-share" title="Chia sẻ">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                    <span>Chia sẻ</span>
                </button>
            </div>
        </div>

        <!-- Hàng 2: Tên sản phẩm H1 & Slogan phụ -->
        <h1 class="gobike-product-title"><?php echo esc_html($product->get_name()); ?></h1>
        <p class="gobike-product-subtitle"><?php echo esc_html(get_field('slogan_san_pham', $product_id) ?: 'Mạnh mẽ trên mọi cung đường'); ?></p>

        <!-- Hàng 3: Đánh giá sao & Số lượng đã bán -->
        <div class="gobike-rating-sales-row">
            <div class="rating-stars-box">
                <span class="star-icon">★</span>
                <strong class="rating-num">4.9</strong>
                <a href="#tab-reviews" class="rating-count-link">(128 đánh giá)</a>
            </div>
            <span class="divider-dot">•</span>
            <div class="sales-count-box">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                <span>Đã bán <strong>432</strong></span>
            </div>
        </div>

        <!-- Hàng 4: Giá bán to màu xanh sạch sẽ, không đóng khung viền đứt -->
        <div class="gobike-price-block">
            <?php if (!empty($current_price)): ?>
                <span class="price-current"><?php echo wc_price($current_price); ?></span>
                <?php if (!empty($regular_price) && $regular_price > $current_price): ?>
                    <span class="price-regular"><?php echo wc_price($regular_price); ?></span>
                <?php endif; ?>
                <?php if ($discount_percent > 0): ?>
                    <span class="price-discount-tag">-<?php echo esc_html($discount_percent); ?>%</span>
                <?php endif; ?>
            <?php else: ?>
                <span class="price-current price-contact">Liên hệ</span>
            <?php endif; ?>
        </div>

        <!-- Hàng 5: Mô tả ngắn -->
        <?php 
        $excerpt = $product->get_short_description();
        if (empty($excerpt)) {
            $excerpt = 'Mẫu xe đạp trợ lực địa hình được ưa chuộng nhờ khả năng vận hành mạnh mẽ, thiết kế thể thao và độ bền vượt trội. Phù hợp cho cả di chuyển hàng ngày lẫn những chuyến đi khám phá, chinh phục thiên nhiên.';
        }
        ?>
        <div class="gobike-short-desc">
            <p><?php echo wp_kses_post($excerpt); ?></p>
        </div>

        <!-- Hàng 6: Form Mua Hàng & Biến Thể Màu Sắc (WooCommerce Standard Form) -->
        <div class="gobike-add-to-cart-wrapper">
            <?php
            // Kích hoạt form mua hàng WooCommerce (Simple hoặc Variable Product)
            woocommerce_template_single_add_to_cart();
            ?>
        </div>

        <!-- Dải 5 Cam Kết Quyền Lợi Khách Hàng (Trust Badges) -->
        <div class="gobike-single-trust-badges">
            <div class="trust-item">
                <div class="trust-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <span>Bảo hành chính hãng</span>
            </div>
            <div class="trust-item">
                <div class="trust-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </div>
                <span>Giao hàng toàn quốc</span>
            </div>
            <div class="trust-item">
                <div class="trust-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </div>
                <span>Lắp ráp miễn phí</span>
            </div>
            <div class="trust-item">
                <div class="trust-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
                <span>Trả góp 0%</span>
            </div>
            <div class="trust-item">
                <div class="trust-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </div>
                <span>Tư vấn 24/7</span>
            </div>
        </div>
    </div>
    <?php
}

/**
 * 4. Render Section 2: 5 Tabs Nội Dung Chi Tiết (Sticky Navigation)
 */
function gobike_render_single_product_tabs($product) {
    $product_id = $product->get_id();
    $specs = gobike_get_single_product_specs($product_id);
    ?>
    <section class="gobike-product-tabs-section" id="gobikeProductTabsSection">
        <!-- Sticky Tabs Header -->
        <div class="gobike-tabs-nav-bar">
            <div class="container">
                <ul class="gobike-tabs-nav-list">
                    <li class="tab-nav-item active" data-tab="tab-description">
                        <a href="#tab-description">Mô tả sản phẩm</a>
                    </li>
                    <li class="tab-nav-item" data-tab="tab-specifications">
                        <a href="#tab-specifications">Thông số kỹ thuật</a>
                    </li>
                    <li class="tab-nav-item" data-tab="tab-media">
                        <a href="#tab-media">Hình ảnh & Video</a>
                    </li>
                    <li class="tab-nav-item" data-tab="tab-reviews">
                        <a href="#tab-reviews">Đánh giá <span>(128)</span></a>
                    </li>
                    <li class="tab-nav-item" data-tab="tab-faq">
                        <a href="#tab-faq">Hỏi đáp <span>(12)</span></a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="container gobike-tabs-content-wrap">
            <!-- TAB 1: MÔ TẢ SẢN PHẨM -->
            <div class="gobike-tab-panel active" id="tab-description">
                <div class="gobike-desc-container">
                    <div class="row align-top">
                        <!-- Cột Trái: Text mô tả + 4 icon tính năng -->
                        <div class="col large-7 medium-12 small-12">
                            <h2 class="desc-heading-primary">Khám phá thế giới theo cách của bạn</h2>
                            <div class="desc-main-text entry-content">
                                <?php
                                $content = get_the_content();
                                if (empty($content)) {
                                    echo '<p><strong>' . esc_html($product->get_name()) . '</strong> không chỉ là một chiếc xe đạp trợ lực, mà còn là người bạn đồng hành đáng tin cậy trên mọi hành trình. Được thiết kế dành cho những ai yêu thích khám phá và tận hưởng cuộc sống năng động, xe mang đến sự kết hợp hoàn hảo giữa sức mạnh, sự linh hoạt và phong cách hiện đại.</p>';
                                    echo '<p>Dù là những cung đường dốc cao, đường mòn gập ghềnh hay phố thị hàng ngày, xe đều giúp bạn di chuyển dễ dàng hơn, xa hơn và thú vị hơn.</p>';
                                } else {
                                    the_content();
                                }
                                ?>
                            </div>

                            <!-- 4 Ô Tính Năng Nổi Bật -->
                            <div class="desc-feature-grid">
                                <div class="feature-box-item">
                                    <div class="feature-icon">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                                    </div>
                                    <div class="feature-text">
                                        <h4>Chinh phục mọi địa hình</h4>
                                        <p>Vận hành mạnh mẽ, an tâm trên cả đường phố và đường mòn đồi dốc.</p>
                                    </div>
                                </div>

                                <div class="feature-box-item">
                                    <div class="feature-icon">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>
                                    </div>
                                    <div class="feature-text">
                                        <h4>Trợ lực thông minh</h4>
                                        <p>Hỗ trợ đạp nhẹ nhàng hơn, tiết kiệm sức lực, đi xa hơn mỗi ngày.</p>
                                    </div>
                                </div>

                                <div class="feature-box-item">
                                    <div class="feature-icon">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    </div>
                                    <div class="feature-text">
                                        <h4>Thiết kế hiện đại</h4>
                                        <p>Khung dáng thể thao, mạnh mẽ, phù hợp phong cách sống năng động.</p>
                                    </div>
                                </div>

                                <div class="feature-box-item">
                                    <div class="feature-icon">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                                    </div>
                                    <div class="feature-text">
                                        <h4>Thân thiện môi trường</h4>
                                        <p>Sử dụng năng lượng sạch, góp phần bảo vệ môi trường xanh bền vững.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cột Phải: Hình ảnh Lifestyle & Feature Banners -->
                        <div class="col large-5 medium-12 small-12">
                            <div class="desc-media-stack">
                                <div class="lifestyle-hero-banner">
                                    <img src="https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=800&q=80" alt="Đi xa hơn mỗi ngày" loading="lazy" />
                                    <div class="banner-quote-overlay">
                                        <span class="quote-handwriting">"Đi xa hơn mỗi ngày"</span>
                                    </div>
                                </div>
                                
                                <div class="lifestyle-sub-card">
                                    <div class="sub-card-img">
                                        <img src="https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=400&q=80" alt="Sức mạnh trên mọi cung đường" loading="lazy" />
                                    </div>
                                    <div class="sub-card-text">
                                        <h4>Sức mạnh trên mọi cung đường</h4>
                                        <p>Vận hành mượt mà nhờ động cơ tân tiến, hỗ trợ lực đạp tối đa khi leo dốc hay di chuyển liên tục.</p>
                                    </div>
                                </div>

                                <div class="lifestyle-sub-card">
                                    <div class="sub-card-img">
                                        <img src="https://images.unsplash.com/photo-1511994298241-608e28f14fde?auto=format&fit=crop&w=400&q=80" alt="Thiết kế tinh tế" loading="lazy" />
                                    </div>
                                    <div class="sub-card-text">
                                        <h4>Thiết kế tinh tế & Trải nghiệm khác biệt</h4>
                                        <p>Khung sườn nhôm hàng không siêu bền, màu sắc sơn bóng bẩy mang đến phong cách thời thượng.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: THÔNG SỐ KỸ THUẬT (2 Cột Chuẩn Thiết Kế) -->
            <div class="gobike-tab-panel" id="tab-specifications">
                <div class="gobike-specs-container">
                    <h3 class="specs-section-title">Thông số kỹ thuật</h3>
                    <div class="specs-table-grid">
                        <!-- Cột Trái -->
                        <div class="specs-col">
                            <div class="spec-row"><span class="spec-lbl">Thương hiệu</span><span class="spec-val"><?php echo esc_html($specs['brand']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Model</span><span class="spec-val"><?php echo esc_html($product->get_name()); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Loại xe</span><span class="spec-val"><?php echo esc_html($specs['cat_name']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Động cơ</span><span class="spec-val"><?php echo esc_html($specs['dong_co']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Pin</span><span class="spec-val"><?php echo esc_html($specs['pin']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Quãng đường</span><span class="spec-val"><?php echo esc_html($specs['quang_duong']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Tốc độ tối đa</span><span class="spec-val"><?php echo esc_html($specs['toc_do']); ?></span></div>
                        </div>

                        <!-- Cột Phải -->
                        <div class="specs-col">
                            <div class="spec-row"><span class="spec-lbl">Khung xe</span><span class="spec-val"><?php echo esc_html($specs['khung_xe']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Phanh</span><span class="spec-val"><?php echo esc_html($specs['phanh']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Giảm xóc</span><span class="spec-val"><?php echo esc_html($specs['giam_xoc']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Lốp xe</span><span class="spec-val"><?php echo esc_html($specs['lop_xe']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Trọng lượng</span><span class="spec-val"><?php echo esc_html($specs['trong_luong']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Tải trọng tối đa</span><span class="spec-val"><?php echo esc_html($specs['tai_trong']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Kích thước (DxRxC)</span><span class="spec-val"><?php echo esc_html($specs['kich_thuoc']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Bảo hành</span><span class="spec-val"><?php echo esc_html($specs['bao_hanh']); ?></span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: HÌNH ẢNH & VIDEO CHI TIẾT -->
            <div class="gobike-tab-panel" id="tab-media">
                <div class="gobike-media-container">
                    <div class="media-header-flex">
                        <h3 class="media-section-title">Hình ảnh chi tiết bộ phận</h3>
                    </div>

                    <!-- Lưới 6 Ảnh Chi Tiết Cận Cảnh -->
                    <div class="media-photo-grid">
                        <div class="photo-card-item">
                            <img src="https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=400&q=80" alt="Khung hợp kim nhôm" loading="lazy" />
                            <span class="photo-caption">Khung hợp kim nhôm</span>
                        </div>
                        <div class="photo-card-item">
                            <img src="https://images.unsplash.com/photo-1532298229144-0ec0c57515c7?auto=format&fit=crop&w=400&q=80" alt="Động cơ mạnh mẽ" loading="lazy" />
                            <span class="photo-caption">Động cơ mạnh mẽ 250W</span>
                        </div>
                        <div class="photo-card-item">
                            <img src="https://images.unsplash.com/photo-1507035895480-2b3156c31fc8?auto=format&fit=crop&w=400&q=80" alt="Phuộc trước giảm xóc" loading="lazy" />
                            <span class="photo-caption">Phuộc trước giảm xóc</span>
                        </div>
                        <div class="photo-card-item">
                            <img src="https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=400&q=80" alt="Pin Lithium" loading="lazy" />
                            <span class="photo-caption">Pin Lithium 48V 15Ah</span>
                        </div>
                        <div class="photo-card-item">
                            <img src="https://images.unsplash.com/photo-1511994298241-608e28f14fde?auto=format&fit=crop&w=400&q=80" alt="Phanh đĩa thủy lực" loading="lazy" />
                            <span class="photo-caption">Phanh dầu thủy lực</span>
                        </div>
                        <div class="photo-card-item">
                            <img src="https://images.unsplash.com/photo-1576435728678-68d0fbf94e91?auto=format&fit=crop&w=400&q=80" alt="Bộ truyền động Shimano" loading="lazy" />
                            <span class="photo-caption">Bộ truyền động Shimano</span>
                        </div>
                    </div>

                    <!-- Khối Video Review Thực Tế -->
                    <div class="media-video-embed-box">
                        <h4 class="video-embed-title">Video trải nghiệm thực tế xe</h4>
                        <div class="video-responsive-wrap">
                            <iframe width="100%" height="480" src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Video Review Xe" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: ĐÁNH GIÁ KHÁCH HÀNG (Rating Summary & Review Cards) -->
            <div class="gobike-tab-panel" id="tab-reviews">
                <div class="gobike-reviews-container">
                    <div class="reviews-header-bar">
                        <h3 class="reviews-section-title">Đánh giá khách hàng</h3>
                        <button type="button" class="btn-write-review" onclick="jQuery('#review_form_wrapper').slideToggle();">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            <span>Viết đánh giá</span>
                        </button>
                    </div>

                    <!-- Box Tổng Điểm & Thanh Tiến Độ Sao -->
                    <div class="reviews-summary-row">
                        <div class="summary-score-box">
                            <div class="big-score">4.9<span>/5</span></div>
                            <div class="score-stars">★★★★★</div>
                            <span class="score-total-txt">Dựa trên 128 đánh giá</span>
                        </div>

                        <div class="summary-progress-bars">
                            <div class="star-bar-item">
                                <span class="bar-lbl">5 sao</span>
                                <div class="bar-track"><div class="bar-fill" style="width: 85%;"></div></div>
                                <span class="bar-percent">85%</span>
                            </div>
                            <div class="star-bar-item">
                                <span class="bar-lbl">4 sao</span>
                                <div class="bar-track"><div class="bar-fill" style="width: 10%;"></div></div>
                                <span class="bar-percent">10%</span>
                            </div>
                            <div class="star-bar-item">
                                <span class="bar-lbl">3 sao</span>
                                <div class="bar-track"><div class="bar-fill" style="width: 3%;"></div></div>
                                <span class="bar-percent">3%</span>
                            </div>
                            <div class="star-bar-item">
                                <span class="bar-lbl">2 sao</span>
                                <div class="bar-track"><div class="bar-fill" style="width: 1%;"></div></div>
                                <span class="bar-percent">1%</span>
                            </div>
                            <div class="star-bar-item">
                                <span class="bar-lbl">1 sao</span>
                                <div class="bar-track"><div class="bar-fill" style="width: 1%;"></div></div>
                                <span class="bar-percent">1%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Đánh Giá Ẩn (Sẽ Mở Ra Khi Bấm Viết Đánh Giá) -->
                    <div id="review_form_wrapper" style="display: none; margin-bottom: 30px;">
                        <?php comments_template(); ?>
                    </div>

                    <!-- Lưới 4 Thẻ Đánh Giá Mẫu Đẹp Kèm Ảnh Thật -->
                    <div class="reviews-cards-grid">
                        <div class="review-card-item">
                            <div class="card-author-row">
                                <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=80&q=80" alt="Nguyễn Minh Tuấn" class="author-avatar" />
                                <div class="author-meta">
                                    <strong class="author-name">Nguyễn Minh Tuấn <span class="verified-tag">✔</span></strong>
                                    <span class="review-date">12/09/2026</span>
                                </div>
                            </div>
                            <div class="review-stars-val">★★★★★</div>
                            <p class="review-comment">Xe rất chắc chắn, trợ lực mượt mà, leo dốc nhẹ nhàng. Rất hài lòng với mẫu xe này!</p>
                            <div class="review-attached-imgs">
                                <img src="https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=150&q=80" alt="Review photo 1" />
                                <img src="https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=150&q=80" alt="Review photo 2" />
                            </div>
                        </div>

                        <div class="review-card-item">
                            <div class="card-author-row">
                                <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=80&q=80" alt="Trần Thị Mai" class="author-avatar" />
                                <div class="author-meta">
                                    <strong class="author-name">Trần Thị Mai <span class="verified-tag">✔</span></strong>
                                    <span class="review-date">28/08/2026</span>
                                </div>
                            </div>
                            <div class="review-stars-val">★★★★★</div>
                            <p class="review-comment">Thiết kế đẹp, pin rất bền. Mình đã đi các chuyến dã ngoại 100km vận hành ổn định, xe đạp êm ái!</p>
                            <div class="review-attached-imgs">
                                <img src="https://images.unsplash.com/photo-1507035895480-2b3156c31fc8?auto=format&fit=crop&w=150&q=80" alt="Review photo 1" />
                                <img src="https://images.unsplash.com/photo-1511994298241-608e28f14fde?auto=format&fit=crop&w=150&q=80" alt="Review photo 2" />
                            </div>
                        </div>

                        <div class="review-card-item">
                            <div class="card-author-row">
                                <img src="https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=80&q=80" alt="Lê Hoàng Nam" class="author-avatar" />
                                <div class="author-meta">
                                    <strong class="author-name">Lê Hoàng Nam <span class="verified-tag">✔</span></strong>
                                    <span class="review-date">10/08/2026</span>
                                </div>
                            </div>
                            <div class="review-stars-val">★★★★★</div>
                            <p class="review-comment">Giao hàng nhanh, đóng gói cẩn thận. Xe đầm, màu sắc đẹp. Sẽ giới thiệu cho bạn bè!</p>
                            <div class="review-attached-imgs">
                                <img src="https://images.unsplash.com/photo-1532298229144-0ec0c57515c7?auto=format&fit=crop&w=150&q=80" alt="Review photo 1" />
                                <img src="https://images.unsplash.com/photo-1576435728678-68d0fbf94e91?auto=format&fit=crop&w=150&q=80" alt="Review photo 2" />
                            </div>
                        </div>

                        <div class="review-card-item">
                            <div class="card-author-row">
                                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=80&q=80" alt="Phạm Thu Hà" class="author-avatar" />
                                <div class="author-meta">
                                    <strong class="author-name">Phạm Thu Hà <span class="verified-tag">✔</span></strong>
                                    <span class="review-date">05/08/2026</span>
                                </div>
                            </div>
                            <div class="review-stars-val">★★★★★</div>
                            <p class="review-comment">Trải nghiệm tuyệt vời! Trợ lực êm ái, túi mềm, phù hợp cả đi làm lẫn đi dã ngoại cuối tuần.</p>
                            <div class="review-attached-imgs">
                                <img src="https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=150&q=80" alt="Review photo 1" />
                                <img src="https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=150&q=80" alt="Review photo 2" />
                            </div>
                        </div>
                    </div>

                    <div class="reviews-footer-link">
                        <a href="#tab-reviews" class="link-see-all-reviews">Xem tất cả đánh giá <i class="fa fa-angle-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- TAB 5: HỎI ĐÁP (FAQ ACCORDION) -->
            <div class="gobike-tab-panel" id="tab-faq">
                <div class="gobike-faq-container">
                    <div class="faq-header-bar">
                        <div class="faq-title-wrap">
                            <h3 class="faq-section-title">Hỏi đáp</h3>
                            <p class="faq-subtitle">Những câu hỏi thường gặp về <?php echo esc_html($product->get_name()); ?></p>
                        </div>
                        <div class="faq-actions-wrap">
                            <a href="https://zalo.me/0944988699" target="_blank" rel="nofollow" class="btn-ask-question">Đặt câu hỏi</a>
                        </div>
                    </div>

                    <!-- Accordion Lưới 2 Cột -->
                    <div class="faq-accordion-grid">
                        <!-- Cột Trái -->
                        <div class="faq-col">
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span><?php echo esc_html($product->get_name()); ?> phù hợp với những đối tượng nào?</span>
                                    <span class="faq-icon">+</span>
                                </div>
                                <div class="faq-answer">
                                    <p>Xe phù hợp cho học sinh, sinh viên, người đi làm và những ai yêu thích dã ngoại, thể thao nhờ thiết kế thể thao linh hoạt và hệ thống trợ lực điện thông minh.</p>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>Thời gian sạc đầy pin là bao lâu?</span>
                                    <span class="faq-icon">+</span>
                                </div>
                                <div class="faq-answer">
                                    <p>Thời gian sạc đầy pin Lithium dao động từ 4 – 6 giờ với củ sạc thông minh tự ngắt khi đầy, bảo vệ tuổi thọ pin tối đa.</p>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>Xe có thể đi được bao nhiêu km sau mỗi lần sạc?</span>
                                    <span class="faq-icon">+</span>
                                </div>
                                <div class="faq-answer">
                                    <p>Ở chế độ thuần điện xe đi được khoảng 40 – 50 km; ở chế độ trợ lực điện thông minh xe đạt quãng đường lên tới 80 – 120 km tùy vào trọng lượng người lái và điều kiện mặt đường.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Cột Phải -->
                        <div class="faq-col">
                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>Xe có hỗ trợ lắp ráp khi giao hàng không?</span>
                                    <span class="faq-icon">+</span>
                                </div>
                                <div class="faq-answer">
                                    <p>GoBike hỗ trợ lắp ráp hoàn chỉnh và căn chỉnh kỹ thuật 100% trước khi giao đến tận nhà cho quý khách trên toàn quốc.</p>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>Chế độ bảo hành của xe như thế nào?</span>
                                    <span class="faq-icon">+</span>
                                </div>
                                <div class="faq-answer">
                                    <p>Sản phẩm được bảo hành chính hãng 24 tháng đối với khung sườn xe, 12 tháng đối với động cơ điện và cụm pin, kèm chế độ bảo dưỡng tra dầu miễn phí trọn đời.</p>
                                </div>
                            </div>

                            <div class="faq-item">
                                <div class="faq-question">
                                    <span>Tôi có thể mua xe trả góp không?</span>
                                    <span class="faq-icon">+</span>
                                </div>
                                <div class="faq-answer">
                                    <p>Có. GoBike hỗ trợ trả góp lãi suất 0% qua thẻ tín dụng hoặc công ty tài chính với thủ tục nhanh gọn chỉ trong 15 phút.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="faq-footer-link">
                        <a href="https://zalo.me/0944988699" target="_blank" rel="nofollow" class="link-see-all-faqs">Xem tất cả câu hỏi <i class="fa fa-angle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
}

/**
 * 5. Render Section 3: Sản Phẩm Liên Quan (Tabs Lọc Phân Loại + Swiper Carousel)
 */
function gobike_render_single_product_related($product) {
    $product_id = $product->get_id();
    $cat_ids = $product->get_category_ids();

    // Query sản phẩm liên quan
    $related_query = new WP_Query(array(
        'post_type'      => 'product',
        'posts_per_page' => 10,
        'post__not_in'   => array($product_id),
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $cat_ids,
            ),
        ),
    ));
    ?>
    <section class="gobike-related-section">
        <div class="container">
            <!-- Header Tabs Phân Loại Sản Phẩm Liên Quan -->
            <div class="related-header-flex">
                <div class="related-title-and-tabs">
                    <h3 class="related-heading">Sản phẩm liên quan</h3>
                    <div class="related-filter-tabs">
                        <button type="button" class="tab-filter-btn active" data-filter="all">Cùng thương hiệu</button>
                        <button type="button" class="tab-filter-btn" data-filter="xe-dia-hinh">Xe địa hình</button>
                        <button type="button" class="tab-filter-btn" data-filter="xe-gap-gon">Xe gấp gọn</button>
                        <button type="button" class="tab-filter-btn" data-filter="xe-touring">Xe touring</button>
                        <button type="button" class="tab-filter-btn" data-filter="ban-chay">Sản phẩm bán chạy</button>
                    </div>
                </div>
                <div class="related-nav-and-link">
                    <a href="<?php echo esc_url(home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/')); ?>" class="link-view-all-related">Xem tất cả <i class="fa fa-angle-right"></i></a>
                    <div class="related-slider-nav">
                        <button type="button" class="rel-btn rel-prev" aria-label="Trước">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor"><path d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/></svg>
                        </button>
                        <button type="button" class="rel-btn rel-next" aria-label="Sau">
                            <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor"><path d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Swiper Carousel Sản Phẩm Liên Quan (5 Cột) -->
            <div class="swiper-container gobike-related-carousel-swiper">
                <div class="swiper-wrapper">
                    <?php if ($related_query->have_posts()): ?>
                        <?php while ($related_query->have_posts()): $related_query->the_post(); 
                            $rel_product = wc_get_product(get_the_ID());
                            if (!$rel_product) continue;
                        ?>
                            <div class="swiper-slide rel-product-slide">
                                <div class="rel-card-box">
                                    <div class="rel-card-image">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php echo $rel_product->get_image('woocommerce_thumbnail'); ?>
                                        </a>
                                        <?php if ($rel_product->is_on_sale()): ?>
                                            <span class="rel-badge-sale">Giảm giá</span>
                                        <?php endif; ?>
                                        <button type="button" class="rel-btn-wishlist" title="Yêu thích">♡</button>
                                    </div>
                                    <div class="rel-card-info">
                                        <h4 class="rel-product-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                        <div class="rel-price-wrap">
                                            <span class="rel-price-current"><?php echo wc_price($rel_product->get_price()); ?></span>
                                            <?php if ($rel_product->get_regular_price() > $rel_product->get_price()): ?>
                                                <span class="rel-price-old"><?php echo wc_price($rel_product->get_regular_price()); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="rel-card-actions">
                                            <a href="?add-to-cart=<?php echo get_the_ID(); ?>" class="btn-rel-cart" data-quantity="1" data-product_id="<?php echo get_the_ID(); ?>">Thêm vào giỏ</a>
                                            <a href="<?php the_permalink(); ?>" class="btn-rel-view">Xem chi tiết</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Dải 5 Cam Kết Dịch Vụ Chân Trang Chuẩn Mẫu -->
            <div class="gobike-footer-trust-strip">
                <div class="footer-trust-item">
                    <div class="trust-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div class="trust-text-box">
                        <strong>Sản phẩm chính hãng</strong>
                        <span>Cam kết 100% chính hãng</span>
                    </div>
                </div>

                <div class="footer-trust-item">
                    <div class="trust-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    </div>
                    <div class="trust-text-box">
                        <strong>Giao hàng toàn quốc</strong>
                        <span>Nhanh chóng, an toàn</span>
                    </div>
                </div>

                <div class="footer-trust-item">
                    <div class="trust-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                    </div>
                    <div class="trust-text-box">
                        <strong>Lắp ráp miễn phí</strong>
                        <span>Tại nhà và tại showroom</span>
                    </div>
                </div>

                <div class="footer-trust-item">
                    <div class="trust-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                    <div class="trust-text-box">
                        <strong>Trả góp 0%</strong>
                        <span>Thủ tục đơn giản, nhanh chóng</span>
                    </div>
                </div>

                <div class="footer-trust-item">
                    <div class="trust-icon-box">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </div>
                    <div class="trust-text-box">
                        <strong>Tư vấn 24/7</strong>
                        <span>0944 988 699</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
}
