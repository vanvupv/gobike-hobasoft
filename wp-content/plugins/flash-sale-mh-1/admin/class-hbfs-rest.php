<?php
/**
 * HBFS_Rest — WordPress REST API endpoints cho HBWeb Flash Sale Admin.
 *
 * Thay thế admin-ajax.php để tránh bị Cloudflare Bot Fight Mode chặn.
 * Endpoint base: /wp-json/hbfs/v1/
 *
 * Routes:
 *  GET  /wp-json/hbfs/v1/campaigns            → danh sách
 *  GET  /wp-json/hbfs/v1/campaigns/{id}       → single campaign
 *  POST /wp-json/hbfs/v1/campaigns            → thêm/cập nhật campaign
 *  DELETE /wp-json/hbfs/v1/campaigns/{id}     → xóa
 *  GET  /wp-json/hbfs/v1/products?search=xxx  → tìm sản phẩm WooCommerce
 *  GET  /wp-json/hbfs/v1/products/id/{id}     → lấy SP theo ID/SKU
 *  GET  /wp-json/hbfs/v1/option?key=xxx       → lấy option
 *  POST /wp-json/hbfs/v1/option               → lưu option
 *  POST /wp-json/hbfs/v1/reset                → truncate tables
 *
 * @package HBWeb_FlashSale
 */

if ( ! defined( 'WPINC' ) ) die;

class HBFS_Rest {

	const NS = 'hbfs/v1';

	public function __construct() {
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	public function register_routes() {
		$perm = [ $this, 'admin_permission' ];

		// Campaigns list + create/update
		register_rest_route( self::NS, '/campaigns', [
			[ 'methods' => WP_REST_Server::READABLE,  'callback' => [ $this, 'get_campaigns' ], 'permission_callback' => $perm ],
			[ 'methods' => WP_REST_Server::CREATABLE,  'callback' => [ $this, 'save_campaign' ], 'permission_callback' => $perm ],
		] );

		// Single campaign GET + DELETE
		register_rest_route( self::NS, '/campaigns/(?P<id>\d+)', [
			[ 'methods' => WP_REST_Server::READABLE,   'callback' => [ $this, 'get_campaign' ],    'permission_callback' => $perm ],
			[ 'methods' => WP_REST_Server::DELETABLE,  'callback' => [ $this, 'delete_campaign' ], 'permission_callback' => $perm ],
		] );

		// Product search
		register_rest_route( self::NS, '/products', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'search_products' ],
			'permission_callback' => $perm,
			'args'                => [
				'search' => [ 'required' => true, 'sanitize_callback' => 'sanitize_text_field' ],
			],
		] );

		// Product by ID/SKU
		register_rest_route( self::NS, '/products/id/(?P<id>[a-zA-Z0-9_-]+)', [
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => [ $this, 'get_product_by_id' ],
			'permission_callback' => $perm,
		] );

		// Option GET + POST
		register_rest_route( self::NS, '/option', [
			[ 'methods' => WP_REST_Server::READABLE,  'callback' => [ $this, 'get_option' ],  'permission_callback' => $perm ],
			[ 'methods' => WP_REST_Server::CREATABLE, 'callback' => [ $this, 'save_option' ], 'permission_callback' => $perm ],
		] );

		// Reset data
		register_rest_route( self::NS, '/reset', [
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => [ $this, 'reset_data' ],
			'permission_callback' => $perm,
		] );
	}

	// ── Permission ────────────────────────────────────────────────────────────

	public function admin_permission() {
		return current_user_can( 'manage_options' );
	}

	// ── Campaigns ─────────────────────────────────────────────────────────────

	public function get_campaigns( WP_REST_Request $request ) {
		$page = intval( $request->get_param( 'page' ) ?: 1 );
		$data = HBFS_Campaign::getCampaigns( [], $page );
		return rest_ensure_response( $data );
	}

	public function get_campaign( WP_REST_Request $request ) {
		$id   = intval( $request->get_param( 'id' ) );
		$data = HBFS_Campaign::getCampaign( [ 'id' => $id, 'is_backend' => true ] );
		if ( ! $data ) {
			return new WP_Error( 'not_found', 'Không tìm thấy chiến dịch.', [ 'status' => 404 ] );
		}
		return rest_ensure_response( [ 'success' => true, 'data' => [ 'data' => $data ] ] );
	}

	public function save_campaign( WP_REST_Request $request ) {
		$raw = $request->get_json_params();
		if ( empty( $raw ) ) {
			return new WP_Error( 'invalid_data', 'Dữ liệu không hợp lệ.', [ 'status' => 400 ] );
		}

		// Flatten products (cha + biến thể) — giữ logic giống ajax-admin.php
		$products = [];
		foreach ( (array) ( $raw['products'] ?? [] ) as &$p ) {
			$children = $p['children'] ?? [];
			unset( $p['children'] );
			$products[] = $p;
			if ( $children ) {
				$parent_index                            = count( $products ) - 1;
				$products[ $parent_index ]['has_child'] = 1;
				$qty_sum                                 = 0;
				foreach ( $children as $child ) {
					$products[] = $child;
					$qty_sum   += intval( $child['qty'] );
				}
				$products[ $parent_index ]['qty'] = $qty_sum;
			}
		}

		// time_begin / time_end từ slots
		$slots      = $raw['data_json']['time_slots'] ?? [];
		$time_begin = null;
		$time_end   = null;
		foreach ( $slots as $slot ) {
			if ( empty( $slot['time'][0] ) || empty( $slot['time'][1] ) ) continue;
			if ( $time_begin === null || $slot['time'][0] < $time_begin ) $time_begin = $slot['time'][0];
			if ( $time_end   === null || $slot['time'][1] > $time_end   ) $time_end   = $slot['time'][1];
		}
		if ( ! $time_begin ) {
			$time_begin = $time_end = current_time( 'mysql' );
		}

		$campaign_data = [
			'name'          => sanitize_text_field( $raw['name'] ?? '' ),
			'time_begin'    => sanitize_text_field( $time_begin ),
			'time_end'      => sanitize_text_field( $time_end ),
			'loop'          => 0,
			'loop_minutes'  => 0,
			'status'        => filter_var( $raw['status']       ?? false, FILTER_VALIDATE_BOOLEAN ) ? 1 : 0,
			'sold_increase' => filter_var( $raw['sold_increase'] ?? false, FILTER_VALIDATE_BOOLEAN ) ? 1 : 0,
			'data_json'     => wp_json_encode( $raw['data_json'] ?? [] ),
		];

		if ( ! empty( $raw['id'] ) ) {
			$campaign_data['id'] = intval( $raw['id'] );
			$result = HBFS_Campaign::updateCampaign( $campaign_data, $products );
			$msg    = 'Cập nhật chiến dịch thành công';
		} else {
			$result = HBFS_Campaign::addCampaign( $campaign_data, $products );
			$msg    = 'Tạo chiến dịch thành công';
		}

		HBFS_Public::clear_cache();

		if ( $result ) {
			return rest_ensure_response( [ 'success' => true, 'data' => [ 'msg' => $msg, 'data' => $result ] ] );
		}
		return new WP_Error( 'save_failed', 'Lưu thất bại, vui lòng thử lại.', [ 'status' => 500 ] );
	}

	public function delete_campaign( WP_REST_Request $request ) {
		$id = intval( $request->get_param( 'id' ) );
		if ( ! $id ) {
			return new WP_Error( 'missing_id', 'Thiếu ID.', [ 'status' => 400 ] );
		}
		HBFS_Campaign::removeCampaign( $id );
		HBFS_Public::clear_cache();
		return rest_ensure_response( [ 'success' => true, 'data' => [ 'msg' => 'Xóa chiến dịch thành công' ] ] );
	}

	// ── Products ──────────────────────────────────────────────────────────────

	public function search_products( WP_REST_Request $request ) {
		$keyword = $request->get_param( 'search' );
		if ( mb_strlen( $keyword ) < 2 ) {
			return new WP_Error( 'keyword_short', 'Từ khoá quá ngắn (tối thiểu 2 ký tự).', [ 'status' => 400 ] );
		}
		$result = FSMH_Product::searchWooProducts( [ 'search' => $keyword ] );
		return rest_ensure_response( $result );
	}

	public function get_product_by_id( WP_REST_Request $request ) {
		$id     = sanitize_text_field( $request->get_param( 'id' ) );
		$result = FSMH_Product::getWooProductById( $id );
		if ( ! $result ) {
			return new WP_Error( 'not_found', 'Không tìm thấy sản phẩm.', [ 'status' => 404 ] );
		}
		return rest_ensure_response( [ 'success' => true, 'data' => [ 'data' => $result ] ] );
	}

	// ── Options ───────────────────────────────────────────────────────────────

	public function get_option( WP_REST_Request $request ) {
		$key  = sanitize_key( $request->get_param( 'key' ) );
		$data = get_option( $key, false );
		return rest_ensure_response( [ 'success' => true, 'data' => $data ? json_decode( $data, true ) : null ] );
	}

	public function save_option( WP_REST_Request $request ) {
		$body = $request->get_json_params();
		$key  = sanitize_key( $body['key'] ?? '' );
		$data = $body['data'] ?? '';
		if ( ! $key ) {
			return new WP_Error( 'missing_key', 'Thiếu key.', [ 'status' => 400 ] );
		}
		update_option( $key, wp_json_encode( $data ) );
		return rest_ensure_response( [ 'success' => true, 'data' => [ 'msg' => 'Lưu thành công' ] ] );
	}

	// ── Reset ─────────────────────────────────────────────────────────────────

	public function reset_data( WP_REST_Request $request ) {
		global $wpdb;
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}hbfs_campaigns" );
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}hbfs_products" );
		HBFS_Public::clear_cache();
		return rest_ensure_response( [ 'success' => true, 'data' => [ 'msg' => 'Đã xóa toàn bộ dữ liệu' ] ] );
	}
}

new HBFS_Rest();
