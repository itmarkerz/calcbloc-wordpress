<?php
/**
 * Settings > CalcBloc: the default colour scheme and a reference of the available calculators.
 *
 * @package CalcBloc_Calculators
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the single option.
 */
function calcbloc_calculators_register_settings() {
	register_setting(
		'calcbloc_calculators',
		'calcbloc_calculators_options',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'calcbloc_calculators_sanitize_options',
			'default'           => array( 'theme' => 'light' ),
		)
	);
	add_settings_section( 'calcbloc_calculators_main', '', '__return_false', 'calcbloc-calculators' );
	add_settings_field(
		'calcbloc_calculators_theme',
		__( 'Default colour scheme', 'calcbloc-calculators' ),
		'calcbloc_calculators_theme_field',
		'calcbloc-calculators',
		'calcbloc_calculators_main',
		array( 'label_for' => 'calcbloc_calculators_theme' )
	);
}

/**
 * Sanitises the option.
 *
 * @param mixed $input Submitted value.
 * @return array
 */
function calcbloc_calculators_sanitize_options( $input ) {
	$theme = ( is_array( $input ) && isset( $input['theme'] ) ) ? sanitize_key( $input['theme'] ) : 'light';
	return array( 'theme' => 'dark' === $theme ? 'dark' : 'light' );
}

/**
 * Colour scheme field.
 */
function calcbloc_calculators_theme_field() {
	$theme = calcbloc_calculators_default_theme();
	?>
	<select id="calcbloc_calculators_theme" name="calcbloc_calculators_options[theme]">
		<option value="light" <?php selected( $theme, 'light' ); ?>><?php esc_html_e( 'Light', 'calcbloc-calculators' ); ?></option>
		<option value="dark" <?php selected( $theme, 'dark' ); ?>><?php esc_html_e( 'Dark', 'calcbloc-calculators' ); ?></option>
	</select>
	<p class="description"><?php esc_html_e( 'Used when a shortcode or block does not choose one.', 'calcbloc-calculators' ); ?></p>
	<?php
}

/**
 * Adds Settings > CalcBloc.
 */
function calcbloc_calculators_add_settings_page() {
	add_options_page(
		__( 'CalcBloc Calculators', 'calcbloc-calculators' ),
		__( 'CalcBloc', 'calcbloc-calculators' ),
		'manage_options',
		'calcbloc-calculators',
		'calcbloc_calculators_settings_page'
	);
}

/**
 * "Settings" link on the Plugins screen.
 *
 * @param array $links Existing links.
 * @return array
 */
function calcbloc_calculators_action_links( $links ) {
	$url = admin_url( 'options-general.php?page=calcbloc-calculators' );
	array_unshift( $links, '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Settings', 'calcbloc-calculators' ) . '</a>' );
	return $links;
}

/**
 * The settings page.
 */
function calcbloc_calculators_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	calcbloc_calculators_refresh_catalog();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'CalcBloc Calculators', 'calcbloc-calculators' ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'calcbloc_calculators' );
			do_settings_sections( 'calcbloc-calculators' );
			submit_button();
			?>
		</form>

		<h2><?php esc_html_e( 'How to add a calculator', 'calcbloc-calculators' ); ?></h2>
		<p><?php esc_html_e( 'In the block editor, add the "CalcBloc Calculator" block and pick a country and calculator in the sidebar. Anywhere shortcodes work, use:', 'calcbloc-calculators' ); ?></p>
		<p><code>[calcbloc country="uk" calculator="take-home-pay"]</code></p>
		<p><?php esc_html_e( 'Optional: theme="dark". Each calculator shows a "Powered by CalcBloc" link to its page on calcbloc.com.', 'calcbloc-calculators' ); ?></p>

		<h2><?php esc_html_e( 'Available calculators', 'calcbloc-calculators' ); ?></h2>
		<table class="widefat striped">
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Country', 'calcbloc-calculators' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Calculator', 'calcbloc-calculators' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Shortcode', 'calcbloc-calculators' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( calcbloc_calculators_get_catalog() as $country ) : ?>
					<?php foreach ( $country['calculators'] as $calc ) : ?>
						<tr>
							<td><?php echo esc_html( $country['name'] ); ?></td>
							<td><a href="<?php echo esc_url( $calc['url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $calc['title'] ); ?></a></td>
							<td><code><?php echo esc_html( '[calcbloc country="' . $country['id'] . '" calculator="' . $calc['type'] . '"]' ); ?></code></td>
						</tr>
					<?php endforeach; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}
