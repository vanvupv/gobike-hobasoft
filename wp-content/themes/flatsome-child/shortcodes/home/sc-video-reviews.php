<?php
/**
 * Shortcode: Khối Video Đánh Giá Thực Tế
 * Cú pháp dùng trong Flatsome: [gobike_home_video_reviews title="VIDEO ĐÁNH GIÁ THỰC TẾ TỪ GOBIKE"]
 * Ghi chú: CSS của khối này được quản lý tập trung tại: home-styles.php (Nạp qua hook wp_head)
 */

if (!defined('ABSPATH')) {
    exit;
}

function gobike_render_home_video_reviews($atts)
{
    $atts = shortcode_atts(array(
        'title' => 'VIDEO TRẢI NGHIỆM & ĐÁNH GIÁ THỰC TẾ',
        'limit' => 4,
    ), $atts, 'gobike_home_video_reviews');

    ob_start();
    ?>
    <div class="gobike-home-video-reviews-block">
        <div class="gobike-block-header">
            <h2 class="block-title"><?php echo esc_html($atts['title']); ?></h2>
        </div>
        <div class="gobike-video-grid">
            <div class="gobike-video-card">
                <div class="gobike-video-thumb">
                    <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Video Review" allowfullscreen></iframe>
                </div>
                <div class="gobike-video-info">
                    <h3 class="gobike-video-title">Trải nghiệm thực tế xe đạp trợ lực điện GOBIKE</h3>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('gobike_home_video_reviews', 'gobike_render_home_video_reviews');
