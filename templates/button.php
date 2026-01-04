<?php
/**
 * Button template.
 *
 * @package Neuralyn_Tryon
 *
 * @var string $button_style Button style class.
 * @var bool   $button_float_right Whether button should float right.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="neuralyn-tryon-button-wrapper">
	<button type="button" class="neuralyn-tryon-app-button neuralyn-tryon-app-button-default">
		<span class="neuralyn-tryon-button-icon">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
				<path d="M12 3l1.912 5.813a2 2 0 0 0 1.275 1.275L21 12l-5.813 1.912a2 2 0 0 0-1.275 1.275L12 21l-1.912-5.813a2 2 0 0 0-1.275-1.275L3 12l5.813-1.912a2 2 0 0 0 1.275-1.275L12 3z"/>
			</svg>
		</span>
		<span class="neuralyn-tryon-button-text"><?php esc_html_e( 'Try-On', 'neuralyn-tryon' ); ?></span>
	</button>
</div>
