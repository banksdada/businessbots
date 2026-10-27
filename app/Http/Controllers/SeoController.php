<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * SEO endpoints — sitemap.xml.
 *
 * Everything here is public and unauthenticated. URLs are derived from the
 * route list so adding a new page means adding a route, not touching this
 * controller.
 */
class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = $this->urls();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.e($url['loc'])."</loc>\n";
            if (! empty($url['lastmod'])) {
                $xml .= '    <lastmod>'.e($url['lastmod'])."</lastmod>\n";
            }
            if (! empty($url['changefreq'])) {
                $xml .= '    <changefreq>'.e($url['changefreq'])."</changefreq>\n";
            }
            $xml .= "  </url>\n";
        }

        $xml .= "</urlset>\n";

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    /**
     * The public, indexable pages of the site.
     *
     * lastmod dates are static — the marketing pages are hand-edited, not
     * generated from a CMS, so a hard-coded date is accurate. If we ever move
     * to a CMS, switch this to read the post's updated_at.
     */
    private function urls(): array
    {
        $domain = rtrim(config('legal.domain'), '/');

        $urls = [
            ['loc' => $domain.'/', 'changefreq' => 'weekly', 'lastmod' => '2026-10-26'],
            ['loc' => $domain.'/pricing', 'changefreq' => 'monthly', 'lastmod' => '2026-10-26'],
            ['loc' => $domain.'/industries', 'changefreq' => 'monthly', 'lastmod' => '2026-10-26'],
            ['loc' => $domain.'/demo', 'changefreq' => 'monthly', 'lastmod' => '2026-10-26'],
            ['loc' => $domain.'/contact', 'changefreq' => 'yearly', 'lastmod' => '2026-10-26'],
            ['loc' => $domain.'/insights', 'changefreq' => 'weekly', 'lastmod' => '2026-10-26'],
            ['loc' => $domain.'/insights/you-dont-need-ai', 'changefreq' => 'monthly', 'lastmod' => '2026-10-24'],
            ['loc' => $domain.'/insights/two-minute-test', 'changefreq' => 'monthly', 'lastmod' => '2026-10-24'],
            ['loc' => $domain.'/insights/human-review', 'changefreq' => 'monthly', 'lastmod' => '2026-10-24'],
            ['loc' => $domain.'/ai-audit/', 'changefreq' => 'monthly', 'lastmod' => '2026-10-27'],
            ['loc' => $domain.'/career/', 'changefreq' => 'monthly', 'lastmod' => '2026-10-27'],
            ['loc' => $domain.'/consult/', 'changefreq' => 'monthly', 'lastmod' => '2026-10-27'],
            ['loc' => $domain.'/terms', 'changefreq' => 'yearly', 'lastmod' => '2026-08-17'],
            ['loc' => $domain.'/privacy', 'changefreq' => 'yearly', 'lastmod' => '2026-10-26'],
            ['loc' => $domain.'/cookies', 'changefreq' => 'yearly', 'lastmod' => '2026-08-17'],
        ];

        if (config('features.social')) {
            $urls[] = ['loc' => $domain.'/live-proof', 'changefreq' => 'weekly', 'lastmod' => '2026-10-26'];
        }

        return $urls;
    }
}
