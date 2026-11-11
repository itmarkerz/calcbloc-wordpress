<?php
/**
 * Shared markup for the shortcode and the block: the same snippet calcbloc.com/embed gives site owners.
 *
 * @package CalcBloc_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the CalcBloc loader script. It is enqueued only on pages that contain a calculator.
 */
function calcbloc_calculators_register_script() {
	if ( wp_script_is( 'calcbloc-widget', 'registered' ) ) {
		return;
	}
	wp_register_script(
		'calcbloc-widget',
		calcbloc_calculators_base_url() . '/widget.js',
		array(),
		CALCBLOC_CALCULATORS_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'async',
		)
	);
}

/**
 * The "Powered by CalcBloc" credit link: the calculator page from the catalog plus a referral tag naming this plugin,
 * so CalcBloc can see which product a visit came from. The tag carries nothing about the site or its visitors.
 *
 * @param string $url Calculator page URL from the catalog.
 * @return string The URL with utm_source=calcbloc-wordpress, utm_medium=app and utm_campaign=itmarkerz-oss.
 */
function calcbloc_calculators_credit_url( $url ) {
	$url = (string) $url;
	if ( false !== strpos( $url, 'utm_source=' ) ) {
		return $url;
	}
	$parts  = explode( '#', $url, 2 );
	$tagged = $parts[0] . ( false === strpos( $parts[0], '?' ) ? '?' : '&' ) . 'utm_source=calcbloc-wordpress&utm_medium=app&utm_campaign=itmarkerz-oss';
	return isset( $parts[1] ) ? $tagged . '#' . $parts[1] : $tagged;
}

/**
 * Default colour scheme from the settings page.
 *
 * @return string "light" or "dark".
 */
function calcbloc_calculators_default_theme() {
	$options = get_option( 'calcbloc_calculators_options', array() );
	return ( is_array( $options ) && isset( $options['theme'] ) && 'dark' === $options['theme'] ) ? 'dark' : 'light';
}

/**
 * Renders one calculator widget.
 *
 * @param array $args Widget options: country (hub slug or ISO code, e.g. "uk"), calculator (short name or slug,
 *                    e.g. "take-home-pay") and theme ("light", "dark" or "" for the site default).
 * @return string HTML.
 */
function calcbloc_calculators_render( $args ) {
	$args  = wp_parse_args(
		$args,
		array(
			'country'    => '',
			'calculator' => '',
			'theme'      => '',
		)
	);
	$found = calcbloc_calculators_find( (string) $args['country'], (string) $args['calculator'] );
	if ( ! $found ) {
		if ( current_user_can( 'edit_posts' ) ) {
			return '<p class="calcbloc-error">' . esc_html(
				sprintf(
					/* translators: 1: country value, 2: calculator value. */
					__( 'CalcBloc: no calculator "%2$s" for country "%1$s". Check the names in Settings > CalcBloc.', 'calcbloc-calculators' ),
					(string) $args['country'],
					(string) $args['calculator']
				)
			) . '</p>';
		}
		return '';
	}

	$theme = in_array( $args['theme'], array( 'light', 'dark' ), true ) ? $args['theme'] : calcbloc_calculators_default_theme();
	$calc  = $found['calculator'];
	$frame = calcbloc_calculators_base_url() . '/embed/' . $calc['slug'] . ( 'dark' === $theme ? '?theme=dark' : '' );

	calcbloc_calculators_register_script();
	wp_enqueue_script( 'calcbloc-widget' );

	$html  = '<div class="calcbloc-widget" data-country="' . esc_attr( $found['country']['id'] ) . '" data-calculator="' . esc_attr( $calc['type'] ) . '"';
	$html .= 'dark' === $theme ? ' data-theme="dark"' : '';
	$html .= '>';
	$html .= '<noscript><iframe src="' . esc_url( $frame ) . '" title="' . esc_attr( $calc['title'] ) . '" loading="lazy" style="width:100%;height:820px;border:0"></iframe></noscript>';
	$html .= '<p class="calcbloc-credit">' . esc_html__( 'Powered by', 'calcbloc-calculators' ) . ' <a href="' . esc_url( calcbloc_calculators_credit_url( $calc['url'] ) ) . '" target="_blank" rel="noopener">CalcBloc</a></p>';
	$html .= '</div>';
	return $html;
}
