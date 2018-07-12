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
 * @param array $screen_ids
 *
 * @return array
 */
function wc_ace_musicsource_get_screen_ids( $screen_ids = array() ) {
	$musicsource_admin_pages = array(
		'edit-musicsource',
		'musicsource',
		'edit-musicsource_genre',
		'edit-musicsource_type',
		'edit-musicsource_license',
		'edit-musicsource_tag',
	);

	foreach ( $musicsource_admin_pages as $page ) {
		$screen_ids[] = $page;
	}

	return $screen_ids;
}