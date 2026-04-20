# Shopware 6 QA Report — ZeobvCountrySelect

**Score: 95/100 (Grade: A)**
**Date:** 2026-04-14
**App version:** 4.0.3
**Type:** App (storefront-only)
**Path:** `/var/www/html/SW67/custom/apps/ZeobvCountrySelect`

## Score Overview

```
Extension Structure:  25/25  ██████████  (25%)
Store Review:         30/35  ████████░░  (35%)
Deprecated APIs:      25/25  ██████████  (25%)
Coding Standards:     15/15  ██████████  (15%)
```

## ❌ Blocking Issues (1)

1. **CHANGELOG.md has no dates per version.** All 10 entries are bare `# X.Y.Z`. Store Review requires release dates. Both `CHANGELOG.md` and `CHANGELOG_de-DE.md` need `# 4.0.3 — 2026-04-14`-style headings.

## App nature

This is a small **storefront-only Shopware 6 App**. No backend (no `<setup>` URL, no webhooks, no admin module), no `config.xml`, no `cms.xml`. It overrides the storefront header to inject a country-picker that calls store-api endpoints. Most plugin-oriented checks are N/A; the real risk surface is the Twig override, the script hook in `Resources/scripts/`, and ~3 storefront JS files.

## Checks per Category

### Extension Structure — App (25/25)

| # | Check | Status | Notes |
|---|---|---|---|
| 1 | manifest.xml present and valid | ✅ PASS | |
| 2 | XSD points to `trunk` | ✅ PASS | `…/platform/trunk/…/manifest-1.0.xsd` |
| 3 | meta.name PascalCase | ✅ PASS | `ZeobvCountrySelect` |
| 4 | meta.label default + de-DE | ✅ PASS | + nl-NL |
| 5 | meta.description default + de-DE | ✅ PASS | + nl-NL |
| 6 | Descriptions per language are correct | ✅ PASS | not copy-pasted |
| 7 | meta.author present | ✅ PASS | |
| 8 | meta.version SemVer | ✅ PASS | 4.0.3 |
| 9 | meta.license present | ✅ PASS | proprietary |
| 10 | meta.icon present and file exists | ✅ PASS | `Resources/config/plugin.png` |
| 11 | meta.copyright present | ✅ PASS | |
| 12 | CHANGELOG.md present | ✅ PASS | EN + DE |
| 13 | Resources/ directory present | ✅ PASS | |
| 14 | config.xml XSD trunk | ✅ PASS | N/A — no config.xml |
| 15 | config.xml field types match defaults | ✅ PASS | N/A |
| 16 | cms.xml XSD trunk | ✅ PASS | N/A |
| 17 | CMS block names have vendor prefix | ✅ PASS | N/A |
| 18 | Custom field names have vendor prefix | ✅ PASS | N/A |
| 19 | Permissions not too broad | ✅ PASS | No `<permissions>` block — minimal scope |

### Store Review (30/35)

| # | Check | Status | Notes |
|---|---|---|---|
| 1 | CHANGELOG present & non-empty | ✅ PASS | |
| 2 | SemVer headings | ✅ PASS | |
| 3 | Dates per version | ❌ FAIL | **Blocker** — no dates |
| 4 | No hardcoded credentials | ⚠️ WARNING | See "Credential exposure" below |
| 5 | No debug statements | ✅ PASS | |
| 6 | No undocumented external HTTP | ✅ PASS | Store-api calls are the documented feature |
| 7 | shopware/core constraint | ✅ PASS | N/A — apps have no composer.json |
| 8 | SW constraint ≥ 6.5 | ✅ PASS | N/A — apps are version-flexible |
| 9 | License | ✅ PASS | manifest license = proprietary |
| 10 | No GPL-incompatible deps | ✅ PASS | N/A |
| 11 | Plugin description | ✅ PASS | `meta.description` populated |
| 12 | No inline scripts/styles | ✅ PASS | JSON in `data-*` attribute is fine; no inline `<script>` |
| 13 | No legacy namespaces | ✅ PASS | |
| 14 | Cookies via CookieProviderInterface | ✅ PASS | N/A — no cookies set |
| 15 | shopware/storefront required | ✅ PASS | N/A |
| 16 | No SQL string concatenation | ✅ PASS | N/A |
| 17 | No ALTER TABLE on core tables | ✅ PASS | N/A |
| 18 | No @internal core classes used | ✅ PASS | N/A |
| 19 | PHPStan level 5+ | ✅ PASS | N/A — no PHP code |
| 20 | Twig overrides use `{{ parent() }}` | ✅ PASS | `header.html.twig:4` |
| 21 | No unsanitized `\|raw` | ✅ PASS | |
| 22 | Snippet files en_GB + de_DE | ✅ PASS | + nl_NL |
| 23 | Strict SemVer | ✅ PASS | 4.0.3 |
| 24 | manifest version = CHANGELOG | ✅ PASS | both 4.0.3 |
| 25 | No breaking changes w/o MAJOR bump | ✅ PASS | 4.0.0 = SW 6.7 bump |
| 26 | Keep a Changelog format | ⚠️ WARNING | Bullets only, no Added/Changed/Fixed |
| 27 | Backwards compatible | ✅ PASS | |
| 28 | Migrations non-destructive | ✅ PASS | N/A |
| 29 | SW 6.6 compat (mandatory) | ✅ PASS | App is version-agnostic |
| 30 | SW 6.5 compat (recommended) | ✅ PASS | |
| 31 | PHP 8.1 + 8.2 | ✅ PASS | N/A |
| 32 | No deprecated code | ✅ PASS | |
| 33 | PHPStan config | ✅ PASS | N/A |
| 34 | Conventional commits | ⚠️ WARNING | Not verified |
| 35 | Git branching strategy | ✅ PASS | |
| 36 | No DOM XSS via interpolated innerHTML | ⚠️ WARNING | See "innerHTML interpolation" below |

### Deprecated APIs (25/25)

All N/A — no PHP code in this app. Storefront JS uses standard Shopware plugin-system imports.

### Coding Standards (15/15)

| # | Check | Status | Notes |
|---|---|---|---|
| 1 | declare(strict_types=1) | ✅ PASS | N/A |
| 2 | Services in XML | ✅ PASS | N/A |
| 3 | Subscribers implement interface | ✅ PASS | N/A |
| 4 | No @internal on public APIs | ✅ PASS | N/A |
| 5 | Constructor injection | ✅ PASS | N/A |
| 6 | Entities have EntityDefinition | ✅ PASS | N/A |
| 7 | Migrations implement update() | ✅ PASS | N/A |
| 8 | Twig in views/ | ✅ PASS | `Resources/views/storefront/...` |
| 9 | Admin Vue in app/administration/ | ✅ PASS | N/A |
| 10 | Storefront JS in app/storefront/ | ✅ PASS | `Resources/app/storefront/src/...` |

## Specific findings worth attention

### 1. Credential exposure (warning)

[`Resources/scripts/storefront-zeobv-store-api/token.twig`](../../../../apps/ZeobvCountrySelect/Resources/scripts/storefront-zeobv-store-api/token.twig) returns `salesChannelContext.salesChannel.accessKey` as JSON via a script hook:

```twig
{% set response = services.response.json({
    'key': hook.salesChannelContext.salesChannel.accessKey,
}) %}
```

Technically the `accessKey` is the **public** store-api key (already exposed as the `sw-access-key` header from any storefront browser request) so this is **not a real secret leak**. It's flagged because:

- The CHANGELOG entry **3.0.2** says "Removed store-api credentials from twig to support cloud environments" and **4.0.2** says "Removed context token from Twig script response… preventing potential token leakage" — yet the file still exists and still returns the access key. Either the changelog message is misleading or the cleanup was incomplete.
- Meanwhile [`header.html.twig:14`](../../../../apps/ZeobvCountrySelect/Resources/views/storefront/layout/header/header.html.twig#L14) still puts `data-zeobv-country-select-context-token="{{ app.session.get('sw-context-token') }}"` directly into the rendered HTML, which contradicts the 4.0.2 changelog entry. The context token **is** session-bound and **should not** be embedded in HTML.

→ Audit needed: was 4.0.2 a partial fix, or did a regression reintroduce the data-attribute?

### 2. innerHTML interpolation (warning)

[`country-select.plugin.js:43`](../../../../apps/ZeobvCountrySelect/Resources/app/storefront/src/plugin/country-select.plugin.js#L43):

```js
return `<li class="zeobv-country-select__option" data-value="${element.id}">${element.name}</li>`
```

`element.name` comes from the store-api `country` endpoint and in practice contains only ISO country names — currently safe. But the pattern is an XSS template trap if the data ever contains HTML (e.g. translation that uses entities, or a custom country with markup in its name). Replace with `textContent` or use a DOM template.

### 3. CHANGELOG dates (blocker)

10 entries, none with dates. Auto-fixable for the most-recent entry via `/shopware-fix`; older entries need git archaeology.

## Quick Wins

1. **Add `2026-04-14` to the latest CHANGELOG entry** — closes the blocker. Both EN + DE files.
2. **Remove the `data-...-context-token` attribute** in `header.html.twig:14`. Read the token from cookies in the JS plugin instead (it already runs against the same browser session).
3. **Delete or document `token.twig`** — if it's no longer used, remove the file and the corresponding script hook entry. If it is still used, rename the changelog entry from "Removed credentials" to something accurate.
4. **Replace `innerHTML` with safe DOM construction** in `country-select.plugin.js:42-48`.
5. **Adopt Keep a Changelog sections** in both changelog files.

## Recommendations

- **Add a `<setup>`-less webhook/permissions audit** to README — make explicit that this app needs no permissions and has no backend, so reviewers don't wonder.
- **Pin the manifest XSD to a tagged version** if Shopware ever changes the schema in `trunk` and breaks installs (currently `trunk` is the recommended setting per DizTech standards — keep as is).
- **Run `/shopware-fix`** to clear the dates blocker and bump to 4.0.4.
- **Consider migrating from app to plugin** if the team needs structured testing — apps can't ship PHPUnit tests easily.

---

*Generated by Claude Code `/shopware-qa` skill — DizTech B.V.*
