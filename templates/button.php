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
	<button type="button" class="neuralyn-tryon-app-button neuralyn-tryon-app-button-default" styles="display: none;">
<svg FILL="currentColor" version="1.0" xmlns="http://www.w3.org/2000/svg"
    width="15.000000pt" height="10.000000pt" viewBox="0 0 50.000000 54.000000"
    preserveAspectRatio="xMidYMid meet">
        <g transform="translate(0.000000,54.000000) scale(0.100000,-0.100000)"
        stroke="none">
        <path d="M396 514 c-3 -12 -16 -26 -29 -32 -17 -8 -18 -11 -5 -11 10 -1 24
        -13 31 -28 14 -25 15 -26 21 -6 4 11 16 25 29 30 l22 10 -21 7 c-12 3 -26 16
        -31 29 l-10 22 -7 -21z"/>
        <path d="M193 450 c-21 -87 -60 -126 -158 -156 -24 -7 -34 -14 -25 -17 123
        -40 159 -70 188 -156 l19 -56 13 45 c19 73 48 110 104 136 28 13 59 24 69 24
        33 0 17 17 -29 30 -84 24 -144 99 -153 193 -1 4 -4 7 -9 7 -4 0 -13 -23 -19
        -50z"/>
        <path d="M416 167 c-3 -14 -19 -31 -37 -39 l-31 -15 27 -7 c17 -4 31 -18 39
        -38 l13 -30 7 26 c4 17 18 32 37 40 28 12 29 14 11 18 -12 3 -30 20 -40 38
        l-20 31 -6 -24z"/>
        </g>
    </svg>
		<span class="neuralyn-tryon-button-text"><?php esc_html_e( 'Try-On', 'neuralyn-tryon' ); ?></span>
	</button>
</div>
