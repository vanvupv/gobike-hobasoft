<?php
/**
 * Template Name: Trang Chi Tiết Video Review - GoBike
 * Template Post Type: page, video_review
 * 
 * @package Flatsome-Child
 */

get_header();

// Thiết lập dữ liệu post data chuẩn WordPress loop
if (have_posts() && empty($GLOBALS['gb_vd_post_setup'])) {
    the_post();
    $GLOBALS['gb_vd_post_setup'] = true;
}

// Lấy thông tin bài viết hiện tại (TỰ ĐỘNG ăn theo bài viết post type: video_review)
$current_id = get_the_ID();
$is_video_cpt = (get_post_type($current_id) === 'video_review');

$current_title = $is_video_cpt ? get_the_title() : 'Trải nghiệm thực tế Phoenix C200: Đạp nhẹ hơn, đi xa hơn';
$current_date  = $is_video_cpt ? get_the_date('d/m/Y') : '12/09/2025';
$current_views = ($is_video_cpt && get_field('video_views_text', $current_id)) ? get_field('video_views_text', $current_id) : '12.5K lượt xem';
$current_url   = ($is_video_cpt && get_field('video_url', $current_id)) ? get_field('video_url', $current_id) : 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';

$current_thumb = get_the_post_thumbnail_url($current_id, 'full');
if (!$current_thumb && $is_video_cpt) {
    $acf_thumb = get_field('video_thumbnail', $current_id);
    if ($acf_thumb) {
        $current_thumb = is_array($acf_thumb) ? $acf_thumb['url'] : $acf_thumb;
    }
}
if (!$current_thumb) {
    $current_thumb = 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg';
}

// 1. Tự động truy vấn 5 Video Tiếp Theo (Loại trừ bài viết hiện tại)
$up_next_query = new WP_Query(array(
    'post_type'      => 'video_review',
    'post_status'    => 'publish',
    'posts_per_page' => 5,
    'post__not_in'   => array($current_id),
    'orderby'        => 'date',
    'order'          => 'DESC',
));

$up_next_videos = array();
if ($up_next_query->have_posts()) {
    while ($up_next_query->have_posts()) {
        $up_next_query->the_post();
        $v_id = get_the_ID();
        $up_next_videos[] = array(
            'title'    => get_the_title(),
            'channel'  => 'GoBike',
            'meta'     => (get_field('video_views_text', $v_id) ?: '15K lượt xem') . ' • ' . human_time_diff(get_the_time('U'), current_time('timestamp')) . ' trước',
            'duration' => get_field('video_duration', $v_id) ?: '08:15',
            'thumb'    => get_the_post_thumbnail_url($v_id, 'medium') ?: 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
            'url'      => get_permalink($v_id),
        );
    }
    wp_reset_postdata();
}

// Fallback mẫu chuẩn thiết kế nếu chưa có đủ bài
$fallback_upnext = array(
    array(
        'title'    => 'So sánh Phoenix C200 và Phoenix S1 – Nên chọn mẫu nào?',
        'channel'  => 'GoBike',
        'meta'     => '24K lượt xem • 7 ngày trước',
        'duration' => '12:04',
        'thumb'    => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
        'url'      => home_url('/video-review/'),
    ),
    array(
        'title'    => 'Hướng dẫn lắp đặt xe đạp trợ lực tại nhà',
        'channel'  => 'GoBike',
        'meta'     => '8.5K lượt xem • 10 ngày trước',
        'duration' => '06:42',
        'thumb'    => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
        'url'      => home_url('/video-review/'),
    ),
    array(
        'title'    => 'Leo dốc cùng ADO A20 – Không còn là nỗi lo',
        'channel'  => 'GoBike',
        'meta'     => '15K lượt xem • 2 tuần trước',
        'duration' => '07:21',
        'thumb'    => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
        'url'      => home_url('/video-review/'),
    ),
    array(
        'title'    => '5 lưu ý quan trọng khi mua xe đạp trợ lực điện',
        'channel'  => 'GoBike',
        'meta'     => '18K lượt xem • 2 tuần trước',
        'duration' => '09:18',
        'thumb'    => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
        'url'      => home_url('/video-review/'),
    ),
    array(
        'title'    => 'Trải nghiệm thực tế Shengmilo MX06',
        'channel'  => 'GoBike',
        'meta'     => '11K lượt xem • 3 tuần trước',
        'duration' => '08:36',
        'thumb'    => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
        'url'      => home_url('/video-review/'),
    ),
);
while (count($up_next_videos) < 5) {
    $up_next_videos[] = $fallback_upnext[count($up_next_videos) % count($fallback_upnext)];
}

// 2. Tự động truy vấn 4 Video Liên Quan (Ảnh 2)
$related_query = new WP_Query(array(
    'post_type'      => 'video_review',
    'post_status'    => 'publish',
    'posts_per_page' => 4,
    'post__not_in'   => array_merge(array($current_id), wp_list_pluck($up_next_videos, 'id')),
    'orderby'        => 'rand',
));

$related_videos = array();
if ($related_query->have_posts()) {
    while ($related_query->have_posts()) {
        $related_query->the_post();
        $r_id = get_the_ID();
        $related_videos[] = array(
            'title'    => get_the_title(),
            'meta'     => (get_field('video_views_text', $r_id) ?: '20K lượt xem') . ' • ' . human_time_diff(get_the_time('U'), current_time('timestamp')) . ' trước',
            'duration' => get_field('video_duration', $r_id) ?: '09:30',
            'thumb'    => get_the_post_thumbnail_url($r_id, 'medium') ?: 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
            'url'      => get_permalink($r_id),
        );
    }
    wp_reset_postdata();
}

$fallback_related = array(
    array(
        'title'    => 'Đánh giá chi tiết Phoenix C200: Hiệu năng vượt mong đợi',
        'meta'     => '28K lượt xem • 2 tuần trước',
        'duration' => '10:24',
        'thumb'    => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
        'url'      => home_url('/video-review/'),
    ),
    array(
        'title'    => 'So sánh Phoenix C200 và Phoenix S1 – Đâu là lựa chọn...',
        'meta'     => '15K lượt xem • 3 tuần trước',
        'duration' => '08:56',
        'thumb'    => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
        'url'      => home_url('/video-review/'),
    ),
    array(
        'title'    => 'Hành trình 100km cùng Phoenix C200 – Pin có thật sự...',
        'meta'     => '42K lượt xem • 1 tháng trước',
        'duration' => '06:18',
        'thumb'    => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
        'url'      => home_url('/video-review/'),
    ),
    array(
        'title'    => 'Hướng dẫn bảo dưỡng xe đạp trợ lực điện',
        'meta'     => '9.1K lượt xem • 1 tháng trước',
        'duration' => '07:30',
        'thumb'    => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
        'url'      => home_url('/video-review/'),
    ),
);
while (count($related_videos) < 4) {
    $related_videos[] = $fallback_related[count($related_videos) % count($fallback_related)];
}

// 6 Câu hỏi thường gặp FAQ (Ảnh 2 - Có câu trả lời chi tiết đóng/mở Accordion)
$faqs = array(
    array(
        'q' => 'Phoenix C200 đi được bao xa sau mỗi lần sạc?',
        'a' => 'Phoenix C200 có thể di chuyển quãng đường từ 70 - 100km ở chế độ trợ lực điện và khoảng 40 - 50km ở chế độ thuần điện tùy thuộc vào tải trọng, địa hình và tốc độ di chuyển.',
    ),
    array(
        'q' => 'Thời gian sạc pin của Phoenix C200 là bao lâu?',
        'a' => 'Thời gian sạc đầy viên pin Lithium 48V-15Ah của Phoenix C200 mất khoảng 4 - 6 tiếng với củ sạc thông minh có tính năng tự động ngắt điện khi pin đầy an toàn chống cháy nổ.',
    ),
    array(
        'q' => 'Xe phù hợp với chiều cao bao nhiêu?',
        'a' => 'Xe được thiết kế công thái học phù hợp cho người có chiều cao từ 1m55 đến 1m85 nhờ cọc yên và ghi-đông có thể điều chỉnh linh hoạt.',
    ),
    array(
        'q' => 'Có thể lái thử xe ở đâu?',
        'a' => 'Bạn có thể đến trải nghiệm và lái thử trực tiếp tại hệ thống showroom GoBike trên toàn quốc (Hà Nội, TP.HCM, Đà Nẵng, Hải Phòng) hoặc đặt lịch trước trên website để được phục vụ tốt nhất.',
    ),
    array(
        'q' => 'Chính sách bảo hành như thế nào?',
        'a' => 'GoBike bảo hành chính hãng khung sườn 3 năm, động cơ và pin lithium 2 năm, linh kiện phụ tùng khác 1 năm. Hỗ trợ bảo dưỡng định kỳ miễn phí trọn đời tại các showroom.',
    ),
    array(
        'q' => 'GoBike có hỗ trợ trả góp không?',
        'a' => 'GoBike có hỗ trợ trả góp 0% lãi suất qua thẻ tín dụng của hơn 25 ngân hàng hoặc trả góp qua CCCD duyệt hồ sơ nhanh chỉ trong 15 phút.',
    ),
);

// Lấy danh sách bình luận thật của bài viết này trong WordPress
$real_comments = get_comments(array(
    'post_id' => $current_id,
    'status'  => 'approve',
    'order'   => 'DESC',
));
$has_real_comments = !empty($real_comments);
$comments_count = $has_real_comments ? count($real_comments) : 24;

// 3 Bình luận mẫu fallback (Ảnh 2)
$comments_sample = array(
    array(
        'name'    => 'Nguyễn Minh Tuấn',
        'time'    => '3 ngày trước',
        'avatar'  => 'https://secure.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=80&d=mm&r=g',
        'content' => 'Trải nghiệm thực tế rất chân thực, mình đã đặt lịch lái thử cuối tuần này!',
        'likes'   => '12',
    ),
    array(
        'name'    => 'Lê Thảo Vy',
        'time'    => '5 ngày trước',
        'avatar'  => 'https://secure.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=80&d=mm&r=g',
        'content' => 'Video rất chi tiết, hữu ích. Cảm ơn GoBike!',
        'likes'   => '8',
    ),
    array(
        'name'    => 'Trần Quốc Bảo',
        'time'    => '1 tuần trước',
        'avatar'  => 'https://secure.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=80&d=mm&r=g',
        'content' => 'Mình đang phân vân giữa C200 và S1, xem xong video thấy rõ khác biệt. Rất hay!',
        'likes'   => '6',
    ),
);
?>

<div id="content" role="main" class="content-area gobike-video-detail-page">
    <div class="row">
        <div class="col large-12">
            <div class="col-inner">

                <!-- 1. BREADCRUMB -->
                <div class="gb-vd-breadcrumb">
                    <a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a> &gt; 
                    <a href="<?php echo esc_url(home_url('/video-review/')); ?>">Video</a> &gt; 
                    <span class="current"><?php echo esc_html($current_title); ?></span>
                </div>

                <!-- 2. BỐ CỤC 2 CỘT CHÍNH (TOP HERO: MAIN PLAYER + SIDEBAR) -->
                <div class="gb-vd-main-grid">
                    
                    <!-- ================= CỘT TRÁI: NỘI DUNG CHÍNH ================= -->
                    <main class="gb-vd-main-content">
                        
                        <!-- 2.1 Khung Video Player 16:9 với hiệu ứng hover zoom thumbnail -->
                        <div class="gb-vd-player-box">
                            <div class="gb-vd-player-cover">
                                <div class="gb-vd-zoom-wrap">
                                    <img src="<?php echo esc_url($current_thumb); ?>" alt="<?php echo esc_attr($current_title); ?>">
                                </div>


                                <!-- Nút Play to chính giữa -->
                                <div class="gb-vd-big-play-btn">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                </div>

                                <!-- Thanh Player Bar giả lập ở đáy video (như ảnh mẫu) -->
                                <div class="gb-vd-player-controls-mock">
                                    <div class="gb-vd-progress-bar-mock">
                                        <div class="gb-vd-progress-fill-mock"></div>
                                    </div>
                                    <div class="gb-vd-controls-row">
                                        <div class="gb-vd-ctrl-left">
                                            <svg viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                                            <span>0:00 / 8:15</span>
                                        </div>
                                        <div class="gb-vd-ctrl-right">
                                            <!-- Icon Volume -->
                                            <svg viewBox="0 0 24 24"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path><path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path></svg>
                                            <!-- Icon Gear -->
                                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                                            <!-- Icon Fullscreen -->
                                            <svg viewBox="0 0 24 24"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2.2 Tiêu đề & Metadata -->
                        <div class="gb-vd-title-info">
                            <h1 class="gb-vd-main-title"><?php echo esc_html($current_title); ?></h1>
                            
                            <div class="gb-vd-meta-row">
                                <span class="gb-vd-meta-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                    <span><?php echo esc_html($current_date); ?></span>
                                </span>
                                <span class="gb-vd-meta-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <span><?php echo esc_html($current_views); ?></span>
                                </span>
                                <div class="gb-vd-hashtags">
                                    <a href="#" class="gb-vd-tag">#PhoenixC200</a>
                                    <a href="#" class="gb-vd-tag">#XeĐạpĐiện</a>
                                    <a href="#" class="gb-vd-tag">#TrảiNghiệmThựcTế</a>
                                </div>
                            </div>
                        </div>

                        <!-- 2.3 Hàng nút hành động -->
                        <div class="gb-vd-action-buttons">
                            <a href="#san-pham" class="gb-vd-btn-buy-now">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                                <span>Xem sản phẩm</span>
                            </a>
                            <a href="#dat-lich" class="gb-vd-btn-action">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                                <span>Đặt lịch lái thử</span>
                            </a>
                            <a href="javascript:void(0);" class="gb-vd-btn-action">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line></svg>
                                <span>Chia sẻ</span>
                            </a>
                            <a href="javascript:void(0);" class="gb-vd-btn-action">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg>
                                <span>Lưu</span>
                            </a>
                        </div>

                        <!-- 2.4 Hộp thông tin Kênh / Thương hiệu (Channel Box) -->
                        <div class="gb-vd-channel-box">
                            <div class="gb-vd-channel-left">
                                <div class="gb-vd-channel-avatar">
                                    <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                                </div>
                                <div>
                                    <h4 class="gb-vd-channel-name">GoBike Việt Nam</h4>
                                    <p class="gb-vd-channel-sub">Xe tốt hơn mỗi ngày</p>
                                </div>
                            </div>
                            <div class="gb-vd-channel-right">
                                <span class="gb-vd-channel-label">Liên hệ tư vấn</span>
                                <div class="gb-vd-channel-btns">
                                    <a href="tel:0944988699" class="gb-vd-btn-phone">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                        <span>0944 988 699</span>
                                    </a>
                                    <a href="https://zalo.me/0944988699" target="_blank" class="gb-vd-btn-zalo">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.82.49 3.53 1.34 5L2 22l5.18-1.32C8.61 21.49 10.26 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2z"/></svg>
                                        <span>Nhắn Zalo</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- 2.5 Khối "Sản phẩm trong video" -->
                        <div class="gb-vd-product-section" id="san-pham">
                            <h3 class="gb-vd-section-title">Sản phẩm trong video</h3>
                            
                            <div class="gb-vd-product-card">
                                <!-- Khung ảnh xe đạp có hiệu ứng hover zoom ảnh -->
                                <div class="gb-vd-product-thumb">
                                    <div class="gb-vd-zoom-wrap">
                                        <img src="https://gobike.demoweb360.top/wp-content/uploads/2026/08/xe-dap-tro-luc-dien-phoenix-a9-pro.png" alt="Phoenix C200">
                                    </div>
                                </div>

                                <!-- Thông tin xe đạp -->
                                <div class="gb-vd-prod-info">
                                    <h4 class="gb-vd-prod-name">Phoenix C200</h4>
                                    <p class="gb-vd-prod-desc">Mẫu xe đạp trợ lực điện được yêu thích nhờ thiết kế thể thao, vận hành êm ái.</p>
                                    
                                    <div class="gb-vd-prod-pricing">
                                        <span class="gb-vd-price-current">17.990.000đ</span>
                                        <span class="gb-vd-price-old">20.490.000đ</span>
                                        <span class="gb-vd-price-disc">-12%</span>
                                    </div>
                                </div>

                                <!-- Cột nút hành động -->
                                <div class="gb-vd-prod-actions">
                                    <a href="<?php echo esc_url(home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/')); ?>" class="gb-vd-btn-prod-detail">Xem chi tiết &rarr;</a>
                                    <a href="#dat-lich" class="gb-vd-btn-prod-testdrive">Đặt lịch lái thử</a>
                                    <a href="<?php echo esc_url(home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/')); ?>" class="gb-vd-btn-prod-cart">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="9" cy="21" r="1"></circle>
                                            <circle cx="20" cy="21" r="1"></circle>
                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                        </svg>
                                        <span>Thêm vào giỏ hàng</span>
                                    </a>
                                </div>

                                <!-- 4 thông số kỹ thuật nằm ở dưới gồm Icon, Tiêu đề và Nội dung -->
                                <div class="gb-vd-prod-specs-grid">
                                    <div class="gb-vd-prod-spec-item">
                                        <div class="gb-vd-spec-icon">
                                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="9.5"></circle>
                                                <path d="M10 8l-2 2a3 3 0 0 0 0 4.24l1.76 1.76a3 3 0 0 0 4.24 0l2-2"></path>
                                                <path d="M7 6l2 2"></path>
                                                <path d="M16 15l2 2"></path>
                                            </svg>
                                        </div>
                                        <div class="gb-vd-spec-content">
                                            <span class="gb-vd-spec-label">Quãng đường</span>
                                            <span class="gb-vd-spec-val">lên tới <strong>100km</strong></span>
                                        </div>
                                    </div>

                                    <div class="gb-vd-prod-spec-item">
                                        <div class="gb-vd-spec-icon">
                                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="4.5"></circle>
                                                <path d="M10 3h4a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z"></path>
                                                <path d="M10 17h4a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-4a1 1 0 0 1-1-1v-2a1 1 0 0 1 1-1z"></path>
                                                <path d="M3 10h2a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1z"></path>
                                                <path d="M17 10h2a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1z"></path>
                                                <circle cx="12" cy="12" r="1.5" fill="currentColor"></circle>
                                            </svg>
                                        </div>
                                        <div class="gb-vd-spec-content">
                                            <span class="gb-vd-spec-label">Động cơ mạnh mẽ</span>
                                            <span class="gb-vd-spec-val"><strong>250W</strong></span>
                                        </div>
                                    </div>

                                    <div class="gb-vd-prod-spec-item">
                                        <div class="gb-vd-spec-icon">
                                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="6" y="5" width="12" height="16" rx="2.5"></rect>
                                                <line x1="10" y1="2" x2="14" y2="2" stroke-width="2.5" stroke-linecap="round"></line>
                                                <path d="M12.5 9l-2 3.5h3l-2 4"></path>
                                            </svg>
                                        </div>
                                        <div class="gb-vd-spec-content">
                                            <span class="gb-vd-spec-label">Pin lithium</span>
                                            <span class="gb-vd-spec-val"><strong>48V - 15Ah</strong></span>
                                        </div>
                                    </div>

                                    <div class="gb-vd-prod-spec-item">
                                        <div class="gb-vd-spec-icon">
                                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="5.5" r="3"></circle>
                                                <path d="M6.5 20.5h11a1.5 1.5 0 0 0 1.5-1.3l-1.2-9.5A2 2 0 0 0 15.8 8H8.2a2 2 0 0 0-2 1.7l-1.2 9.5a1.5 1.5 0 0 0 1.5 1.3z"></path>
                                                <circle cx="12" cy="14.5" r="1.5" fill="currentColor"></circle>
                                            </svg>
                                        </div>
                                        <div class="gb-vd-spec-content">
                                            <span class="gb-vd-spec-label">Trọng lượng</span>
                                            <span class="gb-vd-spec-val">chỉ <strong>24kg</strong></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </main>

                    <!-- ================= CỘT PHẢI: SIDEBAR ================= -->
                    <aside class="gb-vd-sidebar">
                        
                        <!-- 2.6 Khối "Video tiếp theo" (Khối bài viết ngang width 40% xử lý padding-bottom) -->
                        <div class="gb-vd-upnext-box">
                            <div class="gb-vd-upnext-header">
                                <h3>Video tiếp theo</h3>
                                <div class="gb-vd-autoplay-toggle">
                                    <span>Tự động phát</span>
                                    <label class="gb-vd-switch">
                                        <input type="checkbox" checked>
                                        <span class="gb-vd-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="gb-vd-upnext-list">
                                <?php foreach ($up_next_videos as $item): ?>
                                    <a href="<?php echo esc_url($item['url']); ?>" class="gb-vd-upnext-item">
                                        <!-- Thumbnail width 40% xử lý bằng padding-bottom 56.25% -->
                                        <div class="gb-vd-upnext-thumb">
                                            <div class="gb-vd-zoom-wrap">
                                                <img src="<?php echo esc_url($item['thumb']); ?>" alt="<?php echo esc_attr($item['title']); ?>">
                                            </div>
                                            <span class="gb-vd-duration-badge"><?php echo esc_html($item['duration']); ?></span>
                                        </div>
                                        
                                        <div class="gb-vd-upnext-info">
                                            <h4 class="gb-vd-upnext-title"><?php echo esc_html($item['title']); ?></h4>
                                            <div class="gb-vd-upnext-channel"><?php echo esc_html($item['channel']); ?></div>
                                            <div class="gb-vd-upnext-meta"><?php echo esc_html($item['meta']); ?></div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- 2.7 Banner "Đặt lịch lái thử tại showroom GoBike" -->
                        <div class="gb-vd-booking-card" id="dat-lich">
                            <h3 class="gb-vd-booking-title">Đặt lịch lái thử tại showroom GoBike</h3>
                            
                            <div class="gb-vd-booking-img-wrap">
                                <div class="gb-vd-zoom-wrap">
                                    <img src="https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg" alt="Showroom GoBike">
                                </div>
                            </div>

                            <div class="gb-vd-booking-checklist">
                                <div class="gb-vd-booking-check-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span>Trải nghiệm thực tế</span>
                                </div>
                                <div class="gb-vd-booking-check-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span>Được tư vấn 1-1</span>
                                </div>
                                <div class="gb-vd-booking-check-item">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                    <span>Hỗ trợ chọn xe phù hợp</span>
                                </div>
                            </div>

                            <a href="<?php echo esc_url(home_url('/showroom/')); ?>" class="gb-vd-btn-booking-cta">Đặt lịch ngay &rarr;</a>
                        </div>

                    </aside>

                </div>

                <!-- 3. SECTION "VIDEO LIÊN QUAN" (Ảnh 2) -->
                <section class="gb-vd-related-section">
                    <div class="gb-vd-sec-header">
                        <h2>Video liên quan</h2>
                        <a href="<?php echo esc_url(home_url('/video-review/')); ?>" class="gb-vd-link-all">Xem tất cả &rarr;</a>
                    </div>

                    <div class="gb-vd-related-grid">
                        <?php foreach ($related_videos as $rel): ?>
                            <a href="<?php echo esc_url($rel['url']); ?>" class="gb-vd-rel-card">
                                <!-- Thumbnail 16:9 với hiệu ứng hover zoom ảnh -->
                                <div class="gb-vd-rel-thumb">
                                    <div class="gb-vd-zoom-wrap">
                                        <img src="<?php echo esc_url($rel['thumb']); ?>" alt="<?php echo esc_attr($rel['title']); ?>">
                                    </div>
                                    <span class="gb-vd-duration-badge"><?php echo esc_html($rel['duration']); ?></span>
                                </div>
                                <h3 class="gb-vd-rel-title"><?php echo esc_html($rel['title']); ?></h3>
                                <div class="gb-vd-rel-meta"><?php echo esc_html($rel['meta']); ?></div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>

                <!-- 4. KHU VỰC 2 CỘT PHÍA DƯỚI: BÌNH LUẬN & CÂU HỎI THƯỜNG GẶP (Ảnh 2) -->
                <div class="gb-vd-bottom-grid">
                    
                    <!-- 4.1 Cột Trái: Bình Luận (Dữ liệu động theo bài viết) -->
                    <div class="gb-vd-comments-box">
                        <h3 class="gb-vd-comments-header">Bình luận (<?php echo esc_html($comments_count); ?>)</h3>

                        <!-- Form nhập bình luận hoạt động thật (gửi vào cơ sở dữ liệu WordPress) -->
                        <form action="<?php echo esc_url(site_url('/wp-comments-post.php')); ?>" method="post" class="gb-vd-comment-form">
                            <input type="hidden" name="comment_post_ID" value="<?php echo esc_attr($current_id); ?>">
                            <input type="hidden" name="comment_parent" value="0">
                            
                            <div class="gb-vd-comment-form-mock">
                                <div class="gb-vd-user-avatar-mock">
                                    <?php 
                                    if (is_user_logged_in()) {
                                        echo get_avatar(get_current_user_id(), 38);
                                    } else {
                                        echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>';
                                    }
                                    ?>
                                </div>
                                <div class="gb-vd-comment-input-wrap">
                                    <input type="text" name="comment" placeholder="Viết bình luận..." required class="gb-vd-comment-input">
                                    <button type="submit" class="gb-vd-btn-send-comment" aria-label="Gửi bình luận">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                                    </button>
                                </div>
                            </div>

                            <?php if (!is_user_logged_in()): ?>
                                <input type="hidden" name="author" value="Khách hàng GoBike">
                                <input type="hidden" name="email" value="guest_<?php echo time(); ?>@gobike.vn">
                            <?php endif; ?>
                        </form>

                        <!-- Danh sách bình luận -->
                        <div class="gb-vd-comments-list">
                            <?php if ($has_real_comments): ?>
                                <!-- 1. Hiển thị bình luận THỰC TẾ của bài viết này -->
                                <?php foreach ($real_comments as $cmt): ?>
                                    <div class="gb-vd-comment-item">
                                        <div class="gb-vd-cmt-avatar">
                                            <?php echo get_avatar($cmt, 36); ?>
                                        </div>
                                        <div class="gb-vd-cmt-content">
                                            <div class="gb-vd-cmt-top">
                                                <span class="gb-vd-cmt-author"><?php echo esc_html(get_comment_author($cmt)); ?></span>
                                                <span class="gb-vd-cmt-time"><?php echo human_time_diff(get_comment_time('U', true, $cmt), current_time('timestamp')) . ' trước'; ?></span>
                                            </div>
                                            <div class="gb-vd-cmt-text"><?php echo wpautop(esc_html($cmt->comment_content)); ?></div>
                                            <div class="gb-vd-cmt-actions">
                                                <span class="gb-vd-cmt-action-btn">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                                                    <span>Thích</span>
                                                </span>
                                                <span class="gb-vd-cmt-action-btn">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                                    <span>Trả lời</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- 2. Hiển thị bình luận mẫu chuẩn thiết kế khi bài viết chưa có bình luận -->
                                <?php foreach ($comments_sample as $cmt): ?>
                                    <div class="gb-vd-comment-item">
                                        <div class="gb-vd-cmt-avatar">
                                            <img src="<?php echo esc_url($cmt['avatar']); ?>" alt="<?php echo esc_attr($cmt['name']); ?>">
                                        </div>
                                        <div class="gb-vd-cmt-content">
                                            <div class="gb-vd-cmt-top">
                                                <span class="gb-vd-cmt-author"><?php echo esc_html($cmt['name']); ?></span>
                                                <span class="gb-vd-cmt-time"><?php echo esc_html($cmt['time']); ?></span>
                                            </div>
                                            <p class="gb-vd-cmt-text"><?php echo esc_html($cmt['content']); ?></p>
                                            <div class="gb-vd-cmt-actions">
                                                <span class="gb-vd-cmt-action-btn">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                                                    <span><?php echo esc_html($cmt['likes']); ?></span>
                                                </span>
                                                <span class="gb-vd-cmt-action-btn">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                                                    <span>Trả lời</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <a href="javascript:void(0);" class="gb-vd-more-comments-btn">Xem thêm bình luận</a>
                    </div>

                    <!-- 4.2 Cột Phải: Câu Hỏi Thường GẶP (Accordion hoạt động thật) -->
                    <div class="gb-vd-faq-box">
                        <div class="gb-vd-sec-header">
                            <h3>Câu hỏi thường gặp</h3>
                        </div>

                        <!-- Accordion danh sách câu hỏi đóng mở -->
                        <div class="gb-vd-faq-list">
                            <?php foreach ($faqs as $index => $faq): ?>
                                <details class="gb-vd-faq-item" <?php echo ($index === 0) ? 'open' : ''; ?>>
                                    <summary class="gb-vd-faq-summary">
                                        <span class="gb-vd-faq-question-text"><?php echo esc_html($faq['q']); ?></span>
                                        <span class="gb-vd-faq-plus">+</span>
                                    </summary>
                                    <div class="gb-vd-faq-answer">
                                        <p><?php echo esc_html($faq['a']); ?></p>
                                    </div>
                                </details>
                            <?php endforeach; ?>
                        </div>

                        <!-- Khối "Vẫn còn thắc mắc?" -->
                        <div class="gb-vd-faq-contact-card">
                            <div class="gb-vd-faq-contact-left">
                                <div class="gb-vd-faq-icon-headphone">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#0d7030" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 12a8 8 0 0 1 16 0"></path>
                                        <rect x="2" y="11" width="4" height="7" rx="2"></rect>
                                        <rect x="18" y="11" width="4" height="7" rx="2"></rect>
                                        <path d="M4 18v1a4 4 0 0 0 4 4h2"></path>
                                        <circle cx="11" cy="23" r="1" fill="#0d7030"></circle>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="gb-vd-faq-contact-title">Vẫn còn thắc mắc?</h4>
                                    <p class="gb-vd-faq-contact-sub">Đội ngũ GoBike luôn sẵn sàng hỗ trợ bạn!</p>
                                </div>
                            </div>
                            <div class="gb-vd-faq-contact-right">
                                <a href="tel:0944988699" class="gb-vd-btn-phone gb-vd-faq-btn">
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                                    <span>0944 988 699</span>
                                </a>
                                <a href="https://zalo.me/0944988699" target="_blank" class="gb-vd-btn-zalo gb-vd-faq-btn">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12c0 1.82.49 3.53 1.34 5L2 22l5.18-1.32C8.61 21.49 10.26 22 12 22c5.52 0 10-4.48 10-10S17.52 2 12 2z"/></svg>
                                    <span>Nhắn Zalo</span>
                                </a>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
