import { siteConfig, mainNav, services, locations } from './nav';

// Bump this whenever page content changes. It feeds dateModified in every WebPage schema.
export const siteLastModified = '2026-11-14';

export const orgId = `${siteConfig.url}/#organization`;

// The one Organization object for the whole site. Other schema blocks point to it by @id.
export const organization = {
  '@context': 'https://schema.org',
  '@type': 'Organization',
  '@id': orgId,
  name: siteConfig.name,
  url: siteConfig.url,
  logo: `${siteConfig.url}/favicon.svg`,
  areaServed: 'PK',
  sameAs: [],
};

const labels: Record<string, string> = Object.fromEntries(
  [...mainNav, ...services, ...locations].map((i) => [i.href, i.title])
);
labels['/about-us/'] = 'About Us';
labels['/privacy-policy/'] = 'Privacy Policy';
labels['/terms-and-conditions/'] = 'Terms & Conditions';
labels['/zip-codes/'] = 'Zip Codes';
labels['/sitemap/'] = 'Sitemap';

export function breadcrumbsFor(path: string) {
  const segs = path.split('/').filter(Boolean);
  const items = [{ name: 'Home', item: `${siteConfig.url}/` }];
  let acc = '';
  for (const s of segs) {
    acc += `/${s}`;
    const href = `${acc}/`;
    const name = labels[href] ?? s.replace(/-/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
    items.push({ name, item: `${siteConfig.url}${href}` });
  }
  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: items.map((it, i) => ({ '@type': 'ListItem', position: i + 1, name: it.name, item: it.item })),
  };
}

export function webPageFor(opts: { name: string; description: string; url: string }) {
  return {
    '@context': 'https://schema.org',
    '@type': 'WebPage',
    name: opts.name,
    description: opts.description,
    url: opts.url,
    isPartOf: { '@type': 'WebSite', url: `${siteConfig.url}/` },
    about: { '@id': orgId },
    publisher: { '@id': orgId },
    dateModified: siteLastModified,
  };
}
