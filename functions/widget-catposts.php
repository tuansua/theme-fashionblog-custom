<?php
/*-----------------------------------------------------------------------------------

	Plugin Name: MyThemeShop Category Posts

-----------------------------------------------------------------------------------*/

class CategoryPosts extends WP_Widget {

function __construct() {
	parent::__construct('categoryposts', $name='MyThemeShop: Category Posts');
}

function widget($args, $instance) {
	global $post;
	$post_old = $post; // Save the post object.
	
	extract( $args );
	
	// If not title, use the name of the category.
	if( !$instance["title"] && !empty($instance["cat"])) {
		$category_info = get_category($instance["cat"]);
		$instance["title"] = $category_info->name;
  }

  $valid_sort_orders = array('date', 'title', 'comment_count', 'rand');
  if ( isset($instance['sort_by']) && in_array($instance['sort_by'], $valid_sort_orders) ) {
    $sort_by = $instance['sort_by'];
    $sort_order = (bool) $instance['asc_sort_order'] ? 'ASC' : 'DESC';
  } else {
    // by default, display latest first
    $sort_by = 'date';
    $sort_order = 'DESC';
  }
$categ = isset( $instance["cat"] ) ? $instance["cat"] : '';
	// Get array of post info.
  $cat_posts = new WP_Query(
    "showposts=" . $instance["num"] . 
    "&cat=" . $categ .
    "&orderby=" . $sort_by .
    "&order=" . $sort_order
  );

	echo $before_widget;
	
	// Widget title
	echo $before_title;
	if( isset($instance["title_link"]) && $instance["title_link"] )
		echo '<a href="' . get_category_link($instance["cat"]) . '">' . $instance["title"] . '</a>';
	else
		echo $instance["title"];
	echo $after_title;

	// Post list
	echo "<ul class=\"category-posts\">\n";
	
	while ( $cat_posts->have_posts() )
	{
		$cat_posts->the_post();
	?>
		<li class="cat-post-item">
		
			<?php
				if (
					current_theme_supports("post-thumbnails") &&
					$instance["thumb"]
				) :
			?>
				<?php if(has_post_thumbnail()): ?>
				<?php $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'widgetthumb'); ?>
<a href='<?php the_permalink(); ?>' title='<?php the_title_attribute(); ?>'><img src="<?php echo $image[0]; ?>" alt="<?php the_title(); ?>"  class="wp-post-image" /></a>
				<?php else: ?>
				<a href='<?php the_permalink(); ?>'><img src="<?php echo get_template_directory_uri(); ?>/images/smallthumb.png" alt="<?php the_title(); ?>" class="wp-post-image" /></a>
				<?php endif; ?>
			<?php endif; ?>
			
			<a class="plink" href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
			<p class="post-date">
			<?php if ( $instance['date'] ) : ?>
			<?php the_time("j M Y"); ?>
			<?php endif; ?>
			
			
			<?php if ( $instance['comment_num'] ) : ?>
			(<?php comments_number(); ?>)
			<?php endif; ?>
			</p>
		</li>
	<?php
	}
	
	echo "</ul>\n";
	
	echo $after_widget;

	
	$post = $post_old; // Restore the post object.
}

/**
 * The configuration form.
 */
function form($instance) {

	$defaults = array('title' => '','cat' => '','num' => 5,'sort_by' => 'date','asc_sort_order' => '','asc_sort_order' => '','title_link' => '','comment_num'=>'','date' => '','thumb' => '' );
	$instance = wp_parse_args((array) $instance, $defaults);
?>
		<p>
			<label for="<?php echo $this->get_field_id("title"); ?>">
				<?php _e( 'Title', 'mythemeshop'); ?>:
				<input class="widefat" id="<?php echo $this->get_field_id("title"); ?>" name="<?php echo $this->get_field_name("title"); ?>" type="text" value="<?php echo esc_attr($instance["title"]); ?>" />
			</label>
		</p>
		
		<p>
			<label>
				<?php _e( 'Category', 'mythemeshop'); ?>:
				<?php wp_dropdown_categories( array( 'name' => $this->get_field_name("cat"), 'selected' => $instance["cat"] ) ); ?>
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id("num"); ?>">
				<?php _e('Number of posts to show', 'mythemeshop'); ?>:
				<input style="text-align: center;" id="<?php echo $this->get_field_id("num"); ?>" name="<?php echo $this->get_field_name("num"); ?>" type="text" value="<?php echo absint($instance["num"]); ?>" size='3' />
			</label>
    </p>

    <p>
			<label for="<?php echo $this->get_field_id("sort_by"); ?>">
        <?php _e('Sort by', 'mythemeshop'); ?>:
        <select id="<?php echo $this->get_field_id("sort_by"); ?>" name="<?php echo $this->get_field_name("sort_by"); ?>">
          <option value="date"<?php selected( $instance["sort_by"], "date" ); ?>>Date</option>
          <option value="title"<?php selected( $instance["sort_by"], "title" ); ?>>Title</option>
          <option value="comment_count"<?php selected( $instance["sort_by"], "comment_count" ); ?>>Number of comments</option>
          <option value="rand"<?php selected( $instance["sort_by"], "rand" ); ?>>Random</option>
        </select>
			</label>
    </p>
		
		<p>
			<label for="<?php echo $this->get_field_id("asc_sort_order"); ?>">
        <input type="checkbox" class="checkbox" 
          id="<?php echo $this->get_field_id("asc_sort_order"); ?>" 
          name="<?php echo $this->get_field_name("asc_sort_order"); ?>"
          <?php checked( (bool) $instance["asc_sort_order"], true ); ?> />
				<?php _e( 'Reverse sort order (ascending)', 'mythemeshop'); ?>
			</label>
    </p>

		<p>
			<label for="<?php echo $this->get_field_id("title_link"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("title_link"); ?>" name="<?php echo $this->get_field_name("title_link"); ?>"<?php checked( (bool) $instance["title_link"], true ); ?> />
				<?php _e( 'Make widget title link', 'mythemeshop'); ?>
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id("comment_num"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("comment_num"); ?>" name="<?php echo $this->get_field_name("comment_num"); ?>"<?php checked( (bool) $instance["comment_num"], true ); ?> />
				<?php _e( 'Show number of comments', 'mythemeshop'); ?>
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id("date"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("date"); ?>" name="<?php echo $this->get_field_name("date"); ?>"<?php checked( (bool) $instance["date"], true ); ?> />
				<?php _e( 'Show post date', 'mythemeshop'); ?>
			</label>
		</p>
		
		<?php if ( function_exists('the_post_thumbnail') && current_theme_supports("post-thumbnails") ) : ?>
		<p>
			<label for="<?php echo $this->get_field_id("thumb"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("thumb"); ?>" name="<?php echo $this->get_field_name("thumb"); ?>"<?php checked( (bool) $instance["thumb"], true ); ?> />
				<?php _e( 'Show post thumbnail', 'mythemeshop'); ?>
			</label>
		</p>
		<?php endif; ?>

<?php

}

function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		
		$instance['title'] = $new_instance['title'];
		$instance['cat'] = $new_instance['cat'];
		$instance['num'] = $new_instance['num'];
		$instance['sort_by'] = $new_instance['sort_by'];
		$instance['asc_sort_order'] = $new_instance['asc_sort_order'];
		$instance['title_link'] = $new_instance['title_link'];
		$instance['comment_num'] = $new_instance['comment_num'];
		$instance['date'] = $new_instance['date'];
		$instance['thumb'] = $new_instance['thumb'];
		
		return $instance;
	}

}

add_action( 'widgets_init', create_function('', 'return register_widget("CategoryPosts");') );

?>