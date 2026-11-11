# CalcBloc Calculators for WordPress

CalcBloc is a product of [itmarkerz technologies](https://itmarkerz.co.in/?utm_source=calcbloc-wordpress&utm_medium=readme&utm_campaign=itmarkerz-oss).

Add free salary, take-home pay and income tax calculators from [CalcBloc](https://calcbloc.com/?utm_source=calcbloc-wordpress&utm_medium=readme&utm_campaign=itmarkerz-oss) to any WordPress site with a block or a shortcode. The calculators run in a sandboxed frame, so they cannot break your theme, and every rate comes from the official tax agency, checked and updated on calcbloc.com.

> **Status:** version 1.0.0, ready for review. Not yet submitted to the WordPress.org plugin directory.

## What it does

- **Block:** "CalcBloc Calculator". Pick the country, calculator and colour scheme in the sidebar; the editor shows a live preview.
- **Shortcode:** `[calcbloc country="uk" calculator="take-home-pay"]`, optional `theme="dark"`.
- **Settings > CalcBloc:** default colour scheme, plus every available calculator with its ready-made shortcode.
- **Light:** one script (`https://calcbloc.com/widget.js`, about 3 KB compressed), loaded asynchronously and only on pages with a calculator. The frame resizes to its content and fits phone screens.
- **Credit:** a visible "Powered by CalcBloc" line with a normal link to the matching calculator page.

The plugin outputs the same markup as the embed code from [calcbloc.com/embed](https://calcbloc.com/embed?utm_source=calcbloc-wordpress&utm_medium=readme&utm_campaign=itmarkerz-oss):

```html
<div class="calcbloc-widget" data-country="uk" data-calculator="take-home-pay">
  <p class="calcbloc-credit">Powered by <a href="https://calcbloc.com/uk/take-home-pay-calculator" target="_blank" rel="noopener">CalcBloc</a></p>
</div>
<script src="https://calcbloc.com/widget.js" async></script>
```

13 countries are covered: the US, UK, Australia, New Zealand, Canada, Ireland, Germany, India, Spain, the Netherlands, France, Singapore and the UAE. The list of calculators ships with the plugin (`assets/catalog.json`) and is refreshed from `https://calcbloc.com/embed/catalog.json` when an administrator opens the block editor or the settings page (at most every 12 hours).

## Layout

```
calcbloc-calculators.php   plugin header and bootstrap
includes/                  catalog, shared rendering, shortcode, block registration, settings
src/block/                 block source (block.json, edit.js, index.js, render.php)
build/block/               built block, committed because WordPress.org ships built assets
assets/catalog.json        bundled list of countries and calculators
uninstall.php              removes the plugin's option, cached catalog and transient
readme.txt                 WordPress.org readme
tests/harness.php          renders the shortcode and block outside WordPress and checks the markup
```

## Development

```sh
npm install
npm run build          # src/block -> build/block (@wordpress/scripts)
composer install
vendor/bin/phpcs       # WordPress Coding Standards + PHP 7.4 compatibility
php tests/harness.php  # markup checks; add --demo=out.html --base=http://localhost:3101 for a demo page
```

A filter changes the CalcBloc base URL for staging: `add_filter( 'calcbloc_calculators_base_url', fn () => 'https://staging.example' );`

Release steps for the WordPress.org directory are in [SUBMISSION_CHECKLIST.md](SUBMISSION_CHECKLIST.md).

## Data and licence

The plugin is copyright itmarkerz technologies and licensed under the GPL, version 2 or later (see [LICENSE](LICENSE)), as the WordPress.org directory requires. The tax data it displays comes from [CalcBloc](https://calcbloc.com/data?utm_source=calcbloc-wordpress&utm_medium=readme&utm_campaign=itmarkerz-oss) under CC BY 4.0, credit "Data by CalcBloc (calcbloc.com/data)". Results are estimates, not tax advice: see the [disclaimer](https://calcbloc.com/disclaimer?utm_source=calcbloc-wordpress&utm_medium=readme&utm_campaign=itmarkerz-oss).

## Privacy

Pages with a calculator load `widget.js` and the calculator frame from calcbloc.com, so calcbloc.com receives the visitor's IP address, user agent and the page address. Numbers typed into the calculator are processed in the browser. The calculator page may count page views with Google Analytics 4 (Consent Mode; analytics storage denied by default in the EEA, UK and Switzerland). See the [privacy policy](https://calcbloc.com/privacy?utm_source=calcbloc-wordpress&utm_medium=readme&utm_campaign=itmarkerz-oss) and [terms](https://calcbloc.com/terms?utm_source=calcbloc-wordpress&utm_medium=readme&utm_campaign=itmarkerz-oss).

## Links

- Website: [calcbloc.com](https://calcbloc.com/?utm_source=calcbloc-wordpress&utm_medium=readme&utm_campaign=itmarkerz-oss)
- Embed code generator: [calcbloc.com/embed](https://calcbloc.com/embed?utm_source=calcbloc-wordpress&utm_medium=readme&utm_campaign=itmarkerz-oss)
- Tax tables and downloads: [calcbloc.com/data](https://calcbloc.com/data?utm_source=calcbloc-wordpress&utm_medium=readme&utm_campaign=itmarkerz-oss)
- API documentation: [calcbloc.com/developers](https://calcbloc.com/developers?utm_source=calcbloc-wordpress&utm_medium=readme&utm_campaign=itmarkerz-oss)

## About

CalcBloc is built and run by [itmarkerz technologies](https://itmarkerz.co.in/?utm_source=calcbloc-wordpress&utm_medium=readme&utm_campaign=itmarkerz-oss), a web and mobile app development company in Kerala, India. Website: [calcbloc.com](https://calcbloc.com/?utm_source=calcbloc-wordpress&utm_medium=readme&utm_campaign=itmarkerz-oss) · Company: [itmarkerz.co.in](https://itmarkerz.co.in/?utm_source=calcbloc-wordpress&utm_medium=readme&utm_campaign=itmarkerz-oss)
