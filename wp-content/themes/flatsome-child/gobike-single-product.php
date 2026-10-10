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
    if (!$pin) $pin = ($gf ? get_field('dung_luong_pin', $product_id) : '') ?: '';

    $dong_co = $product->get_attribute('pa_dong-co');
    if (!$dong_co) $dong_co = ($gf ? get_field('dong_co', $product_id) : '') ?: '';

    $quang_duong = $product->get_attribute('pa_quang-duong');
    if (!$quang_duong) $quang_duong = ($gf ? get_field('quang_duong', $product_id) : '') ?: '';

    $toc_do = $product->get_attribute('pa_toc-do');
    if (!$toc_do) $toc_do = ($gf ? get_field('toc_do_toi_da', $product_id) : '') ?: '';

    $lop_xe = $product->get_attribute('pa_kich-thuoc-lop');
    if (!$lop_xe) $lop_xe = ($gf ? get_field('kich_thuoc_lop', $product_id) : '') ?: '';

    $trong_luong = $product->get_weight() ? $product->get_weight() . ' kg' : (($gf ? get_field('trong_luong', $product_id) : '') ?: '');
    $bao_hanh = ($gf ? get_field('thoi_gian_bao_hanh', $product_id) : '') ?: '';

    // Thương hiệu
    $brand_terms = get_the_terms($product_id, 'pa_thuong-hieu');
    $brand = '';
    if (!empty($brand_terms) && !is_wp_error($brand_terms)) {
        $brand = $brand_terms[0]->name;
    } else {
        $brand_acf = $gf ? get_field('thuong_hieu', $product_id) : '';
        if ($brand_acf) $brand = $brand_acf;
    }

    // Danh mục chính (Lọc bỏ các danh mục tiện ích không liên quan)
    $cats = get_the_terms($product_id, 'product_cat');
    $cat_name = '';
    $cat_link = '';
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
        'khung_xe'       => ($gf ? get_field('chat_lieu_khung', $product_id) : '') ?: '',
        'phanh'          => ($gf ? get_field('he_thong_phanh', $product_id) : '') ?: '',
        'giam_xoc'       => ($gf ? get_field('giam_xoc', $product_id) : '') ?: '',
        'tai_trong'      => ($gf ? get_field('tai_trong_toi_da', $product_id) : '') ?: '',
        'kich_thuoc'     => ($gf ? get_field('kich_thuoc_xe', $product_id) : '') ?: '',
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
    $video_url = get_field('video_url', $product_id) ?: '';
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

        <!-- Dải Thông Số Nhanh Dưới Gallery (Chỉ hiển thị khi có thông số thật) -->
        <?php
        $quick_badges = array();

        if (!empty($specs['quang_duong'])) {
            $qd_raw = $specs['quang_duong'];
            $qd_display = trim(explode('(', $qd_raw)[0]);
            if (!str_contains(strtolower($qd_display), 'km')) {
                $qd_display .= ' km';
            }
            $quick_badges[] = array(
                'val'   => $qd_display,
                'label' => 'Quãng đường trợ lực',
                'icon'  => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
            );
        }

        if (!empty($specs['dong_co'])) {
            $dc_raw = $specs['dong_co'];
            $dc_display = trim(explode('–', $dc_raw)[0]);
            $quick_badges[] = array(
                'val'   => $dc_display,
                'label' => 'Động cơ mạnh mẽ',
                'icon'  => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
            );
        }

        if (!empty($specs['pin'])) {
            $pin_raw = $specs['pin'];
            $pin_display = trim(explode('(', $pin_raw)[0]);
            $quick_badges[] = array(
                'val'   => $pin_display,
                'label' => 'Pin Lithium cao cấp',
                'icon'  => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="6" width="18" height="12" rx="2"/><line x1="23" y1="13" x2="23" y2="11"/></svg>',
            );
        }

        if (!empty($specs['lop_xe'])) {
            $lop_raw = $specs['lop_xe'];
            $lop_display = trim(explode('–', $lop_raw)[0]);
            $quick_badges[] = array(
                'val'   => $lop_display,
                'label' => 'Lốp xe đa địa hình',
                'icon'  => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>',
            );
        }
        ?>
        <?php if (!empty($quick_badges)): ?>
            <div class="gobike-quick-spec-badges">
                <?php foreach ($quick_badges as $badge): ?>
                    <div class="spec-badge-item">
                        <div class="badge-icon">
                            <?php echo $badge['icon']; ?>
                        </div>
                        <div class="badge-info">
                            <strong class="badge-val"><?php echo esc_html($badge['val']); ?></strong>
                            <span class="badge-label"><?php echo esc_html($badge['label']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
                <?php if (!empty($specs['brand'])): ?>
                    <span class="brand-tag-badge">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="#b91c1c"><polygon points="12 2 2 22 22 22 12 2"/></svg>
                        <strong><?php echo esc_html($specs['brand']); ?></strong>
                    </span>
                <?php endif; ?>
                <?php if (!empty($specs['cat_name'])): ?>
                    <a href="<?php echo esc_url($specs['cat_link'] ?: '#'); ?>" class="cat-tag-badge">
                        <?php echo esc_html($specs['cat_name']); ?>
                    </a>
                <?php endif; ?>
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
        <?php 
        $slogan = get_field('slogan_san_pham', $product_id) ?: '';
        if (!empty($slogan)): 
        ?>
            <p class="gobike-product-subtitle"><?php echo esc_html($slogan); ?></p>
        <?php endif; ?>

        <!-- Hàng 3: Đánh giá sao & Số lượng đã bán (Thật từ WooCommerce) -->
        <?php 
        $rating_count = (int) $product->get_rating_count();
        $average_rating = (float) $product->get_average_rating();
        $total_sales = (int) $product->get_total_sales();
        ?>
        <?php if ($rating_count > 0 || $total_sales > 0): ?>
            <div class="gobike-rating-sales-row">
                <?php if ($rating_count > 0): ?>
                    <div class="rating-stars-box">
                        <span class="star-icon">★</span>
                        <strong class="rating-num"><?php echo number_format($average_rating, 1); ?></strong>
                        <a href="#tab-reviews" class="rating-count-link">(<?php echo esc_html($rating_count); ?> đánh giá)</a>
                    </div>
                <?php endif; ?>
                <?php if ($rating_count > 0 && $total_sales > 0): ?>
                    <span class="divider-dot">•</span>
                <?php endif; ?>
                <?php if ($total_sales > 0): ?>
                    <div class="sales-count-box">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        <span>Đã bán <strong><?php echo esc_html($total_sales); ?></strong></span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

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
        if (!empty($excerpt)): 
        ?>
            <div class="gobike-short-desc">
                <?php echo wp_kses_post($excerpt); ?>
            </div>
        <?php endif; ?>

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

    // Đánh giá: Lấy trực tiếp từ YITH Advanced Reviews hoặc WooCommerce chuẩn
    $review_count = 0;
    if (function_exists('YITH_YWAR')) {
        $review_count = (int) YITH_YWAR()->get_reviews_count($product_id);
    } else {
        $review_count = (int) $product->get_review_count();
    }

    // Thống kê đánh giá động phục vụ hiển thị
    $avg_rating = (float) $product->get_average_rating();
    $rating_score = $avg_rating > 0 ? number_format($avg_rating, 1) : '5.0';
    $total_text = 'Dựa trên ' . $review_count . ' đánh giá';

    $rating_counts = $product->get_rating_counts();
    $total_voted = array_sum($rating_counts);
    if ($total_voted > 0) {
        $s5 = round((($rating_counts[5] ?? 0) / $total_voted) * 100);
        $s4 = round((($rating_counts[4] ?? 0) / $total_voted) * 100);
        $s3 = round((($rating_counts[3] ?? 0) / $total_voted) * 100);
        $s2 = round((($rating_counts[2] ?? 0) / $total_voted) * 100);
        $s1 = round((($rating_counts[1] ?? 0) / $total_voted) * 100);
    } else {
        $s5 = 0; $s4 = 0; $s3 = 0; $s2 = 0; $s1 = 0;
    }

    // Danh sách đánh giá thực tế đã duyệt
    $latest_reviews = get_comments(array(
        'post_id' => $product_id,
        'status'  => 'approve',
        'number'  => 4,
    ));

    // --- TAB 1: Dữ liệu Mô tả & Banners ---
    $desc_subtitle = $gf ? get_field('sp_desc_subtitle', $product_id) : '';

    $sp_features = $gf ? get_field('sp_features', $product_id) : array();
    if (!is_array($sp_features)) {
        $sp_features = array();
    }
    $sp_features = array_filter($sp_features, function($item) {
        return !empty($item['title']) || !empty($item['desc']);
    });

    $hero_img = $gf ? get_field('sp_lifestyle_hero_img', $product_id) : '';
    $hero_quote = $gf ? get_field('sp_lifestyle_hero_quote', $product_id) : '';

    $lifestyle_cards = $gf ? get_field('sp_lifestyle_cards', $product_id) : array();
    if (!is_array($lifestyle_cards)) {
        $lifestyle_cards = array();
    }
    $lifestyle_cards = array_filter($lifestyle_cards, function($item) {
        return !empty($item['image']) || !empty($item['title']);
    });

    // --- TAB 2: Dữ liệu Thông số Kỹ thuật ---
    $specs_custom_html = $gf ? get_field('sp_specs_custom_html', $product_id) : '';

    $specs_items_left = array();
    if (!empty($specs['brand']))       $specs_items_left[] = array('Thương hiệu', $specs['brand']);
    if (!empty($product->get_name()))  $specs_items_left[] = array('Model', $product->get_name());
    if (!empty($specs['cat_name']))    $specs_items_left[] = array('Loại xe', $specs['cat_name']);
    if (!empty($specs['dong_co']))     $specs_items_left[] = array('Động cơ', $specs['dong_co']);
    if (!empty($specs['pin']))         $specs_items_left[] = array('Pin', $specs['pin']);
    if (!empty($specs['quang_duong'])) $specs_items_left[] = array('Quãng đường', $specs['quang_duong']);
    if (!empty($specs['toc_do']))      $specs_items_left[] = array('Tốc độ tối đa', $specs['toc_do']);

    $specs_items_right = array();
    if (!empty($specs['khung_xe']))    $specs_items_right[] = array('Khung xe', $specs['khung_xe']);
    if (!empty($specs['phanh']))       $specs_items_right[] = array('Phanh', $specs['phanh']);
    if (!empty($specs['giam_xoc']))    $specs_items_right[] = array('Giảm xóc', $specs['giam_xoc']);
    if (!empty($specs['lop_xe']))      $specs_items_right[] = array('Lốp xe', $specs['lop_xe']);
    if (!empty($specs['trong_luong'])) $specs_items_right[] = array('Trọng lượng', $specs['trong_luong']);
    if (!empty($specs['tai_trong']))   $specs_items_right[] = array('Tải trọng tối đa', $specs['tai_trong']);
    if (!empty($specs['kich_thuoc']))  $specs_items_right[] = array('Kích thước (DxRxC)', $specs['kich_thuoc']);
    if (!empty($specs['bao_hanh']))    $specs_items_right[] = array('Bảo hành', $specs['bao_hanh']);
    if (!empty($specs['extra_specs'])) {
        foreach ($specs['extra_specs'] as $es) {
            if (!empty($es['spec_name']) && !empty($es['spec_value'])) {
                $specs_items_right[] = array($es['spec_name'], $es['spec_value']);
            }
        }
    }
    $has_specs = (!empty($specs_items_left) || !empty($specs_items_right));

    // --- TAB 3: Dữ liệu Hình ảnh & Video ---
    $media_title = $gf ? get_field('sp_media_photos_title', $product_id) : '';
    if (empty($media_title)) $media_title = 'HÌNH ẢNH CHI TIẾT BỘ PHẬN';

    $detail_photos = $gf ? get_field('sp_detail_photos', $product_id) : array();
    if (empty($detail_photos) || !is_array($detail_photos)) {
        // Fallback sang gallery ảnh thật của WooCommerce nếu có
        $gallery_ids = $product->get_gallery_image_ids();
        if (!empty($gallery_ids)) {
            $detail_photos = array();
            $idx = 0;
            foreach ($gallery_ids as $gid) {
                $img_url = wp_get_attachment_image_url($gid, 'large');
                if ($img_url) {
                    $alt = get_post_meta($gid, '_wp_attachment_image_alt', true) ?: ('Ảnh ' . $product->get_name());
                    $detail_photos[] = array(
                        'image'   => $img_url,
                        'caption' => $alt,
                    );
                    $idx++;
                    if ($idx >= 10) break;
                }
            }
        } else {
            $detail_photos = array();
        }
    }

    $video_title = $gf ? get_field('sp_video_title', $product_id) : '';
    if (empty($video_title)) $video_title = 'VIDEO TRẢI NGHIỆM THỰC TẾ';

    // Thu thập danh sách Video cho Slider
    $all_videos = array();

    // 1. Từ repeater sp_detail_videos
    $detail_videos = $gf ? get_field('sp_detail_videos', $product_id) : array();
    if (!empty($detail_videos) && is_array($detail_videos)) {
        foreach ($detail_videos as $dv) {
            $v_u = trim($dv['video_url'] ?? '');
            if (!empty($v_u)) {
                $yt_id = '';
                if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/))([\w-]{11})/', $v_u, $m)) {
                    $yt_id = $m[1];
                }
                $thumb = !empty($dv['video_thumb']) ? $dv['video_thumb'] : ($yt_id ? "https://img.youtube.com/vi/{$yt_id}/hqdefault.jpg" : '');
                $all_videos[] = array(
                    'url'   => $v_u,
                    'title' => !empty($dv['video_title']) ? $dv['video_title'] : ('Trải nghiệm ' . $product->get_name()),
                    'thumb' => $thumb,
                );
            }
        }
    }

    // 2. Video đơn lẻ sp_video_url hoặc product_video_url
    $single_v_url = ($gf ? get_field('sp_video_url', $product_id) : '') ?: ($gf ? get_field('product_video_url', $product_id) : '');
    if (!empty($single_v_url)) {
        $already = false;
        foreach ($all_videos as $av) {
            if ($av['url'] === $single_v_url) { $already = true; break; }
        }
        if (!$already) {
            $yt_id = '';
            if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/))([\w-]{11})/', $single_v_url, $m)) {
                $yt_id = $m[1];
            }
            array_unshift($all_videos, array(
                'url'   => $single_v_url,
                'title' => 'Video review ' . $product->get_name(),
                'thumb' => $yt_id ? "https://img.youtube.com/vi/{$yt_id}/hqdefault.jpg" : '',
            ));
        }
    }

    // 3. CPT Video Review liên kết qua relationship
    $linked_revs = $gf ? get_field('product_linked_reviews', $product_id) : array();
    if (!empty($linked_revs) && is_array($linked_revs)) {
        foreach ($linked_revs as $r_id) {
            $r_url = $gf ? get_field('video_url', $r_id) : '';
            if ($r_url) {
                $yt_id = '';
                if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/))([\w-]{11})/', $r_url, $m)) {
                    $yt_id = $m[1];
                }
                $r_thumb = ($gf ? get_field('video_thumbnail', $r_id) : '') ?: ($yt_id ? "https://img.youtube.com/vi/{$yt_id}/hqdefault.jpg" : '');
                $all_videos[] = array(
                    'url'   => $r_url,
                    'title' => get_the_title($r_id),
                    'thumb' => $r_thumb,
                );
            }
        }
    }

    // 4. Nếu vẫn trống -> Tìm từ CPT video_review có related_product
    if (empty($all_videos)) {
        $cpt_v = get_posts(array(
            'post_type'      => 'video_review',
            'posts_per_page' => 4,
            'meta_key'       => 'related_product',
            'meta_value'     => $product_id,
        ));
        if (!empty($cpt_v)) {
            foreach ($cpt_v as $cv) {
                $r_url = $gf ? get_field('video_url', $cv->ID) : '';
                if ($r_url) {
                    $yt_id = '';
                    if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/))([\w-]{11})/', $r_url, $m)) {
                        $yt_id = $m[1];
                    }
                    $r_thumb = ($gf ? get_field('video_thumbnail', $cv->ID) : '') ?: ($yt_id ? "https://img.youtube.com/vi/{$yt_id}/hqdefault.jpg" : '');
                    $all_videos[] = array(
                        'url'   => $r_url,
                        'title' => get_the_title($cv->ID),
                        'thumb' => $r_thumb,
                    );
                }
            }
        }
    }

    // --- TAB 5: Dữ liệu Hỏi đáp ---
    $faq_slogan = $gf ? get_field('sp_faq_slogan', $product_id) : '';
    $faq_zalo = ($gf ? get_field('sp_faq_zalo_link', $product_id) : '') ?: 'https://zalo.me/0944988699';

    $faqs = $gf ? get_field('sp_faqs', $product_id) : array();
    if (!is_array($faqs)) {
        $faqs = array();
    }
    $faqs = array_filter($faqs, function($item) {
        return !empty($item['question']) || !empty($item['answer']);
    });
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
                        <a href="#tab-reviews">Đánh giá <span>(<?php echo esc_html($review_count); ?>)</span></a>
                    </li>
                    <li class="tab-nav-item" data-tab="tab-faq">
                        <a href="#tab-faq">Hỏi đáp <?php if ($faq_count > 0): ?><span>(<?php echo esc_html($faq_count); ?>)</span><?php endif; ?></a>
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
                            <?php 
                            $has_lifestyle = (!empty($hero_img) || !empty($lifestyle_cards));
                            $col_desc_class = $has_lifestyle ? 'col large-7 medium-12 small-12' : 'col large-12 medium-12 small-12';
                            ?>
                            <div class="row align-top desc-intro-row">
                                <!-- Cột Trái: Text mô tả + Icon tính năng nổi bật -->
                                <div class="<?php echo esc_attr($col_desc_class); ?>">
                                    <?php if (!empty($desc_subtitle)): ?>
                                        <h2 class="desc-heading-primary"><?php echo esc_html($desc_subtitle); ?></h2>
                                    <?php endif; ?>
                                    <div class="desc-main-text entry-content">
                                        <?php
                                        $content = get_the_content();
                                        if (empty($content)) {
                                            echo '<p>Nội dung mô tả chi tiết của sản phẩm đang được cập nhật.</p>';
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

                                <!-- Cột Phải: Hình ảnh Lifestyle & Banners (Chỉ hiển thị khi có dữ liệu thật) -->
                                <?php if ($has_lifestyle): ?>
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
                                <?php endif; ?>
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

                    <!-- PHẦN 2: THÔNG SỐ KỸ THUẬT (Tóm tắt tại Tab 1 - Chỉ hiển thị khi có thông số thật) -->
                    <?php if ($has_specs): ?>
                        <div class="desc-specs-block">
                            <div class="gobike-block-header">
                                <div class="header-left">
                                    <h2 class="block-title">THÔNG SỐ KỸ THUẬT</h2>
                                </div>
                            </div>
                            <div class="specs-table-grid">
                                <div class="specs-col">
                                    <?php foreach ($specs_items_left as $si): ?>
                                        <div class="spec-row"><span class="spec-lbl"><?php echo esc_html($si[0]); ?></span><span class="spec-val"><?php echo esc_html($si[1]); ?></span></div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="specs-col">
                                    <?php foreach ($specs_items_right as $si): ?>
                                        <div class="spec-row"><span class="spec-lbl"><?php echo esc_html($si[0]); ?></span><span class="spec-val"><?php echo esc_html($si[1]); ?></span></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- PHẦN 3: ĐÁNH GIÁ KHÁCH HÀNG (Tóm tắt tại Tab 1) -->
                    <div class="desc-reviews-block">
                        <div class="gobike-block-header align-center">
                            <div class="header-left">
                                <h2 class="block-title">ĐÁNH GIÁ KHÁCH HÀNG</h2>
                                <a href="#tab-reviews" class="btn-write-review-outline btn-write-review">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Viết đánh giá
                                </a>
                            </div>
                            <?php if ($review_count > 0): ?>
                                <div class="header-right">
                                    <a href="#tab-reviews" class="view-all-link">
                                        Xem tất cả đánh giá <span class="arr">➔</span>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($review_count > 0 && !empty($latest_reviews)): ?>
                            <!-- Khi CÓ đánh giá: Lưới 5 ô (1 ô tổng điểm + các thẻ đánh giá thực tế) -->
                            <div class="desc-reviews-5col-grid">
                                <div class="desc-score-summary-card">
                                    <div class="big-score"><?php echo esc_html($rating_score); ?><span>/5</span></div>
                                    <div class="score-stars">
                                        <?php
                                        $full_stars = round($avg_rating);
                                        echo str_repeat('★', max(1, min(5, $full_stars)));
                                        ?>
                                    </div>
                                    <span class="score-total-txt"><?php echo esc_html($total_text); ?></span>
                                    <div class="summary-progress-bars">
                                        <div class="star-bar-item"><span class="bar-lbl">5 sao</span><div class="bar-track"><div class="bar-fill" style="width: <?php echo esc_attr($s5); ?>%;"></div></div><span class="bar-percent"><?php echo esc_html($s5); ?>%</span></div>
                                        <div class="star-bar-item"><span class="bar-lbl">4 sao</span><div class="bar-track"><div class="bar-fill" style="width: <?php echo esc_attr($s4); ?>%;"></div></div><span class="bar-percent"><?php echo esc_html($s4); ?>%</span></div>
                                        <div class="star-bar-item"><span class="bar-lbl">3 sao</span><div class="bar-track"><div class="bar-fill" style="width: <?php echo esc_attr($s3); ?>%;"></div></div><span class="bar-percent"><?php echo esc_html($s3); ?>%</span></div>
                                        <div class="star-bar-item"><span class="bar-lbl">2 sao</span><div class="bar-track"><div class="bar-fill" style="width: <?php echo esc_attr($s2); ?>%;"></div></div><span class="bar-percent"><?php echo esc_html($s2); ?>%</span></div>
                                        <div class="star-bar-item"><span class="bar-lbl">1 sao</span><div class="bar-track"><div class="bar-fill" style="width: <?php echo esc_attr($s1); ?>%;"></div></div><span class="bar-percent"><?php echo esc_html($s1); ?>%</span></div>
                                    </div>
                                </div>

                                <?php foreach ($latest_reviews as $rev): 
                                    $st = intval(get_comment_meta($rev->comment_ID, 'rating', true) ?: 5);
                                    $avatar = get_avatar_url($rev->comment_author_email, array('size' => 80));
                                    $ywar_photos = get_comment_meta($rev->comment_ID, 'ywar_attachments', true);
                                    if (!is_array($ywar_photos)) $ywar_photos = array();
                                ?>
                                    <div class="review-card-item">
                                        <div class="card-author-row">
                                            <?php if (!empty($avatar)): ?>
                                                <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($rev->comment_author); ?>" class="author-avatar" />
                                            <?php else: ?>
                                                <div class="author-avatar-placeholder"><?php echo esc_html(mb_substr($rev->comment_author, 0, 1)); ?></div>
                                            <?php endif; ?>
                                            <div class="author-meta">
                                                <strong class="author-name"><?php echo esc_html($rev->comment_author); ?></strong>
                                                <span class="review-date"><?php echo esc_html(get_comment_date('d/m/Y', $rev)); ?></span>
                                            </div>
                                        </div>
                                        <div class="review-stars-val">
                                            <?php echo str_repeat('★', max(1, min(5, $st))); ?>
                                        </div>
                                        <p class="review-comment"><?php echo esc_html(wp_trim_words($rev->comment_content, 35)); ?></p>
                                        <?php if (!empty($ywar_photos)): ?>
                                            <div class="review-attached-imgs">
                                                <?php foreach (array_slice($ywar_photos, 0, 2) as $att_id): 
                                                    $img_u = wp_get_attachment_image_url($att_id, 'medium');
                                                    if ($img_u):
                                                ?>
                                                    <img src="<?php echo esc_url($img_u); ?>" alt="Review photo" />
                                                <?php endif; endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <!-- Khi CHƯA có đánh giá: Hiển thị thông báo thân thiện + Nút đánh giá ngay -->
                            <div class="desc-reviews-empty-card">
                                <div class="empty-card-inner">
                                    <div class="empty-icon-circle">
                                        <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                        </svg>
                                    </div>
                                    <div class="empty-text-wrap">
                                        <h4 class="empty-title">Chưa có đánh giá nào cho sản phẩm này</h4>
                                        <p class="empty-desc">Hãy là người đầu tiên trải nghiệm và chia sẻ cảm nhận thực tế về <strong><?php echo esc_html($product->get_name()); ?></strong> để nhận ưu đãi hấp dẫn từ GoBike!</p>
                                    </div>
                                    <div class="empty-action-wrap">
                                        <a href="#tab-reviews" class="btn-write-review-now btn-write-review">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                            Viết đánh giá ngay
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>


                    <!-- PHẦN 4: HỎI ĐÁP (Tóm tắt tại Tab 1 - Chỉ hiển thị khi có câu hỏi thật) -->
                    <?php if (!empty($faqs)): ?>
                        <div class="desc-faq-block">
                            <div class="gobike-block-header">
                                <div class="header-left">
                                    <h2 class="block-title">HỎI ĐÁP</h2>
                                    <?php if (!empty($faq_slogan)): ?>
                                        <span class="block-slogan"><?php echo esc_html($faq_slogan); ?></span>
                                    <?php endif; ?>
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
                    <?php endif; ?>
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
                    <?php if (!empty($specs_custom_html)): ?>
                        <!-- Người dùng copy HTML / Bảng vào editor ACF -->
                        <div class="specs-custom-content entry-content">
                            <?php echo $specs_custom_html; ?>
                        </div>
                    <?php elseif ($has_specs): ?>
                        <!-- Hiển thị bảng thông số chuẩn từ hệ thống -->
                        <div class="specs-table-grid">
                            <div class="specs-col">
                                <?php foreach ($specs_items_left as $si): ?>
                                    <div class="spec-row"><span class="spec-lbl"><?php echo esc_html($si[0]); ?></span><span class="spec-val"><?php echo esc_html($si[1]); ?></span></div>
                                <?php endforeach; ?>
                            </div>
                            <div class="specs-col">
                                <?php foreach ($specs_items_right as $si): ?>
                                    <div class="spec-row"><span class="spec-lbl"><?php echo esc_html($si[0]); ?></span><span class="spec-val"><?php echo esc_html($si[1]); ?></span></div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="specs-empty-notice" style="text-align: center; padding: 40px 20px; color: #64748b;">
                            <p>Thông số kỹ thuật chi tiết của sản phẩm đang được cập nhật.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TAB 3: HÌNH ẢNH & VIDEO CHI TIẾT (Dedicated Full Panel) -->
            <div class="gobike-tab-panel" id="tab-media">
                <div class="gobike-media-container">
                    <!-- 1. BỘ SƯU TẬP ẢNH BỘ PHẬN (SLIDER + LIGHTBOX PHÓNG TO) -->
                    <?php if (!empty($detail_photos)): ?>
                        <div class="media-photos-section">
                            <div class="gobike-block-header">
                                <div class="header-left">
                                    <h2 class="block-title"><?php echo esc_html($media_title); ?></h2>
                                </div>
                            </div>
                            <div class="media-photos-slider-wrap">
                                <div class="swiper-container gobike-media-photos-swiper">
                                    <div class="swiper-wrapper">
                                        <?php foreach ($detail_photos as $dp): ?>
                                            <div class="swiper-slide photo-card-item">
                                                <a href="<?php echo esc_url($dp['image'] ?? ''); ?>" class="gobike-photo-zoom" data-caption="<?php echo esc_attr($dp['caption'] ?? ''); ?>" title="<?php echo esc_attr($dp['caption'] ?? ''); ?>">
                                                    <img src="<?php echo esc_url($dp['image'] ?? ''); ?>" alt="<?php echo esc_attr($dp['caption'] ?? ''); ?>" loading="lazy" />
                                                    <span class="photo-zoom-icon" title="Nhấp để phóng to">
                                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
                                                    </span>
                                                </a>
                                                <?php if (!empty($dp['caption'])): ?>
                                                    <span class="photo-caption"><?php echo esc_html($dp['caption']); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <button type="button" class="media-btn-slide media-btn-prev gobike-photos-prev" aria-label="Trước">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                                </button>
                                <button type="button" class="media-btn-slide media-btn-next gobike-photos-next" aria-label="Sau">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 2. DANH SÁCH VIDEO TRẢI NGHIỆM XE (SLIDER + POPUP XEM TRỰC TIẾP) -->
                    <?php if (!empty($all_videos)): ?>
                        <div class="media-videos-section">
                            <div class="gobike-block-header">
                                <div class="header-left">
                                    <h2 class="block-title"><?php echo esc_html($video_title); ?></h2>
                                </div>
                            </div>
                            <div class="media-videos-slider-wrap">
                                <div class="swiper-container gobike-media-videos-swiper">
                                    <div class="swiper-wrapper">
                                        <?php foreach ($all_videos as $v): ?>
                                            <div class="swiper-slide video-slide-item">
                                                <div class="video-card-thumb btn-open-video" data-video="<?php echo esc_url($v['url']); ?>">
                                                    <img src="<?php echo esc_url($v['thumb']); ?>" alt="<?php echo esc_attr($v['title']); ?>" loading="lazy" />
                                                    <div class="video-play-btn-circle" title="Xem video">
                                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                                    </div>
                                                    <div class="video-overlay-gradient"></div>
                                                </div>
                                                <h4 class="video-slide-title btn-open-video" data-video="<?php echo esc_url($v['url']); ?>"><?php echo esc_html($v['title']); ?></h4>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <button type="button" class="media-btn-slide media-btn-prev gobike-videos-prev" aria-label="Trước">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                                </button>
                                <button type="button" class="media-btn-slide media-btn-next gobike-videos-next" aria-label="Sau">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($detail_photos) && empty($all_videos)): ?>
                        <div class="media-empty-notice" style="text-align: center; padding: 40px 20px; color: #64748b;">
                            <p>Hình ảnh chi tiết và video trải nghiệm đang được cập nhật.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TAB 4: ĐÁNH GIÁ (Dữ liệu động 100% từ WooCommerce & YITH Advanced Reviews) -->
            <div class="gobike-tab-panel" id="tab-reviews">
                <div class="gobike-reviews-container">
                    <!-- Form Đánh Giá & Danh Sách Bình Luận Động Chuẩn WooCommerce & YITH -->
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
                            <?php if (!empty($faq_slogan)): ?>
                                <span class="block-slogan"><?php echo esc_html($faq_slogan); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="header-right">
                            <a href="<?php echo esc_url($faq_zalo); ?>" target="_blank" rel="nofollow" class="btn-ask-question">Đặt câu hỏi</a>
                        </div>
                    </div>

                    <?php if (!empty($faqs)): ?>
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
                    <?php else: ?>
                        <div class="faq-empty-notice" style="text-align: center; padding: 40px 20px; color: #64748b;">
                            <p>Chưa có câu hỏi thường gặp nào cho sản phẩm này.</p>
                            <a href="<?php echo esc_url($faq_zalo); ?>" target="_blank" rel="nofollow" class="btn-ask-question" style="margin-top: 14px; display: inline-flex;">Đặt câu hỏi ngay</a>
                        </div>
                    <?php endif; ?>
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
