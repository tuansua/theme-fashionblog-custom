<?php $options = get_option('fashionblog'); ?>
	</div><!--#page-->
</div><!--.container-->
</div>
	<footer>
		<div class="container">
			<div class="footer-widgets">
				<?php widgetized_footer(); ?>
			</div><!--.footer-widgets-->
		</div><!--.container-->
		<?php if ( is_active_sidebar( 'footer-last' ) ) : ?>
		<div class="last-footer">
			<?php dynamic_sidebar( 'footer-last' ); ?>
		</div>
		<?php endif; ?>
	</footer><!--footer-->
</div>
<div class="copyrights">
	<?php mts_copyrights_credit(); ?>
</div> 
<?php mts_footer(); ?>
<?php wp_footer(); ?>
</body>
</html>