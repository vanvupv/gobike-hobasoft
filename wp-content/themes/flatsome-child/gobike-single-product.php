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

    $gf = function_exists('get_field');

    // Lấy thông số từ thuộc tính WooCommerce hoặc ACF
    $pin = $product->get_attribute('pa_pin');
    if (!$pin) $pin = ($gf ? get_field('dung_luong_pin', $product_id) : '') ?: '48V 15Ah (Lithium)';

    $dong_co = $product->get_attribute('pa_dong-co');
    if (!$dong_co) $dong_co = ($gf ? get_field('dong_co', $product_id) : '') ?: '250W – Trợ lực thông minh';

    $quang_duong = $product->get_attribute('pa_quang-duong');
    if (!$quang_duong) $quang_duong = ($gf ? get_field('quang_duong', $product_id) : '') ?: '80 – 120 km (tùy điều kiện)';

    $toc_do = $product->get_attribute('pa_toc-do');
    if (!$toc_do) $toc_do = ($gf ? get_field('toc_do_toi_da', $product_id) : '') ?: '25 km/h (trợ lực)';

    $lop_xe = $product->get_attribute('pa_kich-thuoc-lop');
    if (!$lop_xe) $lop_xe = ($gf ? get_field('kich_thuoc_lop', $product_id) : '') ?: '27.5 inch – chống trượt';

    $trong_luong = $product->get_weight() ? $product->get_weight() . ' kg' : (($gf ? get_field('trong_luong', $product_id) : '') ?: '27 kg');
    $bao_hanh = ($gf ? get_field('thoi_gian_bao_hanh', $product_id) : '') ?: '24 tháng (Khung), 12 tháng (Động cơ, pin)';

    // Thương hiệu
    $brand_terms = get_the_terms($product_id, 'pa_thuong-hieu');
    $brand = 'PHOENIX';
    if (!empty($brand_terms) && !is_wp_error($brand_terms)) {
        $brand = $brand_terms[0]->name;
    } else {
        $brand_acf = $gf ? get_field('thuong_hieu', $product_id) : '';
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

    $extra_specs = ($gf && function_exists('have_rows') && have_rows('thong_so_bo_sung', $product_id)) ? get_field('thong_so_bo_sung', $product_id) : array();
    if (!is_array($extra_specs)) $extra_specs = array();

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
        'khung_xe'       => ($gf ? get_field('chat_lieu_khung', $product_id) : '') ?: 'Hợp kim nhôm 6061',
        'phanh'          => ($gf ? get_field('he_thong_phanh', $product_id) : '') ?: 'Phanh dầu thủy lực',
        'giam_xoc'       => ($gf ? get_field('giam_xoc', $product_id) : '') ?: 'Phuộc trước khóa hành trình',
        'tai_trong'      => ($gf ? get_field('tai_trong_toi_da', $product_id) : '') ?: '120 kg',
        'kich_thuoc'     => ($gf ? get_field('kich_thuoc_xe', $product_id) : '') ?: '1780 x 680 x 1050 mm',
        'extra_specs'    => $extra_specs,
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

                    <!-- Điều hướng Swiper -->
                    <div class="swiper-button-prev gobike-gal-prev"></div>
                    <div class="swiper-button-next gobike-gal-next"></div>
                    <div class="swiper-pagination gobike-gal-pagination"></div>
                </div>
            </div>
        </div>

        <!-- Dải 4 Thông Số Nhanh Dưới Gallery (Quick Spec Badges) -->
        <?php
        // Xử lý giá trị gọn gàng, có đầy đủ đơn vị
        $qd_raw = $specs['quang_duong'];
        $qd_display = trim(explode('(', $qd_raw)[0]);
        if (!str_contains($qd_display, 'km')) {
            $qd_display .= ' km';
        }

        $dc_raw = $specs['dong_co'];
        $dc_display = trim(explode('–', $dc_raw)[0]);
        if (!str_contains($dc_display, 'W') && !str_contains($dc_display, 'w')) {
            $dc_display .= ' 250W';
        }

        $pin_raw = $specs['pin'];
        $pin_display = trim(explode('(', $pin_raw)[0]);

        $lop_raw = $specs['lop_xe'];
        $lop_display = trim(explode('–', $lop_raw)[0]);
        ?>
        <div class="gobike-quick-spec-badges">
            <div class="spec-badge-item">
                <div class="badge-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="badge-info">
                    <strong class="badge-val"><?php echo esc_html($qd_display); ?></strong>
                    <span class="badge-label">Quãng đường trợ lực</span>
                </div>
            </div>

            <div class="spec-badge-item">
                <div class="badge-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                </div>
                <div class="badge-info">
                    <strong class="badge-val"><?php echo esc_html($dc_display); ?></strong>
                    <span class="badge-label">Động cơ mạnh mẽ</span>
                </div>
            </div>

            <div class="spec-badge-item">
                <div class="badge-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="6" width="18" height="12" rx="2"/><line x1="23" y1="13" x2="23" y2="11"/></svg>
                </div>
                <div class="badge-info">
                    <strong class="badge-val"><?php echo esc_html($pin_display); ?></strong>
                    <span class="badge-label">Pin Lithium cao cấp</span>
                </div>
            </div>

            <div class="spec-badge-item">
                <div class="badge-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
                <div class="badge-info">
                    <strong class="badge-val"><?php echo esc_html($lop_display); ?></strong>
                    <span class="badge-label">Lốp xe đa địa hình</span>
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
add_filter( 'comments_open', 'gobike_force_product_comments_open', 99, 2 );
function gobike_force_product_comments_open( $open, $post_id ) {
    if ( get_post_type( $post_id ) === 'product' ) {
        return true;
    }
    return $open;
}

add_filter( 'pre_option_woocommerce_review_rating_verification_required', function() {
    return 'no';
});

function gobike_get_feature_icon_svg($type) {
    switch ($type) {
        case 'assist':
            return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg>';
        case 'design':
            return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
        case 'eco':
            return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>';
        case 'battery':
            return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="16" height="10" rx="2" ry="2"/><line x1="22" y1="11" x2="22" y2="13"/><polygon points="9 11 11 11 10 13 12 13" fill="currentColor"/></svg>';
        case 'motor':
            return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>';
        case 'brake':
            return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/><line x1="12" y1="3" x2="12" y2="5"/><line x1="12" y1="19" x2="12" y2="21"/><line x1="3" y1="12" x2="5" y2="12"/><line x1="19" y1="12" x2="21" y2="12"/></svg>';
        case 'terrain':
        default:
            return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>';
    }
}

function gobike_get_youtube_embed_url($url) {
    if (empty($url)) return '';
    if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $url, $matches)) {
        return 'https://www.youtube.com/embed/' . $matches[1];
    }
    return $url;
}

function gobike_render_single_product_tabs($product) {
    global $post;
    $product_id = $product->get_id();
    $gf = function_exists('get_field');

    $specs = gobike_get_single_product_specs($product_id);
    $review_count = $product->get_review_count();
    $review_count_display = $review_count > 0 ? $review_count : 128;

    // --- TAB 1: Dữ liệu Mô tả & Banners ---
    $desc_subtitle = $gf ? get_field('sp_desc_subtitle', $product_id) : '';
    if (empty($desc_subtitle)) $desc_subtitle = 'Khám phá thế giới theo cách của bạn';

    $sp_features = $gf ? get_field('sp_features', $product_id) : array();
    if (empty($sp_features) || !is_array($sp_features)) {
        $sp_features = array(
            array('icon_type' => 'terrain', 'title' => 'Chinh phục mọi địa hình', 'desc' => 'Vận hành mạnh mẽ, an tâm trên cả đường phố và đường mòn đồi dốc.'),
            array('icon_type' => 'assist',  'title' => 'Trợ lực thông minh',     'desc' => 'Hỗ trợ đạp nhẹ nhàng hơn, tiết kiệm sức lực, đi xa hơn mỗi ngày.'),
            array('icon_type' => 'design',  'title' => 'Thiết kế hiện đại',      'desc' => 'Khung dáng thể thao, mạnh mẽ, phù hợp phong cách sống năng động.'),
            array('icon_type' => 'eco',     'title' => 'Thân thiện môi trường',  'desc' => 'Sử dụng năng lượng sạch, góp phần bảo vệ môi trường xanh bền vững.'),
        );
    }

    $hero_img = $gf ? get_field('sp_lifestyle_hero_img', $product_id) : '';
    if (empty($hero_img)) {
        $hero_img = has_post_thumbnail($product_id) ? get_the_post_thumbnail_url($product_id, 'large') : 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=800&q=80';
    }
    $hero_quote = $gf ? get_field('sp_lifestyle_hero_quote', $product_id) : '';
    if (empty($hero_quote)) $hero_quote = '"Đi xa hơn mỗi ngày"';

    $lifestyle_cards = $gf ? get_field('sp_lifestyle_cards', $product_id) : array();
    if (empty($lifestyle_cards) || !is_array($lifestyle_cards)) {
        $lifestyle_cards = array(
            array(
                'image' => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=400&q=80',
                'title' => 'Sức mạnh trên mọi cung đường',
                'desc'  => 'Vận hành mượt mà nhờ động cơ tân tiến, hỗ trợ lực đạp tối đa khi leo dốc hay di chuyển liên tục.'
            ),
            array(
                'image' => 'https://images.unsplash.com/photo-1511994298241-608e28f14fde?auto=format&fit=crop&w=400&q=80',
                'title' => 'Thiết kế tinh tế & Trải nghiệm khác biệt',
                'desc'  => 'Khung sườn nhôm hàng không siêu bền, màu sắc sơn bóng bẩy mang đến phong cách thời thượng.'
            ),
        );
    }

    // --- TAB 3: Dữ liệu Hình ảnh & Video ---
    $media_title = $gf ? get_field('sp_media_photos_title', $product_id) : '';
    if (empty($media_title)) $media_title = 'HÌNH ẢNH CHI TIẾT BỘ PHẬN';

    $detail_photos = $gf ? get_field('sp_detail_photos', $product_id) : array();
    if (empty($detail_photos) || !is_array($detail_photos)) {
        // Fallback sang gallery ảnh của WooCommerce
        $gallery_ids = $product->get_gallery_image_ids();
        if (!empty($gallery_ids)) {
            $detail_photos = array();
            $captions = array('Khung sườn xe', 'Động cơ trợ lực', 'Giảm xóc trước', 'Cụm Pin Lithium', 'Hệ thống phanh', 'Bộ truyền động');
            $idx = 0;
            foreach ($gallery_ids as $gid) {
                $detail_photos[] = array(
                    'image'   => wp_get_attachment_image_url($gid, 'medium_large'),
                    'caption' => isset($captions[$idx]) ? $captions[$idx] : ('Chi tiết ' . $product->get_name()),
                );
                $idx++;
                if ($idx >= 6) break;
            }
        }
    }
    if (empty($detail_photos)) {
        $detail_photos = array(
            array('image' => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=400&q=80', 'caption' => 'Khung hợp kim nhôm'),
            array('image' => 'https://images.unsplash.com/photo-1532298229144-0ec0c57515c7?auto=format&fit=crop&w=400&q=80', 'caption' => 'Động cơ mạnh mẽ'),
            array('image' => 'https://images.unsplash.com/photo-1507035895480-2b3156c31fc8?auto=format&fit=crop&w=400&q=80', 'caption' => 'Phuộc trước giảm xóc'),
            array('image' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=400&q=80', 'caption' => 'Pin Lithium tháo rời'),
            array('image' => 'https://images.unsplash.com/photo-1511994298241-608e28f14fde?auto=format&fit=crop&w=400&q=80', 'caption' => 'Phanh dầu thủy lực'),
            array('image' => 'https://images.unsplash.com/photo-1576435728678-68d0fbf94e91?auto=format&fit=crop&w=400&q=80', 'caption' => 'Bộ truyền động Shimano'),
        );
    }

    $video_title = $gf ? get_field('sp_video_title', $product_id) : '';
    if (empty($video_title)) $video_title = 'Video trải nghiệm thực tế xe';

    $video_url = $gf ? get_field('sp_video_url', $product_id) : '';
    if (empty($video_url)) {
        // Tìm từ video_review CPT liên kết
        $cpt_v = get_posts(array(
            'post_type'      => 'video_review',
            'posts_per_page' => 1,
            'meta_key'       => 'related_product',
            'meta_value'     => $product_id,
        ));
        if (!empty($cpt_v) && $gf) {
            $video_url = get_field('video_url', $cpt_v[0]->ID);
        }
    }
    $video_embed_url = gobike_get_youtube_embed_url($video_url);

    // --- TAB 4: Dữ liệu Đánh giá ---
    $rating_score = $gf ? get_field('sp_rating_score', $product_id) : '';
    if (empty($rating_score)) {
        $avg = $product->get_average_rating();
        $rating_score = $avg ? number_format((float)$avg, 1) : '4.9';
    }

    $total_text = $gf ? get_field('sp_rating_total_text', $product_id) : '';
    if (empty($total_text)) $total_text = 'Dựa trên ' . $review_count_display . ' đánh giá';

    $s5 = $gf ? (int) get_field('sp_star_5', $product_id) : 85;
    $s4 = $gf ? (int) get_field('sp_star_4', $product_id) : 10;
    $s3 = $gf ? (int) get_field('sp_star_3', $product_id) : 3;
    $s2 = $gf ? (int) get_field('sp_star_2', $product_id) : 1;
    $s1 = $gf ? (int) get_field('sp_star_1', $product_id) : 1;
    if ($s5 <= 0 && $s4 <= 0 && $s3 <= 0 && $s2 <= 0 && $s1 <= 0) {
        $s5 = 85; $s4 = 10; $s3 = 3; $s2 = 1; $s1 = 1;
    }

    $featured_reviews = $gf ? get_field('sp_featured_reviews', $product_id) : array();
    if (empty($featured_reviews) || !is_array($featured_reviews)) {
        $featured_reviews = array(
            array(
                'name'    => 'Nguyễn Minh Tuấn',
                'avatar'  => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=80&q=80',
                'date'    => '12/04/2024',
                'stars'   => '5',
                'comment' => 'Xe rất chắc chắn, trợ lực mượt mà, leo dốc nhẹ như không. Rất hài lòng với ' . $specs['brand'] . '!',
                'photo_1' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=150&q=80',
                'photo_2' => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=150&q=80',
            ),
            array(
                'name'    => 'Trần Thị Mai',
                'avatar'  => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=80&q=80',
                'date'    => '25/03/2024',
                'stars'   => '5',
                'comment' => 'Thiết kế đẹp, pin rất bền. Mình đã đi các chuyến dã ngoại dài, xe vận hành ổn định, cực kỳ đáng tiền!',
                'photo_1' => 'https://images.unsplash.com/photo-1507035895480-2b3156c31fc8?auto=format&fit=crop&w=150&q=80',
                'photo_2' => 'https://images.unsplash.com/photo-1511994298241-608e28f14fde?auto=format&fit=crop&w=150&q=80',
            ),
            array(
                'name'    => 'Lê Hoàng Nam',
                'avatar'  => 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=80&q=80',
                'date'    => '16/05/2024',
                'stars'   => '5',
                'comment' => 'Giao hàng nhanh, lắp ráp cẩn thận. Xe đi êm, màu sắc đẹp. Sẽ giới thiệu cho bạn bè!',
                'photo_1' => 'https://images.unsplash.com/photo-1532298229144-0ec0c57515c7?auto=format&fit=crop&w=150&q=80',
                'photo_2' => 'https://images.unsplash.com/photo-1576435728678-68d0fbf94e91?auto=format&fit=crop&w=150&q=80',
            ),
            array(
                'name'    => 'Phạm Thu Hà',
                'avatar'  => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=80&q=80',
                'date'    => '08/03/2024',
                'stars'   => '5',
                'comment' => 'Trải nghiệm tuyệt vời! Trợ lực êm ái, tự nhiên, phù hợp cả đi làm lẫn đi du lịch cuối tuần.',
                'photo_1' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=150&q=80',
                'photo_2' => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=150&q=80',
            ),
        );
    }

    // --- TAB 5: Dữ liệu Hỏi đáp ---
    $faq_slogan = $gf ? get_field('sp_faq_slogan', $product_id) : '';
    if (empty($faq_slogan)) $faq_slogan = 'Những câu hỏi thường gặp về ' . $product->get_name();

    $faq_zalo = $gf ? get_field('sp_faq_zalo_link', $product_id) : '';
    if (empty($faq_zalo)) $faq_zalo = 'https://zalo.me/0944988699';

    $faqs = $gf ? get_field('sp_faqs', $product_id) : array();
    if (empty($faqs) || !is_array($faqs)) {
        $faqs = array(
            array('question' => $product->get_name() . ' phù hợp với những đối tượng nào?', 'answer' => 'Xe phù hợp cho học sinh, sinh viên, người đi làm và những ai yêu thích dã ngoại, thể thao nhờ thiết kế thể thao linh hoạt và hệ thống trợ lực điện thông minh.'),
            array('question' => 'Thời gian sạc đầy pin là bao lâu?', 'answer' => 'Thời gian sạc đầy pin Lithium dao động từ 4 – 6 giờ với củ sạc thông minh tự ngắt khi đầy, bảo vệ tuổi thọ pin tối đa.'),
            array('question' => 'Xe có thể đi được bao nhiêu km sau mỗi lần sạc?', 'answer' => 'Ở chế độ thuần điện xe đi được khoảng 40 – 50 km; ở chế độ trợ lực điện thông minh xe đạt quãng đường lên tới 80 – 120 km tùy vào trọng lượng người lái và điều kiện mặt đường.'),
            array('question' => 'Xe có hỗ trợ lắp ráp khi giao hàng không?', 'answer' => 'GoBike hỗ trợ lắp ráp hoàn chỉnh và căn chỉnh kỹ thuật 100% trước khi giao đến tận nhà cho quý khách trên toàn quốc.'),
            array('question' => 'Chế độ bảo hành của ' . $product->get_name() . ' như thế nào?', 'answer' => 'Sản phẩm được bảo hành chính hãng 24 tháng đối với khung sườn xe, 12 tháng đối với động cơ điện và cụm pin, kèm chế độ bảo dưỡng tra dầu miễn phí trọn đời.'),
            array('question' => 'Tôi có thể trả góp khi mua xe không?', 'answer' => 'Có. GoBike hỗ trợ trả góp lãi suất 0% qua thẻ tín dụng hoặc công ty tài chính với thủ tục nhanh gọn chỉ trong 15 phút.'),
        );
    }
    $faq_count = count($faqs);

    // Chia mảng FAQ thành 2 cột đều nhau
    $half = ceil(count($faqs) / 2);
    $faqs_left = array_slice($faqs, 0, $half);
    $faqs_right = array_slice($faqs, $half);
    ?>
    <section class="gobike-product-tabs-section" id="gobikeProductTabsSection">
        <!-- Sticky Tabs Header (Không viền) -->
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
                        <a href="#tab-reviews">Đánh giá <span>(<?php echo esc_html($review_count_display); ?>)</span></a>
                    </li>
                    <li class="tab-nav-item" data-tab="tab-faq">
                        <a href="#tab-faq">Hỏi đáp <span>(<?php echo esc_html($faq_count); ?>)</span></a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="container gobike-tabs-content-wrap">
            <!-- TAB 1: MÔ TẢ SẢN PHẨM -->
            <div class="gobike-tab-panel active" id="tab-description">
                <div class="gobike-desc-container">
                    <!-- PHẦN 1: NỘI DUNG & LIFESTYLE BANNER -->
                    <div class="gobike-desc-content-collapsible" id="descContentCollapsible">
                        <div class="desc-content-inner">
                            <div class="row align-top desc-intro-row">
                                <!-- Cột Trái: Text mô tả + Icon tính năng nổi bật -->
                                <div class="col large-7 medium-12 small-12">
                                    <h2 class="desc-heading-primary"><?php echo esc_html($desc_subtitle); ?></h2>
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

                                    <!-- Lưới Tính Năng Nổi Bật (Dynamic ACF) -->
                                    <?php if (!empty($sp_features)): ?>
                                        <div class="desc-feature-grid">
                                            <?php foreach ($sp_features as $feat): ?>
                                                <div class="feature-box-item">
                                                    <div class="feature-icon">
                                                        <?php echo gobike_get_feature_icon_svg($feat['icon_type'] ?? 'terrain'); ?>
                                                    </div>
                                                    <div class="feature-text">
                                                        <h4><?php echo esc_html($feat['title'] ?? ''); ?></h4>
                                                        <p><?php echo esc_html($feat['desc'] ?? ''); ?></p>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Cột Phải: Hình ảnh Lifestyle & Banners -->
                                <div class="col large-5 medium-12 small-12">
                                    <div class="desc-media-stack">
                                        <?php if (!empty($hero_img)): ?>
                                            <div class="lifestyle-hero-banner">
                                                <img src="<?php echo esc_url($hero_img); ?>" alt="<?php echo esc_attr($product->get_name()); ?>" loading="lazy" />
                                                <?php if (!empty($hero_quote)): ?>
                                                    <div class="banner-quote-overlay">
                                                        <span class="quote-handwriting"><?php echo esc_html($hero_quote); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (!empty($lifestyle_cards)): ?>
                                            <?php foreach ($lifestyle_cards as $card): ?>
                                                <div class="lifestyle-sub-card">
                                                    <?php if (!empty($card['image'])): ?>
                                                        <div class="sub-card-img">
                                                            <img src="<?php echo esc_url($card['image']); ?>" alt="<?php echo esc_attr($card['title'] ?? ''); ?>" loading="lazy" />
                                                        </div>
                                                    <?php endif; ?>
                                                    <div class="sub-card-text">
                                                        <h4><?php echo esc_html($card['title'] ?? ''); ?></h4>
                                                        <p><?php echo esc_html($card['desc'] ?? ''); ?></p>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gradient mờ đáy & Nút Xem thêm / Thu gọn -->
                        <div class="desc-content-gradient"></div>
                        <div class="desc-content-btn-wrap">
                            <button type="button" class="btn-toggle-desc-content" id="btnToggleDescContent">
                                <span class="toggle-txt">Xem thêm</span>
                                <svg class="toggle-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- PHẦN 2: THÔNG SỐ KỸ THUẬT (Tóm tắt tại Tab 1) -->
                    <div class="desc-specs-block">
                        <div class="gobike-block-header">
                            <div class="header-left">
                                <h2 class="block-title">THÔNG SỐ KỸ THUẬT</h2>
                            </div>
                        </div>
                        <div class="specs-table-grid">
                            <div class="specs-col">
                                <div class="spec-row"><span class="spec-lbl">Thương hiệu</span><span class="spec-val"><?php echo esc_html($specs['brand']); ?></span></div>
                                <div class="spec-row"><span class="spec-lbl">Model</span><span class="spec-val"><?php echo esc_html($product->get_name()); ?></span></div>
                                <div class="spec-row"><span class="spec-lbl">Loại xe</span><span class="spec-val"><?php echo esc_html($specs['cat_name']); ?></span></div>
                                <div class="spec-row"><span class="spec-lbl">Động cơ</span><span class="spec-val"><?php echo esc_html($specs['dong_co']); ?></span></div>
                                <div class="spec-row"><span class="spec-lbl">Pin</span><span class="spec-val"><?php echo esc_html($specs['pin']); ?></span></div>
                                <div class="spec-row"><span class="spec-lbl">Quãng đường</span><span class="spec-val"><?php echo esc_html($specs['quang_duong']); ?></span></div>
                                <div class="spec-row"><span class="spec-lbl">Tốc độ tối đa</span><span class="spec-val"><?php echo esc_html($specs['toc_do']); ?></span></div>
                            </div>
                            <div class="specs-col">
                                <div class="spec-row"><span class="spec-lbl">Khung xe</span><span class="spec-val"><?php echo esc_html($specs['khung_xe']); ?></span></div>
                                <div class="spec-row"><span class="spec-lbl">Phanh</span><span class="spec-val"><?php echo esc_html($specs['phanh']); ?></span></div>
                                <div class="spec-row"><span class="spec-lbl">Giảm xóc</span><span class="spec-val"><?php echo esc_html($specs['giam_xoc']); ?></span></div>
                                <div class="spec-row"><span class="spec-lbl">Lốp xe</span><span class="spec-val"><?php echo esc_html($specs['lop_xe']); ?></span></div>
                                <div class="spec-row"><span class="spec-lbl">Trọng lượng</span><span class="spec-val"><?php echo esc_html($specs['trong_luong']); ?></span></div>
                                <div class="spec-row"><span class="spec-lbl">Tải trọng tối đa</span><span class="spec-val"><?php echo esc_html($specs['tai_trong']); ?></span></div>
                                <div class="spec-row"><span class="spec-lbl">Kích thước (DxRxC)</span><span class="spec-val"><?php echo esc_html($specs['kich_thuoc']); ?></span></div>
                                <div class="spec-row"><span class="spec-lbl">Bảo hành</span><span class="spec-val"><?php echo esc_html($specs['bao_hanh']); ?></span></div>
                                <?php if (!empty($specs['extra_specs'])): ?>
                                    <?php foreach ($specs['extra_specs'] as $es): ?>
                                        <div class="spec-row"><span class="spec-lbl"><?php echo esc_html($es['spec_name'] ?? ''); ?></span><span class="spec-val"><?php echo esc_html($es['spec_value'] ?? ''); ?></span></div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- PHẦN 3: ĐÁNH GIÁ KHÁCH HÀNG (Tóm tắt tại Tab 1) -->
                    <div class="desc-reviews-block">
                        <div class="gobike-block-header">
                            <div class="header-left">
                                <h2 class="block-title">ĐÁNH GIÁ KHÁCH HÀNG</h2>
                            </div>
                            <div class="header-right">
                                <a href="#tab-reviews" class="view-all-link">
                                    Xem tất cả đánh giá <span class="arr">➔</span>
                                </a>
                            </div>
                        </div>

                        <!-- Lưới 5 ô: 1 ô tổng điểm + các thẻ đánh giá nổi bật -->
                        <div class="desc-reviews-5col-grid">
                            <div class="desc-score-summary-card">
                                <div class="big-score"><?php echo esc_html($rating_score); ?><span>/5</span></div>
                                <div class="score-stars">★★★★★</div>
                                <span class="score-total-txt"><?php echo esc_html($total_text); ?></span>
                                <div class="summary-progress-bars">
                                    <div class="star-bar-item"><span class="bar-lbl">5 sao</span><div class="bar-track"><div class="bar-fill" style="width: <?php echo esc_attr($s5); ?>%;"></div></div><span class="bar-percent"><?php echo esc_html($s5); ?>%</span></div>
                                    <div class="star-bar-item"><span class="bar-lbl">4 sao</span><div class="bar-track"><div class="bar-fill" style="width: <?php echo esc_attr($s4); ?>%;"></div></div><span class="bar-percent"><?php echo esc_html($s4); ?>%</span></div>
                                    <div class="star-bar-item"><span class="bar-lbl">3 sao</span><div class="bar-track"><div class="bar-fill" style="width: <?php echo esc_attr($s3); ?>%;"></div></div><span class="bar-percent"><?php echo esc_html($s3); ?>%</span></div>
                                    <div class="star-bar-item"><span class="bar-lbl">2 sao</span><div class="bar-track"><div class="bar-fill" style="width: <?php echo esc_attr($s2); ?>%;"></div></div><span class="bar-percent"><?php echo esc_html($s2); ?>%</span></div>
                                    <div class="star-bar-item"><span class="bar-lbl">1 sao</span><div class="bar-track"><div class="bar-fill" style="width: <?php echo esc_attr($s1); ?>%;"></div></div><span class="bar-percent"><?php echo esc_html($s1); ?>%</span></div>
                                </div>
                            </div>

                            <?php if (!empty($featured_reviews)): ?>
                                <?php foreach ($featured_reviews as $rev): ?>
                                    <div class="review-card-item">
                                        <div class="card-author-row">
                                            <?php if (!empty($rev['avatar'])): ?>
                                                <img src="<?php echo esc_url($rev['avatar']); ?>" alt="<?php echo esc_attr($rev['name'] ?? ''); ?>" class="author-avatar" />
                                            <?php else: ?>
                                                <div class="author-avatar-placeholder"><?php echo esc_html(mb_substr($rev['name'] ?? 'K', 0, 1)); ?></div>
                                            <?php endif; ?>
                                            <div class="author-meta">
                                                <strong class="author-name"><?php echo esc_html($rev['name'] ?? 'Khách hàng'); ?></strong>
                                                <?php if (!empty($rev['date'])): ?>
                                                    <span class="review-date"><?php echo esc_html($rev['date']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="review-stars-val">
                                            <?php
                                            $st = intval($rev['stars'] ?? 5);
                                            echo str_repeat('★', max(1, min(5, $st)));
                                            ?>
                                        </div>
                                        <p class="review-comment"><?php echo esc_html($rev['comment'] ?? ''); ?></p>
                                        <?php if (!empty($rev['photo_1']) || !empty($rev['photo_2'])): ?>
                                            <div class="review-attached-imgs">
                                                <?php if (!empty($rev['photo_1'])): ?>
                                                    <img src="<?php echo esc_url($rev['photo_1']); ?>" alt="Review photo 1" />
                                                <?php endif; ?>
                                                <?php if (!empty($rev['photo_2'])): ?>
                                                    <img src="<?php echo esc_url($rev['photo_2']); ?>" alt="Review photo 2" />
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- PHẦN 4: HỎI ĐÁP (Tóm tắt tại Tab 1) -->
                    <div class="desc-faq-block">
                        <div class="gobike-block-header">
                            <div class="header-left">
                                <h2 class="block-title">HỎI ĐÁP</h2>
                                <span class="block-slogan"><?php echo esc_html($faq_slogan); ?></span>
                            </div>
                            <div class="header-right">
                                <a href="#tab-faq" class="view-all-link">
                                    Xem tất cả câu hỏi <span class="arr">➔</span>
                                </a>
                                <a href="<?php echo esc_url($faq_zalo); ?>" target="_blank" rel="nofollow" class="btn-ask-question">Đặt câu hỏi</a>
                            </div>
                        </div>

                        <div class="faq-accordion-grid">
                            <div class="faq-col">
                                <?php foreach ($faqs_left as $item): ?>
                                    <div class="faq-item">
                                        <div class="faq-question">
                                            <span><?php echo esc_html($item['question'] ?? ''); ?></span>
                                            <span class="faq-icon">+</span>
                                        </div>
                                        <div class="faq-answer">
                                            <p><?php echo esc_html($item['answer'] ?? ''); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="faq-col">
                                <?php foreach ($faqs_right as $item): ?>
                                    <div class="faq-item">
                                        <div class="faq-question">
                                            <span><?php echo esc_html($item['question'] ?? ''); ?></span>
                                            <span class="faq-icon">+</span>
                                        </div>
                                        <div class="faq-answer">
                                            <p><?php echo esc_html($item['answer'] ?? ''); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: THÔNG SỐ KỸ THUẬT (Dedicated Full Panel) -->
            <div class="gobike-tab-panel" id="tab-specifications">
                <div class="gobike-specs-container">
                    <div class="gobike-block-header">
                        <div class="header-left">
                            <h2 class="block-title">THÔNG SỐ KỸ THUẬT CHI TIẾT</h2>
                        </div>
                    </div>
                    <div class="specs-table-grid">
                        <div class="specs-col">
                            <div class="spec-row"><span class="spec-lbl">Thương hiệu</span><span class="spec-val"><?php echo esc_html($specs['brand']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Model</span><span class="spec-val"><?php echo esc_html($product->get_name()); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Loại xe</span><span class="spec-val"><?php echo esc_html($specs['cat_name']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Động cơ</span><span class="spec-val"><?php echo esc_html($specs['dong_co']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Pin</span><span class="spec-val"><?php echo esc_html($specs['pin']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Quãng đường</span><span class="spec-val"><?php echo esc_html($specs['quang_duong']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Tốc độ tối đa</span><span class="spec-val"><?php echo esc_html($specs['toc_do']); ?></span></div>
                        </div>
                        <div class="specs-col">
                            <div class="spec-row"><span class="spec-lbl">Khung xe</span><span class="spec-val"><?php echo esc_html($specs['khung_xe']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Phanh</span><span class="spec-val"><?php echo esc_html($specs['phanh']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Giảm xóc</span><span class="spec-val"><?php echo esc_html($specs['giam_xoc']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Lốp xe</span><span class="spec-val"><?php echo esc_html($specs['lop_xe']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Trọng lượng</span><span class="spec-val"><?php echo esc_html($specs['trong_luong']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Tải trọng tối đa</span><span class="spec-val"><?php echo esc_html($specs['tai_trong']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Kích thước (DxRxC)</span><span class="spec-val"><?php echo esc_html($specs['kich_thuoc']); ?></span></div>
                            <div class="spec-row"><span class="spec-lbl">Bảo hành</span><span class="spec-val"><?php echo esc_html($specs['bao_hanh']); ?></span></div>
                            <?php if (!empty($specs['extra_specs'])): ?>
                                <?php foreach ($specs['extra_specs'] as $es): ?>
                                    <div class="spec-row"><span class="spec-lbl"><?php echo esc_html($es['spec_name'] ?? ''); ?></span><span class="spec-val"><?php echo esc_html($es['spec_value'] ?? ''); ?></span></div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: HÌNH ẢNH & VIDEO CHI TIẾT (Dedicated Full Panel) -->
            <div class="gobike-tab-panel" id="tab-media">
                <div class="gobike-media-container">
                    <div class="gobike-block-header">
                        <div class="header-left">
                            <h2 class="block-title"><?php echo esc_html($media_title); ?></h2>
                        </div>
                    </div>

                    <?php if (!empty($detail_photos)): ?>
                        <div class="media-photo-grid">
                            <?php foreach ($detail_photos as $dp): ?>
                                <div class="photo-card-item">
                                    <img src="<?php echo esc_url($dp['image'] ?? ''); ?>" alt="<?php echo esc_attr($dp['caption'] ?? ''); ?>" loading="lazy" />
                                    <?php if (!empty($dp['caption'])): ?>
                                        <span class="photo-caption"><?php echo esc_html($dp['caption']); ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($video_embed_url)): ?>
                        <div class="media-video-embed-box">
                            <h4 class="video-embed-title"><?php echo esc_html($video_title); ?></h4>
                            <div class="video-responsive-wrap">
                                <iframe width="100%" height="480" src="<?php echo esc_url($video_embed_url); ?>" title="<?php echo esc_attr($video_title); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TAB 4: ĐÁNH GIÁ (Dedicated Full Panel) -->
            <div class="gobike-tab-panel" id="tab-reviews">
                <div class="gobike-reviews-container">
                    <div class="gobike-block-header">
                        <div class="header-left">
                            <h2 class="block-title">ĐÁNH GIÁ KHÁCH HÀNG</h2>
                        </div>
                    </div>

                    <!-- Box Tổng Điểm & Thanh Tiến Độ Sao -->
                    <div class="reviews-summary-row">
                        <div class="summary-score-box">
                            <div class="big-score"><?php echo esc_html($rating_score); ?><span>/5</span></div>
                            <div class="score-stars">★★★★★</div>
                            <span class="score-total-txt"><?php echo esc_html($total_text); ?></span>
                        </div>

                        <div class="summary-progress-bars">
                            <div class="star-bar-item"><span class="bar-lbl">5 sao</span><div class="bar-track"><div class="bar-fill" style="width: <?php echo esc_attr($s5); ?>%;"></div></div><span class="bar-percent"><?php echo esc_html($s5); ?>%</span></div>
                            <div class="star-bar-item"><span class="bar-lbl">4 sao</span><div class="bar-track"><div class="bar-fill" style="width: <?php echo esc_attr($s4); ?>%;"></div></div><span class="bar-percent"><?php echo esc_html($s4); ?>%</span></div>
                            <div class="star-bar-item"><span class="bar-lbl">3 sao</span><div class="bar-track"><div class="bar-fill" style="width: <?php echo esc_attr($s3); ?>%;"></div></div><span class="bar-percent"><?php echo esc_html($s3); ?>%</span></div>
                            <div class="star-bar-item"><span class="bar-lbl">2 sao</span><div class="bar-track"><div class="bar-fill" style="width: <?php echo esc_attr($s2); ?>%;"></div></div><span class="bar-percent"><?php echo esc_html($s2); ?>%</span></div>
                            <div class="star-bar-item"><span class="bar-lbl">1 sao</span><div class="bar-track"><div class="bar-fill" style="width: <?php echo esc_attr($s1); ?>%;"></div></div><span class="bar-percent"><?php echo esc_html($s1); ?>%</span></div>
                        </div>
                    </div>

                    <!-- Form Đánh Giá & Danh Sách Bình Luận Chuẩn WooCommerce -->
                    <div class="gobike-woocommerce-reviews-wrap">
                        <?php comments_template(); ?>
                    </div>
                </div>
            </div>

            <!-- TAB 5: HỎI ĐÁP (Dedicated Full Panel) -->
            <div class="gobike-tab-panel" id="tab-faq">
                <div class="gobike-faq-container">
                    <div class="gobike-block-header">
                        <div class="header-left">
                            <h2 class="block-title">HỎI ĐÁP</h2>
                            <span class="block-slogan"><?php echo esc_html($faq_slogan); ?></span>
                        </div>
                        <div class="header-right">
                            <a href="<?php echo esc_url($faq_zalo); ?>" target="_blank" rel="nofollow" class="btn-ask-question">Đặt câu hỏi</a>
                        </div>
                    </div>

                    <div class="faq-accordion-grid">
                        <div class="faq-col">
                            <?php foreach ($faqs_left as $item): ?>
                                <div class="faq-item">
                                    <div class="faq-question">
                                        <span><?php echo esc_html($item['question'] ?? ''); ?></span>
                                        <span class="faq-icon">+</span>
                                    </div>
                                    <div class="faq-answer">
                                        <p><?php echo esc_html($item['answer'] ?? ''); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="faq-col">
                            <?php foreach ($faqs_right as $item): ?>
                                <div class="faq-item">
                                    <div class="faq-question">
                                        <span><?php echo esc_html($item['question'] ?? ''); ?></span>
                                        <span class="faq-icon">+</span>
                                    </div>
                                    <div class="faq-answer">
                                        <p><?php echo esc_html($item['answer'] ?? ''); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="faq-footer-link">
                        <a href="<?php echo esc_url($faq_zalo); ?>" target="_blank" rel="nofollow" class="link-see-all-faqs">Xem tất cả câu hỏi <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg></a>
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
            <!-- Header Sản Phẩm Liên Quan (Chuẩn gobike-block-header - Ảnh 2) -->
            <div class="gobike-block-header">
                <div class="header-left">
                    <h2 class="block-title">SẢN PHẨM LIÊN QUAN</h2>
                </div>
                <div class="header-right">
                    <a href="<?php echo esc_url(home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/')); ?>" class="view-all-link">
                        Xem tất cả <span class="arr">➔</span>
                    </a>
                </div>
            </div>

            <!-- Khung Slide Sản Phẩm Liên Quan (Nút chuyển slide 2 bên) -->
            <div class="rel-slider-wrapper">
                <button type="button" class="rel-btn-side rel-btn-prev-side" aria-label="Trước">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>

                <div class="swiper-container gobike-related-carousel-swiper">
                    <div class="swiper-wrapper">
                        <?php if ($related_query->have_posts()): ?>
                            <?php while ($related_query->have_posts()): $related_query->the_post(); 
                                $rel_product = wc_get_product(get_the_ID());
                                if (!$rel_product) continue;
                                $regular_price = $rel_product->get_regular_price();
                                $sale_price = $rel_product->get_price();
                                $discount_badge = '';
                                if ($rel_product->is_on_sale() && $regular_price > $sale_price && $regular_price > 0) {
                                    $discount_badge = '-' . round((($regular_price - $sale_price) / $regular_price) * 100) . '%';
                                }
                            ?>
                                <div class="swiper-slide rel-product-slide">
                                    <div class="rel-card-box">
                                        <!-- Khung ảnh dùng padding-bottom thay cho width/height cố định -->
                                        <div class="rel-card-image">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php echo $rel_product->get_image('woocommerce_thumbnail'); ?>
                                            </a>
                                            <?php if ($discount_badge): ?>
                                                <span class="rel-badge-sale"><?php echo esc_html($discount_badge); ?></span>
                                            <?php endif; ?>
                                            <button type="button" class="rel-btn-wishlist" title="Yêu thích">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                                            </button>
                                        </div>
                                        <div class="rel-card-info">
                                            <h4 class="rel-product-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                            <div class="rel-price-wrap">
                                                <span class="rel-price-current"><?php echo wc_price($sale_price); ?></span>
                                                <?php if ($regular_price > $sale_price): ?>
                                                    <span class="rel-price-old"><?php echo wc_price($regular_price); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="rel-card-actions">
                                                <a href="?add-to-cart=<?php echo get_the_ID(); ?>" class="btn-rel-cart" data-quantity="1" data-product_id="<?php echo get_the_ID(); ?>">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                                                    <span>Thêm vào giỏ</span>
                                                </a>
                                                <a href="<?php the_permalink(); ?>" class="btn-rel-view">Xem chi tiết</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; wp_reset_postdata(); ?>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="button" class="rel-btn-side rel-btn-next-side" aria-label="Sau">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1e293b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>

            <!-- Dải 5 Cam Kết Dịch Vụ Chân Trang Chuẩn Mẫu (Ảnh 3) -->
            <div class="gobike-footer-trust-strip">
                <div class="footer-trust-item">
                    <div class="trust-icon-box">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <polyline points="9 12 11 14 15 10"/>
                        </svg>
                    </div>
                    <div class="trust-text-box">
                        <strong>Sản phẩm chính hãng</strong>
                        <span>Cam kết 100% chính hãng</span>
                    </div>
                </div>

                <div class="footer-trust-item">
                    <div class="trust-icon-box">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="3" width="15" height="13"/>
                            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                            <circle cx="5.5" cy="18.5" r="2.5"/>
                            <circle cx="18.5" cy="18.5" r="2.5"/>
                        </svg>
                    </div>
                    <div class="trust-text-box">
                        <strong>Giao hàng toàn quốc</strong>
                        <span>Nhanh chóng, an toàn</span>
                    </div>
                </div>

                <div class="footer-trust-item">
                    <div class="trust-icon-box">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                            <line x1="6" y1="18" x2="3" y2="21"/>
                        </svg>
                    </div>
                    <div class="trust-text-box">
                        <strong>Lắp ráp miễn phí</strong>
                        <span>Tại nhà trên toàn quốc</span>
                    </div>
                </div>

                <div class="footer-trust-item">
                    <div class="trust-icon-box">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="14" rx="2"/>
                            <line x1="2" y1="10" x2="22" y2="10"/>
                            <circle cx="6" cy="15" r="1"/>
                        </svg>
                    </div>
                    <div class="trust-text-box">
                        <strong>Trả góp 0%</strong>
                        <span>Thủ tục đơn giản, nhanh chóng</span>
                    </div>
                </div>

                <div class="footer-trust-item">
                    <div class="trust-icon-box">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 18v-6a9 9 0 0 1 18 0v6"/>
                            <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>
                        </svg>
                    </div>
                    <div class="trust-text-box">
                        <strong>Tư vấn 24/7</strong>
                        <span>0925 568 566</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
}
