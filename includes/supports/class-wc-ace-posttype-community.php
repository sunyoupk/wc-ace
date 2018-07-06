<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 05/07/2018
 * Time: 1:37 PM
 */

/**
 * Class PostType_Community
 *
 * @package wc_ace\includes\supports
 */
class WC_Ace_Post_Type_Community {

	/**
	 * Support Community post type
	 */
	public static function init() {
		add_action( 'wc_ace_register_support_post_type', array( __CLASS__, 'register_post_type' ), 10 );
		add_action( 'wc_ace_register_support_post_type', array( __CLASS__, 'register_taxonomies' ), 20 );

		self::revoke_role();
		self::grant_role();
	}

	/**
	 * Register post type
	 */
	public static function register_post_type() {
		if ( ! is_blog_installed() || post_type_exists( 'community' ) ) {
			return;
		}

		$supports    = array( 'title', 'editor', 'thumbnail', 'comments' );
		$has_archive = false;

		register_post_type(
			'community', array(
				'labels'              => array(
					'name'                  => __( '커뮤니티', 'wc-ace' ),
					'singular_name'         => __( '커뮤니티', 'wc-ace' ),
					'all_items'             => __( '모든 게시글', 'wc-ace' ),
					'menu_name'             => _x( '커뮤니티', 'Admin menu name', 'wc-ace' ),
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
					'featured_image'        => __( '대표 이미지', 'wc-ace' ),
					'set_featured_image'    => __( '대표 이미지 설정', 'wc-ace' ),
					'remove_featured_image' => __( '대표 이미지 제거', 'wc-ace' ),
					'use_featured_image'    => __( '대표 이미지 사용', 'wc-ace' ),
					'insert_into_item'      => __( '게시글에 삽입', 'wc-ace' ),
					'filter_items_list'     => __( '필터', 'wc-ace' ),
					'items_list_navigation' => __( '네비게이션', 'wc-ace' ),
					'items_list'            => __( '게시글 목록', 'wc-ace' ),
				),
				'description'         => __( '사이트 커뮤니케이션을 위해 질의 응답 작성 및 메시지를 주고 받을 수 있습니다.', 'wc-ace' ),
				'public'              => true,
				'show_ui'             => true,
				'capability_type'     => 'community',
				'map_meta_cap'        => true,
				'publicly_queryable'  => true,
				'exclude_from_search' => false,
				'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
				'query_var'           => true,
				'supports'            => $supports,
				'has_archive'         => $has_archive,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'menu_position'       => 25,
				'menu_icon'           => 'dashicons-format-chat',
				'show_in_rest'        => false,
			)

		);
	}

	public static function register_taxonomies() {

		if ( ! is_blog_installed() ) {
			return;
		}

		if ( taxonomy_exists( 'community_cat' ) ) {
			return;
		}

		register_taxonomy(
			'community_cat',
			array( 'community' ),
			array(
				'hierarchical' => true,
				'label'        => __( '카테고리', 'wc-ace' ),
				'labels'       => array(
					'name'              => __( '커뮤니티 카테고리', 'wc-ace' ),
					'singular_name'     => __( '카테고리', 'wc-ace' ),
					'menu_name'         => _x( '카테고리', 'Admin menu name', 'wc-ace' ),
					'search_items'      => __( '카테고리 검색', 'wc-ace' ),
					'all_items'         => __( '모든 카테고리', 'wc-ace' ),
					'parent_item'       => __( '상위 카테고리', 'wc-ace' ),
					'parent_item_colon' => __( '상위 카테고리:', 'wc-ace' ),
					'edit_item'         => __( '카테고리 편집', 'wc-ace' ),
					'update_item'       => __( '카테고리 수정', 'wc-ace' ),
					'add_new_item'      => __( '새로운 카테고리 추가', 'wc-ace' ),
					'new_item_name'     => __( '신규 카테고리 이름', 'wc-ace' ),
					'not_found'         => __( '카테고리를 찾을 수 없습니다.', 'wc-ace' ),
				),
				'show_ui'      => true,
				'query_var'    => true,
				'capabilities' => array(
					'manage_terms' => 'manage_community_terms',
					'edit_terms'   => 'edit_community_terms',
					'delete_terms' => 'delete_community_terms',
					'assign_terms' => 'assign_community_terms',
				),
			)
		);

		register_taxonomy(
			'community_tag',
			array( 'community' ),
			array(
				'hierarchical'          => false,
				'label'                 => __( '태그', 'wc-ace' ),
				'labels'                => array(
					'name'                       => __( '커뮤니티 태그', 'wc-ace' ),
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
				'show_ui'               => true,
				'query_var'             => true,
				'capabilities'          => array(
					'manage_terms' => 'manage_community_terms',
					'edit_terms'   => 'edit_community_terms',
					'delete_terms' => 'delete_community_terms',
					'assign_terms' => 'assign_community_terms',
				),
			)
		);

		register_taxonomy(
			'product_shipping_class',
			apply_filters( 'woocommerce_taxonomy_objects_product_shipping_class', array(
				'product',
				'product_variation'
			) ),
			apply_filters(
				'woocommerce_taxonomy_args_product_shipping_class', array(
					'hierarchical'          => false,
					'update_count_callback' => '_update_post_term_count',
					'label'                 => __( 'Shipping classes', 'woocommerce' ),
					'labels'                => array(
						'name'              => __( 'Product shipping classes', 'woocommerce' ),
						'singular_name'     => __( 'Shipping class', 'woocommerce' ),
						'menu_name'         => _x( 'Shipping classes', 'Admin menu name', 'woocommerce' ),
						'search_items'      => __( 'Search shipping classes', 'woocommerce' ),
						'all_items'         => __( 'All shipping classes', 'woocommerce' ),
						'parent_item'       => __( 'Parent shipping class', 'woocommerce' ),
						'parent_item_colon' => __( 'Parent shipping class:', 'woocommerce' ),
						'edit_item'         => __( 'Edit shipping class', 'woocommerce' ),
						'update_item'       => __( 'Update shipping class', 'woocommerce' ),
						'add_new_item'      => __( 'Add new shipping class', 'woocommerce' ),
						'new_item_name'     => __( 'New shipping class Name', 'woocommerce' ),
					),
					'show_ui'               => false,
					'show_in_quick_edit'    => false,
					'show_in_nav_menus'     => false,
					'query_var'             => is_admin(),
					'capabilities'          => array(
						'manage_terms' => 'manage_product_terms',
						'edit_terms'   => 'edit_product_terms',
						'delete_terms' => 'delete_product_terms',
						'assign_terms' => 'assign_product_terms',
					),
					'rewrite'               => false,
				)
			)
		);

		global $wc_product_attributes;

		$wc_product_attributes = array();
		$attribute_taxonomies  = wc_get_attribute_taxonomies();

		if ( $attribute_taxonomies ) {
			foreach ( $attribute_taxonomies as $tax ) {
				$name = wc_attribute_taxonomy_name( $tax->attribute_name );

				if ( $name ) {
					$tax->attribute_public          = absint( isset( $tax->attribute_public ) ? $tax->attribute_public : 1 );
					$label                          = ! empty( $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;
					$wc_product_attributes[ $name ] = $tax;
					$taxonomy_data                  = array(
						'hierarchical'          => false,
						'update_count_callback' => '_update_post_term_count',
						'labels'                => array(
							/* translators: %s: attribute name */
							'name'              => sprintf( _x( 'Product %s', 'Product Attribute', 'woocommerce' ), $label ),
							'singular_name'     => $label,
							/* translators: %s: attribute name */
							'search_items'      => sprintf( __( 'Search %s', 'woocommerce' ), $label ),
							/* translators: %s: attribute name */
							'all_items'         => sprintf( __( 'All %s', 'woocommerce' ), $label ),
							/* translators: %s: attribute name */
							'parent_item'       => sprintf( __( 'Parent %s', 'woocommerce' ), $label ),
							/* translators: %s: attribute name */
							'parent_item_colon' => sprintf( __( 'Parent %s:', 'woocommerce' ), $label ),
							/* translators: %s: attribute name */
							'edit_item'         => sprintf( __( 'Edit %s', 'woocommerce' ), $label ),
							/* translators: %s: attribute name */
							'update_item'       => sprintf( __( 'Update %s', 'woocommerce' ), $label ),
							/* translators: %s: attribute name */
							'add_new_item'      => sprintf( __( 'Add new %s', 'woocommerce' ), $label ),
							/* translators: %s: attribute name */
							'new_item_name'     => sprintf( __( 'New %s', 'woocommerce' ), $label ),
							/* translators: %s: attribute name */
							'not_found'         => sprintf( __( 'No &quot;%s&quot; found', 'woocommerce' ), $label ),
						),
						'show_ui'               => true,
						'show_in_quick_edit'    => false,
						'show_in_menu'          => false,
						'meta_box_cb'           => false,
						'query_var'             => 1 === $tax->attribute_public,
						'rewrite'               => false,
						'sort'                  => false,
						'public'                => 1 === $tax->attribute_public,
						'show_in_nav_menus'     => 1 === $tax->attribute_public && apply_filters( 'woocommerce_attribute_show_in_nav_menus', false, $name ),
						'capabilities'          => array(
							'manage_terms' => 'manage_product_terms',
							'edit_terms'   => 'edit_product_terms',
							'delete_terms' => 'delete_product_terms',
							'assign_terms' => 'assign_product_terms',
						),
					);

					if ( 1 === $tax->attribute_public && sanitize_title( $tax->attribute_name ) ) {
						$taxonomy_data['rewrite'] = array(
							'slug'         => trailingslashit( $permalinks['attribute_rewrite_slug'] ) . sanitize_title( $tax->attribute_name ),
							'with_front'   => false,
							'hierarchical' => true,
						);
					}

					register_taxonomy( $name, apply_filters( "woocommerce_taxonomy_objects_{$name}", array( 'product' ) ), apply_filters( "woocommerce_taxonomy_args_{$name}", $taxonomy_data ) );
				}
			}
		}

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

	private static function get_core_capabilities() {
		$capabilities = array();

		$capabilities['core'] = array(
			'manage_woocommerce'
		);

		$capability_types = array( 'community' );

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

WC_Ace_Post_Type_Community::init();
