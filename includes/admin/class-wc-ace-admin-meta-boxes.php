<?php
/**
 * Created by PhpStorm.
 * User: ace
 * Date: 13/07/2018
 * Time: 11:34 AM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class WC_Ace_Admin_Meta_Boxes
 */
class WC_Ace_Admin_Meta_Boxes {

	/**
	 * Is meta boxes saved once?
	 *
	 * @var boolean
	 */
	private static $saved_meta_boxes = false;

	/**
	 * Meta box error messages.
	 *
	 * @var array
	 */
	public static $meta_box_errors = array();

	/**
	 * WC_Ace_Admin_Meta_Boxes constructor.
	 */
	public function __construct() {
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

		// Error handling (for showing errors from meta boxes on next page load).
		add_action( 'admin_notices', array( $this, 'output_errors' ) );
		add_action( 'shutdown', array( $this, 'save_errors' ) );
	}

	/**
	 * Add an error message.
	 *
	 * @param string $text
	 */
	public static function add_error( $text ) {
		self::$meta_box_errors[] = $text;
	}

	/**
	 * Save errors to an option.
	 */
	public function save_errors() {
		update_option( 'wc_ace_meta_box_errors', self::$meta_box_errors );
	}

	/**
	 * Show any stored error messages.
	 */
	public function output_errors() {
		$errors = array_filter( (array) get_option( 'wc_ace_meta_box_errors' ) );

		if ( ! empty( $errors ) ) {

			echo '<div id="wc_ace_errors" class="error notice is-dismissible">';

			foreach ( $errors as $error ) {
				echo '<p>' . wp_kses_post( $error ) . '</p>';
			}

			echo '</div>';

			// Clear
			delete_option( 'wc_ace_meta_box_errors' );
		}
	}

	/**
	 * Check if we're saving, the trigger an action based on the post type.
	 *
	 * @param  int $post_id
	 * @param  object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves.
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the nonce.
		if ( empty( $_POST['wc_ace_meta_nonce'] ) || ! wp_verify_nonce( $_POST['wc_ace_meta_nonce'], 'wc_ace_save_data' ) ) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events.
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// We need this save event to run once to avoid potential endless loops. This would have been perfect:
		// remove_action( current_filter(), __METHOD__ );
		// But cannot be used due to https://github.com/woocommerce/woocommerce/issues/6485
		// When that is patched in core we can use the above. For now:
		self::$saved_meta_boxes = true;

		// Check the post type.
		if ( in_array( $post->post_type, wc_ace_get_availiable_post_types() ) ) {
			do_action( 'wc_ace_process_' . $post->post_type . '_meta', $post_id, $post );
		}
	}
}

new WC_Ace_Admin_Meta_Boxes();
