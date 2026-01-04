<?php
/**
 * Frontend rendering class.
 *
 * @package Neuralyn_Tryon
 */

defined( 'ABSPATH' ) || exit;

/**
 * Frontend class.
 */
class Neuralyn_Tryon_Frontend {

	/**
	 * Whether SDK has been rendered.
	 *
	 * @var bool
	 */
	private $sdk_rendered = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		add_action( 'wp_footer', array( $this, 'render_sdk' ), 5 );

		// Register hooks for button rendering.
		$this->register_button_hooks();
	}

	/**
	 * Register button hooks.
	 */
	private function register_button_hooks() {
		$priority = Neuralyn_Tryon::get_hook_priority();

		// Product page hooks.
		add_action( 'woocommerce_single_product_summary', array( $this, 'render_button' ), $priority );
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'render_button' ), $priority );
		add_action( 'woocommerce_product_thumbnails', array( $this, 'render_button' ), $priority );
		add_action( 'woocommerce_after_single_product_summary', array( $this, 'render_button' ), $priority );
		add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'render_button' ), $priority );

		// Shop loop hook.
		add_action( 'woocommerce_after_shop_loop_item', array( $this, 'render_button' ), $priority );

		// Product tabs hook.
		add_filter( 'woocommerce_product_tabs', array( $this, 'add_product_tab' ), $priority );
	}

	/**
	 * Enqueue frontend assets.
	 */
	public function enqueue_frontend_assets() {
		if ( ! $this->should_load_assets() ) {
			return;
		}
	}

	/**
	 * Check if assets should be loaded.
	 *
	 * @return bool
	 */
	private function should_load_assets() {
		// Don't load on excluded pages.
		if ( Neuralyn_Tryon::is_excluded_page() ) {
			return false;
		}

		// Load on product pages.
		if ( Neuralyn_Tryon::is_product_page() ) {
			return true;
		}

		// Check if any listing hook is enabled.
		if ( Neuralyn_Tryon::is_listing_page() ) {
			foreach ( Neuralyn_Tryon::$available_hooks as $hook_name => $hook_data ) {
				if ( 'listing' === $hook_data['context'] && Neuralyn_Tryon::is_hook_enabled( $hook_name ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Render SDK in footer.
	 */
	public function render_sdk() {
		// Only render once.
		if ( $this->sdk_rendered ) {
			return;
		}

		// Check if we should load.
		if ( ! $this->should_load_assets() ) {
			return;
		}

		// Check for license key.
		$license_key = Neuralyn_Tryon::get_license_key();
		if ( empty( $license_key ) ) {
			return;
		}

		$this->sdk_rendered = true;

		// Get customer data.
		$customer      = Neuralyn_Tryon::get_instance()->customer;
		$customer_data = $customer->get_sdk_customer_data();

		$config = array(
			'licenseKey'   => $license_key,
			'customerId'   => $customer_data['customerId'],
			'customerUUID' => $customer_data['customerUUID'],
			'customerType' => $customer_data['customerType'],
			'loginUrl'     => Neuralyn_Tryon::get_login_url(),
			'platform'     => 'woocommerce',
		);

		include NEURALYN_TRYON_PLUGIN_DIR . 'templates/widget.php';
	}

	/**
	 * Render button.
	 */
	public function render_button() {
		// Get current hook.
		$current_hook = current_filter();

		// Check if this hook is enabled.
		if ( ! Neuralyn_Tryon::is_hook_enabled( $current_hook ) ) {
			return;
		}

		// Don't render on excluded pages.
		if ( Neuralyn_Tryon::is_excluded_page() ) {
			return;
		}

		// Check context.
		if ( isset( Neuralyn_Tryon::$available_hooks[ $current_hook ] ) ) {
			$hook_data = Neuralyn_Tryon::$available_hooks[ $current_hook ];

			// Product page hooks should only show on product pages.
			if ( 'product' === $hook_data['context'] && ! Neuralyn_Tryon::is_product_page() ) {
				return;
			}

			// Listing hooks should only show on listing pages.
			if ( 'listing' === $hook_data['context'] && ! Neuralyn_Tryon::is_listing_page() ) {
				return;
			}
		}

		// Check for license key.
		$license_key = Neuralyn_Tryon::get_license_key();
		if ( empty( $license_key ) ) {
			return;
		}

		include NEURALYN_TRYON_PLUGIN_DIR . 'templates/button.php';
	}

	/**
	 * Add product tab.
	 *
	 * @param array $tabs Existing tabs.
	 * @return array
	 */
	public function add_product_tab( $tabs ) {
		// Check if tab hook is enabled.
		if ( ! Neuralyn_Tryon::is_hook_enabled( 'woocommerce_product_tabs' ) ) {
			return $tabs;
		}

		// Check for license key.
		$license_key = Neuralyn_Tryon::get_license_key();
		if ( empty( $license_key ) ) {
			return $tabs;
		}

		$tabs['neuralyn_tryon'] = array(
			'title'    => __( 'Virtual Try-On', 'neuralyn-tryon' ),
			'priority' => 50,
			'callback' => array( $this, 'render_product_tab_content' ),
		);

		return $tabs;
	}

	/**
	 * Render product tab content.
	 */
	public function render_product_tab_content() {
		include NEURALYN_TRYON_PLUGIN_DIR . 'templates/tab.php';
	}
}
