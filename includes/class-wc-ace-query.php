<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 20/07/2018
 * Time: 5:13 PM
 */

/**
 * Class WC_Ace_Query
 */
class WC_Ace_Query {
	public $query_vars = array();

	/**
	 * WC_Ace_Query constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'add_endpoints' ) );
		if ( ! is_admin() ) {
			add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
			add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		}
		$this->init_query_vars();
	}

	public function init_query_vars() {
		// Query vars to add to WP.
		$this->query_vars = array(
			'gift' => get_option( 'bangrang_gift_endpoint', 'gift' ),
		);
	}

	public function add_query_vars( $vars ) {
		foreach ( $this->get_query_vars() as $key => $var ) {
			$vars[] = $key;
		}
		return $vars;
	}

	public function add_endpoints() {
		$mask = $this->get_endpoints_mask();
		foreach ( $this->query_vars as $key => $var ) {
			if ( ! empty( $var ) ) {
				add_rewrite_endpoint( $var, $mask );
			}
		}
	}

	public function get_endpoints_mask() {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$page_on_front = get_option( 'page_on_front' );
			$gift_page_id  = get_option( 'bangrang_gift_page_id' );

			if ( in_array( $page_on_front, array( $gift_page_id ), true ) ) {
				return EP_ROOT | EP_PAGES;
			}
		}

		return EP_PAGES;
	}

	public function get_query_vars() {
		return apply_filters( 'bangrang_get_query_vars', $this->query_vars );
	}

	public function pre_get_posts( $q ) {
		// We only want to affect the main query.
		if ( ! $q->is_main_query() ) {
			return;
		}

		// Fix for endpoints on the homepage.
		if ( $this->is_showing_page_on_front( $q ) && ! $this->page_on_front_is( $q->get( 'page_id' ) ) ) {
			$_query = wp_parse_args( $q->query );
			if ( ! empty( $_query ) && array_intersect( array_keys( $_query ), array_keys( $this->get_query_vars() ) ) ) {
				$q->is_page     = true;
				$q->is_home     = false;
				$q->is_singular = true;
				$q->set( 'page_id', (int) get_option( 'page_on_front' ) );
				add_filter( 'redirect_canonical', '__return_false' );
			}
		}

		error_log( print_r( $q, true ) );

		return;
	}

	private function is_showing_page_on_front( $q ) {
		return $q->is_home() && 'page' === get_option( 'show_on_front' );
	}

	private function page_on_front_is( $page_id ) {
		return absint( get_option( 'page_on_front' ) ) === absint( $page_id );
	}
}