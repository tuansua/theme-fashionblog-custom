<?php
/*-----------------------------------------------------------------------------------

	Plugin Name: MyThemeShop Popular Posts
	Description: A widget for displaying Popular Posts.
	Version: 1.0

-----------------------------------------------------------------------------------*/
add_action('widgets_init', 'mts_pp_load_widgets');

function mts_pp_load_widgets()
{
	register_widget('mts_pp_Widget');
}

function string_limit_words($string, $word_limit)
{
	$words = explode(' ', $string, ($word_limit + 1));
	
	if(count($words) > $word_limit) {
		array_pop($words);
	}
	
	return implode(' ', $words);
}

class mts_pp_Widget extends WP_Widget {
	
	function __construct()
	{
		$widget_ops = array('classname' => 'mts_pp', 'description' => 'Displays most Popular Posts with Thumbnail.');

		$control_ops = array('id_base' => 'mts_pp-widget');

		parent::__construct('mts_pp-widget', 'MyThemeShop: Popular Posts', $widget_ops, $control_ops);
	}
	
	function widget($args, $instance)
	{
		extract($args);

		$posts = $instance['posts'];
		$images = true;
		
		echo $before_widget;
		
		/*if($title) {
			echo $before_title.$title.$after_title;
		}	*/	
		?>
		<!-- BEGIN WIDGET -->
		<div class="pp-wrapper">
			
			<h3><span><?php _e('Popular Posts', 'mythemeshop'); ?></span></h3>
			<ul class="popular-posts">
					<?php
					$popular_posts = new WP_Query('showposts='.$posts.'&orderby=comment_count&order=DESC');
					if($popular_posts->have_posts()): ?>
						<?php while($popular_posts->have_posts()): $popular_posts->the_post(); ?>
						<?php
						if(has_post_format('video') || has_post_format('audio') || has_post_format('gallery')) {
							$icon = '<span class="' . get_post_format(get_the_ID()) . '-icon"></span>';
						} else {
							$icon = '';
						}
						?>
						
						<li>
							<?php if(has_post_thumbnail()): ?>
<?php $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'widgetthumb'); ?>
<a href='<?php the_permalink(); ?>'><img src="<?php echo $image[0]; ?>" alt="<?php the_title(); ?>"  class="wp-post-image" /></a>
<?php else: ?>
<a href='<?php the_permalink(); ?>'><img src="<?php echo get_template_directory_uri(); ?>/images/smallthumb.png" alt="<?php the_title(); ?>"  class="wp-post-image" /></a>
<?php endif; ?><a href='<?php the_permalink(); ?>' title='<?php the_title(); ?>' class="plink"><?php the_title(); ?></a>
<time><?php the_time('F j, Y'); ?></time>
						</li>
					
						<?php endwhile; ?>
					<?php endif; ?>
			</ul>
		</div>
		<!-- END WIDGET -->
		<?php
		echo $after_widget;
	}
	
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		
		$instance['posts'] = $new_instance['posts'];
		$instance['show_images'] = true;
		
		return $instance;
	}

	function form($instance)
	{
		$defaults = array('posts' => 3);
		$instance = wp_parse_args((array) $instance, $defaults); ?>
		<p>
			<label for="<?php echo $this->get_field_id('posts'); ?>">Number of posts:</label>
			<input class="widefat" style="width: 30px;" id="<?php echo $this->get_field_id('posts'); ?>" name="<?php echo $this->get_field_name('posts'); ?>" value="<?php echo $instance['posts']; ?>" />
		</p>
	<?php }
}
?>