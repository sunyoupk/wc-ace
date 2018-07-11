<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 11/07/2018
 * Time: 6:54 PM
 */

/**
 * Filter hook Admin Functions.
 */
add_filter( 'wc_ace_screen_ids', 'wc_ace_musicsource_get_screen_ids' );

/**
 * Get Musicsource Admin pages.
 *
 * @param $screen_ids
 *
 * @return mixed
 */
function wc_ace_musicsource_get_screen_ids( $screen_ids ) {
	$musicsource_admin_pages = array(
		'edit-musicsource',
		'musicsource',
		'edit-musicsource_genre',
		'edit-musicsource_type',
		'edit-musicsource_license',
		'edit-musicsource_tag',
	);

	if ( ! is_array( $screen_ids ) ) {
		$screen_ids = array();
	}

	foreach ( $musicsource_admin_pages as $page ) {
		$screen_ids[] = $page;
	}

	return $screen_ids;
}