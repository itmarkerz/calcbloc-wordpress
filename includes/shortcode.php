<?php
/**
 * The [calcbloc] shortcode.
 *
 * @package CalcBloc_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers [calcbloc country="uk" calculator="take-home-pay" theme="dark"].
 */
function calcbloc_calculators_register_shortcode() {
	add_shortcode( 'calcbloc', 'calcbloc_calculators_shortcode' );
}

/**
 * Shortcode callback.
 *
 * @param array|string $atts Shortcode attributes.
 * @return string HTML.
 */
function calcbloc_calculators_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'country'    => 'uk',
			'calculator' => 'take-home-pay',
			'theme'      => '',
		),
		$atts,
		'calcbloc'
	);
	return calcbloc_calculators_render(
		array(
			'country'    => sanitize_key( $atts['country'] ),
			'calculator' => sanitize_key( $atts['calculator'] ),
			'theme'      => sanitize_key( $atts['theme'] ),
		)
	);
}
