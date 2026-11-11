<?php
/**
 * The list of countries and calculators the widget offers.
 *
 * A copy ships in assets/catalog.json. When an administrator opens the block editor or the settings page, the
 * plugin refreshes it from https://calcbloc.com/embed/catalog.json at most every 12 hours, so new calculators
 * appear without a plugin update. Front-end page views never wait for a remote request.
 *
 * @package CalcBloc_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base URL of the CalcBloc site. Filterable for staging copies.
 *
 * @return string URL without a trailing slash.
 */
function calcbloc_calculators_base_url() {
	/**
	 * Filters the CalcBloc base URL used for widget.js, the frames and the catalog.
	 *
	 * @param string $url Default https://calcbloc.com.
	 */
	$url = apply_filters( 'calcbloc_calculators_base_url', 'https://calcbloc.com' );
	return untrailingslashit( esc_url_raw( $url ) );
}

/**
 * Checks that a decoded catalog has the expected shape and keeps only clean values.
 *
 * @param mixed $data Decoded JSON.
 * @return array|null List of countries, or null when the data is unusable.
 */
function calcbloc_calculators_clean_catalog( $data ) {
	if ( ! is_array( $data ) || empty( $data['countries'] ) || ! is_array( $data['countries'] ) ) {
		return null;
	}
	$countries = array();
	foreach ( $data['countries'] as $country ) {
		if ( ! is_array( $country ) || empty( $country['id'] ) || empty( $country['calculators'] ) || ! is_array( $country['calculators'] ) ) {
			continue;
		}
		$calculators = array();
		foreach ( $country['calculators'] as $calc ) {
			if ( ! is_array( $calc ) || empty( $calc['type'] ) || empty( $calc['slug'] ) || empty( $calc['url'] ) ) {
				continue;
			}
			$calculators[] = array(
				'type'     => sanitize_key( $calc['type'] ),
				'slug'     => sanitize_key( $calc['slug'] ),
				'title'    => sanitize_text_field( isset( $calc['title'] ) ? $calc['title'] : $calc['slug'] ),
				'url'      => esc_url_raw( $calc['url'] ),
				'frameUrl' => isset( $calc['frameUrl'] ) ? esc_url_raw( $calc['frameUrl'] ) : '',
			);
		}
		if ( $calculators ) {
			$countries[] = array(
				'id'          => sanitize_key( $country['id'] ),
				'name'        => sanitize_text_field( isset( $country['name'] ) ? $country['name'] : $country['id'] ),
				'code'        => isset( $country['code'] ) ? sanitize_key( $country['code'] ) : '',
				'calculators' => $calculators,
			);
		}
	}
	return $countries ? $countries : null;
}

/**
 * The catalog: the last copy fetched from calcbloc.com, or the bundled one.
 *
 * @return array List of countries with their calculators.
 */
function calcbloc_calculators_get_catalog() {
	static $catalog = null;
	if ( null !== $catalog ) {
		return $catalog;
	}
	$stored = get_option( 'calcbloc_calculators_catalog' );
	$clean  = is_array( $stored ) ? calcbloc_calculators_clean_catalog( array( 'countries' => $stored ) ) : null;
	if ( ! $clean ) {
		$file  = CALCBLOC_CALCULATORS_DIR . 'assets/catalog.json';
		$json  = is_readable( $file ) ? file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local bundled file.
		$clean = calcbloc_calculators_clean_catalog( json_decode( (string) $json, true ) );
	}
	$catalog = $clean ? $clean : array();
	return $catalog;
}

/**
 * Refreshes the stored catalog from calcbloc.com, at most every 12 hours. Admin screens only.
 */
function calcbloc_calculators_refresh_catalog() {
	if ( get_transient( 'calcbloc_calculators_catalog_checked' ) ) {
		return;
	}
	set_transient( 'calcbloc_calculators_catalog_checked', 1, 12 * HOUR_IN_SECONDS );
	$response = wp_remote_get(
		calcbloc_calculators_base_url() . '/embed/catalog.json',
		array(
			'timeout'    => 5,
			'user-agent' => 'CalcBloc-WordPress/' . CALCBLOC_CALCULATORS_VERSION . '; ' . home_url( '/' ),
		)
	);
	if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
		return;
	}
	$clean = calcbloc_calculators_clean_catalog( json_decode( wp_remote_retrieve_body( $response ), true ) );
	if ( $clean ) {
		update_option( 'calcbloc_calculators_catalog', $clean, false );
	}
}

/**
 * Finds a calculator by country (hub slug or ISO code) and calculator (short name or full slug).
 *
 * @param string $country    Country, e.g. "uk", "gb", "new-zealand".
 * @param string $calculator Calculator, e.g. "take-home-pay" or "take-home-pay-calculator".
 * @return array|null array( 'country' => ..., 'calculator' => ... ) or null.
 */
function calcbloc_calculators_find( $country, $calculator ) {
	$country    = sanitize_key( $country );
	$calculator = sanitize_key( $calculator );
	foreach ( calcbloc_calculators_get_catalog() as $entry ) {
		if ( $entry['id'] !== $country && $entry['code'] !== $country && ! ( 'uk' === $country && 'gb' === $entry['code'] ) ) {
			continue;
		}
		foreach ( $entry['calculators'] as $calc ) {
			if ( $calc['type'] === $calculator || $calc['slug'] === $calculator ) {
				return array(
					'country'    => $entry,
					'calculator' => $calc,
				);
			}
		}
	}
	return null;
}
