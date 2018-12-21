<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 20/07/2018
 * Time: 5:50 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require WC_ACE_ABSPATH . 'includes/wc-ace-conditional-functions.php';
require WC_ACE_ABSPATH . 'includes/wc-ace-page-functions.php';


add_filter( 'wc_ace_gift_shipping_address_fields', 'wc_ace_gift_shipping_address_fields' );

//add_filter( 'woocommerce_billing_fields', 'wc_ace_billing_fields', 10, 2 );
//add_filter( 'woocommerce_shipping_fields', 'wc_ace_shipping_fields', 10, 2 );

/**
 * Get wc-ace temeplate.
 *
 * @param        $template_name
 * @param array $args
 * @param string $template_path
 * @param string $default_path
 */
function wc_ace_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args ); // @codingStandardsIgnoreLine
	}

	$located = wc_ace_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		/* translators: %s template */
		wc_doing_it_wrong( __FUNCTION__, sprintf( __( '%s does not exist.', 'woocommerce' ), '<code>' . $located . '</code>' ), '2.1' );

		return;
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$located = apply_filters( 'wc_ace_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'wc_ace_before_template_part', $template_name, $template_path, $located, $args );

	include $located;

	do_action( 'wc_ace_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 * yourtheme/$template_path/$template_name
 * yourtheme/$template_name
 * $default_path/$template_name
 *
 * @see wc_locate_template()
 *
 * @param        $template_name
 * @param string $template_path
 * @param string $default_path
 *
 * @return mixed|void
 */
function wc_ace_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = wc_ace()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = wc_ace()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);

	// Get default template/.
	if ( ! $template || WC_ACE_TEMPLATE_DEBUG_MODE ) {
		$template = $default_path . $template_name;
	}

	return apply_filters( 'wc_ace_locate_template', $template, $template_name, $template_path );
}

/**
 * Gets the url to the gift page.
 *
 * @return mixed|void
 */
function wc_ace_get_gift_url() {
	$gift_url = wc_ace_get_page_permalink( 'gift' );
	if ( $gift_url ) {
		// Force SSL if needed.
		if ( is_ssl() || 'yes' === get_option( 'woocommerce_force_ssl_checkout' ) ) {
			$gift_url = str_replace( 'http:', 'https:', $gift_url );
		}
	}

	return apply_filters( 'wc_ace_get_ace_url', $gift_url );
}

/**
 *
 * Gift page address filed customization.
 *
 * @param $fields
 *
 * @return mixed
 */
function wc_ace_gift_shipping_address_fields( $fields ) {
	unset( $fields['shipping']['shipping_last_name'] );
	unset( $fields['shipping']['shipping_company'] );
	unset( $fields['shipping']['shipping_country'] );
	unset( $fields['shipping']['shipping_state'] );
	unset( $fields['shipping']['shipping_city'] );

	$fields['shipping']['shipping_address_1']['required'] = 1;
	$fields['shipping']['shipping_address_2']['required'] = 0;
	$fields['shipping']['shipping_postcode']['required']  = 0;

	return $fields;
}

if ( ! function_exists( 'wc_ace_billing_fields' ) ) {
	function wc_ace_billing_fields( $fields, $country ) {
		$fields['billing_address_1']['label'] = '<input type="button" id="billing_postcode_search" data-type="billing" value="주소검색" class="button btn-search-postcode" style="height: 40px;">';

		return $fields;
	}
}

if ( ! function_exists( 'wc_ace_gift_statuses' ) ) {
	function wc_ace_gift_statuses() {
		$gift_statuses = array(
			'wc-gift-addressing' => array(
				'label'                     => __( '선물 배송주소 입력 중', 'wc-ace' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( '선물 배송주소 입력 중 <span class="count">(%s)</span>', '선물 배송주소 입력 중 <span class="count">(%s)</span>' )
			),
			'wc-gift-requested'  => array(
				'label'                     => __( '선물 배송요청', 'wc-ace' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( '선물 배송요청 <span class="count">(%s)</span>', '선물 배송요청 <span class="count">(%s)</span>' )
			)
		);

		return $gift_statuses;
	}
}

if ( ! function_exists( 'wc_ace_gift_message_api' ) ) {
	function wc_ace_gift_message_api() {
		return array(
			'direct' => array(
				'input_id'    => 'shipping_address_method_direct',
				'input_value' => 'direct',
				'label'       => '주소 직접입력',
				'description' => '선물로 보낼 주소를 직접 입력합니다.',
			),
			'email'  => array(
				'input_id'    => 'shipping_address_method_email',
				'input_value' => 'email',
				'label'       => '주소 입력폼 전송(EMail)',
				'description' => '주문확정을 하시면 받는 분이 주소를 직접 입력하도록 입력 화면의 URL을 EMail로 전송합니다.',
			),
			'kakao'  => array(
				'input_id'    => 'shipping_address_method_kakao',
				'input_value' => 'kakao',
				'label'       => '주소 입력폼 전송(kakao talk)',
				'description' => '주문확정을 하시면 받는 분이 주소를 직접 입력하도록 입력 화면의 URL을 kakao talk로 전송합니다.',
			),
			'sms'    => array(
				'input_id'    => 'shipping_address_method_sms',
				'input_value' => 'sms',
				'label'       => '주소 입력폼 전송(SMS)',
				'description' => '주문확정을 하시면 받는 분이 주소를 직접 입력하도록 입력 화면의 URL을 SMS로 전송합니다.',
			),
		);
	}
}


