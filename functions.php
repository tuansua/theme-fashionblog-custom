<?php
define( 'MTS_THEME_NAME', 'fashionblog' );
define( 'MTS_THEME_VERSION', '1.0.11' );
require_once( dirname( __FILE__ ) . '/theme-options.php' );
include("functions/tinymce/tinymce.php");

if ( ! isset( $content_width ) ) $content_width = 960;

/*-----------------------------------------------------------------------------------*/
/*	Load Translation Text Domain
/*-----------------------------------------------------------------------------------*/

load_theme_textdomain( 'mythemeshop', get_template_directory().'/lang' );

if ( function_exists('add_theme_support') ) add_theme_support('automatic-feed-links');

/*-----------------------------------------------------------------------------------*/
/*	Post Thumbnail Support
/*-----------------------------------------------------------------------------------*/
	if ( function_exists( 'add_theme_support' ) ) { 
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 300, 210, true );
	add_image_size( 'featured', 300, 210, true ); //featured
	add_image_size( 'related', 190, 140, true ); //related
	add_image_size( 'slider', 940, 500, true ); //slider
	add_image_size( 'widgetthumb', 55, 55, true ); //widget
	}

/*-----------------------------------------------------------------------------------*/
/*	Enable Widgetized sidebar
/*-----------------------------------------------------------------------------------*/
	if ( function_exists('register_sidebar') )
	// Sidebar Widget
	register_sidebar(array(
		'name'=>'Sidebar',
		'id'=>'sidebar-1',
		'before_widget' => '<li id="%2$s" class="widget widget-sidebar">',
		'after_widget' => '</li>',
		'before_title' => '<h3><span>',
		'after_title' => '</span></h3>',
	));
/*-----------------------------------------------------------------------------------*/
/*	Load Widgets & Shortcodes
/*-----------------------------------------------------------------------------------*/

// Add the 125x125 Ad Block Custom Widget
include("functions/widget-ad125.php");

// Add the 125x125 Ad Block Custom Widget
include("functions/widget-ad300.php");

// Add the 125x125 Ad Block Custom Widget
include("functions/widget-tabs.php");

// Add the Latest Tweets Custom Widget
include("functions/widget-tweets.php");

// Add the Theme Shortcodes
include("functions/theme-shortcodes.php");

// Add RecentPosts Widget
include("functions/widget-recentposts.php");

// Add PopularPosts Widget
include("functions/widget-popular.php");

// Add Facebook Like box Widget
include("functions/widget-fblikebox.php");

// Add Subscribe Widget
include("functions/widget-subscribe.php");

// Add Social Profile Widget
include("functions/widget-social.php");

// Add Category Posts Widget
include("functions/widget-catposts.php");

// Add Welcome message
include("functions/welcome-message.php");

// Theme Functions
include("functions/theme-actions.php");

// TGM Plugin Activation.
include_once( "functions/plugin-activation.php" );

/*-----------------------------------------------------------------------------------*/
/*	Filters customize wp_title
/*-----------------------------------------------------------------------------------*/
// Filter the page title wp_title() in header.php
	if ( ! function_exists('mythemeshop_page_title' ) ) {
		function mythemeshop_page_title( $title ) { 
			$the_page_title = $title;
			if( ! $the_page_title ){
				$the_page_title = get_bloginfo("name");
			}else{
				$the_page_title = $the_page_title;
			}
			return $the_page_title;
		} 
		add_filter('wp_title', 'mythemeshop_page_title');
	}
/*-----------------------------------------------------------------------------------*/
/*	Filters that allow shortcodes in Text Widgets
/*-----------------------------------------------------------------------------------*/

add_filter('widget_text', 'shortcode_unautop');
add_filter('widget_text', 'do_shortcode');
add_filter('the_content_rss', 'do_shortcode');

/*-----------------------------------------------------------------------------------*/
/*	Register Footer widgets
/*-----------------------------------------------------------------------------------*/
if (function_exists('register_sidebar')) {
	$sidebars = array(1, 2, 3, 4);
	foreach($sidebars as $number) {
	register_sidebar(array(
		'name' => 'Footer ' . $number,
		'id' => 'footer-' . $number,
		'before_widget' => '<div class="widget">',
		'after_widget' => '</div>',
		'before_title' => '<h3>',
		'after_title' => '</h3>'
	));
	}
}
function widgetized_footer() {
?>
		<div class="f-widget f-widget-1">
			<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer 1') ) : ?>
			<?php endif; ?>
		</div>
		<div class="f-widget f-widget-2">
			<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer 2') ) : ?>
			<?php endif; ?>
		</div>
		<div class="f-widget f-widget-3">
			<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer 3') ) : ?>
			<?php endif; ?>
		</div>
		<div class="f-widget last">
			<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Footer 4') ) : ?>
			<?php endif; ?>
		</div>
<?php
}

/*-----------------------------------------------------------------------------------*/
/*	Custom Comments template
/*-----------------------------------------------------------------------------------*/
function mytheme_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	<div id="comment-<?php comment_ID(); ?>" style="position:relative;">
		<div class="commentArrow"></div>
		<div class="commentLeft">
			<?php echo get_avatar( $comment->comment_author_email, 75 ); ?>	
			<div class="reply">
			<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
			</div>
		</div>
		<div class="commentRight">
			<div class="comment-author vcard">
			<?php printf(__('<span class="fn">%s</span>', 'mythemeshop'), get_comment_author_link()) ?> 
			<?php $options = get_option('fashionblog'); if($options['mts_comment_date'] == '1') { ?>
				<time><?php comment_date(); ?></time>
			<?php } ?>
			</div>
			<?php if ($comment->comment_approved == '0') : ?>
			<em><?php _e('Your comment is awaiting moderation.', 'mythemeshop') ?></em>
			<br />
			<?php endif; ?>
			<div class="comment-meta commentmetadata">
			<?php edit_comment_link(__('(Edit)', 'mythemeshop'),'  ','') ?></div>
			<?php comment_text() ?>	
		</div>
	</div>
<?php
        }
/*-----------------------------------------------------------------------------------*/
/*	Custom Menu Support
/*-----------------------------------------------------------------------------------*/
	add_theme_support( 'menus' );
	if ( function_exists( 'register_nav_menus' ) ) {
	  	register_nav_menus(
	  		array(
	  		  'primary-menu' => 'Primary Menu'
	  		)
	  	);
	}

/*-----------------------------------------------------------------------------------*/
/*	excerpt
/*-----------------------------------------------------------------------------------*/
function excerpt($limit) {
  $excerpt = explode(' ', get_the_excerpt(), $limit);
  if (count($excerpt)>=$limit) {
    array_pop($excerpt);
    $excerpt = implode(" ",$excerpt);
  } else {
    $excerpt = implode(" ",$excerpt);
  }
  $excerpt = preg_replace('`[[^]]*]`','',$excerpt);
  return $excerpt;
}

/*-----------------------------------------------------------------------------------*/
/* nofollow to next/previous links
/*-----------------------------------------------------------------------------------*/
function pagination_add_nofollow($content) {
    return 'rel="nofollow"';
}
add_filter('next_posts_link_attributes', 'pagination_add_nofollow' );
add_filter('previous_posts_link_attributes', 'pagination_add_nofollow' );

/*-----------------------------------------------------------------------------------*/
/* Nofollow to category links
/*-----------------------------------------------------------------------------------*/
add_filter( 'the_category', 'add_nofollow_cat' ); 
function add_nofollow_cat( $text ) {
$text = str_replace('rel="category tag"', 'rel="nofollow"', $text); return $text;
}

/*-----------------------------------------------------------------------------------*/	
/* nofollow post author link
/*-----------------------------------------------------------------------------------*/
add_filter('the_author_posts_link', 'mts_nofollow_the_author_posts_link');
function mts_nofollow_the_author_posts_link ($link) {
return str_replace('<a href=', '<a rel="nofollow" href=',$link); 
}

/*-----------------------------------------------------------------------------------*/
/* removes detailed login error information for security
/*-----------------------------------------------------------------------------------*/
	add_filter('login_errors',create_function('$a', "return null;"));
	
/*-----------------------------------------------------------------------------------*/
/* removes the WordPress version from your header for security
/*-----------------------------------------------------------------------------------*/
	function wb_remove_version() {
		return '<!--Theme by MyThemeShop.com-->';
	}
	add_filter('the_generator', 'wb_remove_version');
	
/*-----------------------------------------------------------------------------------*/
/* Removes Trackbacks from the comment count
/*-----------------------------------------------------------------------------------*/
function mts_comment_count( $count ) {
	if ( ! is_admin() ) {
		global $id;
		$comments = get_comments( 'status=approve&post_id=' . $id );
		$comments_by_type = separate_comments( $comments );
		return count( $comments_by_type['comment'] );
	} else {
		return $count;
	}
}
add_filter( 'get_comments_number', 'mts_comment_count', 0 );
	
/*-----------------------------------------------------------------------------------*/
/* category id in body and post class
/*-----------------------------------------------------------------------------------*/
	function category_id_class($classes) {
		if ( is_single() ) {
			global $post;
			foreach((get_the_category($post->ID)) as $category) $classes [] = 'cat-' . $category->cat_ID . '-id';
		}
		return $classes;
	}
	add_filter('post_class', 'category_id_class');
	add_filter('body_class', 'category_id_class');

/*-----------------------------------------------------------------------------------*/
/* adds a class to the post if there is a thumbnail
/*-----------------------------------------------------------------------------------*/
	function has_thumb_class($classes) {
		global $post;
		if( has_post_thumbnail($post->ID) ) { $classes[] = 'has_thumb'; }
			return $classes;
	}
	add_filter('post_class', 'has_thumb_class');

/*-----------------------------------------------------------------------------------*/	
/* Breadcrumb
/*-----------------------------------------------------------------------------------*/
function the_breadcrumb() {
	echo '<a href="';
	echo home_url();
	echo '" rel="nofollow">Home';
	echo "</a>";
		if (is_category() || is_single()) {
			echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
			the_category(' &bull; ');
				if (is_single()) {
					echo " &nbsp;&nbsp;&#187;&nbsp;&nbsp; ";
					the_title();
				}
        } elseif (is_page()) {
            echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
            echo the_title();
		} elseif (is_search()) {
            echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;Kết quả tìm kiếm cho... ";
			echo '"<em>';
			echo the_search_query();
			echo '</em>"';
        }
    }

/*-----------------------------------------------------------------------------------*/	
/* Pagination
/*-----------------------------------------------------------------------------------*/
function pagination($pages = '', $range = 3)
{ $showitems = ($range * 3)+1;
 global $paged; if(empty($paged)) $paged = 1;
 if($pages == '') {
 global $wp_query; $pages = $wp_query->max_num_pages; if(!$pages)
 { $pages = 1; } }
 if(1 != $pages)
 { echo "<div class='pagination'><ul>";
 if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<li><a rel='nofollow' href='".get_pagenum_link(1)."'>&laquo; First</a></li>";
 if($paged > 1 && $showitems < $pages) echo "<li><a rel='nofollow' href='".get_pagenum_link($paged - 1)."' class='inactive'>&lsaquo; Previous</a></li>";
 for ($i=1; $i <= $pages; $i++)
 { if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
 { echo ($paged == $i)? "<li class='current'><span class='currenttext'>".$i."</span></li>":"<li><a rel='nofollow' href='".get_pagenum_link($i)."' class='inactive'>".$i."</a></li>";
 } } if ($paged < $pages && $showitems < $pages) echo "<li><a rel='nofollow' href='".get_pagenum_link($paged + 1)."' class='inactive'>Next &rsaquo;</a></li>";
 if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a rel='nofollow' class='inactive' href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
 echo "</ul></div>"; }}

/*-----------------------------------------------------------------------------------*/
/* Redirect feed to feedburner
/*-----------------------------------------------------------------------------------*/
$options = get_option('fashionblog');
if ( $options['mts_feedburner'] != '') {
function mts_rss_feed_redirect() {
    $options = get_option('fashionblog');

    global $feed;
    $new_feed = $options['mts_feedburner'];

    if (!is_feed()) {
            return;
    }
    if (preg_match('/feedburner/i', $_SERVER['HTTP_USER_AGENT'])){
            return;
    }

    if ($feed != 'comments-rss2') {
            if (function_exists('status_header')) status_header( 302 );
            header("Location:" . $new_feed);
            header("HTTP/1.1 302 Temporary Redirect");
            exit();
    }
}
add_action('template_redirect', 'mts_rss_feed_redirect');
}

register_sidebar(array(
	'name' => 'Footer Last',
	'id' => 'footer-last',
	'before_widget' => '<div class="widget">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));
add_action( 'rest_api_init', function() {
header( 'Access-Control-Allow-Origin: *' );
header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
header( 'Access-Control-Allow-Credentials: true' );
return $value;
}, 15 );
add_action( 'rest_api_init', 'add_thumbnail_to_JSON' );
function add_thumbnail_to_JSON() {
//Add featured image
register_rest_field( 
    'post', // Where to add the field (Here, blog posts. Could be an array)
    'featured_image_src', // Name of new field (You can call this anything)
    array(
        'get_callback'    => 'get_image_src',
        'update_callback' => null,
        'schema'          => null,
         )
    );
}

function get_image_src( $object, $field_name, $request ) {
  $feat_img_array = wp_get_attachment_image_src(
    $object['featured_media'], // Image attachment ID
    'featured',  // Size.  Ex. "thumbnail", "large", "full", etc..
    true // Whether the image should be treated as an icon.
  );
  return $feat_img_array[0];
}

function tp_custom_logo() { ?>
    <style type="text/css">
        .login  {
            background-image: url(https://24hdev.com/background-login/background-login-24hdev.jpg);
            background-repeat: no-repeat;
            background-size: cover;
        }

        #login h1 a {
            background-image: url(https://24hdev.com/wp-content/uploads/2019/09/logo.png);
            background-size: 280px 80px;
            width: 280px;
            height: 80px;
        }
    </style>

<?php }
add_action('login_enqueue_scripts', 'tp_custom_logo');

add_filter( 'login_headerurl', 'pridio_login_headerurl');
function pridio_login_headerurl()
{
    return '24hdev.com';
}

add_filter('wp_handle_upload_prefilter', 'ts_validate_file_upload');
function ts_validate_file_upload($file) {
    // get imagetype of upload
    $type_img = $file['type'];

    // set allowed imagetype
    $type_jpeg = 'image/jpeg';
    $type_png = 'image/png';
    $type_jpg = 'image/jpg';

    if ($type_img == $type_jpg || $type_img == $type_png || $type_img == $type_jpeg) {

        // get filesize of upload
        $size = $file['size'];
        $size = $size / 1024; // Calculate down to KB

        // set sizelimit
        $limit = 150; // Your Filesize in KB

        if ( $size > $limit ) { // check if the image is small enough
            $file['error'] = 'Hình ảnh có kích thước quá lớn. Kích thước tối đa '.$limit.'KB. Vui lòng tối ưu tại https://compressor.io';
        }

        return $file;
    } else {

        return $file;
    }
}

?>
