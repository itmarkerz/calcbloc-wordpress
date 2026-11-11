<?php
/**
 * Plugin Name:       CalcBloc Calculators
 * Plugin URI:        https://calcbloc.com/embed
 * Description:       Add free take-home pay, salary and income tax calculators from CalcBloc to any post or page with a shortcode or a block.
 * Version:           1.0.0
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            itmarkerz technologies
 * Author URI:        https://itmarkerz.co.in
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       calcbloc-calculators
 *
 * @package CalcBloc_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CALCBLOC_CALCULATORS_VERSION', '1.0.0' );
define( 'CALCBLOC_CALCULATORS_FILE', __FILE__ );
define( 'CALCBLOC_CALCULATORS_DIR', plugin_dir_path( __FILE__ ) );

require_once CALCBLOC_CALCULATORS_DIR . 'includes/catalog.php';
require_once CALCBLOC_CALCULATORS_DIR . 'includes/render.php';
require_once CALCBLOC_CALCULATORS_DIR . 'includes/shortcode.php';
require_once CALCBLOC_CALCULATORS_DIR . 'includes/block.php';
require_once CALCBLOC_CALCULATORS_DIR . 'includes/settings.php';

add_action( 'init', 'calcbloc_calculators_register_shortcode' );
add_action( 'init', 'calcbloc_calculators_register_block' );
add_action( 'admin_init', 'calcbloc_calculators_register_settings' );
add_action( 'admin_menu', 'calcbloc_calculators_add_settings_page' );
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'calcbloc_calculators_action_links' );
