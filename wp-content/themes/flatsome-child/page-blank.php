<?php
/*
Template name: Page - Full Width
*/
get_header(); ?>

<?php do_action( 'flatsome_before_page' ); ?>

<div id="content" role="main" class="content-area">
	<?php if ( is_front_page() ) { ?>
	<div class="banner-home">
		<div class="container">
			<div class="row">
				<div class="col large-3 hide-for-medium box_left">
					<div class="menu-top">
						<?php if ( is_active_sidebar( 'menu-main' ) ) : ?>
						<?php dynamic_sidebar( 'menu-main' ); ?>
						<?php endif; ?>
					</div>
				</div>
				<div class="col slider-top large-7 medium-12 box_center"><div class="swiper-container">
					<?php 
						$rows = get_field('slider_top');
						if( $rows ) {
							echo '<div class="mySwiper2">
							  <div class="swiper-wrapper">
								';
							foreach( $rows as $row ) {
								$image = $row['image_slide'];
								$link_url = $row['link_url'];
								echo '<div class="swiper-slide">';
									echo '<a href="' . $link_url . '">' ;
									echo '<img src="' . $image['url'] . '" /></a>';
								echo '</div>';
							}
							echo '</div>
							  <div class="swiper-button-next"></div>
							  <div class="swiper-button-prev"></div>
							</div>';
							echo '<div class="mySwiper">
							  <div class="swiper-wrapper">';
							foreach( $rows as $row ) {
									echo '<div class="swiper-slide">';
									echo $row['title_slide'] ;
									echo '<br>';
									echo $row['des_slide'] ;
									echo '</div>';
							}
							echo '</div></div>';
						} ?>
		<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
		<script>
		  var swiper = new Swiper(".mySwiper", {
			spaceBetween: 0,
			slidesPerView: 5,
			freeMode: false,
			allowTouchMove: false,
			watchSlidesVisibility: true,
			watchSlidesProgress: true,
		  });
		  var swiper2 = new Swiper(".mySwiper2", {
			spaceBetween: 0,
			loop: true,
			autoplay: {
			  delay: 2500,
			  disableOnInteraction: false,
			},
			navigation: {
			  nextEl: ".swiper-button-next",
			  prevEl: ".swiper-button-prev",
			},
			thumbs: {
			  swiper: swiper,
			},
		  });
		</script>
				</div></div>
												<div class="col large-3 hide-for-medium box_right">
					<!-- ĐÃ THÊM BOX-SHADOW ĐỔ BÓNG ĐẸP NHƯ SLIDER -->
					<div class="news-home-box" style="background:#fff; padding:12px; border-radius:8px; border:1px solid #eee; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);">
						<h3 style="font-size:14px; font-weight:bold; text-transform:uppercase; margin:0 0 10px 0; border-bottom:2px solid #149D29; padding-bottom:5px; color:#333;">
							Tin tức mới nhất
						</h3>
						
						<!-- DANH SÁCH 3 BÀI VIẾT -->
						<div class="news-list">
							<?php 
							$news_query = new WP_Query(array(
								'post_type'      => 'post',
								'posts_per_page' => 3,
								'post_status'    => 'publish'
							));

							if ( $news_query->have_posts() ) :
								while ( $news_query->have_posts() ) : $news_query->the_post();
									$thumb_url = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') : 'https://via.placeholder.com/80x60';
									?>
									<div style="display:flex; gap:10px; margin-bottom:10px; align-items:center;">
										<a href="<?php the_permalink(); ?>" style="width:70px; height:50px; flex-shrink:0; display:block; overflow:hidden; border-radius:8px;">
											<img src="<?php echo esc_url($thumb_url); ?>" style="width:100%; height:100%; object-fit:cover;" alt="<?php echo esc_attr(get_the_title()); ?>">
										</a>
										<div>
											<h4 style="font-size:12px; margin:0 0 2px 0; line-height:1.3; font-weight:600;">
												<a href="<?php the_permalink(); ?>" style="color:#333; text-decoration:none;">
													<?php echo wp_trim_words(get_the_title(), 9999, '...'); ?>
												</a>
											</h4>
											<span style="font-size:11px; color:#888;">
												<?php echo get_the_date('d/m/Y'); ?>
											</span>
										</div>
									</div>
									<?php 
								endwhile;
								wp_reset_postdata();
							endif;
							?>
						</div>

						<!-- ĐOẠN ẢNH HIỂN THỊ 100% TRỌN VẸN KHÔNG BỊ ĂN MẤT CHI TIẾT -->
						<div class="bottom-news-image" style="margin-top: 10px; padding-top: 8px; border-top: 1px dashed #eee;">
							<a href="#" style="display:block;">
								<img src="https://gobike.demoweb360.top/wp-content/uploads/2026/08/a1e918d9-98b2-4b9d-a3ee-e8b471e1c491.webp" alt="Banner quảng cáo" style="width:100%; height:auto; display:block; border-radius:8px;">
							</a>
						</div>

					</div>
				</div>



				<div class="menu-mobile">
						<?php if ( is_active_sidebar( 'menu-mobile' ) ) : ?>
						<?php dynamic_sidebar( 'menu-mobile' ); ?>
						<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
	
		<?php while ( have_posts() ) : the_post(); ?>
			<?php the_content(); ?>
		<?php endwhile; // end of the loop. ?>
		
</div>

<?php do_action( 'flatsome_after_page' ); ?>

<?php get_footer(); ?>
