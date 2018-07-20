<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 20/07/2018
 * Time: 5:47 PM
 */

/**
 * Class WC_Ace_Shortcode_Gift
 */
class WC_Ace_Shortcode_Gift {

	public static function get( $atts ) {
		return WC_Ace_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	public static function output( $atts ) {
		$atts        = shortcode_atts( array(), $atts, 'wc_ace_gift' );

		//		$nonce_value = wc_get_var( $_REQUEST['wc-ace-gift-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.
//
//		if ( isset( $_REQUEST['orderid'] ) && wp_verify_nonce( $nonce_value, 'wc-ace-gift' ) ) { // WPCS: input var ok.
//
//			$order_id    = empty( $_REQUEST['orderid'] ) ? 0 : ltrim( wc_clean( wp_unslash( $_REQUEST['orderid'] ) ), '#' ); // WPCS: input var ok.
//			$order_email = empty( $_REQUEST['order_email'] ) ? '' : sanitize_email( wp_unslash( $_REQUEST['order_email'] ) ); // WPCS: input var ok.
//
//			if ( ! $order_id ) {
//				wc_add_notice( __( 'Please enter a valid order ID', 'woocommerce' ), 'error' );
//			} elseif ( ! $order_email ) {
//				wc_add_notice( __( 'Please enter a valid email address', 'woocommerce' ), 'error' );
//			} else {
//				$order = wc_get_order( $order_id );
//
//				if ( $order && $order->get_id() && strtolower( $order->get_billing_email() ) === strtolower( $order_email ) ) {
//					do_action( 'wc_ace_gift', $order->get_id() );
//					wc_ace_get_template(
//						'gift/mygift.php', array(
//							'order' => $order,
//						)
//					);
//
//					return;
//				} else {
//					wc_add_notice( __( 'Sorry, the order could not be found. Please contact us if you are having difficulty finding your order details.', 'woocommerce' ), 'error' );
//				}
//			}
//		}

		wc_print_notices();
		// todo Recipient authentication
		wc_ace_get_template( 'gift/mygift.php' );
	}
}
