<?php
/**
 * Created by PhpStorm.
 * User: pm
 * Date: 25/06/2018
 * Time: 4:10 PM
 */

/**
 * Class WC_Ace_Install
 *
 * @package wc_ace\includes
 */
class WC_Ace_Install {

	/**
	 * Init for setup.
	 */
	public static function init() {
	}

	/**
	 * Install plugin.
	 *
	 * @see WooCommerce/class-woocommerce.php
	 */
	public static function install() {
		if ( ! is_blog_installed() ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'wc_ace_installing' ) ) {
			return;
		}

		// If we made it till here nothing is running yet, lets set the transient now.
		set_transient( 'wc_ace_installing', 'yes', MINUTE_IN_SECONDS * 10 );
		wc_maybe_define_constant( 'WC_ACE_INSTALLING', true );

		self::prepare_post_types();

		delete_transient( 'wc_ace_installing' );
	}

	/**
	 * Setup custom post type.
	 */
	private static function prepare_post_types() {
		WC_Ace_Post_Types::register_post_types();
		WC_Ace_Post_Types::register_taxonomies();
//		ace()->query->init_query_vars();
	}

}

WC_Ace_Install::init();
