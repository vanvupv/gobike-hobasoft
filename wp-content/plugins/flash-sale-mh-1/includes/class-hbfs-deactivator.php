<?php
/**
 * Runs on plugin deactivation.
 */
class HBFS_Deactivator {
	public static function deactivate() {
		// Xóa lịch WP-Cron khi tắt plugin
		$timestamp = wp_next_scheduled( 'hbfs_cron_job' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'hbfs_cron_job' );
		}
		// Xóa transient cache
		delete_transient( 'hbfs_active_products' );
	}
}
