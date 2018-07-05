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
 * @package wc_ace\includes\posttypes
 */
class WC_Ace_Posttype_Community {

	/**
	 * Support Community post type
	 */
	public static function register() {
		add_action( 'wc_ace_support_post_type', array( __CLASS__, 'register_post_type' ), 20 );
	}

	/**
	 * Register post type
	 */
	public static function register_post_type() {
		if ( ! is_blog_installed() || post_type_exists( 'community' ) ) {
			return;
		}

		$supports    = array( 'title', 'editor', 'thumbnail', 'custom-fields', 'comments' );
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
				'show_in_nav_menus'   => true,
				'show_in_rest'        => false,
			)

		);

	}

}

WC_Ace_Posttype_Community::register();
