<?php
/**
 * AJAX Admin handlers for HBWeb Flash Sale.
 * Tất cả đều kế thừa FSMH_APP (dùng chung DB layer).
 */
if ( ! defined( 'WPINC' ) ) die;

class HBFS_Ajax_Admin extends FSMH_APP {

	public function __construct() {
		parent::__construct(); // FSMH_APP: gọi để DB static được set nếu cần
		$admin_actions = [
			'hbfs_get_campaigns'       => 'get_campaigns',
			'hbfs_get_campaign'        => 'get_campaign',
			'hbfs_add_campaign'        => 'add_campaign',
			'hbfs_remove_campaign'     => 'remove_campaign',
			'hbfs_search_products'     => 'search_products',
			'hbfs_get_product_by_id'   => 'get_product_by_id',
			'hbfs_get_option'          => 'get_option',
			'hbfs_save_option'         => 'save_option',
			'hbfs_reset_data'          => 'reset_data',
		];
		foreach ( $admin_actions as $action => $method ) {
			add_action( 'wp_ajax_' . $action, [ $this, $method ] );
		}
	}

	// ---------- Helpers ----------

	private function verify_nonce() {
		if ( ! check_ajax_referer( 'hbfs_nonce', 'nonce', false ) ) {
			wp_send_json_error( [ 'msg' => 'Phiên làm việc hết hạn, vui lòng tải lại trang.' ] );
		}
	}

	private function require_admin() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'msg' => 'Bạn không có quyền thực hiện thao tác này.' ] );
		}
	}

	private function ok( $data = [], $msg = 'Thành công' ) {
		wp_send_json_success( [ 'msg' => $msg, 'data' => $data ] );
	}

	private function fail( $msg = 'Có lỗi xảy ra' ) {
		wp_send_json_error( [ 'msg' => $msg ] );
	}

	// ---------- Campaigns ----------

	public function get_campaigns() {
		$this->verify_nonce();
		$page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 1;
		$data = HBFS_Campaign::getCampaigns( [], $page );
		wp_send_json( $data ); // trả nguyên để giữ pagination
	}

	public function get_campaign() {
		$this->verify_nonce();
		$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
		if ( ! $id ) { $this->fail( 'Thiếu ID chiến dịch' ); return; }
		$data = HBFS_Campaign::getCampaign( [ 'id' => $id, 'is_backend' => true ] );
		$data ? $this->ok( $data ) : $this->fail( 'Không tìm thấy chiến dịch' );
	}

	public function add_campaign() {
		$this->verify_nonce();
		$this->require_admin();

		$raw = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : '';
		if ( ! $raw ) { $this->fail( 'Dữ liệu không hợp lệ' ); return; }

		// Flatten products (cha + biến thể)
		$products = [];
		foreach ( (array) $raw['products'] as $key => &$p ) {
			$children = [];
			if ( isset( $p['children'] ) ) {
				$children = $p['children'];
				unset( $p['children'] );
			}
			$products[] = $p;
			if ( $children ) {
				$parent_index                               = count( $products ) - 1;
				$products[ $parent_index ]['has_child']    = 1;
				$qty_sum                                    = 0;
				foreach ( $children as $child ) {
					$products[] = $child;
					$qty_sum   += intval( $child['qty'] );
				}
				$products[ $parent_index ]['qty'] = $qty_sum;
			}
		}

		$slots = isset( $raw['data_json']['time_slots'] ) ? $raw['data_json']['time_slots'] : [];
		$time_begin = null;
		$time_end = null;
		foreach ( $slots as $slot ) {
			if ( empty( $slot['time'][0] ) || empty( $slot['time'][1] ) ) {
				continue;
			}
			$slot_begin = $slot['time'][0];
			$slot_end   = $slot['time'][1];
			if ( $time_begin === null || $slot_begin < $time_begin ) {
				$time_begin = $slot_begin;
			}
			if ( $time_end === null || $slot_end > $time_end ) {
				$time_end = $slot_end;
			}
		}
		if ( $time_begin === null || $time_end === null ) {
			$time_begin = current_time( 'mysql' );
			$time_end   = current_time( 'mysql' );
		}

		$campaign_data = [
			'name'          => sanitize_text_field( $raw['name'] ),
			'time_begin'    => sanitize_text_field( $time_begin ),
			'time_end'      => sanitize_text_field( $time_end ),
			'loop'          => 0,
			'loop_minutes'  => 0,
			'status'        => filter_var( $raw['status'] ?? false, FILTER_VALIDATE_BOOLEAN ) ? 1 : 0,
			'sold_increase' => filter_var( $raw['sold_increase'] ?? false, FILTER_VALIDATE_BOOLEAN ) ? 1 : 0,
			'data_json'     => wp_json_encode( $raw['data_json'] ),
		];

		if ( isset( $raw['id'] ) && $raw['id'] ) {
			$campaign_data['id'] = intval( $raw['id'] );
			$result = HBFS_Campaign::updateCampaign( $campaign_data, $products );
			$msg    = 'Cập nhật chiến dịch thành công';
		} else {
			$result = HBFS_Campaign::addCampaign( $campaign_data, $products );
			$msg    = 'Tạo chiến dịch thành công';
		}

		HBFS_Public::clear_cache();
		$result ? $this->ok( $result, $msg ) : $this->fail( 'Lưu thất bại, vui lòng thử lại' );
	}

	public function remove_campaign() {
		$this->verify_nonce();
		$this->require_admin();
		$id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
		if ( ! $id ) { $this->fail(); return; }
		$r = HBFS_Campaign::removeCampaign( $id );
		HBFS_Public::clear_cache();
		$r ? $this->ok( [], 'Xóa chiến dịch thành công' ) : $this->fail();
	}

	// ---------- Products ----------

	public function search_products() {
		$this->verify_nonce();
		$this->require_admin();
		$filters = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : [];
		// Giới hạn tối thiểu 2 ký tự để tránh query quá rộng
		if ( empty( $filters['search'] ) || mb_strlen( $filters['search'] ) < 2 ) {
			$this->fail( 'Từ khoá tìm kiếm quá ngắn' );
			return;
		}
		$result = FSMH_Product::searchWooProducts( $filters );
		wp_send_json( $result );
	}

	public function get_product_by_id() {
		$this->verify_nonce();
		$id     = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
		$result = FSMH_Product::getWooProductById( $id );
		$result ? $this->ok( $result ) : $this->fail( 'Không tìm thấy sản phẩm' );
	}

	// ---------- Options / Settings ----------

	public function get_option() {
		$key  = isset( $_POST['key'] ) ? sanitize_key( $_POST['key'] ) : '';
		$data = get_option( $key, false );
		$this->ok( $data ? json_decode( $data, true ) : null );
	}

	public function save_option() {
		$this->verify_nonce();
		$this->require_admin();
		$key  = isset( $_POST['key'] )  ? sanitize_key( $_POST['key'] ) : '';
		$data = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] )  : '';
		if ( ! $key || ! $data ) { $this->fail(); return; }
		update_option( $key, wp_json_encode( $data ) );
		$this->ok( json_decode( get_option( $key ), true ), 'Lưu cài đặt thành công' );
	}

	public function reset_data() {
		$this->verify_nonce();
		$this->require_admin();
		global $wpdb;
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}hbfs_campaigns" );
		$wpdb->query( "TRUNCATE TABLE {$wpdb->prefix}hbfs_products" );
		HBFS_Public::clear_cache();
		$this->ok( [], 'Đã xóa toàn bộ dữ liệu' );
	}

}


new HBFS_Ajax_Admin();