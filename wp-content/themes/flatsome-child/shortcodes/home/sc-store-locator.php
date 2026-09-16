<?php
/**
 * Shortcode: Hệ Thống Cửa Hàng (Tab Dọc Kết Hợp Bản Đồ Google Maps)
 * 
 * Tag: [gobike_store_locator] hoặc [he_thong_cua_hang]
 * 
 * Cách dùng trong Flatsome UX Builder:
 * Chèn Shortcode / Text element: [gobike_store_locator]
 */

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('gobike_store_locator', 'gobike_render_store_locator_shortcode');
add_shortcode('he_thong_cua_hang', 'gobike_render_store_locator_shortcode');

function gobike_render_store_locator_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title'    => 'Hệ thống cửa hàng của Chúng tôi',
        'bg_color' => 'rgb(246, 244, 244)',
    ), $atts, 'gobike_store_locator');

    // Danh sách các chi nhánh cửa hàng
    $stores = array(
        array(
            'id'       => 'store-1',
            'title'    => 'Sencom 71 Trần Đăng Ninh, Quang Trung, Hà Đông, Hà Nội',
            'hours'    => '8h00 - 22h00 (tất cả các ngày trong tuần)',
            'phone'    => '0944 988 699',
            'phone_tel'=> '0944988699',
            'zalo_url' => 'https://zalo.me/0944988699',
            'map_dir'  => 'https://www.google.com/maps/dir//SENCOM.VN+-+71+TR%E1%BA%A6N+%C4%90%C4%82NG+NINH,+H%C3%80+%C4%90%C3%94NG,+71+Tr%E1%BA%A7n+%C4%90%C4%83ng+Ninh,+H%C3%A0+C%E1%BA%A7u,+H%C3%A0+%C4%90%C3%B4ng,+H%C3%A0+N%E1%BB%99i+10000,+Vi%E1%BB%87t+Nam/@20.9654392,105.7676176,17z',
            'map_embed'=> 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3725.6779560193754!2d105.76761757593923!3d20.9654441899488!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31345333c469972d%3A0xa64c16e1dbc88ea5!2zU0VOQ09NLlZOIC0gNzEgVFLhuqZOIMSQxIJORyBOSU5ILCBIw4AgxJDDlE5H!5e0!3m2!1svi!2s!4v1685351013279!5m2!1svi!2s',
        ),
        array(
            'id'       => 'store-2',
            'title'    => 'Sencom 1275C Đường 3/2, Phường 16, Quận 11, HCM',
            'hours'    => '8h00 - 22h00 (tất cả các ngày trong tuần)',
            'phone'    => '0944 988 699',
            'phone_tel'=> '0944988699',
            'zalo_url' => 'https://zalo.me/0944988699',
            'map_dir'  => 'https://www.google.com/maps/dir//1275C+%C4%90.+3+Th%C3%A1ng+2,+Ph%C6%B0%E1%BB%9Dng+16,+Qu%E1%BA%ADn+11,+Th%C3%A0nh+ph%E1%BB%91+H%E1%BB%93+Ch%C3%AD+Minh,+Vi%E1%BB%87t+Nam/@10.7587475,106.6509258,21z',
            'map_embed'=> 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d979.9211652098174!2d106.65042726961539!3d10.75876831686764!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752e8d67214249%3A0xfc9ccf91e6f7b9c5!2zMTI3NUMgxJAuIDMgVGjDoW5nIDIsIFBoxrDhu51uZyAxNiwgUXXhuq1uIDExLCBUaMOgbmggcGjhu5EgSOG7kyBDaMOtIE1pbmgsIFZp4buHdCBOYW0!5e0!3m2!1svi!2s!4v1685351079720!5m2!1svi!2s',
        ),
        array(
            'id'       => 'store-3',
            'title'    => 'Sencom 111A Nguyễn Hữu Cảnh, Phường 22, Quận Bình Thạnh, HCM',
            'hours'    => '8h00 - 22h00 (tất cả các ngày trong tuần)',
            'phone'    => '0944 988 699',
            'phone_tel'=> '0944988699',
            'zalo_url' => 'https://zalo.me/0944988699',
            'map_dir'  => 'https://www.google.com/maps/dir//111a+Nguy%E1%BB%85n+H%E1%BB%AFu+C%E1%BA%A3nh,+Ph%C6%B0%E1%BB%9Dng+22,+B%C3%ACnh+Th%E1%BA%A1nh,+Th%C3%A0nh+ph%E1%BB%91+H%E1%BB%93+Ch%C3%AD+Minh+700000,+Vi%E1%BB%87t+Nam/@10.7950519,106.7179236,20.5z',
            'map_embed'=> 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d979.8030644515766!2d106.71735306961534!3d10.795048916706712!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317528a936130ef1%3A0x6c6add47c17c1aba!2zMTExYSBOZ3V54buFbiBI4buvdSBD4bqjbmgsIFBoxrDhu51uZyAyMiwgQsOsbmggVGjhuqFuaCwgVGjDoG5oIHBo4buRIEjhu5MgQ2jDrSBNaW5oIDcwMDAwMCwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2s!4v1685351111683!5m2!1svi!2s',
        ),
    );

    ob_start();
    ?>
    <div class="row vp-row-custom gobike-store-locator-row">
        <div class="col small-12 large-12">
            <div class="col-inner gobike-store-locator-wrapper" style="background-color: <?php echo esc_attr($atts['bg_color']); ?>;">
                <?php if (!empty($atts['title'])) : ?>
                    <h3 class="ch_main_title"><?php echo esc_html($atts['title']); ?></h3>
                <?php endif; ?>

                <div class="tabbed-content tab_list_showroom">
                    <ul class="nav nav-simple nav-vertical nav-size-normal nav-left" role="tablist">
                        <?php foreach ($stores as $i => $store) : 
                            $is_active = ($i === 0);
                        ?>
                            <li class="tab has-icon <?php echo $is_active ? 'active' : ''; ?>" role="presentation">
                                <div class="info_ch">
                                    <div class="ch_title"><?php echo esc_html($store['title']); ?></div>
                                    <div class="ch_info">
                                        <div class="item_x">
                                            <i class="icon-clock"></i> 
                                            <span><?php echo esc_html($store['hours']); ?></span>
                                        </div>
                                        <div class="item_x">
                                            <i class="icon-phone"></i> 
                                            <a href="tel:<?php echo esc_attr($store['phone_tel']); ?>" style="color: inherit; font-weight: 500;">
                                                <?php echo esc_html($store['phone']); ?>
                                            </a>
                                        </div>
                                        <div class="item_x">
                                            <a href="<?php echo esc_url($store['zalo_url']); ?>" target="_blank" class="link_zalo" rel="noopener">
                                                <svg width="16" height="16" viewBox="0 0 48 48" fill="none">
                                                    <circle cx="24" cy="24" r="24" fill="#0068FF"/>
                                                    <path d="M14 16H34V20L21 30H34V34H14V30L27 20H14V16Z" fill="#FFFFFF"/>
                                                </svg>
                                                Chat zalo
                                            </a>
                                        </div>
                                        <div class="item_x">
                                            <a href="<?php echo esc_url($store['map_dir']); ?>" target="_blank" class="link_chiduong" rel="noopener">
                                                <svg width="14" height="16" viewBox="0 0 24 24" fill="#0d6e2e">
                                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                                </svg>
                                                Xem đường đi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="tab-panels">
                        <?php foreach ($stores as $i => $store) : 
                            $is_active = ($i === 0);
                        ?>
                            <div class="panel entry-content <?php echo $is_active ? 'active' : ''; ?>" role="tabpanel">
                                <iframe 
                                    src="<?php echo esc_url($store['map_embed']); ?>" 
                                    width="100%" 
                                    height="470" 
                                    style="border: 0;" 
                                    allowfullscreen="" 
                                    loading="lazy" 
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
