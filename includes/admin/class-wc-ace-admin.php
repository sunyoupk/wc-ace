<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 11/07/2018
 * Time: 6:17 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_Ace_Admin
 *
 */
class WC_Ace_Admin {

	/**
	 * Admin Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
	}


	/**
	 * Include admin files.
	 */
	public function includes() {
		include_once dirname( __FILE__ ) . '/wc-ace-admin-functions.php';
		include_once dirname( __FILE__ ) . '/class-wc-ace-admin-assets.php';
	}

}

return new WC_Ace_Admin();
