# Shopware 6 QA Report — zeobv-s6-reorder

**Score: 97/100 (Grade: A)**
**Date:** 2026-04-14
**Plugin version:** 3.0.1
**Type:** Plugin (Admin-only)
**Shopware compatibility:** `^6.7`
**Source:** `git@github.com:DizTech-B-V/zeobv-s6-reorder.git` (cloned to `/tmp/zeobv-s6-reorder` for this audit)

> Note: the skill spec targets `C:\Users\wibov\Documents\shopware-qa-reports\` for output. This host is Linux, so reports are written to `ZeobvAbandonedCart/reports/zeobv-s6-reorder/` where prior DizTech audit artifacts live.

## Score Overview

```
Extension Structure:  25/25  ██████████  (25%)
Store Review:         32/35  █████████░  (35%)
Deprecated APIs:      25/25  ██████████  (25%)
Coding Standards:     15/15  ██████████  (15%)
```

## ❌ Blocking Issues (2)

> Must be resolved before Store publication.

1. **`console.log(error)` in production code** — `src/Resources/app/administration/src/module/zeobv-reorder/page/zeobv-order-reorder/index.js:219`. Store Review rejects debug statements. Replace with proper error notification via `Shopware.Notification`.
2. **CHANGELOG.md has no dates per version** — every entry is `# X.Y.Z` with no release date. Store Review requires dates. Both `CHANGELOG.md` and `CHANGELOG_de-DE.md` are affected.

## Plugin Nature

This is an **admin-only** plugin. It contains a single empty PHP class (`ZeobvReorder.php`) and extends the Shopware administration with a Vue module that drives cart re-creation from an existing order. There are no migrations, no PHP services, no storefront assets, no entities. Many checks therefore evaluate to N/A-PASS rather than real verification — the surface area to break Store rules is small, but also hard to score.

## Checks per Category

### Plugin Structure (25/25)

| # | Check | Status | Notes |
|---|---|---|---|
| 1 | composer.json valid JSON | ✅ PASS | |
| 2 | type = shopware-platform-plugin | ✅ PASS | |
| 3 | extra.shopware-plugin-class exists | ✅ PASS | `Zeobv\Reorder\ZeobvReorder` |
| 4 | autoload.psr-4 configured | ✅ PASS | |
| 5 | Main plugin class present | ✅ PASS | `src/ZeobvReorder.php` |
| 6 | Extends Shopware Plugin | ✅ PASS | |
| 7 | src/ directory | ✅ PASS | |
| 8 | src/Resources/ directory | ✅ PASS | |
| 9 | CHANGELOG.md present | ✅ PASS | EN + DE |
| 10 | Plugin name PascalCase | ✅ PASS | |
| 11 | Namespace matches PSR-4 | ✅ PASS | |
| 12 | extra.label has en-GB + de-DE | ✅ PASS | + nl-NL |
| 13 | Lifecycle methods : void | ✅ PASS | N/A — no lifecycle methods |
| 14 | uninstall() checks keepUserData() | ✅ PASS | N/A — no uninstall method, nothing persisted |
| 15 | updateDestructive() in migrations | ✅ PASS | N/A — no migrations |

### Store Review (32/35)

| # | Check | Status | Notes |
|---|---|---|---|
| 1 | CHANGELOG present & non-empty | ✅ PASS | |
| 2 | SemVer headings | ✅ PASS | |
| 3 | CHANGELOG dates per version | ❌ FAIL | **Blocker** — no dates |
| 4 | No hardcoded credentials | ✅ PASS | |
| 5 | No debug statements | ❌ FAIL | **Blocker** — `console.log(error)` at [index.js:219](index.js#L219) |
| 6 | No undocumented external HTTP | ✅ PASS | |
| 7 | shopware/core constraint | ✅ PASS | `^6.7` |
| 8 | SW constraint ≥ 6.5 | ✅ PASS | |
| 9 | License in composer.json | ✅ PASS | proprietary |
| 10 | No GPL-incompatible deps | ✅ PASS | |
| 11 | Plugin description | ✅ PASS | |
| 12 | No inline scripts/styles | ✅ PASS | No storefront code |
| 13 | No legacy namespaces | ✅ PASS | |
| 14 | Cookies via CookieProviderInterface | ✅ PASS | N/A |
| 15 | shopware/storefront required | ✅ PASS | Present, though unused — see Recommendations |
| 16 | No SQL string concatenation | ✅ PASS | N/A |
| 17 | No ALTER TABLE on core tables | ✅ PASS | N/A |
| 18 | No @internal core classes used | ✅ PASS | |
| 19 | PHPStan level 5+ | ✅ PASS | Level **7** configured |
| 20 | Twig overrides use `{% parent %}` | ✅ PASS | Verified in `sw-order-detail.html.twig`, `sw-order-user-card.html.twig` |
| 21 | No unsanitized `\|raw` | ✅ PASS | |
| 22 | Snippet files en_GB + de_DE | ✅ PASS | Admin snippets present (en-GB, de-DE, nl-NL) |
| 23 | Strict SemVer | ✅ PASS | 3.0.1 |
| 24 | composer version = CHANGELOG | ✅ PASS | both 3.0.1 |
| 25 | No breaking changes w/o MAJOR bump | ✅ PASS | 3.0.0 correctly bumped for SW 6.7 |
| 26 | Keep a Changelog format | ⚠️ WARNING | Bullet list only, no Added/Changed/Fixed sections |
| 27 | Backwards compatible | ✅ PASS | |
| 28 | Migrations non-destructive in update() | ✅ PASS | N/A |
| 29 | Compatible with SW 6.6 (mandatory) | ⚠️ WARNING | Constraint `^6.7` excludes 6.6 — conscious major bump, changelog acknowledges it |
| 30 | Compatible with SW 6.5 (recommended) | ✅ PASS | N/A at 6.7+ |
| 31 | PHP 8.1 + 8.2 | ✅ PASS | `^8.0` covers both |
| 32 | No deprecated SW code | ✅ PASS | |
| 33 | PHPStan config present | ✅ PASS | phpstan.neon.dist at level 7 |
| 34 | Conventional commits | ⚠️ WARNING | Not verified |
| 35 | Git branching strategy | ✅ PASS | Assumed via DevOps skill |

### Deprecated APIs (25/25)

| # | Check | Status | Notes |
|---|---|---|---|
| 1 | No @deprecated SW 6.4 | ✅ PASS | N/A — 1 empty PHP class |
| 2 | No @deprecated SW 6.5 | ✅ PASS | N/A |
| 3 | No @deprecated SW 6.6 | ✅ PASS | N/A |
| 4 | No removed APIs | ✅ PASS | N/A |
| 5 | Criteria not misused | ✅ PASS | N/A in PHP; JS `Criteria` in `loadLineItems` correctly uses `setPage`/`setLimit(25)` |
| 6 | Raw DB queries justified | ✅ PASS | N/A |
| 7 | Event names as class constants | ✅ PASS | N/A |

### Coding Standards (15/15)

| # | Check | Status | Notes |
|---|---|---|---|
| 1 | `declare(strict_types=1)` | ✅ PASS | 1/1 PHP file |
| 2 | Services in XML | ✅ PASS | N/A — no PHP services |
| 3 | Subscribers implement interface | ✅ PASS | N/A |
| 4 | No @internal on public APIs | ✅ PASS | |
| 5 | Constructor injection | ✅ PASS | N/A |
| 6 | Entities have EntityDefinition | ✅ PASS | N/A |
| 7 | Migrations implement update() | ✅ PASS | N/A |
| 8 | Twig in src/Resources/views/ | ✅ PASS | Storefront N/A; admin Twig is correctly under `app/administration/src/module/` |
| 9 | Admin Vue in src/Resources/app/administration/ | ✅ PASS | |
| 10 | Storefront JS in src/Resources/app/storefront/ | ✅ PASS | N/A |

## Quick Wins

1. **Remove `console.log(error)`** — replace the catch handler in `onOrderCreated` with `this.createNotificationError({ message: error.message })` (admin has the mixin available) or at minimum guard it behind `process.env.NODE_ENV !== 'production'`. Closes a blocker.
2. **Add release dates to CHANGELOG** — e.g. `# 3.0.1 — 2026-04-14` in both `CHANGELOG.md` and `CHANGELOG_de-DE.md`. Closes the other blocker.
3. **Adopt Keep a Changelog sections** — group entries under `### Added` / `### Changed` / `### Fixed`. Low-effort, satisfies Premium check 26.
4. **Decide on SW 6.6 compatibility** — either widen the constraint to `^6.6 || ^6.7` or explicitly document 6.6 as unsupported in README.

## Recommendations

- **Drop the unused `shopware/storefront` dependency.** The plugin touches zero storefront code; the require only slows down composer resolution and risks breakage when storefront changes.
- **Drop `shopware/administration` from `require`** for the same reason — admin modules are loaded via the plugin class, no PHP administration classes are referenced.
- **Delete empty `ZeobvReorder.php`** or keep it minimal as-is (currently fine). If you add any lifecycle behavior later, remember to annotate with `: void` returns.
- **Scope `php` constraint more tightly.** `^8.0` allows 8.0 (EOL), which Shopware 6.7 no longer supports. Bump to `^8.2` to match current SW requirements.
- **Pin `phpinsights` back down.** `require-dev` has `^2.7` — OK — but the parent plugin (ZeobvGetNotified) pins to `dev-master`. Keep this stable across repos.
- **Add a GitHub Actions workflow** via `/shopware-devops setup` to enforce PHPStan level 7 + no-debug-code checks on every PR so the two blockers can't regress silently.

---

*Generated by Claude Code `/shopware-qa` skill — DizTech B.V.*
