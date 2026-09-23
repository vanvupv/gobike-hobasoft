<?php
/**
 * GoBike News AJAX Handlers: Load More Posts
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_gobike_load_more_news', 'gobike_ajax_load_more_news');
add_action('wp_ajax_nopriv_gobike_load_more_news', 'gobike_ajax_load_more_news');

function gobike_ajax_load_more_news() {
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 2;
    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $bg_cyclist = 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=1200&q=80';

    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 5,
        'paged'          => $paged,
    );

    if (!empty($search)) {
        $args['s'] = $search;
    }
    if (!empty($category)) {
        $args['category_name'] = $category;
    }

    $query = new WP_Query($args);
    $html = '';
    $has_more = false;

    if ($query->have_posts()) {
        $has_more = ($paged < $query->max_num_pages);
        ob_start();
        while ($query->have_posts()) {
            $query->the_post();
            $pid = get_the_ID();
            $cats = get_the_category($pid);
            $cat_names = array();
            if (!empty($cats)) {
                foreach ($cats as $c) {
                    $cat_names[] = $c->name;
                }
            }
            if (empty($cat_names)) {
                $cat_names = array('Tin tức', 'Kinh nghiệm');
            }
            $thumb = get_the_post_thumbnail_url($pid, 'medium_large') ?: $bg_cyclist;
            $url = get_permalink();
            $title = get_the_title();
            $date = get_the_date('d/m/Y');
            $excerpt = wp_trim_words(get_the_excerpt(), 24, '...');
            ?>
            <article class="latest-post-horizontal-card ajax-loaded-item" style="animation: fadeInUp 0.4s ease forwards;">
                <div class="card-thumb-wrap">
                    <a href="<?php echo esc_url($url); ?>">
                        <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" />
                    </a>
                </div>
                <div class="card-body-wrap">
                    <div class="card-meta-top">
                        <span class="card-date"><?php echo esc_html($date); ?></span>
                    </div>
                    <h3 class="card-post-title">
                        <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($title); ?></a>
                    </h3>
                    <p class="card-post-excerpt"><?php echo esc_html($excerpt); ?></p>
                    <div class="card-footer-meta">
                        <div class="card-tags-list">
                            <?php foreach ($cat_names as $tag): ?>
                                <span class="tag-pill"><?php echo esc_html($tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <a href="<?php echo esc_url($url); ?>" class="card-read-more">
                            <span class="read-more-text">Đọc thêm</span> <span class="arrow">&rarr;</span>
                        </a>
                    </div>
                </div>
                <a href="<?php echo esc_url($url); ?>" class="card-mobile-arrow" aria-label="<?php echo esc_attr($title); ?>">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0d7030" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </article>
            <?php
        }
        $html = ob_get_clean();
        wp_reset_postdata();
    } else {
        // Fallback demo posts if DB doesn't have more real posts
        $fallback_items = array(
            array(
                'title' => 'Tổng hợp 10 mẹo tiết kiệm pin xe đạp trợ lực khi leo đèo',
                'thumb' => 'https://images.unsplash.com/photo-1517649763962-0c623266ddc0?auto=format&fit=crop&w=800&q=80',
                'date'  => '26/08/2026',
                'cats'  => array('Mẹo vặt', 'Kỹ thuật'),
                'excerpt' => 'Cách chuyển số hợp lý và sử dụng mức trợ lực thông minh giúp xe đi xa hơn 30%.',
            ),
            array(
                'title' => 'Trải nghiệm GoBike trên cung đường ven biển Phan Thiết',
                'thumb' => 'https://images.unsplash.com/photo-1508962914676-134849a727f0?auto=format&fit=crop&w=800&q=80',
                'date'  => '24/08/2026',
                'cats'  => array('Du lịch', 'Trải nghiệm'),
                'excerpt' => 'Đón bình minh trên cung đường cát trắng với cảm giác lướt êm ái cùng trợ lực điện.',
            ),
            array(
                'title' => 'Nên sạc pin xe đạp trợ lực khi nào để không bị chai?',
                'thumb' => 'https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=800&q=80',
                'date'  => '20/08/2026',
                'cats'  => array('Bảo dưỡng'),
                'excerpt' => 'Nguyên tắc vàng sạc pin chuẩn kỹ thuật giúp tăng tuổi thọ cell pin lên 3 năm.',
            ),
        );

        if ($paged <= 3) {
            $has_more = ($paged < 3);
            ob_start();
            foreach ($fallback_items as $f_item) {
                ?>
                <article class="latest-post-horizontal-card ajax-loaded-item" style="animation: fadeInUp 0.4s ease forwards;">
                    <div class="card-thumb-wrap">
                        <a href="#">
                            <img src="<?php echo esc_url($f_item['thumb']); ?>" alt="<?php echo esc_attr($f_item['title']); ?>" loading="lazy" />
                        </a>
                    </div>
                    <div class="card-body-wrap">
                        <div class="card-meta-top">
                            <span class="card-date"><?php echo esc_html($f_item['date']); ?></span>
                        </div>
                        <h3 class="card-post-title">
                            <a href="#"><?php echo esc_html($f_item['title']); ?></a>
                        </h3>
                        <p class="card-post-excerpt"><?php echo esc_html($f_item['excerpt']); ?></p>
                        <div class="card-footer-meta">
                            <div class="card-tags-list">
                                <?php foreach ($f_item['cats'] as $tag): ?>
                                    <span class="tag-pill"><?php echo esc_html($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <a href="#" class="card-read-more">
                                <span class="read-more-text">Đọc thêm</span> <span class="arrow">&rarr;</span>
                            </a>
                        </div>
                    </div>
                    <a href="#" class="card-mobile-arrow" aria-label="<?php echo esc_attr($f_item['title']); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0d7030" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </article>
                <?php
            }
            $html = ob_get_clean();
        }
    }

    wp_send_json_success(array(
        'html'       => $html,
        'has_more'   => $has_more,
        'next_paged' => $paged + 1,
    ));
}
