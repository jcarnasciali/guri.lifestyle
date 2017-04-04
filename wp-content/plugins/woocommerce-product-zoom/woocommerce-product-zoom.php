<?php
/*
 * Plugin Name: WooCommerce Product Zoom
 * Plugin URI: http://the-croc.ru/product/woocommerce-product-zoom
 * Description: Handsome product gallery with asynchronous zoom for desktop and mobiles.
 * Version: 1.0.7
 * Author: WooCommerce
 * Author URI: http://woocommerce.com/
 * Developer: Ildar Akhmetov & Ruslan Askarov
 * Developer URI: http://the-croc.ru/
 * Text Domain: woocommerce-product-zoom
 *
 * Requires at least: 3.8
 * Tested up to: 4.7
 *
 * Copyright: © 2009-2017 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * WC requires at least: 2.1
 * WC tested up to: 2.6
 */

/**
* The main plugin file 
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

#includes 
#can't use autoladers because classes are singletones 
if( !class_exists( 'Mobile_Detect' ) )
	include 'inc/Mobile_Detect.php';
include 'WCProductZoom.php';

/**
 * WC Detection
 *
 * @return boolean
 */
if ( ! function_exists( 'is_woocommerce_active' ) ) {
	function is_woocommerce_active() {
		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}
		
		return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins ) ;
	}
}


/**
 * WooCommerce inactive notice. 
 *
 * @return string
 */
function wcpz_woocommerce_inactive_notice() {
	if ( current_user_can( 'activate_plugins' && is_admin() ) ) {
		echo '<div id="message" class="error"><p>';
		printf( __( '%1$sWooCommerce Product Zoom is inactive%2$s. %3$sWooCommerce plugin %4$s must be active for Product Zoom to work. Please %5$sinstall and activate WooCommerce &raquo;%6$s', 'woocommerce-product-zoom'), '<strong>', '</strong>', '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">', '</a>' );
		echo '</p></div>';
	}
}


add_action( "plugins_loaded", function() {
	if( is_woocommerce_active() )
		$woocommerce_product_zoom = WCProductZoom::instance();
	else
		add_action( "admin_notices", 'wcpz_woocommerce_inactive_notice' );
});

register_activation_hook( __FILE__, array( "WCProductZoom", "wcpz_activation" ) );