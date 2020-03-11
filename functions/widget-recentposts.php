<?php
/*-----------------------------------------------------------------------------------

	Plugin Name: MyThemeShop Recent Posts
	Description: A widget for advanced recent posts.
	Version: 1.0

-----------------------------------------------------------------------------------*/

// Make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
    die('Direct script access not allowed');
}

/**
 * Recent Posts Widget Class
 */
class AdvancedRecentPosts extends WP_Widget {
    
    private $default_config = array(
        'title' => '',
        'count' => 5,
        'include_post_thumbnail' => 'true',
        'include_post_excerpt' => 'false',
        'truncate_post_title' => '',
        'truncate_post_title_type' => 'char',
        'truncate_post_excerpt' => '',
        'truncate_post_excerpt_type' => 'char',
        'truncate_elipsis' => '...',
        'widget_output_template' => '<li>{THUMBNAIL}<a title="{TITLE_RAW}" href="{PERMALINK}">{TITLE}</a>{EXCERPT}</li>', //default format
        'show_expert_options' => 'false'
    );
    
    /** constructor */    
    function __construct() {
        $widget_ops = array(
            'classname'   => 'widget_recent_entries', 
            'description' => __('Includes advanced options.', 'mythemeshop')
        );
        parent::__construct('advanced-recent-posts', __('MyThemeShop: Recent Posts'), $widget_ops);
    }

    /** @see WP_Widget::widget */
    function widget( $args, $instance ) {
        extract( $args );
        echo $before_widget;
        
        $title = apply_filters( 'widget_title', empty($instance['title']) ? 'Recent Posts' : $instance['title'], $instance, $this->id_base);        
        $widget_output_template = (empty($instance['widget_output_template'])) ? $this->default_config['widget_output_template'] : $instance['widget_output_template'];
        echo $before_title . $title . $after_title;
        
        $output = $this->parse_output($instance);
        
        //if the first tag of the widget_output_template is a <li> tag then wrap it in <ul>
        if(stripos(ltrim($widget_output_template), '<li') === 0)
            echo '<ul class="advanced-recent-posts">'.$output.'</ul>';
        else
            echo $output;
        
        echo $after_widget;

    }
    
    function parse_output($instance) {
        
        $output = '';
        
        foreach($this->default_config as $key => $val) {
            $$key = (empty($instance[$key])) ? $val : $instance[$key];
        }
        
        $query_args = array(
            'post_type' => 'post',
            'post_status' => 'publish', 
            'posts_per_page' => $count, 
            'orderby' => 'date', 
            'order' => 'DESC'
        );
        
        $the_query = new WP_Query( $query_args );
        
        if( $the_query->have_posts() ) {         
            //Deal with custom meta tags, e.g. [META[key]]
            $meta_matches = array();
            preg_match_all('/\{META\[(.*?)\]\}/', $widget_output_template, $meta_matches);
            
            //check if custom ellipsis has been defined, use strpos before preg_match since it is a lot faster
            $truncate_elipsis_template = '';
            
            if(preg_match('/\{ELLIPSIS\}(.*?)\{\/ELLIPSIS\}/', $widget_output_template, $ellipsis_match) > 0) {
                $truncate_elipsis_template = $ellipsis_match[1];
            }
            
            while ( $the_query->have_posts() ) { $the_query->the_post();
                
                $ID = get_the_ID();
                $image_id = get_post_thumbnail_id();
$image_url = wp_get_attachment_image_src($image_id,'widgetthumb');
$image_url = $image_url[0];
					if(has_post_thumbnail()) {
						$POST_THUMBNAIL = "<a href=\"".get_permalink($ID)."\" title=\"".get_the_title($ID)."\" ><img src=\"".$image_url."\" class=\"wp-post-image\"></a>";
					} else {
						$POST_THUMBNAIL = "<a href=\"".get_permalink($ID)."\" title=\"".get_the_title($ID)."\" ><img src=\"".get_template_directory_uri()."/images/smallthumb.png\" class=\"wp-post-image\"></a>";
					}
                
                $POST_TITLE_RAW = strip_tags(get_the_title($ID));
                if(empty($truncate_post_title))
                    $POST_TITLE = $POST_TITLE_RAW;
                else {
                    if($truncate_post_title_type == "word")
                        $POST_TITLE = $this->_truncate_words($POST_TITLE_RAW, $truncate_post_title, $truncate_elipsis);
                    else
                        $POST_TITLE = $this->_truncate_chars($POST_TITLE_RAW, $truncate_post_title, $truncate_elipsis);
                }
                
                $widget_ouput_template_params = array(
                    '{ID}' => $ID,
                    '{THUMBNAIL}' => $POST_THUMBNAIL,
                    '{TITLE_RAW}' => $POST_TITLE_RAW,
                    '{TITLE}' => $POST_TITLE,
                    '{PERMALINK}' => get_permalink($ID),
                    '{AUTHOR}' => get_the_author(),
                    '{AUTHOR_LINK}' => get_the_author_link(),
                    '{AUTHOR_AVATAR}' => ((strpos($widget_output_template, '{AUTHOR_AVATAR}') !== FALSE) ? get_avatar(get_the_author_meta('user_email')) : ""),
                    '{COMMENT_COUNT}' => ((strpos($widget_output_template, '{COMMENT_COUNT}') !== FALSE) ? get_comments_number() : "") //Only load comment count if necessary since it might cause more db queries
                );
                
                //Deal with meta fields
                foreach($meta_matches[0] as $key => $meta_match) {
                    if(!empty($meta_matches[1][$key]))
                        $widget_ouput_template_params[$meta_match] = get_post_meta($ID, $meta_matches[1][$key], true);
                    else
                        $widget_ouput_template_params[$meta_match] = '';
                }
                
                //Deal with {ELLIPSIS}{/ELLIPSIS} tags, we parse it with the template tags, so you can use these tags in the excerpt
				$truncate_elipsis_excerpt = $truncate_elipsis;
                if(!empty($truncate_elipsis_template)) {
                    $truncate_elipsis_excerpt = str_replace(array_keys($widget_ouput_template_params), array_values($widget_ouput_template_params), $truncate_elipsis_template);
                    $widget_output_template = preg_replace('/\{ELLIPSIS\}(.*?)\{\/ELLIPSIS\}/', '', $widget_output_template); //remove {ELLIPSIS}{/ELLIPSIS} tags from widget_output_template
                }
                
                //Deal with post excerpt
                if($include_post_excerpt == "false") {
                    $POST_EXCERPT_RAW = $POST_EXCERPT = '';
                } else {
                    $POST_EXCERPT_RAW = $this->_custom_trim_excerpt();
                    if(empty($truncate_post_excerpt))
                        $POST_EXCERPT = $POST_EXCERPT_RAW;
                    else
                        $POST_EXCERPT = $this->_custom_trim_excerpt($truncate_post_excerpt, $truncate_elipsis_excerpt, $truncate_post_excerpt_type);
                }
                
                $widget_ouput_template_params['{EXCERPT_RAW}'] = $POST_EXCERPT_RAW;
                $widget_ouput_template_params['{EXCERPT}'] = $POST_EXCERPT;
                
                //Deal with embedded php code, only evan if php tags exist and current user is admin
                $widget_output_template_eval = $widget_output_template;
                
                if(preg_match("/<\?(.*?)\?>/", $widget_output_template) > 0) {
                    ob_start();
                    $eval_result = eval("?>".$widget_output_template);
                    $widget_output_template_eval = ob_get_clean();
                }
                
                $output .= str_replace(array_keys($widget_ouput_template_params), array_values($widget_ouput_template_params), $widget_output_template_eval);
                
            } //end while
        }
        
        wp_reset_postdata();
        
        return $output;
    }
    
    /* Replacement to WordPress overly simplistic excerpt trimming function */
    function _custom_trim_excerpt($excerpt_length = NULL, $excerpt_more = NULL, $truncate_type = "word") {
        
        global $post;
        
        //If post is password protected then return empty string
        if(!empty($post->post_password))
            return '';
        
        $text = $post->post_excerpt;
        if($text == '') {
            $text = $post->post_content;
            
            //Deal with more tag, if it exists then only grab the content before it
            if ( preg_match('/<!--more(.*?)?-->/', $text, $matches) ) {
                $text = explode($matches[0], $text, 2);
                $text = $text[0];
            }
        }
        
        $text = strip_shortcodes($text);
        
        $text = str_replace(']]>', ']]&gt;', $text);
        //$text = str_replace('\]\]\>', ']]&gt;', $text);
        $text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text); // Strip out javascript including tag contents
        $text = preg_replace('@<style[^>]*?>.*?</style>@siU', '', $text); // Strip style tags including tag contents
        $text = preg_replace('@<![\s\S]*?--[ \t\n\r]*>@', '', $text); // Strip multi-line comments including CDATA since this is included in char count
        $text = strip_tags($text);
        
        $excerpt_length = ($excerpt_length === NULL) ? apply_filters('excerpt_length', 55) : $excerpt_length; 
        $excerpt_more = ($excerpt_more === NULL) ? apply_filters('excerpt_more', ' ' . '[...]') : $excerpt_more; 
        
        if($truncate_type == "word") {
            $text = $this->_truncate_words($text, $excerpt_length, $excerpt_more);
        } else {
            $text = $this->_truncate_chars($text, $excerpt_length, $excerpt_more);
        }
        
        // Lets just apply the default filters but not the_content filter so plugins that have added to it don't modify the content
        $text = wptexturize($text);
        $text = convert_smilies($text);
        $text = convert_chars($text);
        $text = wpautop($text);
        //$text = shortcode_unautop($text); //shortcodes have been stripped so it's not needed
        
        return $text;
    }
    
    function _truncate_chars($text, $limit, $ellipsis = '...') {
        if($limit) {
            if( strlen($text) > $limit )
                $text = trim(substr($text, 0, $limit)).$ellipsis;
        }
        return $text;
    }
    
    function _truncate_words($text, $limit, $ellipsis = '...') {
        if($limit) {
            $words = preg_split("/[\n\r\t ]+/", $text, $limit + 1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_OFFSET_CAPTURE);
            if (count($words) > $limit) {
                end($words); //ignore last element since it contains the rest of the string after applying limit
                $last_word = prev($words);
                
                $text =  substr($text, 0, $last_word[1] + strlen($last_word[0])) . $ellipsis;
            }
        }
        return $text;
    }
    
    /** @see WP_Widget::update */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['count'] = strip_tags($new_instance['count']);
        
        $instance['include_post_thumbnail'] = strip_tags( $new_instance[ 'include_post_thumbnail' ] );
        if( empty($instance['include_post_thumbnail']) ) $instance['include_post_thumbnail'] = 'false';
         
        $instance['include_post_excerpt'] = strip_tags( $new_instance[ 'include_post_excerpt' ] );
        if( empty($instance['include_post_excerpt']) ) $instance['include_post_excerpt'] = 'false';
        
        $instance['truncate_post_title'] = strip_tags( $new_instance[ 'truncate_post_title' ] );
        $instance['truncate_post_title_type'] = strip_tags( $new_instance[ 'truncate_post_title_type' ] );
        $instance['truncate_post_excerpt'] = strip_tags( $new_instance[ 'truncate_post_excerpt' ] );
        $instance['truncate_post_excerpt_type'] = strip_tags( $new_instance[ 'truncate_post_excerpt_type' ] );
        $instance['truncate_elipsis'] = strip_tags( $new_instance[ 'truncate_elipsis' ] );
        $instance['post_thumbnail_width'] = strip_tags( $new_instance[ 'post_thumbnail_width' ] );
        $instance['post_thumbnail_height'] = strip_tags( $new_instance[ 'post_thumbnail_height' ] );
        $instance['widget_output_template'] = $new_instance[ 'widget_output_template' ];
        $instance['show_expert_options'] = strip_tags( $new_instance[ 'show_expert_options' ] );
        
        return $instance;
    }

    /** @see WP_Widget::form */
    function form( $instance ) {
        if ( $instance ) {
            foreach($this->default_config as $key => $val) {
                $$key = esc_attr($instance[$key]);
            }            
        } else {
            /* DEFAULT OPTIONS */
            foreach($this->default_config as $key => $val) {
                $$key = $val;
            }
        }
        ?>
        
        <script type="text/javascript">
            jQuery(document).ready(function($) {    
                $('.arp_show-expert-options').trigger('change');
            });
        </script>
        
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'mythemeshop'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of posts to show:', 'mythemeshop'); ?></label> 
            <input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" size="3" value="<?php echo $count; ?>" />
        </p>
        
        <p>
            
            <input id="<?php echo $this->get_field_id('include_post_excerpt'); ?>" name="<?php echo $this->get_field_name('include_post_excerpt'); ?>" type="checkbox" value="true" class="checkbox" <?php echo ($include_post_excerpt == 'true') ? 'checked="checked"' : '' ?> />
            <label for="<?php echo $this->get_field_id('include_post_excerpt'); ?>"><?php _e('Include post excerpt', 'mythemeshop'); ?></label><br>
            
            <br>
            
            <input class="arp_show-expert-options checkbox" id="<?php echo $this->get_field_name('show_expert_options'); ?>" name="<?php echo $this->get_field_name('show_expert_options'); ?>" type="checkbox" value="true" <?php echo ($show_expert_options == 'true') ? 'checked="checked"' : '' ?> /> 
            <label for="<?php echo $this->get_field_id('show_expert_options'); ?>"><?php _e('Show expert options', 'mythemeshop'); ?></label>
        </p>
        
        <div class="arp_expert-panel" style="display:none; margin-top: 10px">
        
            <p>
                <label for="<?php echo $this->get_field_id('truncate_post_title'); ?>"><?php _e('Limit post title:', 'mythemeshop'); ?></label> 
                <input id="<?php echo $this->get_field_id('truncate_post_title'); ?>" name="<?php echo $this->get_field_name('truncate_post_title'); ?>" type="text" size="2" value="<?php echo $truncate_post_title; ?>" />
                <select id="<?php echo $this->get_field_id('truncate_post_title_type'); ?>" name="<?php echo $this->get_field_name('truncate_post_title_type'); ?>">
                      <option value="char" <?php echo ($truncate_post_title_type == 'char') ? 'selected="selected"' : '' ?>>Chars</option>
                      <option value="word" <?php echo ($truncate_post_title_type == 'word') ? 'selected="selected"' : '' ?>>Words</option>
                </select>
                <br>
                
                <label for="<?php echo $this->get_field_id('truncate_post_excerpt'); ?>"><?php _e('Limit post excerpt:', 'mythemeshop'); ?></label> 
                <input id="<?php echo $this->get_field_id('truncate_post_excerpt'); ?>" name="<?php echo $this->get_field_name('truncate_post_excerpt'); ?>" type="text" size="2" value="<?php echo $truncate_post_excerpt; ?>" />
                <select id="<?php echo $this->get_field_id('truncate_post_excerpt_type'); ?>" name="<?php echo $this->get_field_name('truncate_post_excerpt_type'); ?>">
                      <option value="char" <?php echo ($truncate_post_excerpt_type == 'char') ? 'selected="selected"' : '' ?>>Chars</option>
                      <option value="word" <?php echo ($truncate_post_excerpt_type == 'word') ? 'selected="selected"' : '' ?>>Words</option>
                </select>
                <br>
                
                <label for="<?php echo $this->get_field_id('truncate_elipsis'); ?>"><?php _e('Limit ellipsis:', 'mythemeshop'); ?></label> 
                <input id="<?php echo $this->get_field_id('truncate_elipsis'); ?>" name="<?php echo $this->get_field_name('truncate_elipsis'); ?>" type="text" size="3" value="<?php echo $truncate_elipsis; ?>" /><br>
                
            </p>
            
            
        
        </div>
        
        <?php 
    }
    
    /** Appends the form script tag to the admin head to allow toggling the expert options panel */
    static function arp_form_script($hook_suffix) { 
        if($hook_suffix == 'widgets.php') {
            wp_enqueue_script(
                'arp_widget_script', get_template_directory_uri().'/functions/js/recent-posts.js', array('jquery')
            );
        }
    }

} // class AdvancedRecentPosts

add_action( 'admin_enqueue_scripts', array('AdvancedRecentPosts', 'arp_form_script') );

// register AdvancedRecentPosts widget
add_action( 'widgets_init', create_function( '', 'return register_widget("AdvancedRecentPosts");' ) );