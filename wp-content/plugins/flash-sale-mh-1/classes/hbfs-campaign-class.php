<?php
/**
 * HBFS_Campaign – quản lý chiến dịch Flash Sale.
 * Dùng bảng wp_hbfs_campaigns và wp_hbfs_products.
 *
 * @package HBWeb_FlashSale
 */
class HBFS_Campaign extends FSMH_APP {

	static $table         = 'hbfs_campaigns';
	static $table_product = 'hbfs_products';

	// ===========================
	// LIST
	// ===========================
	static function getCampaigns( $filters = [], $paged = 1 ) {
		global $wpdb;
		$limit  = self::$pageLimit; // 20
		$offset = ( $paged - 1 ) * $limit;

		$where = '1=1';
		if ( isset( $filters['is_launching'] ) && $filters['is_launching'] ) {
			$t     = $filters['is_launching'];
			$where .= $wpdb->prepare( " AND time_begin <= %s AND time_end > %s AND status = 1", $t, $t );
		}

		$total = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}hbfs_campaigns WHERE $where" );
		$rows  = $wpdb->get_results(
			"SELECT * FROM {$wpdb->prefix}hbfs_campaigns WHERE $where ORDER BY id DESC LIMIT $limit OFFSET $offset",
			ARRAY_A
		);

		if ( $rows ) {
			foreach ( $rows as &$row ) {
				$row['data_json'] = json_decode( $row['data_json'], true );
				$row['status']    = (int) $row['status'];
				$row['loop']      = (int) $row['loop'];
				$row['sold_increase'] = (int) $row['sold_increase'];
				$row['total']     = (int) $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(*) FROM {$wpdb->prefix}hbfs_products WHERE campaign_id = %d AND parent_id = 0",
					$row['id']
				) );
			}
		}

		return [
			'data'       => $rows,
			'pagination' => [
				'current_page' => (int) $paged,
				'max_page'     => ceil( $total / $limit ),
				'total'        => (int) $total,
				'limit'        => $limit,
			],
		];
	}

	// ===========================
	// SINGLE
	// ===========================
	static function getCampaign( $filters ) {
		global $wpdb;

		$where = '1=1';
		if ( isset( $filters['id'] ) && $filters['id'] ) {
			$where .= $wpdb->prepare( ' AND c.id = %d', $filters['id'] );
		}
		if ( isset( $filters['is_launching'] ) && $filters['is_launching'] ) {
			$t      = $filters['is_launching'];
			$where .= $wpdb->prepare( ' AND c.time_begin <= %s AND c.time_end >= %s AND c.status = 1', $t, $t );
		}

		$row = $wpdb->get_row(
			"SELECT * FROM {$wpdb->prefix}hbfs_campaigns c WHERE $where LIMIT 1",
			ARRAY_A
		);
		if ( ! $row ) return false;

		$json            = json_decode( $row['data_json'], true ) ?: [];
		$row['data_json'] = $json;
		$row['loop']          = (bool) $row['loop'];
		$row['status']        = (bool) $row['status'];
		$row['sold_increase'] = (bool) $row['sold_increase'];

		// Product limit
		$limit = isset( $filters['is_backend'] ) ? 1000
			: ( isset( $json['product_limit'] ) ? intval( $json['product_limit'] ) : 1000 );
		if ( isset( $filters['get_all_product'] ) ) $limit = 1000;

		$paged  = isset( $filters['page'] ) ? intval( $filters['page'] ) : 1;
		$offset = ( $paged - 1 ) * $limit;

		// Lấy sản phẩm cha (parent_id = 0)
		$parents = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}hbfs_products
			 WHERE campaign_id = %d AND parent_id = 0
			 ORDER BY sort_id ASC
			 LIMIT %d OFFSET %d",
			$row['id'], $limit, $offset
		), ARRAY_A );

		$total_products = (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(*) FROM {$wpdb->prefix}hbfs_products WHERE campaign_id = %d AND parent_id = 0",
			$row['id']
		) );

		foreach ( $parents as &$p ) {
			$p['name']       = get_the_title( $p['product_id'] );
			$p['slot_index'] = isset( $p['slot_index'] ) ? intval( $p['slot_index'] ) : 0;
			$p['children']   = [];
			$children = $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}hbfs_products
				 WHERE campaign_id = %d AND parent_id = %d AND slot_index = %d
				 ORDER BY sort_id ASC",
				$row['id'], $p['product_id'], $p['slot_index']
			), ARRAY_A );
			foreach ( (array) $children as &$c ) {
				$c['name']       = get_the_title( $c['product_id'] );
				$c['slot_index'] = isset( $c['slot_index'] ) ? intval( $c['slot_index'] ) : $p['slot_index'];
				$c['real_sold']  = (bool) $c['real_sold'];
			}
			$p['children']  = $children ?: [];
			$p['real_sold'] = (bool) $p['real_sold'];
		}

		$row['products']   = $parents;
		$row['pagination'] = [
			'current_page' => $paged,
			'max_page'     => $limit > 0 ? ceil( $total_products / $limit ) : 1,
			'total'        => $total_products,
		];

		return $row;
	}

	// ===========================
	// CREATE
	// ===========================
	static function addCampaign( $campaign_data, $products ) {
		global $wpdb;
		$wpdb->insert( $wpdb->prefix . 'hbfs_campaigns', $campaign_data );
		$campaign_id = $wpdb->insert_id;
		if ( ! $campaign_id ) return false;

		foreach ( $products as $i => &$p ) {
			$p['campaign_id'] = $campaign_id;
			$p['sort_id']     = $i;
			$p = self::sanitizeProductRow( $p );
			$wpdb->insert( $wpdb->prefix . 'hbfs_products', $p );
		}
		return $campaign_id;
	}

	// ===========================
	// UPDATE
	// ===========================
	static function updateCampaign( $campaign_data, $products ) {
		global $wpdb;
		$id = intval( $campaign_data['id'] );
		unset( $campaign_data['id'] );
		$wpdb->update( $wpdb->prefix . 'hbfs_campaigns', $campaign_data, [ 'id' => $id ] );

		// Lấy TẤT CẢ sản phẩm hiện có (cả cha lẫn con), map theo (product_id|parent_id|slot_index) => row_id
		$existing = [];
		$rows = $wpdb->get_results( $wpdb->prepare(
			"SELECT id, product_id, parent_id, slot_index FROM {$wpdb->prefix}hbfs_products WHERE campaign_id = %d",
			$id
		), ARRAY_A );
		foreach ( (array) $rows as $r ) {
			$slot_idx         = isset( $r['slot_index'] ) ? intval( $r['slot_index'] ) : 0;
			$key              = $r['product_id'] . '|' . $r['parent_id'] . '|' . $slot_idx;
			$existing[ $key ] = intval( $r['id'] );
		}

		$processed_keys = [];
		foreach ( $products as $i => &$p ) {
			$p['campaign_id'] = $id;
			$p['sort_id']     = $i;
			$p = self::sanitizeProductRow( $p );

			$slot_idx         = isset( $p['slot_index'] ) ? intval( $p['slot_index'] ) : 0;
			$key              = intval( $p['product_id'] ) . '|' . intval( $p['parent_id'] ) . '|' . $slot_idx;
			$processed_keys[] = $key;

			if ( isset( $existing[ $key ] ) ) {
				// Đã tồn tại → update
				$wpdb->update( $wpdb->prefix . 'hbfs_products', $p, [ 'id' => $existing[ $key ] ] );
			} else {
				// Mới → insert
				$wpdb->insert( $wpdb->prefix . 'hbfs_products', $p );
			}
		}

		// Xóa các sản phẩm (cha & con) không còn trong danh sách mới
		foreach ( $existing as $key => $row_id ) {
			if ( ! in_array( $key, $processed_keys, true ) ) {
				$wpdb->delete( $wpdb->prefix . 'hbfs_products', [ 'id' => $row_id ] );
			}
		}
		return $id;
	}

	// ===========================
	// DELETE
	// ===========================
	static function removeCampaign( $id ) {
		global $wpdb;
		$id = intval( $id );
		$wpdb->delete( $wpdb->prefix . 'hbfs_products',  [ 'campaign_id' => $id ] );
		$wpdb->delete( $wpdb->prefix . 'hbfs_campaigns', [ 'id'          => $id ] );
		return true;
	}

	// ===========================
	// HELPERS
	// ===========================
	private static function sanitizeProductRow( $p ) {
		$fields_nullable = [ 'flash_sale_price', 'sale_price', 'regular_price', 'qty', 'percent_sold_max' ];
		foreach ( $fields_nullable as $f ) {
			$p[ $f ] = isset( $p[ $f ] ) && $p[ $f ] !== '' ? floatval( $p[ $f ] ) : null;
		}
		$p['sold']       = isset( $p['sold'] )       ? intval( $p['sold'] )  : 0;
		$p['real_sold']  = filter_var( $p['real_sold'] ?? false, FILTER_VALIDATE_BOOLEAN ) ? 1 : 0;
		$p['has_child']  = ! empty( $p['has_child'] ) ? 1 : 0;
		$p['parent_id']  = isset( $p['parent_id'] )  ? intval( $p['parent_id'] ) : 0;
		$p['slot_index'] = isset( $p['slot_index'] ) ? intval( $p['slot_index'] ) : 0;
		$p['product_id'] = intval( $p['product_id'] );
		$p['sku']        = isset( $p['sku'] )        ? sanitize_text_field( $p['sku'] )  : '';
		$p['name']       = isset( $p['name'] )       ? sanitize_text_field( $p['name'] ) : '';

		// Chỉ giữ các cột tồn tại trong bảng hbfs_products
		$allowed_columns = [
			'campaign_id', 'product_id', 'parent_id', 'slot_index', 'has_child',
			'sku', 'name', 'image', 'post_thumbnail',
			'regular_price', 'sale_price', 'flash_sale_price',
			'qty', 'sold', 'real_sold', 'percent_sold_max', 'sort_id',
		];
		return array_intersect_key( $p, array_flip( $allowed_columns ) );
	}
}
