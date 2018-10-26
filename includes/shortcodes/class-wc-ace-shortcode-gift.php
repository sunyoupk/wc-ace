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

	/**
	 * @param $atts
	 *
	 * @return string
	 */
	public static function get( $atts ) {
		return WC_Ace_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	/**
	 * @param $atts
	 */
	public static function output( $atts ) {
		$atts = shortcode_atts( array(), $atts, 'wc_ace_gift' );

		//$nonce_value = wc_get_var( $_REQUEST['wc-ace-gift-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.
		$order_id = get_query_var( 'page' );
		$order_id = empty( $order_id ) ? 0 : ltrim( wc_clean( wp_unslash( $order_id ) ), '#' );

		// The gift recipient is an unsubscribed user(just one time user).
		$recipient_id = wc()->session->get_customer_id();
		// error_log( print_r( wc()->session->get_customer_id(), true ) );
		// Check transient order_id and session->customer_id
		$recipient_auth_transient = get_transient( 'gift_recipient_auth_'.$order_id );
		error_log( 'Gift order id => ' . $order_id );
		error_log( 'Recipent customer id => ' . $recipient_id );
		error_log( 'Transient value => '. $recipient_auth_transient );

		$is_validate_recipient = $recipient_auth_transient === $recipient_id;

		$order = wc_get_order( $order_id );
		if ( $order && $order->get_id() ) {
			if ( $is_validate_recipient ) {
				do_action( 'wc_ace_gift', $order->get_id() );
				wc_ace_get_template(
					'gift/gift.php', array(
						'order' => $order,
					)
				);
				return;

			} else {
//			wc_add_notice( __( 'Please authentication to your gift.', 'wc-ace' ), 'error' );
				wc_ace_get_template(
					'gift/recipient-check.php', array(
						'order_id' => $order,
					)
				);
				return;
			}

		} else {
			wc_add_notice( __( 'Sorry, the order could not be found. Please contact us if you are having difficulty finding your order details.', 'woocommerce' ), 'error' );
		}

		wc_print_notices();
	}
}
