<?php
/**
 * HBWeb Flash Sale - Additional helper functions.
 * Auto-included from flash-sale-mh.php after helpers/functions.php.
 *
 * @package HBWeb_FlashSale
 */

if ( ! defined( 'WPINC' ) ) die;

/**
 * Build an inline style string from a campaign's color config.
 */
if ( ! function_exists( 'hbfs_get_campaign_style' ) ) {
    function hbfs_get_campaign_style( $campaign ): string {
        if ( ! is_array( $campaign ) ) {
            $campaign = [];
        }
        $raw  = $campaign['data_json'] ?? [];
        $json = is_array( $raw ) ? $raw : ( json_decode( $raw, true ) ?: [] );
        $color = isset( $json['color'] ) ? trim( $json['color'] ) : '';
        if ( ! $color ) return '';
        return 'background:' . esc_attr( $color ) . ';';
    }
}

/**
 * Build inline style cho .hbfs-product-bar (single product page).
 * Ưu tiên bar_bg_color (riêng cho bar), fallback về color chung.
 */
if ( ! function_exists( 'hbfs_get_bar_style' ) ) {
    function hbfs_get_bar_style( $campaign ): string {
        if ( ! is_array( $campaign ) ) {
            $campaign = [];
        }
        $raw       = $campaign['data_json'] ?? [];
        $json      = is_array( $raw ) ? $raw : ( json_decode( $raw, true ) ?: [] );
        $bar_color = isset( $json['bar_bg_color'] ) ? trim( $json['bar_bg_color'] ) : '';
        $color     = isset( $json['color'] ) ? trim( $json['color'] ) : '';
        $use_color = $bar_color ?: $color;
        if ( ! $use_color ) return '';
        return 'background:' . esc_attr( $use_color ) . ';';
    }
}

/**
 * Format price for display (WC or fallback).
 */
if ( ! function_exists( 'hbfs_format_price' ) ) {
    function hbfs_format_price( $price ): string {
        if ( function_exists( 'wc_price' ) ) {
            return wc_price( $price );
        }
        return number_format( (float) $price, 0, ',', '.' ) . '&#x20ab;';
    }
}

/**
 * Calculate discount percent between regular and flash/sale price.
 */
if ( ! function_exists( 'hbfs_discount_percent' ) ) {
    function hbfs_discount_percent( $regular, $flash ): int {
        $regular = floatval( $regular );
        $flash   = floatval( $flash );
        if ( ! $regular || ! $flash ) return 0;
        return (int) round( 100 - $flash / $regular * 100 );
    }
}

/**
 * Mask price for upcoming flash sale (e.g. 18490000 -> 1x.xxx.000₫, 26490000 -> 2x.xxx.000₫).
 */
if ( ! function_exists( 'hbfs_mask_price' ) ) {
    function hbfs_mask_price( $price ): string {
        $num = (int) round( floatval( $price ) );
        if ( ! $num ) {
            return 'xx.xxx.000₫';
        }
        $str = (string) $num;
        $len = strlen( $str );
        if ( $len >= 7 ) {
            // Giữ chữ số đầu tiên, phần còn lại ẩn dạng x.xxx.000₫
            $first = substr( $str, 0, 1 );
            $masked = $first . 'x.xxx.000₫';
            if ( $len >= 8 ) {
                $first_two = substr( $str, 0, 1 );
                $masked = $first_two . 'x.xxx.000₫';
            }
            return $masked;
        }
        return 'xx.xxx₫';
    }
}
