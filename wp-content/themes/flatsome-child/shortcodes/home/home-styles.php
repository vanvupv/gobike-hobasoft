<?php
/**
 * TẬP TRUNG TOÀN BỘ CSS TÙY BIẾN GOBIKE (NẠP QUA HOOK wp_head)
 * 
 * Ưu điểm:
 * 1. Không cần quản lý version (?ver=...).
 * 2. Không bao giờ bị dính cache trình duyệt (F5 là cập nhật CSS mới ngay lập tức).
 * 3. Quản lý toàn bộ CSS của Website, Trang chủ, Single Product tại 1 nơi duy nhất.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_head', 'gobike_custom_styles_output', 100);

function gobike_custom_styles_output()
{
    ?>
    <style id="gobike-custom-css">
        /* ==========================================================================
                                               CHUẨN HÓA TOÀN TRANG: BỎ HẾT BOX-SHADOW & ĐỔ BÓNG
                                               ========================================================================== */
        *,
        *::before,
        *::after {
            box-shadow: none !important;
            -webkit-box-shadow: none !important;
        }

        /* ==========================================================================
                                               1. THIẾT LẬP CHUNG & TIỆN ÍCH (General & Utilities)
                                               ========================================================================== */
        .m-0 {
            margin: 0px !important;
        }

        b,
        strong {
            font-weight: 600;
        }

        .vp-row-custom .col {
            padding-bottom: 0px;
        }

        .tab-product-related .related-products-wrapper .row {
            gap: 10px;
        }

        .shop-page-title.product-page-title {
            padding-top: 30px;
        }

        .row .section {
            padding-left: 30px;
            padding-right: 0px;
        }

        .col-divided+.col {
            padding: 0px !important;
        }

        /* Xóa padding-right 30px và đường viền phân cách của cột nội dung #content */
        #content.col-divided {
            padding-right: 0 !important;
            border-right: none !important;
        }

        /* Tin tức blog trang chủ */
        .vp-blog-post.row {
            row-gap: 10px;
        }

        .vp-blog-post .col-inner {
            box-shadow: none !important;
        }

        .vp-blog-post .col-inner .post-title a {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            overflow: hidden;
        }

        /* Tiêu đề tin tức tràn viền 100% */
        .title-news {
            width: auto !important;
            margin-left: -30px !important;
            margin-right: -30px !important;
            padding: 0 !important;
        }

        .title-news h3 {
            width: 100% !important;
            display: block !important;
            box-sizing: border-box !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
            text-align: center !important;
            line-height: 1.5 !important;
            padding: 10px 0 !important;
        }

        /* .medium-logo-center .flex-left {
                                                        flex: none;
                                                    } */

        /* ==========================================================================
                                                       2. QUẢNG CÁO CỐ ĐỊNH 2 BÊN MÀN HÌNH (.qc-left, .qc-right)
                                                       ========================================================================== */
        .qc-left,
        .qc-right {
            position: fixed;
            top: 25%;
            z-index: 99999;
            width: 120px;
        }

        .qc-left {
            left: 50%;
            margin-left: -787px;
        }

        .qc-right {
            right: 50%;
            margin-right: -787px;
        }

        .qc-left a,
        .qc-right a {
            display: block;
            margin-bottom: 10px;
        }

        @media screen and (max-width: 1574px) {

            .qc-left,
            .qc-right {
                display: none !important;
            }
        }

        /* ==========================================================================
                                                       3. HEADER & THANH ĐIỀU HƯỚNG (Sticky Header, Search Form, Vertical Menu)
                                                       ========================================================================== */
        /* Giữ nền trắng tinh từ đầu, khử độ trễ chuyển động chống nhấp nháy */
        #header,
        .header-wrapper,
        .header-main,
        .header-bottom {
            background-color: #ffffff !important;
        }

        .header-wrapper,
        .header-wrapper.stuck,
        .header-main {
            transition: none !important;
            -webkit-transition: none !important;
        }

        .header-wrapper.stuck {
            box-shadow: none !important;
        }

        /* Ô tìm kiếm header chính */
        header.header .header-search-form input[type="search"] {
            border: 1px solid #149d29;
        }

        /* Ẩn / Hiện Form tìm kiếm hàng trên theo trạng thái Sticky */
        header.header .header-wrapper:not(.stuck) .html_nav_position_text,
        header.header:not(.active) .html_nav_position_text {
            display: none !important;
        }

        header.header .header-wrapper.stuck .html_nav_position_text,
        header.header.active .html_nav_position_text {
            display: block !important;
        }

        header.header.active .header-button-2,
        header.header .header-button-2 span,
        header.header .header-wrapper.stuck .header-button-2,
        header.header.active .header-button-2 .header-button span {
            display: none !important;
        }

        /* Tùy chỉnh cỡ chữ Vertical Menu */
        .header-vertical-menu__title {
            font-size: 14px !important;
            font-weight: 700 !important;
            text-transform: uppercase;
        }

        .header-vertical-menu .ux-nav-vertical-menu>li>a {
            font-size: 14px !important;
            font-weight: 500;
        }

        .header-vertical-menu .ux-nav-vertical-menu .sub-menu li a {
            font-size: 13px !important;
        }

        /* Responsive Header trên Mobile / Tablet (<= 849px) */
        @media screen and (max-width: 849px) {
            header.header .header-wrapper.stuck .show-for-medium.flex-right .mobile-nav {
                display: flex !important;
                align-items: center !important;
                width: 100% !important;
            }

            header.header .header-wrapper.stuck .html_nav_position_text {
                flex: 1 1 auto !important;
                margin-right: 8px !important;
            }

            header.header .header-wrapper.stuck .html_nav_position_text .searchform-wrapper {
                width: 100% !important;
            }

            header.header .header-wrapper.stuck .html_nav_position_text input[type="search"] {
                height: 36px !important;
                border-radius: 20px !important;
                font-size: 13px !important;
            }
        }

        /* ==========================================================================
                                                       4. CHÂN TRANG & FORM BẢN TIN (Footer Newsletter & Form)
                                                       ========================================================================== */
        .footer-wrapper .wpcf7 {
            display: flex;
            justify-content: center;
        }

        .footer-wrapper .wpcf7-form.init,
        .footer-wrapper .wpcf7-form.init .newsletter-form .wpcf7-form-control,
        .footer-wrapper .wpcf7-form.init .newsletter-form .wpcf7-form-control.newsletter-submit {
            margin: 0px !important;
        }

        .newsletter-form {
            width: 100%;
        }

        .newsletter-input-group {
            width: 100%;
            max-width: 405px;
        }

        .newsletter-input-group p {
            display: flex;
            align-items: stretch;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .newsletter-input-group .wpcf7-form-control-wrap {
            flex: 1;
            min-width: 0;
        }

        .newsletter-input-group .newsletter-email {
            display: block;
            width: 100%;
            height: 40px;
            padding: 0 10px;
            border: 1px solid #ddd;
            border-right: none;
            border-radius: 3px 0 0 3px;
            background: #fff;
            color: #333;
            font-size: 13px;
            box-sizing: border-box;
            outline: none;
        }

        .newsletter-input-group .newsletter-email::placeholder {
            color: #999;
        }

        .newsletter-input-group .newsletter-submit {
            flex: 0 0 90px;
            width: 90px;
            height: 40px;
            padding: 0 10px;
            border: 0;
            border-radius: 0 3px 3px 0;
            background: #149d29;
            color: #fff;
            font-size: 13px;
            cursor: pointer;
            box-sizing: border-box;
        }

        .newsletter-input-group .newsletter-submit:hover {
            background: #c00000;
        }

        .newsletter-input-group .wpcf7-spinner {
            display: none;
        }

        /* ==========================================================================
                                                       5. MENU ĐIỀU HƯỚNG CHÂN TRANG MOBILE (Mobile Bottom Bar & FAB Call)
                                                       ========================================================================== */
        @media screen and (min-width: 850px) {

            .footer-menu-mobile,
            .gobike-bottom-bar-nav {
                display: none !important;
            }
        }

        @media screen and (max-width: 849px) {
            body {
                padding-bottom: 60px !important;
            }

            .gobike-bottom-bar-nav,
            .footer-menu-mobile {
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                right: 0 !important;
                width: 100% !important;
                height: 54px !important;
                background: #ffffff !important;
                box-shadow: none !important;
                border-top: 1px solid #eeeeee !important;
                z-index: 99999 !important;
                display: block !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .gobike-bottom-bar-nav .wrap-fixed-footer,
            .footer-menu-mobile ul#menu-footer-menu-mobile {
                display: flex !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-around !important;
                margin: 0 !important;
                padding: 0 !important;
                list-style: none !important;
                height: 100% !important;
                width: 100% !important;
            }

            .gobike-bottom-bar-nav .wrap-fixed-footer>li,
            .footer-menu-mobile ul#menu-footer-menu-mobile li {
                flex: 1 1 20% !important;
                max-width: 20% !important;
                height: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                text-align: center !important;
                position: relative !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }

            .gobike-bottom-bar-nav .wrap-fixed-footer>li>a,
            .footer-menu-mobile ul#menu-footer-menu-mobile li a.nav-top-link {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                width: 100% !important;
                height: 100% !important;
                text-decoration: none !important;
                color: #4a5568 !important;
                padding: 4px 0 !important;
                gap: 2px !important;
                font-size: 10.5px !important;
                font-weight: 600 !important;
                line-height: 1.2 !important;
                letter-spacing: 0 !important;
                white-space: nowrap !important;
                transition: color 0.2s ease !important;
            }

            .gobike-bottom-bar-nav .wrap-fixed-footer>li>a span {
                font-size: 11px !important;
                font-weight: 500 !important;
                color: #4a5568 !important;
            }

            .footer-menu-mobile .ux-menu-icon {
                width: 22px !important;
                height: 22px !important;
                margin: 0 0 3px 0 !important;
                display: block !important;
                object-fit: contain !important;
            }

            .footer-menu-mobile ul#menu-footer-menu-mobile li.current-menu-item a.nav-top-link,
            .footer-menu-mobile ul#menu-footer-menu-mobile li.active a.nav-top-link,
            .gobike-bottom-bar-nav .wrap-fixed-footer>li.active a {
                color: #149D29 !important;
            }

            .gobike-bottom-bar-nav .wrap-fixed-footer>li.item-phone-fab {
                overflow: visible !important;
            }

            .gobike-bottom-bar-nav .wrap-fixed-footer>li.item-phone-fab>a.btn-phone-call {
                position: relative !important;
                overflow: visible !important;
            }

            .gobike-bottom-bar-nav .phone-circle-btn {
                position: absolute !important;
                top: -18px !important;
                width: 48px !important;
                height: 48px !important;
                border-radius: 50% !important;
                background: #e52828 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                box-shadow: none !important;
                z-index: 2 !important;
                transition: transform 0.2s ease !important;
            }

            .gobike-bottom-bar-nav .phone-circle-glow {
                position: absolute !important;
                top: -21px !important;
                width: 54px !important;
                height: 54px !important;
                border-radius: 50% !important;
                background: rgba(229, 40, 40, 0.35) !important;
                z-index: 1 !important;
                animation: gobike-pulse-ring 1.8s infinite ease-out !important;
            }

            @keyframes gobike-pulse-ring {
                0% {
                    transform: scale(0.9);
                    opacity: 0.8;
                }

                50% {
                    transform: scale(1.25);
                    opacity: 0.15;
                }

                100% {
                    transform: scale(1.4);
                    opacity: 0;
                }
            }

            .gobike-bottom-bar-nav .btn_phone_txt {
                font-size: 11px !important;
                font-weight: 600 !important;
                color: #2b3a4a !important;
                margin-top: 32px !important;
                line-height: 1 !important;
                white-space: nowrap !important;
            }

            .gobike-bottom-bar-nav .zalo-badge-wrap {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                position: relative !important;
            }
        }

        /* ==========================================================================
                                                       6. TRANG CHỦ: BANNER SLIDER & TIN TỨC (.banner-home)
                                                       ========================================================================== */
        .banner-home {
            background: #f0f0f0;
            margin-top: 0px !important;
            padding: 10px 10px 0px !important;
        }

        .banner-home .box_left {
            display: none !important;
        }

        .banner-home .swiper-slide img,
        .banner-home .image-ads img {
            width: 100% !important;
            object-fit: cover !important;
            object-position: center !important;
            display: block !important;
        }

        .mySwiper2 .swiper-navigation-icon {
            display: none;
        }

        /* --- 6.1. Desktop (>= 850px) --- */
        @media screen and (min-width: 850px) {
            .banner-home .row {
                display: flex !important;
                align-items: stretch !important;
            }

            .banner-home .box_center {
                width: 66.666% !important;
                max-width: 66.666% !important;
                flex: 0 0 66.666% !important;
            }

            .banner-home .box_right {
                width: 33.333% !important;
                max-width: 33.333% !important;
                flex: 0 0 33.333% !important;
            }

            .banner-home .box_center .swiper-container {
                width: 100% !important;
                height: 380px !important;
                max-height: 380px !important;
                display: flex !important;
                flex-direction: column !important;
                border-radius: 8px !important;
                border: 1px solid #eee !important;
                box-shadow: none !important;
                overflow: hidden !important;
                background: #fff !important;
                box-sizing: border-box !important;
            }

            .banner-home .mySwiper2 {
                width: 100% !important;
                height: 320px !important;
                max-height: 320px !important;
                flex: 0 0 320px !important;
                overflow: hidden !important;
                position: relative !important;
            }

            .banner-home .mySwiper2 .swiper-wrapper,
            .banner-home .mySwiper2 .swiper-slide,
            .banner-home .mySwiper2 .swiper-slide a,
            .banner-home .mySwiper2 .swiper-slide img {
                height: 320px !important;
                min-height: 320px !important;
                max-height: 320px !important;
            }

            .banner-home .mySwiper {
                width: 100% !important;
                height: 60px !important;
                max-height: 60px !important;
                flex: 0 0 60px !important;
                background: #fff !important;
                border-top: 1px solid #eee !important;
                padding: 0 !important;
                box-sizing: border-box !important;
                overflow: hidden !important;
            }

            .banner-home .mySwiper .swiper-wrapper {
                height: 60px !important;
            }

            .banner-home .mySwiper .swiper-slide {
                height: 60px !important;
                max-height: 60px !important;
                padding: 6px 4px 10px !important;
                font-size: 11px !important;
                line-height: 1.35 !important;
                white-space: normal !important;
                cursor: pointer !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
                text-align: center !important;
                border-right: 1px solid #f5f5f5 !important;
                box-sizing: border-box !important;
            }

            .banner-home .mySwiper .swiper-slide:last-child {
                border-right: none !important;
            }

            .banner-home .box_right .news-home-box {
                height: 380px !important;
                max-height: 380px !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
                box-sizing: border-box !important;
                overflow: hidden !important;
            }

            .banner-home .box_right .news-home-box .news-list {
                flex: 1 1 auto !important;
                overflow: hidden !important;
            }

            .banner-home .box_right .news-home-box .bottom-news-image {
                margin-top: auto !important;
                padding-top: 6px !important;
            }

            .banner-home .box_right .news-home-box .bottom-news-image img {
                width: 100% !important;
                height: 115px !important;
                max-height: 115px !important;
                object-fit: cover !important;
                display: block !important;
                border-radius: 6px !important;
            }

            .banner-home-pagination {
                display: none !important;
            }
        }

        /* --- 6.2. Mobile & Tablet (<= 849px) --- */
        @media screen and (max-width: 849px) {
            .banner-home {
                padding: 10px !important;
            }

            .banner-home .box_center,
            .banner-home .box_right {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
                margin: 0 auto !important;
                padding: 0 !important;
            }

            .banner-home .swiper-container {
                width: 100% !important;
                height: auto !important;
                overflow: visible !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                background: transparent !important;
            }

            .banner-home .mySwiper2 {
                width: 100% !important;
                height: auto !important;
                border-radius: 12px !important;
                overflow: hidden !important;
            }

            .banner-home .mySwiper2 .swiper-slide {
                background: #fff !important;
                border-radius: 12px !important;
                overflow: hidden !important;
            }

            .banner-home .mySwiper2 .swiper-slide a {
                display: block !important;
                width: 100% !important;
                line-height: 0 !important;
            }

            .banner-home .mySwiper2 .swiper-slide img {
                width: 100% !important;
                height: auto !important;
                object-fit: cover !important;
                display: block !important;
                margin: 0 auto !important;
                border-radius: 12px !important;
            }

            .banner-home .mySwiper2 .swiper-button-next,
            .banner-home .mySwiper2 .swiper-button-prev,
            .banner-home .mySwiper {
                display: none !important;
            }

            .banner-home .banner-home-pagination {
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                position: relative !important;
                margin: 12px auto 0 !important;
                width: 100% !important;
                gap: 7px !important;
                z-index: 10 !important;
            }

            .banner-home .banner-home-pagination .swiper-pagination-bullet {
                width: 8px !important;
                height: 8px !important;
                background: #b0b0b0 !important;
                opacity: 0.8 !important;
                border-radius: 50% !important;
                margin: 0 !important;
                display: inline-block !important;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
                cursor: pointer !important;
            }

            .banner-home .banner-home-pagination .swiper-pagination-bullet-active {
                width: 24px !important;
                height: 8px !important;
                background: #e52828 !important;
                border-radius: 4px !important;
                opacity: 1 !important;
            }
        }

        /* ==========================================================================
                                                       7. TRANG CHỦ: DANH MỤC CUỘN NGANG (.row_cat)
                                                       ========================================================================== */
        .row_cat {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .row_cat::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        .row_cat>.col {
            min-width: 150px !important;
            padding: 0 6px !important;
        }

        .row_cat>.col .col-inner {
            height: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .row_cat .icon-box-text h3 {
            font-size: 11px !important;
            line-height: 1.3 !important;
            margin-top: 8px !important;
            white-space: normal !important;
        }

        /* ==========================================================================
                                                       8. TRANG CHỦ: KHỐI SẢN PHẨM THEO DANH MỤC (CHUẨN ẢNH 2)
                                                       - Desktop: 8 sản phẩm (4 cột x 2 hàng) + 1 Cột Banner dọc bên phải
                                                       - Tablet & Mobile: Slider trượt 2 sản phẩm / lượt + Banner ở cuối
                                                       ========================================================================== */
        .gobike-category-block-wrapper {
            max-width: 1230px;
            margin: 0 auto 35px auto;
            padding: 0 10px;
            box-sizing: border-box;
        }

        /* Header của khối */
        .gobike-category-block-wrapper .gobike-block-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #149d29;
            padding-bottom: 8px;
            margin-bottom: 16px;
            gap: 15px;
        }

        .gobike-category-block-wrapper .header-left {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .gobike-category-block-wrapper .header-brand-badge {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: #149d29;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .gobike-category-block-wrapper .block-title {
            margin: 0;
            font-size: 19px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }

        .gobike-category-block-wrapper .block-title a {
            color: #149d29;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .gobike-category-block-wrapper .block-title a:hover {
            color: #149d29;
        }

        .gobike-category-block-wrapper .block-slogan {
            font-size: 13.5px;
            color: #64748b;
            font-weight: 400;
            margin-left: 6px;
        }

        .gobike-category-block-wrapper .header-right .view-all-link {
            font-size: 13.5px;
            font-weight: 700;
            color: #149d29;
            text-decoration: none;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
        }

        .gobike-category-block-wrapper .header-right .view-all-link:hover {
            color: #149d29;
            transform: translateX(3px);
        }

        /* Layout chính: Khung sản phẩm + Cột Banner */
        .gobike-cat-main-content {
            display: flex;
            gap: 12px;
            align-items: stretch;
            width: 100%;
        }

        /* Cột sản phẩm bên trái */
        .gobike-products-container {
            flex: 1 1 calc(100% - 240px);
            min-width: 0;
        }

        /* Desktop: Lưới 4 cột x 2 hàng = 8 sản phẩm */
        @media screen and (min-width: 1025px) {
            .gobike-cat-swiper {
                overflow: visible !important;
                width: 100% !important;
            }

            .gobike-products-grid {
                display: grid !important;
                grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
                gap: 8px !important;
                transform: none !important;
                width: 100% !important;
            }

            .gobike-pcard-slide {
                width: auto !important;
                height: 100% !important;
                margin: 0 !important;
                min-width: 0 !important;
            }

            .gobike-cat-pagination {
                display: none !important;
            }
        }

        /* Thẻ sản phẩm chuẩn Ảnh 2 */
        .gobike-pcard {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 10px 12px 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            height: 100%;
            min-width: 0;
            box-sizing: border-box;
            transition: all 0.25s ease;
        }

        .gobike-pcard:hover {
            border-color: #149d29;
            box-shadow: none !important;
            transform: translateY(-2px);
        }

        /* Badge giảm giá góc trên trái */
        .gobike-card-discount {
            position: absolute;
            top: 8px;
            left: 8px;
            z-index: 2;
            background: #e5101d;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            line-height: 1.2;
            box-shadow: none !important;
        }

        /* Khung ảnh xe đạp */
        .gobike-pcard-thumb {
            width: 100%;
            aspect-ratio: 1 / 1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            overflow: hidden;
            background: #ffffff;
            /* padding: 8px; */
            box-sizing: border-box;
        }

        .gobike-pcard-thumb a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }

        .gobike-pcard-thumb img {
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
            margin: 0 auto;
            transition: transform 0.3s ease;
        }

        .gobike-pcard:hover .gobike-pcard-thumb img {
            transform: scale(1.05);
        }

        /* Nội dung thẻ */
        .gobike-pcard-body {
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            justify-content: space-between;
        }

        .gobike-pcard-title {
            font-size: 18px;
            font-weight: 600;
            line-height: 1.35;
            margin: 0 0 8px 0;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            text-align: left;
        }

        .gobike-pcard-title a {
            color: #1e293b;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .gobike-pcard-title a:hover {
            color: #149d29;
        }

        /* 3 Thông số có icon (GPS km, kg, W) - 3 cột bằng nhau, cắt chữ dài thành dấu chấm */
        .gobike-pcard-specs {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 4px;
            margin-bottom: 8px;
            background: #f8fafc;
            border-radius: 4px;
            padding: 6px 4px;
            border: 1px solid #f1f5f9;
            box-sizing: border-box;
        }

        .gobike-pcard-specs .spec-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 3px;
            min-width: 0;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            overflow: hidden;
        }

        .gobike-pcard-specs .spec-badge svg {
            flex-shrink: 0;
            width: 16px;
            height: 16px;
            color: #149d29;
        }

        .gobike-pcard-specs .spec-badge span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            min-width: 0;
        }

        /* Cụm giá tiền: Bình thường 2 giá cùng 1 hàng, khi giá dài sẽ rơi giá cũ xuống dòng 2, giá đỏ dài quá 100% dòng thì hiển thị ... */
        .gobike-pcard-price-box {
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: baseline !important;
            align-content: flex-start !important;
            gap: 4px 8px;
            margin-bottom: 8px;
            width: 100%;
            min-width: 0;
        }

        .gobike-pcard-price-box .price-current {
            color: #d70018 !important;
            font-size: 16px !important;
            font-weight: 700 !important;
            text-decoration: none !important;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            flex-shrink: 0;
            display: inline-block !important;
        }

        .gobike-pcard-price-box .price-current .woocommerce-Price-amount,
        .gobike-pcard-price-box .price-current bdi {
            color: #d70018 !important;
            font-size: 16px !important;
            font-weight: 700 !important;
            text-decoration: none !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            display: inline-block;
            vertical-align: bottom;
        }

        .gobike-pcard-price-box .price-old {
            color: #94a3b8 !important;
            font-size: 12px !important;
            text-decoration: line-through !important;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            flex-shrink: 0;
            display: inline-block !important;
            opacity: 1 !important;
            margin: 0 !important;
        }

        .gobike-pcard-price-box .price-old .woocommerce-Price-amount,
        .gobike-pcard-price-box .price-old bdi {
            color: #94a3b8 !important;
            font-size: 12px !important;
            text-decoration: line-through !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
            display: inline-block;
            vertical-align: bottom;
        }

        /* Nút Xem chi tiết */
        .gobike-pcard-btn {
            display: block;
            width: 100%;
            text-align: center;
            border: 1px solid #149d29;
            color: #149d29;
            background: #ffffff;
            padding: 6px 8px;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none !important;
            transition: all 0.2s ease;
            box-sizing: border-box;
            cursor: pointer;
            margin-top: auto;
        }

        .gobike-pcard-btn:hover {
            background: #149d29;
            color: #ffffff !important;
            box-shadow: none !important;
        }

        /* CỘT BANNER DỌC (CHIẾM ~20% BÊN PHẢI TRÊN DESKTOP, Ở CUỐI TRÊN MOBILE) */
        .gobike-cat-banner-col {
            flex: 0 0 230px;
            width: 230px;
            min-width: 230px;
            display: flex;
            flex-direction: column;
            border-radius: 8px;
            overflow: hidden;
            box-sizing: border-box;
        }

        .gobike-cat-banner-link {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            width: 100%;
            border-radius: 8px;
            overflow: hidden;
            text-decoration: none !important;
            position: relative;
            box-sizing: border-box;
        }

        .gobike-cat-banner-link.custom-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: 8px;
        }

        .gobike-cat-banner-link.branded-card {
            background: linear-gradient(180deg, #0b2f4c 0%, #0d4b68 35%, #153852 70%, #0a1f2e 100%);
            color: #fff;
            padding: 24px 18px 18px 18px;
            box-shadow: none !important;
        }

        .gobike-cat-banner-link.branded-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 80% 20%, rgba(20, 157, 41, 0.25) 0%, transparent 60%);
            pointer-events: none;
        }

        .banner-brand-logo {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #ffffff;
            margin-bottom: 12px;
            font-style: italic;
        }

        .banner-title {
            font-size: 16px;
            font-weight: 700;
            line-height: 1.35;
            color: #ffffff;
            text-transform: uppercase;
            margin: 0 0 16px 0;
            letter-spacing: 0.5px;
        }

        .banner-features {
            list-style: none;
            margin: 0 0 20px 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .banner-features li {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12.5px;
            font-weight: 500;
            color: #e2e8f0;
            line-height: 1.3;
        }

        .banner-features .chk-icon {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #149d29;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .banner-cta-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #ffffff;
            color: #149d29 !important;
            font-size: 12.5px;
            font-weight: 700;
            padding: 8px 18px;
            border-radius: 25px;
            box-shadow: none !important;
            transition: transform 0.2s ease, background 0.2s ease;
            width: fit-content;
        }

        .banner-cta-btn:hover {
            transform: scale(1.04);
            background: #f8fafc;
        }

        .banner-card-bottom {
            margin-top: 30px;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 12px;
        }

        .banner-slogan {
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 1.5px;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
        }

        /* --- RESPONSIVE TABLET & MOBILE (<= 1024px) ---
                                                       Hiển thị Slide 2 sản phẩm 1 lượt + Banner chuyển xuống ở cuối */
        @media screen and (max-width: 1024px) {
            .gobike-cat-main-content {
                flex-direction: column !important;
                gap: 16px !important;
            }

            .gobike-products-container {
                width: 100% !important;
                flex: none !important;
            }

            /* Swiper kích hoạt: trượt 2 sản phẩm / view */
            .gobike-cat-swiper {
                width: 100% !important;
                overflow: hidden !important;
                padding-bottom: 24px !important;
                position: relative !important;
            }

            .gobike-products-grid {
                display: flex !important;
                flex-wrap: nowrap !important;
                gap: 0 !important;
                box-sizing: border-box !important;
            }

            .gobike-pcard-slide {
                width: calc(50% - 5px) !important;
                flex-shrink: 0 !important;
                box-sizing: border-box !important;
            }

            /* Chấm phân trang Swiper */
            .gobike-cat-pagination {
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                gap: 6px !important;
                position: absolute !important;
                bottom: 0 !important;
                left: 0 !important;
                width: 100% !important;
                z-index: 5 !important;
            }

            .gobike-cat-pagination .swiper-pagination-bullet {
                width: 8px !important;
                height: 8px !important;
                background: #cbd5e1 !important;
                border-radius: 50% !important;
                opacity: 0.8 !important;
                margin: 0 !important;
                transition: all 0.3s ease !important;
                cursor: pointer !important;
            }

            .gobike-cat-pagination .swiper-pagination-bullet-active {
                width: 22px !important;
                background: #149d29 !important;
                border-radius: 4px !important;
                opacity: 1 !important;
            }

            /* Banner chuyển xuống ở cuối khối */
            .gobike-cat-banner-col {
                flex: none !important;
                width: 100% !important;
                min-width: 0 !important;
                order: 2 !important;
            }

            .gobike-cat-banner-link.branded-card {
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
                padding: 20px !important;
                gap: 20px !important;
            }

            .gobike-cat-banner-link.branded-card .banner-card-top {
                flex: 1 1 auto !important;
            }

            .gobike-cat-banner-link.branded-card .banner-card-bottom {
                margin-top: 0 !important;
                border-top: none !important;
                border-left: 1px solid rgba(255, 255, 255, 0.15) !important;
                padding-top: 0 !important;
                padding-left: 20px !important;
                flex: 0 0 auto !important;
            }

            .gobike-category-block-wrapper .block-slogan {
                display: none !important;
            }
        }

        @media screen and (max-width: 640px) {
            .gobike-category-block-wrapper .block-title {
                font-size: 16px !important;
            }

            .gobike-category-block-wrapper .header-right .view-all-link {
                font-size: 12px !important;
            }

            .gobike-cat-banner-link.branded-card {
                flex-direction: column !important;
                text-align: center !important;
            }

            .gobike-cat-banner-link.branded-card .banner-cta-btn {
                margin: 0 auto !important;
            }

            .gobike-cat-banner-link.branded-card .banner-card-bottom {
                border-left: none !important;
                border-top: 1px solid rgba(255, 255, 255, 0.15) !important;
                padding-left: 0 !important;
                padding-top: 12px !important;
                margin-top: 15px !important;
                width: 100% !important;
            }
        }

        /* ==========================================================================
                                                       9. KHỐI SHORTCODE TRANG CHỦ MỞ RỘNG (Flash Sale, Brand Tabs, Video)
                                                       ========================================================================== */
        /* Flash Sale */
        .gobike-home-flashsale-block {
            max-width: 1230px;
            margin: 0 auto 30px auto;
            padding: 16px;
            background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%);
            border: 1px solid #ffd6dc;
            border-radius: 12px;
            box-sizing: border-box;
        }

        .flashsale-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #d90429;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }

        .flashsale-title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            color: #d90429;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .flashsale-header .view-all-link {
            font-size: 13px;
            font-weight: 600;
            color: #d90429;
            text-decoration: none;
        }

        .gobike-flashsale-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
        }

        .gobike-flashsale-item {
            background: #fff;
            border: 1px solid #f0f0f0;
            border-radius: 8px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: all 0.3s ease;
        }

        .gobike-flashsale-item:hover {
            border-color: #d90429;
            box-shadow: none !important;
            transform: translateY(-2px);
        }

        .gobike-flashsale-item .item-thumb {
            position: relative;
            text-align: center;
            margin-bottom: 10px;
            overflow: hidden;
            border-radius: 6px;
        }

        .gobike-flashsale-item .item-thumb img {
            max-width: 100%;
            height: 160px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .gobike-flashsale-item .item-title {
            font-size: 13px;
            font-weight: 600;
            line-height: 1.4;
            color: #333;
            margin: 0 0 8px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Tabs Thương hiệu */
        .gobike-home-brand-tabs-block {
            max-width: 1230px;
            margin: 0 auto 30px auto;
            padding: 0 10px;
            box-sizing: border-box;
        }

        .gobike-brand-tabs-nav {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .brand-tab-btn {
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            border-radius: 25px;
            padding: 8px 20px;
            font-size: 14px;
            font-weight: 600;
            color: #555;
            cursor: pointer;
            transition: all 0.25s ease;
            outline: none;
        }

        .brand-tab-btn:hover {
            border-color: #149d29;
            color: #149d29;
            background: #f0fbf2;
        }

        .brand-tab-btn.active {
            background: #149d29;
            color: #fff;
            border-color: #149d29;
            box-shadow: none !important;
        }

        /* Video Reviews */
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
            box-shadow: none !important;
            transition: all 0.3s ease;
        }

        .gobike-video-thumb {
            position: relative;
            padding-top: 56.25%;
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

        /* ==========================================================================
                                                       10. BỘ LỌC SẢN PHẨM HUSKY / WOOF HÀNG NGANG (.woof_redraw_zone)
                                                       ========================================================================== */
        .woof_redraw_zone {
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 10px 14px !important;
            background: #fff;
            padding: 10px 0;
            width: 100% !important;
        }

        .woof_redraw_zone::before {
            content: "Tìm theo:";
            font-weight: 700;
            color: #d0021b;
            font-size: 14px;
            margin-right: 5px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
        }

        .woof_redraw_zone .woof_container {
            display: inline-flex !important;
            align-items: center !important;
            width: auto !important;
            float: none !important;
            clear: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .woof_redraw_zone .woof_container .woof_container_inner h4,
        .woof_redraw_zone .woof_container_product_visibility {
            display: none !important;
        }

        .woof_redraw_zone .chosen-container {
            width: auto !important;
            min-width: 130px !important;
            max-width: 220px !important;
            font-size: 13px !important;
        }

        .woof_redraw_zone .chosen-container-single .chosen-single {
            height: 36px !important;
            line-height: 34px !important;
            border: 1px solid #e5e5e5 !important;
            border-radius: 6px !important;
            background: #f9f9f9 !important;
            padding: 0 28px 0 12px !important;
            color: #333 !important;
            font-weight: 500 !important;
            box-shadow: none !important;
        }

        .woof_redraw_zone .chosen-container-single .chosen-single div b {
            background-position: 0 7px !important;
        }

        .woof_redraw_zone .chosen-container-multi .chosen-choices {
            height: 36px !important;
            min-height: 36px !important;
            border: 1px solid #e5e5e5 !important;
            border-radius: 6px !important;
            background: #f9f9f9 !important;
            padding: 2px 10px !important;
            display: flex !important;
            align-items: center !important;
            box-shadow: none !important;
        }

        .woof_redraw_zone .chosen-container-multi .chosen-choices li.search-field input[type="text"] {
            color: #333 !important;
            font-weight: 500 !important;
            font-size: 13px !important;
            height: 30px !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .woof_redraw_zone .chosen-container:hover .chosen-single,
        .woof_redraw_zone .chosen-container:hover .chosen-choices,
        .woof_redraw_zone .chosen-container-active .chosen-single,
        .woof_redraw_zone .chosen-container-active .chosen-choices {
            border-color: #d0021b !important;
            background: #fff !important;
        }

        .woof_redraw_zone .chosen-drop {
            border: 1px solid #e5e5e5 !important;
            border-radius: 8px !important;
            box-shadow: none !important;
            padding: 6px 0 !important;
            min-width: 180px !important;
            z-index: 99999 !important;
        }

        .woof_redraw_zone .chosen-results li {
            padding: 7px 14px !important;
            font-size: 13px !important;
            line-height: 1.4 !important;
        }

        .woof_redraw_zone .chosen-results li.highlighted {
            background-color: #d0021b !important;
            color: #fff !important;
        }

        .woof_redraw_zone select.woof_select {
            height: 36px !important;
            min-width: 130px !important;
            padding: 0 28px 0 12px !important;
            border: 1px solid #e5e5e5 !important;
            border-radius: 6px !important;
            background-color: #f9f9f9 !important;
            color: #333 !important;
            font-size: 13px !important;
            font-weight: 500 !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6' viewBox='0 0 10 6'%3E%3Cpath fill='%23666' d='M0 0l5 6 5-6z'/%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: right 10px center !important;
            appearance: none !important;
            -webkit-appearance: none !important;
        }

        .woof_redraw_zone select.woof_select:hover {
            border-color: #d0021b !important;
            background-color: #fff !important;
        }

        /* ==========================================================================
                                                       11. TRANG CHI TIẾT SẢN PHẨM (Single Product Page Elements)
                                                       ========================================================================== */
        /* --- 11.1. Cặp Banner Tiện Ích (Zoom từ tâm) --- */
        @keyframes gobikeZoomFromCenter {
            0% {
                opacity: 0;
                transform: scale(0);
            }

            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        .gobike-dual-banners-row {
            margin-top: 8px !important;
            margin-bottom: 16px !important;
            width: 100% !important;
        }

        .gobike-banner-col {
            padding-bottom: 0 !important;
        }

        .gobike-zoom-banner {
            width: 100%;
            overflow: hidden;
            border-radius: 6px;
            box-shadow: none !important;
            transform-origin: center center !important;
            animation: gobikeZoomFromCenter 0.85s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards !important;
            transition: transform 0.3s ease;
        }

        .gobike-zoom-banner:hover {
            transform: scale(1.02) !important;
            box-shadow: none !important;
        }

        .gobike-zoom-banner a {
            display: block !important;
            width: 100% !important;
        }

        .gobike-zoom-banner img {
            width: 100% !important;
            height: auto !important;
            display: block !important;
            border-radius: 6px !important;
            object-fit: cover !important;
        }

        @media screen and (max-width: 549px) {
            .gobike-dual-banners-row {
                margin-top: 5px !important;
                margin-bottom: 10px !important;
            }

            .gobike-banner-col {
                margin-bottom: 10px !important;
            }
        }

        /* --- 11.2. Cụm 2 Nút Mua Hàng & Giỏ Hàng Ngang Hàng (50% - 50%) --- */
        .product-info .woocommerce-variation-add-to-cart,
        .product-info form.cart {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            gap: 10px !important;
            width: 100% !important;
            align-items: center !important;
            margin-top: 15px !important;
            margin-bottom: 15px !important;
        }

        .product-info .woocommerce-variation-add-to-cart>button.single_add_to_cart_button,
        .product-info .woocommerce-variation-add-to-cart>button.buy_now_button,
        .product-info form.cart>button.single_add_to_cart_button,
        .product-info form.cart>button.buy_now_button {
            flex: 1 1 50% !important;
            width: calc(50% - 5px) !important;
            min-width: 0 !important;
            max-width: 50% !important;
            height: 48px !important;
            min-height: 48px !important;
            border-radius: 8px !important;
            margin: 0 !important;
            padding: 0 10px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            text-transform: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
            white-space: nowrap !important;
            box-sizing: border-box !important;
            cursor: pointer !important;
            background-color: var(--primary-color, #149D29) !important;
            border: 1px solid var(--primary-color, #149D29) !important;
            color: #fff !important;
        }

        .product-info button.single_add_to_cart_button:hover,
        .product-info button.buy_now_button:hover {
            opacity: 0.9 !important;
            box-shadow: none !important;
        }

        /* --- 11.3. Sidebar Phải: Bảng Thông Số Kỹ Thuật & Bạn Có Thể Thích --- */
        .product-footer .product-footer-right {
            box-shadow: none !important;
            -webkit-box-shadow: none !important;
            border-radius: 0;
            padding: 0;
            margin-bottom: 25px;
            background: transparent;
        }

        .product-footer-right h3.spec-title {
            font-size: 18px;
            font-weight: 700;
            color: #1c1c1c;
            margin-top: 0;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
            text-transform: none;
        }

        .product-footer-right .spec-table-wrapper {
            width: 100%;
        }

        .product-footer-right .spec-table-wrapper table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
            border-radius: 0;
            margin-bottom: 10px;
            overflow: visible;
        }

        .product-footer-right .spec-table-wrapper table tr {
            display: none;
            border-bottom: 1px solid #ddd;
        }

        .product-footer-right .spec-table-wrapper table tr:nth-child(-n+7),
        .product-footer-right .spec-table-wrapper.expanded table tr {
            display: table-row;
        }

        .product-footer-right .spec-table-wrapper table td {
            padding: 8px 10px;
            font-size: 13px;
            line-height: 1.45;
            border: 1px solid #e5e5e5;
            vertical-align: middle;
        }

        .product-footer-right .spec-table-wrapper table tr td:first-child {
            width: 35%;
            font-weight: 700;
            color: #111;
            background-color: #f9f9f9;
            padding-left: 10px;
        }

        .product-footer-right .spec-table-wrapper table tr td:last-child {
            width: 65%;
            color: #333;
            background-color: #fff;
        }

        .product-footer-right .btn-more-specific,
        .product-footer-right #more-specific {
            width: 100%;
            display: block;
            border: 1px solid #288ad6;
            color: #288ad6;
            background: #fff;
            text-align: center;
            padding: 8px 12px;
            margin: 12px 0 20px 0;
            border-radius: 4px;
            font-size: 13.5px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .product-footer-right .btn-more-specific:hover,
        .product-footer-right #more-specific:hover {
            border-color: #1a6cb3;
            color: #1a6cb3;
            background-color: #f0f7fd;
        }

        .product-sidebar-related {
            margin-top: 25px;
            margin-bottom: 25px;
        }

        .product-sidebar-related .sidebar-block-title {
            font-size: 16px;
            font-weight: 700;
            color: #1c1c1c;
            margin-top: 0;
            margin-bottom: 15px;
            position: relative;
            border-bottom: 1px solid #eee;
            padding-bottom: 8px;
            text-transform: none;
        }

        .product-sidebar-related .sidebar-block-title span {
            position: relative;
            display: inline-block;
        }

        .product-sidebar-related .sidebar-block-title span::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -9px;
            width: 100%;
            height: 2.5px;
            background-color: #d70018;
        }

        .product-sidebar-related ul.product_list_widget {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .product-sidebar-related ul.product_list_widget li {
            padding: 10px 0 10px 75px;
            border-bottom: 1px solid #f2f2f2;
            border-top: none;
            min-height: 75px;
            position: relative;
        }

        .product-sidebar-related ul.product_list_widget li:last-child {
            border-bottom: none;
        }

        .product-sidebar-related ul.product_list_widget li img {
            width: 62px;
            height: 62px;
            border-radius: 6px;
            border: 1px solid #eee;
            padding: 2px;
            object-fit: cover;
            position: absolute;
            left: 0;
            top: 10px;
        }

        .product-sidebar-related ul.product_list_widget li .product-title {
            font-size: 13.5px;
            font-weight: 600;
            color: #333;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.35;
            margin-bottom: 5px;
            transition: color 0.2s ease;
        }

        .product-sidebar-related ul.product_list_widget li a:hover .product-title {
            color: #d70018;
        }

        .product-sidebar-related ul.product_list_widget li .price {
            font-size: 13.5px;
            line-height: 1.2;
        }

        .product-sidebar-related ul.product_list_widget li .amount {
            color: #d70018;
            font-size: 14px;
            font-weight: 700;
        }

        .product-sidebar-related ul.product_list_widget li del {
            margin-left: 5px;
        }

        .product-sidebar-related ul.product_list_widget li del .amount {
            color: #999;
            font-size: 12px;
            font-weight: 400;
        }

        /* --- 11.4. Khối Mô Tả Sản Phẩm & Nút Xem Thêm Nội Dung --- */
        .product-page-sections {
            margin-bottom: 25px;
        }

        .product-page-sections,
        .product-page-sections .product-section,
        .product-page-sections .product-section .entry-content {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            -webkit-box-shadow: none !important;
        }

        .product-page-sections .product-section-header {
            width: 100%;
            border-bottom: 1px solid #e5e5e5;
            margin-bottom: 20px;
            padding-bottom: 0;
        }

        .product-page-sections .product-section-title {
            font-size: 15px;
            font-weight: 700;
            color: #1c1c1c;
            text-transform: uppercase;
            display: inline-block;
            padding: 0 4px 8px 4px;
            margin-bottom: -1px;
            border-bottom: 2px solid #111;
            letter-spacing: 0.5px;
        }

        .product-section {
            border-top: 0;
            background: transparent;
            position: relative;
        }

        .product-page-sections .product-section {
            padding: 0;
            max-height: 650px;
            height: auto;
            overflow: hidden;
            margin-top: 0;
            margin-bottom: 15px;
            position: relative;
            transition: max-height 0.4s ease;
        }

        .product-page-sections .product-section.active {
            max-height: none !important;
            height: auto !important;
            overflow: visible;
        }

        .product-section .entry-content {
            font-size: 15px;
            line-height: 1.7;
            color: #2b2b2b;
        }

        .product-section .entry-content p {
            margin-bottom: 14px;
            font-size: 15px;
            line-height: 1.7;
            color: #2b2b2b;
        }

        .product-section .entry-content h2 {
            font-size: 18px;
            font-weight: 700;
            color: #111;
            margin-top: 22px;
            margin-bottom: 12px;
            line-height: 1.4;
            text-transform: none;
        }

        .product-section .entry-content h3 {
            font-size: 16px;
            font-weight: 700;
            color: #111;
            margin-top: 18px;
            margin-bottom: 10px;
            text-transform: none;
        }

        .product-section .entry-content i,
        .product-section .entry-content em {
            color: #666;
            font-size: 13.5px;
            line-height: 1.5;
        }

        .product-section .entry-content img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 15px auto;
            border-radius: 4px;
        }

        .product-footer-showmore {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            padding-top: 60px;
            padding-bottom: 5px;
            text-align: center;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.85) 45%, #fff 100%);
            display: block;
            margin-bottom: 0;
            z-index: 5;
        }

        .button_readmore {
            width: 100%;
            max-width: 335px;
            height: 34px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            margin: 0 auto 15px auto;
            border: 1px solid #d5d5d5;
            background: #ffffff;
            font-size: 13.5px;
            font-weight: 500;
            color: #212529;
            border-radius: 25px;
            text-decoration: none !important;
            cursor: pointer;
            box-shadow: none !important;
            transition: all 0.25s ease;
        }

        .button_readmore:hover {
            border-color: #888;
            color: #000;
            box-shadow: none !important;
        }

        .button_readmore i {
            margin-left: 8px;
        }

        /* --- 11.5. Khối Hỗ Trợ Khách Hàng / Hotline Tư Vấn --- */
        .col-support-single {
            padding-left: 15px;
            padding-right: 15px;
        }

        .gobike-single-support-card {
            background: #ffffff;
            border: 1px solid #eeeeee;
            border-radius: 4px;
            padding: 24px 18px 20px 18px;
            text-align: center;
            box-shadow: none !important;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        .gobike-single-support-card .single-support-title {
            font-size: 15.5px;
            font-weight: 700;
            color: #323c3f;
            text-transform: uppercase;
            line-height: 1.45;
            margin: 0 0 16px 0;
            padding: 0;
            letter-spacing: 0.2px;
            text-align: center;
        }

        .gobike-single-support-card .single-support-image {
            width: 100%;
            margin: 0 0 16px 0;
            overflow: hidden;
            border-radius: 2px;
        }

        .gobike-single-support-card .single-support-image img {
            width: 100%;
            height: auto;
            display: block;
            object-fit: cover;
            margin: 0 auto;
            border-radius: 2px;
        }

        .gobike-single-support-card .single-support-call-text {
            font-size: 14px;
            color: #333333;
            margin: 0 0 8px 0;
            line-height: 1.4;
            font-weight: 400;
        }

        .gobike-single-support-card .single-support-phone-wrap {
            margin: 0 0 12px 0;
            line-height: 1;
        }

        .gobike-single-support-card .single-support-phone {
            font-size: 32px;
            font-weight: 700;
            color: #fe701a !important;
            line-height: 1.1;
            text-decoration: none !important;
            display: inline-block;
            letter-spacing: 0.5px;
            transition: all 0.2s ease;
        }

        .gobike-single-support-card .single-support-phone:hover {
            color: #e05b0a !important;
            transform: scale(1.03);
        }

        .gobike-single-support-card .single-support-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 14px 0 12px 0;
            color: #9ca3af;
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .gobike-single-support-card .single-support-divider::before,
        .gobike-single-support-card .single-support-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #eeeeee;
        }

        .gobike-single-support-card .single-support-divider span {
            padding: 0 12px;
            color: #94a3b8;
            font-size: 12px;
        }

        .gobike-single-support-card .single-support-chat-text {
            font-size: 15px;
            font-weight: 600;
            color: #111827;
            margin: 0 0 12px 0;
            line-height: 1.4;
        }

        .gobike-single-support-card .single-support-btn-wrap {
            margin-top: 5px;
        }

        .gobike-single-support-card .single-support-chat-btn {
            display: block;
            width: 100%;
            border: 1px solid #fe701a;
            background: #ffffff;
            color: #fe701a !important;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            padding: 10px 14px;
            border-radius: 3px;
            text-align: center;
            text-decoration: none !important;
            transition: all 0.25s ease;
            box-sizing: border-box;
            letter-spacing: 0.3px;
        }

        .gobike-single-support-card .single-support-chat-btn:hover {
            background: #fe701a;
            color: #ffffff !important;
            box-shadow: none !important;
            transform: translateY(-1px);
        }

        @media screen and (max-width: 849px) {
            .gobike-single-support-card {
                max-width: 360px;
                margin: 20px auto;
            }
        }

        /* --- 11.6. Slider Sản Phẩm Tương Tự / Cùng Loại (5 Cột) --- */
        .gobike-related-wrapper {
            width: 100%;
            position: relative;
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .gobike-related-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-bottom: 1px solid #e5e5e5;
            margin-bottom: 22px;
            padding-bottom: 0;
            position: relative;
        }

        .gobike-related-title-box {
            display: flex;
            gap: 20px;
            align-items: flex-end;
        }

        .gobike-related-title,
        .gobike-related-tab-btn {
            font-size: 16.5px;
            font-weight: 700;
            color: #222;
            text-transform: uppercase;
            display: inline-block;
            padding: 0 4px 10px 4px;
            margin-bottom: -1px;
            letter-spacing: 0.5px;
            background: transparent;
            border: none;
            outline: none;
            cursor: default;
            position: relative;
        }

        .gobike-related-title,
        .gobike-related-tab-btn.active {
            border-bottom: 2px solid #d21b1b;
            color: #111;
        }

        .gobike-related-tab-btn {
            cursor: pointer;
        }

        .gobike-related-tab-btn:not(.active) {
            color: #777;
            border-bottom: 2px solid transparent;
        }

        .gobike-related-tab-btn:not(.active):hover {
            color: #d21b1b;
        }

        .gobike-related-nav {
            display: flex;
            gap: 6px;
            align-items: center;
            margin-bottom: 8px;
        }

        .gobike-nav-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            color: #4a5568;
            cursor: pointer;
            padding: 0;
            transition: all 0.2s ease;
            outline: none;
        }

        .gobike-nav-btn:hover {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #111;
            box-shadow: none !important;
        }

        .gobike-nav-btn:active {
            transform: scale(0.95);
        }

        .gobike-nav-btn svg {
            width: 12px;
            height: 12px;
            display: block;
        }

        .gobike-related-wrapper,
        .gobike-related-wrapper .gobike-swiper-box,
        .gobike-related-wrapper .swiper-container,
        .gobike-related-wrapper .swiper-wrapper {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            -webkit-box-shadow: none !important;
        }

        .gobike-related-wrapper .swiper-container {
            width: 100% !important;
            height: auto !important;
            position: relative !important;
            overflow: hidden !important;
            padding: 10px 4px 18px 4px !important;
            margin: 0 -4px !important;
        }

        .gobike-related-wrapper .swiper-wrapper {
            display: flex !important;
            align-items: stretch !important;
            box-sizing: border-box !important;
        }

        .gobike-related-wrapper .swiper-slide {
            height: auto !important;
            display: flex !important;
            flex-direction: column !important;
            background: transparent !important;
            box-sizing: border-box !important;
            padding: 0 !important;
            font-size: 14px !important;
        }

        .gobike-related-wrapper .swiper-slide .product-small.col {
            width: 100% !important;
            max-width: 100% !important;
            height: 100% !important;
            margin: 0 !important;
            padding: 10px !important;
            background: #ffffff !important;
            position: relative !important;
            border-radius: 10px !important;
            box-shadow: none !important;
            border: 1px solid #f0f0f0 !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            box-sizing: border-box !important;
            transition: all 0.3s ease !important;
        }

        .gobike-related-wrapper .swiper-slide .product-small.col:hover {
            box-shadow: none !important;
            transform: translateY(-2px) scale(1.01) !important;
            z-index: 2 !important;
        }

        .gobike-related-wrapper .product-small .col-inner,
        .gobike-related-wrapper .product-small .product-small.box {
            height: 100% !important;
            width: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .gobike-related-wrapper .product-small .badge-container {
            position: absolute !important;
            top: 8px !important;
            left: 8px !important;
            margin: 0 !important;
            z-index: 5 !important;
            display: block !important;
        }

        .gobike-related-wrapper .product-small .badge {
            margin: 0 !important;
            width: auto !important;
            height: auto !important;
            display: inline-block !important;
        }

        .gobike-related-wrapper .product-small .badge-inner {
            background: #f00 !important;
            color: #fff !important;
            font-weight: 700 !important;
            font-size: 11.5px !important;
            line-height: 1.2 !important;
            padding: 2px 7px !important;
            border-radius: 4px !important;
            display: inline-block !important;
            min-height: auto !important;
            min-width: auto !important;
            box-shadow: none !important;
        }

        .gobike-related-wrapper .product-small .badge-inner:after {
            display: none !important;
        }

        .gobike-related-wrapper .product-small .box-image {
            padding: 10px 4px !important;
            height: 150px !important;
            max-height: 150px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            position: relative !important;
            overflow: hidden !important;
            background: transparent !important;
            margin-bottom: 5px !important;
        }

        .gobike-related-wrapper .product-small .box-image a,
        .gobike-related-wrapper .product-small .box-image .image-none {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
            height: 100% !important;
        }

        .gobike-related-wrapper .product-small .box-image img,
        .gobike-related-wrapper .swiper-slide img {
            max-height: 135px !important;
            width: auto !important;
            max-width: 100% !important;
            margin: 0 auto !important;
            object-fit: contain !important;
            display: block !important;
            position: static !important;
            transition: transform 0.3s ease !important;
        }

        .gobike-related-wrapper .product-small:hover .box-image img {
            transform: scale(1.04) !important;
        }

        .gobike-related-wrapper .product-small .image-tools {
            display: none !important;
        }

        .gobike-related-wrapper .product-small .box-text {
            text-align: left !important;
            padding: 0 !important;
            flex: 1 1 auto !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
        }

        .gobike-related-wrapper .product-small .title-wrapper {
            margin-bottom: 6px !important;
        }

        .gobike-related-wrapper .product-small .title-wrapper a {
            color: #111 !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            line-height: 1.4 !important;
            text-align: left !important;
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
            text-decoration: none !important;
        }

        .gobike-related-wrapper .product-small .title-wrapper a:hover {
            color: var(--primary-color, #149D29) !important;
        }

        .gobike-related-wrapper .product-small .price-wrapper {
            display: block !important;
            text-align: left !important;
            margin: 4px 0 0 0 !important;
        }

        .gobike-related-wrapper .product-small .price {
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: baseline !important;
            gap: 6px !important;
            margin: 0 0 6px 0 !important;
            text-align: left !important;
            line-height: 1.3 !important;
        }

        .gobike-related-wrapper .product-small .price ins {
            text-decoration: none !important;
        }

        .gobike-related-wrapper .product-small .price ins span.amount,
        .gobike-related-wrapper .product-small .price>span.amount {
            color: var(--primary-color, #149D29) !important;
            font-weight: 700 !important;
            font-size: 15px !important;
        }

        .gobike-related-wrapper .product-small .price del {
            text-decoration: none !important;
        }

        .gobike-related-wrapper .product-small .price del span.amount {
            color: #888888 !important;
            font-size: 12px !important;
            text-decoration: line-through !important;
        }

        .gobike-related-wrapper .product-small .price del>span:not(.amount) {
            display: none !important;
        }

        .gobike-related-wrapper .product-small .promotion {
            background: #f1f2f4 !important;
            border-radius: 4px !important;
            padding: 6px 8px !important;
            margin-top: 4px !important;
            margin-bottom: 2px !important;
            font-size: 11.5px !important;
            color: #333333 !important;
            line-height: 1.4 !important;
            text-align: left !important;
            display: block !important;
        }

        .gobike-related-wrapper .product-small .text-count-review {
            font-size: 11px !important;
            color: #888 !important;
            display: block !important;
            margin-top: 4px !important;
            text-align: left !important;
        }

        /* --- 11.7. Khối SEO Cuối Trang Sản Phẩm (Viền Gạch Đứt) --- */
        .gobike-shop-bottom-seo-row {
            margin-top: 30px !important;
            margin-bottom: 30px !important;
            padding-top: 20px !important;
            width: 100% !important;
        }

        .gobike-shop-seo-box {
            width: 100% !important;
            background: transparent !important;
        }

        .gobike-shop-seo-box .gobike-seo-title {
            font-size: 16.5px !important;
            font-weight: 700 !important;
            color: #222 !important;
            margin-top: 0 !important;
            margin-bottom: 16px !important;
            padding-bottom: 10px !important;
            border-bottom: 1px dashed #ccc !important;
            line-height: 1.4 !important;
            text-transform: none !important;
        }

        .gobike-shop-seo-box .gobike-seo-content {
            font-size: 14px !important;
            line-height: 1.85 !important;
            color: #333 !important;
        }

        .gobike-shop-seo-box .gobike-seo-content p {
            margin-bottom: 6px !important;
            line-height: 1.85 !important;
            color: #333 !important;
        }

        .gobike-shop-seo-box .gobike-seo-content p:last-child {
            margin-bottom: 0 !important;
        }

        /* --- 11.8. Khối Flash Sale Plugin MH (.hbfs-slider-wrap) --- */
        .hbfs-slider-wrap {
            max-width: 100% !important;
            margin: 0 auto 30px auto !important;
            border-radius: 10px !important;
            overflow: hidden !important;
        }

        .hbfs-slider-banner {
            line-height: 0 !important;
        }

        .hbfs-slider-banner img {
            width: 100% !important;
            height: auto !important;
            display: block !important;
            border-radius: 10px 10px 0 0 !important;
        }

        .hbfs-slider-box-frame {
            border-radius: 0 0 10px 10px !important;
            padding: 10px !important;
        }

        .hbfs-products-row {
            border-radius: 0 0 6px 6px !important;
        }

        .hbfs-products-row.splide .splide__arrow:disabled {
            display: none !important;
        }

        .hbfs-products-row.splide .splide__track {
            overflow: hidden !important;
        }

        .hbfs-products-row.splide .splide__list {
            display: flex !important;
            align-items: stretch !important;
        }

        /* ==========================================================================
                                                       KHỐI TIN TỨC TRANG CHỦ (TABS PILL STYLE & TIN TỨC 2 CỘT GOBIKE)
                                                       ========================================================================== */
        #row-1141733086,
        .pv-row-custom {
            background: #ffffff;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 30px;
            box-shadow: none !important;
        }

        #row-1141733086 h2,
        .pv-row-custom h2 {
            font-size: 20px !important;
            font-weight: 700 !important;
            color: #111827 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            margin-bottom: 16px !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        /* Thanh chỉ thị xanh lá trước tiêu đề TIN TỨC */
        #row-1141733086 h2::before,
        .pv-row-custom h2::before {
            content: "";
            display: inline-block;
            width: 4px;
            height: 18px;
            background: #149d29;
            border-radius: 2px;
        }

        /* Reset thanh Tab Flatsome sang dạng viên thuốc bo tròn (Pills) */
        #row-1141733086 .tabbed-content>ul.nav,
        .pv-row-custom .tabbed-content>ul.nav {
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            gap: 10px !important;
            border-bottom: none !important;
            margin-bottom: 22px !important;
            padding-bottom: 0 !important;
        }

        #row-1141733086 .tabbed-content>ul.nav>li.tab,
        .pv-row-custom .tabbed-content>ul.nav>li.tab {
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            background: transparent !important;
        }

        /* Các nút Tab chưa chọn (Inactive Pills) */
        #row-1141733086 .tabbed-content>ul.nav>li.tab>a,
        .pv-row-custom .tabbed-content>ul.nav>li.tab>a {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 7px 22px !important;
            border-radius: 999px !important;
            border: 1px solid #d1d5db !important;
            background-color: #ffffff !important;
            color: #374151 !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            text-transform: none !important;
            letter-spacing: normal !important;
            line-height: 1.4 !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: none !important;
        }

        /* Xóa viền gạch chân hoặc tam giác mặc định của Flatsome */
        #row-1141733086 .tabbed-content>ul.nav>li.tab>a::before,
        #row-1141733086 .tabbed-content>ul.nav>li.tab>a::after,
        .pv-row-custom .tabbed-content>ul.nav>li.tab>a::before,
        .pv-row-custom .tabbed-content>ul.nav>li.tab>a::after {
            display: none !important;
            content: none !important;
        }

        /* Hover vào tab */
        #row-1141733086 .tabbed-content>ul.nav>li.tab:not(.active)>a:hover,
        .pv-row-custom .tabbed-content>ul.nav>li.tab:not(.active)>a:hover {
            border-color: #149d29 !important;
            color: #149d29 !important;
            background-color: #f0fdf4 !important;
        }

        /* Nút Tab đang chọn (Active Pill - Xanh lá đặc trưng GoBike theo mẫu ảnh) */
        #row-1141733086 .tabbed-content>ul.nav>li.tab.active>a,
        #row-1141733086 .tabbed-content>ul.nav>li.tab>a[aria-selected="true"],
        .pv-row-custom .tabbed-content>ul.nav>li.tab.active>a,
        .pv-row-custom .tabbed-content>ul.nav>li.tab>a[aria-selected="true"] {
            background: #149d29 !important;
            background-color: #149d29 !important;
            color: #ffffff !important;
            border-color: #149d29 !important;
            font-weight: 600 !important;
            box-shadow: none !important;
        }

        /* Bài viết nổi bật bên trái */
        #row-1141733086 .medium-6:first-child .box-blog-post,
        .pv-row-custom .medium-6:first-child .box-blog-post {
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        #row-1141733086 .medium-6:first-child .box-image,
        .pv-row-custom .medium-6:first-child .box-image {
            border-radius: 10px;
            overflow: hidden;
        }

        #row-1141733086 .medium-6:first-child .box-image .image-cover,
        .pv-row-custom .medium-6:first-child .box-image .image-cover {
            padding-top: 66% !important;
        }

        #row-1141733086 .medium-6:first-child .box-image img,
        .pv-row-custom .medium-6:first-child .box-image img {
            transition: transform 0.4s ease !important;
            object-fit: cover !important;
        }

        #row-1141733086 .medium-6:first-child .box-blog-post:hover .box-image img,
        .pv-row-custom .medium-6:first-child .box-blog-post:hover .box-image img {
            transform: scale(1.04) !important;
        }

        #row-1141733086 .medium-6:first-child .post-title,
        .pv-row-custom .medium-6:first-child .post-title {
            font-size: 17px !important;
            font-weight: 700 !important;
            line-height: 1.4 !important;
            margin-top: 12px !important;
            margin-bottom: 6px !important;
        }

        #row-1141733086 .medium-6:first-child .post-title a,
        .pv-row-custom .medium-6:first-child .post-title a {
            color: #1e293b !important;
            transition: color 0.2s ease !important;
        }

        #row-1141733086 .medium-6:first-child .post-title a:hover,
        .pv-row-custom .medium-6:first-child .post-title a:hover {
            color: #149d29 !important;
        }

        /* Cột bài viết phụ bên phải */
        #row-1141733086 .medium-6:last-child .row-masonry,
        .pv-row-custom .medium-6:last-child .row-masonry {
            display: flex !important;
            flex-direction: column !important;
            gap: 14px !important;
            height: auto !important;
        }

        #row-1141733086 .medium-6:last-child .post-item,
        .pv-row-custom .medium-6:last-child .post-item {
            position: static !important;
            width: 100% !important;
            transform: none !important;
            left: auto !important;
            top: auto !important;
            padding-bottom: 12px !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }

        #row-1141733086 .medium-6:last-child .post-item:last-child,
        .pv-row-custom .medium-6:last-child .post-item:last-child {
            border-bottom: none !important;
            padding-bottom: 0 !important;
        }

        #row-1141733086 .medium-6:last-child .box-vertical,
        .pv-row-custom .medium-6:last-child .box-vertical {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 14px !important;
            margin: 0 !important;
        }

        #row-1141733086 .medium-6:last-child .box-image,
        .pv-row-custom .medium-6:last-child .box-image {
            width: 120px !important;
            min-width: 120px !important;
            max-width: 120px !important;
            border-radius: 8px !important;
            overflow: hidden !important;
            flex-shrink: 0 !important;
        }

        #row-1141733086 .medium-6:last-child .box-image .image-cover,
        .pv-row-custom .medium-6:last-child .box-image .image-cover {
            padding-top: 65% !important;
        }

        #row-1141733086 .medium-6:last-child .box-image img,
        .pv-row-custom .medium-6:last-child .box-image img {
            object-fit: cover !important;
            transition: transform 0.3s ease !important;
        }

        #row-1141733086 .medium-6:last-child .box-vertical:hover .box-image img,
        .pv-row-custom .medium-6:last-child .box-vertical:hover .box-image img {
            transform: scale(1.08) !important;
        }

        #row-1141733086 .medium-6:last-child .box-text,
        .pv-row-custom .medium-6:last-child .box-text {
            flex: 1 !important;
            padding: 0 !important;
        }

        #row-1141733086 .medium-6:last-child .post-title,
        .pv-row-custom .medium-6:last-child .post-title {
            font-size: 14px !important;
            font-weight: 600 !important;
            line-height: 1.4 !important;
            margin-bottom: 4px !important;
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
        }

        #row-1141733086 .medium-6:last-child .post-title a,
        .pv-row-custom .medium-6:last-child .post-title a {
            color: #1e293b !important;
            transition: color 0.2s ease !important;
        }

        #row-1141733086 .medium-6:last-child .post-title a:hover,
        .pv-row-custom .medium-6:last-child .post-title a:hover {
            color: #149d29 !important;
        }

        #row-1141733086 .post-meta,
        .pv-row-custom .post-meta {
            font-size: 12px !important;
            color: #94a3b8 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            margin-top: 4px !important;
        }

        #row-1141733086 .post-meta::before,
        .pv-row-custom .post-meta::before {
            content: "";
            display: inline-block;
            width: 12px;
            height: 12px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'%3E%3C/path%3E%3C/svg%3E") no-repeat center center / contain;
            flex-shrink: 0;
        }

        #row-1141733086 .is-divider,
        .pv-row-custom .is-divider {
            display: none !important;
        }

        #row-1141733086 .overlay-icon,
        .pv-row-custom .overlay-icon {
            width: 30px !important;
            height: 30px !important;
            background: rgba(0, 0, 0, 0.55) !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        #row-1141733086 .overlay-icon i,
        .pv-row-custom .overlay-icon i {
            font-size: 12px !important;
            color: #ffffff !important;
            margin-left: 2px;
        }

        /* ==========================================================================
                                                       GOBIKE - THẺ SẢN PHẨM V2 (CHUẨN ẢNH 2: 3 THÔNG SỐ, BADGE ĐỎ & 2 NÚT BẤM)
                                                       ========================================================================== */
        .gobike-catalog-card-col {
            margin-bottom: 20px !important;
        }

        .gobike-product-card-v2 {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            position: relative;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: all 0.25s ease-in-out;
            overflow: hidden;
        }

        .gobike-product-card-v2:hover {
            border-color: #cbd5e1;
            box-shadow: none !important;
            transform: translateY(-3px);
        }

        .gobike-product-card-v2 .card-badge-wrap {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 5;
        }

        .gobike-product-card-v2 .badge-hot {
            background: #ef4444;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            display: inline-block;
            letter-spacing: 0.2px;
            box-shadow: none !important;
        }

        .gobike-product-card-v2 .card-image-wrap {
            padding: 20px 15px 10px;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
        }

        .gobike-product-card-v2 .card-image-wrap a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }

        .gobike-product-card-v2 .bike-thumb-img {
            max-height: 100%;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            transition: transform 0.35s ease;
        }

        .gobike-product-card-v2:hover .bike-thumb-img {
            transform: scale(1.06);
        }

        .gobike-product-card-v2 .card-info-wrap {
            padding: 10px 14px 16px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .gobike-product-card-v2 .card-product-title {
            font-size: 14px;
            font-weight: 700;
            line-height: 1.35;
            margin: 0 0 4px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .gobike-product-card-v2 .card-product-title a {
            color: #1e293b;
            transition: color 0.2s ease;
        }

        .gobike-product-card-v2 .card-product-title a:hover {
            color: #149d29;
        }

        .gobike-product-card-v2 .card-product-subtitle {
            font-size: 12px;
            color: #64748b;
            line-height: 1.35;
            margin: 0 0 10px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .gobike-product-card-v2 .card-price-row {
            display: flex;
            align-items: baseline;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 12px;
        }

        .gobike-product-card-v2 .price-current {
            color: #dc2626;
            font-size: 16px;
            font-weight: 700;
        }

        .gobike-product-card-v2 .price-old {
            color: #94a3b8;
            font-size: 12px;
            text-decoration: line-through;
        }

        .gobike-product-card-v2 .discount-pill {
            background: #fee2e2;
            color: #dc2626;
            font-size: 11px;
            font-weight: 700;
            padding: 1px 6px;
            border-radius: 4px;
        }

        .gobike-product-card-v2 .card-specs-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            border-top: 1px dashed #e2e8f0;
            border-bottom: 1px dashed #e2e8f0;
            margin-bottom: 14px;
        }

        .gobike-product-card-v2 .spec-col {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .gobike-product-card-v2 .spec-icon {
            color: #149d29;
            flex-shrink: 0;
        }

        .gobike-product-card-v2 .spec-value {
            font-size: 12px;
            font-weight: 600;
            color: #334155;
            white-space: nowrap;
        }

        .gobike-product-card-v2 .card-actions-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: auto;
        }

        .gobike-product-card-v2 .btn-detail-action {
            flex: 1;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #149d29;
            color: #ffffff !important;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .gobike-product-card-v2 .btn-detail-action:hover {
            background: #149d29;
            color: #ffffff !important;
            box-shadow: none !important;
        }

        .gobike-product-card-v2 .btn-cart-action {
            width: 38px;
            height: 38px;
            min-width: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1.5px solid #149d29;
            background: #ffffff;
            color: #149d29 !important;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .gobike-product-card-v2 .btn-cart-action:hover {
            background: #149d29;
            color: #ffffff !important;
        }

        .gobike-product-card-v2 .btn-cart-action:hover svg {
            stroke: #ffffff;
        }

        /* ==========================================================================
                                                       BỐ CỤC 2 CỘT TRANG SẢN PHẨM & BỘ LỌC SIDEBAR (CHUẨN ẢNH 4)
                                                       ========================================================================== */
        .gobike-shop-page-container {
            padding-top: 15px;
            padding-bottom: 40px;
        }

        .gobike-shop-hero-banner {
            background: linear-gradient(135deg, #f0fdf4 0%, #e6f7ec 100%);
            border: 1px solid #dcfce7;
            border-radius: 14px;
            padding: 24px 30px;
            margin-bottom: 20px;
        }

        .gobike-shop-hero-banner .hero-banner-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .gobike-shop-hero-banner .hero-text-col {
            flex: 1;
            max-width: 600px;
        }

        .gobike-shop-hero-banner .hero-title {
            font-size: 24px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin-bottom: 6px !important;
            text-transform: uppercase;
        }

        .gobike-shop-hero-banner .hero-slogan {
            font-size: 14px;
            font-weight: 600;
            color: #149d29;
            margin-bottom: 10px;
        }

        .gobike-shop-hero-banner .hero-intro {
            font-size: 13px;
            color: #475569;
            line-height: 1.5;
            margin-bottom: 18px;
        }

        .gobike-shop-hero-banner .hero-buttons {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .gobike-shop-hero-banner .hero-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #149d29;
            color: #ffffff !important;
            padding: 9px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s ease;
        }

        .gobike-shop-hero-banner .hero-btn-primary:hover {
            background: #149d29;
        }

        .gobike-shop-hero-banner .hero-btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ffffff;
            border: 1.5px solid #149d29;
            color: #149d29 !important;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .gobike-shop-hero-banner .hero-btn-outline:hover {
            background: #149d29;
            color: #ffffff !important;
        }

        .gobike-shop-hero-banner .hero-image-col {
            width: 320px;
            flex-shrink: 0;
            text-align: right;
        }

        .gobike-shop-hero-banner .hero-featured-bike {
            max-height: 200px;
            width: auto;
            object-fit: contain;
            filter: none !important;
        }

        .gobike-shop-trust-badges-bar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 24px;
        }

        .gobike-shop-trust-badges-bar .trust-item {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .gobike-shop-trust-badges-bar .trust-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #f0fdf4;
            color: #149d29;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .gobike-shop-trust-badges-bar .trust-text strong {
            display: block;
            font-size: 13px;
            color: #0f172a;
            line-height: 1.3;
        }

        .gobike-shop-trust-badges-bar .trust-text span {
            display: block;
            font-size: 11px;
            color: #64748b;
            line-height: 1.3;
        }

        .gobike-sidebar-filter-wrapper {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px 16px;
            margin-bottom: 30px;
        }

        .gobike-sidebar-filter-wrapper .gobike-filter-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 12px;
            border-bottom: 1.5px solid #f1f5f9;
            margin-bottom: 14px;
        }

        .gobike-sidebar-filter-wrapper .filter-title-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #149d29;
        }

        .gobike-sidebar-filter-wrapper .filter-title {
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin: 0 !important;
            letter-spacing: 0.3px;
        }

        .gobike-sidebar-filter-wrapper .filter-reset-link {
            font-size: 12px;
            color: #64748b;
            text-decoration: underline;
        }

        .gobike-sidebar-filter-wrapper .filter-reset-link:hover {
            color: #dc2626;
        }

        .gobike-active-filters-box {
            background: #f8fafc;
            border-radius: 8px;
            padding: 10px 12px;
            margin-bottom: 16px;
        }

        .gobike-active-filters-box .active-title {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            display: block;
            margin-bottom: 6px;
        }

        .gobike-active-filters-box .active-tags-list {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
        }

        .gobike-active-filters-box .active-tag-item {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 3px 8px;
            font-size: 11px;
            color: #1e293b;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .gobike-active-filters-box .remove-tag {
            color: #94a3b8;
            font-weight: 700;
            text-decoration: none;
            font-size: 13px;
        }

        .gobike-active-filters-box .remove-tag:hover {
            color: #dc2626;
        }

        .gobike-active-filters-box .clear-all-link {
            font-size: 11px;
            color: #dc2626;
            font-weight: 600;
            margin-left: 4px;
        }

        .gobike-sidebar-filter-wrapper .filter-group {
            border-bottom: 1px solid #f1f5f9;
            padding: 12px 0;
        }

        .gobike-sidebar-filter-wrapper .filter-group-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            user-select: none;
        }

        .gobike-sidebar-filter-wrapper .group-title {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .gobike-sidebar-filter-wrapper .toggle-icon {
            font-size: 14px;
            color: #64748b;
            transition: transform 0.2s ease;
        }

        .gobike-sidebar-filter-wrapper .filter-group:not(.open) .toggle-icon {
            transform: rotate(-90deg);
        }

        .gobike-sidebar-filter-wrapper .filter-group-content {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .gobike-sidebar-filter-wrapper .filter-checkbox-item {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 13px;
            color: #334155;
            margin: 0;
        }

        .gobike-sidebar-filter-wrapper .filter-checkbox-item input[type="checkbox"],
        .gobike-sidebar-filter-wrapper .filter-checkbox-item input[type="radio"] {
            accent-color: #149d29;
            width: 15px;
            height: 15px;
            margin: 0;
        }

        .gobike-sidebar-filter-wrapper .filter-checkbox-item .count {
            color: #94a3b8;
            font-size: 12px;
        }

        .gobike-sidebar-filter-wrapper .filter-select-field {
            margin-bottom: 8px;
        }

        .gobike-sidebar-filter-wrapper .filter-select-field label {
            font-size: 12px;
            color: #64748b;
            display: block;
            margin-bottom: 4px;
        }

        .gobike-sidebar-filter-wrapper .filter-select-field select {
            width: 100%;
            height: 34px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 12px;
            padding: 0 8px;
            color: #1e293b;
            background: #f8fafc;
        }

        .gobike-sidebar-filter-wrapper .gobike-apply-filter-btn {
            width: 100%;
            height: 40px;
            background: #149d29;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 14px;
            transition: background 0.2s ease;
        }

        .gobike-sidebar-filter-wrapper .gobike-apply-filter-btn:hover {
            background: #149d29;
        }

        .gobike-support-hotline-box {
            margin-top: 20px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 16px;
            text-align: center;
        }

        .gobike-support-hotline-box .support-icon {
            color: #149d29;
            margin-bottom: 6px;
        }

        .gobike-support-hotline-box .support-title {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin: 0 0 4px 0 !important;
        }

        .gobike-support-hotline-box .support-desc {
            font-size: 12px;
            color: #64748b;
            margin: 0 0 12px 0;
            line-height: 1.4;
        }

        .gobike-support-hotline-box .support-phone-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            background: #149d29;
            color: #ffffff !important;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            width: 100%;
            transition: background 0.2s;
        }

        .gobike-support-hotline-box .support-phone-btn:hover {
            background: #149d29;
        }

        .gobike-shop-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0 16px 0;
            margin-bottom: 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .gobike-shop-toolbar .toolbar-result-count {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }

        .gobike-shop-toolbar .toolbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .gobike-shop-toolbar .sort-prefix-label {
            font-size: 13px;
            color: #475569;
            font-weight: 500;
        }

        .gobike-shop-toolbar .custom-ordering-form select {
            height: 34px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 13px;
            padding: 0 10px;
            color: #1e293b;
            background: #ffffff;
        }

        @media (min-width: 850px) {
            /* .gobike-products-main-col .products.row .col {
                                                            width: 25% !important;
                                                            max-width: 25% !important;
                                                            flex: 0 0 25% !important;
                                                        } */
        }

        .gobike-shop-bottom-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .gobike-shop-bottom-cards .feature-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: all 0.25s ease;
        }

        .gobike-shop-bottom-cards .feature-card:hover {
            border-color: #cbd5e1;
            box-shadow: none !important;
            transform: translateY(-3px);
        }

        .gobike-shop-bottom-cards .feature-card h4 {
            font-size: 16px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin: 0 0 6px 0 !important;
        }

        .gobike-shop-bottom-cards .feature-card p {
            font-size: 13px;
            color: #64748b;
            margin: 0 0 14px 0;
        }

        .gobike-shop-bottom-cards .feature-card .card-link {
            font-size: 13px;
            font-weight: 600;
            color: #149d29;
            text-decoration: none;
        }

        .gobike-shop-bottom-cards .feature-card .card-link.btn-action {
            background: #149d29;
            color: #ffffff;
            padding: 6px 14px;
            border-radius: 6px;
            display: inline-block;
        }

        .gobike-why-choose-section {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 30px;
            margin-bottom: 40px;
            text-align: center;
        }

        .gobike-why-choose-section .why-title {
            font-size: 20px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin-bottom: 24px !important;
        }

        .gobike-why-choose-section .why-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            text-align: left;
        }

        .gobike-why-choose-section .why-item {
            background: #f8fafc;
            border-radius: 10px;
            padding: 18px;
        }

        .gobike-why-choose-section .why-icon {
            font-size: 24px;
            margin-bottom: 8px;
        }

        .gobike-why-choose-section .why-item h5 {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin-bottom: 4px !important;
        }

        .gobike-why-choose-section .why-item p {
            font-size: 12px;
            color: #64748b;
            line-height: 1.4;
            margin: 0;
        }

        .gobike-showrooms-section {
            background: #f8fafc;
            border-radius: 14px;
            padding: 30px;
            margin-bottom: 40px;
            text-align: center;
        }

        .gobike-showrooms-section .showroom-title {
            font-size: 20px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin-bottom: 4px !important;
        }

        .gobike-showrooms-section .showroom-subtitle {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 24px;
        }

        .gobike-showrooms-section .showroom-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            text-align: left;
        }

        .gobike-showrooms-section .showroom-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 16px;
        }

        .gobike-showrooms-section .showroom-card h5 {
            font-size: 14px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin-bottom: 6px !important;
        }

        .gobike-showrooms-section .showroom-card p {
            font-size: 12px;
            color: #475569;
            line-height: 1.4;
            margin-bottom: 8px;
        }

        .gobike-showrooms-section .showroom-card .open-time {
            font-size: 11px;
            color: #94a3b8;
            display: block;
        }

        .gobike-showrooms-section .showroom-card.card-contact {
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .gobike-showrooms-section .card-contact .hotline-label {
            font-size: 11px;
            color: #149d29;
            font-weight: 600;
            display: block;
        }

        .gobike-showrooms-section .card-contact .hotline-number {
            font-size: 18px;
            font-weight: 700;
            color: #149d29;
            text-decoration: none;
            display: block;
        }

        .gobike-showrooms-section .card-contact .hotline-time {
            font-size: 11px;
            color: #64748b;
            display: block;
            margin-bottom: 10px;
        }

        .gobike-showrooms-section .card-contact .map-link-btn {
            font-size: 12px;
            font-weight: 600;
            color: #149d29;
            text-decoration: underline;
        }

        @media (max-width: 849px) {

            #row-1141733086,
            .pv-row-custom {
                padding: 12px !important;
            }

            #row-1141733086 .tabbed-content>ul.nav,
            .pv-row-custom .tabbed-content>ul.nav {
                gap: 8px !important;
                overflow-x: auto !important;
                flex-wrap: nowrap !important;
                padding-bottom: 6px !important;
                -webkit-overflow-scrolling: touch;
            }

            #row-1141733086 .tabbed-content>ul.nav>li.tab>a,
            .pv-row-custom .tabbed-content>ul.nav>li.tab>a {
                padding: 6px 16px !important;
                font-size: 13px !important;
                white-space: nowrap !important;
            }

            #row-1141733086 .medium-6:first-child,
            .pv-row-custom .medium-6:first-child {
                margin-bottom: 16px !important;
            }

            #row-1141733086 .medium-6:last-child .box-image,
            .pv-row-custom .medium-6:last-child .box-image {
                width: 100px !important;
                min-width: 100px !important;
                max-width: 100px !important;
            }

            .gobike-shop-hero-banner .hero-banner-inner {
                flex-direction: column;
                text-align: center;
            }

            .gobike-shop-hero-banner .hero-image-col {
                width: 100%;
                text-align: center;
            }

            .gobike-shop-hero-banner .hero-buttons {
                justify-content: center;
            }

            .gobike-shop-trust-badges-bar {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .gobike-shop-layout-2col .gobike-sidebar-col,
            .gobike-shop-layout-2col .gobike-products-main-col {
                width: 100% !important;
                max-width: 100% !important;
                flex: 0 0 100% !important;
            }

            .gobike-products-main-col .products.row .col {
                width: 50% !important;
                max-width: 50% !important;
                flex: 0 0 50% !important;
            }

            .gobike-shop-bottom-cards,
            .gobike-why-choose-section .why-grid,
            .gobike-showrooms-section .showroom-grid {
                grid-template-columns: 1fr;
            }
        /* ==========================================================================
           HEADER SEARCH FORM - PHONG CÁCH GOBIKE (CHUẨN GIAO DIỆN THEO MẪU)
           ========================================================================== */
        .header-nav li.header-search-form:before,
        .header-nav li.header-search-form:after {
            display: none !important;
            content: none !important;
        }

        li.header-search-form {
            width: 100% !important;
            max-width: 540px !important;
        }

        .header-search-form-wrapper,
        .header-search-form .searchform-wrapper,
        .header .searchform-wrapper {
            width: 100% !important;
            max-width: 540px !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .header-search-form form.searchform,
        .header form.searchform {
            background: #ffffff !important;
            border: 1.5px solid #dbe2ea !important;
            border-radius: 8px !important;
            height: 42px !important;
            min-height: 42px !important;
            position: relative !important;
            display: flex !important;
            align-items: center !important;
            padding: 3px 3px 3px 14px !important;
            box-shadow: none !important;
            box-sizing: border-box !important;
            transition: border-color 0.2s ease !important;
            margin: 0 !important;
        }

        .header-search-form form.searchform:focus-within,
        .header form.searchform:focus-within {
            border-color: #149d29 !important;
            box-shadow: none !important;
        }

        .header-search-form form.searchform .flex-row,
        .header form.searchform .flex-row {
            width: 100% !important;
            height: 100% !important;
            display: flex !important;
            align-items: center !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .header-search-form form.searchform .flex-col.flex-grow,
        .header form.searchform .flex-col.flex-grow {
            flex: 1 1 auto !important;
            height: 100% !important;
            display: flex !important;
            align-items: center !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .header-search-form form.searchform input[type="search"],
        .header form.searchform input[type="search"] {
            width: 100% !important;
            height: 100% !important;
            min-height: unset !important;
            border: none !important;
            background: transparent !important;
            box-shadow: none !important;
            outline: none !important;
            padding: 0 10px 0 0 !important;
            margin: 0 !important;
            font-size: 14px !important;
            font-weight: 400 !important;
            color: #1e293b !important;
            line-height: normal !important;
            box-sizing: border-box !important;
        }

        .header-search-form form.searchform input[type="search"]::placeholder,
        .header form.searchform input[type="search"]::placeholder {
            color: #596377 !important;
            font-size: 13.5px !important;
            font-weight: 400 !important;
            opacity: 1 !important;
        }

        .header-search-form form.searchform .flex-col:not(.flex-grow),
        .header form.searchform .flex-col:not(.flex-grow) {
            padding: 0 !important;
            margin: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }

        /* NÚT TÌM KIẾM MÀU XANH BO GÓC NHƯ MẪU */
        .header-search-form form.searchform .ux-search-submit,
        .header form.searchform .ux-search-submit,
        .header form.searchform .button.submit-button,
        .searchform .button.submit-button {
            background: #0d6e2e !important;
            background-color: #0d6e2e !important;
            border: none !important;
            border-radius: 6px !important;
            width: 38px !important;
            height: 34px !important;
            min-width: 38px !important;
            min-height: 34px !important;
            max-width: 38px !important;
            max-height: 34px !important;
            padding: 0 !important;
            margin: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            color: #ffffff !important;
            box-shadow: none !important;
            transform: none !important;
            transition: background-color 0.2s ease !important;
        }

        .header-search-form form.searchform .ux-search-submit:hover,
        .header form.searchform .ux-search-submit:hover,
        .header form.searchform .button.submit-button:hover,
        .searchform .button.submit-button:hover {
            background: #149d29 !important;
            background-color: #149d29 !important;
            color: #ffffff !important;
            transform: none !important;
            box-shadow: none !important;
        }

        .header-search-form form.searchform .ux-search-submit i,
        .header-search-form form.searchform .ux-search-submit .icon-search,
        .header form.searchform .ux-search-submit i,
        .header form.searchform .ux-search-submit .icon-search,
        .searchform .button.icon i {
            color: #ffffff !important;
            font-size: 15px !important;
            display: block !important;
            line-height: 1 !important;
            margin: 0 !important;
        }

        /* KẾT QUẢ TÌM KIẾM TRỰC TIẾP (LIVE SEARCH RESULTS) */
        .header .search-form .live-search-results {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
            margin-top: 6px !important;
            overflow: hidden !important;
        }

        .live-search-results .autocomplete-suggestion {
            padding: 10px 12px !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            transition: background-color 0.15s ease !important;
        }

        .live-search-results .autocomplete-suggestion:hover {
            background-color: #f8fafc !important;
        }

        .live-search-results .autocomplete-suggestion img {
            width: 48px !important;
            height: 48px !important;
            border-radius: 6px !important;
            object-fit: contain !important;
            flex-shrink: 0 !important;
        }

        .live-search-results .search-name {
            font-size: 13.5px !important;
            font-weight: 600 !important;
            color: #0f172a !important;
            line-height: 1.3 !important;
        }

        .live-search-results .search-price ins span.amount {
            color: #dc2626 !important;
            font-size: 13.5px !important;
            font-weight: 700 !important;
        }
    </style>
    <?php
}

