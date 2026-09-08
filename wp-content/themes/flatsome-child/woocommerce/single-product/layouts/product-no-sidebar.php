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
			<div class="col large-3">
				<div class="description-product">
					<?php the_field( 'description_product' ); ?>
				</div>
			</div>
		</div>
	</div>
	<div class="row tab-product-related">
		<div class="col small-12">
			<div class="tabs nav nav-outline">
				<?php if ( $upsells ) : ?>
				<button class="tab" onclick="opentab(event, 'Upsell')" id="tab-active">Phụ kiện mua cùng</button>
				<?php endif; ?>
				<button class="tab" onclick="opentab(event, 'Related')">Sản phẩm tương tự</button>
			</div>
			<?php if ( $upsells ) : ?>
			<div id="Upsell" class="tab-content">
				<?php echo do_shortcode( '[ux_product_upsell style="grid"]' ); ?>
			</div>
			<?php endif; ?>
			<div id="Related" class="tab-content">
				<?php echo do_shortcode( '[ux_product_related style="grid"]' ); ?> 
			</div>
			<script>
				function opentab(evt, Name) {
				  var i, tabcontent, tab;
				  tabcontent = document.getElementsByClassName("tab-content");
				  for (i = 0; i < tabcontent.length; i++) {
					tabcontent[i].style.display = "none";
				  }
				  tab = document.getElementsByClassName("tab");
				  for (i = 0; i < tab.length; i++) {
					tab[i].className = tab[i].className.replace(" active", "");
				  }
				  document.getElementById(Name).style.display = "block";
				  evt.currentTarget.className += " active";
				}
				document.getElementById("tab-active").click();
			</script>
		</div>
		
	</div>
	
	
  <div class="product-footer">
  	<div class="container">
		<div class="row row-small content-product-page">
			<div class="col medium-9 product-footer-left">
    		<?php
//     			do_action( 'woocommerce_after_single_product_summary' );
    		?>
				<div class="product-page-sections">
					<div class="product-section">
						<div class="entry-content">
							<?php the_content() ;?>
						</div>
						<div class="product-footer-showmore"><a title="Đọc thêm" href="javascript:void(0);" class="button_readmore">Xem thêm <i class="fa fa-angle-down"></i></a></div>
						<script type="text/javascript">
							jQuery(document).ready(function () {
								jQuery(".product-footer-showmore .button_readmore").click(function(){	
									jQuery(".product-page-sections .product-section").addClass("active");
									jQuery(".product-page-sections .product-section.active .product-footer-showmore").remove();
								});	
							});
						</script>
					</div>
					<div class="product-video-reviews show-for-medium">
						<?php 
							$rows = get_field('list_video_review');
							if( $rows ) {
								echo '<h3>Video đánh giá sản phẩm</h3>
								<div class="list-video-reviews">';
								foreach( $rows as $row ) {
									$id_video_review = $row['id_video_review'];
									$apikey = 'AIzaSyD07N7LWo8xPVMXkrNsDNL3aadK09RuPPA'; // change this
									$json = file_get_contents('https://www.googleapis.com/youtube/v3/videos?id=' . $id_video_review . '&key=' . $apikey . '&part=snippet');
									$data = json_decode($json, true);

									echo '<div class="item-video"><a href="https://www.youtube.com/watch?v=' . $id_video_review . '" class="open-video">';
									echo '<div class="img-video"><img src="https://img.youtube.com/vi/' . $id_video_review .'/maxresdefault.jpg" />';
									echo '</div>';
									echo '<div class="title-video">';
									echo $data['items'][0]['snippet']['title'];
									echo '</div></a></div>';
								}
								echo '</div>';
							} ?>
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
			<div class="col medium-3 content-product-footer-right">
				<div class="product-footer-right">
					<?php $thong_so_ky_thuat = get_field( 'thong_so_ky_thuat' ); 
						if ( $thong_so_ky_thuat )  {
					?>
						<h3>Thông số kỹ thuật</h3>
						<div class="table">
							<?php the_field( 'thong_so_ky_thuat' ); ?>
							<a id="more-specific" class="btn btn-default btn-sm" href="javascript:void()">Xem thêm</a>
							<script type="text/javascript">
							jQuery(document).ready(function () {
								jQuery("#more-specific").click(function(){	
									jQuery(".product-footer-right .table table tr").css("display", "table-row");
								});
							});
						</script>
						</div>
					<?php } ?>
				</div>
				<div class="product-video-reviews hide-for-medium">
						<?php 
							$rows = get_field('list_video_review');
							if( $rows ) {
								echo '<h3>Video đánh giá sản phẩm</h3>
								<div class="list-video-reviews">';
								foreach( $rows as $row ) {
									$id_video_review = $row['id_video_review'];
									$apikey = 'AIzaSyD07N7LWo8xPVMXkrNsDNL3aadK09RuPPA'; // change this
									$json = file_get_contents('https://www.googleapis.com/youtube/v3/videos?id=' . $id_video_review . '&key=' . $apikey . '&part=snippet');
									$data = json_decode($json, true);

									echo '<div class="item-video"><a href="https://www.youtube.com/watch?v=' . $id_video_review . '" class="open-video">';
									echo '<div class="img-video"><img src="https://img.youtube.com/vi/' . $id_video_review .'/maxresdefault.jpg" />';
									echo '</div>';
									echo '<div class="title-video">';
									echo $data['items'][0]['snippet']['title'];
									echo '</div></a></div>';
								}
								echo '</div>';
							} ?>
					</div>
			</div>
		</div>
    </div>
  </div>
</div>
