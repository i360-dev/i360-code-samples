// scripts/vendor-css.mjs
// Builds trimmed Bootstrap 5.3.0 and Font Awesome 6.4.0 stylesheets by scanning
// the site's markup/JS/CSS for class names actually used. Run: npm run vendor-css
// Re-run after adding any Bootstrap component or FA icon to a view, and commit
// the output in public/vendor/.
import { PurgeCSS } from 'purgecss';
import { copyFileSync, mkdirSync, readdirSync, statSync, writeFileSync } from 'fs';

const content = [
    'resources/views/**/*.blade.php',
    'resources/js/**/*.js',
    'public/js/**/*.js',
    'public/css/**/*.css',
    'app/**/*.php',
];

// Classes Bootstrap's bundle.min.js adds/removes at runtime. Taken from the
// CLASS_NAME_* constants and className/template config in
// node_modules/bootstrap/dist/js/bootstrap.js (v5.3.0), not guessed.
const bootstrapRuntime = [
    // shared state
    'show', 'showing', 'hide', 'hiding', 'active', 'fade', 'disabled',
    // collapse / accordion
    'collapse', 'collapsing', 'collapsed', 'collapse-horizontal',
    // carousel
    'carousel', 'slide', 'pointer-event', 'carousel-item',
    'carousel-item-start', 'carousel-item-end', 'carousel-item-next', 'carousel-item-prev',
    // dropdown (Popper placement classes included)
    'dropdown', 'dropdown-menu', 'dropdown-item', 'dropup', 'dropend', 'dropstart',
    'dropup-center', 'dropdown-center',
    // modal / offcanvas backdrops and body lock
    'modal', 'modal-open', 'modal-static', 'modal-backdrop', 'offcanvas-backdrop',
    // Tooltip/popover templates are deliberately NOT safelisted: the site uses
    // neither and they are ~20% of the remaining CSS. If you add one, safelist
    // tooltip, tooltip-arrow, tooltip-inner, bs-tooltip-auto (or the popover-*
    // equivalents) and re-run.
];

// Always-needed Font Awesome base classes (style prefixes, both v6 and v5 names).
const faBase = [
    'fa', 'fas', 'far', 'fab', 'fa-solid', 'fa-regular', 'fa-brands', 'fa-classic',
    'fa-sharp',
];

const jobs = [
    {
        css: 'node_modules/bootstrap/dist/css/bootstrap.min.css',
        out: 'public/vendor/bootstrap/css/bootstrap.trim.min.css',
        safelist: {
            standard: bootstrapRuntime,
            // Popper sets data-popper-placement on dropdown menus at runtime.
            greedy: [/data-popper-placement/],
        },
    },
    {
        css: 'node_modules/@fortawesome/fontawesome-free/css/all.min.css',
        out: 'public/vendor/fontawesome/css/fa.trim.min.css',
        safelist: { standard: faBase },
    },
];

for (const job of jobs) {
    const [result] = await new PurgeCSS().purge({
        content,
        css: [job.css],
        safelist: job.safelist,
        // Keep @font-face, @keyframes and CSS custom properties (--bs-*, --fa-*)
        // untouched; they are cheap and dropping them breaks things subtly.
        fontFace: false,
        keyframes: false,
        variables: false,
    });
    mkdirSync(job.out.replace(/\/[^/]+$/, ''), { recursive: true });
    writeFileSync(job.out, result.css);
    console.log(`${job.out}: ${statSync(job.css).size} -> ${statSync(job.out).size} bytes`);
}

// FA's CSS references ../webfonts/*; ship woff2 only (every supported browser
// takes it; the .ttf fallbacks are never requested).
const fontSrc = 'node_modules/@fortawesome/fontawesome-free/webfonts';
const fontOut = 'public/vendor/fontawesome/webfonts';
mkdirSync(fontOut, { recursive: true });
for (const f of readdirSync(fontSrc).filter(f => f.endsWith('.woff2'))) {
    copyFileSync(`${fontSrc}/${f}`, `${fontOut}/${f}`);
}
