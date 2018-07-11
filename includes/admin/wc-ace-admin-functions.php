<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 11/07/2018
 * Time: 6:48 PM
 */

/**
 * Admin Core functions.
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require WC_ACE_ABSPATH . 'includes/admin/wc-ace-admin-musicsource-functions.php';

/**
 * Get all wc-ace managed page.
 *
 * @return mixed
 */
function wc_ace_get_screen_ids() {
	$wc_ace_screen_id = sanitize_title( __( 'WooCommerce Ace', 'wc-ace' ) );
	$screen_ids       = array(
		'toplevel_page_' . $wc_ace_screen_id,
	);

	return apply_filters( 'wc_ace_screen_ids', $screen_ids );
}