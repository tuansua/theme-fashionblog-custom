<?php get_header(); ?>
<?php $options = get_option('fashionblog'); ?>
<div id="page" class="single">
	<div class="content">
		<article class="article">
			<div id="content_box" >
				<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
					<div id="post-<?php the_ID(); ?>" <?php post_class('g post'); ?>>
						<div class="single_post">
							<?php if ($options['mts_breadcrumb'] == '1') { ?>
								<div class="breadcrumb"><?php the_breadcrumb(); ?></div>
							<?php } ?>
							<header>
								<h1 class="title single-title"><?php the_title(); ?></h1>
								<?php if($options['mts_headline_meta'] == '1') { ?>
									<span class="post-info single-postmeta"><?php the_author_posts_link(); ?>&nbsp;&nbsp;/&nbsp;&nbsp;<?php the_time('F j, Y'); ?>&nbsp;&nbsp;/&nbsp;&nbsp;<?php echo comments_number();?></span>
								<?php } ?>
							</header><!--.headline_area-->
							<div class="post-single-content box mark-links">
								<?php if ($options['mts_posttop_adcode'] != '') { ?>
									<?php $toptime = $options['mts_posttop_adcode_time']; if (strcmp( date("Y-m-d", strtotime( "-$toptime day")), get_the_time("Y-m-d") ) >= 0) { ?>
										<div class="topad">
											<?php echo $options['mts_posttop_adcode']; ?>
										</div>
									<?php } ?>
								<?php } ?>
									<?php the_content(); ?>
									<?php wp_link_pages('before=<div class="pagination2">&after=</div>'); ?>
								<?php if ($options['mts_postend_adcode'] != '') { ?>
									<?php $endtime = $options['mts_postend_adcode_time']; if (strcmp( date("Y-m-d", strtotime( "-$endtime day")), get_the_time("Y-m-d") ) >= 0) { ?>
										<div class="bottomad">
											<?php echo $options['mts_postend_adcode'];?>
										</div>
									<?php } ?>
								<?php } ?> 
								<?php if($options['mts_social_buttons'] == '1') { ?>
									<div class="shareit">
										<?php if($options['mts_twitter'] == '1') { ?>
												<!-- Twitter -->
												<span class="share-item twitterbtn">
												<a href="https://twitter.com/share" class="twitter-share-button" data-via="<?php echo $options['mts_twitter_username']; ?>">Tweet</a>
												</span>
										<?php } ?>
										<?php if($options['mts_gplus'] == '1') { ?>
												<!-- GPlus -->
												<span class="share-item gplusbtn">
												<g:plusone size="medium"></g:plusone>
												</span>
										<?php } ?>
										<?php if($options['mts_facebook'] == '1') { ?>
												<!-- Facebook -->
												<span class="share-item facebookbtn">
												<div id="fb-root"></div>
												<div class="fb-like" data-send="false" data-layout="button_count" data-width="150" data-show-faces="false"></div>
												</span>
										<?php } ?>
										<?php if($options['mts_linkedin'] == '1') { ?>
												<!--Linkedin -->
												<span class="share-item linkedinbtn">
												<script type="text/javascript" src="http://platform.linkedin.com/in.js"></script><script type="in/share" data-url="<?php the_permalink(); ?>" data-counter="right"></script>
												</span>
										<?php } ?>
										<?php if($options['mts_stumble'] == '1') { ?>
												<!-- Stumble -->
												<span class="share-item stumblebtn">
												<su:badge layout="1"></su:badge>
												<script type="text/javascript"> 
												(function() { 
												var li = document.createElement('script'); li.type = 'text/javascript'; li.async = true; 
												li.src = window.location.protocol + '//platform.stumbleupon.com/1/widgets.js'; 
												var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(li, s); 
												})(); 
												</script>
												</span>
										<?php } ?>
										<?php if($options['mts_pinterest'] == '1') { ?>
												<!-- Pinterest -->
												<span class="share-item pinbtn">
												<a href="http://pinterest.com/pin/create/button/?url=<?php the_permalink(); ?>&media=<?php $thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large' ); echo $thumb['0']; ?>&description=<?php the_title(); ?>" class="pin-it-button" count-layout="horizontal">Pin It</a>
												<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
												</span>
										<?php } ?>
									</div>
								<?php } ?><!--Shareit-->
								<?php if($options['mts_tags'] == '1') { ?>
									<div class="tags"><?php the_tags('<span class="tagtext">Tags:</span>',', ') ?></div>
								<?php } ?>
							</div>
						</div><!--.post-content box mark-links-->
						<?php if($options['mts_related_posts'] == '1') { ?>	
							<?php $categories = get_the_category($post->ID); if ($categories) { $category_ids = array(); foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;
							$args=array(
							'category__in' => $category_ids,
							'post__not_in' => array($post->ID),
							'showposts'=>3, // Number of related posts that will be shown.
							'ignore_sticky_posts'=>1,
							'orderby' => 'rand'
							);
							$my_query = new wp_query( $args );
							if( $my_query->have_posts() ) {
							echo '<div class="related-posts"><div class="postauthor-top"><h3><span>'.__('Related Posts','mythemeshop').'</span></h3></div><ul>';
							$counter = 0;
							while( $my_query->have_posts() ) {
							++$counter;
							if($counter == 3) {
							$postclass = 'last';
							$counter = 0;
							} else { $postclass = ''; }
							$my_query->the_post();?>
							<li class="<?php echo $postclass; ?>">
								<a rel="nofollow" class="relatedthumb" href="<?php the_permalink()?>" rel="bookmark" title="<?php the_title(); ?>">
									<span class="rthumb">
										<?php if(has_post_thumbnail()): ?>
											<?php the_post_thumbnail('related', 'title='); ?>
										<?php else: ?>
											<img src="<?php echo get_template_directory_uri(); ?>/images/relthumb.png" alt="<?php the_title(); ?>"  width='180' height='120' class="wp-post-image" />
										<?php endif; ?>
									</span>
									<span class="relPostTitle"><?php the_title(); ?></span>
								</a>
							</li>
							<?php } echo '</ul></div>'; } } wp_reset_postdata(); ?>
							<!-- .related-posts -->
						<?php }?>  
						<?php if($options['mts_author_box'] == '1') { ?>
							<div class="postauthor">
								<h4><span><?php _e('About Author', 'mythemeshop'); ?></span></h4>
								<?php if(function_exists('get_avatar')) { echo get_avatar( get_the_author_meta('email'), '100' );  } ?>
								<h5><?php the_author_meta( 'nickname' ); ?></h5>
								<p><?php the_author_meta('description') ?></p>
							</div>
						<?php }?>  
					</div><!--.g post-->
				<?php comments_template( '', true ); ?>
				<?php endwhile; /* end loop */ ?>
			</div>
		</article>
		<?php get_sidebar(); ?>
<?php get_footer(); ?>