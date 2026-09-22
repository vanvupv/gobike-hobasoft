<?php
/**
 * Single Template: Video Review
 * Tự động gọi cho toàn bộ bài viết thuộc Custom Post Type video_review
 * 
 * @package Flatsome-Child
 */

if (have_posts()) {
    the_post();
    $GLOBALS['gb_vd_post_setup'] = true;
}

require_once get_stylesheet_directory() . '/template-video-detail.php';
