<?php
/*
Plugin Name: wc-ace
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0
Author: Sunyoup Kim
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define WC_PLUGIN_FILE.
if ( ! defined( 'WC_ACE_PLUGIN_FILE' ) ) {
	define( 'WC_ACE_PLUGIN_FILE', __FILE__ );
}

// Include the main WooCommerce class.
if ( ! class_exists( 'WC_Ace' ) ) {
	include_once dirname( __FILE__ ) . '/includes/class-wc-ace.php';
}

/**
 * Main instance of wc-ace.
 *
 * @since  1.0
 * @return WC_Ace
 */
function wc_ace() {
	return WC_Ace::instance();
}

// Global for backwards compatibility.
$GLOBALS['wc_ace'] = wc_ace();
