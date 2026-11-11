import { __, sprintf } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, Notice } from '@wordpress/components';

const data = window.calcblocCalculators || {
	catalog: [],
	base: 'https://calcbloc.com',
	defaultTheme: 'light',
};

/** Same referral tag as the front-end credit link (calcbloc_calculators_credit_url() in includes/render.php). */
const CREDIT_UTM =
	'utm_source=calcbloc-wordpress&utm_medium=app&utm_campaign=itmarkerz-oss';
function creditUrl( url ) {
	if ( url.includes( 'utm_source=' ) ) {
		return url;
	}
	const [ base, ...hash ] = url.split( '#' );
	const tagged = `${ base }${ base.includes( '?' ) ? '&' : '?' }${ CREDIT_UTM }`;
	return hash.length ? `${ tagged }#${ hash.join( '#' ) }` : tagged;
}

function findCountry( id ) {
	return data.catalog.find( ( c ) => c.id === id || c.code === id );
}

export default function Edit( { attributes, setAttributes } ) {
	const { country, calculator, theme } = attributes;
	const blockProps = useBlockProps();
	const entry = findCountry( country );
	const calc = entry
		? entry.calculators.find(
				( c ) => c.type === calculator || c.slug === calculator
		  )
		: undefined;
	const scheme = theme || data.defaultTheme;
	const frame = calc
		? `${ data.base }/embed/${ calc.slug }${
				scheme === 'dark' ? '?theme=dark' : ''
		  }`
		: '';

	const controls = (
		<InspectorControls>
			<PanelBody title={ __( 'Calculator', 'calcbloc-calculators' ) }>
				<SelectControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={ __( 'Country', 'calcbloc-calculators' ) }
					value={ entry ? entry.id : '' }
					options={ data.catalog.map( ( c ) => ( {
						label: c.name,
						value: c.id,
					} ) ) }
					onChange={ ( value ) => {
						const next = findCountry( value );
						setAttributes( {
							country: value,
							calculator: next ? next.calculators[ 0 ].type : '',
						} );
					} }
				/>
				<SelectControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={ __( 'Calculator', 'calcbloc-calculators' ) }
					value={ calc ? calc.type : '' }
					options={ ( entry ? entry.calculators : [] ).map(
						( c ) => ( { label: c.title, value: c.type } )
					) }
					onChange={ ( value ) =>
						setAttributes( { calculator: value } )
					}
				/>
				<SelectControl
					__next40pxDefaultSize
					__nextHasNoMarginBottom
					label={ __( 'Colour scheme', 'calcbloc-calculators' ) }
					value={ theme }
					options={ [
						{
							label: __( 'Site default', 'calcbloc-calculators' ),
							value: '',
						},
						{
							label: __( 'Light', 'calcbloc-calculators' ),
							value: 'light',
						},
						{
							label: __( 'Dark', 'calcbloc-calculators' ),
							value: 'dark',
						},
					] }
					onChange={ ( value ) => setAttributes( { theme: value } ) }
				/>
			</PanelBody>
		</InspectorControls>
	);

	return (
		<div { ...blockProps }>
			{ controls }
			{ calc ? (
				<>
					<iframe
						src={ frame }
						title={ calc.title }
						loading="lazy"
						sandbox="allow-scripts allow-same-origin allow-forms"
						style={ {
							display: 'block',
							width: '100%',
							height: '640px',
							border: 0,
							borderRadius: '12px',
						} }
					/>
					<p
						className="calcbloc-credit"
						style={ { textAlign: 'right', fontSize: '13px' } }
					>
						{ __( 'Powered by', 'calcbloc-calculators' ) }{ ' ' }
						<a
							href={ creditUrl( calc.url ) }
							target="_blank"
							rel="noopener noreferrer"
						>
							CalcBloc
						</a>
					</p>
				</>
			) : (
				<Notice status="warning" isDismissible={ false }>
					{ sprintf(
						/* translators: 1: country, 2: calculator. */
						__(
							'No CalcBloc calculator "%2$s" for "%1$s". Pick one in the block settings.',
							'calcbloc-calculators'
						),
						country,
						calculator
					) }
				</Notice>
			) }
		</div>
	);
}
