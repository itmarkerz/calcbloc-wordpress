<?php
/**
 * Minimal harness: runs the plugin's shortcode and block rendering outside WordPress with stub core functions,
 * checks the markup, and can write a demo page.
 *
 * Usage:
 *   php tests/harness.php                              run the checks
 *   php tests/harness.php --demo=out.html [--base=URL] also write a demo page (base URL of the CalcBloc site)
 *
 * @package CalcBloc_Calculators
 */

// phpcs:disable

define( 'ABSPATH', __DIR__ . '/' );
define( 'HOUR_IN_SECONDS', 3600 );

$GLOBALS['cb_filters']    = array();
$GLOBALS['cb_options']    = array();
$GLOBALS['cb_transients'] = array();
$GLOBALS['cb_scripts']    = array();
$GLOBALS['cb_enqueued']   = array();
$GLOBALS['cb_shortcodes'] = array();
$GLOBALS['cb_blocks']     = array();
$GLOBALS['cb_can_edit']   = false;

function plugin_dir_path( $file ) { return rtrim( dirname( $file ), '/\\' ) . '/'; }
function plugin_basename( $file ) { return basename( dirname( $file ) ) . '/' . basename( $file ); }
function add_action( $hook, $cb ) { $GLOBALS['cb_filters'][ $hook ][] = $cb; }
function add_filter( $hook, $cb ) { $GLOBALS['cb_filters'][ $hook ][] = $cb; }
function do_action( $hook ) { foreach ( $GLOBALS['cb_filters'][ $hook ] ?? array() as $cb ) { $cb(); } }
function apply_filters( $hook, $value ) { foreach ( $GLOBALS['cb_filters'][ $hook ] ?? array() as $cb ) { $value = $cb( $value ); } return $value; }
function untrailingslashit( $s ) { return rtrim( $s, '/\\' ); }
function esc_url_raw( $u ) { return preg_match( '#^https?://#i', (string) $u ) ? str_replace( array( '"', "'", '<', '>', ' ' ), '', (string) $u ) : ''; }
function esc_url( $u ) { return htmlspecialchars( esc_url_raw( $u ), ENT_QUOTES ); }
function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function __( $s ) { return $s; }
function esc_html__( $s ) { return esc_html( $s ); }
function sanitize_key( $k ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $k ) ); }
function sanitize_text_field( $s ) { return trim( strip_tags( (string) $s ) ); }
function get_option( $k, $d = false ) { return $GLOBALS['cb_options'][ $k ] ?? $d; }
function update_option( $k, $v ) { $GLOBALS['cb_options'][ $k ] = $v; return true; }
function get_transient( $k ) { return $GLOBALS['cb_transients'][ $k ] ?? false; }
function set_transient( $k, $v ) { $GLOBALS['cb_transients'][ $k ] = $v; return true; }
function wp_parse_args( $a, $d ) { return array_merge( $d, (array) $a ); }
function current_user_can() { return $GLOBALS['cb_can_edit']; }
function wp_script_is( $h ) { return isset( $GLOBALS['cb_scripts'][ $h ] ); }
function wp_register_script( $h, $src, $deps, $ver, $args ) { $GLOBALS['cb_scripts'][ $h ] = compact( 'src', 'ver', 'args' ); }
function wp_enqueue_script( $h ) { $GLOBALS['cb_enqueued'][ $h ] = true; }
function add_shortcode( $tag, $cb ) { $GLOBALS['cb_shortcodes'][ $tag ] = $cb; }
function shortcode_atts( $pairs, $atts ) { $atts = (array) $atts; $out = array(); foreach ( $pairs as $k => $v ) { $out[ $k ] = array_key_exists( $k, $atts ) ? $atts[ $k ] : $v; } return $out; }
function register_block_type( $path ) { $GLOBALS['cb_blocks'][] = $path; return is_readable( $path . '/block.json' ) ? (object) array( 'name' => 'calcbloc/calculator' ) : false; }
function get_block_wrapper_attributes() { return 'class="wp-block-calcbloc-calculator"'; }
function home_url() { return 'https://example.test/'; }
function is_wp_error() { return false; }

require dirname( __DIR__ ) . '/calcbloc-calculators.php';

$args = getopt( '', array( 'demo::', 'base::' ) );
if ( ! empty( $args['base'] ) ) {
	add_filter( 'calcbloc_calculators_base_url', function () use ( $args ) { return $args['base']; } );
}
do_action( 'init' );

$failures = 0;
function check( $ok, $label ) {
	global $failures;
	echo ( $ok ? 'ok   ' : 'FAIL ' ) . $label . PHP_EOL;
	if ( ! $ok ) { $failures++; }
}

function shortcode( $atts ) { return call_user_func( $GLOBALS['cb_shortcodes']['calcbloc'], $atts ); }
function block( $attributes ) { ob_start(); include dirname( __DIR__ ) . '/build/block/render.php'; return ob_get_clean(); }

check( isset( $GLOBALS['cb_shortcodes']['calcbloc'] ), 'registers the [calcbloc] shortcode' );
check( (bool) register_block_type( dirname( __DIR__ ) . '/build/block' ), 'built block (build/block/block.json) is present' );
check( count( calcbloc_calculators_get_catalog() ) >= 10, 'bundled catalog loads (' . count( calcbloc_calculators_get_catalog() ) . ' countries)' );

$uk = shortcode( array( 'country' => 'uk', 'calculator' => 'take-home-pay' ) );
check( false !== strpos( $uk, '<div class="calcbloc-widget" data-country="uk" data-calculator="take-home-pay">' ), 'shortcode renders the widget div' );
check( false !== strpos( $uk, 'Powered by <a href="https://calcbloc.com/uk/take-home-pay-calculator?utm_source=calcbloc-wordpress&amp;utm_medium=app&amp;utm_campaign=itmarkerz-oss" target="_blank" rel="noopener">CalcBloc</a>' ), 'shortcode renders the visible credit link to the calculator page, with the referral tag' );
check( 'https://calcbloc.com/a?x=1&utm_source=calcbloc-wordpress&utm_medium=app&utm_campaign=itmarkerz-oss#s' === calcbloc_calculators_credit_url( 'https://calcbloc.com/a?x=1#s' ), 'referral tag goes before the fragment and after an existing query' );
check( 'https://calcbloc.com/a?utm_source=calcbloc-wordpress&utm_medium=app&utm_campaign=itmarkerz-oss' === calcbloc_calculators_credit_url( 'https://calcbloc.com/a?utm_source=calcbloc-wordpress&utm_medium=app&utm_campaign=itmarkerz-oss' ), 'referral tag is not added twice' );
check( ! preg_match( '/nofollow|display:\s*none|visibility:\s*hidden/', $uk ), 'credit link is plain (no nofollow, not hidden)' );
check( ! empty( $GLOBALS['cb_enqueued']['calcbloc-widget'] ), 'enqueues widget.js only when a calculator renders' );
check( 'async' === $GLOBALS['cb_scripts']['calcbloc-widget']['args']['strategy'] && $GLOBALS['cb_scripts']['calcbloc-widget']['args']['in_footer'], 'widget.js loads async in the footer' );

$gb = shortcode( array( 'country' => 'GB', 'calculator' => 'take-home-pay-calculator', 'theme' => 'dark' ) );
check( false !== strpos( $gb, 'data-country="uk"' ) && false !== strpos( $gb, 'data-theme="dark"' ), 'accepts ISO codes, full slugs and theme="dark"' );

$evil = shortcode( array( 'country' => '"><script>alert(1)</script>', 'calculator' => 'take-home-pay' ) );
check( '' === $evil, 'unknown or hostile values render nothing for visitors' );
$GLOBALS['cb_can_edit'] = true;
$evil = shortcode( array( 'country' => 'uk', 'calculator' => '"><img src=x onerror=alert(1)>' ) );
check( false !== strpos( $evil, 'calcbloc-error' ) && false === strpos( $evil, '<img' ), 'editors see an escaped error message' );
$GLOBALS['cb_can_edit'] = false;

$blk = block( array( 'country' => 'new-zealand', 'calculator' => 'paye', 'theme' => '' ) );
check( false !== strpos( $blk, 'wp-block-calcbloc-calculator' ) && false !== strpos( $blk, 'data-calculator="paye"' ), 'block renders the same markup in its wrapper' );

$GLOBALS['cb_options']['calcbloc_calculators_options'] = array( 'theme' => 'dark' );
check( false !== strpos( shortcode( array( 'country' => 'us', 'calculator' => 'paycheck' ) ), 'data-theme="dark"' ), 'default colour scheme setting applies' );
check( array( 'theme' => 'light' ) === calcbloc_calculators_sanitize_options( array( 'theme' => '<b>' ) ), 'settings are sanitised' );
unset( $GLOBALS['cb_options']['calcbloc_calculators_options'] );

if ( ! empty( $args['demo'] ) ) {
	$script = $GLOBALS['cb_scripts']['calcbloc-widget'];
	$src    = esc_url( $script['src'] . '?ver=' . $script['ver'] );
	$html   = '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>WordPress plugin render (harness)</title>'
		. '<style>body{margin:0;font:17px/1.7 -apple-system,Segoe UI,sans-serif;background:#fff;color:#1e1e1e}main{max-width:720px;margin:0 auto;padding:16px}code{background:#f0f0f1;padding:2px 4px}</style></head><body><main>'
		. '<h1>Shortcode and block output from the plugin</h1>'
		. '<p>Rendered by <code>tests/harness.php</code> with the plugin\'s own PHP; WordPress core functions are stubbed.</p>'
		. '<h2><code>[calcbloc country="uk" calculator="take-home-pay"]</code></h2>' . shortcode( array( 'country' => 'uk', 'calculator' => 'take-home-pay' ) )
		. '<h2>Block: Canada, CPP and EI, dark</h2>' . block( array( 'country' => 'canada', 'calculator' => 'cpp-ei', 'theme' => 'dark' ) )
		. '</main><script src="' . $src . '" id="calcbloc-widget-js" async></script></body></html>';
	file_put_contents( $args['demo'], $html );
	echo 'demo written to ' . $args['demo'] . PHP_EOL;
}

echo $failures ? "\n$failures check(s) failed\n" : "\nall checks passed\n";
exit( $failures ? 1 : 0 );
