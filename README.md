# i360-llc.com — code samples

Selected files from the Laravel 12 site I built for my company, Insight360 of Northeast Ohio.
Live site: https://www.i360-llc.com

The full repo stays private because it also holds admin tooling and environment config.
These files are copied from it unchanged.

---

## 1. Front-end performance pass (Sept 2026)

**Problem.** The homepage hero was loading full-size JPGs (430–800 KB each, plus a 16 MB
photo on another page), and every page pulled the full Bootstrap and Font Awesome CSS
even though the site uses a small slice of each.

**What I changed**

| File | What it does |
|---|---|
| `scripts/images.mjs` | Uses `sharp` to generate 800/1200/1600 px WebP and JPG versions of each hero image. |
| `views/components/hero-carousel.blade.php` | `<picture>` with WebP + JPG `srcset`, explicit `width`/`height` to avoid layout shift, `fetchpriority` high on the first slide only, and a `<link rel="preload">` pushed into the layout head for the LCP image. |
| `scripts/vendor-css.mjs` | PurgeCSS build that scans Blade/JS/PHP for classes actually used. Classes Bootstrap's JS adds at runtime are safelisted from the constants in Bootstrap's own source, not guessed. Tooltip/popover styles are left out on purpose, with a note on how to add them back. |
| `views/layouts/app.blade.php` | Serves the trimmed, self-hosted CSS with `filemtime` cache-busting. Bootstrap stays render-blocking (layout depends on it). Font Awesome, Google Fonts and AOS load non-blocking with a `<noscript>` fallback. |

**Results**

- Bootstrap CSS: 233 KB → 54 KB (−77%)
- Font Awesome CSS: 102 KB → 15 KB (−85%), woff2 fonts only
- Hero images: 430–800 KB source JPGs → 24–69 KB WebP at 800 px; 16 MB SOC photo replaced with responsive WebP

**Why I'm proud of it:** The changes in the core systems, the images and so on, greatly increased the overall speed of the site from click-to-delivery. Additionally, I was able to add some tweaks to improve LLM Searchability.

---

## 2. Contact form and email delivery

| File | What it does |
|---|---|
| `app/Http/Controllers/ContactController.php` | Validates the form, drops bots with a honeypot field and a minimum fill time, stores the lead, and sends a notification and a confirmation. Email failures are logged but never lose the submission. |
| `app/Services/MicrosoftGraphMailService.php` | Sends mail through the Microsoft Graph API with the OAuth2 client-credentials flow, no mail package. The access token is cached for 50 minutes (tokens last ~60). The same request sequence was checked in PowerShell before it was ported to PHP. |
| `app/Http/Middleware/SecurityHeaders.php` | CSP, HSTS (production only), and standard hardening headers on every response. |

**Why I'm proud of it:** The mail delivery took more work than it looked like it would. The old setup only worked because a Graph mail package happened to be sitting in `vendor/` without being listed in `composer.json`, so delivery was sporadic and couldn't be reproduced on a fresh deploy. Microsoft is also retiring basic-auth SMTP for Exchange Online, so I moved sending to the Microsoft Graph API with a small service I control instead of another package.

The protection lives in the controller: Laravel validation on every field, the message body escaped before it's sent, and a hidden honeypot field plus a 3-second minimum fill time to drop bots. If Graph rejects a request, the service throws and the error is logged, but the lead is always saved first. Every message is saved to the mailbox's Sent Items, so we have a record. The person who writes in gets a confirmation, and our lead notice has reply-to set to them so we can answer directly.

**What I'd improve next:** I am presently working on a portal system for the MSP side of our business. I would ultimately like to have both systems interact with each other so that we can centrally control as much as we can from one location. The portal, by the way, would use SSO so it will be protected by Microsoft's own MFA process.

---

## 3. Cookie consent (plain JavaScript)

| File | What it does |
|---|---|
| `public/js/cookies.js` | Consent banner and settings modal written with native DOM APIs, no jQuery or framework. Preferences are stored in `localStorage` with a version number, so a material policy change re-prompts visitors, and a corrupt stored value is ignored instead of breaking the page. |
| `public/js/tracking-scripts.js` | A registry of third-party scripts grouped by category. `cookies.js` only loads a category after the visitor opts in, so adding a new tag never touches the consent logic. |

**Why it matters:** Marketing and analytics scripts don't load at all until a visitor agrees to them. The site's security headers only allow those scripts' domains, so the consent gate and the CSP work together.

---

## How this was built

Using what PageSpeed reported, I worked through the issues one at a time. I used Claude Code to speed up writing a couple of build scripts, which meant I didn't have to resize photos by hand in Photoshop. That saved me an hour or two. I reviewed the code myself and talked through the approach with Claude to make sure I was headed down the right road.

For the mail service, Claude Code helped write the first version. The Graph token and send calls were checked in PowerShell before the PHP was written, and the service was tested in Tinker before the contact form was switched over to it. I can walk through every line of all three samples.

## About me

Vivek Bagal — https://www.linkedin.com/in/vivekbagal/
