<?php
/**
 * ACF Options Page Configuration
 * 
 * @package Flatsome-Child
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Đăng ký trang Theme Settings trong WP Admin
add_action('acf/init', 'gobike_register_acf_options_page');
function gobike_register_acf_options_page()
{
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(array(
            'page_title' => 'Cài đặt chung Website',
            'menu_title' => 'Theme Settings',
            'menu_slug'  => 'theme-general-settings',
            'capability' => 'edit_posts',
            'redirect'   => false,
        ));
    }
}
