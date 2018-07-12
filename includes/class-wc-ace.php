<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 25/06/2018
 * Time: 11:24 AM
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Ace.
 *
 * @package wc_ace\includes
 */
final class WC_Ace {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version = '1.0';

	/**
	 * Plugin main singleton instance.
	 *
	 * @var null
	 */
	protected static $_instance = null;

	/**
	 * Make plugin main instance(single).
	 *
	 * @return null|WC_Ace
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Ace constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Define constants.
	 */
	private function define_constants() {
		define( 'WC_ACE_ABSPATH', dirname( WC_ACE_PLUGIN_FILE ) . '/' );
		define( 'WC_ACE_PLUGIN_BASENAME', plugin_basename( WC_ACE_PLUGIN_FILE ) );
		define( 'WC_ACE_VERSION', $this->version );
	}

	/**
	 * Include files.
	 */
	public function includes() {

		/**
		 * Class autoloader.
		 */
		include_once WC_ACE_ABSPATH . 'includes/class-wc-ace-autoloader.php';

		/**
		 * Core classes.
		 */
//		include_once WC_ACE_ABSPATH . 'includes/wc-core-functions.php';
//		include_once WC_ACE_ABSPATH . 'includes/class-wc-datetime.php';
		include_once WC_ACE_ABSPATH . 'includes/class-wc-ace-post-types.php';
		include_once WC_ACE_ABSPATH . 'includes/class-wc-ace-install.php';

		if ( $this->is_request( 'admin' ) ) {
			include_once WC_ACE_ABSPATH . 'includes/admin/class-wc-ace-admin.php';
		}

		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_includes();
		}
	}

	/**
	 * Include front-end files.
	 */
	public function frontend_includes() {
	}

	/**
	 * Hook WordPress and WooCommerce.
	 */
	private function init_hooks() {
		register_activation_hook( WC_ACE_PLUGIN_FILE, array( 'WC_Ace_Install', 'install' ) );
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Check request type.
	 *
	 * @see WooCommerce/class-woocommerce.php
	 *
	 * @param string $type admin, ajax, cron or frontend.
	 *
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Init plugin.
	 */
	public function init() {
		// Set up localisation.
		$this->load_plugin_textdomain();
	}

	/**
	 * Load plugin textdomain.
	 */
	public function load_plugin_textdomain() {
		$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		unload_textdomain( 'wc-ace' );
		load_textdomain( 'wc-ace', WP_LANG_DIR . '/wc-ace/' . $locale . '.mo' );
		load_plugin_textdomain( 'wc-ace', false, plugin_basename( dirname( WC_ACE_PLUGIN_FILE ) ) . '/languages' );
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', WC_ACE_PLUGIN_FILE ) );
	}
}