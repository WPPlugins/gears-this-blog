<?php
/*
Plugin Name: Gears This Blog
Plugin URI: http://www.yriase.fr/
Author: Hugo Giraud
Author URI: http://www.yriase.fr/
Description: Allow users to stock blog content on their computer for offline surf.
Version: 0.3.1
Text Domain: gearsthisblog
*/

/**
 * Plugin (de)activation
 */
function gearsthisblog_load() {
	if ( is_admin() ) {
		require_once( 'inc/admin.php' );
		register_activation_hook( __FILE__, 'gearsthisblog_install' );
	}
}
gearsthisblog_load();

/**
 * Things to run during init hook
 */
function gearsthisblog_init() {
	// always needed for footer link
	require_once( 'inc/page.php' );

	if ( is_admin() ) {
		require_once( 'inc/admin.php' );
		add_action( 'admin_menu', 'gearsthisblog_add_pages' );

		register_widget_control( __('Gears This Blog', 'gearsthisblog' ), 'gearsthisblog_control' );
		register_sidebar_widget( __('Gears This Blog', 'gearsthisblog'), 'widget_gearsthisblog' );
	}
	else {
		add_shortcode( 'gearsthisblog', 'gearsthisblog_shortcode' );
		register_sidebar_widget( __('Gears This Blog', 'gearsthisblog'), 'widget_gearsthisblog' );
	}
}
add_action( 'init', 'gearsthisblog_init' );

/**
 * Create a shortcode
 */
function gearsthisblog_shortcode() {
	$before = '<div class="better-tag-cloud-shortcode" >';
	$after = '</div>';
	$option = get_option( 'gearsthisblog' );
	$config = $option['config'];
	$cloud = gearsthisblog_the_gears( $config );
	return $before . $cloud . $after;
}

