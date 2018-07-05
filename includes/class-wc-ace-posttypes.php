<?php
/**
 * Created by PhpStorm.
 * User: pm
 * Date: 26/06/2018
 * Time: 6:55 AM
 */

/**
 * Class WC_Ace_Posttypes
 *
 * @package wc_ace\includes
 */
class WC_Ace_Posttypes {

	/**
	 * Init post types
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_status' ), 9 );
		add_action( 'wc_ace_after_register_post_type', array( __CLASS__, 'maybe_flush_rewrite_rules' ) );
		add_action( 'wc_ace_flush_rewrite_rules', array( __CLASS__, 'flush_rewrite_rules' ) );
	}

	/**
	 * Register Core post types.
	 */
	public static function register_post_types() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Support custom post types.
		error_log( 'core register_post_type' );
		do_action( 'wc_ace_support_post_type' );
	}

	/**
	 * Register Core taxonomies.
	 */
	public static function register_taxonomies() {
		do_action( 'wc_ace_support_taxonomies' );
	}

	/**
	 * Register Core post status.
	 */
	public static function register_post_status() {
		do_action( 'wc_ace_support_post_status' );
	}

	/**
	 *
	 * @see WooCommerce/class-woocommerce.php
	 */
	public static function maybe_flush_rewrite_rules() {
		if ( 'yes' === get_option( 'wc_ace_queue_flush_rewrite_rules' ) ) {
			update_option( 'wc_ace_queue_flush_rewrite_rules', 'no' );
			self::flush_rewrite_rules();
		}
	}

	/**
	 *
	 * @see WooCommerce/class-woocommerce.php
	 */
	public static function flush_rewrite_rules() {
		flush_rewrite_rules();
	}

}
//
//WC_Ace_Posttypes::init();
