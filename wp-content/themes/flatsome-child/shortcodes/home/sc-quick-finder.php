<?php
/**
 * Shortcode: [gobike_quick_finder] / [gobike_filter_60s]
 * 
 * Section: BỘ LỌC NHANH & TƯ VẤN CHỌN XE 60 GIÂY
 * Chức năng:
 * 1. Product Discovery: Thanh lọc nhanh 5 tiêu chí (Nhu cầu, Mức giá, Dòng xe, Quãng đường, Thương hiệu)
 * 2. Lead Generation: Modal Popup "Tư vấn chọn xe trong 60 giây" (được in ở wp_footer để tránh lỗi Stacking Context)
 * 3. Quản lý Lead trong WordPress: Tự động lưu vào CPT customer_lead và gửi email thông báo cho Admin
 * 
 * @package Flatsome-Child
 */

if (!defined('ABSPATH')) {
    exit;
}

/* ============================================================================
 * 1. ĐĂNG KÝ CUSTOM POST TYPE "CUSTOMER_LEAD" ĐỂ LƯU THÔNG TIN KHÁCH HÀNG
 * ============================================================================
 */
add_action('init', 'gobike_register_customer_lead_cpt');
function gobike_register_customer_lead_cpt()
{
    $labels = array(
        'name'               => 'Yêu cầu tư vấn (Leads)',
        'singular_name'      => 'Yêu cầu tư vấn',
        'menu_name'          => 'Tư vấn xe (Leads)',
        'name_admin_bar'     => 'Lead tư vấn mới',
        'add_new'            => 'Thêm lead mới',
        'add_new_item'       => 'Thêm yêu cầu tư vấn mới',
        'new_item'           => 'Yêu cầu tư vấn mới',
        'edit_item'          => 'Xem / Sửa yêu cầu tư vấn',
        'view_item'          => 'Xem yêu cầu',
        'all_items'          => 'Tất cả yêu cầu',
        'search_items'       => 'Tìm kiếm lead',
        'not_found'          => 'Chưa có yêu cầu nào.',
        'not_found_in_trash' => 'Không có yêu cầu nào trong thùng rác.',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => false,
        'rewrite'            => false,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 26,
        'menu_icon'          => 'dashicons-phone',
        'supports'           => array('title'),
    );

    register_post_type('customer_lead', $args);
}

// Thêm các cột thông tin trong bảng danh sách Admin
add_filter('manage_customer_lead_posts_columns', 'gobike_lead_columns');
function gobike_lead_columns($columns)
{
    $new_columns = array(
        'cb'           => $columns['cb'],
        'title'        => 'Khách hàng',
        'lead_phone'   => 'Số điện thoại / Zalo',
        'lead_demand'  => 'Nhu cầu',
        'lead_budget'  => 'Ngân sách',
        'lead_type'    => 'Dòng xe',
        'lead_brand'   => 'Thương hiệu',
        'lead_loc'     => 'Khu vực / Showroom',
        'date'         => 'Thời gian gửi',
    );
    return $new_columns;
}

add_action('manage_customer_lead_posts_custom_column', 'gobike_lead_column_data', 10, 2);
function gobike_lead_column_data($column, $post_id)
{
    switch ($column) {
        case 'lead_phone':
            $phone = get_post_meta($post_id, '_lead_phone', true);
            echo $phone ? '<strong style="color:#149d29;"><a href="tel:' . esc_attr($phone) . '">' . esc_html($phone) . '</a></strong>' : '—';
            break;
        case 'lead_demand':
            $val = get_post_meta($post_id, '_lead_demand', true);
            echo $val ? '<span class="badge" style="background:#e0f2fe;color:#0369a1;padding:3px 8px;border-radius:4px;">' . esc_html($val) . '</span>' : '—';
            break;
        case 'lead_budget':
            $val = get_post_meta($post_id, '_lead_budget', true);
            echo $val ? esc_html($val) : '—';
            break;
        case 'lead_type':
            $val = get_post_meta($post_id, '_lead_type', true);
            echo $val ? esc_html($val) : '—';
            break;
        case 'lead_brand':
            $val = get_post_meta($post_id, '_lead_brand', true);
            echo $val ? esc_html($val) : '—';
            break;
        case 'lead_loc':
            $val = get_post_meta($post_id, '_lead_location', true);
            echo $val ? esc_html($val) : '—';
            break;
    }
}

// Meta box hiển thị chi tiết Lead
add_action('add_meta_boxes', 'gobike_lead_register_meta_box');
function gobike_lead_register_meta_box()
{
    add_meta_box(
        'gobike_lead_details',
        'Chi tiết yêu cầu tư vấn',
        'gobike_lead_meta_box_callback',
        'customer_lead',
        'normal',
        'high'
    );
}

function gobike_lead_meta_box_callback($post)
{
    $name     = get_post_meta($post->ID, '_lead_name', true);
    $phone    = get_post_meta($post->ID, '_lead_phone', true);
    $demand   = get_post_meta($post->ID, '_lead_demand', true);
    $budget   = get_post_meta($post->ID, '_lead_budget', true);
    $type     = get_post_meta($post->ID, '_lead_type', true);
    $range    = get_post_meta($post->ID, '_lead_range', true);
    $brand    = get_post_meta($post->ID, '_lead_brand', true);
    $location = get_post_meta($post->ID, '_lead_location', true);
    $note     = get_post_meta($post->ID, '_lead_note', true);
    ?>
    <table class="form-table" style="max-width: 800px;">
        <tr>
            <th style="width: 200px;"><strong>Họ và tên:</strong></th>
            <td><?php echo esc_html($name ?: $post->post_title); ?></td>
        </tr>
        <tr>
            <th><strong>Số điện thoại / Zalo:</strong></th>
            <td><a href="tel:<?php echo esc_attr($phone); ?>" style="font-size: 16px; font-weight: bold; color: #149d29;"><?php echo esc_html($phone); ?></a></td>
        </tr>
        <tr>
            <th><strong>Nhu cầu sử dụng:</strong></th>
            <td><strong><?php echo esc_html($demand ?: '—'); ?></strong></td>
        </tr>
        <tr>
            <th><strong>Ngân sách dự kiến:</strong></th>
            <td><?php echo esc_html($budget ?: '—'); ?></td>
        </tr>
        <tr>
            <th><strong>Dòng xe quan tâm:</strong></th>
            <td><?php echo esc_html($type ?: '—'); ?></td>
        </tr>
        <tr>
            <th><strong>Quãng đường / ngày:</strong></th>
            <td><?php echo esc_html($range ?: '—'); ?></td>
        </tr>
        <tr>
            <th><strong>Thương hiệu quan tâm:</strong></th>
            <td><?php echo esc_html($brand ?: '—'); ?></td>
        </tr>
        <tr>
            <th><strong>Khu vực / Showroom:</strong></th>
            <td><?php echo esc_html($location ?: '—'); ?></td>
        </tr>
        <tr>
            <th><strong>Ghi chú của khách:</strong></th>
            <td><div style="background: #f8fafc; padding: 12px; border-radius: 6px; border: 1px solid #e2e8f0;"><?php echo nl2br(esc_html($note ?: 'Không có ghi chú')); ?></div></td>
        </tr>
        <tr>
            <th><strong>Thời gian gửi:</strong></th>
            <td><?php echo esc_html(get_the_date('d/m/Y H:i:s', $post)); ?></td>
        </tr>
    </table>
    <?php
}


/* ============================================================================
 * 2. AJAX XỬ LÝ NHẬN FORM LEAD TỪ MODAL POPUP
 * ============================================================================
 */
add_action('wp_ajax_gobike_submit_lead_60s', 'gobike_ajax_submit_lead_60s');
add_action('wp_ajax_nopriv_gobike_submit_lead_60s', 'gobike_ajax_submit_lead_60s');

function gobike_ajax_submit_lead_60s()
{
    check_ajax_referer('gobike_lead_nonce', 'security');

    $name     = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
    $phone    = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $demand   = isset($_POST['demand']) ? sanitize_text_field($_POST['demand']) : '';
    $budget   = isset($_POST['budget']) ? sanitize_text_field($_POST['budget']) : '';
    $type     = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';
    $range    = isset($_POST['range']) ? sanitize_text_field($_POST['range']) : '';
    $brand    = isset($_POST['brand']) ? sanitize_text_field($_POST['brand']) : '';
    $location = isset($_POST['location']) ? sanitize_text_field($_POST['location']) : '';
    $note     = isset($_POST['note']) ? sanitize_textarea_field($_POST['note']) : '';

    if (empty($name) || empty($phone)) {
        wp_send_json_error(array('message' => 'Vui lòng nhập đầy đủ Họ tên và Số điện thoại / Zalo.'));
    }

    // Làm sạch số điện thoại
    $clean_phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($clean_phone) < 9 || strlen($clean_phone) > 11) {
        wp_send_json_error(array('message' => 'Số điện thoại không hợp lệ, vui lòng kiểm tra lại.'));
    }

    // Tạo bài viết Lead trong CPT customer_lead
    $post_title = $name . ' - ' . $phone;
    $post_id = wp_insert_post(array(
        'post_title'   => $post_title,
        'post_type'    => 'customer_lead',
        'post_status'  => 'publish',
    ));

    if (is_wp_error($post_id) || !$post_id) {
        wp_send_json_error(array('message' => 'Có lỗi xảy ra khi lưu dữ liệu. Vui lòng thử lại sau.'));
    }

    // Lưu meta data
    update_post_meta($post_id, '_lead_name', $name);
    update_post_meta($post_id, '_lead_phone', $phone);
    update_post_meta($post_id, '_lead_demand', $demand);
    update_post_meta($post_id, '_lead_budget', $budget);
    update_post_meta($post_id, '_lead_type', $type);
    update_post_meta($post_id, '_lead_range', $range);
    update_post_meta($post_id, '_lead_brand', $brand);
    update_post_meta($post_id, '_lead_location', $location);
    update_post_meta($post_id, '_lead_note', $note);
    update_post_meta($post_id, '_lead_created_at', current_time('mysql'));

    // Gửi email thông báo cho Quản trị viên
    $admin_email = get_option('admin_email');
    $subject = '[GoBike] Khách hàng yêu cầu tư vấn xe 60s - ' . $name . ' (' . $phone . ')';

    $body  = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">';
    $body .= '  <div style="background: #149d29; color: #ffffff; padding: 20px; text-align: center;">';
    $body .= '    <h2 style="margin: 0; font-size: 20px;">YÊU CẦU TƯ VẤN CHỌN XE 60 GIÂY</h2>';
    $body .= '    <p style="margin: 5px 0 0 0; opacity: 0.9; font-size: 14px;">Hệ thống GoBike tiếp nhận yêu cầu mới</p>';
    $body .= '  </div>';
    $body .= '  <div style="padding: 24px;">';
    $body .= '    <h3 style="color: #149d29; margin-top: 0; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px;">Thông tin khách hàng</h3>';
    $body .= '    <p><strong>Họ và tên:</strong> ' . esc_html($name) . '</p>';
    $body .= '    <p><strong>Số điện thoại / Zalo:</strong> <a href="tel:' . esc_attr($phone) . '" style="color: #149d29; font-weight: bold; font-size: 16px;">' . esc_html($phone) . '</a></p>';
    $body .= '    <p><strong>Khu vực / Showroom:</strong> ' . esc_html($location ?: 'Chưa chọn') . '</p>';
    $body .= '    <h3 style="color: #149d29; margin-top: 24px; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px;">Nhu cầu chọn xe</h3>';
    $body .= '    <p><strong>Nhu cầu sử dụng:</strong> <span style="background:#e0f2fe;color:#0369a1;padding:2px 8px;border-radius:4px;font-weight:bold;">' . esc_html($demand ?: 'Chưa rõ') . '</span></p>';
    $body .= '    <p><strong>Ngân sách dự kiến:</strong> ' . esc_html($budget ?: 'Chưa chọn') . '</p>';
    $body .= '    <p><strong>Dòng xe quan tâm:</strong> ' . esc_html($type ?: 'Tất cả') . '</p>';
    $body .= '    <p><strong>Quãng đường / ngày:</strong> ' . esc_html($range ?: 'Tất cả') . '</p>';
    $body .= '    <p><strong>Thương hiệu quan tâm:</strong> ' . esc_html($brand ?: 'Tất cả') . '</p>';
    if (!empty($note)) {
        $body .= '    <h3 style="color: #149d29; margin-top: 24px; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px;">Ghi chú của khách</h3>';
        $body .= '    <p style="background: #f8fafc; padding: 12px; border-radius: 6px; border-left: 4px solid #149d29;">' . nl2br(esc_html($note)) . '</p>';
    }
    $body .= '    <p style="margin-top: 24px; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 12px;">Thời gian gửi: ' . current_time('d/m/Y H:i:s') . '</p>';
    $body .= '  </div>';
    $body .= '</div>';

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $admin_email . '>',
    );

    @wp_mail($admin_email, $subject, $body, $headers);

    wp_send_json_success(array(
        'message' => 'Gửi yêu cầu tư vấn thành công! Chuyên viên GoBike sẽ liên hệ với bạn trong thời gian sớm nhất.',
    ));
}


/* ============================================================================
 * 3. HÀM RENDER SHORTCODE [gobike_quick_finder]
 * ============================================================================
 */
add_shortcode('gobike_quick_finder', 'gobike_render_quick_finder_shortcode');
add_shortcode('gobike_filter_60s', 'gobike_render_quick_finder_shortcode');

function gobike_render_quick_finder_shortcode($atts)
{
    // Đánh dấu để in Modal ở wp_footer
    global $gobike_has_quick_finder;
    $gobike_has_quick_finder = true;

    $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/cua-hang/');

    ob_start();
    ?>
    <!-- SECTION QUICK FINDER / TƯ VẤN CHỌN XE 60 GIÂY -->
    <div class="gobike-quick-finder-wrap">
        <div class="gqf-container">
            <!-- TOP AREA: INTRO & 4 STEPS & CTA -->
            <div class="gqf-top-area">
                <!-- Cột trái: Intro -->
                <div class="gqf-intro">
                    <div class="gqf-badge">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                        </svg>
                        <span>Chỉ mất 60 giây</span>
                    </div>
                    <h2 class="gqf-title"><span class="gqf-title-yellow">60 GIÂY</span><br>CHỌN ĐÚNG XE</h2>
                    <p class="gqf-desc">Trả lời vài câu hỏi đơn giản, GoBike sẽ gợi ý những mẫu xe phù hợp nhất dành cho bạn.</p>
                </div>

                <!-- Cột giữa: 4 Bước trực quan -->
                <div class="gqf-steps">
                    <!-- Bước 1 -->
                    <div class="gqf-step-item">
                        <span class="gqf-step-num">1</span>
                        <div class="gqf-step-circle">
                            <div class="gqf-step-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </div>
                        </div>
                        <span class="gqf-step-label">Nhu cầu sử dụng</span>
                    </div>

                    <!-- Bước 2 -->
                    <div class="gqf-step-item">
                        <span class="gqf-step-num">2</span>
                        <div class="gqf-step-circle">
                            <div class="gqf-step-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="8" cy="8" r="6"></circle>
                                    <path d="M18.09 10.37A6 6 0 1 1 10.34 18"></path>
                                    <path d="M7 6h1v4"></path>
                                    <path d="M16.7 13.3a6 6 0 0 1 .3 2.7 6 6 0 0 1-6 6"></path>
                                </svg>
                            </div>
                        </div>
                        <span class="gqf-step-label">Ngân sách dự kiến</span>
                    </div>

                    <!-- Bước 3 -->
                    <div class="gqf-step-item">
                        <span class="gqf-step-num">3</span>
                        <div class="gqf-step-circle">
                            <div class="gqf-step-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </div>
                        </div>
                        <span class="gqf-step-label">Phong cách yêu thích</span>
                    </div>

                    <!-- Bước 4 -->
                    <div class="gqf-step-item">
                        <span class="gqf-step-num">4</span>
                        <div class="gqf-step-circle">
                            <div class="gqf-step-icon">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 12 20 22 4 22 4 12"></polyline>
                                    <rect x="2" y="7" width="20" height="5"></rect>
                                    <line x1="12" y1="22" x2="12" y2="7"></line>
                                    <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
                                    <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
                                </svg>
                            </div>
                        </div>
                        <span class="gqf-step-label">Nhận gợi ý ngay</span>
                    </div>
                </div>

                <!-- Cột phải: CTA Button -->
                <div class="gqf-cta-wrap">
                    <button type="button" class="gqf-btn-primary js-open-gqf-modal">
                        <span>BẮT ĐẦU TƯ VẤN 60S</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                    <span class="gqf-cta-subtext">Nhanh • Dễ chọn • Cá nhân hoá</span>
                </div>
            </div>

            <!-- BOTTOM AREA: QUICK FILTER BAR (5 BỘ LỌC) -->
            <div class="gqf-filter-bar">
                <!-- Tag tiêu đề bộ lọc -->
                <div class="gqf-filter-tag">
                    <div class="gqf-filter-tag-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                    <div class="gqf-filter-tag-text">
                        <strong>Lọc nhanh tìm xe ngay</strong>
                        <span>Chọn tiêu chí phù hợp với bạn</span>
                    </div>
                </div>

                <!-- 5 Dropdowns bộ lọc -->
                <div class="gqf-dropdowns-group">
                    <!-- 1. Nhu cầu -->
                    <div class="gqf-select-box">
                        <svg class="gqf-box-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="18.5" cy="17.5" r="3.5"></circle>
                            <circle cx="5.5" cy="17.5" r="3.5"></circle>
                            <circle cx="15" cy="5" r="1"></circle>
                            <path d="M12 17.5V14l-3-3 4-3 2 3h2"></path>
                        </svg>
                        <select id="gqf_nhu_cau" class="gqf-select">
                            <option value="">Nhu cầu</option>
                            <option value="di-lam">Đi làm</option>
                            <option value="di-hoc">Đi học</option>
                            <option value="dao-pho">Dạo phố / Đi chơi</option>
                            <option value="the-thao">Thể thao</option>
                            <option value="du-lich-phuot">Du lịch - Phượt</option>
                            <option value="cho-hang">Chở hàng</option>
                        </select>
                        <span class="gqf-arrow">∨</span>
                    </div>

                    <!-- 2. Mức giá -->
                    <div class="gqf-select-box">
                        <svg class="gqf-box-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                            <line x1="2" y1="10" x2="22" y2="10"></line>
                        </svg>
                        <select id="gqf_muc_gia" class="gqf-select">
                            <option value="">Mức giá</option>
                            <option value="0-15000000">Dưới 15 triệu</option>
                            <option value="15000000-20000000">15 – 20 triệu</option>
                            <option value="20000000-30000000">20 – 30 triệu</option>
                            <option value="30000000-40000000">30 – 40 triệu</option>
                            <option value="40000000-999999999">Trên 40 triệu</option>
                        </select>
                        <span class="gqf-arrow">∨</span>
                    </div>

                    <!-- 3. Dòng xe -->
                    <div class="gqf-select-box">
                        <svg class="gqf-box-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="18.5" cy="17.5" r="3.5"></circle>
                            <circle cx="5.5" cy="17.5" r="3.5"></circle>
                            <path d="M15 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-3 11.5V14l-3-3 4-3 2 3h2"></path>
                        </svg>
                        <select id="gqf_dong_xe" class="gqf-select">
                            <option value="">Dòng xe</option>
                            <option value="xe-dap-tro-luc-dien">Xe đạp trợ lực điện</option>
                            <option value="xe-dap-tro-luc-dien-gap">Xe đạp trợ lực điện gấp</option>
                            <option value="xe-dap-tro-luc-dien-the-thao">Xe đạp thể thao / Địa hình</option>
                            <option value="xe-dap-tro-luc-dien-hoc-sinh">Xe đạp học sinh</option>
                            <option value="xe-may-dien">Xe máy điện</option>
                        </select>
                        <span class="gqf-arrow">∨</span>
                    </div>

                    <!-- 4. Quãng đường -->
                    <div class="gqf-select-box">
                        <svg class="gqf-box-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 19L8 5"></path>
                            <path d="M16 5l4 14"></path>
                            <line x1="12" y1="8" x2="12" y2="10"></line>
                            <line x1="12" y1="14" x2="12" y2="16"></line>
                        </svg>
                        <select id="gqf_quang_duong" class="gqf-select">
                            <option value="">Quãng đường</option>
                            <option value="duoi-20km">Dưới 20 km/ngày</option>
                            <option value="20-40km">20 – 40 km/ngày</option>
                            <option value="40-60km">40 – 60 km/ngày</option>
                            <option value="tren-60km">Trên 60 km/ngày</option>
                        </select>
                        <span class="gqf-arrow">∨</span>
                    </div>

                    <!-- 5. Thương hiệu -->
                    <div class="gqf-select-box">
                        <svg class="gqf-box-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                            <polyline points="2 17 12 22 22 17"></polyline>
                            <polyline points="2 12 12 17 22 12"></polyline>
                        </svg>
                        <select id="gqf_thuong_hieu" class="gqf-select">
                            <option value="">Thương hiệu</option>
                            <option value="ado">ADO</option>
                            <option value="phoenix">Phoenix</option>
                            <option value="samebike">Samebike</option>
                            <option value="engwe">Engwe</option>
                            <option value="himo">Himo</option>
                            <option value="aimos">Aimos</option>
                        </select>
                        <span class="gqf-arrow">∨</span>
                    </div>
                </div>

                <!-- 2 CTA Action Buttons -->
                <div class="gqf-actions-group">
                    <!-- 1. Lọc nhanh (Redirect Shop) -->
                    <button type="button" class="gqf-btn-loc-nhanh" id="js-btn-loc-nhanh">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="4" y1="21" x2="4" y2="14"></line>
                            <line x1="4" y1="10" x2="4" y2="3"></line>
                            <line x1="12" y1="21" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12" y2="3"></line>
                            <line x1="20" y1="21" x2="20" y2="16"></line>
                            <line x1="20" y1="12" x2="20" y2="3"></line>
                            <line x1="1" y1="14" x2="7" y2="14"></line>
                            <line x1="9" y1="8" x2="15" y2="8"></line>
                            <line x1="17" y1="16" x2="23" y2="16"></line>
                        </svg>
                        <span>LỌC NHANH</span>
                    </button>

                    <!-- 2. Tìm xe phù hợp (Mở Modal) -->
                    <button type="button" class="gqf-btn-tim-xe js-open-gqf-modal">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <span>TÌM XE PHÙ HỢP →</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}


/* ============================================================================
 * 4. RENDER MODAL POPUP Ở WP_FOOTER ĐỂ HOÀN TOÀN TRÁNH BỊ ĐÈ STACKING CONTEXT
 * ============================================================================
 */
add_action('wp_footer', 'gobike_render_quick_finder_modal_footer', 9999);
function gobike_render_quick_finder_modal_footer()
{
    $ajax_url = admin_url('admin-ajax.php');
    $nonce    = wp_create_nonce('gobike_lead_nonce');
    $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/cua-hang/');
    ?>
    <!-- MODAL POPUP: TƯ VẤN CHỌN XE TRONG 60 GIÂY (ĐẶT Ở GỐC BODY ĐỂ KHÔNG BỊ PHẦN TỬ KHÁC ĐÈ) -->
    <div class="gqf-modal-overlay" id="js-gqf-modal" style="display: none;">
        <div class="gqf-modal-dialog">
            <button type="button" class="gqf-modal-close" id="js-gqf-modal-close">&times;</button>
            
            <div class="gqf-modal-header">
                <h3 class="gqf-modal-title">Tư vấn chọn xe trong 60 giây</h3>
                <p class="gqf-modal-desc">Điền nhanh thông tin để GoBike gợi ý mẫu xe phù hợp nhất cho bạn.</p>
            </div>

            <form id="js-gqf-lead-form" class="gqf-modal-form" method="POST">
                <input type="hidden" name="action" value="gobike_submit_lead_60s">
                <input type="hidden" name="security" value="<?php echo esc_attr($nonce); ?>">
                <input type="hidden" name="type" id="modal_hidden_type" value="">
                <input type="hidden" name="range" id="modal_hidden_range" value="">
                <input type="hidden" name="brand" id="modal_hidden_brand" value="">

                <!-- HÀNG 1: Họ tên & SĐT -->
                <div class="gqf-form-row gqf-form-row-2">
                    <div class="gqf-form-field">
                        <label class="gqf-label">Họ và tên <span class="req">*</span></label>
                        <div class="gqf-input-icon-wrap">
                            <svg class="gqf-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <input type="text" name="name" id="modal_lead_name" class="gqf-input" placeholder="Nhập họ và tên của bạn" required>
                        </div>
                    </div>

                    <div class="gqf-form-field">
                        <label class="gqf-label">Số điện thoại / Zalo <span class="req">*</span></label>
                        <div class="gqf-input-icon-wrap">
                            <svg class="gqf-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                            <input type="tel" name="phone" id="modal_lead_phone" class="gqf-input" placeholder="Nhập số điện thoại hoặc Zalo" required>
                        </div>
                    </div>
                </div>

                <!-- HÀNG 2: Nhu cầu sử dụng (Chip Tags) -->
                <div class="gqf-form-field">
                    <label class="gqf-label">Nhu cầu sử dụng</label>
                    <input type="hidden" name="demand" id="modal_lead_demand" value="Đi làm">
                    <div class="gqf-chip-group" id="js-gqf-demand-chips">
                        <button type="button" class="gqf-chip active" data-val="Đi làm">Đi làm</button>
                        <button type="button" class="gqf-chip" data-val="Đi học">Đi học</button>
                        <button type="button" class="gqf-chip" data-val="Dạo phố">Dạo phố</button>
                        <button type="button" class="gqf-chip" data-val="Thể thao">Thể thao</button>
                        <button type="button" class="gqf-chip" data-val="Du lịch - Phượt">Du lịch - Phượt</button>
                        <button type="button" class="gqf-chip" data-val="Chở hàng">Chở hàng</button>
                    </div>
                </div>

                <!-- HÀNG 3: Ngân sách & Khu vực -->
                <div class="gqf-form-row gqf-form-row-2">
                    <div class="gqf-form-field">
                        <label class="gqf-label">Ngân sách dự kiến</label>
                        <div class="gqf-input-icon-wrap">
                            <svg class="gqf-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"></path>
                                <line x1="12" y1="6" x2="12" y2="8"></line>
                                <line x1="12" y1="16" x2="12" y2="18"></line>
                            </svg>
                            <select name="budget" id="modal_lead_budget" class="gqf-input gqf-select-field">
                                <option value="">Chọn khoảng ngân sách</option>
                                <option value="Dưới 15 triệu">Dưới 15 triệu</option>
                                <option value="15 – 20 triệu">15 – 20 triệu</option>
                                <option value="20 – 30 triệu">20 – 30 triệu</option>
                                <option value="30 – 40 triệu">30 – 40 triệu</option>
                                <option value="Trên 40 triệu">Trên 40 triệu</option>
                            </select>
                        </div>
                    </div>

                    <div class="gqf-form-field">
                        <label class="gqf-label">Khu vực / showroom gần bạn</label>
                        <div class="gqf-input-icon-wrap">
                            <svg class="gqf-input-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            <select name="location" id="modal_lead_location" class="gqf-input gqf-select-field">
                                <option value="">Chọn tỉnh/thành phố</option>
                                <option value="Hà Nội">Hà Nội</option>
                                <option value="TP. Hồ Chí Minh">TP. Hồ Chí Minh</option>
                                <option value="Đà Nẵng">Đà Nẵng</option>
                                <option value="Hải Phòng">Hải Phòng</option>
                                <option value="Cần Thơ">Cần Thơ</option>
                                <option value="Bình Dương">Bình Dương</option>
                                <option value="Đồng Nai">Đồng Nai</option>
                                <option value="Tỉnh thành khác">Tỉnh thành khác</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- HÀNG 4: Ghi chú thêm -->
                <div class="gqf-form-field">
                    <label class="gqf-label">Ghi chú thêm (không bắt buộc)</label>
                    <div class="gqf-input-icon-wrap" style="align-items: flex-start;">
                        <svg class="gqf-input-icon" style="margin-top: 10px;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        <textarea name="note" id="modal_lead_note" class="gqf-input gqf-textarea" rows="2" placeholder="Ví dụ: màu sắc yêu thích, chiều cao, dòng xe đang quan tâm..."></textarea>
                    </div>
                </div>

                <!-- CAM KẾT BẢO MẬT -->
                <div class="gqf-form-security">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#149d29" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    <span>Bảo mật thông tin • Tư vấn nhanh • Miễn phí</span>
                </div>

                <!-- THÔNG BÁO LỖI / THÀNH CÔNG -->
                <div id="js-gqf-msg" class="gqf-form-message" style="display: none;"></div>

                <!-- HÀNG NÚT BẤM -->
                <div class="gqf-form-actions">
                    <button type="button" class="gqf-btn-cancel" id="js-btn-gqf-cancel">Để sau</button>
                    <button type="submit" class="gqf-btn-submit" id="js-btn-gqf-submit">
                        <span class="btn-text">Nhận tư vấn ngay →</span>
                        <span class="btn-loading" style="display:none;">
                            <svg class="spinner" width="18" height="18" viewBox="0 0 50 50">
                                <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
                            </svg>
                            Đang gửi...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- CSS VÀ JS ĐỒNG BỘ -->
    <style>
    /* ==========================================================================
       GOBIKE QUICK FINDER WRAPPER
       ========================================================================== */
    .gobike-quick-finder-wrap {
        width: 100%;
        margin: 20px 0 25px 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
    }
    .gqf-container {
        background: linear-gradient(135deg, #149d29 0%, #0c6a1b 100%);
        border-radius: 16px;
        padding: 16px 20px 14px 20px;
        color: #ffffff;
        box-shadow: none !important;
        position: relative;
        overflow: hidden;
    }

    /* Pattern chìm trang trí */
    .gqf-container::before {
        content: "";
        position: absolute;
        right: -80px;
        top: -80px;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* TOP AREA: INTRO, 4 STEPS, CTA */
    .gqf-top-area {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 14px;
    }

    /* Cột trái: Intro */
    .gqf-intro {
        flex: 0 0 280px;
    }
    .gqf-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #facc15;
        color: #0f172a;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 20px;
        margin-bottom: 8px;
        letter-spacing: 0.2px;
    }
    .gqf-title {
        font-size: 34px;
        font-weight: 700;
        line-height: 1.08;
        color: #ffffff;
        margin: 0 0 8px 0;
        letter-spacing: -0.5px;
    }
    .gqf-title-yellow {
        color: #facc15;
    }
    .gqf-desc {
        font-size: 16px;
        line-height: 1.45;
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
    }

    /* Cột giữa: 4 Bước */
    .gqf-steps {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        flex: 1;
    }
    .gqf-step-item {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        background: rgba(0, 0, 0, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 14px;
        padding: 14px 10px 10px 10px;
        width: 125px;
        min-height: 125px;
        box-shadow: none !important;
        transition: background 0.2s ease;
    }
    .gqf-step-item:hover {
        background: rgba(0, 0, 0, 0.24);
    }
    .gqf-step-num {
        position: absolute;
        top: 8px;
        left: 8px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #ffffff;
        color: #0f172a;
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: none !important;
    }
    .gqf-step-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 4px 0 8px 0;
        color: #ffffff;
    }
    .gqf-step-icon svg {
        width: 22px;
        height: 22px;
        stroke: #ffffff;
        display: block;
    }
    .gqf-step-label {
        font-size: 13px;
        font-weight: 600;
        color: #ffffff;
        line-height: 1.3;
    }

    /* Cột phải: CTA Button */
    .gqf-cta-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 0 0 220px;
    }
    .gqf-btn-primary {
        width: 100%;
        background: #facc15 !important;
        color: #0f172a !important;
        font-size: 15px !important;
        font-weight: 700 !important;
        padding: 13px 20px !important;
        margin: 0 !important;
        line-height: 1.2 !important;
        border: none !important;
        border-radius: 30px !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        box-shadow: none !important;
        white-space: nowrap !important;
        transition: all 0.2s ease !important;
    }
    .gqf-btn-primary:hover {
        background: #eab308 !important;
        color: #0f172a !important;
    }
    .gqf-btn-primary span {
        font-size: 15px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        line-height: 1.2 !important;
    }
    .gqf-btn-primary svg {
        stroke: #0f172a !important;
    }
    .gqf-cta-subtext {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.85);
        margin-top: 6px;
        white-space: nowrap;
    }

    /* ==========================================================================
       BOTTOM AREA: QUICK FILTER BAR
       ========================================================================== */
    .gqf-filter-bar {
        background: rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 40px;
        padding: 6px 10px 6px 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Nhãn bên trái */
    .gqf-filter-tag {
        display: flex;
        align-items: center;
        gap: 8px;
        padding-right: 12px;
        border-right: 1px solid rgba(255, 255, 255, 0.15);
        flex-shrink: 0;
    }
    .gqf-filter-tag-icon {
        color: #fbbf24;
        display: flex;
        align-items: center;
    }
    .gqf-filter-tag-text {
        display: flex;
        flex-direction: column;
    }
    .gqf-filter-tag-text strong {
        font-size: 12.5px;
        font-weight: 700;
        color: #ffffff;
        white-space: nowrap;
    }
    .gqf-filter-tag-text span {
        font-size: 10px;
        color: rgba(255, 255, 255, 0.7);
        white-space: nowrap;
    }

    /* Nhóm 5 Dropdowns */
    .gqf-dropdowns-group {
        display: flex;
        align-items: center;
        gap: 6px;
        flex: 1;
    }
    .gqf-select-box {
        position: relative;
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 20px;
        padding: 0 10px 0 10px;
        height: 38px;
        flex: 1;
        transition: all 0.2s;
    }
    .gqf-select-box:hover {
        background: rgba(255, 255, 255, 0.18);
        border-color: rgba(255, 255, 255, 0.35);
    }
    .gqf-box-icon {
        color: rgba(255, 255, 255, 0.85);
        margin-right: 6px;
        flex-shrink: 0;
    }
    .gqf-select {
        width: 100%;
        background: transparent;
        border: none;
        color: #ffffff;
        font-size: 12.5px;
        font-weight: 500;
        outline: none;
        cursor: pointer;
        padding-right: 14px;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        /*  */
        padding: 0px;
        margin: 0px;
    }
    .gqf-select option {
        background: #149d29;
        color: #ffffff;
    }
    .gqf-arrow {
        position: absolute;
        right: 10px;
        font-size: 11px;
        color: rgba(255, 255, 255, 0.7);
        pointer-events: none;
    }

    /* Nhóm 2 Nút CTA */
    .gqf-actions-group {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }
    .gqf-btn-loc-nhanh {
        background: transparent !important;
        border: 1px solid rgba(255, 255, 255, 0.5) !important;
        color: #ffffff !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        height: 38px !important;
        padding: 0 14px !important;
        margin: 0 !important;
        line-height: 1.2 !important;
        border-radius: 20px !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        transition: all 0.2s !important;
        white-space: nowrap !important;
        box-shadow: none !important;
    }
    .gqf-btn-loc-nhanh:hover {
        background: rgba(255, 255, 255, 0.15) !important;
        border-color: #ffffff !important;
        color: #ffffff !important;
    }
    .gqf-btn-tim-xe {
        background: #facc15 !important;
        border: 1px solid #facc15 !important;
        color: #0f172a !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        height: 38px !important;
        padding: 0 16px !important;
        margin: 0 !important;
        line-height: 1.2 !important;
        border-radius: 20px !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        transition: all 0.2s !important;
        white-space: nowrap !important;
        box-shadow: none !important;
    }
    .gqf-btn-tim-xe:hover {
        background: #eab308 !important;
        border-color: #eab308 !important;
        color: #0f172a !important;
    }

    /* ==========================================================================
       MODAL POPUP STYLES - ÁP DỤNG Z-INDEX VÀ FIXED GỐC ĐỂ KHÔNG BỊ ĐÈ
       ========================================================================== */
    .gqf-modal-overlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background: rgba(15, 23, 42, 0.72) !important;
        backdrop-filter: blur(5px) !important;
        -webkit-backdrop-filter: blur(5px) !important;
        z-index: 999999999 !important;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        animation: gqfFadeIn 0.2s ease-out;
    }
    @keyframes gqfFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .gqf-modal-dialog {
        background: #ffffff !important;
        width: 100% !important;
        max-width: 580px !important;
        border-radius: 16px !important;
        padding: 28px 30px 24px 30px !important;
        position: relative !important;
        z-index: 1000000000 !important;
        box-shadow: none !important;
        animation: gqfSlideUp 0.25s ease-out;
    }
    @keyframes gqfSlideUp {
        from { transform: translateY(15px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .gqf-modal-close {
        position: absolute;
        top: 14px;
        right: 16px;
        background: transparent;
        border: none;
        font-size: 26px;
        color: #94a3b8;
        cursor: pointer;
        line-height: 1;
        padding: 4px 8px;
        border-radius: 50%;
        transition: color 0.15s;
    }
    .gqf-modal-close:hover {
        color: #0f172a;
    }
    .gqf-modal-header {
        text-align: center;
        margin-bottom: 20px;
    }
    .gqf-modal-title {
        font-size: 21px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 5px 0;
    }
    .gqf-modal-desc {
        font-size: 13.5px;
        color: #64748b;
        margin: 0;
    }

    /* Form Fields */
    .gqf-modal-form {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }
    .gqf-form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }
    .gqf-form-field {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .gqf-label {
        font-size: 12px;
        font-weight: 600;
        color: #334155;
        margin: 0;
    }
    .gqf-label .req {
        color: #ef4444;
    }
    .gqf-input-icon-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .gqf-input-icon {
        position: absolute;
        left: 12px;
        color: #94a3b8;
        pointer-events: none;
    }
    .gqf-input {
        width: 100%;
        height: 40px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 0 12px 0 38px;
        font-size: 13.5px;
        color: #1e293b;
        background: #ffffff;
        outline: none;
        transition: border-color 0.2s;
    }
    .gqf-input:focus {
        border-color: #149d29;
        box-shadow: none !important;
    }
    .gqf-select-field {
        cursor: pointer;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    .gqf-textarea {
        height: auto;
        padding-top: 8px;
        resize: none;
    }

    /* Chips Group */
    .gqf-chip-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .gqf-chip {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #475569;
        font-size: 12.5px;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.18s;
    }
    .gqf-chip:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .gqf-chip.active {
        background: #ecfdf5;
        border-color: #149d29;
        color: #149d29;
        box-shadow: none !important;
    }

    /* Security Notice */
    .gqf-form-security {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 12px;
        color: #149d29;
        font-weight: 500;
        margin-top: 2px;
    }

    /* Message Alert */
    .gqf-form-message {
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 13px;
        line-height: 1.4;
    }
    .gqf-form-message.success {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #149d29;
    }
    .gqf-form-message.error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    /* Buttons Action */
    .gqf-form-actions {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 12px;
        margin-top: 4px;
    }
    .gqf-btn-cancel {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        color: #64748b;
        font-size: 14px;
        font-weight: 600;
        height: 44px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .gqf-btn-cancel:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .gqf-btn-submit {
        background: #149d29;
        border: none;
        color: #ffffff;
        font-size: 14px;
        font-weight: 700;
        height: 44px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: none !important;
        transition: all 0.2s;
    }
    .gqf-btn-submit:hover {
        background: #0e701d;
        box-shadow: none !important;
        color: #ffffff;
    }
    .gqf-btn-submit:disabled {
        opacity: 0.65;
        cursor: not-allowed;
    }

    /* Loading Spinner */
    .spinner {
        animation: rotate 2s linear infinite;
    }
    .spinner .path {
        stroke: #ffffff;
        stroke-linecap: round;
        animation: dash 1.5s ease-in-out infinite;
    }
    @keyframes rotate {
        100% { transform: rotate(360deg); }
    }
    @keyframes dash {
        0% { stroke-dasharray: 1, 150; stroke-dashoffset: 0; }
        50% { stroke-dasharray: 90, 150; stroke-dashoffset: -35; }
        100% { stroke-dasharray: 90, 150; stroke-dashoffset: -124; }
    }

    /* ==========================================================================
       RESPONSIVE DESIGN (TABLET & MOBILE)
       ========================================================================== */
    @media (max-width: 1024px) {
        .gqf-top-area {
            flex-wrap: wrap;
        }
        .gqf-intro {
            flex: 1 1 100%;
            text-align: center;
        }
        .gqf-badge {
            margin: 0 auto 8px auto;
        }
        .gqf-steps {
            order: 2;
            flex: 1 1 100%;
            gap: 16px;
        }
        .gqf-cta-wrap {
            order: 3;
            flex: 1 1 100%;
            max-width: 280px;
            margin: 0 auto;
        }
        .gqf-filter-bar {
            flex-direction: column;
            border-radius: 16px;
            padding: 12px;
            gap: 10px;
        }
        .gqf-filter-tag {
            border-right: none;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            padding-bottom: 8px;
            width: 100%;
            justify-content: center;
        }
        .gqf-dropdowns-group {
            flex-wrap: wrap;
            width: 100%;
            gap: 8px;
        }
        .gqf-select-box {
            flex: 1 1 calc(50% - 4px);
            min-width: 140px;
        }
        .gqf-actions-group {
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .gqf-btn-loc-nhanh, .gqf-btn-tim-xe {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 640px) {
        .gqf-container {
            padding: 18px 14px;
            border-radius: 12px;
        }
        .gqf-title {
            font-size: 22px;
        }
        .gqf-steps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
        }
        .gqf-step-item {
            width: 100%;
            min-height: unset;
            padding: 8px 4px;
        }
        .gqf-step-num {
            width: 16px;
            height: 16px;
            font-size: 10px;
            top: 4px;
            left: 4px;
        }
        .gqf-step-circle {
            width: 36px;
            height: 36px;
            margin: 2px 0 4px 0;
        }
        .gqf-step-icon svg {
            width: 18px;
            height: 18px;
        }
        .gqf-step-label {
            font-size: 10.5px;
        }
        .gqf-select-box {
            flex: 1 1 100%;
        }
        .gqf-form-row-2 {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .gqf-modal-dialog {
            padding: 20px 18px 18px 18px !important;
        }
        .gqf-modal-title {
            font-size: 18px;
        }
        .gqf-chip {
            font-size: 11.5px;
            padding: 5px 10px;
        }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var shopUrl = <?php echo json_encode($shop_url); ?>;
        var ajaxUrl = <?php echo json_encode($ajax_url); ?>;

        var modal = document.getElementById('js-gqf-modal');
        var openBtns = document.querySelectorAll('.js-open-gqf-modal');
        var closeBtn = document.getElementById('js-gqf-modal-close');
        var cancelBtn = document.getElementById('js-btn-gqf-cancel');

        // BẢO ĐẢM MODAL LUÔN NẰM TRỰC TIẾP Ở ROOT BODY ĐỂ TRÁNH BỊ BẤT KỲ SECTION NÀO ĐÈ LÊN
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }

        // 1. XỬ LÝ NÚT "LỌC NHANH" (Chuyển hướng Shop kèm parameters)
        var btnLocNhanh = document.getElementById('js-btn-loc-nhanh');
        if (btnLocNhanh) {
            btnLocNhanh.addEventListener('click', function() {
                var elNhuCau     = document.getElementById('gqf_nhu_cau');
                var elMucGia     = document.getElementById('gqf_muc_gia');
                var elDongXe     = document.getElementById('gqf_dong_xe');
                var elQuangDuong = document.getElementById('gqf_quang_duong');
                var elThuongHieu = document.getElementById('gqf_thuong_hieu');

                var nhuCau     = elNhuCau ? elNhuCau.value : '';
                var mucGia     = elMucGia ? elMucGia.value : '';
                var dongXe     = elDongXe ? elDongXe.value : '';
                var quangDuong = elQuangDuong ? elQuangDuong.value : '';
                var thuongHieu = elThuongHieu ? elThuongHieu.value : '';

                var params = new URLSearchParams();
                if (nhuCau) params.append('nhu_cau', nhuCau);
                if (dongXe) params.append('dong_xe', dongXe);
                if (quangDuong) params.append('quang_duong', quangDuong);
                if (thuongHieu) params.append('thuong_hieu', thuongHieu);

                if (mucGia) {
                    var parts = mucGia.split('-');
                    if (parts.length === 2) {
                        params.append('min_price', parts[0]);
                        params.append('max_price', parts[1]);
                    }
                }

                var targetUrl = shopUrl;
                var queryStr = params.toString();
                if (queryStr) {
                    targetUrl += (targetUrl.indexOf('?') !== -1 ? '&' : '?') + queryStr;
                }
                window.location.href = targetUrl;
            });
        }

        // 2. XỬ LÝ MỞ / ĐÓNG MODAL POPUP
        function openModal() {
            if (!modal) return;

            // Đảm bảo phần tử modal nằm ở trực tiếp document.body
            if (modal.parentElement !== document.body) {
                document.body.appendChild(modal);
            }

            // Đồng bộ dữ liệu vừa chọn ngoài thanh filter vào modal
            var elNhuCau     = document.getElementById('gqf_nhu_cau');
            var elMucGia     = document.getElementById('gqf_muc_gia');
            var elDongXe     = document.getElementById('gqf_dong_xe');
            var elQuangDuong = document.getElementById('gqf_quang_duong');
            var elThuongHieu = document.getElementById('gqf_thuong_hieu');

            var nhuCau     = elNhuCau ? elNhuCau.value : '';
            var mucGia     = elMucGia ? elMucGia.value : '';
            var dongXe     = elDongXe ? elDongXe.value : '';
            var quangDuong = elQuangDuong ? elQuangDuong.value : '';
            var thuongHieu = elThuongHieu ? elThuongHieu.value : '';

            // Đồng bộ chips Nhu cầu
            var demandInput = document.getElementById('modal_lead_demand');
            var chips = document.querySelectorAll('#js-gqf-demand-chips .gqf-chip');
            if (nhuCau) {
                var textMap = {
                    'di-lam': 'Đi làm',
                    'di-hoc': 'Đi học',
                    'dao-pho': 'Dạo phố',
                    'the-thao': 'Thể thao',
                    'du-lich-phuot': 'Du lịch - Phượt',
                    'cho-hang': 'Chở hàng'
                };
                var targetText = textMap[nhuCau] || 'Đi làm';
                chips.forEach(function(c) {
                    if (c.getAttribute('data-val') === targetText) {
                        c.classList.add('active');
                        if (demandInput) demandInput.value = targetText;
                    } else {
                        c.classList.remove('active');
                    }
                });
            }

            // Đồng bộ Ngân sách
            var budgetSelect = document.getElementById('modal_lead_budget');
            if (mucGia && budgetSelect) {
                var budgetMap = {
                    '0-15000000': 'Dưới 15 triệu',
                    '15000000-20000000': '15 – 20 triệu',
                    '20000000-30000000': '20 – 30 triệu',
                    '30000000-40000000': '30 – 40 triệu',
                    '40000000-999999999': 'Trên 40 triệu'
                };
                var bVal = budgetMap[mucGia];
                if (bVal) budgetSelect.value = bVal;
            }

            // Gán hidden field cho dòng xe, quãng đường, thương hiệu
            var hiddenType = document.getElementById('modal_hidden_type');
            var hiddenRange = document.getElementById('modal_hidden_range');
            var hiddenBrand = document.getElementById('modal_hidden_brand');
            if (hiddenType) hiddenType.value = dongXe;
            if (hiddenRange) hiddenRange.value = quangDuong;
            if (hiddenBrand) hiddenBrand.value = thuongHieu;

            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            if (!modal) return;
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }

        // Ủy quyền sự kiện mở modal cho các nút
        document.addEventListener('click', function(e) {
            var btn = e.target.closest('.js-open-gqf-modal');
            if (btn) {
                e.preventDefault();
                openModal();
            }
        });

        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

        // Click outside backdrop to close
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
        }

        // Đóng bằng phím ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal && modal.style.display === 'flex') {
                closeModal();
            }
        });

        // 3. CHIPS NHU CẦU TRONG MODAL
        var chipBtns = document.querySelectorAll('#js-gqf-demand-chips .gqf-chip');
        var demandInput = document.getElementById('modal_lead_demand');
        chipBtns.forEach(function(chip) {
            chip.addEventListener('click', function() {
                chipBtns.forEach(function(c) { c.classList.remove('active'); });
                chip.classList.add('active');
                if (demandInput) {
                    demandInput.value = chip.getAttribute('data-val');
                }
            });
        });

        // 4. SUBMIT FORM LEAD BẰNG AJAX
        var leadForm = document.getElementById('js-gqf-lead-form');
        var submitBtn = document.getElementById('js-btn-gqf-submit');
        var msgBox = document.getElementById('js-gqf-msg');

        if (leadForm) {
            leadForm.addEventListener('submit', function(e) {
                e.preventDefault();

                var btnText = submitBtn.querySelector('.btn-text');
                var btnLoading = submitBtn.querySelector('.btn-loading');

                btnText.style.display = 'none';
                btnLoading.style.display = 'inline-flex';
                submitBtn.disabled = true;
                msgBox.style.display = 'none';
                msgBox.className = 'gqf-form-message';

                var formData = new FormData(leadForm);

                fetch(ajaxUrl, {
                    method: 'POST',
                    body: formData
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    btnText.style.display = 'inline';
                    btnLoading.style.display = 'none';
                    submitBtn.disabled = false;

                    if (data.success) {
                        msgBox.className = 'gqf-form-message success';
                        msgBox.innerHTML = '<strong>Thành công!</strong> ' + (data.data.message || 'GoBike đã nhận được yêu cầu tư vấn của bạn.');
                        msgBox.style.display = 'block';
                        leadForm.reset();

                        // Tự động đóng modal sau 2.5 giây
                        setTimeout(function() {
                            closeModal();
                            msgBox.style.display = 'none';
                        }, 2500);
                    } else {
                        msgBox.className = 'gqf-form-message error';
                        msgBox.innerHTML = '<strong>Lỗi:</strong> ' + (data.data.message || 'Không thể gửi thông tin. Vui lòng thử lại.');
                        msgBox.style.display = 'block';
                    }
                })
                .catch(function(err) {
                    btnText.style.display = 'inline';
                    btnLoading.style.display = 'none';
                    submitBtn.disabled = false;
                    msgBox.className = 'gqf-form-message error';
                    msgBox.innerHTML = '<strong>Lỗi kết nối:</strong> Vui lòng kiểm tra lại mạng và thử lại.';
                    msgBox.style.display = 'block';
                });
            });
        }
    });
    </script>
    <?php
}
