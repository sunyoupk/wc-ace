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
 * Class WC_Ace_Admin_Musicsource_Assets
 *
 */
class WC_Ace_Admin_Musicsource_Assets {

	/**
	 * WC_Ace_Admin_Musicsource_Assets constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Define admin scripts.
	 */
	public function admin_scripts() {
		global $wp_scripts;

		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';
		$suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'wc_ace_musicsource_admin', wc_ace()->plugin_url() . '/public/js/admin/wc-ace-musicsource-admin.js', array(
			'jquery',
			'jquery-blockui',
			'jquery-ui-datepicker',
			'jquery-ui-sortable',
			'jquery-ui-widget',
			'jquery-ui-core',
			'jquery-tiptip',
			'accounting',
			'round',
			'wc-enhanced-select',
			'plupload-all',
			'stupidtable',
			'jquery-tiptip',
			'wc-backbone-modal',
		), WC_ACE_VERSION );

		if ( in_array( $screen_id, wc_ace_musicsource_get_screen_ids() ) ) {
			wp_enqueue_script( 'wc_ace_musicsource_admin' );
		}

		$params = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'strings'  => array(
				'import_products' => __( 'Import', 'woocommerce' ),
				'export_products' => __( 'Export', 'woocommerce' ),
			),
			'nonces'   => array(
				'gateway_toggle' => wp_create_nonce( 'woocommerce-toggle-payment-gateway-enabled' ),
			),
		);
		wp_localize_script( 'wc_ace_musicsource_admin', 'wc_ace_musicsource_admin', $params );
	}

	/**
	 * Define admin styles.
	 */
	public function admin_styles() {
		global $wp_scripts;
		$screen         = get_current_screen();
		$screen_id      = $screen ? $screen->id : '';
		$suffix    = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register admin styles.
		wp_register_style( 'wc_ace_musicsource_admin_style', wc_ace()->plugin_url() . '/public/css/admin_musicsource' . $suffix . '.css', array(), WC_ACE_VERSION );
//		wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
//		wp_register_style( 'jquery-ui-style', WC()->plugin_url() . '/assets/css/jquery-ui/jquery-ui.min.css', array(), WC_VERSION );


		if ( in_array( $screen_id, wc_ace_musicsource_get_screen_ids() ) ) {
			error_log( wc_ace()->plugin_url() . '/public/css/admin_musicsource' . $suffix . '.css' );
			wp_enqueue_style( 'wc_ace_musicsource_admin_style' );
		}

	}

}

return new WC_Ace_Admin_Musicsource_Assets();
