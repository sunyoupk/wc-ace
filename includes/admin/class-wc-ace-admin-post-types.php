<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 12/07/2018
 * Time: 2:41 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_Ace_Admin_Post_Types
 */
class WC_Ace_Admin_Post_Types {

	/**
	 * WC_Ace_Admin_Post_Types constructor.
	 */
	public function __construct() {
		include_once dirname( __FILE__ ) . '/class-wc-ace-admin-meta-boxes.php';
		include_once dirname( __FILE__ ) . '/class-wc-ace-admin-post-type-musicsource.php';
	}
}

new WC_Ace_Admin_Post_Types();
