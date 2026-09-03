
# Aimeos CMS HTML/CSS Rules

Create all HTML/CSS as a **CMS fragment**, intended to be pasted directly into an Aimeos CMS content/HTML block.

## HTML
- Do NOT include `<!DOCTYPE>`, `<html>`, `<head>`, `<body>`, or `<main>`.
- Start with one unique wrapper `<div>` for the entire component/page.
- Give every important element its own descriptive class so its styling can be changed independently.
- Use IDs only when needed for anchors or JavaScript functionality.
- Keep the HTML semantic and copy-paste ready.

## CSS
- CSS must be completely scoped to the unique page/component wrapper.
- NEVER use global selectors such as `:root`, `html`, `body`, `*`, `h1`, `h2`, `p`, `a`, `img`, `section`, `button`, etc.
- Avoid generic class names such as `.hero`, `.button`, `.card`, `.container`, `.title`, `.section`, `.content`, `.product`, etc.
- Use unique descriptive class names, preferably with a page/component prefix.

Example:

```html
<div class="duain-fragrance-page">
    <section class="duain-fragrance-hero">
        <h1 class="duain-fragrance-hero-title">...</h1>
        <p class="duain-fragrance-hero-description">...</p>
        <a class="duain-fragrance-hero-button">...</a>
    </section>
</div>
````

```css
.duain-fragrance-page {
    --brand-gold: #d4af37;
}

.duain-fragrance-page .duain-fragrance-hero {
    ...
}

.duain-fragrance-page .duain-fragrance-hero-title {
    ...
}

.duain-fragrance-page .duain-fragrance-hero-button {
    ...
}
```

## Aimeos Compatibility

* Assume the page is being inserted into an existing Aimeos storefront.
* Do not override or depend on global Aimeos/Bootstrap styles.
* Do not use generic Bootstrap class names unless explicitly required.
* Do not modify vendor files.
* Do not assume control over the surrounding document.
* Keep all styles isolated to the component/page wrapper.
* Make the result responsive for desktop, tablet, and mobile.

## Important

The user's page-specific requirements control the **content, layout, design, imagery, and functionality**.

These rules control the **HTML structure, CSS isolation, naming, and Aimeos compatibility**.

The final HTML and CSS must be **copy-paste ready for an Aimeos CMS content block**.



