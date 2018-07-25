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
/**
 * Get wc-ace temeplate.
 *
 * @param        $template_name
 * @param array  $args
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