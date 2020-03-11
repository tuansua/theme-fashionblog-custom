<?php $options = get_option('fashionblog'); ?>
<?php get_header(); ?>
<div id="page">
	<?php if (is_home() && !is_paged()) { ?>
		<?php if( $options['mts_featured_slider'] == '1') { ?>
			<div class="slider-container">
				<div class="flex-container">
					<div class="flexslider">
						<ul class="slides">
						<?php $my_query = new WP_Query('cat='.$options['mts_featured_slider_cat'].'&posts_per_page=3'); while ($my_query->have_posts()) : $my_query->the_post(); $image_id = get_post_thumbnail_id(); $image_url = wp_get_attachment_image_src($image_id,'slider'); $image_url = $image_url[0]; ?>
							<li data-thumb="<?php echo $image_url; ?>">
								<a href="<?php the_permalink() ?>">
									<?php the_post_thumbnail('slider',array('title' => '')); ?>
									<p class="flex-caption">
										<span class="sliderAuthor"><span>By:</span> <?php the_author(); ?></span>
										<span class="title slidertitle"><?php the_title(); ?></span>
										<span class="slidertext"><?php echo excerpt(20); ?></span>
										<span class="sliderReadMore"><?php _e('Continue Reading ','mythemeshop'); ?>&rarr;</span>
									</p>
								</a>
							</li>
					   <?php endwhile; ?>
					   </ul>
					</div>
				</div>
			</div>
		<?php } ?> 
	<?php } ?>
	<div class="content">
		<article class="article">
			<div id="content_box">
				<?php 
					$j = 0;
					if (have_posts()) : while (have_posts()) : the_post(); 
				?>
					<div class="post excerpt <?php echo (++$j % 3 == 0) ? 'last' : ''; ?>">
						<header>
							<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="nofollow" id="featured-thumbnail">
								<div class="category-head"><?php $category = get_the_category(); if (isset($category[0]))echo $category[0]->cat_name; ?></div>
								<?php if ( has_post_thumbnail() ) { ?> 
									<?php echo '<div class="featured">'; the_post_thumbnail('featured',array('title' => '')); echo '</div>'; ?>
								<?php } else { ?>
									<div class="featured-thumbnail">
									<img width="300" height="210" src="<?php echo get_template_directory_uri(); ?>/images/nothumb.png" class="attachment-featured wp-post-image" alt="<?php the_title(); ?>">
									</div>
								<?php } ?>
							</a>
							<h2 class="title">
								<a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="bookmark"><?php the_title(); ?></a>
							</h2>
							<?php if($options['mts_headline_meta'] == '1') { ?>
								<div class="post-info">
								<?php the_author_posts_link(); ?> | <?php the_time('d/m'); ?>
								</div>
							<?php } ?>
						</header><!--.header-->
						<div class="post-content image-caption-format-1">
							<?php echo excerpt(30);?>
						</div>
					</div><!--.post excerpt-->
				<?php endwhile; else: ?>
					<div class="post excerpt">
						<div class="no-results">
							<p><strong><?php _e('There has been an error.', 'mythemeshop'); ?></strong></p>
							<p><?php _e('We apologize for any inconvenience, please hit back on your browser or use the search form below.', 'mythemeshop'); ?></p>
							<?php get_search_form(); ?>
						</div><!--noResults-->
					</div>
				<?php endif; ?>
				<?php if ($options['mts_pagenavigation'] == '1') {?>
					<?php pagination();?>
				<?php } else { ?>
					<div class="pnavigation2">
						<div class="nav-previous"><?php next_posts_link( __( '&larr; '.'Older posts', 'mythemeshop' ) ); ?></div>
						<div class="nav-next"><?php previous_posts_link( __( 'Newer posts'.' &rarr;', 'mythemeshop' ) ); ?></div>
					</div>
				<?php } ?>		
			</div>
		</article>
<?php get_footer(); ?>