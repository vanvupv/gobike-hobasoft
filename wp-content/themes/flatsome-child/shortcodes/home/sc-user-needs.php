<?php
/**
 * Shortcode: Khối Phân Loại Theo Nhu Cầu Sử Dụng (GoBike User Needs)
 * Cú pháp dùng trong Flatsome UX Builder: [gobike_user_needs]
 * Tự động hiển thị 5 card trên Desktop, tự động trượt Slide trên Mobile/Tablet.
 */

if (!defined('ABSPATH')) {
    exit;
}

function gobike_render_user_needs_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'class' => '',
    ), $atts, 'gobike_user_needs');

    // Danh sách 5 nhu cầu sử dụng chuẩn thiết kế
    $items = array(
        array(
            'title' => 'Đi làm hàng ngày',
            'desc'  => 'Gọn nhẹ, linh hoạt',
            'link'  => home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/?nhu-cau=di-lam-di-hoc'),
            'type'  => 'svg',
            'img'   => '',
        ),
        array(
            'title' => 'Học sinh – Sinh viên',
            'desc'  => 'Bền bỉ, dễ sử dụng',
            'link'  => home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/?nhu-cau=hoc-sinh-sinh-vien'),
            'type'  => 'img',
            'img'   => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/2375.jpg',
        ),
        array(
            'title' => 'Thể thao – Khám phá',
            'desc'  => 'Mạnh mẽ, chinh phục',
            'link'  => home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/xe-dap-tro-luc-dia-hinh/'),
            'type'  => 'img',
            'img'   => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/Xe-dap-the-thao-tro-luc-dien-Phoenix-999.webp',
        ),
        array(
            'title' => 'Du lịch – Dã ngoại',
            'desc'  => 'Tự do, trải nghiệm',
            'link'  => home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/?nhu-cau=du-lich-da-ngoai'),
            'type'  => 'img',
            'img'   => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-2.jpg',
        ),
        array(
            'title' => 'Người lớn tuổi',
            'desc'  => 'An toàn, thoải mái',
            'link'  => home_url('/danh-muc-san-pham/xe-dap-tro-luc-dien/?nhu-cau=cho-nguoi-lon-tuoi'),
            'type'  => 'img',
            'img'   => 'https://gobike.demoweb360.top/wp-content/uploads/2026/08/sua-pin-lithium-ha-noi-o-dau-uy-tin-va-an-toan-cho-nguoi-dung-2491-1.jpg',
        ),
    );

    ob_start();
    ?>
    <div class="gobike-user-needs-container <?php echo esc_attr($atts['class']); ?>">
        <div class="gobike-needs-track">
            <?php foreach ($items as $item): ?>
                <a href="<?php echo esc_url($item['link']); ?>" class="gobike-need-card">
                    <div class="gobike-need-thumb">
                        <?php if ($item['type'] === 'svg'): ?>
                            <svg class="gobike-need-icon" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="17" cy="46" r="10" stroke="#149d29" stroke-width="3.5" />
                                <circle cx="47" cy="46" r="10" stroke="#149d29" stroke-width="3.5" />
                                <circle cx="17" cy="46" r="3" fill="#149d29" />
                                <circle cx="47" cy="46" r="3" fill="#149d29" />
                                <path d="M17 46L28 27L37 46H17Z" stroke="#149d29" stroke-width="3.5" stroke-linejoin="round" />
                                <path d="M37 46L30 31L47 46" stroke="#149d29" stroke-width="3.5" stroke-linejoin="round" />
                                <path d="M28 27L38 27L43 19" stroke="#149d29" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
                                <circle cx="34" cy="14" r="4.5" fill="#149d29" />
                                <path d="M41 19H48" stroke="#149d29" stroke-width="3.5" stroke-linecap="round" />
                            </svg>
                        <?php else: ?>
                            <img src="<?php echo esc_url($item['img']); ?>" alt="<?php echo esc_attr($item['title']); ?>" loading="lazy" />
                        <?php endif; ?>
                    </div>
                    <div class="gobike-need-info">
                        <h4 class="gobike-need-title"><?php echo esc_html($item['title']); ?></h4>
                        <span class="gobike-need-desc"><?php echo esc_html($item['desc']); ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('gobike_user_needs', 'gobike_render_user_needs_shortcode');
