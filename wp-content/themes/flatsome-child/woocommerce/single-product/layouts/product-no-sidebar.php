<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
global $product, $post;
		$review_ratings_enabled = wc_review_ratings_enabled();
		if ( ! $review_ratings_enabled ) {
			return;
		}
		$rating_count = $product->get_rating_count();
		$review_count = $product->get_review_count();
		$average      = $product->get_average_rating();
		$id = $product->get_id();
 		$upsells = $product->get_upsells();
?>
<div class="product-container">
	<div class="product-main">
		<div class="row pdb-0">
			<div class="col medium-12 header-title">
				<h1><?php the_title(); ?></h1>
				<div class="meta-title">
					<?php echo flatsome_get_rating_html( $average, $rating_count ); ?>
					<?php if ( get_theme_mod( 'product_info_review_count' ) && get_theme_mod( 'product_info_review_count_style' ) != 'tooltip' ) : ?>
					<?php if ( comments_open() ) : ?>
					<a href="#reviews" class="woocommerce-review-link" rel="nofollow"><?php printf( _n( '%s Đánh Giá', '%s Đánh Giá', $review_count, 'woocommerce' ), '<span class="count">' . esc_html( $review_count ) . '</span>' ); ?></a>
					<?php endif ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div class="row content-row mb-0">
			<div class="product-gallery large-4 col">
				<?php do_action( 'woocommerce_before_single_product_summary' ); ?>
			</div>
			<div class="product-info summary col-fit col entry-summary large-5">
				<?php do_action( 'woocommerce_single_product_summary' ); ?>
				<script type="text/javascript">
					jQuery(document).ready(function () {
						jQuery(document).ready(function(event) {
							var m = jQuery('.price.product-page-price ').html();
							jQuery('.single_variation_wrap').change(function(){
								jQuery('.woocommerce-variation-price').hide();
								var p = jQuery('.single_variation_wrap').find('.price').html();
								jQuery('.price.product-page-price').html(p);
							});
							jQuery('body').on('click','.reset_variations',function(event) {
								jQuery('.price.product-page-price').html(m);
							});
						});
					});
				</script>
			</div>
			<div class="col large-3 col-support-single">
				<?php echo gobike_render_single_product_support_box(); ?>
			</div>
		</div>
	</div>
	
  <div class="product-footer">
  	<div class="container">
		<div class="row row-small content-product-page">
			<div class="col large-9 medium-8 small-12 product-footer-left">
    		<?php
//     			do_action( 'woocommerce_after_single_product_summary' );
    		?>
				<div class="product-page-sections">
					<div class="product-section-header">
						<span class="product-section-title active">THÔNG TIN SẢN PHẨM</span>
					</div>
					<div class="product-section">
						<div class="entry-content">
							<?php the_content() ;?>
						</div>
						<div class="product-footer-showmore" style="display: none;"><a title="Đọc thêm" href="javascript:void(0);" class="button_readmore">Xem thêm <i class="fa fa-angle-down"></i></a></div>
					</div>
					<div class="product-reviews">
					<?php
					$product_tabs = apply_filters( 'woocommerce_product_tabs', array() );
					if ( ! empty( $product_tabs ) ) : ?>
						<?php foreach ( $product_tabs as $key => $product_tab ) : ?>
						<div class="row">
							<div class="large-12 col pb-0 mb-0">
								<div class="panel entry-content">
									<?php
									if ( isset( $product_tab['callback'] ) ) {
										call_user_func( $product_tab['callback'], $key, $product_tab );
									}
									?>
								</div>
							</div>
						</div>
						<?php endforeach; ?>
					<?php endif; ?>
					
					</div>
					
				</div>
			</div>
			<div class="col large-3 medium-4 small-12 content-product-footer-right">
				<div class="product-footer-right">
					<?php $thong_so_ky_thuat = get_field( 'thong_so_ky_thuat' ); 
						if ( $thong_so_ky_thuat )  {
					?>
						<h3 class="spec-title">Thông số kỹ thuật</h3>
						<div class="table spec-table-wrapper">
							<?php the_field( 'thong_so_ky_thuat' ); ?>
							<a id="more-specific" class="btn-more-specific" href="javascript:void(0);" style="display: none;">Xem cấu hình chi tiết</a>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>

		<script type="text/javascript">
		jQuery(document).ready(function ($) {
			// 1. Xử lý "Xem thêm" Thông tin sản phẩm (chỉ hiện khi nội dung vượt quá chiều cao quy định)
			var $productSection = $(".product-page-sections .product-section");
			var $entryContent = $productSection.find(".entry-content");
			var $showMoreWrap = $productSection.find(".product-footer-showmore");
			var $btnReadMore = $showMoreWrap.find(".button_readmore");
			var limitHeight = 500; // Ngưỡng chiều cao (px) để kích hoạt thu gọn / xem thêm

			function updateProductReadmore() {
				if (!$productSection.length || !$entryContent.length) return;
				if ($productSection.hasClass("active")) return; // Giữ nguyên trạng thái nếu người dùng đang mở rộng

				var actualHeight = $entryContent[0] ? $entryContent[0].scrollHeight : 0;

				if (actualHeight > limitHeight + 35) {
					$productSection.addClass("has-readmore");
					$showMoreWrap.show();
					$btnReadMore.html('Xem thêm <i class="fa fa-angle-down"></i>');
				} else {
					$productSection.removeClass("has-readmore active");
					$showMoreWrap.hide();
				}
			}

			// Kiểm tra khi DOM ready
			updateProductReadmore();

			// Lắng nghe khi toàn bộ ảnh trong nội dung tải xong
			$entryContent.find("img").on("load", function() {
				updateProductReadmore();
			});

			// Lắng nghe window load
			$(window).on("load", function() {
				updateProductReadmore();
			});

			// Tự động cập nhật nếu layout/hình ảnh thay đổi kích thước
			if (window.ResizeObserver && $entryContent[0]) {
				var ro = new ResizeObserver(function() {
					updateProductReadmore();
				});
				ro.observe($entryContent[0]);
			}

			// Xử lý sự kiện click nút Xem thêm / Thu gọn
			$btnReadMore.on("click", function(e){
				e.preventDefault();
				if ($productSection.hasClass("active")) {
					$productSection.removeClass("active").addClass("has-readmore");
					$(this).html('Xem thêm <i class="fa fa-angle-down"></i>');
					$("html, body").animate({
						scrollTop: $productSection.offset().top - 80
					}, 300);
				} else {
					$productSection.addClass("active").removeClass("has-readmore");
					$(this).html('Thu gọn <i class="fa fa-angle-up"></i>');
				}
			});

			// 2. Xử lý "Xem cấu hình chi tiết" Thông số kỹ thuật (chỉ hiện khi số hàng > 10)
			var $specWrapper = $(".spec-table-wrapper");
			if ($specWrapper.length) {
				var $btnSpec = $("#more-specific");
				var maxVisibleRows = 10;

				function updateSpecTable() {
					var rowCount = $specWrapper.find("table tr").length;
					if (rowCount <= maxVisibleRows) {
						$btnSpec.hide();
						$specWrapper.addClass("no-more");
					} else {
						$btnSpec.show();
						$specWrapper.removeClass("no-more");
					}
				}

				updateSpecTable();
				$(window).on("load", updateSpecTable);

				$btnSpec.on("click", function(e){
					e.preventDefault();
					$specWrapper.toggleClass("expanded");
					if ($specWrapper.hasClass("expanded")) {
						$(this).text("Thu gọn cấu hình");
					} else {
						$(this).text("Xem cấu hình chi tiết");
						$("html, body").animate({
							scrollTop: $specWrapper.offset().top - 80
						}, 300);
					}
				});
			}
		});
		</script>
    </div>
  </div>
  
  <?php
  // Khối Video Trải Nghiệm Thực Tế cho sản phẩm (ACF hoặc CPT Video Review liên kết)
  if ( function_exists( 'gobike_render_single_product_videos' ) && isset( $product ) ) {
      echo gobike_render_single_product_videos( $product->get_id() );
  }
  ?>

  <div class="container">
	<div class="gobike-related-wrapper">

		<div class="gobike-related-header">
			<div class="gobike-related-title-box">
				<?php if ( ! empty( $upsells ) ) : ?>
					<button type="button" class="gobike-related-tab-btn active" data-target="#RelatedSwiperBox">SẢN PHẨM CÙNG LOẠI</button>
					<button type="button" class="gobike-related-tab-btn" data-target="#UpsellSwiperBox">PHỤ KIỆN MUA CÙNG</button>
				<?php else : ?>
					<span class="gobike-related-title">SẢN PHẨM CÙNG LOẠI</span>
				<?php endif; ?>
			</div>
			<div class="gobike-related-nav">
				<button type="button" class="gobike-nav-btn gobike-btn-prev" aria-label="Trước">
					<svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor"><path d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/></svg>
				</button>
				<button type="button" class="gobike-nav-btn gobike-btn-next" aria-label="Sau">
					<svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor"><path d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg>
				</button>
			</div>
		</div>

		<?php if ( ! empty( $upsells ) ) : ?>
		<div id="UpsellSwiperBox" class="gobike-swiper-box" style="display:none;">
			<div class="swiper-container gobike-related-swiper gobike-upsell-swiper">
				<div class="swiper-wrapper">
					<?php
					foreach ( $upsells as $upsell_id ) :
						$post_object = get_post( $upsell_id );
						if ( ! $post_object ) continue;
						setup_postdata( $GLOBALS['post'] =& $post_object );
					?>
						<div class="swiper-slide">
							<?php wc_get_template_part( 'content', 'product' ); ?>
						</div>
					<?php endforeach; wp_reset_postdata(); ?>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<div id="RelatedSwiperBox" class="gobike-swiper-box">
			<?php
			global $product;
			$related_ids = array();
			if ( $product ) {
				$related_ids = wc_get_related_products( $product->get_id(), 15, $product->get_upsell_ids() );
				if ( count( $related_ids ) < 5 ) {
					$cats = $product->get_category_ids();
					if ( ! empty( $cats ) ) {
						$more_ids = wc_get_products( array(
							'category' => $cats,
							'exclude'  => array( $product->get_id() ),
							'limit'    => 15,
							'return'   => 'ids',
						) );
						$related_ids = array_unique( array_merge( $related_ids, $more_ids ) );
					}
				}
			}
			if ( ! empty( $related_ids ) ) :
			?>
			<div class="swiper-container gobike-related-swiper gobike-main-related-swiper">
				<div class="swiper-wrapper">
					<?php
					foreach ( $related_ids as $rel_id ) :
						$post_object = get_post( $rel_id );
						if ( ! $post_object ) continue;
						setup_postdata( $GLOBALS['post'] =& $post_object );
					?>
						<div class="swiper-slide">
							<?php wc_get_template_part( 'content', 'product' ); ?>
						</div>
					<?php endforeach; wp_reset_postdata(); ?>
				</div>
			</div>
			<?php endif; ?>
		</div>

		<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			function initSwipers() {
				if (typeof Swiper === 'undefined') return;

				var swiperRelated = new Swiper('.gobike-main-related-swiper', {
					slidesPerView: 5,
					spaceBetween: 15,
					watchOverflow: true,
					navigation: {
						nextEl: '.gobike-btn-next',
						prevEl: '.gobike-btn-prev',
					},
					breakpoints: {
						0: {
							slidesPerView: 2,
							spaceBetween: 10
						},
						550: {
							slidesPerView: 3,
							spaceBetween: 12
						},
						850: {
							slidesPerView: 4,
							spaceBetween: 14
						},
						1050: {
							slidesPerView: 5,
							spaceBetween: 15
						}
					}
				});

				if ($('.gobike-upsell-swiper').length) {
					var swiperUpsell = new Swiper('.gobike-upsell-swiper', {
						slidesPerView: 5,
						spaceBetween: 15,
						watchOverflow: true,
						navigation: {
							nextEl: '.gobike-btn-next',
							prevEl: '.gobike-btn-prev',
						},
						breakpoints: {
							0: {
								slidesPerView: 2,
								spaceBetween: 10
							},
							550: {
								slidesPerView: 3,
								spaceBetween: 12
							},
							850: {
								slidesPerView: 4,
								spaceBetween: 14
							},
							1050: {
								slidesPerView: 5,
								spaceBetween: 15
							}
						}
					});
				}

				$('.gobike-related-tab-btn').on('click', function(e) {
					e.preventDefault();
					var target = $(this).data('target');
					$('.gobike-related-tab-btn').removeClass('active');
					$(this).addClass('active');
					$('.gobike-swiper-box').hide();
					$(target).show();
					if (target === '#RelatedSwiperBox' && swiperRelated) {
						swiperRelated.update();
					} else if (target === '#UpsellSwiperBox' && typeof swiperUpsell !== 'undefined') {
						swiperUpsell.update();
					}
				});
			}

			if (typeof Swiper === 'undefined') {
				$.getScript('https://unpkg.com/swiper/swiper-bundle.min.js', function() {
					initSwipers();
				});
			} else {
				initSwipers();
			}
		});
		</script>
	</div>
  </div>
</div>
