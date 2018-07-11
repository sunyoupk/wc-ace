<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 11/07/2018
 * Time: 6:43 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_Ace_Admin_Assets
 *
 */
class WC_Ace_Admin_Assets {

	/**
	 * WC_Ace_Admin_Assets constructor.
	 *
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
//		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Define admin styles.
	 */
	public function admin_styles() {

		global $wp_scripts;

		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		// Register WooCommerce admin styles.
		wp_register_style( 'woocommerce_admin_menu_styles', WC()->plugin_url() . '/assets/css/menu.css', array(), WC_VERSION );
		wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
		wp_register_style( 'jquery-ui-style', WC()->plugin_url() . '/assets/css/jquery-ui/jquery-ui.min.css', array(), WC_VERSION );

		// Sitewide menu CSS.
		wp_enqueue_style( 'woocommerce_admin_menu_styles' );

		// Applying the WooCommerce admin style.
		if ( in_array( $screen_id, wc_ace_get_screen_ids() ) ) {
			wp_enqueue_style( 'woocommerce_admin_styles' );
			wp_enqueue_style( 'jquery-ui-style' );
			wp_enqueue_style( 'wp-color-picker' );
		}
	}

}

return new WC_Ace_Admin_Assets();
