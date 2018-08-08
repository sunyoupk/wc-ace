<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 24/07/2018
 * Time: 10:24 AM
 */

/**
 * Class WC_Ace_Frontend_Scripts
 */
class WC_Ace_Frontend_Scripts {

	private static $scripts = array();

	private static $styles = array();

	private static $wp_localize_scripts = array();

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
		add_action( 'wp_print_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
		add_action( 'wp_print_footer_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
	}

	/**
	 * Get styles for the frontend.
	 *
	 * @return array
	 */
	public static function get_styles() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		return apply_filters(
			'wc_ace_enqueue_styles', array(
				'wc-ace-layout'      => array(
					'src'     => self::get_asset_url( 'public/css/wc-ace-layout' . $suffix . '.css' ),
					'deps'    => '',
					'version' => WC_ACE_VERSION,
					'media'   => 'all',
					'has_rtl' => true,
				),
				'wc-ace-smallscreen' => array(
					'src'     => self::get_asset_url( 'public/css/wc-ace-smallscreen' . $suffix . '.css' ),
					'deps'    => 'wc-ace-layout',
					'version' => WC_ACE_VERSION,
					'media'   => 'only screen and (max-width: ' . apply_filters( 'wc_ace_style_smallscreen_breakpoint', '768px' ) . ')',
					'has_rtl' => true,
				),
				'wc-ace-general'     => array(
					'src'     => self::get_asset_url( 'public/css/wc-ace' . $suffix . '.css' ),
					'deps'    => '',
					'version' => WC_ACE_VERSION,
					'media'   => 'all',
					'has_rtl' => true,
				),
			)
		);
	}

	/**
	 * Return asset URL.
	 *
	 * @param string $path Assets path.
	 *
	 * @return string
	 */
	private static function get_asset_url( $path ) {
		return apply_filters( 'wc_ace_get_asset_url', plugins_url( $path, WC_ACE_PLUGIN_FILE ), $path );
	}

	/**
	 * Register a script for use.
	 *
	 * @uses   wp_register_script()
	 *
	 * @param  string   $handle Name of the script. Should be unique.
	 * @param  string   $path Full URL of the script, or path of the script relative to the WordPress root directory.
	 * @param  string[] $deps An array of registered script handles this script depends on.
	 * @param  string   $version String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
	 * @param  boolean  $in_footer Whether to enqueue the script before </body> instead of in the <head>. Default 'false'.
	 */
	private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = WC_ACE_VERSION, $in_footer = true ) {
		self::$scripts[] = $handle;
		wp_register_script( $handle, $path, $deps, $version, $in_footer );
	}

	/**
	 * Register and enqueue a script for use.
	 *
	 * @uses   wp_enqueue_script()
	 * @see    WC_Frontend_Scripts::enqueue_script()
	 *
	 * @param  string   $handle Name of the script. Should be unique.
	 * @param  string   $path Full URL of the script, or path of the script relative to the WordPress root directory.
	 * @param  string[] $deps An array of registered script handles this script depends on.
	 * @param  string   $version String specifying script version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
	 * @param  boolean  $in_footer Whether to enqueue the script before </body> instead of in the <head>. Default 'false'.
	 */
	private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = WC_ACE_VERSION, $in_footer = true ) {
		if ( ! in_array( $handle, self::$scripts, true ) && $path ) {
			self::register_script( $handle, $path, $deps, $version, $in_footer );
		}
		wp_enqueue_script( $handle );
	}

	/**
	 * Register a style for use.
	 *
	 * @uses   wp_register_style()
	 * @see    WC_Frontend_Scripts::register_style()
	 *
	 * @param  string   $handle Name of the stylesheet. Should be unique.
	 * @param  string   $path Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
	 * @param  string[] $deps An array of registered stylesheet handles this stylesheet depends on.
	 * @param  string   $version String specifying stylesheet version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
	 * @param  string   $media The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
	 * @param  boolean  $has_rtl If has RTL version to load too.
	 */
	private static function register_style( $handle, $path, $deps = array(), $version = WC_ACE_VERSION, $media = 'all', $has_rtl = false ) {
		self::$styles[] = $handle;
		wp_register_style( $handle, $path, $deps, $version, $media );

		if ( $has_rtl ) {
			wp_style_add_data( $handle, 'rtl', 'replace' );
		}
	}

	/**
	 * Register and enqueue a styles for use.
	 *
	 * @uses   wp_enqueue_style()
	 * @see    WC_Frontend_Scripts::enqueue_style()
	 *
	 * @param  string   $handle Name of the stylesheet. Should be unique.
	 * @param  string   $path Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
	 * @param  string[] $deps An array of registered stylesheet handles this stylesheet depends on.
	 * @param  string   $version String specifying stylesheet version number, if it has one, which is added to the URL as a query string for cache busting purposes. If version is set to false, a version number is automatically added equal to current installed WordPress version. If set to null, no version is added.
	 * @param  string   $media The media for which this stylesheet has been defined. Accepts media types like 'all', 'print' and 'screen', or media queries like '(orientation: portrait)' and '(max-width: 640px)'.
	 * @param  boolean  $has_rtl If has RTL version to load too.
	 */
	private static function enqueue_style( $handle, $path = '', $deps = array(), $version = WC_ACE_VERSION, $media = 'all', $has_rtl = false ) {
		if ( ! in_array( $handle, self::$styles, true ) && $path ) {
			self::register_style( $handle, $path, $deps, $version, $media, $has_rtl );
		}
		wp_enqueue_style( $handle );
	}

	/**
	 * Register all WC scripts.
	 * @see WC_Frontend_Scripts::register_scripts()
	 */
	private static function register_scripts() {
		$suffix = '';
		//$suffix           = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$register_scripts = array(
			'wc-ace-gift'     => array(
				'src'     => self::get_asset_url( 'public/js/frontend/gift' . $suffix . '.js' ),
				'deps'    => array( 'jquery', 'kakao-api' ),
				'version' => WC_ACE_VERSION,
			),
			'wc-ace-checkout' => array(
				'src'     => self::get_asset_url( 'public/js/frontend/checkout' . $suffix . '.js' ),
				'deps'    => array( 'jquery', 'postcode-api' ),
				'version' => WC_ACE_VERSION,
			),
			'wc-ace'          => array(
				'src'     => self::get_asset_url( 'public/js/frontend/wc-ace' . $suffix . '.js' ),
				'deps'    => array( 'jquery', 'jquery-blockui', 'js-cookie' ),
				'version' => WC_ACE_VERSION,
			),
			'kakao-api'       => array(
				'src'     => '//developers.kakao.com/sdk/js/kakao.min.js',
				'deps'    => null,
				'version' => null,
			),
			'postcode-api'    => array(
				'src'     => '//ssl.daumcdn.net/dmaps/map_js_init/postcode.v2.js',
				'deps'    => null,
				'version' => null,
			),
		);
		foreach ( $register_scripts as $name => $props ) {
			self::register_script( $name, $props['src'], $props['deps'], $props['version'] );
		}
	}

	/**
	 * Register all WC sty;es.
	 * @see WC_Frontend_Scripts::register_styles()
	 */
	private static function register_styles() {
//		$register_styles = array(
//			'thirdparty-css'                  => array(
//				'src'     => self::get_asset_url( 'public/css/thirdparty/blabla.css' ),
//				'deps'    => array(),
//				'version' => WC_ACE_VERSION,
//				'has_rtl' => false,
//			),
//		);
//		foreach ( $register_styles as $name => $props ) {
//			self::register_style( $name, $props['src'], $props['deps'], $props['version'], 'all', $props['has_rtl'] );
//		}
	}

	/**
	 * Register/queue frontend scripts.
	 * @see WC_Frontend_Scripts::load_scripts()
	 */
	public static function load_scripts() {
		global $post;

		if ( ! did_action( 'before_wc_ace_init' ) ) {
			return;
		}

		self::register_scripts();
		self::register_styles();


		if ( is_gift() || is_view_order_page() ) {
			self::enqueue_script( 'wc-ace-gift' );
		}

		if ( is_checkout() || is_view_order_page() ) {
			self::enqueue_script( 'wc-ace-checkout' );
		}

		// Global frontend scripts.
		self::enqueue_script( 'wc-ace' );

		// CSS Styles.
		$enqueue_styles = self::get_styles();
		if ( $enqueue_styles ) {
			foreach ( $enqueue_styles as $handle => $args ) {
				if ( ! isset( $args['has_rtl'] ) ) {
					$args['has_rtl'] = false;
				}

				self::enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'], $args['has_rtl'] );
			}
		}

		// Placeholder style.
		wp_register_style( 'wc-ace-inline', false );
		wp_enqueue_style( 'wc-ace-inline' );
	}

	/**
	 * Localize a wc_ace script once.
	 *
	 * @see WC_Frontend_Scripts::localize_script()
	 *
	 * @param string $handle Script handle the data will be attached to.
	 */
	private static function localize_script( $handle ) {
		if ( ! in_array( $handle, self::$wp_localize_scripts, true ) && wp_script_is( $handle ) ) {
			$data = self::get_script_data( $handle );

			if ( ! $data ) {
				return;
			}

			$name                        = str_replace( '-', '_', $handle ) . '_params';
			self::$wp_localize_scripts[] = $handle;
			wp_localize_script( $handle, $name, apply_filters( $name, $data ) );
		}
	}

	/**
	 * Return data for script handles.
	 *
	 * @param  string $handle Script handle the data will be attached to.
	 *
	 * @return array|bool
	 */
	private static function get_script_data( $handle ) {
		global $wp;

		switch ( $handle ) {
			case 'wc-ace':
				$params = array(
					'ajax_url'        => wc_ace()->ajax_url(),
					'wc_ace_ajax_url' => WC_Ace_AJAX::get_endpoint( '%%endpoint%%' ),
				);
				break;
			case 'wc-ace-checkout':
				$params = array(
					'ajax_url'       => wc_ace()->ajax_url(),
					'postcode_digit' => '5',
				);
				break;
			case 'wc-ace-gift':
				$params = array(
					'ajax_url'                           => wc_ace()->ajax_url(),
					'wc_ace_ajax_url'                    => WC_Ace_AJAX::get_endpoint( '%%endpoint%%' ),
					'gift_update_shipping_address_nonce' => wp_create_nonce( 'gift-update-shipping-address' ),
					'gift_url'                           => WC_Ace_AJAX::get_endpoint( 'gift' ),
					'is_gift'                            => is_page( wc_ace_get_page_id( 'gift' ) ) && empty( $wp->query_vars['gift'] ) && ! isset( $wp->query_vars['gift-received'] ) ? 1 : 0,
					'debug_mode'                         => defined( 'WP_DEBUG' ) && WP_DEBUG,
					'i18n_gift_error'                    => esc_attr__( '처리중 에러가 발생하였습니다. 다시 시도해주시기 바랍니다.', 'wc-ace' ),
					'order_id'                           => is_view_order_page() ? $wp->query_vars['view-order'] : get_query_var( 'page' ),
				);
				break;
			default:
				$params = false;
		}

		return apply_filters( 'wc_ace_get_script_data', $params, $handle );
	}

	/**
	 * Localize scripts only when enqueued.
	 */
	public static function localize_printed_scripts() {
		foreach ( self::$scripts as $handle ) {
			self::localize_script( $handle );
		}
	}
}

WC_Ace_Frontend_Scripts::init();
