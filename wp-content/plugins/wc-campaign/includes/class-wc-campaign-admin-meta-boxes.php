<?php
/**
 * WooCommerce Meta Boxes
 *
 * Sets up the write panels used by products and orders (custom post types).
 *
 * @author      WooThemes
 * @category    Admin
 * @package     WooCommerce/Admin/Meta Boxes
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WC_Admin_Meta_Boxes.
 */
class WC_Campaign_Admin_Meta_Boxes {

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
	public static $meta_box_errors  = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'rename_meta_boxes' ), 20 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

		// Save Coupon Meta Boxes.
		add_action( 'woocommerce_process_shop_coupon_meta', 'WC_Meta_Box_Coupon_Data::save', 10, 2 );


		// Error handling (for showing errors from meta boxes on next page load).
		add_action( 'admin_notices', array( $this, 'output_errors' ) );
		add_action( 'shutdown', array( $this, 'save_errors' ) );
	}

	/**
	 * Add an error message.
	 * @param string $text
	 */
	public static function add_error( $text ) {
		self::$meta_box_errors[] = $text;
	}

	/**
	 * Save errors to an option.
	 */
	public function save_errors() {
		update_option( 'woocommerce_meta_box_errors', self::$meta_box_errors );
	}

	/**
	 * Show any stored error messages.
	 */
	public function output_errors() {
		$errors = array_filter( (array) get_option( 'woocommerce_meta_box_errors' ) );

		if ( ! empty( $errors ) ) {

			echo '<div id="woocommerce_errors" class="error notice is-dismissible">';

			foreach ( $errors as $error ) {
				echo '<p>' . wp_kses_post( $error ) . '</p>';
			}

			echo '</div>';

			// Clear
			delete_option( 'woocommerce_meta_box_errors' );
		}
	}

	/**
	 * Add WC Meta boxes.
	 */
	public function add_meta_boxes() {
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		// Products.
		add_meta_box( 'postexcerpt', __( 'Product short description', 'woocommerce' ), 'WC_Meta_Box_Product_Short_Description::output', 'product', 'normal' );
		add_meta_box( 'woocommerce-product-data', __( 'Product data', 'woocommerce' ), 'WC_Meta_Box_Product_Data::output', 'product', 'normal', 'high' );
		add_meta_box( 'woocommerce-product-images', __( 'Product gallery', 'woocommerce' ), 'WC_Meta_Box_Product_Images::output', 'product', 'side', 'low' );

		// Orders.
		foreach ( wc_get_order_types( 'order-meta-boxes' ) as $type ) {
			$order_type_object = get_post_type_object( $type );
			add_meta_box( 'woocommerce-order-data', sprintf( __( '%s data', 'woocommerce' ), $order_type_object->labels->singular_name ), 'WC_Meta_Box_Order_Data::output', $type, 'normal', 'high' );
			add_meta_box( 'woocommerce-order-items', __( 'Items', 'woocommerce' ), 'WC_Meta_Box_Order_Items::output', $type, 'normal', 'high' );
			add_meta_box( 'woocommerce-order-notes', sprintf( __( '%s notes', 'woocommerce' ), $order_type_object->labels->singular_name ), 'WC_Meta_Box_Order_Notes::output', $type, 'side', 'default' );
			add_meta_box( 'woocommerce-order-downloads', __( 'Downloadable product permissions', 'woocommerce' ) . wc_help_tip( __( 'Note: Permissions for order items will automatically be granted when the order status changes to processing/completed.', 'woocommerce' ) ), 'WC_Meta_Box_Order_Downloads::output', $type, 'normal', 'default' );
			add_meta_box( 'woocommerce-order-actions', sprintf( __( '%s actions', 'woocommerce' ), $order_type_object->labels->singular_name ), 'WC_Meta_Box_Order_Actions::output', $type, 'side', 'high' );
		}

		// Coupons.
		add_meta_box( 'woocommerce-coupon-data', __( 'Coupon data', 'woocommerce' ), 'WC_Meta_Box_Coupon_Data::output', 'shop_coupon', 'normal', 'high' );

		// Comment rating.
		if ( 'comment' === $screen_id && isset( $_GET['c'] ) && metadata_exists( 'comment', $_GET['c'], 'rating' ) ) {
			add_meta_box( 'woocommerce-rating', __( 'Rating', 'woocommerce' ), 'WC_Meta_Box_Product_Reviews::output', 'comment', 'normal', 'high' );
		}
	}

	/**
	 * Remove bloat.
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'postexcerpt', 'product', 'normal' );
		remove_meta_box( 'product_shipping_classdiv', 'product', 'side' );
		remove_meta_box( 'commentsdiv', 'product', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'product', 'side' );
		remove_meta_box( 'commentstatusdiv', 'product', 'normal' );
		remove_meta_box( 'woothemes-settings', 'shop_coupon', 'normal' );
		remove_meta_box( 'commentstatusdiv', 'shop_coupon', 'normal' );
		remove_meta_box( 'slugdiv', 'shop_coupon', 'normal' );

		foreach ( wc_get_order_types( 'order-meta-boxes' ) as $type ) {
			remove_meta_box( 'commentsdiv', $type, 'normal' );
			remove_meta_box( 'woothemes-settings', $type, 'normal' );
			remove_meta_box( 'commentstatusdiv', $type, 'normal' );
			remove_meta_box( 'slugdiv', $type, 'normal' );
			remove_meta_box( 'submitdiv', $type, 'side' );
		}
	}

	/**
	 * Rename core meta boxes.
	 */
	public function rename_meta_boxes() {
		global $post;

		// Comments/Reviews
		if ( isset( $post ) && ( 'publish' == $post->post_status || 'private' == $post->post_status ) && post_type_supports( 'product', 'comments' ) ) {
			remove_meta_box( 'commentsdiv', 'product', 'normal' );
			add_meta_box( 'commentsdiv', __( 'Reviews', 'woocommerce' ), 'post_comment_meta_box', 'product', 'normal' );
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

		// Dont' save meta boxes for revisions or autosaves
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the nonce
		if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' ) ) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// We need this save event to run once to avoid potential endless loops. This would have been perfect:
		// remove_action( current_filter(), __METHOD__ );
		// But cannot be used due to https://github.com/woocommerce/woocommerce/issues/6485
		// When that is patched in core we can use the above. For now:
		self::$saved_meta_boxes = true;

		// Check the post type
		if ( in_array( $post->post_type, wc_get_order_types( 'order-meta-boxes' ) ) ) {
			do_action( 'woocommerce_process_shop_order_meta', $post_id, $post );
		} elseif ( in_array( $post->post_type, array( 'product', 'shop_coupon' ) ) ) {
			do_action( 'woocommerce_process_' . $post->post_type . '_meta', $post_id, $post );
		}
	}
}

new WC_Campaign_Admin_Meta_Boxes();