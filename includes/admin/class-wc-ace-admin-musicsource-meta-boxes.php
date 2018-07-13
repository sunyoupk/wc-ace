<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 12/07/2018
 * Time: 2:45 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_Ace_Admin_Musicsource_Meta_Boxes
 */
class WC_Ace_Admin_Musicsource_Meta_Boxes {

	/**
	 * WC_Ace_Admin_Musicsource_Meta_Boxes constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
	}

	/**
	 * Add Musicsource Meta boxes.
	 */
	public function add_meta_boxes() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		add_meta_box( 'wc-ace-musicsource-data', __( '음원 정보', 'wc-ace' ), 'WC_Ace_Meta_Box_Musicsource::output_data', 'musicsource', 'normal', 'high' );
		add_meta_box( 'wc-ace-musicsource-actions', __( '음원 상태', 'wc-ace' ), 'WC_Ace_Meta_Box_Musicsource::output_action', 'musicsource', 'side', 'high' );

		add_meta_box( 'wc-ace-musicsource-licenses', __( '음원 라이선스', 'wc-ace' ), 'WC_Ace_Meta_Box_Musicsource::output_license', 'musicsource', 'normal', 'high' );
		add_meta_box( 'wc-ace-musicsource-files', __( '음원 파일', 'wc-ace' ), 'WC_Ace_Meta_Box_Musicsource::output_files', 'musicsource', 'normal', 'high' );
	}

	/**
	 * Remove bloat.
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'submitdiv', 'musicsource', 'side' );
	}

}

new WC_Ace_Admin_Musicsource_Meta_Boxes();
