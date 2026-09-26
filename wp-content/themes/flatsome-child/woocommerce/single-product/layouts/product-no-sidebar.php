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

        // Khởi tạo Carousel Sản Phẩm Liên Quan (5 sản phẩm, điều hướng 2 bên - Chuẩn Ảnh 2)
        var relatedSwiper = new Swiper('.gobike-related-carousel-swiper', {
            slidesPerView: 5,
            spaceBetween: 14,
            watchOverflow: true,
            navigation: {
                nextEl: '.rel-btn-next-side',
                prevEl: '.rel-btn-prev-side',
            },
            breakpoints: {
                0: {
                    slidesPerView: 2,
                    spaceBetween: 8
                },
                550: {
                    slidesPerView: 3,
                    spaceBetween: 10
                },
                768: {
                    slidesPerView: 4,
                    spaceBetween: 12
                },
                1024: {
                    slidesPerView: 5,
                    spaceBetween: 14
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

    // 2. Chuyển đổi 5 Tabs nội dung & Liên kết nội bộ tab
    $(document).on('click', 'a[href^="#tab-"]', function(e) {
        var targetId = $(this).attr('href');
        if ($(targetId).length && $('.gobike-tabs-nav-list a[href="' + targetId + '"]').length) {
            e.preventDefault();
            
            $('.gobike-tabs-nav-list .tab-nav-item').removeClass('active');
            $('.gobike-tabs-nav-list a[href="' + targetId + '"]').parent('.tab-nav-item').addClass('active');

            $('.gobike-tab-panel').removeClass('active');
            $(targetId).addClass('active');

            // Cuộn mượt mà đến đầu nội dung tab
            $('html, body').animate({
                scrollTop: $('#gobikeProductTabsSection').offset().top - 70
            }, 300);

            // Nếu bấm viết đánh giá, focus vào textarea bình luận
            if ($(this).hasClass('btn-write-review-outline') || $(this).hasClass('btn-write-review')) {
                setTimeout(function() {
                    $('#comment').focus();
                }, 350);
            }
        }
    });

    // 3. Đóng mở Accordion FAQ (hỗ trợ cả Tab 1 và Tab 5)
    $(document).on('click', '.faq-item .faq-question', function() {
        var $item = $(this).closest('.faq-item');
        var $grid = $item.closest('.faq-accordion-grid, .desc-faq-grid');
        var $answer = $item.find('.faq-answer');
        var $icon = $(this).find('.faq-icon');

        if ($item.hasClass('open')) {
            $answer.slideUp(250);
            $item.removeClass('open');
            $icon.text('+');
        } else {
            $grid.find('.faq-answer').slideUp(250);
            $grid.find('.faq-item').removeClass('open');
            $grid.find('.faq-icon').text('+');

            $answer.slideDown(250);
            $item.addClass('open');
            $icon.text('−');
        }
    });

    // Mở sẵn câu hỏi đầu tiên ở các khối FAQ
    $('.faq-accordion-grid, .desc-faq-grid').each(function() {
        $(this).find('.faq-item:first').addClass('open').find('.faq-answer').show().end().find('.faq-icon').text('−');
    });

    // 4. Thu gọn / Mở rộng nội dung mô tả sản phẩm
    $(document).on('click', '#btnToggleDescContent', function() {
        var $wrap = $('#descContentCollapsible');
        var isExpanded = $wrap.hasClass('expanded');

        if (isExpanded) {
            $wrap.removeClass('expanded');
            $(this).find('.toggle-txt').text('Xem thêm');
            $('html, body').animate({
                scrollTop: $('#gobikeProductTabsSection').offset().top - 70
            }, 300);
        } else {
            $wrap.addClass('expanded');
            $(this).find('.toggle-txt').text('Thu gọn');
        }
    });

    // Tự động ẩn nút Xem thêm nếu nội dung ngắn
    function checkDescContentHeight() {
        var $inner = $('#descContentCollapsible .desc-content-inner');
        if ($inner.length && $inner[0].scrollHeight <= 500) {
            $('#descContentCollapsible').addClass('expanded');
            $('.desc-content-gradient, .desc-content-btn-wrap').hide();
        }
    }
    checkDescContentHeight();

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

    // 5. Tối ưu Swatches Màu Sắc & Nút Mua Ngay
    function enhanceSwatches() {
        $('.ux-swatches').each(function() {
            var $swatches = $(this);
            $swatches.find('.ux-swatch').each(function() {
                var $swatch = $(this);
                var val = ($swatch.data('value') || $swatch.attr('title') || $swatch.text() || '').toString().toLowerCase();

                // Nếu có nhiều hơn 1 chấm màu, xóa các chấm thừa
                if ($swatch.find('.ux-swatch__color').length > 1) {
                    $swatch.find('.ux-swatch__color').slice(1).remove();
                }

                if (!$swatch.find('.ux-swatch__color').length) {
                    var colorHex = '#64748b'; // Mặc định xám
                    if (val.indexOf('den') !== -1 || val.indexOf('đen') !== -1) {
                        colorHex = '#183b32';
                        if (val.indexOf('reu') !== -1 || val.indexOf('rêu') !== -1) colorHex = '#14382c';
                    } else if (val.indexOf('xam') !== -1 || val.indexOf('xám') !== -1 || val.indexOf('titan') !== -1) {
                        colorHex = '#64748b';
                    } else if (val.indexOf('trang') !== -1 || val.indexOf('trắng') !== -1 || val.indexOf('ngoc') !== -1 || val.indexOf('ngọc') !== -1) {
                        colorHex = '#ffffff';
                    } else if (val.indexOf('do') !== -1 || val.indexOf('đỏ') !== -1) {
                        colorHex = '#b91c1c';
                    } else if (val.indexOf('duong') !== -1 || val.indexOf('dương') !== -1 || val.indexOf('bien') !== -1 || val.indexOf('biển') !== -1) {
                        colorHex = '#1d4ed8';
                    } else if (val.indexOf('reu') !== -1 || val.indexOf('rêu') !== -1 || val.indexOf('la') !== -1) {
                        colorHex = '#005a36';
                    } else if (val.indexOf('cam') !== -1) {
                        colorHex = '#ea580c';
                    } else if (val.indexOf('vang') !== -1 || val.indexOf('vàng') !== -1) {
                        colorHex = '#eab308';
                    }

                    var $colorDot = $('<span class="ux-swatch__color" style="background-color:' + colorHex + '"></span>');
                    $swatch.prepend($colorDot);
                }
            });
        });
    }

    function enhanceAddToCartForm() {
        var $form = $('form.cart');
        if (!$form.length) return;

        // Xóa triệt để promotion-info nếu bị chèn bởi hook cũ
        $('.promotion-info').remove();

        // Xóa chữ "Số lượng" dư thừa lặp lại bên trong ô quantity nếu có
        $form.find('.quantity > span').remove();
        $('.gobike-qty-stock-row .quantity > span').remove();

        // Đảm bảo nút [Thêm vào giỏ hàng] có đầy đủ SVG icon
        var $submitBtn = $form.find('button[type="submit"].single_add_to_cart_button');
        if ($submitBtn.length && !$submitBtn.find('svg').length) {
            $submitBtn.addClass('btn-gobike-add-cart').html('<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:6px;vertical-align:-2px;"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg> <span>Thêm vào giỏ hàng</span>');
        }

        // Bổ sung nút Mua Ngay nếu template chưa có
        if (!$form.find('.btn-gobike-buy-now').length && $submitBtn.length) {
            var $buyNowBtn = $('<button type="button" class="button alt btn-gobike-buy-now"><svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor" style="margin-right:6px;vertical-align:-2px;"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg> <span>Mua ngay</span></button>');
            $submitBtn.after($buyNowBtn);
        }

        enhanceSwatches();
    }

    // Xử lý Click Nút [Mua Ngay]
    $(document).on('click', '.btn-gobike-buy-now', function(e) {
        e.preventDefault();
        var $form = $(this).closest('form.cart');
        if (!$form.length) $form = $('form.cart');
        var $submitBtn = $form.find('button[type="submit"].single_add_to_cart_button');

        if ($submitBtn.hasClass('disabled') || $submitBtn.is(':disabled')) {
            $submitBtn.trigger('click');
            return false;
        }

        if (!$form.find('input[name="gobike_is_buy_now"]').length) {
            $form.append('<input type="hidden" name="gobike_is_buy_now" value="1" />');
        }
        if (!$form.find('input[name="is_buy_now"]').length) {
            $form.append('<input type="hidden" name="is_buy_now" value="1" />');
        }
        $submitBtn.trigger('click');
    });

    // Xử lý Tăng Giảm Số Lượng mượt mà (Đồng bộ nút [-] và [+])
    $(document).on('click', '.gobike-qty-stock-row .quantity .button.minus, .gobike-add-to-cart-wrapper .quantity .button.minus', function(e) {
        e.preventDefault();
        var $qty = $(this).closest('.quantity').find('input.qty');
        var current = parseFloat($qty.val()) || 1;
        var min = parseFloat($qty.attr('min')) || 1;
        var step = parseFloat($qty.attr('step')) || 1;
        if (current > min) {
            $qty.val(current - step).trigger('change');
        }
    });

    $(document).on('click', '.gobike-qty-stock-row .quantity .button.plus, .gobike-add-to-cart-wrapper .quantity .button.plus', function(e) {
        e.preventDefault();
        var $qty = $(this).closest('.quantity').find('input.qty');
        var current = parseFloat($qty.val()) || 1;
        var max = parseFloat($qty.attr('max'));
        var step = parseFloat($qty.attr('step')) || 1;
        if (!max || current < max) {
            $qty.val(current + step).trigger('change');
        }
    });

    enhanceAddToCartForm();
    $(document).ajaxComplete(enhanceAddToCartForm);

    // 6. Tabs Lọc Sản Phẩm Liên Quan
    $('.related-filter-tabs .tab-filter-btn').on('click', function() {
        $('.related-filter-tabs .tab-filter-btn').removeClass('active');
        $(this).addClass('active');
    });
});
</script>
