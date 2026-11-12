export type CaseStudy = {
  slug: string;
  category: string;
  client: string;
  city: string;
  title: string;
  summary: string;
  result: string;
  icon: string;
  videoUrl?: string;
};

export const caseStudies: CaseStudy[] = [
  {
    slug: 'construction-company-karachi',
    category: 'Construction',
    client: 'Construction Company',
    city: 'Karachi',
    title: 'From invisible on Maps to top 3 for local construction searches',
    summary: 'A Karachi-based construction and renovation company had an unclaimed Google Business Profile and no ranking content. We rebuilt the profile, added service-area pages and fixed on-page SEO across the site.',
    result: 'Moved into the Google Maps top 3 for "construction company in Karachi" within 4 months.',
    icon: '🏗️',
  },
  {
    slug: 'dental-clinic-lahore',
    category: 'Dentist',
    client: 'Dental Clinic',
    city: 'Lahore',
    title: 'Dental clinic in Lahore triples calls from Google Maps',
    summary: 'A private dental clinic was losing patients to competitors who ranked higher on Maps. We optimized their GMB categories, photos and reviews strategy, and built dedicated treatment pages for SEO.',
    result: 'Call clicks from the Google Business Profile roughly tripled in 3 months.',
    icon: '🦷',
  },
  {
    slug: 'auto-workshop-islamabad',
    category: 'Automobile',
    client: 'Auto Repair Workshop',
    city: 'Islamabad',
    title: 'Auto workshop now ranks for car repair searches across Islamabad',
    summary: 'An automobile repair and servicing workshop had no website and relied only on word of mouth. We built a fast, SEO-ready website and set up local citations across major directories.',
    result: 'Ranking on page 1 for multiple "car repair near me" style searches.',
    icon: '🚗',
  },
  {
    slug: 'furniture-showroom-faisalabad',
    category: 'Furniture',
    client: 'Furniture Showroom',
    city: 'Faisalabad',
    title: 'Furniture showroom builds consistent organic traffic from product pages',
    summary: 'A furniture retailer had thin, duplicate product descriptions. We rewrote content around real buyer search terms and improved internal linking between categories.',
    result: 'Organic sessions to product pages grew steadily month over month.',
    icon: '🛋️',
  },
  {
    slug: 'restaurant-multan',
    category: 'Restaurant',
    client: 'Family Restaurant',
    city: 'Multan',
    title: 'Local restaurant climbs the Maps pack for "restaurant near me"',
    summary: 'A family restaurant had scattered listings and inconsistent business details online. We cleaned up citations, optimized their menu and photos on GMB, and built a review-generation routine.',
    result: 'Visible in the Maps top 3 for core restaurant searches in their area.',
    icon: '🍽️',
  },
  {
    slug: 'real-estate-agency-peshawar',
    category: 'Real Estate',
    client: 'Real Estate Agency',
    city: 'Peshawar',
    title: 'Real estate agency generates more inquiries from location pages',
    summary: 'A real estate agency needed visibility for specific neighborhoods and plot types. We built targeted location and property-type pages with proper on-page SEO and internal linking.',
    result: 'Steady increase in inquiry form submissions from organic search.',
    icon: '🏠',
  },
  {
    slug: 'beauty-salon-quetta',
    category: 'Beauty & Salon',
    client: 'Beauty Salon',
    city: 'Quetta',
    title: 'Beauty salon improves local discovery with an optimized profile',
    summary: 'A beauty salon had an incomplete Google Business Profile with no service list or recent photos. We rebuilt the profile structure and added service-specific keywords.',
    result: 'Noticeable increase in profile views and direction requests.',
    icon: '💇',
  },
];
