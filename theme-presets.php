<?php
// make sure to not include translations
$args['presets']['default'] = array(
	'title' => 'Default',
	'demo' => 'http://demo.mythemeshop.com/fashionblog/',
	'thumbnail' => get_template_directory_uri().'/options/demo-importer/demo-files/default/thumb.jpg', // could use external url, to minimize theme zip size
	'menus' => array( 'primary-menu' => 'menu2' ), // menu location slug => Demo menu name
	'options' => array( 'show_on_front' => 'posts', 'posts_per_page' => 9 ),
);


global $mts_presets;
$mts_presets = $args['presets'];
