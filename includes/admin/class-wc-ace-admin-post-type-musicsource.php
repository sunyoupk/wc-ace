<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 13/07/2018
 * Time: 11:26 AM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_Ace_Admin_Post_Type_Musicsource
 */
class WC_Ace_Admin_Post_Type_Musicsource {

	/**
	 * WC_Ace_Admin_Post_Type_Musicsource constructor.
	 */
	public function __construct() {
		include_once dirname( __FILE__ ) . '/class-wc-ace-admin-musicsource-meta-boxes.php';
	}

}

new WC_Ace_Admin_Post_Type_Musicsource();
