<?php
/**
 * Product Layout: No Sidebar (Customized for GoBike)
 * Chuẩn hóa 100% theo bản thiết kế Ảnh 1 (Desktop) và Ảnh 3 (Mobile)
 * 
 * Section 1: Swiper Gallery + Quick Specs + Product Info (50% - 50%)
 * Section 2: 5 Tabs Sticky (Mô tả, Thông số, Media, Đánh giá, Hỏi đáp)
 * Section 3: Sản phẩm liên quan (Tabs phân loại + Swiper Carousel 5 cột) + Cam kết
 * 
 * @package Flatsome-Child
 */

if (!defined('ABSPATH')) {
    exit;
}

global $product, $post;

if (empty($product) || !is_a($product, 'WC_Product')) {
    $product = wc_get_product(get_the_ID());
}

if (!$product) {
    return;
}
?>

<div class="gobike-single-product-page" id="product-<?php the_ID(); ?>" <?php wc_product_class('gobike-product-scope', $product); ?>>
    
    <!-- 1. BREADCRUMB ĐIỀU HƯỚNG PHÂN CẤP -->
    <div class="gobike-product-breadcrumb-wrap">
        <div class="container">
            <?php
            woocommerce_breadcrumb(array(
                'delimiter'   => ' <span class="bc-sep">/</span> ',
                'wrap_before' => '<nav class="gobike-breadcrumb" aria-label="Breadcrumb">',
                'wrap_after'  => '</nav>',
                'before'      => '<span class="bc-item">',
                'after'       => '</span>',
                'home'        => _x('Trang chủ', 'breadcrumb', 'woocommerce'),
            ));
            ?>
        </div>
    </div>

    <!-- THÔNG BÁO WOOCOMMERCE (NẾU CÓ) -->
    <div class="container">
        <?php wc_print_notices(); ?>
    </div>

    <!-- 2. SECTION 1: GALLERY & THÔNG TIN MUA HÀNG (50% - 50%) -->
    <section class="gobike-product-hero-section">
        <div class="container">
            <div class="row row-main-product align-top">
                <!-- Cột Trái (50%): Gallery Slider + Quick Specs -->
                <div class="col large-6 medium-12 small-12 col-gallery-side">
                    <?php 
                    if (function_exists('gobike_render_single_product_gallery')) {
                        gobike_render_single_product_gallery($product);
                    }
                    ?>
                </div>

                <!-- Cột Phải (50%): Thông tin chi tiết + Nút Mua ngay -->
                <div class="col large-6 medium-12 small-12 col-info-side">
                    <?php 
                    if (function_exists('gobike_render_single_product_info')) {
                        gobike_render_single_product_info($product);
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. SECTION 2: 5 TABS NỘI DUNG CHI TIẾT (STICKY TABS) -->
    <?php 
    if (function_exists('gobike_render_single_product_tabs')) {
        gobike_render_single_product_tabs($product);
    }
    ?>

    <!-- 4. SECTION 3: SẢN PHẨM LIÊN QUAN & CAM KẾT CHÂN TRANG -->
    <?php 
    if (function_exists('gobike_render_single_product_related')) {
        gobike_render_single_product_related($product);
    }
    ?>

    <!-- POPUP MODAL XEM VIDEO YOUTUBE -->
    <div class="gobike-video-modal-overlay" id="gobikeVideoModal" style="display: none;">
        <div class="video-modal-box">
            <button type="button" class="btn-close-modal" id="btnCloseVideoModal" aria-label="Đóng">&times;</button>
            <div class="video-iframe-container">
                <iframe id="gobikeModalIframe" src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    </div>

</div>

<!-- JAVASCRIPT ĐIỀU KHIỂN SINGLE PRODUCT (SWIPER, TABS, FAQ, BUY NOW) -->
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script type="text/javascript">
jQuery(document).ready(function($) {
    // 1. Khởi tạo Swiper Gallery (Main Slider + Thumbs)
    function initGallerySwiper() {
        if (typeof Swiper === 'undefined') return;

        var thumbsSwiper = new Swiper('.gobike-gallery-thumbs-swiper', {
            direction: 'vertical',
            slidesPerView: 5,
            spaceBetween: 10,
            watchSlidesProgress: true,
            breakpoints: {
                0: {
                    direction: 'horizontal',
                    slidesPerView: 4,
                    spaceBetween: 8
                },
                768: {
                    direction: 'vertical',
                    slidesPerView: 5,
                    spaceBetween: 10
                }
            }
        });

        var mainSwiper = new Swiper('.gobike-gallery-main-swiper', {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: false,
            navigation: {
                nextEl: '.gobike-gal-next',
                prevEl: '.gobike-gal-prev',
            },
            pagination: {
                el: '.gobike-gal-pagination',
                clickable: true,
            },
            thumbs: {
                swiper: thumbsSwiper,
            }
        });

        // Click vào thumb cập nhật class active
        $('.gobike-gallery-thumbs-swiper .thumb-item').on('click', function() {
            $('.gobike-gallery-thumbs-swiper .thumb-item').removeClass('active');
            $(this).addClass('active');
        });

        // Khởi tạo Carousel Sản Phẩm Liên Quan
        var relatedSwiper = new Swiper('.gobike-related-carousel-swiper', {
            slidesPerView: 5,
            spaceBetween: 15,
            watchOverflow: true,
            navigation: {
                nextEl: '.rel-next',
                prevEl: '.rel-prev',
            },
            breakpoints: {
                0: {
                    slidesPerView: 2,
                    spaceBetween: 10
                },
                550: {
                    slidesPerView: 3,
                    spaceBetween: 12
                },
                850: {
                    slidesPerView: 4,
                    spaceBetween: 14
                },
                1100: {
                    slidesPerView: 5,
                    spaceBetween: 15
                }
            }
        });
    }

    if (typeof Swiper === 'undefined') {
        $.getScript('https://unpkg.com/swiper/swiper-bundle.min.js', function() {
            initGallerySwiper();
        });
    } else {
        initGallerySwiper();
    }

    // 2. Chuyển đổi 5 Tabs nội dung
    $('.gobike-tabs-nav-list .tab-nav-item a').on('click', function(e) {
        e.preventDefault();
        var targetId = $(this).attr('href');
        
        $('.gobike-tabs-nav-list .tab-nav-item').removeClass('active');
        $(this).parent('.tab-nav-item').addClass('active');

        $('.gobike-tab-panel').removeClass('active');
        $(targetId).addClass('active');

        // Cuộn mượt mà đến đầu nội dung tab
        $('html, body').animate({
            scrollTop: $('#gobikeProductTabsSection').offset().top - 70
        }, 300);
    });

    // 3. Đóng mở Accordion FAQ
    $('.faq-item .faq-question').on('click', function() {
        var $item = $(this).closest('.faq-item');
        var $answer = $item.find('.faq-answer');
        var $icon = $(this).find('.faq-icon');

        if ($item.hasClass('open')) {
            $answer.slideUp(250);
            $item.removeClass('open');
            $icon.text('+');
        } else {
            $('.faq-item .faq-answer').slideUp(250);
            $('.faq-item').removeClass('open');
            $('.faq-item .faq-icon').text('+');

            $answer.slideDown(250);
            $item.addClass('open');
            $icon.text('−');
        }
    });

    // Mở sẵn câu hỏi đầu tiên
    $('.faq-accordion-grid .faq-item:first').addClass('open').find('.faq-answer').show().end().find('.faq-icon').text('−');

    // 4. Modal Popup Video
    function openVideoModal(videoUrl) {
        var embedUrl = videoUrl;
        if (videoUrl.indexOf('youtube.com/watch?v=') !== -1) {
            var vId = videoUrl.split('v=')[1];
            var ampersandPos = vId.indexOf('&');
            if (ampersandPos !== -1) {
                vId = vId.substring(0, ampersandPos);
            }
            embedUrl = 'https://www.youtube.com/embed/' + vId + '?autoplay=1';
        } else if (videoUrl.indexOf('youtu.be/') !== -1) {
            var vId = videoUrl.split('youtu.be/')[1];
            embedUrl = 'https://www.youtube.com/embed/' + vId + '?autoplay=1';
        }
        $('#gobikeModalIframe').attr('src', embedUrl);
        $('#gobikeVideoModal').fadeIn(250);
    }

    function closeVideoModal() {
        $('#gobikeVideoModal').fadeOut(200, function() {
            $('#gobikeModalIframe').attr('src', '');
        });
    }

    $('.btn-open-video, .thumb-video-btn').on('click', function(e) {
        e.preventDefault();
        var vUrl = $(this).data('video');
        if (vUrl) {
            openVideoModal(vUrl);
        }
    });

    $('#btnCloseVideoModal, #gobikeVideoModal').on('click', function(e) {
        if (e.target === this) {
            closeVideoModal();
        }
    });

    // 5. Thêm Nút [Mua Ngay] Tự Động Vào Form Giỏ Hàng
    function enhanceAddToCartForm() {
        var $form = $('form.cart');
        if (!$form.length) return;

        // Nếu chưa có nút Mua ngay trong form, bổ sung nút Mua ngay
        if (!$form.find('.btn-gobike-buy-now').length) {
            var $submitBtn = $form.find('button[type="submit"].single_add_to_cart_button');
            if ($submitBtn.length) {
                // Đổi text nút thêm giỏ cho sắc nét
                $submitBtn.addClass('btn-gobike-add-cart').html('<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;vertical-align:-2px;"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg> Thêm vào giỏ hàng');
                
                var $buyNowBtn = $('<button type="button" class="button alt btn-gobike-buy-now"><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="margin-right:6px;vertical-align:-2px;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg> Mua ngay</button>');
                $submitBtn.after($buyNowBtn);

                $buyNowBtn.on('click', function(e) {
                    e.preventDefault();
                    if (!$form.find('input[name="gobike_is_buy_now"]').length) {
                        $form.append('<input type="hidden" name="gobike_is_buy_now" value="1" />');
                    }
                    $submitBtn.trigger('click');
                });
            }
        }
    }

    enhanceAddToCartForm();
    $(document).ajaxComplete(enhanceAddToCartForm);

    // 6. Tabs Lọc Sản Phẩm Liên Quan
    $('.related-filter-tabs .tab-filter-btn').on('click', function() {
        $('.related-filter-tabs .tab-filter-btn').removeClass('active');
        $(this).addClass('active');
    });
});
</script>
