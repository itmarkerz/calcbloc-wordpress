# Submission checklist

Two parts: (A) put the widget live on calcbloc.com, which the plugin depends on; (B) submit the plugin to the WordPress.org directory. Do A first: the plugin loads `https://calcbloc.com/widget.js`, reads `https://calcbloc.com/embed/catalog.json`, and its readme links to `https://calcbloc.com/embed`, none of which exist until the site branch `feat/embed-widget` is deployed.

## A. Going live with widget.js on calcbloc.com

1. **Review and merge** `feat/embed-widget` in the site repo, then run the usual pre-push checks (full vitest on Node 24, `tsc --noEmit`) and deploy with `deploy.sh`.
2. **nginx:** copy the updated `deploy/nginx/calcbloc.com.conf` to the server (`nginx -t && systemctl reload nginx`). The X-Frame-Options map now also exempts `/es/embed/` and `/de/embed/`; before this, only `/embed/` could be framed. `/embed` (the picker page) stays `SAMEORIGIN`.
3. **Check headers after deploy:**
   - `curl -sI https://calcbloc.com/widget.js` → `200`, `content-type: application/javascript`, `cache-control: public, max-age=3600, s-maxage=86400, stale-while-revalidate=604800`, `access-control-allow-origin: *`.
   - `curl -sI https://calcbloc.com/embed/catalog.json` → `200`, JSON, `x-robots-tag: noindex, follow`.
   - `curl -sI https://calcbloc.com/embed/take-home-pay-calculator` → `x-robots-tag: noindex, follow` and **no** `x-frame-options` header.
   - `curl -sI https://calcbloc.com/embed` → `x-frame-options: SAMEORIGIN`, robots meta `noindex`.
4. **Cloudflare:** `.js` is cached by default and honours the origin's Cache-Control. After a release that adds calculators, purge `https://calcbloc.com/widget.js` and `https://calcbloc.com/embed/catalog.json` (or wait up to a day). Do not enable Rocket Loader or minification rules that rewrite `widget.js`.
5. **CSP:** the site sends no Content-Security-Policy today. If one is added later, the frame pages need `frame-ancestors *` (or no frame-ancestors) while other pages keep `frame-ancestors 'self'`. Host pages with their own CSP need `script-src https://calcbloc.com` and `frame-src https://calcbloc.com`; mention this on /embed if support questions come in.
6. **SRI:** do not publish an `integrity=` hash for widget.js: the file changes whenever calculators are added, which would break every embed. Version stays in the `CalcBlocWidget.version` field instead.
7. **Smoke test** the live picker (https://calcbloc.com/embed): pick a country and calculator, copy the code into a plain HTML page on another origin, confirm the frame loads, resizes and shows "Powered by CalcBloc" linking to the right calculator page.
8. **Monitoring:**
   - Uptime check on `https://calcbloc.com/widget.js` and one frame page (status 200, body contains `CalcBlocWidget`).
   - nginx access log: requests to `/embed/` with a foreign `Referer` show which sites embed calculators (`grep ' /embed/' calcbloc.access.log | awk '{print $11}' | sort | uniq -c | sort -rn`).
   - GA4: frame page views have `/embed/` paths; `calculator_start` events carry `placement=embed`.
   - Google Search Console: the frame pages are noindex and robots-disallowed; they should never show as indexed. Watch for "Indexed, though blocked by robots.txt" on `/embed/` URLs.
9. **Index decision (owner):** `/embed` is noindex for now (meta robots and the `X-Robots-Tag` header from the `/embed/:path*` rule in `next.config.ts`). To index it later, drop `noindex: true` in `src/app/(english)/(site)/(core)/embed/page.tsx`, exclude `/embed` itself from the header rule, and add it to `src/lib/routes.ts` so it reaches the sitemap.

## B. WordPress.org submission

### Before submitting

- [ ] Part A is live (the plugin needs calcbloc.com/widget.js and /embed/catalog.json).
- [ ] Decide the analytics question (see "Decisions" below) and make `readme.txt` match.
- [ ] `npm ci && npm run build`: `build/block/` is up to date and committed.
- [ ] `composer install && vendor/bin/phpcs` reports no errors or warnings.
- [ ] `php tests/harness.php` passes.
- [ ] Refresh `assets/catalog.json` from `https://calcbloc.com/embed/catalog.json`.
- [ ] Run the **Plugin Check** plugin (https://wordpress.org/plugins/plugin-check/) on a test site (WordPress Playground works without installing WordPress: `npx @wp-playground/cli@latest server --port=3102 --mount-dir . /wordpress/wp-content/plugins/calcbloc-calculators --login`; in Git Bash set `MSYS_NO_PATHCONV=1` first), fix every error, review warnings.
- [ ] Test on the minimum (6.3) and latest (7.1) WordPress, PHP 7.4 and 8.3: block insert, all three sidebar controls, shortcode in classic editor, settings save, uninstall removes `calcbloc_calculators_options`, `calcbloc_calculators_catalog` and the transient.
- [ ] Build the zip: `npm run plugin-zip` (uses the `files` list in package.json), unzip into a clean site and activate it. The zip must not contain `node_modules`, `vendor`, `src`, `tests`, `.git`.
- [ ] Screenshots for readme.txt (`screenshot-1.png` … `screenshot-4.png`, captions already in readme.txt) and directory art (below).

### Manual Plugin Check rules already applied

- GPL-2.0-or-later header and LICENSE file; `readme.txt` licence matches.
- Text domain `calcbloc-calculators` equals the expected slug; every string uses it; no `load_plugin_textdomain()` (not needed for directory plugins since WP 4.6); JS strings use `@wordpress/i18n` and `wp_set_script_translations()`.
- Every output escaped late (`esc_html`, `esc_attr`, `esc_url`); input sanitised (`sanitize_key`, `sanitize_text_field`, `esc_url_raw`); settings via the Settings API with a sanitize callback and `manage_options` check.
- Direct file access blocked (`ABSPATH` / `WP_UNINSTALL_PLUGIN` checks) in every PHP file.
- All globals prefixed `calcbloc_calculators_` / `CALCBLOC_CALCULATORS_`; the shortcode tag is `calcbloc`, the block `calcbloc/calculator`.
- Scripts enqueued with `wp_register_script()` / `wp_enqueue_script()` (versioned, async, footer), only on pages that render a calculator; no inline `<script>` in front-end output.
- Remote requests use `wp_remote_get()` with a timeout, admin screens only, at most every 12 hours; no requests on activation; nothing sent without the admin opening the editor or settings.
- External service documented in readme.txt with what is sent, when, and links to terms and privacy policy (Guideline 6/7).
- Source of the built JS is included (`src/block/`) and the public repo is linked (Guideline 4: human-readable code).
- `uninstall.php` cleans up all stored data, multisite included.
- Readme: short description under 150 characters, 5 tags maximum, `Tested up to: 7.1`, `Stable tag: 1.0.0` equal to the plugin header version.
- No "powered by" link inserted without the site owner's action: the credit appears only inside a calculator the user deliberately added, and it is disclosed in the readme and FAQ (Guideline 10: credit links must be part of the user-requested feature or opt-in). Expect reviewers to ask about it; the answer is that the credit is part of the embedded service, visible, and tied to the calculator the user placed.

### Submitting

1. Log in at https://wordpress.org/plugins/developers/add/ with the account that will own the plugin (Contributors in readme.txt is set to `calcbloc`: create that WordPress.org account first, or change the field to the owner's username). Use an email on the calcbloc.com domain so the reviewers can verify ownership of the brand.
2. Upload the zip. The slug comes from the Plugin Name header: **calcbloc-calculators** (checked free on 2026-11-11 via the plugins API, as are `calcbloc`, `calcbloc-widget` and `calcbloc-calculator-widget`). To get a different slug, ask in the reply to the review email **before** approval; it cannot change afterwards.
3. Review takes from a few days to several weeks (the queue length is shown on the add page). Reviewers reply by email from plugins@wordpress.org; answer in the same thread, upload fixes via the link in the email, and keep the version number the same until approval.
4. Typical review questions for this kind of plugin: remote script loading (answer: it is the service's own embed script, documented), the credit link (see above), and the data sent to calcbloc.com.

### After approval: SVN

```sh
svn co https://plugins.svn.wordpress.org/calcbloc-calculators svn-calcbloc
cd svn-calcbloc
# copy the contents of the release zip into trunk/
svn add --force trunk
# directory art into assets/ (not trunk/assets)
svn add --force assets
svn cp trunk tags/1.0.0
svn ci -m "Release 1.0.0" --username <wporg-username>
```

- Stable tag in `trunk/readme.txt` must match the `tags/1.0.0` folder.
- For later releases: bump the version in `calcbloc-calculators.php`, `CALCBLOC_CALCULATORS_VERSION`, `block.json`, `package.json` and readme `Stable tag`; add a changelog entry; commit to trunk; `svn cp trunk tags/x.y.z`.
- Updating only "Tested up to" needs a readme-only commit to trunk and the current tag.

### Directory art (SVN `assets/` folder)

| File | Size | Notes |
|---|---|---|
| `icon-128x128.png`, `icon-256x256.png` | 128×128, 256×256 | or a single `icon.svg` |
| `banner-772x250.png` | 772×250 | required for a banner |
| `banner-1544x500.png` | 1544×500 | retina banner |
| `screenshot-1.png` … `screenshot-4.png` | any, ideally 1200 wide | captions come from readme.txt "Screenshots" |

PNG or JPG; keep each under 1 MB. Use the CalcBloc logo and cobalt (#1F4FE0) brand colour; no third-party trademarks.

## Decisions for the owner

1. **Analytics in the frame:** the calculator frame loads Google Analytics 4 like every site page. For a plugin that markets itself as light, turning analytics off in `/embed/*` frames would simplify the privacy disclosure (the readme section on analytics could then be removed). Otherwise keep the current wording.
2. **Contributors username** in readme.txt (`calcbloc` placeholder).
3. **Slug:** `calcbloc-calculators` (default from the plugin name) or `calcbloc`.
4. **Directory art** and screenshots: none are in the repo yet.
