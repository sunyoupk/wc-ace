<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 24/07/2018
 * Time: 1:47 PM
 */

defined( 'ABSPATH' ) || exit;

/**
 * Retrieve page ids - used for gift. returns -1 if no page is found.
 *
 * @param string $page Page slug.
 * @return int
 */
function wc_ace_get_page_id( $page ) {
	$page = apply_filters( 'wc_ace_get_' . $page . '_page_id', get_option( 'wc_ace_' . $page . '_page_id' ) );

	return $page ? absint( $page ) : -1;
}


/**
 * @param      $page
 * @param null $fallback
 *
 * @return mixed|void
 */
function wc_ace_get_page_permalink( $page, $fallback = null ) {
	$page_id   = wc_ace_get_page_id( $page );
	$permalink = 0 < $page_id ? get_permalink( $page_id ) : '';

	if ( ! $permalink ) {
		$permalink = is_null( $fallback ) ? get_home_url() : $fallback;
	}

	return apply_filters( 'wc_ace_get_' . $page . '_page_permalink', $permalink );
}
