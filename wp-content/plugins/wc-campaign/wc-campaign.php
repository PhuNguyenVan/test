<?php
/**
 * Plugin Name: WooComerce Campaign
 * Plugin URI:
 * Description: This is plugin for Woocomerce to create campaign
 * Version: 1.0
 * Author: Bluebelt Asia
 * Author URI:
 * License: GPLv2 or later
 */

if (!defined('ABSPATH')) {
	exit;
	// Exit if accessed directly
}
?>

<?php
if(!class_exists('Wc_Campaign')) {
	class Wc_Campaign {

		function __construct(){

			if(!defined('WC_CAMPAIGN_DIR')){
				@define('WC_CAMPAIGN_DIR', __DIR__);
			}
			define( 'WCC_PLUGIN_FILE', __FILE__ );
			define( 'WCC_ABSPATH', dirname( __FILE__ ) . '/' );

			add_action('init', array($this, 'admin_menu_campaign'));
			add_filter('woocommerce_screen_ids',array($this,'add_screen_id'),999,1);
			register_activation_hook(__FILE__, array($this, 'add_theme_caps'));
			add_filter( 'manage_shop_campaign_posts_columns', array( $this, 'shop_campaign_columns' ) );
			add_action( 'manage_shop_campaign_posts_custom_column', array( $this, 'render_shop_campaign_columns' ), 2 );
			add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );
//			add_action('plugins_loaded',array($this,'wc_cancel_load_textdomain'));
			$this->includeAsset();

		}

		public function includeAsset() {

			include_once( WCC_ABSPATH . 'includes/class-wc-campaign-autoloader.php' );
			/**
			 * Interfaces.
			 */

			include_once( WCC_ABSPATH . 'includes/interfaces/class-wc-campaign-data-store-interface.php' );
			include_once( WCC_ABSPATH . 'includes/interfaces/class-wc-object-campaign-data-store-interface.php' );

			/**
			 * Data stores - used to store and retrieve CRUD object data from the database.
			 */
			//include_once( WC_ABSPATH . 'includes/data-stores/class-wc-data-store-wp.php' );
			include_once( WCC_ABSPATH . 'includes/class-wc-campaign-data-store.php' );
			include_once( WCC_ABSPATH . 'includes/data-stores/class-wc-campaign-data-store-wp.php' );
			include_once( WCC_ABSPATH . 'includes/data-stores/class-wc-campaign-data-store-cpt.php' );
		}

		function edit_form_after_title( $post ) {
			if ( 'shop_campaign' === $post->post_type ) {
				?>
				<textarea id="woocommerce-coupon-description" name="excerpt" cols="5" rows="2" placeholder="<?php esc_attr_e( 'Description (optional)', 'woocommerce' ); ?>"><?php echo $post->post_excerpt; // This is already escaped in core ?></textarea>
				<?php
			}
		}

		function admin_menu_campaign(){
			register_post_type( 'shop_campaign',
				apply_filters( 'woocommerce_register_post_type_shop_campaign',
					array(
						'labels'              => array(
							'name'                  => __( 'Campaign', 'woocommerce' ),
							'singular_name'         => __( 'Campaign', 'woocommerce' ),
							'menu_name'             => _x( 'Campaign', 'Admin menu name', 'woocommerce' ),
							'add_new'               => __( 'Add Campaign', 'woocommerce' ),
							'add_new_item'          => __( 'Add new Campaign', 'woocommerce' ),
							'edit'                  => __( 'Edit', 'woocommerce' ),
							'edit_item'             => __( 'Edit Campaign', 'woocommerce' ),
							'new_item'              => __( 'New Campaign', 'woocommerce' ),
							'view'                  => __( 'View Campaign', 'woocommerce' ),
							'view_item'             => __( 'View Campaign', 'woocommerce' ),
							'search_items'          => __( 'Search Campaign', 'woocommerce' ),
							'not_found'             => __( 'No Campaign found', 'woocommerce' ),
							'not_found_in_trash'    => __( 'No Campaign found in trash', 'woocommerce' ),
							'parent'                => __( 'Parent Campaign', 'woocommerce' ),
							'filter_items_list'     => __( 'Filter Campaign', 'woocommerce' ),
							'items_list_navigation' => __( 'Campaign navigation', 'woocommerce' ),
							'items_list'            => __( 'Campaign list', 'woocommerce' ),
						),
						'description'         => __( 'This is where you can add new coupons that customers can use in your store.', 'woocommerce' ),
						'public'              => false,
						'show_ui'             => true,
						'capability_type'     => 'shop_campaign',
						'map_meta_cap'        => true,
						'publicly_queryable'  => false,
						'exclude_from_search' => true,
						'show_in_menu'        => current_user_can( 'manage_woocommerce' ) ? 'woocommerce' : true,
						'hierarchical'        => false,
						'rewrite'             => false,
						'query_var'           => false,
						'supports'            => array( 'title' ),
						'show_in_nav_menus'   => false,
						'show_in_admin_bar'   => true,
					)
				)
			);
//			add_submenu_page('woocommerce', __('Campaign'), __('Campaign'), 'manage_woocommerce', 'wc_campaign', array($this, 'wc_campaign_dashboard'));
		}
		function add_screen_id($screen_ids){
			$screen = get_current_screen();
			$screen_ids[]=$screen->id;
			return $screen_ids;
		}

		/**
		 * Define custom columns for coupons.
		 * @param  array $existing_columns
		 * @return array
		 */
		public function shop_campaign_columns( $existing_columns ) {
			$columns                = array();
			$columns['cb']          = $existing_columns['cb'];
			$columns['campaign_code'] = __( 'Code', 'woocommerce' );
			$columns['type']        = __( 'Campaign type', 'woocommerce' );
			$columns['amount']      = __( 'Campaign amount', 'woocommerce' );
			$columns['description'] = __( 'Description', 'woocommerce' );
			$columns['products']    = __( 'Product IDs', 'woocommerce' );
			$columns['usage']       = __( 'Usage / Limit', 'woocommerce' );
			$columns['expiry_date'] = __( 'Expiry date', 'woocommerce' );

			return $columns;
		}

		public function render_shop_campaign_columns( $column ) {
			include_once( WCC_ABSPATH . 'includes/class-wc-campaign.php' );
			global $post, $the_campaign;

			if ( empty( $the_campaign ) || $the_campaign->get_id() !== $post->ID ) {
				$the_campaign = new WC_Custom_Campaign( $post->ID );
			}

			switch ( $column ) {
				case 'campaign_code' :
					$edit_link = get_edit_post_link( $post->ID );
					$title     = $the_campaign->get_code();

					echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html( $title ) . '</a>';
					_post_states( $post );
					echo '</strong>';
					break;
				case 'type' :
					echo esc_html( wc_get_coupon_type( $the_campaign->get_discount_type() ) );
					break;
				case 'amount' :
					echo esc_html( wc_format_localized_price( $the_campaign->get_amount() ) );
					break;
				case 'products' :
					$product_ids = $the_campaign->get_product_ids();

					if ( sizeof( $product_ids ) > 0 ) {
						echo esc_html( implode( ', ', $product_ids ) );
					} else {
						echo '&ndash;';
					}
					break;
				case 'usage_limit' :
					$usage_limit = $the_campaign->get_usage_limit();

					if ( $usage_limit ) {
						echo esc_html( $usage_limit );
					} else {
						echo '&ndash;';
					}
					break;
				case 'usage' :
					$usage_count = $the_campaign->get_usage_count();
					$usage_limit = $the_campaign->get_usage_limit();

					/* translators: 1: count 2: limit */
					printf(
						__( '%1$s / %2$s', 'woocommerce' ),
						esc_html( $usage_count ),
						esc_html( $usage_limit ? $usage_limit : '&infin;' )
					);
					break;
				case 'expiry_date' :
					$expiry_date = $the_campaign->get_date_expires();

					if ( $expiry_date ) {
						echo esc_html( $expiry_date->date_i18n( 'F j, Y' ) );
					} else {
						echo '&ndash;';
					}
					break;
				case 'description' :
					echo wp_kses_post( $the_campaign->get_description() ? $the_campaign->get_description() : '&ndash;' );
					break;
			}
		}
		function add_theme_caps() {
			// gets the administrator role
			$admins = get_role( 'administrator' );



			$admins->add_cap("edit_shop_campaign");
			$admins->add_cap("read_shop_campaign");
			$admins->add_cap("delete_shop_campaign");
			$admins->add_cap("edit_shop_campaigns");
			$admins->add_cap("edit_others_shop_campaigns");
			$admins->add_cap("publish_shop_campaigns");
			$admins->add_cap("read_private_shop_campaigns");
			$admins->add_cap("read");
			$admins->add_cap("delete_shop_campaigns");
			$admins->add_cap("delete_private_shop_campaigns");
			$admins->add_cap("delete_published_shop_campaigns");
			$admins->add_cap("delete_others_shop_campaigns");
			$admins->add_cap("edit_private_shop_campaigns");
			$admins->add_cap("edit_published_shop_campaigns");
			$admins->add_cap("edit_shop_campaigns");

		}
//		function __construct() {
//			if(!function_exists('add_shortcode')) {
//				return;
//			}
//			add_shortcode( 'hello' , array(&$this, 'hello_func') );
//		}
//
//		function hello_func($atts = array(), $content = null) {
//			extract(shortcode_atts(array('name' => 'World'), $atts));
//			return '<div><p>Hello '.$name.'!!!</p></div>';
//		}
	}
}
//function mfpd_load() {
//	global $mfpd;
//	$mfpd = new Wc_Campain();
//}
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	$wc_campaign = new Wc_Campaign;
}
//add_action( 'plugins_loaded', 'mfpd_load' );
?>
