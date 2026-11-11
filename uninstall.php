<?php
/**
 * Removes everything the plugin stored: its option, the cached catalog and the refresh marker.
 *
 * @package CalcBloc_Calculators
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Deletes the plugin's data for the current site.
 */
function calcbloc_calculators_uninstall_site() {
	delete_option( 'calcbloc_calculators_options' );
	delete_option( 'calcbloc_calculators_catalog' );
	delete_transient( 'calcbloc_calculators_catalog_checked' );
}

if ( is_multisite() ) {
	$calcbloc_calculators_sites = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);
	foreach ( $calcbloc_calculators_sites as $calcbloc_calculators_site_id ) {
		switch_to_blog( $calcbloc_calculators_site_id );
		calcbloc_calculators_uninstall_site();
		restore_current_blog();
	}
} else {
	calcbloc_calculators_uninstall_site();
}
