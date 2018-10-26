<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 24/07/2018
 * Time: 3:10 PM
 */

/**
 * Class WC_Ace_Ajax
 */
class WC_Ace_Ajax {

	/**
	 * Hook in ajax handlers.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
		add_action( 'template_redirect', array( __CLASS__, 'do_wc_ace_ajax' ), 0 );
		self::add_ajax_events();
	}

	/**
	 * Get WC_Ace Ajax Endpoint.
	 *
	 * @param  string $request Optional.
	 *
	 * @return string
	 */
	public static function get_endpoint( $request = '' ) {
		return esc_url_raw( apply_filters( 'wc_ace_ajax_get_endpoint', add_query_arg( 'wc-ace-ajax', $request, remove_query_arg( array(
			'remove_item',
			'add-to-cart',
			'added-to-cart'
		), home_url( '/', 'relative' ) ) ), $request ) );
	}

	/**
	 * Set WC_Ace AJAX constant and headers.
	 */
	public static function define_ajax() {
		if ( ! empty( $_GET['wc-ace-ajax'] ) ) {
			wc_maybe_define_constant( 'DOING_AJAX', true );
			wc_maybe_define_constant( 'WC_ACE_DOING_AJAX', true );
			if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
				@ini_set( 'display_errors', 0 ); // Turn off display_errors during AJAX events to prevent malformed JSON.
			}
			$GLOBALS['wpdb']->hide_errors();
		}
	}

	/**
	 * Send headers for WC Ajax Requests.
	 *
	 * @since 2.5.0
	 */
	private static function wc_ace_ajax_headers() {
		send_origin_headers();
		@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		@header( 'X-Robots-Tag: noindex' );
		send_nosniff_header();
		wc_nocache_headers();
		status_header( 200 );
	}

	/**
	 * Check for WC Ajax request and fire action.
	 */
	public static function do_wc_ace_ajax() {
		global $wp_query;

		if ( ! empty( $_GET['wc-ace-ajax'] ) ) {
			$wp_query->set( 'wc-ace-ajax', sanitize_text_field( wp_unslash( $_GET['wc-ace-ajax'] ) ) );
		}

		$action = $wp_query->get( 'wc-ace-ajax' );

		if ( $action ) {
			self::wc_ace_ajax_headers();
			$action = sanitize_text_field( $action );
			do_action( 'wc_ace_ajax_' . $action );
			wp_die();
		}
	}


	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax).
	 */
	public static function add_ajax_events() {
		// wc_ace_EVENT => nopriv.
		$ajax_events = array(
			'gift_update_shipping_address' => true,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_wc_ace_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_wc_ace_' . $ajax_event, array( __CLASS__, $ajax_event ) );

				// WC_Ace AJAX can be used for frontend ajax requests.
				add_action( 'wc_ace_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Shipping address update(from gift).
	 */
	public static function gift_update_shipping_address() {
		check_ajax_referer( 'gift-update-shipping-address', 'security' );

		wc_maybe_define_constant( 'WC_ACE_GIFT', true );

		try {
			$order_id = absint( $_POST['order_id'] );
			$order    = wc_get_order( $order_id );

			if ( ! $order ) {
				throw new exception( __( 'Invalid order', 'wc-ace' ) );
			}

			$required_fields = apply_filters( 'wc_ace_gift_shipping_address_required_fields', array(
				'shipping_first_name' => __( '이름', 'wc-ace' ),
				'shipping_phone'      => __( '전화번호', 'wc-ace' ),
				'shipping_address_1'  => __( '주소', 'wc-ace' ),
			) );

			// Must check fields from Gift page.
			if ( absint( $_POST['is_gift'] ) != 1 && in_array( $_POST['shipping_address_method'], array(
					'sms',
					'kakao'
				) ) ) {
				unset( $required_fields['shipping_address_1'] );
			}

			$messages = '';
			ob_start();
			foreach ( $required_fields as $field_key => $field_name ) {
				if ( empty( $_POST[ $field_key ] ) ) {
					wc_add_notice( sprintf( __( '%s is a required field.', 'woocommerce' ), '<strong>' . esc_html( $field_name ) . '</strong>' ), 'error' );
				}
			}
			wc_print_notices();
			$messages = ob_get_clean();

			if ( empty( $messages ) ) {
				$order->set_props( array(
					'shipping_postcode'   => isset( $_POST['shipping_postcode'] ) ? wp_unslash( $_POST['shipping_postcode'] ) : null,
					'shipping_address_1'  => isset( $_POST['shipping_address_1'] ) ? wp_unslash( $_POST['shipping_address_1'] ) : null,
					'shipping_address_2'  => isset( $_POST['shipping_address_2'] ) ? wp_unslash( $_POST['shipping_address_2'] ) : null,
					'shipping_first_name' => isset( $_POST['shipping_first_name'] ) ? wp_unslash( $_POST['shipping_first_name'] ) : null,
				) );
				$order->update_meta_data( '_shipping_phone', isset( $_POST['shipping_phone'] ) ? wp_unslash( $_POST['shipping_phone'] ) : null );
				$order->update_meta_data( '_shipping_address_method', isset( $_POST['shipping_address_method'] ) ? wp_unslash( $_POST['shipping_address_method'] ) : null );
				$order->save();
			}

			wp_send_json(
				array(
					'result'   => empty( $messages ) ? 'success' : 'failure',
					'messages' => $messages,
					'reload'   => empty( $messages ) ? true : false,
				)
			);

		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}

	/**
	 * Authenticate gift recipient identification.
	 */
	public static function gift_recipient_check() {
		check_ajax_referer( 'gift-recipient-auth-check-nonce', 'security' );

		try {
			$order_id = absint( $_POST['order_id'] );
			$order    = wc_get_order( $order_id );

			if ( ! $order ) {
				throw new exception( __( 'Invalid order', 'wc-ace' ) );
			}

			$required_fields = array(
				// todo namespace 휴대전화번호
				'recipient_phone' => __( '휴대전화번호', 'wc-ace' ),
			);

			$messages = '';
			ob_start();
			foreach ( $required_fields as $field_key => $field_name ) {
				if ( empty( $_POST[ $field_key ] ) ) {
					wc_add_notice( sprintf( __( '%s is a required field.', 'woocommerce' ), '<strong>' . esc_html( $field_name ) . '</strong>' ), 'error' );
				}
			}
			wc_print_notices();
			$messages = ob_get_clean();

			if ( empty( $messages ) ) {
				$order_shipping_phone = esc_html( $order->get_meta( '_shipping_phone' ) );
				$recipient_phone = isset( $_POST['recipient_phone'] ) ? wp_unslash( $_POST['recipient_phone'] ) : null;

			}

			wp_send_json(
				array(
					'result'   => empty( $messages ) ? 'success' : 'failure',
					'messages' => $messages,
					'reload'   => empty( $messages ) ? true : false,
				)
			);

		} catch ( Exception $e ) {
			wp_send_json_error( array( 'error' => $e->getMessage() ) );
		}
	}
}

WC_Ace_Ajax::init();
