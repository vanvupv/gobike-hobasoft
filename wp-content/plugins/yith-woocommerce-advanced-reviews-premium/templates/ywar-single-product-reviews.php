<?php
/**
 * Display single product reviews for YITH WooCommerce Advanced Reviews
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$product_id = yit_get_prop( $product, 'id' );

global $product;
$YWAR_AdvancedReview = YITH_YWAR();

$total_reviews = isset(  $review_stats['voted_reviews'] ) ?  $review_stats['voted_reviews'] :  $review_stats['total'];
$total_reviews = ( $total_reviews > 0 ) ? $total_reviews : 1;

$review_count = $YWAR_AdvancedReview->get_reviews_count( $product_id );
$rating_count = $review_count;
$average      = $YWAR_AdvancedReview->get_average_rating( $product_id );
?>

<div id="ywar_reviews">
	<div id="reviews_summary">
		<h3><?php echo apply_filters( 'ywar_reviews_summary_title', esc_html__( "ĐÁNH GIÁ SẢN PHẨM", 'yith-woocommerce-advanced-reviews' ), $product ); ?></h3>
		<div class="row table-reviews">
			<div class="star-rating-col medium-3">
				<div class="star-point">
				<p style="font-size: 16px;font-weight: 600;">SAO TRUNG BÌNH</p>
				<span><?php printf( esc_html__( '%s', 'yith-woocommerce-advanced-reviews' ), $average ); ?></span> trên 5</div>
				<div class="star-rating">
					<span style="width:<?php echo( ( $average / 5 ) * 100 ); ?>%">
						<strong class="rating"><?php echo esc_html( $average ); ?></strong> <?php printf( esc_html__( 'out of %s5%s', 'yith-woocommerce-advanced-reviews' ), '<span>', '</span>' ); ?>
						<?php printf( _n( 'based on %s customer rating', 'based on %s customer ratings', $rating_count, 'yith-woocommerce-advanced-reviews' ), '<span class="rating">' . $rating_count . '</span>' ); ?>
					</span>
				</div>
			</div>
			<div class="medium-6">
				<?php // do_action( 'ywar_summary', $product, $review_stats ) ?>
				<div class="reviews_bar">
					<?php for ( $i = 5; $i >= 1; $i -- ) :
						$perc = ( $review_stats['total'] == '0' ) ? 0 : floor( $review_stats[ $i ] / $total_reviews * 100 );
						?>
						<div class="ywar_review_row">
							<?php do_action( 'ywar_summary_row_prepend', $i, $product_id, $perc ) ?>
							<span class="ywar_stars_value" style="color:<?php echo get_option( 'ywar_summary_rating_label_color' ); ?>"> <?php printf( _n( '%s', '%s', $i, 'yith-woocommerce-advanced-reviews' ), $i ); ?> <i class="fas fa-star"></i></span>
							<span class="ywar_num_reviews" style="color:<?php echo get_option( 'ywar_summary_count_color' ); ?>"> <?php echo $review_stats[ $i ]; ?> </span>
							<span class="ywar_rating_bar">
								<span style="background-color:<?php echo get_option( 'ywar_summary_bar_color' ); ?>" class="ywar_scala_rating">
									<span class="ywar_perc_rating" style="width: <?php echo $perc; ?>%; background-color:<?php echo get_option( 'ywar_summary_percentage_bar_color' ); ?>">
										<?php if ( 'yes' == get_option( 'ywar_summary_percentage_value' ) ) : ?>
											<span style="color:<?php echo get_option( 'ywar_summary_percentage_value_color' ); ?>" class="ywar_perc_value"><?php printf( '%s %%', $perc ); ?> </span>
										<?php endif; ?>
									</span>
								</span>
							</span>
							<?php do_action( 'ywar_summary_row_append', $i, $product_id, $perc ) ?>
						</div>
					<?php endfor; ?>
				</div>
			</div>
			<div class="medium-3 button-review text-center">
				<button class="send-review"><a href="#review_form_wrapper">Gửi đánh giá của bạn</a></button>
			</div>
		</div>
		
		<?php do_action( 'ywar_summary_append', $product, $review_stats ) ?>
		<?php if ( has_action( 'ywar_reviews_header' ) ) : ?>
			<div id="reviews_header">
				<?php do_action( 'ywar_reviews_header', $review_stats ) ?>
			</div>
		<?php endif; ?>
	</div>

	<?php do_action( 'ywar_after_summary', $product_id, $review_stats ) ?>

	<div id="reviews_dialog"></div>
</div>
