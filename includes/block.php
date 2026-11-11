<?php
/**
 * The CalcBloc Calculator block (build/block, built from src/block with @wordpress/scripts).
 *
 * @package CalcBloc_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the block from its built block.json and hands the catalog to the editor script.
 */
function calcbloc_calculators_register_block() {
	$block = register_block_type( CALCBLOC_CALCULATORS_DIR . 'build/block' );
	if ( ! $block ) {
		return;
	}
	add_action( 'enqueue_block_editor_assets', 'calcbloc_calculators_editor_data' );
}

/**
 * Editor data: the catalog (refreshed from calcbloc.com at most every 12 hours) and the frame base URL.
 */
function calcbloc_calculators_editor_data() {
	calcbloc_calculators_refresh_catalog();
	$handle = generate_block_asset_handle( 'calcbloc/calculator', 'editorScript' );
	$data   = array(
		'catalog'      => calcbloc_calculators_get_catalog(),
		'base'         => calcbloc_calculators_base_url(),
		'defaultTheme' => calcbloc_calculators_default_theme(),
	);
	wp_add_inline_script( $handle, 'window.calcblocCalculators = ' . wp_json_encode( $data ) . ';', 'before' );
	wp_set_script_translations( $handle, 'calcbloc-calculators' );
}
