<?php
$options = get_option('fashionblog');	

/*------------[ Meta ]-------------*/
if ( ! function_exists( 'mts_meta' ) ) {
	function mts_meta() { 
	global $options
?>
<?php if ($options['mts_favicon'] != '') { ?>
<link rel="icon" href="<?php echo $options['mts_favicon']; ?>" type="image/x-icon" />
<?php } ?>
<!--iOS/android/handheld specific -->	
<link rel="apple-touch-icon" href="apple-touch-icon.png">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<?php }
}

/*------------[ Head ]-------------*/
if ( ! function_exists( 'mts_head' ) ) {
	function mts_head() { 
	global $options
?>
<!--start fonts-->
<?php if ($options['mts_title_font'] == 'Arial') { ?>
<?php } else { ?>
<?php if ($options['mts_title_font'] != '' || $options['mts_google_title_font'] != '') { ?>
<link href="http://fonts.googleapis.com/css?family=<?php if ($options['mts_google_title_font'] != '') { ?><?php echo $options['mts_google_title_font']; ?><?php } else { ?><?php echo $options['mts_title_font']; ?><?php } ?>:400,600,700" rel="stylesheet" type="text/css">
<style type="text/css">
.title, .total-comments, .footer-widgets h3, .widget h3, .fn, .reply, .contact-submit, #cancel-comment-reply-link, .relPostTitle, h1,h2,h3,h4,h5,h6 { font-family: '<?php if ($options['mts_google_title_font'] != '') { ?><?php echo $options['mts_google_title_font']; ?><?php } else { ?><?php echo $options['mts_title_font']; ?><?php } ?>', sans-serif;}
</style>
<?php } ?>
<?php } ?>
<?php if ($options['mts_content_font'] == 'Arial') { ?>
<?php } else { ?>
<?php if ($options['mts_content_font'] != '' || $options['mts_google_content_font'] != '') { ?>
<link href="http://fonts.googleapis.com/css?family=<?php if ($options['mts_google_content_font'] != '') { ?><?php echo $options['mts_google_content_font']; ?><?php } else { ?><?php echo $options['mts_content_font']; ?><?php } ?>:400,400italic,700,700italic" rel="stylesheet" type="text/css">
<style type="text/css">
body {font-family: '<?php if ($options['mts_google_content_font'] != '') { ?><?php echo $options['mts_google_content_font']; ?><?php } else { ?><?php echo $options['mts_content_font']; ?><?php } ?>', sans-serif;}
</style>
<?php } ?>
<?php } ?>
<!--end fonts-->
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/modernizr.min.js"></script>
<script src="<?php echo get_template_directory_uri(); ?>/js/customscript.js" type="text/javascript"></script>
<!--start slider-->
<?php if( $options['mts_featured_slider'] == '1') { ?>
<?php if( is_home() ) { ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/flexslider.css" type="text/css">
<script src="<?php echo get_template_directory_uri(); ?>/js/jquery.flexslider-min.js"></script>
<script type="text/javascript">
    $(window).load(function(){
      $('.flexslider').flexslider({
        animation: "fade",
		pauseOnHover: true,
      });
	  
    });
  </script>
<script type="text/javascript">
    $(window).load(function(){
      $('.carousel').flexslider({
        animation: "slide",
		animationLoop: false,
		itemWidth: 140,
		itemMargin: 15,
		minItems: 2,
		maxItems: 8
      });
	  
    });
  </script>
<?php } ?>
<?php } ?>
<!--end slider-->
<style type="text/css">
<?php if($options['mts_bg_color'] != '') { ?>
body {background-color:<?php echo $options['mts_bg_color']; ?>;}
<?php } ?>
<?php if ($options['mts_bg_pattern_upload'] != '') { ?>
body {background-image: url(<?php echo $options['mts_bg_pattern_upload']; ?>);}
<?php } else { ?>
<?php if($options['mts_bg_pattern'] != '') { ?>
body {background-image:url(<?php echo get_template_directory_uri(); ?>/images/<?php echo $options['mts_bg_pattern']; ?>.png);}
<?php } ?>
<?php } ?>
<?php if ($options['mts_color_scheme'] != '') { ?>
.mts-subscribe input[type="submit"], .related-posts h3 span, .postauthor h4 span, .category-head, .currenttext, .pagination a:hover, .reply, .total-comments, #commentform input#submit, .contact-submit, .tagcloud a, .sbutton, .children .commentArrow, .secondary-navigation a:hover, .secondary-navigation ul ul a:hover, .current-menu-item a, .widget h3 span, .related-posts h3 span, .postauthor h4 span, .total-comments, #respond h4 span {background-color:<?php echo $options['mts_color_scheme']; ?>; }
#header, .post.excerpt, #sidebars .widget, .footer-widgets, .tagcloud a, #tabber, .single_post, .single_page,.ss-full-width, input#author:hover, input#email:hover, input#url:hover, #comment:hover  {border-color:<?php echo $options['mts_color_scheme']; ?>; }
.single_post a, a:hover, #logo a, .textwidget a, #commentform a, .copyrights a:hover, .readMore a, .slidertitle, .widget li a:hover, a, #tabber .inside li div.info .entry-title a:hover {color:<?php echo $options['mts_color_scheme']; ?>; }
#tabber ul.tabs li a.selected { border-top-color:<?php echo $options['mts_color_scheme']; ?>; }
.commentlist .children li, .post-single-content blockquote { border-left-color:<?php echo $options['mts_color_scheme']; ?>; }
<?php } ?>
<?php if($options['mts_floating_social'] == '1') { ?>
.shareit { top: 282px; left: auto; z-index: 0; margin: 0 0 0 -130px; width: 90px; position: fixed; overflow: hidden; padding: 5px; background: white; border: 1px solid #E2E2E2; border-right: 0;}
.share-item {margin: 2px;}
<?php } ?>
<?php if ($options['mts_layout'] == 'sclayout') { ?>
.article { float: right;}
.sidebar.c-4-12 { float: left; }
<?php if($options['mts_floating_social'] == '1') { ?>
.shareit { margin: 0 638px 0; border-left: 0; border-right: 1px solid #E2E2E2; }
<?php } ?>
<?php } ?>
<?php if($options['mts_author_comment'] == '1') { ?>
.bypostauthor {background: #F8F8F8;}
<?php } ?>
<?php echo $options['mts_custom_css']; ?>
</style>
<?php echo $options['mts_header_code']; ?>
<?php }
}

/*------------[ footer ]-------------*/
if ( ! function_exists( 'mts_footer' ) ) {
	function mts_footer() { 
	global $options
?>
<!--Twitter Button Script------>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
<script type="text/javascript">
  (function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/plusone.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
<!--Facebook Like Button Script------>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=136911316406581";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<!--start lightbox-->
<?php if($options['mts_lightbox'] == '1') { ?>
<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/prettyPhoto.css" type="text/css" media="screen" charset="utf-8" />
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.prettyPhoto.js"></script>
<script type="text/javascript">  
jQuery(document).ready(function($) {
$("a[href$='.jpg'], a[href$='.jpeg'], a[href$='.gif'], a[href$='.png']").prettyPhoto({
slideshow: 5000, /* false OR interval time in ms */
autoplay_slideshow: false, /* true/false */
animationSpeed: 'normal', /* fast/slow/normal */
padding: 40, /* padding for each side of the picture */
opacity: 0.35, /* Value betwee 0 and 1 */
showTitle: true, /* true/false */	
social_tools: false
});
})
</script>
<?php } ?>
<!--end lightbox-->
<!--start footer code-->
<?php if ($options['mts_analytics_code'] != '') { ?>
<?php echo $options['mts_analytics_code']; ?>
<?php } ?>
<!--end footer code-->
<?php }
}

/*------------[ Copyrights ]-------------*/
if ( ! function_exists( 'mts_copyrights_credit' ) ) {
	function mts_copyrights_credit() { 
	global $options
?>
<!--start copyrights-->
<div class="row" id="copyright-note">
<div class="top"><?php echo $options['mts_copyrights']; ?> <a href="#top" class="toplink">Back to Top &uarr;</a></div>
</div>
<!--end copyrights-->
<?php }
}

?>