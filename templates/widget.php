<?php
/**
 * Widget/SDK loader template.
 *
 * @package Neuralyn_Tryon
 *
 * @var array $config SDK configuration.
 */

defined( 'ABSPATH' ) || exit;
?>
<script type="text/javascript">
	window.TRYON_CONFIG = <?php echo wp_json_encode( $config ); ?>;
</script>
<script src="<?php echo esc_url( NEURALYN_TRYON_CDN_URL . '/sdk.min.js' ); ?>" async></script>
