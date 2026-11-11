=== CalcBloc Calculators ===
Contributors: calcbloc
Tags: calculator, salary calculator, tax calculator, take home pay, paycheck
Requires at least: 6.3
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Free take-home pay, salary and income tax calculators for 13 countries, added with a block or the [calcbloc] shortcode.

== Description ==

CalcBloc is a product of [itmarkerz technologies](https://itmarkerz.co.in).

CalcBloc Calculators puts a working salary or tax calculator from [CalcBloc](https://calcbloc.com) on any post or page. Pick a country and a calculator, and your readers can work out their take-home pay, income tax, National Insurance, GST or employer cost without leaving your site.

The calculators cover the United States, the United Kingdom, Australia, New Zealand, Canada, Ireland, Germany, India, Spain, the Netherlands, France, Singapore and the United Arab Emirates. Every rate comes from the official tax agency, such as the IRS, HMRC, the ATO or Revenue, and is updated on calcbloc.com, so the calculator on your site stays current without a plugin update.

= Features =

* **Block:** add "CalcBloc Calculator" and choose the country, calculator and colour scheme in the sidebar, with a live preview in the editor.
* **Shortcode:** `[calcbloc country="uk" calculator="take-home-pay"]` works in the classic editor, widgets and page builders.
* **Cannot break your theme:** the calculator runs in a sandboxed iframe inside a shadow root, so your styles and scripts and the calculator's stay apart.
* **Light and mobile friendly:** one small script (about 3 KB compressed), loaded asynchronously and only on pages that contain a calculator. The frame resizes to fit its content on any screen.
* **Light or dark:** pick a colour scheme per calculator or set a default under Settings > CalcBloc.
* **Credit link:** each calculator shows a small "Powered by CalcBloc" line with a normal link to the matching calculator page on calcbloc.com, where readers find the full explanation and sources.

= Shortcode options =

* `country`: the country as in the calcbloc.com address (`us`, `uk`, `australia`, `new-zealand`, `canada`, `ireland`, `germany`, `india`, `spain`, `netherlands`, `france`, `singapore`, `uae`) or its two-letter code (`gb`, `au`, `nz`…).
* `calculator`: the calculator name, for example `take-home-pay`, `income-tax`, `paycheck`, `national-insurance`, `employer-cost` or `gst`. Settings > CalcBloc lists every calculator with its ready-made shortcode.
* `theme`: `light` or `dark` (optional).

Examples:

`[calcbloc country="us" calculator="paycheck"]`
`[calcbloc country="australia" calculator="income-tax" theme="dark"]`

= External service =

This plugin embeds calculators hosted by CalcBloc (calcbloc.com), a product of itmarkerz technologies. It does not work without this service.

* **What is loaded and when:** on pages that contain a CalcBloc block or shortcode, the visitor's browser loads `https://calcbloc.com/widget.js` and the calculator page `https://calcbloc.com/embed/<calculator>` in an iframe. As with any embedded content, calcbloc.com receives the visitor's IP address, browser user agent and the address of the page with the calculator (as the referrer). Numbers typed into the calculator are processed in the visitor's browser.
* **Analytics inside the frame:** the calculator page may use Google Analytics 4 to count page views, with Google Consent Mode; analytics storage is denied by default for visitors in the EEA, the UK and Switzerland.
* **Catalogue updates:** when an administrator opens the block editor or Settings > CalcBloc, the plugin requests `https://calcbloc.com/embed/catalog.json` (at most every 12 hours) to update the list of calculators. This request sends your site address in the user agent and no personal data.
* No data is sent to calcbloc.com when the plugin is installed or activated.

CalcBloc terms of use: https://calcbloc.com/terms
CalcBloc privacy policy: https://calcbloc.com/privacy

= Data and source code =

Calculations use CalcBloc's tax data, published under CC BY 4.0 at https://calcbloc.com/data. The plugin's source code, including the unbuilt block files, is at https://github.com/itmarkerz/calcbloc-wordpress.

Results are estimates for information only, not tax, legal or financial advice. See https://calcbloc.com/disclaimer.

= About =

CalcBloc is built and run by [itmarkerz technologies](https://itmarkerz.co.in), a web and mobile app development company in Kerala, India.

* CalcBloc: https://calcbloc.com
* Embed code generator: https://calcbloc.com/embed
* Tax tables and data: https://calcbloc.com/data
* itmarkerz technologies: https://itmarkerz.co.in

== Installation ==

1. In your WordPress dashboard go to Plugins > Add New, search for "CalcBloc Calculators" and click Install, then Activate. Or upload the plugin folder to `/wp-content/plugins/` and activate it.
2. Edit a post or page and add the "CalcBloc Calculator" block, or paste a shortcode such as `[calcbloc country="uk" calculator="take-home-pay"]`.
3. Optional: choose the default colour scheme under Settings > CalcBloc.

== Frequently Asked Questions ==

= Is it free? =

Yes. The plugin and the calculators are free to use on any website, including commercial sites.

= Which calculators are available? =

Take-home pay, income tax, social security, employer cost, contractor versus employee, VAT/GST, loans and more, in 13 countries. Settings > CalcBloc lists every calculator with its shortcode, and https://calcbloc.com/embed shows a live preview of each one.

= Can I remove the "Powered by CalcBloc" link? =

Please keep it. It is the only thing CalcBloc asks in return, it tells your readers where the figures come from, and it links to the full calculator with its sources. If it is missing, the widget script adds it back.

= Will it slow down my site? =

The script is about 3 KB compressed, loads asynchronously and is only added to pages that contain a calculator. The calculator itself loads lazily when it scrolls into view.

= Does it work with my theme or page builder? =

The calculator runs in its own frame, so theme styles cannot affect it. The shortcode works anywhere WordPress runs shortcodes, including Elementor, Divi and the classic editor.

= Do the rates update automatically? =

Yes. The calculators run on calcbloc.com, where rates are checked against official sources and updated for each tax year. New calculators also appear in the block's list without a plugin update.

= Does the plugin store anything in my database? =

One option for the default colour scheme and a cached copy of the calculator list. Deleting the plugin removes both.

== Screenshots ==

1. The UK take-home pay calculator in a post, with the "Powered by CalcBloc" credit line.
2. Choosing the country, calculator and colour scheme in the block sidebar.
3. Settings > CalcBloc: default colour scheme and every calculator with its shortcode.
4. The calculator on a phone.

== Changelog ==

= 1.0.0 =
* First release: block, `[calcbloc]` shortcode, settings page with the calculator list, light and dark colour schemes.

== Upgrade Notice ==

= 1.0.0 =
First release.
