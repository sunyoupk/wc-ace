<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 05/07/2018
 * Time: 1:37 PM
 */

/**
 * Class PostType_Musicsource
 *
 * @package wc_ace\includes\supports
 */
class WC_Ace_Post_Type_Musicsource {

	/**
	 * Support Musicsource post type
	 */
	public static function init() {
		add_action( 'wc_ace_register_support_post_type', array( __CLASS__, 'register_post_type' ), 10 );
		add_action( 'wc_ace_register_support_taxonomies', array( __CLASS__, 'register_taxonomies' ), 20 );

		self::revoke_role();
		self::grant_role();
	}

	/**
	 * Register post type
	 */
	public static function register_post_type() {
		if ( ! is_blog_installed() || post_type_exists( 'musicsource' ) ) {
			return;
		}

		$supports    = array( 'thumbnail', 'comments' );
		$has_archive = false;

		register_post_type(
			'musicsource', array(
				'labels'              => array(
					'name'                  => __( '음원', 'wc-ace' ),
					'singular_name'         => __( '음원', 'wc-ace' ),
					'all_items'             => __( '모든 음원', 'wc-ace' ),
					'menu_name'             => _x( '음원', 'Admin menu name', 'wc-ace' ),
					'add_new'               => __( '새로 추가', 'wc-ace' ),
					'add_new_item'          => __( '새로 추가', 'wc-ace' ),
					'edit'                  => __( '편집', 'wc-ace' ),
					'edit_item'             => __( '편집', 'wc-ace' ),
					'new_item'              => __( '새로 추가', 'wc-ace' ),
					'view_item'             => __( '보기', 'wc-ace' ),
					'view_items'            => __( '보기', 'wc-ace' ),
					'search_items'          => __( '검색', 'wc-ace' ),
					'not_found'             => __( '찾을 수 없습니다.', 'wc-ace' ),
					'not_found_in_trash'    => __( '찾을 수 없습니다.', 'wc-ace' ),
					'parent'                => __( '상위', 'wc-ace' ),
					'featured_image'        => __( '커버 이미지', 'wc-ace' ),
					'set_featured_image'    => __( '커버 이미지 설정', 'wc-ace' ),
					'remove_featured_image' => __( '커버 이미지 제거', 'wc-ace' ),
					'use_featured_image'    => __( '커버 이미지 사용', 'wc-ace' ),
					'insert_into_item'      => __( '게시글에 삽입', 'wc-ace' ),
					'filter_items_list'     => __( '필터', 'wc-ace' ),
					'items_list_navigation' => __( '네비게이션', 'wc-ace' ),
					'items_list'            => __( '게시글 목록', 'wc-ace' ),
				),
				'description'         => __( '음원 디지털 상품 판매를 위한 메타 포스트 입니다.', 'wc-ace' ),
				'public'              => false,
				'show_ui'             => true,
				'capability_type'     => 'musicsource',
				'map_meta_cap'        => true,
				'publicly_queryable'  => false,
				'exclude_from_search' => false,
				'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
				'query_var'           => true,
				'supports'            => $supports,
				'has_archive'         => $has_archive,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'menu_position'       => 25,
				'menu_icon'           => 'dashicons-format-audio',
				'show_in_rest'        => false,
			)

		);

		// Musicsource post statuses.
		$musicsource_statuses =
			array(
				'musicsource-draft'     => array(
					'label'                     => _x( '작성중', 'Musicsource status', 'wc-ace' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( '작성중 <span class="count">(%s)</span>', '작성중 <span class="count">(%s)</span>', 'wc-ace' )
				),
				'musicsource-requested' => array(
					'label'                     => _x( '판매요청됨', 'Musicsource status', 'wc-ace' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( '판매요청됨 <span class="count">(%s)</span>', '판매요청됨 <span class="count">(%s)</span>', 'wc-ace' )
				),
				'musicsource-on-hold'   => array(
					'label'                     => _x( '판매대기중', 'Musicsource status', 'wc-ace' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( '판매대기중 <span class="count">(%s)</span>', '판매대기중 <span class="count">(%s)</span>', 'wc-ace' )
				),
				'musicsource-rejected'  => array(
					'label'                     => _x( '판매거부됨', 'Musicsource status', 'wc-ace' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( '판매거부됨 <span class="count">(%s)</span>', '판매거부됨 <span class="count">(%s)</span>', 'wc-ace' )
				),
				'musicsource-cancelled' => array(
					'label'                     => _x( '판매취소됨', 'Musicsource status', 'wc-ace' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( '판매취소됨 <span class="count">(%s)</span>', '판매취소됨 <span class="count">(%s)</span>', 'wc-ace' )
				),
				'musicsource-on-sale'   => array(
					'label'                     => _x( '판매중', 'Musicsource status', 'wc-ace' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( '판매중 <span class="count">(%s)</span>', '판매중 <span class="count">(%s)</span>', 'wc-ace' )
				),
				'musicsource-expired'   => array(
					'label'                     => _x( '판매만료됨', 'Musicsource status', 'wc-ace' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( '판매만료됨 <span class="count">(%s)</span>', '판매만료됨 <span class="count">(%s)</span>', 'wc-ace' )
				),
				'musicsource-closed'    => array(
					'label'                     => _x( '판매종료됨', 'Musicsource status', 'wc-ace' ),
					'public'                    => false,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( '판매종료됨 <span class="count">(%s)</span>', '판매종료됨 <span class="count">(%s)</span>', 'wc-ace' )
				),
			);

		foreach ( $musicsource_statuses as $status => $values ) {
			register_post_status( $status, $values );
		}

	}

	/**
	 * Register taxonomies
	 */
	public static function register_taxonomies() {

		if ( ! is_blog_installed() ) {
			return;
		}

		if ( taxonomy_exists( 'musicsource_genre' ) ) {
			return;
		}

		// Genre.
		register_taxonomy(
			'musicsource_genre',
			array( 'musicsource' ),
			array(
				'hierarchical' => true,
				'label'        => __( '장르', 'wc-ace' ),
				'description'  => __( '음원에 대한 장르를 정의합니다.', 'wc-ace' ),
				'labels'       => array(
					'name'              => __( '음원 장르', 'wc-ace' ),
					'singular_name'     => __( '장르', 'wc-ace' ),
					'menu_name'         => _x( '장르', 'Admin menu name', 'wc-ace' ),
					'search_items'      => __( '장르 검색', 'wc-ace' ),
					'all_items'         => __( '모든 장르', 'wc-ace' ),
					'parent_item'       => __( '상위 장르', 'wc-ace' ),
					'parent_item_colon' => __( '상위 장르:', 'wc-ace' ),
					'edit_item'         => __( '장르 편집', 'wc-ace' ),
					'update_item'       => __( '장르 수정', 'wc-ace' ),
					'add_new_item'      => __( '새로운 장르 추가', 'wc-ace' ),
					'new_item_name'     => __( '신규 장르 이름', 'wc-ace' ),
					'not_found'         => __( '장르를 찾을 수 없습니다.', 'wc-ace' ),
				),
				'show_ui'      => true,
				'meta_box_cb'  => false,
				'query_var'    => true,
				'capabilities' => array(
					'manage_terms' => 'manage_musicsource_terms',
					'edit_terms'   => 'edit_musicsource_terms',
					'delete_terms' => 'delete_musicsource_terms',
					'assign_terms' => 'assign_musicsource_terms',
				),
			)
		);

		// Type.
		register_taxonomy(
			'musicsource_type',
			array( 'musicsource' ),
			array(
				'hierarchical' => true,
				'label'        => __( '종류', 'wc-ace' ),
				'description'  => __( '음원에 대한 라이선스를 정의합니다.', 'wc-ace' ),
				'labels'       => array(
					'name'              => __( '음원 종류', 'wc-ace' ),
					'singular_name'     => __( '종류', 'wc-ace' ),
					'menu_name'         => _x( '종류', 'Admin menu name', 'wc-ace' ),
					'search_items'      => __( '종류 검색', 'wc-ace' ),
					'all_items'         => __( '모든 종류', 'wc-ace' ),
					'parent_item'       => __( '상위 종류', 'wc-ace' ),
					'parent_item_colon' => __( '상위 종류:', 'wc-ace' ),
					'edit_item'         => __( '종류 편집', 'wc-ace' ),
					'update_item'       => __( '종류 수정', 'wc-ace' ),
					'add_new_item'      => __( '새로운 종류 추가', 'wc-ace' ),
					'new_item_name'     => __( '신규 종류 이름', 'wc-ace' ),
					'not_found'         => __( '종류를 찾을 수 없습니다.', 'wc-ace' ),
				),
				'show_ui'      => true,
				'meta_box_cb'  => false,
				'query_var'    => true,
				'capabilities' => array(
					'manage_terms' => 'manage_musicsource_terms',
					'edit_terms'   => 'edit_musicsource_terms',
					'delete_terms' => 'delete_musicsource_terms',
					'assign_terms' => 'assign_musicsource_terms',
				),
			)
		);

		// License.
		register_taxonomy(
			'musicsource_license',
			array( 'musicsource' ),
			array(
				'hierarchical' => true,
				'label'        => __( '라이선스', 'wc-ace' ),
				'description'  => __( '음원에 대한 라이선스를 정의합니다.', 'wc-ace' ),
				'labels'       => array(
					'name'              => __( '음원 라이선스', 'wc-ace' ),
					'singular_name'     => __( '라이선스', 'wc-ace' ),
					'menu_name'         => _x( '라이선스', 'Admin menu name', 'wc-ace' ),
					'search_items'      => __( '라이선스 검색', 'wc-ace' ),
					'all_items'         => __( '모든 라이선스', 'wc-ace' ),
					'parent_item'       => __( '상위 라이선스', 'wc-ace' ),
					'parent_item_colon' => __( '상위 라이선스:', 'wc-ace' ),
					'edit_item'         => __( '라이선스 편집', 'wc-ace' ),
					'update_item'       => __( '라이선스 수정', 'wc-ace' ),
					'add_new_item'      => __( '새로운 라이선스 추가', 'wc-ace' ),
					'new_item_name'     => __( '신규 라이선스 이름', 'wc-ace' ),
					'not_found'         => __( '라이선스를 찾을 수 없습니다.', 'wc-ace' ),
				),
				'show_ui'      => true,
				'meta_box_cb'  => false,
				'query_var'    => true,
				'capabilities' => array(
					'manage_terms' => 'manage_musicsource_terms',
					'edit_terms'   => 'edit_musicsource_terms',
					'delete_terms' => 'delete_musicsource_terms',
					'assign_terms' => 'assign_musicsource_terms',
				),
			)
		);

		register_taxonomy(
			'musicsource_tag',
			array( 'musicsource' ),
			array(
				'hierarchical' => false,
				'label'        => __( '태그', 'wc-ace' ),
				'labels'       => array(
					'name'                       => __( '음원 태그', 'wc-ace' ),
					'singular_name'              => __( '태그', 'wc-ace' ),
					'menu_name'                  => _x( '태그', 'Admin menu name', 'wc-ace' ),
					'search_items'               => __( '태그 검색', 'wc-ace' ),
					'all_items'                  => __( '모든 태그', 'wc-ace' ),
					'edit_item'                  => __( '태그 편집', 'wc-ace' ),
					'update_item'                => __( '태그 수정', 'wc-ace' ),
					'add_new_item'               => __( '새로운 태그 추가', 'wc-ace' ),
					'new_item_name'              => __( '신규 태그 이름', 'wc-ace' ),
					'popular_items'              => __( '인기 있는 태그', 'wc-ace' ),
					'separate_items_with_commas' => __( '각 태그를 쉼표로 분리하세', 'wc-ace' ),
					'add_or_remove_items'        => __( '태그 추가 또는 제거', 'wc-ace' ),
					'choose_from_most_used'      => __( '인기 태그 중에서 선', 'wc-ace' ),
					'not_found'                  => __( '태그를 찾을 수 없습니다.', 'wc-ace' ),
				),
				'show_ui'      => true,
				'query_var'    => true,
				'capabilities' => array(
					'manage_terms' => 'manage_musicsource_terms',
					'edit_terms'   => 'edit_musicsource_terms',
					'delete_terms' => 'delete_musicsource_terms',
					'assign_terms' => 'assign_musicsource_terms',
				),
			)
		);

	}

	/**
	 * Grant post type role.
	 */
	private static function grant_role() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
		}

		$capabilities = self::get_core_capabilities();

		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'shop_manager', $cap );
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}

	/**
	 * Revoke role.
	 */
	private static function revoke_role() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
		}

		$capabilities = self::get_core_capabilities();

		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->remove_cap( 'shop_manager', $cap );
				$wp_roles->remove_cap( 'administrator', $cap );
			}
		}
	}

	/**
	 * Capabilities
	 *
	 * @return array
	 */
	private static function get_core_capabilities() {
		$capabilities = array();

		$capabilities['core'] = array(
			'manage_woocommerce'
		);

		$capability_types = array( 'musicsource' );

		foreach ( $capability_types as $capability_type ) {

			$capabilities[ $capability_type ] = array(
				// Post type.
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",

				// Terms.
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms",
			);
		}

		return $capabilities;
	}

}

WC_Ace_Post_Type_Musicsource::init();
