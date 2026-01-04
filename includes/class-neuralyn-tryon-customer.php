<?php
/**
 * Customer classification and UUID management.
 *
 * @package Neuralyn_Tryon
 */

defined( 'ABSPATH' ) || exit;

/**
 * Customer class.
 */
class Neuralyn_Tryon_Customer {

	/**
	 * User meta key for UUID.
	 */
	const UUID_META_KEY = 'neuralyn_tryon_uuid';

	/**
	 * Cache group.
	 */
	const CACHE_GROUP = 'neuralyn_tryon';

	/**
	 * Customer type constants.
	 */
	const TYPE_GUEST      = 'guest';
	const TYPE_REGISTERED = 'registered';
	const TYPE_BUYER      = 'buyer';

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Nothing to initialize.
	}

	/**
	 * Get customer ID.
	 *
	 * @return int Customer ID or 0 if not logged in.
	 */
	public function get_customer_id() {
		return get_current_user_id();
	}

	/**
	 * Get customer UUID.
	 *
	 * @param int $customer_id Customer ID. Default current user.
	 * @return string UUID or empty string for guests.
	 */
	public function get_customer_uuid( $customer_id = 0 ) {
		if ( ! $customer_id ) {
			$customer_id = $this->get_customer_id();
		}

		if ( ! $customer_id ) {
			return '';
		}

		// Try cache first.
		$cache_key = 'uuid_' . $customer_id;
		$uuid      = wp_cache_get( $cache_key, self::CACHE_GROUP );

		if ( false !== $uuid ) {
			return $uuid;
		}

		// Try user meta.
		$uuid = get_user_meta( $customer_id, self::UUID_META_KEY, true );

		if ( empty( $uuid ) ) {
			// Generate new UUID.
			$uuid = $this->generate_uuid_v4();
			update_user_meta( $customer_id, self::UUID_META_KEY, $uuid );
		}

		// Cache it.
		wp_cache_set( $cache_key, $uuid, self::CACHE_GROUP, HOUR_IN_SECONDS );

		return $uuid;
	}

	/**
	 * Generate UUID v4.
	 *
	 * @return string UUID v4.
	 */
	private function generate_uuid_v4() {
		$data = random_bytes( 16 );

		// Set version to 0100 (UUID v4).
		$data[6] = chr( ( ord( $data[6] ) & 0x0f ) | 0x40 );
		// Set bits 6-7 to 10 (UUID variant).
		$data[8] = chr( ( ord( $data[8] ) & 0x3f ) | 0x80 );

		return vsprintf(
			'%s%s-%s-%s-%s-%s%s%s',
			str_split( bin2hex( $data ), 4 )
		);
	}

	/**
	 * Get customer type.
	 *
	 * @param int $customer_id Customer ID. Default current user.
	 * @return string Customer type (guest, registered, or buyer).
	 */
	public function get_customer_type( $customer_id = 0 ) {
		if ( ! $customer_id ) {
			$customer_id = $this->get_customer_id();
		}

		// Not logged in = guest.
		if ( ! $customer_id ) {
			return self::TYPE_GUEST;
		}

		// Try cache first.
		$cache_key     = 'type_' . $customer_id;
		$customer_type = wp_cache_get( $cache_key, self::CACHE_GROUP );

		if ( false !== $customer_type ) {
			return $customer_type;
		}

		// Default to registered.
		$customer_type = self::TYPE_REGISTERED;

		// Check if customer has qualifying orders.
		if ( $this->has_qualifying_orders( $customer_id ) ) {
			$customer_type = self::TYPE_BUYER;
		}

		// Cache it.
		wp_cache_set( $cache_key, $customer_type, self::CACHE_GROUP, HOUR_IN_SECONDS );

		return $customer_type;
	}

	/**
	 * Check if customer has qualifying orders.
	 *
	 * @param int $customer_id Customer ID.
	 * @return bool
	 */
	private function has_qualifying_orders( $customer_id ) {
		$buyer_statuses = Neuralyn_Tryon::get_buyer_order_statuses();

		if ( empty( $buyer_statuses ) ) {
			return false;
		}

		// Prefix statuses with 'wc-' for WooCommerce order query.
		$prefixed_statuses = array_map(
			function ( $status ) {
				return 'wc-' . $status;
			},
			$buyer_statuses
		);

		$args = array(
			'customer_id' => $customer_id,
			'status'      => $prefixed_statuses,
			'limit'       => 1,
			'return'      => 'ids',
		);

		$orders = wc_get_orders( $args );

		return ! empty( $orders );
	}

	/**
	 * Clear customer cache.
	 *
	 * @param int $customer_id Customer ID.
	 */
	public function clear_cache( $customer_id ) {
		wp_cache_delete( 'uuid_' . $customer_id, self::CACHE_GROUP );
		wp_cache_delete( 'type_' . $customer_id, self::CACHE_GROUP );
	}

	/**
	 * Get customer data for SDK config.
	 *
	 * @return array Customer data.
	 */
	public function get_sdk_customer_data() {
		$customer_id = $this->get_customer_id();

		return array(
			'customerId'   => $customer_id ? (string) $customer_id : '',
			'customerUUID' => $this->get_customer_uuid( $customer_id ),
			'customerType' => $this->get_customer_type( $customer_id ),
		);
	}
}
