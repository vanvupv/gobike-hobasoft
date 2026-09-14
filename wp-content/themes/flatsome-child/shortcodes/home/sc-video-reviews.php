<?php
/**
 * Shortcode: Khối Video Đánh Giá Thực Tế
 * Cú pháp dùng trong Flatsome: [gobike_home_video_reviews title="VIDEO ĐÁNH GIÁ THỰC TẾ TỪ GOBIKE"]
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
            <!-- Tùy biến lấy ACF Repeater từ Theme Settings hoặc bài viết video -->
            <div class="gobike-video-placeholder">
                <p>Khối hiển thị video đánh giá sản phẩm (Cấu hình qua ACF Theme Settings hoặc chèn link YouTube).</p>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('gobike_home_video_reviews', 'gobike_render_home_video_reviews');
