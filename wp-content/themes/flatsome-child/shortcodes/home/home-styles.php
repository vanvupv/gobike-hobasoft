<?php
/**
 * TẬP TRUNG TOÀN BỘ CSS TRANG CHỦ GOBIKE (NẠP QUA HOOK wp_head)
 * 
 * Ưu điểm:
 * 1. Không cần quản lý version (?ver=...).
 * 2. Không bao giờ bị dính cache trình duyệt (F5 là ăn CSS mới ngay lập tức).
 * 3. Quản lý toàn bộ CSS của các section trang chủ tại 1 nơi duy nhất.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_head', 'gobike_home_custom_styles_output', 100);

function gobike_home_custom_styles_output()
{
    ?>
    <style id="gobike-home-custom-css">
        /* ==========================================================================
           1. KHỐI DANH MỤC LỚN (sc-category-block.php)
           ========================================================================== */
        .gobike-category-block-wrapper {
            max-width: 1230px;
            margin: 0 auto 30px auto;
            padding: 0 10px;
            box-sizing: border-box;
            font-family: inherit;
        }
        .gobike-block-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-bottom: 2px solid #149d29;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        .gobike-block-header .header-left {
            display: flex;
            align-items: baseline;
            gap: 15px;
            flex-wrap: wrap;
        }
        .gobike-block-header .block-title {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .gobike-block-header .block-title a {
            color: #149d29;
            text-decoration: none;
            transition: color 0.2s;
        }
        .gobike-block-header .block-title a:hover {
            color: #0e7a1e;
        }
        .gobike-block-header .subcat-link {
            font-size: 14px;
            color: #666;
            text-decoration: none;
            transition: color 0.2s;
        }
        .gobike-block-header .subcat-link:hover {
            color: #149d29;
            text-decoration: underline;
        }
        .gobike-block-header .view-all-link {
            font-size: 13px;
            font-weight: 600;
            color: #149d29;
            text-decoration: none;
            white-space: nowrap;
        }
        .gobike-block-header .view-all-link:hover {
            text-decoration: underline;
        }
        .gobike-block-grid {
            display: grid;
            grid-template-columns: 320px repeat(4, 1fr);
            gap: 12px;
        }
        .gobike-big-item {
            grid-row: span 2;
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: all 0.3s ease;
        }
        .gobike-big-item:hover,
        .gobike-small-item:hover {
            border-color: #149d29;
            box-shadow: 0 4px 15px rgba(20, 157, 41, 0.12);
        }
        .gobike-big-item .img-box {
            position: relative;
            text-align: center;
            margin-bottom: 12px;
            overflow: hidden;
            border-radius: 6px;
        }
        .gobike-big-item .img-box img {
            max-width: 100%;
            height: auto;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        .gobike-big-item:hover .img-box img {
            transform: scale(1.03);
        }
        .gobike-small-item {
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 8px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            position: relative;
            transition: all 0.3s ease;
        }
        .gobike-small-item .img-wrap {
            position: relative;
            text-align: center;
            margin-bottom: 10px;
            overflow: hidden;
            border-radius: 6px;
        }
        .gobike-small-item .img-wrap img {
            max-width: 100%;
            height: 150px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }
        .gobike-small-item:hover .img-wrap img {
            transform: scale(1.04);
        }
        .sale-badge-pill {
            position: absolute;
            top: 8px;
            left: 8px;
            background: #d90429;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 12px;
            line-height: 1.4;
            z-index: 2;
        }
        .product-title,
        .item-title {
            font-size: 13px;
            font-weight: 600;
            line-height: 1.4;
            color: #333;
            margin: 0 0 8px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-decoration: none;
            min-height: 36px;
        }
        .product-title:hover,
        .item-title:hover {
            color: #149d29;
        }
        .gobike-price-box {
            margin-bottom: 8px;
            display: flex;
            align-items: baseline;
            flex-wrap: wrap;
            gap: 6px;
        }
        .gobike-price-box .price-current {
            color: #d90429;
            font-weight: 700;
            font-size: 15px;
        }
        .gobike-price-box .price-old {
            color: #999;
            font-size: 12px;
            text-decoration: line-through;
        }
        .spec-table table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            color: #555;
            margin-top: 10px;
        }
        .spec-table td {
            padding: 4px 0;
            border-bottom: 1px dashed #eee;
        }
        .spec-table td span {
            color: #888;
        }
        .gobike-mobile-viewmore {
            display: none;
            margin: 15px auto 0;
            padding: 9px 20px;
            border: 1px solid #149d29;
            border-radius: 25px;
            color: #149d29;
            font-weight: 600;
            font-size: 13px;
            text-align: center;
            text-decoration: none;
            background: #fff;
        }
        .gobike-mobile-viewmore:hover {
            background: #149d29;
            color: #fff;
        }

        /* ==========================================================================
           2. KHỐI FLASH SALE (sc-flash-sale.php)
           ========================================================================== */
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
        .flashsale-header .view-all-link:hover {
            text-decoration: underline;
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
        .gobike-flashsale-item:hover .item-thumb img {
            transform: scale(1.05);
        }
        .gobike-flashsale-item .sale-badge-pill {
            position: absolute;
            top: 6px;
            left: 6px;
            background: #d90429;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 12px;
            z-index: 2;
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
        .gobike-flashsale-item .item-title:hover {
            color: #d90429;
        }
        .gobike-flashsale-item .item-price {
            display: flex;
            align-items: baseline;
            flex-wrap: wrap;
            gap: 6px;
        }
        .gobike-flashsale-item .price-current {
            color: #d90429;
            font-weight: 700;
            font-size: 15px;
        }
        .gobike-flashsale-item .price-old {
            color: #999;
            font-size: 12px;
            text-decoration: line-through;
        }

        /* ==========================================================================
           3. KHỐI TABS THƯƠNG HIỆU (sc-brand-tabs.php)
           ========================================================================== */
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

        /* ==========================================================================
           4. KHỐI VIDEO REVIEWS (sc-video-reviews.php)
           ========================================================================== */
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
           RESPONSIVE CHUNG CHO TRANG CHỦ
           ========================================================================== */
        @media (max-width: 1024px) {
            .gobike-block-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            .gobike-big-item {
                grid-row: auto;
                grid-column: span 3;
            }
            .gobike-flashsale-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        @media (max-width: 768px) {
            .gobike-block-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            .gobike-big-item {
                grid-column: span 2;
            }
            .gobike-block-header .header-right {
                display: none;
            }
            .gobike-mobile-viewmore {
                display: block;
            }
            .gobike-flashsale-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
            .flashsale-title {
                font-size: 16px;
            }
            .gobike-video-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
            }
        }
    </style>
    <?php
}
