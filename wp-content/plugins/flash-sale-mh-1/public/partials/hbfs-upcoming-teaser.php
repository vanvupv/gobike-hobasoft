<?php
/**
 * Template: Upcoming Flash Sale Teaser
 * Hiển thị khi chiến dịch chưa bắt đầu hoặc đang hiển thị tổng thể chiến dịch.
 */
if ( ! defined( 'WPINC' ) ) die;

$slider_id = intval( $campaign['id'] );
$style     = $color ? 'background:' . esc_attr( $color ) . ';' : '';
$json      = is_array( $campaign['data_json'] ) ? $campaign['data_json'] : [];

// Autoplay kế thừa từ campaign
$autoplay_interval    = isset( $json['autoplay_interval'] ) ? intval( $json['autoplay_interval'] ) : 3;
$autoplay_pause_hover = isset( $json['autoplay_pause_hover'] ) ? (bool) $json['autoplay_pause_hover'] : true;
$autoplay_enabled     = $autoplay_interval > 0;

// Time slots for tabs
$time_slots = ! empty( $json['time_slots'] ) && is_array( $json['time_slots'] ) ? $json['time_slots'] : [];

// Lấy sản phẩm cha và gom nhóm theo slot_index
$all_products = ! empty( $campaign['products'] ) ? $campaign['products'] : [];
$slot_products_map = [];

if ( ! empty( $time_slots ) ) {
    foreach ( $time_slots as $s_idx => $slot ) {
        $slot_products_map[ $s_idx ] = [];
    }
} else {
    $slot_products_map[0] = [];
}

foreach ( $all_products as $p ) {
    if ( intval( $p['parent_id'] ) !== 0 ) continue;
    $s_idx = isset( $p['slot_index'] ) ? intval( $p['slot_index'] ) : 0;
    if ( ! isset( $slot_products_map[ $s_idx ] ) ) {
        $slot_products_map[ $s_idx ] = [];
    }
    $slot_products_map[ $s_idx ][] = $p;
}

$frame_bg_color = ! empty( $json['frame_bg_color'] ) ? $json['frame_bg_color'] : '';
$frame_style    = $frame_bg_color ? 'background:' . esc_attr( $frame_bg_color ) . ';' : '';
?>

<div class="hbfs-slider-wrap hbfs-upcoming-teaser hbfs-slider-wrap-<?php echo $slider_id; ?>-upcoming">

	<?php if ( $banner ) : ?>
	<div class="hbfs-slider-banner">
		<img src="<?php echo esc_url( $banner ); ?>" alt="Flash Sale Banner">
	</div>
	<?php endif; ?>

	<div class="hbfs-slider-box-frame" style="<?php echo $frame_style; ?>">

	<?php if ( ! empty( $time_slots ) ) : ?>
	<!-- Flash Sale Event Tabs -->
	<ul class="flash-sale-tabs">
		<?php 
		$slot_index = 0;
		foreach ( $time_slots as $slot ) :
			if ( empty( $slot['time'][0] ) || empty( $slot['time'][1] ) ) continue;
			$slot_start_dt = strtotime( $slot['time'][0] );
			$date_tab      = date( 'd/m', $slot_start_dt );
			$date_val      = date( 'Y-m-d', $slot_start_dt );
			$time_val      = date( 'H:i', $slot_start_dt );
		?>
		<li class="<?php echo $slot_index === 0 ? 'active' : ''; ?>" 
			data-index="<?php echo $slot_index; ?>" 
			data-slot-target="<?php echo $slot_index; ?>"
			data-date="<?php echo esc_attr( $date_val ); ?>" 
			data-time="<?php echo esc_attr( $time_val ); ?>"
			data-start="<?php echo esc_attr( $slot['time'][0] ); ?>"
			data-end="<?php echo esc_attr( $slot['time'][1] ); ?>">
			<div class="tab-date"><?php echo esc_html( $date_tab ); ?></div>
			<!-- Đếm ngược + trạng thái nằm ngay trong tab -->
			<div class="tab-countdown">
				<span class="tab-status" id="tab-status-<?php echo $slot_index; ?>">...</span>
				<span class="tab-timer" id="tab-timer-<?php echo $slot_index; ?>"></span>
			</div>
		</li>
		<?php 
			$slot_index++;
		endforeach; 
		?>
	</ul>
	<?php else : ?>
	<div class="hbfs-slider-header hbfs-upcoming-header" style="<?php echo $style; ?>">
		<svg width="22" height="22" viewBox="0 0 24 24" fill="#fff" style="flex-shrink:0">
			<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/>
		</svg>

		<div class="hbfs-upcoming-header__text">
			<span class="hbfs-upcoming-badge"><?php echo esc_html( $teaser_label ); ?></span>
		</div>

		<div class="hbfs-right">
			<span class="hbfs-upcoming-header__sub">Bắt đầu trong</span>
			<div class="hbfs-slider-header__countdown hbfs-countdown"
				 data-hbfs-countdown="<?php echo esc_attr( $time_begin ); ?>">
				<span class="hbfs-countdown__block">--<small>ngày</small></span>
				<span class="hbfs-countdown__sep">:</span>
				<span class="hbfs-countdown__block">--<small>giờ</small></span>
				<span class="hbfs-countdown__sep">:</span>
				<span class="hbfs-countdown__block">--<small>phút</small></span>
				<span class="hbfs-countdown__sep">:</span>
				<span class="hbfs-countdown__block">--<small>giây</small></span>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- Splide carousel rows for each time slot -->
	<?php 
	$now_ts = current_time( 'timestamp' );
	foreach ( $slot_products_map as $s_idx => $products ) :
		$slot_time = ! empty( $time_slots[ $s_idx ]['time'] ) ? $time_slots[ $s_idx ]['time'] : null;
		$is_slot_active = false;
		if ( $slot_time && ! empty( $slot_time[0] ) && ! empty( $slot_time[1] ) ) {
			$slot_begin_ts = strtotime( $slot_time[0] );
			$slot_end_ts   = strtotime( $slot_time[1] );
			if ( $now_ts >= $slot_begin_ts && $now_ts <= $slot_end_ts ) {
				$is_slot_active = true;
			}
		}

		$product_count = count( $products );
		$use_loop      = $product_count > $show_col;
		$use_autoplay  = $autoplay_enabled && $use_loop;

		$splide_config = wp_json_encode( [
			'type'         => $use_loop ? 'loop' : 'slide',
			'rewind'       => ! $use_loop,
			'perPage'      => $show_col,
			'perMove'      => 1,
			'gap'          => '15px',
			'padding'      => 0,
			'arrows'       => $product_count > 1,
			'pagination'   => false,
			'drag'         => $product_count > 1,
			'autoplay'     => $use_autoplay,
			'interval'     => $autoplay_interval * 1000,
			'pauseOnHover' => $autoplay_pause_hover,
			'resetProgress'=> false,
			'lazyLoad'     => 'nearby',
			'breakpoints'  => [
				992 => [ 'perPage' => min( $show_col, 4 ) ],
				768 => [ 'perPage' => min( $show_col, 3 ) ],
				480 => [ 'perPage' => min( $show_col, 2 ) ],
			],
		] );
	?>
	<div class="hbfs-products-row splide hbfs-slot-products-row hbfs-slot-row-<?php echo esc_attr( $s_idx ); ?>"
		 data-slot-index="<?php echo esc_attr( $s_idx ); ?>"
		 data-hbfs-splide='<?php echo esc_attr( $splide_config ); ?>'
		 style="<?php echo $s_idx === 0 ? '' : 'display:none;'; ?>">
		<div class="splide__track">
			<ul class="splide__list">

				<?php 
				if ( ! empty( $products ) ) :
					foreach ( $products as $p ) :
						$pid         = intval( $p['product_id'] );
						$product_url = get_permalink( $pid );
						$image       = ! empty( $p['post_thumbnail'] ) ? $p['post_thumbnail']
							: ( ! empty( $p['image'] ) ? $p['image'] : wc_placeholder_img_src() );
						$name        = ! empty( $p['name'] ) ? $p['name'] : get_the_title( $pid );
						$flash_price = ! empty( $p['flash_sale_price'] ) ? floatval( $p['flash_sale_price'] ) : null;
						$sale_price  = ! empty( $p['sale_price'] )       ? floatval( $p['sale_price'] )       : null;
						$regular     = ! empty( $p['regular_price'] )    ? floatval( $p['regular_price'] )    : null;
						$compare_price = $flash_price ?? $sale_price ?? $regular;
						$discount    = ( $compare_price && $regular && $regular > 0 )
							? round( 100 - $compare_price / $regular * 100 ) : 0;
						$qty         = intval( $p['qty'] );
						$slot_base   = $qty > 0 ? $qty : 20;
						$slot_text   = $is_slot_active
							? ( $qty > 0 ? ( 'Mở bán ' . $qty . ' suất' ) : 'Đang mở bán' )
							: ( $qty > 0 ? ( 'Sắp mở bán ' . $qty . ' suất' ) : 'Sắp mở bán' );

						$button_text = $is_slot_active ? 'Mua ngay' : 'Sắp diễn ra';
						$masked_price = hbfs_mask_price( $compare_price );
				?>
				<li class="splide__slide">
					<div class="item">
						<div class="news-item-products news-item-products-flashsale product-item" data-auto-slot="1">
							<a href="<?php echo esc_url( $product_url ); ?>" title="<?php echo esc_attr( $name ); ?>"></a>
							
							<div class="relative fix-images">
								<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $name ); ?>" class="img-responsive center-block lazied" loading="lazy">
								<?php if ( $frame_img ) : ?>
								<div class="hbfs-product-card__frame">
									<img src="<?php echo esc_url( $frame_img ); ?>" alt="">
								</div>
								<?php endif; ?>
							</div>

							<div class="aaa-sale-slot">
								<span class="sale-slot-icon">⚡</span>
								<div class="sale-slot <?php echo $is_slot_active ? 'active' : 'upcoming'; ?>" data-base-slot="<?php echo esc_attr( $slot_base ); ?>"><?php echo esc_html( $slot_text ); ?></div>
							</div>

							<div class="price_flashsale">
								<div class="price-content">
									<?php if ( $is_slot_active ) : ?>
										<div class="current-price" data-price="<?php echo esc_attr( $compare_price ); ?>"><?php echo wc_price( $compare_price ); ?></div>
										<?php if ( $regular && $regular > $compare_price ) : ?>
										<div class="original-price"><?php echo wc_price( $regular ); ?></div>
										<?php endif; ?>
									<?php else : ?>
										<div class="current-price" data-price="<?php echo esc_attr( $compare_price ); ?>"><?php echo esc_html( $masked_price ); ?></div>
										<?php if ( $regular ) : ?>
										<div class="original-price"><?php echo wc_price( $regular ); ?></div>
										<?php endif; ?>
									<?php endif; ?>
								</div>

								<?php if ( $is_slot_active && $discount > 0 ) : ?>
								<label class="discount-badge">- <?php echo $discount; ?>%</label>
								<?php else : ?>
								<label class="discount-badge">- xx%</label>
								<?php endif; ?>
							</div>

							<h3><?php echo esc_html( $name ); ?></h3>

							<div class="product-coming <?php echo $is_slot_active ? 'product-buying' : ''; ?>" style="display: block;">
								<span><?php echo esc_html( $button_text ); ?></span>
							</div>

						</div>
					</div>
				</li>
				<?php 
					endforeach; 
				else :
				?>
				<li class="splide__slide" style="width:100%;">
					<div class="text-center q-pa-lg text-grey" style="padding:40px; text-align:center; color:#999;">
						Chưa có sản phẩm nào cho đợt này.
					</div>
				</li>
				<?php endif; ?>

			</ul>
		</div><!-- .splide__track -->
	</div><!-- .hbfs-products-row.splide -->
	<?php endforeach; ?>

	</div><!-- .hbfs-slider-box-frame -->

</div><!-- .hbfs-upcoming-teaser -->



