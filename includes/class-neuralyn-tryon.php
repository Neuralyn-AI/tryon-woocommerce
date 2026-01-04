<?php
/**
 * Main plugin class.
 *
 * @package Neuralyn_Tryon
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main Neuralyn_Tryon class.
 */
class Neuralyn_Tryon {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * Single instance of the class.
	 *
	 * @var Neuralyn_Tryon
	 */
	protected static $instance = null;

	/**
	 * Admin instance.
	 *
	 * @var Neuralyn_Tryon_Admin
	 */
	public $admin = null;

	/**
	 * Frontend instance.
	 *
	 * @var Neuralyn_Tryon_Frontend
	 */
	public $frontend = null;

	/**
	 * Customer instance.
	 *
	 * @var Neuralyn_Tryon_Customer
	 */
	public $customer = null;

	/**
	 * Available hooks for button placement.
	 *
	 * @var array
	 */
	public static $available_hooks = array(
		'woocommerce_single_product_summary'      => array(
			'label'       => 'Product Summary',
			'description' => 'Displayed in the product summary area, after the title.',
			'context'     => 'product',
		),
		'woocommerce_after_add_to_cart_button'    => array(
			'label'       => 'After Add to Cart Button',
			'description' => 'Displayed immediately after the Add to Cart button.',
			'context'     => 'product',
		),
		'woocommerce_product_thumbnails'          => array(
			'label'       => 'Product Thumbnails',
			'description' => 'Displayed in the product gallery thumbnails area.',
			'context'     => 'product',
		),
		'woocommerce_after_single_product_summary' => array(
			'label'       => 'After Product Summary',
			'description' => 'Displayed after the entire product summary section.',
			'context'     => 'product',
		),
		'woocommerce_product_tabs'                => array(
			'label'       => 'Product Tabs',
			'description' => 'Adds a new tab with the Try-On button.',
			'context'     => 'product',
		),
		'woocommerce_before_add_to_cart_form'     => array(
			'label'       => 'Before Add to Cart Form',
			'description' => 'Displayed before the add to cart form.',
			'context'     => 'product',
		),
		'woocommerce_after_shop_loop_item'        => array(
			'label'       => 'Shop Loop Item',
			'description' => 'Displayed after each product in shop/category listings.',
			'context'     => 'listing',
		),
	);

	/**
	 * Main instance.
	 *
	 * @return Neuralyn_Tryon
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init_hooks();
		$this->init_classes();
	}

	/**
	 * Initialize hooks.
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Initialize classes.
	 */
	private function init_classes() {
		$this->customer = new Neuralyn_Tryon_Customer();
		$this->admin    = new Neuralyn_Tryon_Admin();
		$this->frontend = new Neuralyn_Tryon_Frontend();
	}

	/**
	 * Init plugin.
	 */
	public function init() {
		// Additional initialization if needed.
	}

	/**
	 * Get license key.
	 *
	 * @return string
	 */
	public static function get_license_key() {
		return get_option( 'neuralyn_tryon_license_key', '' );
	}

	/**
	 * Get enabled hooks.
	 *
	 * @return array
	 */
	public static function get_enabled_hooks() {
		$hooks = get_option( 'neuralyn_tryon_hooks_enabled', array( 'woocommerce_single_product_summary' ) );
		return is_array( $hooks ) ? $hooks : array( 'woocommerce_single_product_summary' );
	}

	/**
	 * Check if a hook is enabled.
	 *
	 * @param string $hook_name Hook name.
	 * @return bool
	 */
	public static function is_hook_enabled( $hook_name ) {
		$enabled_hooks = self::get_enabled_hooks();
		return in_array( $hook_name, $enabled_hooks, true );
	}

	/**
	 * Get hook priority.
	 *
	 * @return int
	 */
	public static function get_hook_priority() {
		return absint( get_option( 'neuralyn_tryon_hook_priority', 25 ) );
	}

	/**
	 * Get buyer order statuses.
	 *
	 * @return array
	 */
	public static function get_buyer_order_statuses() {
		$statuses = get_option( 'neuralyn_tryon_buyer_order_statuses', array( 'completed', 'processing' ) );
		return is_array( $statuses ) ? $statuses : array( 'completed', 'processing' );
	}

	/**
	 * Check if current page is excluded.
	 *
	 * @return bool
	 */
	public static function is_excluded_page() {
		if ( is_cart() || is_checkout() || is_order_received_page() ) {
			return true;
		}

		// Check for order pay page.
		if ( is_wc_endpoint_url( 'order-pay' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if current page is a product page.
	 *
	 * @return bool
	 */
	public static function is_product_page() {
		return is_product();
	}

	/**
	 * Check if current page is a listing page.
	 *
	 * @return bool
	 */
	public static function is_listing_page() {
		return is_shop() || is_product_category() || is_product_tag() || is_search();
	}

	/**
	 * Get CDN URL.
	 *
	 * @return string
	 */
	public static function get_cdn_url() {
		return NEURALYN_TRYON_CDN_URL;
	}

	/**
	 * Get login URL.
	 *
	 * @return string
	 */
	public static function get_login_url() {
		return wc_get_page_permalink( 'myaccount' );
	}
}
