<?php
/**
 * Admin settings class.
 *
 * @package Neuralyn_Tryon
 */

defined( 'ABSPATH' ) || exit;

/**
 * Admin class.
 */
class Neuralyn_Tryon_Admin {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 50 );
		add_action( 'woocommerce_settings_tabs_neuralyn_tryon', array( $this, 'output_settings' ) );
		add_action( 'woocommerce_update_options_neuralyn_tryon', array( $this, 'save_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_filter( 'woocommerce_admin_settings_sanitize_option_neuralyn_tryon_license_key', array( $this, 'sanitize_license_key' ) );
	}

	/**
	 * Add settings tab.
	 *
	 * @param array $tabs Settings tabs.
	 * @return array
	 */
	public function add_settings_tab( $tabs ) {
		$tabs['neuralyn_tryon'] = __( 'Neuralyn TRYON', 'neuralyn-tryon' );
		return $tabs;
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Current admin page.
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( 'woocommerce_page_wc-settings' !== $hook ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['tab'] ) || 'neuralyn_tryon' !== $_GET['tab'] ) {
			return;
		}
	}

	/**
	 * Output settings.
	 */
	public function output_settings() {
		woocommerce_admin_fields( $this->get_settings() );
		$this->output_hooks_section();
		$this->output_order_statuses_section();
	}

	/**
	 * Save settings.
	 */
	public function save_settings() {
		woocommerce_update_options( $this->get_settings() );
		$this->save_hooks_settings();
		$this->save_order_statuses_settings();
	}

	/**
	 * Get settings fields.
	 *
	 * @return array
	 */
	private function get_settings() {
		$settings = array(
			// License section.
			array(
				'title' => __( 'License Settings', 'neuralyn-tryon' ),
				'type'  => 'title',
				'desc'  => sprintf(
					/* translators: %s: Dashboard URL */
					__( 'Enter your Neuralyn TRYON license key. You can find it in your <a href="%s" target="_blank">Neuralyn Dashboard</a>.', 'neuralyn-tryon' ),
					'https://www.neuralyn.com.br/dashboard'
				),
				'id'    => 'neuralyn_tryon_license_section',
			),
			array(
				'title'             => __( 'License Key', 'neuralyn-tryon' ),
				'type'              => 'text',
				'desc'              => __( 'Your Neuralyn TRYON license key (36 characters, no spaces).', 'neuralyn-tryon' ),
				'desc_tip'          => true,
				'id'                => 'neuralyn_tryon_license_key',
				'css'               => 'min-width: 400px;',
				'custom_attributes' => array(
					'maxlength' => '36',
				),
			),
			array(
				'type' => 'sectionend',
				'id'   => 'neuralyn_tryon_license_section',
			),
		);

		return $settings;
	}

	/**
	 * Output hooks section.
	 */
	private function output_hooks_section() {
		$enabled_hooks = Neuralyn_Tryon::get_enabled_hooks();
		?>
		<h2><?php esc_html_e( 'Button Placement', 'neuralyn-tryon' ); ?></h2>
		<p><?php esc_html_e( 'Select where the Try-On button should appear on your store.', 'neuralyn-tryon' ); ?></p>
		<table class="form-table neuralyn-tryon-hooks-table">
			<tbody>
				<?php foreach ( Neuralyn_Tryon::$available_hooks as $hook_name => $hook_data ) : ?>
					<tr>
						<th scope="row" class="titledesc">
							<label for="neuralyn_hook_<?php echo esc_attr( $hook_name ); ?>">
								<?php echo esc_html( $hook_data['label'] ); ?>
							</label>
						</th>
						<td class="forminp">
							<fieldset>
								<label>
									<input type="checkbox"
										name="neuralyn_tryon_hooks[]"
										id="neuralyn_hook_<?php echo esc_attr( $hook_name ); ?>"
										value="<?php echo esc_attr( $hook_name ); ?>"
										<?php checked( in_array( $hook_name, $enabled_hooks, true ) ); ?>
									/>
									<?php echo esc_html( $hook_data['description'] ); ?>
								</label>
								<span class="neuralyn-hook-context neuralyn-hook-context-<?php echo esc_attr( $hook_data['context'] ); ?>">
									<?php echo 'product' === $hook_data['context'] ? esc_html__( 'Product Page', 'neuralyn-tryon' ) : esc_html__( 'Shop Listing', 'neuralyn-tryon' ); ?>
								</span>
							</fieldset>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		wp_nonce_field( 'neuralyn_tryon_hooks_nonce', 'neuralyn_tryon_hooks_nonce' );
	}

	/**
	 * Save hooks settings.
	 */
	private function save_hooks_settings() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! isset( $_POST['neuralyn_tryon_hooks_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['neuralyn_tryon_hooks_nonce'] ), 'neuralyn_tryon_hooks_nonce' ) ) {
			return;
		}

		$hooks = array();
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( isset( $_POST['neuralyn_tryon_hooks'] ) && is_array( $_POST['neuralyn_tryon_hooks'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$hooks = array_map( 'sanitize_text_field', wp_unslash( $_POST['neuralyn_tryon_hooks'] ) );
			// Validate hooks against allowed list.
			$hooks = array_filter(
				$hooks,
				function ( $hook ) {
					return isset( Neuralyn_Tryon::$available_hooks[ $hook ] );
				}
			);
		}

		update_option( 'neuralyn_tryon_hooks_enabled', $hooks );
	}

	/**
	 * Output order statuses section.
	 */
	private function output_order_statuses_section() {
		$buyer_statuses = Neuralyn_Tryon::get_buyer_order_statuses();
		$order_statuses = wc_get_order_statuses();
		?>
		<h2><?php esc_html_e( 'Customer Classification', 'neuralyn-tryon' ); ?></h2>
		<p><?php esc_html_e( 'Select which order statuses qualify a customer as a "buyer". Customers with at least one order in these statuses will be classified as buyers.', 'neuralyn-tryon' ); ?></p>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row" class="titledesc">
						<label><?php esc_html_e( 'Buyer Order Statuses', 'neuralyn-tryon' ); ?></label>
					</th>
					<td class="forminp neuralyn-order-statuses">
						<fieldset>
							<?php foreach ( $order_statuses as $status_key => $status_name ) : ?>
								<?php
								// Remove 'wc-' prefix for comparison.
								$status_slug = str_replace( 'wc-', '', $status_key );
								?>
								<label class="neuralyn-status-checkbox">
									<input type="checkbox"
										name="neuralyn_tryon_buyer_statuses[]"
										value="<?php echo esc_attr( $status_slug ); ?>"
										<?php checked( in_array( $status_slug, $buyer_statuses, true ) ); ?>
									/>
									<?php echo esc_html( $status_name ); ?>
								</label>
							<?php endforeach; ?>
						</fieldset>
						<p class="description"><?php esc_html_e( 'Default: Completed and Processing orders.', 'neuralyn-tryon' ); ?></p>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		wp_nonce_field( 'neuralyn_tryon_statuses_nonce', 'neuralyn_tryon_statuses_nonce' );
	}

	/**
	 * Save order statuses settings.
	 */
	private function save_order_statuses_settings() {
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ! isset( $_POST['neuralyn_tryon_statuses_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['neuralyn_tryon_statuses_nonce'] ), 'neuralyn_tryon_statuses_nonce' ) ) {
			return;
		}

		$statuses = array();
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( isset( $_POST['neuralyn_tryon_buyer_statuses'] ) && is_array( $_POST['neuralyn_tryon_buyer_statuses'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$statuses = array_map( 'sanitize_text_field', wp_unslash( $_POST['neuralyn_tryon_buyer_statuses'] ) );
		}

		update_option( 'neuralyn_tryon_buyer_order_statuses', $statuses );
	}

	/**
	 * Sanitize license key.
	 *
	 * Removes whitespace and limits to 36 characters.
	 *
	 * @param string $value License key value.
	 * @return string Sanitized license key.
	 */
	public function sanitize_license_key( $value ) {
		// Remove all whitespace (spaces, tabs, newlines).
		$value = preg_replace( '/\s+/', '', $value );

		// Limit to 36 characters.
		$value = substr( $value, 0, 36 );

		return sanitize_text_field( $value );
	}
}
