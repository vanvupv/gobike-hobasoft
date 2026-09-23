<?php
/**
 * The template for displaying all single posts (GoBike Theme)
 * 
 * Chuẩn thiết kế 100% theo ảnh mẫu:
 * - Section 1: Header, Sapo, Author bar, Featured Media, Table of Contents, Post Body & Nút Đọc tiếp/Thu gọn
 * - Section 2: Thẻ của bài viết (Tags)
 * - Section 3: Sản phẩm phù hợp (WooCommerce Product Grid)
 * - Section 4: Bài viết liên quan (Horizontal Cards)
 * - Section 5: Sidebar (Search, Bài viết nổi bật, Bài viết mới nhất, Hero Banner, Newsletter, Trust Badges)
 * 
 * @package Flatsome-Child
 */

get_header();

// Thiết lập dữ liệu post loop chuẩn WordPress
if (have_posts()) {
    the_post();
}

$current_id = get_the_ID();
$current_title = get_the_title();

// Cập nhật lượt xem
if (function_exists('gobike_set_post_views')) {
    gobike_set_post_views($current_id);
}

// 1. Chuyên mục & Ngày đăng
$categories = get_the_category($current_id);
$primary_cat = !empty($categories) ? $categories[0] : null;
$cat_name = $primary_cat ? $primary_cat->name : 'Kinh nghiệm';
$cat_url  = $primary_cat ? get_category_link($primary_cat->term_id) : '#';
$post_date = get_the_date('d/m/Y');

// 2. Sapo mở đầu
$post_excerpt = get_the_excerpt();
if (empty($post_excerpt)) {
    $post_excerpt = 'Miền Bắc Việt Nam sở hữu nhiều cung đường tuyệt đẹp, phù hợp cho cả người mới bắt đầu lẫn những tay đạp xe dày dặn kinh nghiệm. Cùng GoBike khám phá những cung đường đạp xe đáng trải nghiệm nhất.';
}

// 3. Tác giả & Chỉ số (Views, Reading Time)
$author_id   = get_the_author_meta('ID');
$author_name = get_the_author_meta('display_name') ?: 'Minh Hoàng';
$author_role = get_the_author_meta('user_description') ?: 'Người yêu xe đạp';
$author_avatar = get_avatar_url($author_id, array('size' => 88));
if (!$author_avatar || strpos($author_avatar, 'gravatar.com/avatar/?') !== false) {
    $author_avatar = 'https://secure.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=88&d=mm&r=g';
}

$post_views = function_exists('gobike_get_post_views') ? gobike_get_post_views($current_id) : '12.5K';
$reading_time = function_exists('gobike_estimate_reading_time') ? gobike_estimate_reading_time(get_the_content()) : 12;

// 4. Ảnh đại diện
$featured_img = get_the_post_thumbnail_url($current_id, 'full');
if (!$featured_img) {
    $featured_img = 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg';
}

// 5. Xử lý Mục lục tự động (Table of Contents) từ các thẻ <h2>
$content_raw = get_the_content();
$content_html = apply_filters('the_content', $content_raw);

$toc_items = array();
if (preg_match_all('/<h2(.*?)>(.*?)<\/h2>/i', $content_html, $matches, PREG_SET_ORDER)) {
    $index = 1;
    foreach ($matches as $match) {
        $raw_heading = strip_tags($match[2]);
        $heading_clean = preg_replace('/^\d+[\.\-\s]*/', '', $raw_heading);
        $anchor_id = 'muc-luc-' . $index;
        
        $toc_items[] = array(
            'id'    => $anchor_id,
            'title' => $raw_heading,
        );
        
        // Gán id anchor vào thẻ h2 trong content
        $new_h2 = '<h2 id="' . esc_attr($anchor_id) . '"' . $match[1] . '>' . $match[2] . '</h2>';
        $content_html = str_replace($match[0], $new_h2, $content_html);
        $index++;
    }
}

// Nếu bài viết chưa có h2 trong database, dùng mục lục & nội dung mẫu phong phú chuẩn ảnh 100%
$use_sample_content = (empty($toc_items) || strlen(strip_tags($content_raw)) < 150);
if ($use_sample_content) {
    $toc_items = array(
        array('id' => 'muc-luc-1', 'title' => 'Hồ Hòa Bình – Cung đường của thiên nhiên'),
        array('id' => 'muc-luc-2', 'title' => 'Mộc Châu – Cao nguyên trong lành'),
        array('id' => 'muc-luc-3', 'title' => 'Tam Đảo – Thử thách và chinh phục'),
        array('id' => 'muc-luc-4', 'title' => 'Mai Châu – Bình yên giữa núi rừng'),
        array('id' => 'muc-luc-5', 'title' => 'Hà Giang – Hành trình của những trái tim tự do'),
    );
}

// 6. Thẻ của bài viết (Tags)
$tags = get_the_tags($current_id);
if (empty($tags)) {
    $sample_tags = array('đạp xe', 'cung đường đẹp', 'du lịch', 'kinh nghiệm', 'sức khỏe');
}

// 7. Truy vấn 3 Sản phẩm phù hợp (WooCommerce)
$products_query = new WP_Query(array(
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => 3,
    'orderby'        => 'rand',
));

$related_products = array();
if ($products_query->have_posts()) {
    while ($products_query->have_posts()) {
        $products_query->the_post();
        $p_id = get_the_ID();
        $product = wc_get_product($p_id);
        if ($product) {
            $related_products[] = array(
                'id'         => $p_id,
                'title'      => get_the_title(),
                'url'        => get_permalink($p_id),
                'thumb'      => get_the_post_thumbnail_url($p_id, 'medium') ?: $featured_img,
                'cat'        => 'Xe đạp trợ lực điện',
                'badge'      => $product->is_on_sale() ? '-12%' : 'Bán chạy',
                'badge_type' => $product->is_on_sale() ? 'discount' : 'hot',
                'specs_km'   => '120km',
                'specs_kg'   => '27kg',
                'price_html' => $product->get_price_html(),
                'add_to_cart_url' => $product->add_to_cart_url(),
            );
        }
    }
    wp_reset_postdata();
}

// Fallback 3 sản phẩm mẫu nếu WooCommerce chưa có sản phẩm
if (count($related_products) < 3) {
    $fallback_prods = array(
        array(
            'id'         => 101,
            'title'      => 'PHOENIX M800',
            'url'        => home_url('/cua-hang/'),
            'thumb'      => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
            'cat'        => 'Xe đạp trợ lực địa hình',
            'badge'      => 'Bán chạy',
            'badge_type' => 'hot',
            'specs_km'   => '120km',
            'specs_kg'   => '27kg',
            'price_current' => '24.900.000đ',
            'price_old'     => '',
            'add_to_cart_url' => home_url('/cua-hang/'),
        ),
        array(
            'id'         => 102,
            'title'      => 'SHENGMILO S600',
            'url'        => home_url('/cua-hang/'),
            'thumb'      => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
            'cat'        => 'Xe đạp trợ lực leo dốc',
            'badge'      => '-12%',
            'badge_type' => 'discount',
            'specs_km'   => '100km',
            'specs_kg'   => '26kg',
            'price_current' => '26.500.000đ',
            'price_old'     => '30.000.000đ',
            'add_to_cart_url' => home_url('/cua-hang/'),
        ),
        array(
            'id'         => 103,
            'title'      => 'BURCHDA R5',
            'url'        => home_url('/cua-hang/'),
            'thumb'      => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
            'cat'        => 'Xe đạp trợ lực touring',
            'badge'      => '',
            'badge_type' => '',
            'specs_km'   => '150km',
            'specs_kg'   => '29kg',
            'price_current' => '22.800.000đ',
            'price_old'     => '',
            'add_to_cart_url' => home_url('/cua-hang/'),
        ),
    );
    $related_products = array_merge($related_products, array_slice($fallback_prods, count($related_products)));
}

// 8. Truy vấn 3 Bài viết liên quan
$related_query = new WP_Query(array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 3,
    'post__not_in'   => array($current_id),
    'orderby'        => 'date',
    'order'          => 'DESC',
));

$related_posts = array();
if ($related_query->have_posts()) {
    while ($related_query->have_posts()) {
        $related_query->the_post();
        $related_posts[] = array(
            'title' => get_the_title(),
            'url'   => get_permalink(),
            'date'  => get_the_date('d/m/Y'),
            'thumb' => get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: $featured_img,
        );
    }
    wp_reset_postdata();
}

// Fallback 3 bài viết liên quan nếu chưa đủ
if (count($related_posts) < 3) {
    $fallback_related = array(
        array(
            'title' => 'Kinh nghiệm du lịch Mộc Châu bằng xe đạp trợ lực',
            'url'   => home_url('/tin-tuc-cam-nang/'),
            'date'  => '14/09/2026',
            'thumb' => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
        ),
        array(
            'title' => 'Đạp xe mỗi ngày: 7 lợi ích bất ngờ cho sức khỏe',
            'url'   => home_url('/tin-tuc-cam-nang/'),
            'date'  => '10/09/2026',
            'thumb' => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
        ),
        array(
            'title' => 'So sánh xe đạp trợ lực và xe đạp thường chi tiết',
            'url'   => home_url('/tin-tuc-cam-nang/'),
            'date'  => '08/09/2026',
            'thumb' => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
        ),
    );
    $related_posts = array_merge($related_posts, array_slice($fallback_related, count($related_posts)));
}

// 9. Dữ liệu Sidebar: Bài viết nổi bật & Bài viết mới nhất
$popular_posts_query = new WP_Query(array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 3,
    'orderby'        => 'comment_count',
    'order'          => 'DESC',
));

$sidebar_popular_posts = array();
if ($popular_posts_query->have_posts()) {
    while ($popular_posts_query->have_posts()) {
        $popular_posts_query->the_post();
        $sidebar_popular_posts[] = array(
            'title' => get_the_title(),
            'url'   => get_permalink(),
            'date'  => get_the_date('d/m/Y'),
            'thumb' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') ?: $featured_img,
        );
    }
    wp_reset_postdata();
}

$latest_posts_query = new WP_Query(array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => 3,
    'post__not_in'   => array($current_id),
    'orderby'        => 'date',
    'order'          => 'DESC',
));

$sidebar_latest_posts = array();
if ($latest_posts_query->have_posts()) {
    while ($latest_posts_query->have_posts()) {
        $latest_posts_query->the_post();
        $sidebar_latest_posts[] = array(
            'title' => get_the_title(),
            'url'   => get_permalink(),
            'date'  => get_the_date('d/m/Y'),
            'thumb' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') ?: $featured_img,
        );
    }
    wp_reset_postdata();
}
?>

<div class="gobike-single-post-wrapper">
    <div class="gobike-single-container">
        
        <div class="gobike-single-grid">
            
            <!-- ================= CỘT TRÁI: NỘI DUNG CHÍNH (~68%) ================= -->
            <main class="gobike-single-main-col" id="mainPostContent">
                
                <!-- 1. SECTION 1: HEADER, SAPO, AUTHOR BAR -->
                <header class="post-header-section">
                    <div class="post-header-meta-top">
                        <a href="<?php echo esc_url($cat_url); ?>" class="post-cat-badge"><?php echo esc_html($cat_name); ?></a>
                        <span class="post-publish-date"><?php echo esc_html($post_date); ?></span>
                    </div>

                    <h1 class="post-main-title"><?php echo esc_html($current_title); ?></h1>

                    <div class="post-sapo-excerpt">
                        <?php echo wp_kses_post($post_excerpt); ?>
                    </div>

                    <!-- Thanh tác giả (Author Bar) -->
                    <div class="post-author-bar">
                        <div class="author-left">
                            <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>" class="author-avatar">
                            <div class="author-info">
                                <span class="author-name"><?php echo esc_html($author_name); ?></span>
                                <span class="author-role"><?php echo esc_html($author_role); ?></span>
                            </div>
                        </div>

                        <div class="author-meta-right">
                            <div class="meta-metric-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                <span><?php echo esc_html($reading_time); ?> phút đọc</span>
                            </div>

                            <div class="meta-metric-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                <span><?php echo esc_html($post_views); ?> lượt xem</span>
                            </div>

                            <button type="button" class="btn-share-post" id="btnOpenSharePopup" aria-label="Chia sẻ bài viết">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                                <span>Chia sẻ</span>
                            </button>
                        </div>
                    </div>
                </header>

                <!-- 2. ẢNH ĐẠI DIỆN BÀI VIẾT -->
                <div class="post-featured-media">
                    <img src="<?php echo esc_url($featured_img); ?>" alt="<?php echo esc_attr($current_title); ?>">
                    <div class="media-watermark">
                        <div class="media-watermark-title">Những hành trình<br>đẹp hơn cùng</div>
                        <div class="media-watermark-brand">
                            <span>GoBike</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66l.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg>
                        </div>
                    </div>
                </div>

                <!-- 3. HỘP MỤC LỤC BÀI VIẾT (TABLE OF CONTENTS) -->
                <?php if (!empty($toc_items)): ?>
                    <div class="post-toc-box">
                        <div class="toc-left-col">
                            <svg class="toc-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            <h3 class="toc-heading">Nội dung bài viết</h3>
                        </div>
                        <div class="toc-right-col">
                            <ol class="toc-list">
                                <?php foreach ($toc_items as $item): ?>
                                    <li class="toc-item">
                                        <a href="#<?php echo esc_attr($item['id']); ?>" class="toc-link">
                                            <?php echo esc_html($item['title']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- 4. NỘI DUNG BÀI VIẾT & WRAPPER COLLAPSIBLE (ĐỌC TIẾP / THU GỌN) -->
                <div class="post-content-collapsible-wrapper">
                    <div class="post-content-collapsible-inner" id="postContentInner">
                        <div class="post-body-content">
                            <?php if (!$use_sample_content): ?>
                                <?php echo $content_html; ?>
                            <?php else: ?>
                                <!-- Nội dung chi tiết chuẩn mockup 100% khi bài viết mới/chưa có nội dung phong phú -->
                                <p>Đạp xe không chỉ là một bộ môn thể thao, mà còn là cách tuyệt vời để khám phá thiên nhiên, rèn luyện sức khỏe và tìm lại sự cân bằng trong cuộc sống. Dưới đây là 5 cung đường đạp xe đẹp nhất miền Bắc mà bất kỳ tín đồ xe đạp nào cũng nên trải nghiệm ít nhất một lần.</p>

                                <h2 id="muc-luc-1">1. Hồ Hòa Bình – Cung đường của thiên nhiên</h2>
                                <p>Với mặt hồ rộng lớn và những con đường ven núi uốn lượn, Hòa Bình mang đến trải nghiệm đạp xe đầy thư thái, gần gũi với thiên nhiên. Đây là cung đường phù hợp cho cả người mới và những ai yêu thích sự yên bình.</p>

                                <figure>
                                    <img src="<?php echo esc_url($featured_img); ?>" alt="Cung đường ven hồ Hòa Bình">
                                    <figcaption>Cung đường ven hồ Hòa Bình – nơi thiên nhiên và đam mê gặp nhau.</figcaption>
                                </figure>

                                <div class="post-article-quote">
                                    <div class="quote-text-wrap">
                                        <span class="quote-mark">“</span>
                                        <span>Mỗi vòng bánh xe là một lần bạn gần hơn với thiên nhiên và chính mình.</span>
                                    </div>
                                    <span class="quote-author">— GoBike</span>
                                </div>

                                <h2 id="muc-luc-2">2. Mộc Châu – Cao nguyên trong lành</h2>
                                <p>Không khí mát mẻ, đồi chè xanh mướt và những con dốc vừa thử thách vừa thú vị khiến Mộc Châu trở thành điểm đến lý tưởng cho dân đạp xe trợ lực điện và xe thể thao.</p>

                                <figure>
                                    <img src="https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg" alt="Đồi chè Mộc Châu">
                                    <figcaption>Những đồi chè xanh mướt trải dài bất tận tại Mộc Châu.</figcaption>
                                </figure>

                                <h2 id="muc-luc-3">3. Tam Đảo – Thử thách và chinh phục</h2>
                                <p>Cung đường dốc quanh co lên đỉnh Tam Đảo với chiều dài hơn 13km là bài kiểm tra tuyệt vời cho thể lực và sức bền. Đạp xe xuyên qua làn mây mờ ảo mang lại cảm giác phấn khích khó tả.</p>

                                <h2 id="muc-luc-4">4. Mai Châu – Bình yên giữa núi rừng</h2>
                                <p>Đạp xe len lỏi qua những bản làng yên bình của người Thái, ngắm nhìn những cánh đồng lúa chín vàng óng ả vào mùa thu hoạch là trải nghiệm đậm chất thơ.</p>

                                <h2 id="muc-luc-5">5. Hà Giang – Hành trình của những trái tim tự do</h2>
                                <p>Dành cho những tay đạp xe dũng cảm muốn chinh phục đèo Mã Pí Lèng hùng vĩ, ngắm nhìn dòng sông Nho Quế xanh như ngọc bích từ trên đỉnh đèo cao vút.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Nút Đọc tiếp toàn bộ bài viết - Thu gọn -->
                    <div class="post-readmore-action-wrap">
                        <button type="button" class="btn-toggle-post-content" id="btnTogglePostContent">
                            <span class="btn-text">Đọc tiếp toàn bộ bài viết</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- 5. SECTION 2: THẺ CỦA BÀI VIẾT (TAGS) -->
                <div class="post-tags-section">
                    <span class="tags-label">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                        Thẻ:
                    </span>
                    <?php if (!empty($tags)): ?>
                        <?php foreach ($tags as $tag): ?>
                            <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="post-tag-pill"><?php echo esc_html($tag->name); ?></a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php foreach ($sample_tags as $s_tag): ?>
                            <a href="<?php echo esc_url(home_url('/?s=' . urlencode($s_tag))); ?>" class="post-tag-pill"><?php echo esc_html($s_tag); ?></a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- 6. SECTION 3: SẢN PHẨM PHÙ HỢP (WOOCOMMERCE CARDS) -->
                <section class="post-related-section post-products-section">
                    <div class="section-title-row">
                        <h3 class="section-title-heading">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66l.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg>
                            Sản phẩm phù hợp
                        </h3>
                        <a href="<?php echo esc_url(home_url('/cua-hang/')); ?>" class="section-view-all-link">
                            Xem tất cả &rarr;
                        </a>
                    </div>

                    <div class="post-products-grid">
                        <?php foreach ($related_products as $prod): ?>
                            <div class="post-product-card">
                                <?php if (!empty($prod['badge'])): ?>
                                    <div class="prod-badge-wrap">
                                        <span class="prod-badge <?php echo esc_attr($prod['badge_type']); ?>">
                                            <?php echo esc_html($prod['badge']); ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <a href="<?php echo esc_url($prod['url']); ?>" class="prod-thumb-wrap">
                                    <img src="<?php echo esc_url($prod['thumb']); ?>" alt="<?php echo esc_attr($prod['title']); ?>" loading="lazy">
                                </a>

                                <h4 class="prod-title">
                                    <a href="<?php echo esc_url($prod['url']); ?>"><?php echo esc_html($prod['title']); ?></a>
                                </h4>
                                <div class="prod-cat-type"><?php echo esc_html($prod['cat']); ?></div>

                                <div class="prod-quick-specs">
                                    <span class="spec-chip">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                        <?php echo esc_html($prod['specs_km']); ?>
                                    </span>
                                    <span class="spec-chip">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v18"></path><rect x="6" y="8" width="12" height="12" rx="2"></rect></svg>
                                        <?php echo esc_html($prod['specs_kg']); ?>
                                    </span>
                                </div>

                                <div class="prod-card-bottom">
                                    <div class="prod-pricing">
                                        <?php if (!empty($prod['price_html'])): ?>
                                            <?php echo $prod['price_html']; ?>
                                        <?php else: ?>
                                            <span class="price-current <?php echo !empty($prod['price_old']) ? 'has-sale' : ''; ?>"><?php echo esc_html($prod['price_current']); ?></span>
                                            <?php if (!empty($prod['price_old'])): ?>
                                                <span class="price-old"><?php echo esc_html($prod['price_old']); ?></span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>

                                    <a href="<?php echo esc_url($prod['add_to_cart_url'] ?: $prod['url']); ?>" class="btn-add-cart-mini" aria-label="Thêm vào giỏ">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- 7. SECTION 4: BÀI VIẾT LIÊN QUAN (HORIZONTAL CARDS) -->
                <section class="post-related-section post-related-news-section">
                    <div class="section-title-row">
                        <h3 class="section-title-heading">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66l.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg>
                            Bài viết liên quan
                        </h3>
                        <a href="<?php echo esc_url(home_url('/tin-tuc-cam-nang/')); ?>" class="section-view-all-link">
                            Xem tất cả &rarr;
                        </a>
                    </div>

                    <div class="post-related-news-grid">
                        <?php foreach ($related_posts as $r_post): ?>
                            <a href="<?php echo esc_url($r_post['url']); ?>" class="related-news-card">
                                <div class="rel-thumb-wrap">
                                    <img src="<?php echo esc_url($r_post['thumb']); ?>" alt="<?php echo esc_attr($r_post['title']); ?>" loading="lazy">
                                </div>
                                <div class="rel-info-wrap">
                                    <div class="rel-date"><?php echo esc_html($r_post['date']); ?></div>
                                    <h4 class="rel-title"><?php echo esc_html($r_post['title']); ?></h4>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>

            </main>

            <!-- ================= CỘT PHẢI: SECTION 5 - SIDEBAR (~32%) ================= -->
            <aside class="gobike-single-sidebar-col">
                
                <!-- WIDGET 1: TÌM KIẾM BÀI VIẾT -->
                <div class="gobike-sidebar-widget widget-search">
                    <div class="widget-title-row">
                        <h3 class="widget-title-heading">Tìm kiếm bài viết</h3>
                    </div>
                    <form class="widget-search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                        <input type="text" name="s" class="widget-search-input" placeholder="Nhập từ khóa..." required autocomplete="off">
                        <button type="submit" class="widget-search-btn" aria-label="Tìm kiếm">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        </button>
                    </form>
                </div>

                <!-- WIDGET 2: BÀI VIẾT NỔI BẬT -->
                <div class="gobike-sidebar-widget widget-popular">
                    <div class="widget-title-row">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66l.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg>
                        <h3 class="widget-title-heading">Bài viết nổi bật</h3>
                    </div>
                    <ul class="widget-posts-list">
                        <?php if (!empty($sidebar_popular_posts)): ?>
                            <?php foreach ($sidebar_popular_posts as $p_item): ?>
                                <li>
                                    <a href="<?php echo esc_url($p_item['url']); ?>" class="widget-post-item">
                                        <div class="widget-post-thumb">
                                            <img src="<?php echo esc_url($p_item['thumb']); ?>" alt="<?php echo esc_attr($p_item['title']); ?>" loading="lazy">
                                        </div>
                                        <div class="widget-post-body">
                                            <h4 class="widget-post-title"><?php echo esc_html($p_item['title']); ?></h4>
                                            <span class="widget-post-date"><?php echo esc_html($p_item['date']); ?></span>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>
                                <a href="#" class="widget-post-item">
                                    <div class="widget-post-thumb"><img src="<?php echo esc_url($featured_img); ?>" alt=""></div>
                                    <div class="widget-post-body">
                                        <h4 class="widget-post-title">Kinh nghiệm du lịch Mộc Châu bằng xe đạp trợ lực</h4>
                                        <span class="widget-post-date">14/09/2026</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="widget-post-item">
                                    <div class="widget-post-thumb"><img src="<?php echo esc_url($featured_img); ?>" alt=""></div>
                                    <div class="widget-post-body">
                                        <h4 class="widget-post-title">Đạp xe mỗi ngày: 7 lợi ích tốt ngờ cho sức khỏe</h4>
                                        <span class="widget-post-date">10/09/2026</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="widget-post-item">
                                    <div class="widget-post-thumb"><img src="<?php echo esc_url($featured_img); ?>" alt=""></div>
                                    <div class="widget-post-body">
                                        <h4 class="widget-post-title">So sánh xe đạp trợ lực và xe đạp thường</h4>
                                        <span class="widget-post-date">08/09/2026</span>
                                    </div>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- WIDGET 3: BÀI VIẾT MỚI NHẤT -->
                <div class="gobike-sidebar-widget widget-latest">
                    <div class="widget-title-row">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17 8C8 10 5.9 16.17 3.82 21.34l1.89.66l.95-2.3c.48.17.98.3 1.34.3C19 20 22 3 22 3c-1 2-8 2.25-13 3.25S2 11.5 2 13.5s1.75 3.75 1.75 3.75C7 8 17 8 17 8z"/></svg>
                        <h3 class="widget-title-heading">Bài viết mới nhất</h3>
                    </div>
                    <ul class="widget-posts-list">
                        <?php if (!empty($sidebar_latest_posts)): ?>
                            <?php foreach ($sidebar_latest_posts as $l_item): ?>
                                <li>
                                    <a href="<?php echo esc_url($l_item['url']); ?>" class="widget-post-item">
                                        <div class="widget-post-thumb">
                                            <img src="<?php echo esc_url($l_item['thumb']); ?>" alt="<?php echo esc_attr($l_item['title']); ?>" loading="lazy">
                                        </div>
                                        <div class="widget-post-body">
                                            <h4 class="widget-post-title"><?php echo esc_html($l_item['title']); ?></h4>
                                            <span class="widget-post-date"><?php echo esc_html($l_item['date']); ?></span>
                                        </div>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>
                                <a href="#" class="widget-post-item">
                                    <div class="widget-post-thumb"><img src="<?php echo esc_url($featured_img); ?>" alt=""></div>
                                    <div class="widget-post-body">
                                        <h4 class="widget-post-title">Top 5 cung đường đạp xe đẹp nhất miền Bắc không thể bỏ lỡ</h4>
                                        <span class="widget-post-date">15/09/2026</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="widget-post-item">
                                    <div class="widget-post-thumb"><img src="<?php echo esc_url($featured_img); ?>" alt=""></div>
                                    <div class="widget-post-body">
                                        <h4 class="widget-post-title">Cách chọn xe đạp phù hợp với nhu cầu</h4>
                                        <span class="widget-post-date">12/09/2026</span>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="widget-post-item">
                                    <div class="widget-post-thumb"><img src="<?php echo esc_url($featured_img); ?>" alt=""></div>
                                    <div class="widget-post-body">
                                        <h4 class="widget-post-title">Phụ kiện xe đạp cần thiết cho người mới</h4>
                                        <span class="widget-post-date">08/09/2026</span>
                                    </div>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- WIDGET 4: HERO BANNER QUẢNG CÁO -->
                <div class="widget-hero-banner" style="background-image: url('<?php echo esc_url($featured_img); ?>');">
                    <div class="banner-content-inner">
                        <h4 class="banner-title">Mỗi cung đường<br>là một câu chuyện</h4>
                        <p class="banner-sub">Cùng GoBike viết tiếp hành trình của bạn!</p>
                        <a href="<?php echo esc_url(home_url('/cua-hang/')); ?>" class="btn-banner-action">
                            Khám phá ngay &rarr;
                        </a>
                    </div>
                </div>

                <!-- WIDGET 5: FORM ĐĂNG KÝ NHẬN TIN -->
                <div class="widget-newsletter-card">
                    <div class="newsletter-icon-wrap">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    </div>
                    <h4 class="newsletter-title">Nhận tin tức &amp; ưu đãi mới nhất từ GoBike</h4>
                    <p class="newsletter-sub">Đăng ký để không bỏ lỡ những bài viết hay và ưu đãi đặc biệt.</p>
                    
                    <form class="newsletter-form" onsubmit="event.preventDefault(); alert('Cảm ơn bạn đã đăng ký nhận tin từ GoBike!'); this.reset();">
                        <div class="newsletter-form-row">
                            <input type="email" class="newsletter-input" placeholder="Nhập email của bạn" required>
                            <button type="submit" class="btn-newsletter-submit">Đăng ký</button>
                        </div>
                        <p class="newsletter-privacy-text">Chúng tôi cam kết không spam. Bạn có thể hủy đăng ký bất cứ lúc nào.</p>
                    </form>
                </div>

                <!-- WIDGET 6: 3 CAM KẾT VÀNG GOBIKE -->
                <div class="widget-trust-badges">
                    <div class="trust-badge-item">
                        <div class="trust-icon-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
                        </div>
                        <div class="trust-title">Giao hàng toàn quốc</div>
                        <div class="trust-desc">Nhanh chóng, an toàn</div>
                    </div>

                    <div class="trust-badge-item">
                        <div class="trust-icon-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        </div>
                        <div class="trust-title">Tư vấn 24/7</div>
                        <div class="trust-desc">1900 038 834</div>
                    </div>

                    <div class="trust-badge-item">
                        <div class="trust-icon-box">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        </div>
                        <div class="trust-title">Cam kết chính hãng</div>
                        <div class="trust-desc">Sản phẩm chất lượng</div>
                    </div>
                </div>

            </aside>

        </div>

    </div>
</div>

<!-- MODAL POPUP CHIA SẺ BÀI VIẾT -->
<div class="gobike-share-popup" id="gobikeSharePopup">
    <div class="share-popup-box">
        <div class="share-popup-header">
            <h4 class="share-popup-title">Chia sẻ bài viết</h4>
            <button type="button" class="btn-close-share-popup" id="btnCloseSharePopup" aria-label="Đóng popup">&times;</button>
        </div>
        <div class="share-buttons-grid">
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="btn-share-social">
                <svg viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                <span>Facebook</span>
            </a>
            <a href="https://zalo.me/share?url=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="btn-share-social">
                <svg viewBox="0 0 24 24" fill="#0068FF"><circle cx="12" cy="12" r="10"/><path d="M8 12h8M12 8v8" stroke="#fff" stroke-width="2"/></svg>
                <span>Zalo</span>
            </a>
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode($current_title); ?>" target="_blank" rel="noopener noreferrer" class="btn-share-social">
                <svg viewBox="0 0 24 24" fill="#1DA1F2"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                <span>Twitter / X</span>
            </a>
        </div>
        <div class="share-copy-row">
            <input type="text" class="share-copy-input" id="shareCopyInput" value="<?php echo esc_url(get_permalink()); ?>" readonly>
            <button type="button" class="btn-copy-link" id="btnCopyLink">Sao chép</button>
        </div>
    </div>
</div>

<!-- JAVASCRIPT XỬ LÝ COLLAPSE/EXPAND, SMOOTH SCROLL VÀ SHARE POPUP -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Xử lý nút Đọc tiếp toàn bộ bài viết / Thu gọn
    var btnToggle = document.getElementById('btnTogglePostContent');
    var contentInner = document.getElementById('postContentInner');
    var mainPostContent = document.getElementById('mainPostContent');

    if (btnToggle && contentInner) {
        btnToggle.addEventListener('click', function () {
            var isExpanded = contentInner.classList.contains('expanded');
            if (!isExpanded) {
                contentInner.classList.add('expanded');
                btnToggle.classList.add('expanded');
                btnToggle.querySelector('.btn-text').textContent = 'Thu gọn';
            } else {
                contentInner.classList.remove('expanded');
                btnToggle.classList.remove('expanded');
                btnToggle.querySelector('.btn-text').textContent = 'Đọc tiếp toàn bộ bài viết';
                
                // Cuộn êm về đầu nội dung bài viết
                if (mainPostContent) {
                    mainPostContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    }

    // 2. Xử lý cuộn mượt cho các liên kết trong Mục lục (Table of Contents)
    var tocLinks = document.querySelectorAll('.toc-link');
    tocLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            var targetId = this.getAttribute('href');
            var targetEl = document.querySelector(targetId);
            if (targetEl) {
                // Tự động mở rộng nội dung nếu đang thu gọn
                if (contentInner && !contentInner.classList.contains('expanded')) {
                    contentInner.classList.add('expanded');
                    if (btnToggle) {
                        btnToggle.classList.add('expanded');
                        btnToggle.querySelector('.btn-text').textContent = 'Thu gọn';
                    }
                }
                
                var headerOffset = 90;
                var elPosition = targetEl.getBoundingClientRect().top;
                var offsetPosition = elPosition + window.pageYOffset - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // 3. Xử lý Share Popup & Copy Link
    var btnOpenShare = document.getElementById('btnOpenSharePopup');
    var btnCloseShare = document.getElementById('btnCloseSharePopup');
    var sharePopup = document.getElementById('gobikeSharePopup');
    var btnCopyLink = document.getElementById('btnCopyLink');
    var shareInput = document.getElementById('shareCopyInput');

    if (btnOpenShare && sharePopup) {
        btnOpenShare.addEventListener('click', function () {
            sharePopup.classList.add('active');
        });
    }

    if (btnCloseShare && sharePopup) {
        btnCloseShare.addEventListener('click', function () {
            sharePopup.classList.remove('active');
        });
    }

    if (sharePopup) {
        sharePopup.addEventListener('click', function (e) {
            if (e.target === sharePopup) {
                sharePopup.classList.remove('active');
            }
        });
    }

    if (btnCopyLink && shareInput) {
        btnCopyLink.addEventListener('click', function () {
            shareInput.select();
            shareInput.setSelectionRange(0, 99999);
            try {
                navigator.clipboard.writeText(shareInput.value).then(function () {
                    btnCopyLink.textContent = 'Đã chép!';
                    setTimeout(function () {
                        btnCopyLink.textContent = 'Sao chép';
                    }, 2000);
                });
            } catch (err) {
                document.execCommand('copy');
                btnCopyLink.textContent = 'Đã chép!';
                setTimeout(function () {
                    btnCopyLink.textContent = 'Sao chép';
                }, 2000);
            }
        });
    }
});
</script>

<?php
get_footer();
