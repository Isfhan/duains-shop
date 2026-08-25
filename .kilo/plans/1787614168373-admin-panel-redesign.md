# Admin Panel Redesign — DESIGN.md Alignment (Duains Fragrances)

## Goal
Make the entire admin experience (Aimeos JQAdm backend, auth screens, Breeze dashboard/profile) visually consistent with root `DESIGN.md`, with WCAG-AA-legible text in **both** light and dark mode.

## Decisions (confirmed with user)
1. **Scope:** Everything — JQAdm panel + auth screens + Breeze dashboard/profile. Storefront (`base.blade.php`) out of scope.
2. **Typography:** Full DESIGN.md pairing — Playfair Display (display/headings/brand wordmarks) + Montserrat (interface/body). Inter retired.
3. **Colors:** Canonical DESIGN.md palette with AA tuning — Signature Gold `#C5A059` primary, Radiant Gold `#E8D08D` hover/highlight, Deep Onyx `#0A0A0A→#000000` dark canvas, Warm Cream `#F4EFE6` light canvas. Champagne `#C9A96A`/`#E3C68F` retired everywhere.
4. **Architecture:** Keep the established upgrade-safe pattern (Laravel view-namespace overrides + static CSS overlays). No `vendor/` edits, no new extension package, no Vite/npm changes (admin CSS is hand-written static, cache-busted via `config('shop.version')`).
5. **Dark/Light:** Keep the existing cookie-based `aimeos_backend_theme` toggle; dark remains default. Both palettes must pass AA.

## Known defects to fix (found in current code)
- `public/css/admin-theme.css` dark-mode `.btn-secondary`: bg `--bs-secondary-light:#8B929C` + text `--bs-secondary-dark:#F1EEE6` ≈ **1.9:1** (invisible).
- White text on success badges/buttons (`#fff` on `--bs-success:#2E9270`) ≈ **2.6:1**.
- `.app-menu button { color: var(--ai-bg) }` references a possibly-undefined variable (icons may render unstyled/invisible).
- Alert text colors borderline (<3.4:1) in light mode (warning/info).
- Colors/fonts diverge from DESIGN.md (champagne golds, Inter).

## Canonical tokens (single source of truth)
Create **`public/css/duains-tokens.css`**, loaded first by every admin-related shell:

```
--du-gold:        #C5A059   (Signature Gold — primary actions, active states, linework)
--du-gold-light:  #E8D08D   (Radiant Gold — hover, highlights, glow)
--du-on-gold:     #17130B   (text/icons on gold fills)
--du-gold-text:   #D8BC85   (AA-tuned gold for SMALL text on dark bg ≥4.5:1)
--du-focus-ring:  rgba(197,160,89,.28)

LIGHT (Warm Cream):  --du-bg:#F4EFE6 · --du-surface:#FBF8F2 · --du-elevated:#FFFFFF
                     --du-line:#DED7C8 · --du-line-strong:#C8BFA9
                     --du-text:#1A1712 · --du-muted:#5B5548 (≥7:1) · --du-menu-bg:#141109
DARK (Deep Onyx):    --du-bg:#0A0A0A · --du-surface:#111110 · --du-elevated:#181714
                     --du-line:#26241F · --du-line-strong:#3A362C
                     --du-text:#F4EFE6 · --du-muted:#A39C8D (≥4.5:1) · --du-menu-bg:#000000

Status (both modes get explicit fg/bg pairs, never bare white):
  success #2F9E77/dark-fg #06281C·light-fg #FFF→use #0B2B1F on tinted bg
  danger/warning/info analogous tuned pairs (warning text always dark-on-gold-tint)
Fonts: --du-font-display:"Playfair Display",serif · --du-font-body:"Montserrat",sans-serif
Radius: --du-radius-sm:.5rem / --du-radius:.75rem / --du-radius-lg:1rem
```
Map these onto the Bootstrap vars JQAdm consumes (`--bs-primary`, `--bs-secondary*`, `--bs-bg*`, `--bs-line*`, `--bs-menu*`, `--bs-table-*`, `--bs-btn-*` …) inside `duains-tokens.css` per `body` / `body.dark`.

## Task order

### 1. `public/css/duains-tokens.css` (new)
Token block above + `body`/`body.dark` mapping to `--bs-*` variables + font-family assignment (`body { font-family: var(--du-font-body) }`, headings/brand get display serif).

### 2. Rewrite `public/css/admin-theme.css` (JQAdm overlay, currently 519 lines)
- Remove its local token definitions (now inherited); keep component work only.
- Typography: `.aimeos h1–h6`, panel titles, `.list-header strong` → Playfair Display; body/labels/buttons/tables → Montserrat; uppercase + wide tracking (0.08–0.22em) for nav items, table headers, labels per DESIGN.md.
- Fix every defect listed above: correct `.btn-secondary` fg/bg pair per mode; badges/alerts/status pills get explicit AA pairs; replace `var(--ai-bg)` with safe fallbacks (`var(--du-text, #F4EFE6)`).
- Sidebar/menu: near-black `#000` menu bg, gold left-rule on active item, gradient hover fade (90deg gold 16%→0), DUAINS wordmark in Playfair with letter-spacing.
- Top bar: hairline gold bottom border (`rgba(197,160,89,.28)`), brand in Playfair, sun/moon/logout icons inherit `--du-menu-*`.
- Cards/modals/dropdowns/tabs: `--du-elevated` surfaces, `--du-line` borders, radius tokens, soft shadow tokens; tab active = gold underline.
- Tables: header uppercase tracked muted; row hover `--du-panel-hover`; zebra subtle.
- Micro-interactions: 0.15–0.25s ease transitions on color/bg/border only (60fps-friendly), gold focus-visible rings everywhere, custom scrollbar (gold thumb hover), `::selection` gold/on-gold.
- Keep `body.dark img { filter:brightness(.85) }` guard.

### 3. `resources/views/vendor/shop/jqadm/index.blade.php`
- Font link → `Playfair+Display:wght@500;600;700` + `Montserrat:wght@400;500;600;700`.
- Add `<link>` for `duains-tokens.css` **before** `admin-theme.css` (both with `?v=config('shop.version')` cache-buster).
- Inline style block: keep theme-toggle display rules; drop rules now covered by the CSS files (logo wordmark moves to admin-theme.css using display font).
- No CSP change needed: `config/shop.php:72-73` already allows `fonts.googleapis.com`/`fonts.gstatic.com`.

### 4. `public/css/admin-login.css` (auth screens)
- Swap tokens to canonical set (import or duplicate the `--du-*` block scoped to `.du-auth`; simplest: load `duains-tokens.css` too and delete local `:root` gold/bg/text vars that clash).
- Brand wordmark + card headline → Playfair; form labels/buttons → Montserrat.
- Background radial glow recolored to `rgba(197,160,89,…)`; verify input text/muted placeholder AA on `#15181F` panel.

### 5. Breeze pages (dashboard/profile/app shell)
- `resources/views/layouts/guest.blade.php`: update font href (same two families) + add tokens link before `admin-login.css`.
- `resources/views/layouts/app.blade.php`: add same Google Fonts links + `duains-tokens.css` + new `public/css/admin-breeze.css`; keep `@vite` (harmless).
- New `public/css/admin-breeze.css`: style the Breeze app shell (top nav, dropdown, content card) with the same tokens — dark onyx default matching JQAdm, gold accents, Playfair headings, Montserrat body.
- `resources/views/dashboard.blade.php`: replace placeholder card copy/markup with a minimal branded welcome card (uses new classes).
- `resources/views/profile/*`: keep Breeze structure; adjust wrapper classes so forms/cards pick up brand styling (prefer editing class attributes over duplicating Tailwind utilities).
- `resources/views/auth/login.blade.php` etc.: verify `du-*` classes still resolve after token swap (no structural change expected).

### 6. Cache busting
Bump `version` value in `config/shop.php` (used in all `?v=` query strings) so browsers drop old CSS.

## Acceptance criteria
- Every text/surface pair in both modes ≥ 4.5:1 (body) / 3:1 (large text, icons, UI borders) — spot-check the defect list plus: menu items, table headers, placeholders, disabled buttons, badges (success/warning/danger/info × both modes).
- Gold on screen matches `#C5A059`/`#E8D08D`; dark canvas reads as onyx black; light canvas reads as warm cream.
- Headings/wordmark visibly serif (Playfair); interface text visibly Montserrat; no Inter remnants.
- Theme toggle switches cleanly with no flash-of-unstyled or invisible text in either direction.

## Validation
1. `composer install` (vendor missing locally — required to boot).
2. `php artisan view:clear`; serve via `php artisan serve` (or existing docker-compose).
3. Log in → `/admin/default/jqadm`: exercise dashboard, a product edit form, order list, settings tree in **both** themes (toggle cookie).
4. Check `/login`, `/profile`, `/dashboard` in browser; confirm fonts loaded (DevTools network) and no mixed-token leftovers (grep repo for `C9A96A|E3C68F|Inter` — expect zero outside git history).
5. Contrast: compute fg/bg ratios for the acceptance-criteria pairs (any online WCAG checker or manual luminance math).
6. RTL sanity: flip locale dir once (theme must not depend on LTR-only rules added this round).

## Risks & mitigations
- **JQAdm internals unseen** (vendor absent; Vue-rendered trees/selects have own colors): keep all pre-existing selectors, add generic `--bs-*` mappings first; after `composer install`, screenshot-diff and patch specific stragglers (`.tree`, `.list-view`, `.select2`-like widgets) as follow-up tweaks.
- **Montserrat width** slightly densifies/loosens tables vs Inter: acceptable; tables already have tracked uppercase headers.
- **Static CSS duplication** across files (no build pipeline): mitigated by shared `duains-tokens.css`.

## Out of scope
- Storefront theming (`base.blade.php`, `public/vendor/shop/themes/*`).
- Creating an `ext/`/`packages/` Aimeos extension package.
- Any PHP/business logic, routes, permissions, JQAdm JS behavior.
