<?php
/**
 * Created by PhpStorm.
 * User: bangrang
 * Date: 29/10/2018
 * Time: 8:01 PM
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'wc_order_statuses', 'wc_ace_order_and_gift_statuses' );

add_action( 'woocommerce_checkout_update_order_meta', 'wc_ace_checkout_update_order_meta', 10, 2 );

if ( ! function_exists( 'wc_ace_order_and_gift_statuses' ) ) {
	/**
	 * Append gift statuses to Order status.
	 * @param $order_statuses
	 *
	 * @return array
	 */
	function wc_ace_order_and_gift_statuses( $order_statuses ) {
		$new_order_statuses = array();

		foreach ( $order_statuses as $key => $status ) {
			$new_order_statuses[ $key ] = $status;
			if ( 'wc-pending' === $key ) {
				$new_order_statuses['wc-gift-addressing'] = __( '선물 배송주소 입력 중', 'wc-ace' );
				$new_order_statuses['wc-gift-requested'] = __( '선물 배송요청', 'wc-ace' );
			}
		}
		return $new_order_statuses;
	}
}

if ( ! function_exists( 'wc_ace_checkout_update_order_meta' ) ) {
	/**
	 *  Order 메타데이터 생성 후 처리.
	 *
	 * @param $order_id
	 * @param $data
	 */
	function wc_ace_checkout_update_order_meta( $order_id, $data ) {
		if ( isset( $data['ship_to_different_address'] ) && $data['ship_to_different_address'] == '1') {
			update_post_meta( $order_id, '_is_gift', 'yes' );
		} else {
			update_post_meta( $order_id, '_is_gift', 'no' );
		}
	}
}