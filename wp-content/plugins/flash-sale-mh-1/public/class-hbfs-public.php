<?php
/**
 * Frontend (Public) class - HBWeb Flash Sale
 *
 * @package HBWeb_FlashSale
 */
class HBFS_Public {

	const TRANSIENT_KEY = 'hbfs_active_products';
	const TRANSIENT_TTL = 300;

	public function __construct() {
		// Price Hooks
		add_filter( 'woocommerce_product_get_price',                [ $this, 'filter_price' ], 10, 2 );
		add_filter( 'woocommerce_product_get_sale_price',           [ $this, 'filter_price' ], 10, 2 );
		add_filter( 'woocommerce_product_variation_get_price',      [ $this, 'filter_price' ], 10, 2 );
		add_filter( 'woocommerce_product_variation_get_sale_price', [ $this, 'filter_price' ], 10, 2 );
		add_filter( 'woocommerce_product_is_on_sale',               [ $this, 'filter_is_on_sale' ], 10, 2 );
		add_filter( 'woocommerce_get_price_html',                   [ $this, 'filter_price_html' ], 100, 2 );

		// Single product bar
		add_action( 'woocommerce_before_add_to_cart_form', [ $this, 'render_single_product_bar' ] );

		// Flash Sale overlay on product loop (shop/category/search pages)
		add_action( 'woocommerce_before_shop_loop_item',       [ $this, 'loop_item_open_wrapper' ],   5 );
		add_action( 'woocommerce_after_shop_loop_item',        [ $this, 'loop_item_close_wrapper' ], 99 );
		add_action( 'woocommerce_after_shop_loop_item',        [ $this, 'loop_item_render_overlay' ], 15 );

		// Cart badge
		add_filter( 'woocommerce_cart_item_name', [ $this, 'cart_item_badge' ], 10, 3 );

		// Shortcode
		add_shortcode( 'hbfs_slider',   [ $this, 'shortcode_slider' ] );
		add_shortcode( 'hbfs_upcoming', [ $this, 'shortcode_upcoming' ] );

		// Order: update sold
		add_action( 'woocommerce_order_status_processing', [ $this, 'update_sold_on_order' ] );
		add_action( 'woocommerce_order_status_completed',  [ $this, 'update_sold_on_order' ] );

		// Assets — chỉ register, enqueue khi cần
		add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );

		// Late footer enqueue: đảm bảo assets được queue nếu có nội dung cần
		add_action( 'wp_footer', [ $this, 'maybe_enqueue_assets' ], 1 );
	}

	// --- Price Filter ---

	public function filter_price( $price, $product ) {
		$item = $this->get_active_flash_item( $product->get_id() );
		if ( ! $item ) return $price;

		// Hết slot sale → giá thường
		if ( $item['qty'] > 0 && intval( $item['sold'] ) >= intval( $item['qty'] ) ) {
			return $price;
		}

		// Flash sale price riêng
		if ( ! empty( $item['flash_sale_price'] ) && floatval( $item['flash_sale_price'] ) > 0 ) {
			return floatval( $item['flash_sale_price'] );
		}

		// Fallback: sale_price Woo
		$woo_sale = get_post_meta( $product->get_id(), '_sale_price', true );
		if ( $woo_sale !== '' ) return floatval( $woo_sale );

		return $price;
	}

	public function filter_price_html( $price_html, $product ) {
		$item = $this->get_active_flash_item( $product->get_id() );
		if ( ! $item ) return $price_html;

		// Hết slot sale → giá thường
		if ( $item['qty'] > 0 && intval( $item['sold'] ) >= intval( $item['qty'] ) ) {
			return $price_html;
		}

		$flash_price   = ! empty( $item['flash_sale_price'] ) ? floatval( $item['flash_sale_price'] ) : null;
		$sale_price    = ! empty( $item['sale_price'] )       ? floatval( $item['sale_price'] )       : null;
		$regular       = ! empty( $item['regular_price'] )    ? floatval( $item['regular_price'] )    : null;
		$display_price = $flash_price ?? $sale_price ?? $regular;
		$old_price     = $regular;

		if ( $display_price ) {
			if ( $old_price && $old_price != $display_price ) {
				return wc_format_sale_price( $old_price, $display_price ) . $product->get_price_suffix();
			} else {
				return wc_price( $display_price ) . $product->get_price_suffix();
			}
		}

		return $price_html;
	}

	public function filter_is_on_sale( $on_sale, $product ) {
		return $this->get_active_flash_item( $product->get_id() ) ? true : $on_sale;
	}

	// --- Cache map ---

	private function get_active_flash_item( int $product_id ) {
		$map = $this->get_active_products_map();
		return $map[ $product_id ] ?? false;
	}

	private function get_campaign_active_slot( array $campaign ) {
		$now_ts = current_time( 'timestamp' );
		$data_json = is_array( $campaign['data_json'] ) ? $campaign['data_json'] : ( json_decode( $campaign['data_json'], true ) ?: [] );

		if ( ! empty( $data_json['time_slots'] ) && is_array( $data_json['time_slots'] ) ) {
			foreach ( $data_json['time_slots'] as $s_idx => $slot ) {
				if ( empty( $slot['time'][0] ) || empty( $slot['time'][1] ) ) {
					continue;
				}
				$begin_ts = strtotime( $slot['time'][0] );
				$end_ts   = strtotime( $slot['time'][1] );
				if ( $now_ts >= $begin_ts && $now_ts <= $end_ts ) {
					$slot['slot_index'] = $s_idx;
					return $slot;
				}
			}
			return false;
		}

		$begin_ts = strtotime( $campaign['time_begin'] );
		$end_ts   = strtotime( $campaign['time_end'] );
		if ( $now_ts >= $begin_ts && $now_ts <= $end_ts ) {
			return [
				'slot_index' => 0,
				'time' => [ $campaign['time_begin'], $campaign['time_end'] ]
			];
		}
		return false;
	}

	private function get_campaign_upcoming_slot( array $campaign ) {
		$now_ts = current_time( 'timestamp' );
		$data_json = is_array( $campaign['data_json'] ) ? $campaign['data_json'] : ( json_decode( $campaign['data_json'], true ) ?: [] );

		$closest_slot = null;
		$closest_diff = null;

		if ( ! empty( $data_json['time_slots'] ) && is_array( $data_json['time_slots'] ) ) {
			foreach ( $data_json['time_slots'] as $s_idx => $slot ) {
				if ( empty( $slot['time'][0] ) || empty( $slot['time'][1] ) ) {
					continue;
				}
				$begin_ts = strtotime( $slot['time'][0] );
				if ( $begin_ts > $now_ts ) {
					$diff = $begin_ts - $now_ts;
					if ( $closest_diff === null || $diff < $closest_diff ) {
						$closest_diff = $diff;
						$slot['slot_index'] = $s_idx;
						$closest_slot = $slot;
					}
				}
			}
			return $closest_slot;
		}

		$begin_ts = strtotime( $campaign['time_begin'] );
		if ( $begin_ts > $now_ts ) {
			return [
				'slot_index' => 0,
				'time' => [ $campaign['time_begin'], $campaign['time_end'] ]
			];
		}
		return false;
	}

	private function get_active_products_map(): array {
		$cached = get_transient( self::TRANSIENT_KEY );
		if ( false !== $cached ) return $cached;

		global $wpdb;
		$now  = current_time( 'mysql' );
		$rows = $wpdb->get_results( $wpdb->prepare(
			"SELECT p.*, c.time_end AS campaign_time_end, c.time_begin AS campaign_time_begin, c.data_json
			 FROM {$wpdb->prefix}hbfs_products p
			 INNER JOIN {$wpdb->prefix}hbfs_campaigns c ON c.id = p.campaign_id
			 WHERE c.status = 1 AND c.time_begin <= %s AND c.time_end >= %s",
			$now, $now
		), ARRAY_A );

		$map = [];
		foreach ( (array) $rows as $row ) {
			$active_slot = $this->get_campaign_active_slot( [
				'time_begin' => $row['campaign_time_begin'],
				'time_end'   => $row['campaign_time_end'],
				'data_json'  => $row['data_json']
			] );
			if ( ! $active_slot ) {
				continue;
			}
			// Chỉ áp dụng sản phẩm thuộc đúng đợt đang active (hoặc nếu sản phẩm gán slot_index 0 và đợt là 0)
			$slot_idx = isset( $row['slot_index'] ) ? intval( $row['slot_index'] ) : 0;
			if ( $slot_idx !== intval( $active_slot['slot_index'] ) ) {
				continue;
			}

			$row['campaign_time_begin'] = $active_slot['time'][0];
			$row['campaign_time_end']   = $active_slot['time'][1];
			$map[ (int) $row['product_id'] ] = $row;
		}
		set_transient( self::TRANSIENT_KEY, $map, self::TRANSIENT_TTL );
		return $map;
	}

	public static function clear_cache() {
		delete_transient( self::TRANSIENT_KEY );
	}

	// --- Campaign meta cache (tránh query lặp trong loop) ---

	private $campaign_meta_cache = [];

	private function get_campaign_meta( int $campaign_id ): array {
		if ( isset( $this->campaign_meta_cache[ $campaign_id ] ) ) {
			return $this->campaign_meta_cache[ $campaign_id ];
		}
		global $wpdb;
		$row = $wpdb->get_row( $wpdb->prepare(
			"SELECT data_json, time_end, time_begin FROM {$wpdb->prefix}hbfs_campaigns WHERE id = %d",
			$campaign_id
		), ARRAY_A );
		$json = [];
		if ( $row && $row['data_json'] ) {
			$json = json_decode( $row['data_json'], true ) ?: [];
			$active_slot = $this->get_campaign_active_slot( [
				'time_begin' => $row['time_begin'],
				'time_end'   => $row['time_end'],
				'data_json'  => $row['data_json']
			] );
			if ( $active_slot ) {
				$json['time_begin'] = $active_slot['time'][0];
				$json['time_end']   = $active_slot['time'][1];
			} else {
				$json['time_begin'] = $row['time_begin'] ?? '';
				$json['time_end']   = $row['time_end']   ?? '';
			}
		}
		$this->campaign_meta_cache[ $campaign_id ] = $json;
		return $json;
	}

	// --- Product Loop Overlay (Flatsome product box) ---

	/**
	 * Bọc thêm wrapper có position:relative quanh product item.
	 * Flatsome đã có wrapper, nhưng ta thêm class riêng để target CSS.
	 */
	public function loop_item_open_wrapper() {
		global $product;
		if ( ! $product ) return;
		$item = $this->get_active_flash_item( $product->get_id() );
		if ( ! $item ) return;
		$this->enqueue_assets_now();
		echo '<div class="hbfs-loop-wrap">';
	}

	public function loop_item_close_wrapper() {
		global $product;
		if ( ! $product ) return;
		$item = $this->get_active_flash_item( $product->get_id() );
		if ( ! $item ) return;
		echo '</div>';
	}

	/**
	 * Render overlay: frame ảnh + sold bar + flash label.
	 */
	public function loop_item_render_overlay() {
		global $product;
		if ( ! $product ) return;
		$item = $this->get_active_flash_item( $product->get_id() );
		if ( ! $item ) return;

		$json      = $this->get_campaign_meta( (int) $item['campaign_id'] );
		$frame_img = ! empty( $json['frame_image'] ) ? $json['frame_image'] : '';
		$sold      = intval( $item['sold'] );
		$qty       = intval( $item['qty'] );
		$percent   = $qty > 0 ? min( 100, round( $sold / $qty * 100 ) ) : 0;
		// time_end: ưu tiên từ $item (đã JOIN campaign khi build map), fallback $json
		$time_end  = ! empty( $item['campaign_time_end'] )
			? $item['campaign_time_end']
			: ( ! empty( $json['time_end'] ) ? $json['time_end'] : '' );

		require HBFS_PATH . 'public/partials/hbfs-loop-overlay.php';
	}

	// --- Single Product Bar ---

	public function render_single_product_bar() {
		global $product;
		if ( ! $product ) return;
		$item = $this->get_active_flash_item( $product->get_id() );
		if ( ! $item ) return;

		// Dùng time_end đã có trong $item (từ get_active_products_map JOIN)
		// hoặc fallback sang get_campaign_meta (có cache) — không cần query thêm
		if ( ! empty( $item['campaign_time_end'] ) ) {
			$time_end = $item['campaign_time_end'];
		} else {
			$meta     = $this->get_campaign_meta( (int) $item['campaign_id'] );
			$time_end = $meta['time_end'] ?? '';
		}

		if ( ! $time_end ) return;

		// Enqueue assets khi product bar thực sự được render
		$this->enqueue_assets_now();

		$sold    = intval( $item['sold'] );
		$qty     = intval( $item['qty'] );
		$percent = $qty > 0 ? min( 100, round( $sold / $qty * 100 ) ) : 0;
		require HBFS_PATH . 'public/partials/hbfs-single-product-bar.php';
	}

	// --- Cart Badge ---

	public function cart_item_badge( $product_name, $cart_item, $cart_item_key ) {
		$pid  = $cart_item['variation_id'] ?: $cart_item['product_id'];
		$item = $this->get_active_flash_item( $pid );
		if ( $item ) {
			$product_name .= ' <span class="hbfs-cart-badge">⚡ Flash Sale</span>';
		}
		return $product_name;
	}

	// --- Shortcode ---

	public function shortcode_slider( $atts ) {
		$atts     = shortcode_atts( [ 'id' => 0 ], $atts, 'hbfs_slider' );
		$id       = intval( $atts['id'] );
		if ( ! $id ) return '';
		$campaign = HBFS_Campaign::getCampaign( [ 'id' => $id ] );
		if ( ! $campaign ) return '';

		$active_slot = $this->get_campaign_active_slot( $campaign );
		if ( ! $active_slot ) {
			return ''; // Ẩn hoàn toàn slider nếu không trong khung giờ hoạt động
		}

		// Đánh dấu cần enqueue Flickity + plugin assets
		$this->needs_slider_assets = true;
		$this->enqueue_assets_now();

		// Ghi đè time_end của chiến dịch bằng mốc kết thúc của khung giờ hiện tại để countdown chạy đúng
		$campaign['time_end'] = $active_slot['time'][1];

		ob_start();
		require HBFS_PATH . 'public/partials/hbfs-slider.php';
		return ob_get_clean();
	}

	// --- Shortcode Upcoming Teaser ---

	public function shortcode_upcoming( $atts ) {
		$atts = shortcode_atts( [
			'id'    => 0,
			'limit' => 8,
			'label' => __( 'Flash Sale Sắp Diễn Ra', 'hbweb-flashsale' ),
		], $atts, 'hbfs_upcoming' );

		$id = intval( $atts['id'] );
		if ( ! $id ) return '';

		$campaign = HBFS_Campaign::getCampaign( [ 'id' => $id ] );
		if ( ! $campaign ) return '';

		$active_slot = $this->get_campaign_active_slot( $campaign );
		$upcoming_slot = $this->get_campaign_upcoming_slot( $campaign );

		// Đang diễn ra → render slider thật
		if ( $active_slot ) {
			return $this->shortcode_slider( [ 'id' => $id ] );
		}

		// Đã kết thúc hoàn toàn hoặc không có slot tương lai → ẩn
		if ( ! $upcoming_slot ) return '';

		// Chưa bắt đầu → render teaser
		$this->enqueue_assets_now();

		$json         = is_array( $campaign['data_json'] ) ? $campaign['data_json'] : [];
		$color        = ! empty( $json['color'] )       ? $json['color']       : '';
		$banner       = ! empty( $json['banner'] )      ? $json['banner']      : '';
		$frame_img    = ! empty( $json['frame_image'] ) ? $json['frame_image'] : '';
		$show_col     = ! empty( $json['show_col'] )    ? intval( $json['show_col'] ) : 5;
		$time_begin   = $upcoming_slot['time'][0];
		$teaser_label = sanitize_text_field( $atts['label'] );

		// Lấy sản phẩm cha, giới hạn theo 'limit'
		$limit        = max( 1, intval( $atts['limit'] ) );
		$all_products = ! empty( $campaign['products'] ) ? $campaign['products'] : [];
		$products     = [];
		foreach ( $all_products as $p ) {
			if ( intval( $p['parent_id'] ) !== 0 ) continue;
			$products[] = $p;
			if ( count( $products ) >= $limit ) break;
		}
		if ( empty( $products ) ) return '';

		ob_start();
		require HBFS_PATH . 'public/partials/hbfs-upcoming-teaser.php';
		return ob_get_clean();
	}

	// --- Update Sold On Order ---

	public function update_sold_on_order( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! $order ) return;
		global $wpdb;
		$now = current_time( 'mysql' );
		foreach ( $order->get_items() as $item ) {
			$pid = $item->get_variation_id() ?: $item->get_product_id();
			$rows = $wpdb->get_results( $wpdb->prepare(
				"SELECT p.id, c.time_begin, c.time_end, c.data_json FROM {$wpdb->prefix}hbfs_products p
				 INNER JOIN {$wpdb->prefix}hbfs_campaigns c ON c.id = p.campaign_id
				 WHERE p.product_id = %d AND c.status = 1
				   AND c.time_begin <= %s AND c.time_end >= %s",
				$pid, $now, $now
			), ARRAY_A );
			foreach ( $rows as $row ) {
				$active_slot = $this->get_campaign_active_slot( [
					'time_begin' => $row['time_begin'],
					'time_end'   => $row['time_end'],
					'data_json'  => $row['data_json']
				] );
				if ( $active_slot ) {
					$wpdb->query( $wpdb->prepare(
						"UPDATE {$wpdb->prefix}hbfs_products SET sold = sold + %d WHERE id = %d",
						$item->get_quantity(), $row['id']
					) );
					break;
				}
			}
		}
		self::clear_cache();
	}

	// --- Assets ---

	/** Flag: shortcode/product bar đã yêu cầu slider assets */
	private $needs_slider_assets = false;
	/** Flag: assets đã enqueue rồi, tránh enqueue 2 lần */
	private $assets_enqueued     = false;

	/**
	 * Bước 1: Register tất cả assets — KHÔNG enqueue.
	 * Chạy sớm trong wp_enqueue_scripts để đảm bảo handle tồn tại.
	 */
	public function register_assets() {
		// Splide.js từ local file thay vì CDN ngoài
		wp_register_style(
			'splide',
			HBFS_URL . 'public/css/splide-core.min.css',
			[],
			'4.1.4'
		);
		wp_register_script(
			'splide',
			HBFS_URL . 'public/js/splide.min.js',
			[],
			'4.1.4',
			true
		);
		wp_register_style( 'hbfs-public', HBFS_URL . 'public/css/hbfs-public.css', [ 'splide' ], HBFS_VERSION );
		wp_add_inline_style( 'hbfs-public', ':root{--hbfs-price-bg:url("' . esc_url( HBFS_URL . 'assets/images/price-gvgs.webp' ) . '");}' );
		wp_register_script( 'hbfs-public', HBFS_URL . 'public/js/hbfs-public.js', [ 'jquery', 'splide' ], HBFS_VERSION, true );

		// Đã loại bỏ load CSS/JS globally (trên toàn trang).
		// Assets sẽ tự động được late-enqueue khi có flash sale item được render (loop, shortcode, product bar).
	}

	/**
	 * Bước 2: Thực sự enqueue — gọi từ shortcode hoặc product bar render.
	 * Chỉ thực hiện 1 lần duy nhất.
	 */
	public function enqueue_assets_now() {
		if ( $this->assets_enqueued ) return;
		$this->assets_enqueued = true;

		wp_enqueue_style( 'splide' );
		wp_enqueue_script( 'splide' );
		wp_enqueue_style( 'hbfs-public' );
		wp_enqueue_script( 'hbfs-public' );
		wp_localize_script( 'hbfs-public', 'HBFS', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'hbfs_nonce' ),
		] );
	}

	/**
	 * Bước 3 (fallback): Nếu shortcode dùng load_ajax=true, HTML của slider
	 * không được render khi shortcode() chạy → assets cần được enqueue
	 * muộn hơn. Hook wp_footer (priority 1) để đảm bảo scripts vẫn in ra.
	 */
	public function maybe_enqueue_assets() {
		if ( $this->needs_slider_assets ) {
			$this->enqueue_assets_now();
		}
	}
}
