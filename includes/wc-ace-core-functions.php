<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 20/07/2018
 * Time: 5:50 PM
 */


/**
 * @param $template_name
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
 * @param $template_name
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