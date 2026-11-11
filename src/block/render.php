<?php
/**
 * Front-end markup for the CalcBloc Calculator block.
 *
 * @package CalcBloc_Calculators
 *
 * @var array $attributes Block attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$calcbloc_calculators_html = calcbloc_calculators_render(
	array(
		'country'    => isset( $attributes['country'] ) ? sanitize_key( $attributes['country'] ) : '',
		'calculator' => isset( $attributes['calculator'] ) ? sanitize_key( $attributes['calculator'] ) : '',
		'theme'      => isset( $attributes['theme'] ) ? sanitize_key( $attributes['theme'] ) : '',
	)
);

if ( '' !== $calcbloc_calculators_html ) {
	printf(
		'<div %1$s>%2$s</div>',
		get_block_wrapper_attributes(), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core.
		$calcbloc_calculators_html // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_* in calcbloc_calculators_render().
	);
}
