<?php
/**
 * Created by PhpStorm.
 * User: pm
 * Date: 26/06/2018
 * Time: 6:55 AM
 */

/**
 * Class WC_Ace_PostTypes
 *
 * @package wc_ace\includes
 */
class WC_Ace_Post_Types {

	/**
	 * Init post types
	 */
	public static function init() {
		self::register_support_posttypes();
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 10 );
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 20 );
		add_action( 'init', array( __CLASS__, 'register_post_status' ), 30 );
		//		add_action( 'wc_ace_after_register_post_type', array( __CLASS__, 'maybe_flush_rewrite_rules' ) );
		add_action( 'wc_ace_flush_rewrite_rules', array( __CLASS__, 'flush_rewrite_rules' ) );
	}

	/**
	 * Support posttype file load.
	 */
	public static function register_support_posttypes() {
		$include_base_path = untrailingslashit( plugin_dir_path( WC_ACE_PLUGIN_FILE ) ) . '/includes/';
		foreach ( glob( $include_base_path . 'class-wc-ace-post-type-*.php' ) as $filename ) {
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
		// Register attachement category(taxonomy).
		register_taxonomy( 'media_cat', 'attachment', array(
				'hierarchical'      => false,
				'labels'            => array(
					'name'              => '카테고리',
					'singular_name'     => '카테고리',
					'search_items'      => '카테고리 검색',
					'all_items'         => '모든 카테고리',
					'parent_item'       => '상위 카테고리',
					'parent_item_colon' => '상위 카테고리:',
					'edit_item'         => '카테고리 편집',
					'update_item'       => '카테고리 수정',
					'add_new_item'      => '신규 카테고리',
					'new_item_name'     => '신규 카테고리 이름',
					'menu_name'         => '카테고리',
				),
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'media_cat' ),
				'show_admin_column' => true,
			)
		);


		// Support custom taxonomies.
		do_action( 'wc_ace_register_support_taxonomies' );
	}

	/**
	 * Register Core post status.
	 */
	public static function register_post_status() {
		foreach ( wc_ace_gift_statuses() as $order_status => $values ) {
			register_post_status( $order_status, $values );
		}

		do_action( 'wc_ace_register_support_post_status' );
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
