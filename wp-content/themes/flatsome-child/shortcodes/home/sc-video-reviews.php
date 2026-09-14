<?php
/**
 * Shortcode: Khối Video Đánh Giá Thực Tế
 * Cú pháp dùng trong Flatsome: [gobike_home_video_reviews title="VIDEO ĐÁNH GIÁ THỰC TẾ TỪ GOBIKE"]
 * Lưu ý: File CSS được nhúng trực tiếp trong shortcode.
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
    <!-- CSS NHÚNG TRỰC TIẾP TRONG SHORTCODE -->
    <style>
        .gobike-home-video-reviews-block {
            max-width: 1230px;
            margin: 0 auto 30px auto;
            padding: 0 10px;
            box-sizing: border-box;
        }
        .gobike-video-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        .gobike-video-card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .gobike-video-card:hover {
            border-color: #149d29;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .gobike-video-thumb {
            position: relative;
            padding-top: 56.25%; /* 16:9 */
            background: #000;
        }
        .gobike-video-thumb iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        .gobike-video-info {
            padding: 10px 12px;
        }
        .gobike-video-title {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
            line-height: 1.4;
            color: #333;
        }
        @media (max-width: 768px) {
            .gobike-video-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
        }
    </style>

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
