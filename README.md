# Huzaifa Local SEO — Astro Website

Full Astro source for the Huzaifa Local SEO website, built from the supplied markdown
content and the hero design reference.

## Run it locally

```bash
npm install
npm run dev       # http://localhost:4321
npm run build     # outputs static site to dist/
npm run preview   # serve the production build locally
```

Requires Node 18.17+ (works on the Node 22 used to author this project).

## What's included

- 23 pages: homepage, About, 6 service pages (On-Page SEO, Off-Page SEO, GMB
  Optimization, Website Design, Website Maintenance, Content Writing), a
  services index, 8 city local-SEO pages (Karachi, Lahore, Islamabad,
  Faisalabad, Multan, Peshawar, Quetta, Sialkot) built from one data file and
  a shared layout, a locations index, Contact, FAQ hub, Privacy Policy, Terms
  & Conditions, and a custom 404.
- Dark navy / blue design system in `src/styles/global.css`, matching the
  supplied hero reference (badge, large heading, stat row, dual CTA, right-side
  visual panel with a floating stat card). No stock photo of a person is used
  anywhere, since none was supplied — the hero and other visuals are original,
  code-built graphics instead.
- SEO: per-page title/description/canonical, Open Graph + Twitter tags,
  JSON-LD (`Organization`, `WebSite`, `Service`, `FAQPage`, `BreadcrumbList`),
  `@astrojs/sitemap` (generates `/sitemap-index.xml` at build time), and
  `public/robots.txt` pointing to it.
- Fully responsive (desktop/tablet/mobile), keyboard-accessible nav and FAQ
  accordions (`<details>`), a skip-to-content link, and `prefers-reduced-motion`
  handling.
- Internal linking: header/footer nav, service cross-links on every service
  page, "explore our services" on every location page, breadcrumbs everywhere.

## Content notes / honesty flags

Per the brief's rule against fabricating business facts, the following were
deliberately left out or handled conservatively:

- **No portfolio / case studies and no blog** were built, because no verified
  client projects, results or blog drafts were supplied. Adding either would
  have meant inventing clients, numbers or articles.
- **No testimonials** — the source markdown only had `[Client name]` /
  `[Quote]` placeholders; these were removed rather than filled with invented
  quotes.
- **No street address or LocalBusiness schema** — no physical office address
  was supplied, so the site does not claim one. `Service` and `Organization`
  schema are used instead of `LocalBusiness`.
- **Contact form** has no backend (none was supplied/authorized). Submitting
  it builds a pre-filled `mailto:` link using the account email on file
  (`huzaifahashmi142@gmail.com`) so the form is genuinely functional rather
  than a decorative dead end. Swap this for a real form backend (e.g. a
  serverless function or a form service) when one is available.
- **Off-Page SEO page** has no dedicated source file in the supplied content.
  Its copy was written from the Off-Page SEO service consistently described
  across `about-us.md` and every city page (listings, citations, guest
  posting, relevant backlinks) rather than invented from scratch.
- Effective dates on Privacy Policy / Terms are set to "October 2026"
  (today's month) since no specific date was supplied — update if a different
  effective date is needed.

## A note on verification

This project was authored in a sandboxed environment where outbound access to
the npm registry was blocked, so `npm install` / `npm run build` could not be
run here to produce a compiled preview. Every `.astro`/`.ts`/`.css`/`.json`
file was instead checked by hand: all relative imports resolve to real files,
braces/parens balance, `cities.ts` parses cleanly under Node's TypeScript
syntax checker and contains all fields the shared `LocationLayout` reads, and
every internal link uses a consistent trailing slash matching
`trailingSlash: 'always'` in `astro.config.mjs`. Please run `npm install &&
npm run build` on your machine (or in CI) as the final check before deploying.
