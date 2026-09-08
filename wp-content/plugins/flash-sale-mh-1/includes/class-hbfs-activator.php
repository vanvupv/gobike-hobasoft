<?php
/**
 * Activation: tạo bảng DB và đặt lịch WP-Cron.
 *
 * @package HBWeb_FlashSale
 */

class HBFS_Activator {

	public static function activate() {
		global $wpdb;
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		$charset = $wpdb->get_charset_collate();

		// Bảng 1: Chiến dịch
		$sql_campaigns = "CREATE TABLE `{$wpdb->prefix}hbfs_campaigns` (
			`id`            INT(11)      NOT NULL AUTO_INCREMENT,
			`name`          VARCHAR(200) NOT NULL DEFAULT '',
			`time_begin`    DATETIME     NOT NULL,
			`time_end`      DATETIME     NOT NULL,
			`status`        TINYINT(1)   NOT NULL DEFAULT '0',
			`loop`          TINYINT(1)   NOT NULL DEFAULT '0',
			`loop_minutes`  INT(11)      NOT NULL DEFAULT '60',
			`sold_increase` TINYINT(1)   NOT NULL DEFAULT '0',
			`data_json`     LONGTEXT,
			`created_at`    DATETIME     DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`)
		) $charset;";

		// Bảng 2: Sản phẩm trong chiến dịch
		$sql_products = "CREATE TABLE `{$wpdb->prefix}hbfs_products` (
			`id`              INT(11)       NOT NULL AUTO_INCREMENT,
			`campaign_id`     INT(11)       NOT NULL,
			`product_id`      INT(11)       NOT NULL,
			`parent_id`       INT(11)       NOT NULL DEFAULT '0',
			`slot_index`      INT(11)       NOT NULL DEFAULT '0',
			`has_child`       TINYINT(1)    NOT NULL DEFAULT '0',
			`sku`             VARCHAR(100)  DEFAULT NULL,
			`name`            VARCHAR(255)  NOT NULL DEFAULT '',
			`image`           VARCHAR(500)  DEFAULT NULL,
			`post_thumbnail`  VARCHAR(1000) DEFAULT NULL,
			`regular_price`   DECIMAL(15,2) DEFAULT NULL,
			`sale_price`      DECIMAL(15,2) DEFAULT NULL,
			`flash_sale_price`DECIMAL(15,2) DEFAULT NULL,
			`qty`             INT(11)       DEFAULT NULL,
			`sold`            INT(11)       NOT NULL DEFAULT '0',
			`real_sold`       TINYINT(1)    NOT NULL DEFAULT '0',
			`percent_sold_max`FLOAT         DEFAULT NULL,
			`sort_id`         INT(11)       NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`),
			KEY `campaign_id` (`campaign_id`),
			KEY `product_id`  (`product_id`),
			KEY `slot_index`  (`slot_index`)
		) $charset;";

		dbDelta( $sql_campaigns );
		dbDelta( $sql_products );

		// Kiểm tra thêm cột slot_index nếu chưa có (cho site đã cài từ trước)
		$col_check = $wpdb->get_results( "SHOW COLUMNS FROM `{$wpdb->prefix}hbfs_products` LIKE 'slot_index'" );
		if ( empty( $col_check ) ) {
			$wpdb->query( "ALTER TABLE `{$wpdb->prefix}hbfs_products` ADD `slot_index` INT(11) NOT NULL DEFAULT '0' AFTER `parent_id`" );
		}

		// Xóa lịch cron cũ nếu có
		wp_clear_scheduled_hook( 'hbfs_cron_job' );

		// Lưu version để migrate DB sau này nếu cần
		update_option( 'hbfs_db_version', HBFS_VERSION );
	}
}

