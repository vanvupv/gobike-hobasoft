<?php

/*Disable edit code & plugin */



add_filter('xmlrpc_enabled', '__return_false');
add_filter('wp_headers', 'wptangtoc_remove_x_pingback');
add_filter('pings_open', '__return_false', 9999);
add_filter('pre_update_option_enable_xmlrpc', '__return_false');
add_filter('pre_option_enable_xmlrpc', '__return_zero');
function wptangtoc_remove_x_pingback($headers) {
unset($headers['X-Pingback'], $headers['x-pingback']);
return $headers;
}


// 
if ( file_exists( __DIR__ . '/gobike-product-filter.php' ) ) {
    require_once __DIR__ . '/gobike-product-filter.php';
}


function wp_version_remove_version() {
return '';
}
add_filter('the_generator', 'wp_version_remove_version');


global $product, $post;
add_action( 'init', 'hide_notice' );
function hide_notice() {
remove_action( 'admin_notices', 'flatsome_maintenance_admin_notice' );
}
register_sidebar( array(
	'name'          => __( 'Main Menu', 'flatsome' ),
	'id'            => 'menu-main',
	'before_widget' => '<div id="%1$s" class="%2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<span class="widget-title"><span>',
	'after_title'   => '</span></span>',
) );

register_sidebar( array(
	'name'          => __( 'Menu Tab', 'flatsome' ),
	'id'            => 'sidebar-brand',
	'before_widget' => '<div id="%1$s" class="%2$s menu-tab">',
	'after_widget'  => '</div>',
	'before_title'  => '<span class="widget-title"><span>',
	'after_title'   => '</span></span>',
) );

register_sidebar( array(
	'name'          => __( 'Bộ lọc sản phẩm', 'flatsome' ),
	'id'            => 'filter-sidebar',
	'before_widget' => '<div id="%1$s" class="%2$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<span class="widget-title"><span>',
	'after_title'   => '</span></span>',
) );

function register_footer_menu_mobile() {
	register_nav_menus(
		array( 'footer-menu-mobile' => __( 'Footer Menu Mobile' ) )
	);}
add_action( 'init', 'register_footer_menu_mobile' );

function add_footer_menu_mobile() {
	wp_nav_menu( array( 'theme_location' => 'footer-menu-mobile', 'container_class' => 'footer-menu-mobile' ) );
}
add_action('flatsome_after_header', 'add_footer_menu_mobile');

function load_custom_wp_admin_style(){
    wp_register_style( 'custom_wp_admin_css', get_bloginfo('stylesheet_directory') . '/admin.css', false, '1.0.0' );
    wp_enqueue_style( 'custom_wp_admin_css' );
}
add_action('admin_enqueue_scripts', 'load_custom_wp_admin_style');

function load_custom_wp_admin_script() {
    wp_register_script( 'custom_wp_admin_script', get_bloginfo('stylesheet_directory') . '/my-themes.js', false, '1.0.0' );
    wp_enqueue_script( 'custom_wp_admin_script' );
}
add_action( 'wp_footer', 'load_custom_wp_admin_script' );





add_action('init', function() {
  // remove duotone support for Gutenberg blocks
  remove_filter('render_block', 'wp_render_duotone_support');
});

// Enable shortcodes in term description
add_filter( 'term_description', 'do_shortcode' );

// Add Icon Menu in Widget
add_filter('wp_nav_menu_args', 'wpex_ux_menu_icon');
add_filter('widget_nav_menu_args', 'wpex_ux_menu_icon');
function wpex_ux_menu_icon($args) {
   return array_merge( $args, array(
	  'walker' => new FlatsomeNavDropdown(),
   ) );
}
// Remove the Reviews and Additional information tab 
function woo_remove_product_tab($tabs) {
//	unset( $tabs['reviews'] );
	unset( $tabs['description'] ); 
	unset( $tabs['additional_information'] );  	
 	return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tab', 98);

// thay đổi form trang checkout
add_filter( 'woocommerce_checkout_fields' , 'custom_checkout_form' );
function custom_checkout_form( $fields ) {
    unset($fields['billing']['billing_postcode']); //Ẩn postCode
    unset($fields['billing']['billing_state']); //Ẩn bang hạt
    unset($fields['billing']['billing_country']);// Ẩn quốc gia
    unset($fields['billing']['billing_address_2']); //billing_company
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_last_name']);
    unset($fields['billing']['order_comments']);// Ẩn quốc gia
    unset($fields['billing']['billing_city']); //Ẩn select box chọn thành phố
    //unset($fields['billing']['billing_email']); 
     $fields['billing']['billing_first_name']['placeholder'] = "VD: Nguyễn Văn A";
     $fields['billing']['billing_phone']['placeholder'] = "0988xxxxxx";
     $fields['billing']['billing_email']['placeholder'] = "your_email@gmail.com"; 
    return $fields;
}
function custom_checkout_field_label( $fields ) {
    $fields['address_1']['label'] = 'Địa chỉ nhận hàng';
    $fields['first_name']['label'] = 'Họ và tên';
    return $fields;
}
add_filter( 'woocommerce_default_address_fields', 'custom_checkout_field_label' );

// To change add to cart text on single product page
add_filter( 'woocommerce_product_single_add_to_cart_text', 'woocommerce_custom_single_add_to_cart_text' ); 
function woocommerce_custom_single_add_to_cart_text() {
    echo '<i class="fas fa-cart-plus"></i> Thêm Vào Giỏ Hàng'  ; 
}
// Thay doi nut Order trang Thanh toan
function woocommerce_button_proceed_to_checkout() {
       $new_checkout_url = WC()->cart->get_checkout_url();
       ?>
       <a href="<?php echo $new_checkout_url; ?>" class="checkout-button button alt wc-forward">
	   <?php _e( 'Đặt mua ngay', 'woocommerce' ); ?></a>
<?php
}

/* Remove Image Sizes */
add_filter('intermediate_image_sizes_advanced','__return_false');

add_filter( 'woocommerce_variable_sale_price_html', 'lw_variable_product_price', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'lw_variable_product_price', 10, 2 );
function lw_variable_product_price( $v_price, $v_product ) {
// Sale Price
$v_prices = array( $v_product->get_variation_regular_price( 'min', true ), $v_product->get_variation_regular_price( 'max', true ) );
sort( $v_prices );
$v_saleprice = $v_prices[0]!==$v_prices[1] ? sprintf(__('%1$s','woocommerce'), wc_price( $v_prices[0] ) ) : wc_price( $v_prices[0] );
// Regular Price
$v_prices = array( $v_product->get_variation_price( 'min', true ), $v_product->get_variation_price( 'max', true ) );
$v_price = $v_prices[0]!==$v_prices[1] ? sprintf(__('%1$s', 'woocommerce'), wc_price( $v_prices[0] ) ) : wc_price( $v_prices[0] );
if ( $v_price !== $v_saleprice ) { 
	$v_price = '<ins>' . $v_price . $v_product->get_price_suffix() . '</ins><del><span>Giá niêm yết: </span>' . $v_saleprice.$v_product->get_price_suffix() . '</del>';
}
return $v_price;
}

add_filter( 'woocommerce_format_sale_price', 'invert_formatted_sale_price', 10, 3 );
function invert_formatted_sale_price( $price, $regular_price, $sale_price ) {
    return '<ins>' . ( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price ) . '</ins><del><span>Giá niêm yết: </span>' . ( is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price ) . '</del>';
}
add_filter( 'woocommerce_variation_option_name', 'display_price_in_variation_option_name' );
function display_price_in_variation_option_name( $term ) {
    global $wpdb, $product;
    $result = $wpdb->get_col( "SELECT slug FROM {$wpdb->prefix}terms WHERE name = '$term'" );
    $term_slug = ( !empty( $result ) ) ? $result[0] : $term;
    $query = "SELECT postmeta.post_id AS product_id
                FROM {$wpdb->prefix}postmeta AS postmeta
                    LEFT JOIN {$wpdb->prefix}posts AS products ON ( products.ID = postmeta.post_id )
                WHERE postmeta.meta_key LIKE 'attribute_%'
                    AND postmeta.meta_value = '$term_slug'
                    AND products.post_parent = $product->id";
    $variation_id = $wpdb->get_col( $query );
    $parent = wp_get_post_parent_id( $variation_id[0] );
    if ( $parent > 0 ) {
        $_product = new WC_Product_Variation( $variation_id[0] );
        return $_product->get_price();
    }
    return $term;
}
// Add text before add to cart button
add_action( 'woocommerce_before_single_variation', 'xt_before_single_variation' );
function xt_before_single_variation(){
	global $post;
	$promotion_product = get_field( 'promotion_product' );
	$hotsale_product = get_field( 'hotsale_product' );
	$shipping = get_post_meta( $post->ID, 'shipping', true );
 	$select_shop_selected_option = get_field( 'select_shop' );
	$rows = get_field('product_linked');
	if ( get_field( 'installment' ) == 1 ) {
	echo '<span class="installment">Trả góp 0%</span>';
	}
	if( $rows ) {
		echo '<div class="linked-product">';
		foreach( $rows as $row ) {
			$link_linked_product = $row['link_linked_product'];
			$name_linked_product = $row['name_linked_product'];
			$price_linked_product = number_format( $row['price_linked_product'], 0, ',', '.' );
			echo '<a class="item-linked-product" href="' . $link_linked_product . '">';
			echo '<span>' . $name_linked_product . '</span>';
			echo '<strong>' . $price_linked_product . ' ₫</strong>' ;
			echo '</a>';
		}
		echo '</div>'; // linked-product
	}
	
	echo '<div class="promotion-info">';
	if( $hotsale_product ) { 
		echo '<div class="hotsale-product">' . $hotsale_product . '</div>';	
	}
	if( $promotion_product ) { 
		echo '<div class="promotion-product"><div class="promotion-icon"><i class="icon-gift"></i> Khuyến mãi</div><div>' . $promotion_product . '</div></div>';
	}
	echo '</div>'; // End promotion-info
}

// Add text before add to cart form
add_action( 'woocommerce_before_add_to_cart_form', 'xt_before_add_to_cart_form' );
function xt_before_add_to_cart_form(){
	global $post;
	$promotion_product = get_field( 'promotion_product' );
	$hotsale_product = get_field( 'hotsale_product' );
	$shipping = get_post_meta( $post->ID, 'shipping', true );
 	$select_shop_selected_option = get_field( 'select_shop' );
	$rows = get_field('product_linked');
	if ( get_field( 'installment' ) == 1 ) {
	echo '<span class="installment">Trả góp 0%</span>';
	}
}


/* Add quick buy button go to checkout after click */
add_action('woocommerce_after_add_to_cart_button','quickbuy_after_addtocart_button');
function quickbuy_after_addtocart_button(){
    global $product;
    ?>
    <button type="button" class="button buy_now_button"><?php _e('Mua ngay', 'ntx'); ?></button>
    <input type="hidden" name="is_buy_now" class="is_buy_now" value="0" autocomplete="off"/>
    <?php
}
add_filter('woocommerce_add_to_cart_redirect', 'redirect_to_checkout');
function redirect_to_checkout($redirect_url) {
    if (isset($_REQUEST['is_buy_now']) && $_REQUEST['is_buy_now']) {
        $redirect_url = wc_get_cart_url();
    }
    return $redirect_url;
}

function change_translate_text_multiple( $translated ) {
	$text = array(
		'Reply' => 'Bình luận',
		'Was this review helpful to you?' => 'Nhận xét này có hữu ích cho bạn?',
	);
	$translated = str_ireplace(  array_keys($text),  $text,  $translated );
	return $translated;
}
add_filter( 'gettext', 'change_translate_text_multiple', 20 );

function js_custom(){;?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
		jQuery('body').on('click', '.buy_now_button', function(e){
			e.preventDefault();
			var thisParent = jQuery(this).parents('form.cart');
			if(jQuery('.single_add_to_cart_button', thisParent).hasClass('disabled')) {
				jQuery('.single_add_to_cart_button', thisParent).trigger('click');
				return false;
			}
			thisParent.addClass('ntx-quickbuy');
			jQuery('.is_buy_now', thisParent).val('1');
			jQuery('.single_add_to_cart_button', thisParent).trigger('click');
		});
		
		jQuery(".menu .icon-angle-down, .product-footer-right .product.product-small .promotion, .product-footer-right .product.product-small .item-hotsale").remove();
		jQuery(".product-info h1.product-title").remove();
		jQuery(".product-type-variable .product-info>:is(.promotion-info, .linked-product)").remove();
		jQuery(".menu .sub-menu").removeClass("nav-dropdown nav-dropdown-default");
		jQuery("body").removeClass("nav-dropdown-has-arrow nav-dropdown-has-shadow");
				
		jQuery(function(){
			var current = location.pathname;
			jQuery('.item-linked-product').each(function(){
				var $this = $(this);
				if($this.attr('href').indexOf(current) !== -1){
					$this.addClass('active');
				}
			})
		});
    });
</script>
<?php }
add_action('wp_footer','js_custom');


class Auto_Save_Images{
 
    function __construct(){     
        
        add_filter( 'content_save_pre',array($this,'post_save_images') ); 
    }
    
    function post_save_images( $content ){
        if( ($_POST['save'] || $_POST['publish'] )){
            set_time_limit(240);
            global $post;
            $post_id=$post->ID;
            $preg=preg_match_all('/<img.*?src="(.*?)"/',stripslashes($content),$matches);
            if($preg){
                foreach($matches[1] as $image_url){
                    if(empty($image_url)) continue;
                    $pos=strpos($image_url,$_SERVER['HTTP_HOST']);
                    if($pos===false){
                        $res=$this->save_images($image_url,$post_id);
                        $replace=$res['url'];
                        $content=str_replace($image_url,$replace,$content);
                    }
                }
            }
        }
        remove_filter( 'content_save_pre', array( $this, 'post_save_images' ) );
        return $content;
    }
    
    function save_images($image_url,$post_id){
        $file=file_get_contents($image_url);
        $post = get_post($post_id);
        $posttitle = $post->post_title;
        $postname = sanitize_title($posttitle);
        $im_name = "$postname-$post_id.jpg";
        $res=wp_upload_bits($im_name,'',$file);
        $this->insert_attachment($res['file'],$post_id);
        return $res;
    }
    
    function insert_attachment($file,$id){
        $dirs=wp_upload_dir();
        $filetype=wp_check_filetype($file);
        $attachment=array(
            'guid'=>$dirs['baseurl'].'/'._wp_relative_upload_path($file),
            'post_mime_type'=>$filetype['type'],
            'post_title'=>preg_replace('/\.[^.]+$/','',basename($file)),
            'post_content'=>'',
            'post_status'=>'inherit'
        );
        $attach_id=wp_insert_attachment($attachment,$file,$id);
        $attach_data=wp_generate_attachment_metadata($attach_id,$file);
        wp_update_attachment_metadata($attach_id,$attach_data);
        return $attach_id;
    }
}
new Auto_Save_Images();

// Đoạn JS kích hoạt Pop-up Danh mục khi bấm trên Mobile (Dùng jQuery chuẩn Flatsome)
add_action('wp_footer', function() { ?>
<script type="text/javascript">
jQuery(document).ready(function($) {
    // Bấm vào nút Danh mục ở đáy thì Bật/Tắt Pop-up
    $('body').on('click', '.footer-menu-mobile li.open-menu-mobile > a, .footer-menu-mobile li.open-menu-mobile', function(e) {
        if ($(e.target).closest('ul.sub-menu').length === 0) {
            e.preventDefault();
            e.stopPropagation();
            $('.footer-menu-mobile li.open-menu-mobile').toggleClass('active-popup');
        }
    });

    // Bấm ra ngoài màn hình thì tự đóng Pop-up
    $(document).on('click touchstart', function(e) {
        if (!$(e.target).closest('.footer-menu-mobile li.open-menu-mobile').length) {
            $('.footer-menu-mobile li.open-menu-mobile').removeClass('active-popup');
        }
    });
});
</script>

<?php });

/**
 * Shortcode hiển thị Khối danh mục Xe đạp trợ lực điện chuẩn giao diện GOBIKE
 * Cú pháp: [gobike_category_block cat="slug-danh-muc" title="TIÊU ĐỀ" subcat="Tên danh mục phụ" subcat_link="#" view_all="#"]
 */
function gobike_render_category_block($atts) {
    $atts = shortcode_atts(array(
        'cat'           => '',
        'title'         => 'XE ĐẠP TRỢ LỰC ĐIỆN',
        'subcat'        => '',
        'subcat_link'   => '#',
        'view_all'      => '',
        'limit'         => 9,
        'mobile_banner' => ''
    ), $atts, 'gobike_category_block');

    if (empty($atts['cat'])) {
        return '<div style="padding:15px;background:#fff3cd;color:#856404;border:1px solid #ffeeba;margin:10px 0;">'
             . '⚠️ <strong>Chưa nhập slug danh mục!</strong>'
             . '</div>';
    }

    $term = get_term_by('slug', $atts['cat'], 'product_cat');
    if (!$term && is_numeric($atts['cat'])) {
        $term = get_term_by('id', (int)$atts['cat'], 'product_cat');
    }
    
    $view_all_link = !empty($atts['view_all']) ? $atts['view_all'] : ($term ? get_term_link($term) : '#');
    if (is_wp_error($view_all_link)) $view_all_link = '#';

    $tax_field = is_numeric($atts['cat']) ? 'term_id' : 'slug';
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => intval($atts['limit']),
        'tax_query'      => array(
            array(
                'taxonomy'         => 'product_cat',
                'field'            => $tax_field,
                'terms'            => $atts['cat'],
                'include_children' => true,
            ),
        ),
    );

    $loop = new WP_Query($args);
    if (!$loop->have_posts()) {
        return '<div style="padding:15px;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;margin:15px 0;border-radius:4px;">'
             . '⚠️ <strong>[Khối ' . esc_html($atts['title']) . ']:</strong> Không tìm thấy sản phẩm nào trong danh mục <code>"' . esc_html($atts['cat']) . '"</code>.'
             . '</div>';
    }

    ob_start();
    ?>
    <div class="gobike-section">
        <?php if (!empty($atts['mobile_banner'])): ?>
            <div class="gobike-mobile-banner">
                <a href="<?php echo esc_url($view_all_link); ?>">
                    <img src="<?php echo esc_url($atts['mobile_banner']); ?>" alt="<?php echo esc_attr($atts['title']); ?>" />
                </a>
            </div>
        <?php endif; ?>

        <!-- THANH TIÊU ĐỀ MÀU CAM -->
        <div class="gobike-section-head">
            <h2 class="title_blog"><?php echo esc_html($atts['title']); ?></h2>
            <div class="viewallcat">
                <?php if (!empty($atts['subcat'])): ?>
                    <a href="<?php echo esc_url($atts['subcat_link']); ?>" class="subcat-link"><?php echo esc_html($atts['subcat']); ?></a>
                <?php endif; ?>
                <a href="<?php echo esc_url($view_all_link); ?>" class="viewall-link">Xem tất cả</a>
            </div>
        </div>

        <!-- LƯỚI 5 CỘT (1 Ô LỚN + 8 Ô NHỎ) -->
        <div class="gobike-grid-container">
            <?php 
            $index = 0;
            while ($loop->have_posts()) : $loop->the_post();
                global $product;
                $index++;
                $product_id = get_the_ID();
                $title      = get_the_title();
                $permalink  = get_permalink();
                $image_url  = get_the_post_thumbnail_url($product_id, 'medium_large');
                if (!$image_url) {
                    $image_url = wc_placeholder_img_src();
                }

                // XỬ LÝ GIÁ TIỀN & HUY HIỆU GIẢM GIÁ ⚡
                $is_on_sale    = $product->is_on_sale();
                $regular_price = (float)$product->get_regular_price();
                $sale_price    = (float)$product->get_sale_price();

                $sale_badge_html   = '';
                $price_custom_html = '';

                if ($is_on_sale && $sale_price > 0 && $regular_price > $sale_price) {
                    $percent = round((($regular_price - $sale_price) / $regular_price) * 100);
                    // Huy hiệu có icon tia sét ⚡
                    $sale_badge_html = '<span class="gobike-badge-sale">'
                                     . '<svg viewBox="0 0 24 24" width="11" height="11" fill="currentColor"><path d="M13 2L3 14h7v8l10-12h-7z"/></svg>'
                                     . ' Giảm ' . $percent . '%'
                                     . '</span>';
                    // Giá đỏ ở trước, Giá gạch ngang ở sau
                    $price_custom_html = '<strong class="price-current">' . wc_price($sale_price) . '</strong>'
                                       . '<span class="price-old">' . wc_price($regular_price) . '</span>';
                } else {
                    $current_price = $product->get_price();
                    if (!empty($current_price)) {
                        $price_custom_html = '<strong class="price-current">' . wc_price($current_price) . '</strong>';
                    } else {
                        $price_custom_html = '<strong class="price-current">Liên hệ</strong>';
                    }
                }

                if ($index === 1) : 
                    // 1. SẢN PHẨM NỔI BẬT (Ô LỚN)
                ?>
                    <div class="gobike-big-item">
                        <div class="img-box">
                            <a href="<?php echo esc_url($permalink); ?>">
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" />
                            </a>
                            <?php echo $sale_badge_html; ?>
                        </div>
                        <div class="info-box">
                            <a href="<?php echo esc_url($permalink); ?>">
                                <h3 class="product-title"><?php echo esc_html($title); ?></h3>
                            </a>
                            <div class="gobike-price-box">
                                <?php echo $price_custom_html; ?>
                            </div>
                            <div class="spec-table">
                                <?php 
                                $excerpt = get_the_excerpt();
                                if (!empty($excerpt)) {
                                    echo do_shortcode($excerpt);
                                } else {
                                    $attributes = $product->get_attributes();
                                    if (!empty($attributes)) {
                                        echo '<table>';
                                        foreach ($attributes as $attribute) {
                                            $name = wc_attribute_label($attribute->get_name());
                                            $values = array();
                                            if ($attribute->is_taxonomy()) {
                                                $attribute_values = wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'names'));
                                                $values = $attribute_values;
                                            } else {
                                                $values = $attribute->get_options();
                                            }
                                            echo '<tr><td><span>' . esc_html($name) . ':</span></td><td>' . esc_html(implode(', ', $values)) . '</td></tr>';
                                        }
                                        echo '</table>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php else : 
                    // 2. TÁM SẢN PHẨM NHỎ
                ?>
                    <div class="gobike-small-item">
                        <div class="img-wrap">
                            <a href="<?php echo esc_url($permalink); ?>">
                                <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" />
                            </a>
                            <?php echo $sale_badge_html; ?>
                        </div>
                        <a href="<?php echo esc_url($permalink); ?>">
                            <h3 class="item-title"><?php echo esc_html($title); ?></h3>
                        </a>
                        <div class="gobike-price-box">
                            <?php echo $price_custom_html; ?>
                        </div>
                    </div>
                <?php 
                endif;
            endwhile; 
            wp_reset_postdata(); 
            ?>
        </div>
        
        <a href="<?php echo esc_url($view_all_link); ?>" class="gobike-mobile-viewmore">Xem tất cả <?php echo esc_html($atts['title']); ?></a>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('gobike_category_block', 'gobike_render_category_block');

// 1. Tạo trang cài đặt Theme Settings trong WP Admin
if( function_exists('acf_add_options_page') ) {
    acf_add_options_page(array(
        'page_title'    => 'Cài đặt chung Website',
        'menu_title'    => 'Theme Settings',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
}

// 2. Tạo Shortcode [gobike_topbar_ticker] tự động render dòng chữ chạy
    $items = get_field('topbar_marquee_items', 'option');
    
    // Nếu chưa nhập dữ liệu trong ACF thì dùng mặc định
    if( empty($items) ) {
        $items = array(
            array('text' => 'Sản phẩm <strong>Chính hãng - Xuất VAT</strong> đầy đủ'),
            array('text' => '<strong>Giao nhanh - Miễn phí</strong> cho đơn 300k'),
            array('text' => '<strong>Thu cũ</strong> giá ngon - <strong>Lên đời</strong> tiết kiệm'),
        );
    }

    ob_start();
    ?>
    <div class="cps-marquee-wrapper">
      <div class="cps-marquee-track">
        <?php for ($i = 0; $i < 2; $i++): // Lặp 2 lần để chạy vô tận không đứt đoạn ?>
            <?php foreach( $items as $item ): ?>
                <div class="cps-item">
                    <svg width="15" height="15" viewBox="0 0 16 16" fill="none"><path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 6a4 4 0 1 0 8 0 4 4 0 0 0-8 0Z"></path><path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m8.5 10 2.267 3.927 1.065-2.156 2.399.155L11.964 8M5.035 8l-2.267 3.927 2.399-.156 1.065 2.155L8.499 10"></path></svg>
                    <span><?php echo $item['text']; ?></span>
                </div>
            <?php endforeach; ?>
        <?php endfor; ?>
      </div>
    </div>
    <?php
    return ob_get_clean();
add_shortcode('gobike_topbar_ticker', 'gobike_topbar_ticker_shortcode');

// Shortcode [gobike_topbar_ticker] LẤY ĐÚNG 3 Ô BẠN VỪA TẠO Ở TRANG CHỦ
function gobike_topbar_ticker_shortcode() {
    $front_page_id = get_option('page_on_front');

    // Gọi đúng tên 3 trường trong ảnh ACF của bạn
    $item1 = get_field('topbar_item_1', $front_page_id);
    $item2 = get_field('topbar_item_2', $front_page_id);
    $item3 = get_field('topbar_item_3', $front_page_id);

    $items = array();
    if( !empty($item1) ) $items[] = $item1;
    if( !empty($item2) ) $items[] = $item2;
    if( !empty($item3) ) $items[] = $item3;

    // Nếu trang chủ chưa gõ gì thì lấy câu mặc định
    if( empty($items) ) {
        $items = array(
            'Sản phẩm <strong>Chính hãng - Xuất VAT</strong> đầy đủ',
            '<strong>Giao nhanh - Miễn phí</strong> cho đơn 300k',
            '<strong>Thu cũ</strong> giá ngon - <strong>Lên đời</strong> tiết kiệm'
        );
    }
    
    $svgs = array(
        '<svg width="15" height="15" viewBox="0 0 16 16" fill="none"><path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.5 6a4 4 0 1 0 8 0 4 4 0 0 0-8 0Z"></path><path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m8.5 10 2.267 3.927 1.065-2.156 2.399.155L11.964 8M5.035 8l-2.267 3.927 2.399-.156 1.065 2.155L8.499 10"></path></svg>',
        '<svg width="15" height="15" viewBox="0 0 17 16" fill="none"><path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.833 11.333a1.333 1.333 0 1 0 2.667 0 1.333 1.333 0 0 0-2.667 0ZM10.5 11.333a1.333 1.333 0 1 0 2.667 0 1.333 1.333 0 0 0-2.667 0Z"></path><path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.833 11.333H2.5V8.667m-.667-5.334h7.334v8m-2.667 0h4m2.667 0H14.5v-4m0 0H9.167m5.333 0L12.5 4H9.167M2.5 6h2.667"></path></svg>',
        '<svg width="15" height="15" viewBox="0 0 17 16" fill="none"><path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.167 8V6a2 2 0 0 1 2-2h8.666m0 0-2-2m2 2-2 2M13.833 8v2a2 2 0 0 1-2 2H3.167m0 0 2 2m-2-2 2-2"></path></svg>'
    );

    ob_start();
    ?>
    <div class="cps-marquee-wrapper">
      <div class="cps-marquee-track">
        <?php for ($i = 0; $i < 2; $i++): ?>
            <?php 
            $k = 0;
            foreach( $items as $text ): 
                $icon = isset($svgs[$k % count($svgs)]) ? $svgs[$k % count($svgs)] : $svgs[0];
                $k++;
            ?>
                <div class="cps-item">
                    <?php echo $icon; ?>
                    <span><?php echo $text; ?></span>
                </div>
            <?php endforeach; ?>
        <?php endfor; ?>
      </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('gobike_topbar_ticker', 'gobike_topbar_ticker_shortcode');


