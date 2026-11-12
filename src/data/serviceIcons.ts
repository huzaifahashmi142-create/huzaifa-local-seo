export const serviceIconPaths: Record<string, string> = {
  '/services/on-page-seo/': '<path d="M9 4h6l5 5v11a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1z"/><path d="M14 4v5h5"/><path d="M11 13h6M11 16h6M11 10h2"/>',
  '/services/off-page-seo/': '<path d="M9 15l6-6"/><path d="M10 7l1.2-1.2a3.5 3.5 0 0 1 5 5L15 12"/><path d="M14 17l-1.2 1.2a3.5 3.5 0 0 1-5-5L9 12"/>',
  '/services/gmb-optimization/': '<path d="M12 21s-7-6.2-7-11.5A7 7 0 0 1 19 9.5C19 14.8 12 21 12 21z"/><circle cx="12" cy="9.5" r="2.5"/>',
  '/services/website-design/': '<rect x="3" y="4" width="18" height="13" rx="1.5"/><path d="M3 9h18"/><path d="M8 20h8M12 17v3"/>',
  '/services/website-maintenance/': '<path d="M14.7 6.3a4 4 0 0 1-5.4 5.4L4 17l3 3 5.3-5.3a4 4 0 0 1 5.4-5.4l-2.6 2.6-2-2z"/>',
  '/services/content-writing/': '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',
};

export function getServiceIcon(href: string): string {
  return serviceIconPaths[href] ?? '<path d="M13 2L3 14h7l-1 8 10-12h-7l1-8z"/>';
}
