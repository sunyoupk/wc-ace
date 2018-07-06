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
class WC_Ace_Post_Types {

	/**
	 * Init post types
	 */
	public static function init() {
		self::register_support_posttypes();
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
//		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
//		add_action( 'init', array( __CLASS__, 'register_post_status' ), 9 );
//		add_action( 'wc_ace_after_register_post_type', array( __CLASS__, 'maybe_flush_rewrite_rules' ) );
//		add_action( 'wc_ace_flush_rewrite_rules', array( __CLASS__, 'flush_rewrite_rules' ) );
	}

	/**
	 * Support posttype file load.
	 */
	public static function register_support_posttypes() {
		$include_base_path = untrailingslashit( plugin_dir_path( WC_ACE_PLUGIN_FILE ) ) . '/includes/';
		foreach ( glob( $include_base_path . 'supports/class-wc-ace-posttype-*.php' ) as $filename ) {
			if ( file_exists( $filename ) ) {
				require_once $filename;
			}
		}
	}

	/**
	 * Register Core post types.
	 */
	public static function register_post_types() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Support custom post types.
		do_action( 'wc_ace_register_support_post_type' );
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
WC_Ace_Post_Types::init();
