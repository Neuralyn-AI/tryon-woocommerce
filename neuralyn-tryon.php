<?php
/**
 * Plugin Name: Neuralyn TRYON Connector for WooCommerce
 * Plugin URI: https://www.neuralyn.com.br/en/products/tryon
 * Description: Virtual fitting room integration that allows customers to try on clothes virtually before purchasing using AI-powered body detection technology.
 * Version: 1.0.0
 * Author: Neuralyn
 * Author URI: https://www.neuralyn.com.br
 * License: Commercial
 * License URI: https://www.neuralyn.com.br/files/woocommerce/license.txt
 * Text Domain: neuralyn-tryon
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * WC requires at least: 7.0
 * WC tested up to: 9.0
 *
 * @package Neuralyn_Tryon
 */

defined( 'ABSPATH' ) || exit;

// Define plugin constants.
define( 'NEURALYN_TRYON_VERSION', '1.0.0' );
define( 'NEURALYN_TRYON_PLUGIN_FILE', __FILE__ );
define( 'NEURALYN_TRYON_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'NEURALYN_TRYON_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'NEURALYN_TRYON_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'NEURALYN_TRYON_CDN_URL', 'http://localhost:8222' );

/**
 * Check if WooCommerce is active.
 *
 * @return bool
 */
function neuralyn_tryon_is_woocommerce_active() {
	return class_exists( 'WooCommerce' );
}

/**
 * Display admin notice if WooCommerce is not active.
 */
function neuralyn_tryon_woocommerce_missing_notice() {
	?>
	<div class="notice notice-error">
		<p>
			<?php
			printf(
				/* translators: %s: WooCommerce plugin name */
				esc_html__( 'Neuralyn TRYON requires %s to be installed and active.', 'neuralyn-tryon' ),
				'<strong>WooCommerce</strong>'
			);
			?>
		</p>
	</div>
	<?php
}

/**
 * Initialize the plugin.
 */
function neuralyn_tryon_init() {
	// Check for WooCommerce.
	if ( ! neuralyn_tryon_is_woocommerce_active() ) {
		add_action( 'admin_notices', 'neuralyn_tryon_woocommerce_missing_notice' );
		return;
	}

	// Load text domain.
	load_plugin_textdomain( 'neuralyn-tryon', false, dirname( NEURALYN_TRYON_PLUGIN_BASENAME ) . '/languages' );

	// Include required files.
	require_once NEURALYN_TRYON_PLUGIN_DIR . 'includes/class-neuralyn-tryon.php';
	require_once NEURALYN_TRYON_PLUGIN_DIR . 'includes/class-neuralyn-tryon-customer.php';
	require_once NEURALYN_TRYON_PLUGIN_DIR . 'includes/class-neuralyn-tryon-admin.php';
	require_once NEURALYN_TRYON_PLUGIN_DIR . 'includes/class-neuralyn-tryon-frontend.php';

	// Initialize main plugin class.
	Neuralyn_Tryon::get_instance();
}
add_action( 'plugins_loaded', 'neuralyn_tryon_init' );

/**
 * Plugin activation hook.
 */
function neuralyn_tryon_activate() {
	// Set default options on activation.
	$defaults = array(
		'neuralyn_tryon_license_key'         => '',
		'neuralyn_tryon_hooks_enabled'       => array( 'woocommerce_single_product_summary' ),
		'neuralyn_tryon_hook_priority'       => 25,
		'neuralyn_tryon_buyer_order_statuses' => array( 'completed', 'processing' ),
	);

	foreach ( $defaults as $option => $value ) {
		if ( false === get_option( $option ) ) {
			update_option( $option, $value );
		}
	}

	// Flush rewrite rules.
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'neuralyn_tryon_activate' );

/**
 * Plugin deactivation hook.
 */
function neuralyn_tryon_deactivate() {
	// Flush rewrite rules.
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'neuralyn_tryon_deactivate' );

/**
 * Add settings link on plugins page.
 *
 * @param array $links Plugin action links.
 * @return array
 */
function neuralyn_tryon_plugin_action_links( $links ) {
	$settings_link = sprintf(
		'<a href="%s">%s</a>',
		admin_url( 'admin.php?page=wc-settings&tab=neuralyn_tryon' ),
		esc_html__( 'Settings', 'neuralyn-tryon' )
	);
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . NEURALYN_TRYON_PLUGIN_BASENAME, 'neuralyn_tryon_plugin_action_links' );

/**
 * Declare HPOS compatibility.
 */
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);
