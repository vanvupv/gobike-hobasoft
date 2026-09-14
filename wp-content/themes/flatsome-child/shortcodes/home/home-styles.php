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
           1. THIẾT LẬP CHUNG & TIỆN ÍCH (General & Utilities)
           ========================================================================== */
        .m-0 {
            margin: 0px !important;
        }

        b, strong {
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

        .col-divided + .col {
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
            box-shadow: unset !important;
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

        .medium-logo-center .flex-left {
            flex: none;
        }

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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
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

        .header-vertical-menu .ux-nav-vertical-menu > li > a {
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
                box-shadow: 0 -3px 12px rgba(0, 0, 0, 0.08) !important;
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

            .gobike-bottom-bar-nav .wrap-fixed-footer > li,
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

            .gobike-bottom-bar-nav .wrap-fixed-footer > li > a,
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

            .gobike-bottom-bar-nav .wrap-fixed-footer > li > a span {
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
            .gobike-bottom-bar-nav .wrap-fixed-footer > li.active a {
                color: #149D29 !important;
            }

            .gobike-bottom-bar-nav .wrap-fixed-footer > li.item-phone-fab {
                overflow: visible !important;
            }

            .gobike-bottom-bar-nav .wrap-fixed-footer > li.item-phone-fab > a.btn-phone-call {
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
                box-shadow: 0 4px 12px rgba(229, 40, 40, 0.45) !important;
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
                0% { transform: scale(0.9); opacity: 0.8; }
                50% { transform: scale(1.25); opacity: 0.15; }
                100% { transform: scale(1.4); opacity: 0; }
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
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important;
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

        .row_cat > .col {
            min-width: 150px !important;
            padding: 0 6px !important;
        }

        .row_cat > .col .col-inner {
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
            border-bottom: 2px solid #0c5436;
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
            background: #0c5436;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .gobike-category-block-wrapper .block-title {
            margin: 0;
            font-size: 19px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }

        .gobike-category-block-wrapper .block-title a {
            color: #0c5436;
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
            color: #0c5436;
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
                grid-template-columns: repeat(4, 1fr) !important;
                gap: 12px !important;
                transform: none !important;
                width: 100% !important;
            }

            .gobike-pcard-slide {
                width: auto !important;
                height: 100% !important;
                margin: 0 !important;
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
            box-sizing: border-box;
            transition: all 0.25s ease;
        }

        .gobike-pcard:hover {
            border-color: #0c5436;
            box-shadow: 0 4px 16px rgba(12, 84, 54, 0.12);
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
            box-shadow: 0 2px 4px rgba(229,16,29,0.25);
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
            background: #fff;
        }

        .gobike-pcard-thumb a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }

        .gobike-pcard-thumb img {
            max-height: 135px;
            width: auto;
            max-width: 100%;
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
            font-size: 13px;
            font-weight: 600;
            line-height: 1.35;
            margin: 0 0 6px 0;
            height: 35px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            text-align: left;
        }

        .gobike-pcard-title a {
            color: #0c5436;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .gobike-pcard-title a:hover {
            color: #149d29;
        }

        /* 3 Thông số có icon (GPS km, kg, W) */
        .gobike-pcard-specs {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 4px;
            margin-bottom: 8px;
            background: #f8fafc;
            border-radius: 4px;
            padding: 4px 6px;
            border: 1px solid #f1f5f9;
        }

        .gobike-pcard-specs .spec-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            white-space: nowrap;
        }

        .gobike-pcard-specs .spec-badge svg {
            flex-shrink: 0;
            color: #0c5436;
        }

        /* Cụm giá tiền */
        .gobike-pcard-price-box {
            display: flex;
            align-items: baseline;
            gap: 6px;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }

        .gobike-pcard-price-box .price-current,
        .gobike-pcard-price-box .price-current .woocommerce-Price-amount {
            color: #d70018 !important;
            font-size: 14.5px !important;
            font-weight: 700 !important;
            text-decoration: none !important;
        }

        .gobike-pcard-price-box .price-old,
        .gobike-pcard-price-box .price-old .woocommerce-Price-amount {
            color: #94a3b8 !important;
            font-size: 11.5px !important;
            text-decoration: line-through !important;
        }

        /* Nút Xem chi tiết */
        .gobike-pcard-btn {
            display: block;
            width: 100%;
            text-align: center;
            border: 1px solid #0c5436;
            color: #0c5436;
            background: #ffffff;
            padding: 5px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none !important;
            transition: all 0.2s ease;
            box-sizing: border-box;
            cursor: pointer;
        }

        .gobike-pcard-btn:hover {
            background: #0c5436;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(12, 84, 54, 0.2);
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
            box-shadow: 0 4px 15px rgba(11, 47, 76, 0.2);
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
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #ffffff;
            margin-bottom: 12px;
            font-style: italic;
        }

        .banner-title {
            font-size: 16px;
            font-weight: 800;
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
            background: #22c55e;
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
            color: #0c5436 !important;
            font-size: 12.5px;
            font-weight: 700;
            padding: 8px 18px;
            border-radius: 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
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
            border-top: 1px solid rgba(255,255,255,0.15);
            padding-top: 12px;
        }

        .banner-slogan {
            font-size: 10.5px;
            font-weight: 800;
            letter-spacing: 1.5px;
            color: rgba(255,255,255,0.7);
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
                background: #0c5436 !important;
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
                border-left: 1px solid rgba(255,255,255,0.15) !important;
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
                border-top: 1px solid rgba(255,255,255,0.15) !important;
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
            box-shadow: 0 4px 15px rgba(217, 4, 41, 0.15);
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
            min-height: 36px;
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
            box-shadow: 0 4px 10px rgba(20, 157, 41, 0.25);
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
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
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
            box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
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
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            transform-origin: center center !important;
            animation: gobikeZoomFromCenter 0.85s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards !important;
            transition: transform 0.3s ease, box-shadow 0.3s ease !important;
        }

        .gobike-zoom-banner:hover {
            transform: scale(1.02) !important;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15) !important;
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

        .product-info .woocommerce-variation-add-to-cart > button.single_add_to_cart_button,
        .product-info .woocommerce-variation-add-to-cart > button.buy_now_button,
        .product-info form.cart > button.single_add_to_cart_button,
        .product-info form.cart > button.buy_now_button {
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
            box-shadow: none;
            -webkit-box-shadow: none;
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
            background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,0.85) 45%, #fff 100%);
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
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            transition: all 0.25s ease;
        }

        .button_readmore:hover {
            border-color: #888;
            color: #000;
            box-shadow: 0 3px 8px rgba(0,0,0,0.12);
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
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
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
            font-weight: 800;
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
            box-shadow: 0 3px 8px rgba(254, 112, 26, 0.28);
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
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
            box-shadow: rgba(60, 64, 67, 0.1) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px !important;
            border: 1px solid #f0f0f0 !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            box-sizing: border-box !important;
            transition: all 0.3s ease !important;
        }

        .gobike-related-wrapper .swiper-slide .product-small.col:hover {
            box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.3) !important;
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
            box-shadow: 0 1px 3px rgba(0,0,0,0.15) !important;
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
            min-height: 38px !important;
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
        .gobike-related-wrapper .product-small .price > span.amount {
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

        .gobike-related-wrapper .product-small .price del > span:not(.amount) {
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
    </style>
    <?php
}
