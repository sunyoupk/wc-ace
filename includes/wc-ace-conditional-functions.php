<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 24/07/2018
 * Time: 1:45 PM
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'is_gift' ) ) {
	/**
	 * Is_gift - Returns true when viewing the gift page.
	 *
	 * @return bool
	 */
	function is_gift() {
		$page_id = wc_ace_get_page_id( 'gift' );

		return ( $page_id && is_page( $page_id ) ) || wc_post_content_has_shortcode( 'wc_ace_gift' ) || apply_filters( 'wc_ace_is_gift', false ) || defined( 'WC_ACE_GIFT' );
	}
}