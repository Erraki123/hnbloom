<?php

namespace App\Console\Commands;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class ScrapeSolutions extends Command
{
    protected $signature = 'solutions:scrape
                            {--only= : Slug de la solution (ex: detection-incendi)}
                            {--force : Retélécharger même si le fichier existe déjà}
                            {--insecure : Ignorer la vérification SSL (utile sous Windows)}';

    protected $description = 'Télécharge solutions → catégories → produits (images + textes + PDF) de HN BLOOM';

    /** Le slug sert d'URL locale : /solutions/{slug} (doit correspondre à ton index des solutions) */
    private const SOLUTIONS = [
        'obstacles-physiques-automatise' => ['title' => 'Obstacles physiques automatisés', 'url' => 'https://hnbloom.com/obstacles-physiques-automatise/'],
        'hygiene-agro-alimentaire'       => ['title' => 'Hygiène Agro-alimentaire',        'url' => 'https://hnbloom.com/categorie-produit/hygiene-agro-alimentaire/'],
        'securite-electroniques'         => ['title' => 'Sécurité électronique',           'url' => 'https://hnbloom.com/securite-electroniques/'],
        'detection-incendi'              => ['title' => 'Détection incendie',              'url' => 'https://hnbloom.com/detection-incendi/'],
    ];

    private const MAX_DEPTH = 3;

    /** segments d'URL à ignorer (menu, footer, pages hors catalogue) */
    private const IGNORE = [
        'qui-somme-nous', 'qui-sommes-nous', 'no-solution', 'domotique', 'projects', 'contact', 'contact-us',
        'cart', 'panier', 'mon-compte', 'my-account', 'checkout', 'smart-hotel', 'smart-home', 'smart-hospital',
        'wp-admin', 'wp-login.php',
    ];

    private array $cache = [];     // url image => chemin local
    private array $products = [];  // slug => produit
    private array $visited = [];
    private array $failed = [];
    private int $ok = 0;
    private int $skipped = 0;

    public function handle(): int
    {
        $only = $this->option('only');
        $solutions = [];

        foreach (self::SOLUTIONS as $slug => $cfg) {
            if ($only && $only !== $slug) {
                continue;
            }

            $this->info("=== {$cfg['title']}");
            $root = $this->crawl($cfg['url'], $slug, 1, ['title' => $cfg['title']]);
            if (! $root) {
                continue;
            }

            $solutions[] = [
                'slug'       => $slug,
                'title'      => $cfg['title'],
                'eyebrow'    => 'SOLUTION',
                'lead'       => $root['lead'],
                'image'      => $root['image'],
                'video'      => null,
                'fallback'   => null,
                'categories' => $root['children'],
                'products'   => $root['products'],
                'offers'     => [],
                'benefits'   => [],
                'targets'    => [],
            ];
        }

        if ($only) {
            $by = [];
            foreach ($this->readJson('solutions.json') as $s) {
                $by[$s['slug']] = $s;
            }
            foreach ($solutions as $s) {
                $by[$s['slug']] = $s;
            }
            $solutions = array_values($by);
            $this->products = $this->products + $this->readJson('products.json');
        }

        $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
        File::ensureDirectoryExists(storage_path('app'));
        File::put(storage_path('app/solutions.json'), json_encode($solutions, $flags));
        File::put(storage_path('app/products.json'), json_encode($this->products, $flags));

        $this->newLine();
        $this->info("Téléchargées : {$this->ok} | Déjà présentes : {$this->skipped} | Échecs : " . count($this->failed));
        $this->info('Solutions : ' . count($solutions) . ' | Produits : ' . count($this->products));

        if ($this->failed) {
            $this->warn('En échec : ' . implode(', ', array_unique($this->failed)));
            return self::FAILURE;
        }

        $this->comment('Terminé. Lancez : php artisan optimize:clear');
        return self::SUCCESS;
    }

    private function crawl(string $url, string $solution, int $depth, array $hint = []): ?array
    {
        $key = rtrim($url, '/');
        if (isset($this->visited[$key])) {
            return null;
        }
        $this->visited[$key] = true;

        $this->line(str_repeat('  ', $depth - 1) . "→ {$url}");

        $html = $this->fetch($url);
        if (! $html) {
            $this->warn('  ✘ page inaccessible');

            // Page 404 sur le site source : on garde quand même la carte (titre + image)
            if ($depth > 1 && ! empty($hint['title'])) {
                $img = ! empty($hint['image']) ? $this->download($hint['image'], 'images/solutions/' . $solution) : '';

                return [
                    'slug'     => $this->slugOf($url),
                    'title'    => $hint['title'],
                    'url'      => $url,
                    'image'    => $img,
                    'lead'     => '',
                    'products' => [],
                    'children' => [],
                ];
            }

            $this->failed[] = $url;
            return null;
        }

        $xp = $this->xpath($html);
        $og = (string) $xp->evaluate('string(//meta[@property="og:image"]/@content)');

        $menu = '';
        if ($depth === 1) {
            $img = $xp->query('//a[@href="' . $url . '"]//img')->item(0);
            if ($img) {
                $menu = $this->imgSrc($img, $url);
            }
        }

        $this->stripChrome($xp);
        $content = $xp->query('//*[@id="content"]')->item(0) ?? $xp->query('//body')->item(0);

        $slug = $this->slugOf($url);
        $h = $xp->query('.//h1|.//h2', $content)->item(0);
        $title = ($hint['title'] ?? '') ?: ($h ? $this->clean($h->textContent) : ucwords(str_replace('-', ' ', $slug)));

        $lead = '';
        foreach ($xp->query('.//p', $content) as $p) {
            $t = $this->clean($p->textContent);
            if (mb_strlen($t) > 40) {
                $lead = $t;
                break;
            }
        }

        $cand = $depth === 1 ? [$og, $menu] : [$hint['image'] ?? '', $og];
        $image = '';
        foreach ($cand as $c) {
            if ($c && ! preg_match('/fav-icon|logo/i', $c)) {
                $image = $this->download($c, 'images/solutions/' . $solution);
                if ($image) {
                    break;
                }
            }
        }

        $all = $this->collectLinks($xp, $content, $url);

        $productLinks = array_filter($all, fn ($k) => str_contains($k, '/produit/'), ARRAY_FILTER_USE_KEY);

        $pages = [];
        foreach ($xp->query('.//a[contains(@class,"page-numbers")]', $content) as $a) {
            $pages[$this->absolute($a->getAttribute('href'), $url)] = true;
        }
        foreach (array_keys($pages) as $pu) {
            $ph = $this->fetch($pu);
            if (! $ph) {
                continue;
            }
            $px = $this->xpath($ph);
            $this->stripChrome($px);
            $pc = $px->query('//*[@id="content"]')->item(0);
            if ($pc) {
                $productLinks += array_filter($this->collectLinks($px, $pc, $pu), fn ($k) => str_contains($k, '/produit/'), ARRAY_FILTER_USE_KEY);
            }
        }

        $productSlugs = [];
        foreach ($productLinks as $pu => $info) {
            if ($s = $this->scrapeProduct($pu, $solution, $slug, $info)) {
                $productSlugs[] = $s;
            }
        }
        $productSlugs = array_values(array_unique($productSlugs));

        $children = [];
        if ($depth < self::MAX_DEPTH) {
            $subs = [];
            $lis = $xp->query('.//li[contains(@class,"product-category")]', $content);

            if ($lis->length) {
                foreach ($lis as $li) {
                    $subs += $this->collectLinks($xp, $li, $url);
                }
            } elseif (! $productSlugs) {
                $subs = array_filter($all, fn ($i, $k) => ! str_contains($k, '/produit/') && $i['image'] !== '', ARRAY_FILTER_USE_BOTH);
            }

            foreach ($subs as $su => $info) {
                if ($child = $this->crawl($su, $solution, $depth + 1, $info)) {
                    $children[] = $child;
                }
            }
        }

        return [
            'slug'     => $slug,
            'title'    => $title,
            'url'      => $url,
            'image'    => $image,
            'lead'     => $lead,
            'products' => $productSlugs,
            'children' => $children,
        ];
    }

    private function scrapeProduct(string $url, string $solution, string $category, array $hint = []): ?string
    {
        $slug = $this->slugOf($url);
        if (isset($this->products[$slug])) {
            return $slug;
        }

        $this->line("      • produit {$slug}");
        $html = $this->fetch($url);
        if (! $html) {
            $this->failed[] = $url;
            return null;
        }

        $xp = $this->xpath($html);
        $this->stripChrome($xp);

        $box = $xp->query('//*[starts-with(@id,"product-")]')->item(0);
        if (! $box || ! $xp->query('.//h1', $box)->length) {
            $box = $xp->query('//*[@id="content"]')->item(0) ?? $xp->query('//body')->item(0);
        }

        foreach (iterator_to_array($xp->query('.//*[contains(@class,"related") or contains(@class,"upsells")]', $box)) as $n) {
            $n->parentNode?->removeChild($n);
        }

        $h1 = $xp->query('.//h1', $box)->item(0);
        $title = $h1 ? $this->clean($h1->textContent) : ($hint['title'] ?? $slug);

        $subtitle = '';
        if ($h1) {
            $n = $xp->query('following::*[self::h2 or self::h3 or self::h4][1]', $h1)->item(0);
            if ($n) {
                $t = $this->clean($n->textContent);
                if ($t && ! preg_match('/description|related|similaires|qui somme|contact|liens/i', $t)) {
                    $subtitle = $t;
                }
            }
        }

        $features = [];
        foreach ($xp->query('.//li', $box) as $li) {
            $t = $this->clean($li->textContent);
            $len = mb_strlen($t);
            if ($len > 3 && $len < 300 && ! in_array($t, $features, true)) {
                $features[] = $t;
            }
        }

        $paras = [];
        foreach ($xp->query('.//p', $box) as $p) {
            $t = $this->clean($p->textContent);
            if (mb_strlen($t) > 30 && ! in_array($t, $paras, true) && ! in_array($t, $features, true)) {
                $paras[] = $t;
            }
        }

        $urls = [];
        foreach ($xp->query('.//a[@href]', $box) as $a) {
            $h = $a->getAttribute('href');
            if (preg_match('/\.(jpe?g|png|webp|avif|gif)(\?.*)?$/i', $h)) {
                $urls[] = $this->absolute($h, $url);
            }
        }
        foreach ($xp->query('.//img', $box) as $img) {
            $v = $img->getAttribute('data-large_image') ?: $this->imgSrc($img, $url);
            if ($v) {
                $urls[] = $this->absolute($v, $url);
            }
        }

        $gallery = [];
        $seen = [];
        foreach ($urls as $u) {
            if (! str_contains($u, '/wp-content/uploads/')) {
                continue;
            }
            $orig = preg_replace('/-\d{2,4}x\d{2,4}(\.\w+)$/', '$1', $u);
            if (isset($seen[$orig])) {
                continue;
            }
            $seen[$orig] = true;
            if ($p = $this->download($u, 'images/products/' . $slug)) {
                $gallery[] = $p;
            }
        }
        if (! $gallery && ! empty($hint['image'])) {
            if ($p = $this->download($hint['image'], 'images/products/' . $slug)) {
                $gallery[] = $p;
            }
        }

        $pdfs = [];
        foreach ($xp->query('.//a[@href]', $box) as $a) {
            $h = $a->getAttribute('href');
            if (preg_match('/\.pdf(\?.*)?$/i', $h)) {
                if ($p = $this->downloadFile($this->absolute($h, $url), 'docs/products/' . $slug)) {
                    $pdfs[] = ['title' => $this->clean($a->textContent) ?: 'Fiche technique', 'file' => $p];
                }
            }
        }

        $this->products[$slug] = [
            'slug'       => $slug,
            'title'      => $title,
            'subtitle'   => $subtitle,
            'url'        => $url,
            'solution'   => $solution,
            'category'   => $category,
            'image'      => $gallery[0] ?? '',
            'gallery'    => $gallery,
            'features'   => $features,
            'paragraphs' => $paras,
            'pdfs'       => $pdfs,
        ];

        return $slug;
    }

    private function http()
    {
        $http = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120 Safari/537.36',
        ])->timeout(60)->retry(2, 500);

        return $this->option('insecure') ? $http->withoutVerifying() : $http;
    }

    private function fetch(string $url): ?string
    {
        try {
            $res = $this->http()->get($url);
            return $res->successful() ? $res->body() : null;
        } catch (\Throwable $e) {
            $this->warn("  ✘ {$e->getMessage()}");
            return null;
        }
    }

    private function xpath(string $html): DOMXPath
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        libxml_clear_errors();
        return new DOMXPath($dom);
    }

    private function stripChrome(DOMXPath $xp): void
    {
        $q = '//script|//style|//noscript|//nav|//form'
           . '|//*[@data-elementor-type="header"]|//*[@data-elementor-type="footer"]'
           . '|//*[contains(@class,"elementor-location-header")]|//*[contains(@class,"elementor-location-footer")]';

        foreach (iterator_to_array($xp->query($q)) as $n) {
            $n->parentNode?->removeChild($n);
        }
    }

    private function readJson(string $file): array
    {
        $p = storage_path('app/' . $file);
        return is_file($p) ? (json_decode(file_get_contents($p), true) ?: []) : [];
    }

    private function slugOf(string $url): string
    {
        $parts = array_values(array_filter(explode('/', parse_url($url, PHP_URL_PATH) ?? '')));
        return end($parts) ?: 'home';
    }

    private function clean(?string $t): string
    {
        return trim(preg_replace('/\s+/u', ' ', $t ?? ''));
    }

    private function absolute(string $u, string $base): string
    {
        $u = trim($u);
        if (preg_match('#^https?://#i', $u)) {
            return $u;
        }

        $p = parse_url($base);
        $root = $p['scheme'] . '://' . $p['host'];

        if (str_starts_with($u, '//')) {
            return $p['scheme'] . ':' . $u;
        }
        if (str_starts_with($u, '/')) {
            return $root . $u;
        }

        return $root . rtrim(dirname($p['path'] ?? '/'), '/') . '/' . $u;
    }

    private function imgSrc(DOMNode $img, string $base): string
    {
        foreach (['data-lazy-src', 'data-src', 'data-original', 'src'] as $a) {
            $v = $img->getAttribute($a);
            if ($v && ! str_starts_with($v, 'data:')) {
                return $this->absolute($v, $base);
            }
        }
        return '';
    }

    /** @return array<string,array{title:string,image:string}> url => infos */
    private function collectLinks(DOMXPath $xp, DOMNode $scope, string $base): array
    {
        $host = parse_url($base, PHP_URL_HOST);
        $self = rtrim(explode('?', $base)[0], '/');
        $out = [];

        foreach ($xp->query('.//a[@href]', $scope) as $a) {
            $href = trim($a->getAttribute('href'));
            if ($href === '' || str_starts_with($href, '#') || preg_match('#^(mailto|tel|javascript):#i', $href)) {
                continue;
            }

            $u = explode('?', explode('#', $this->absolute($href, $base))[0])[0];
            $p = parse_url($u);

            if (($p['host'] ?? '') !== $host) {
                continue;
            }
            $path = $p['path'] ?? '/';
            if ($path === '/' || rtrim($u, '/') === $self) {
                continue;
            }
            if (preg_match('/\.(jpe?g|png|webp|gif|avif|svg|pdf|zip)$/i', $path)) {
                continue;
            }
            if (preg_match('#/page/\d+/?$#', $path)) {
                continue;
            }

            $segments = array_filter(explode('/', $path));
            foreach (self::IGNORE as $bad) {
                if (in_array($bad, $segments, true)) {
                    continue 2;
                }
            }

            $u = rtrim($u, '/') . '/';
            $out[$u] ??= ['title' => '', 'image' => ''];

            $t = $this->clean($a->textContent);
            if (mb_strlen($t) > mb_strlen($out[$u]['title']) && ! preg_match('/add to cart|ajouter au panier/i', $t)) {
                $out[$u]['title'] = $t;
            }
            if (! $out[$u]['image'] && ($img = $xp->query('.//img', $a)->item(0))) {
                $out[$u]['image'] = $this->imgSrc($img, $base);
            }
        }

        return $out;
    }

    private function download(string $url, string $relDir): string
    {
        if (preg_match('/logo|icon|favicon|sprite|placeholder/i', basename(parse_url($url, PHP_URL_PATH) ?? ''))) {
            return '';
        }

        $orig = preg_replace('/-\d{2,4}x\d{2,4}(\.\w+)$/', '$1', $url);
        $try = array_unique([$orig, $url]);

        foreach ($try as $u) {
            if (isset($this->cache[$u])) {
                return $this->cache[$u];
            }
        }

        File::ensureDirectoryExists(public_path($relDir));

        foreach ($try as $u) {
            $name = preg_replace('/[^\w.\-]/', '_', basename(parse_url($u, PHP_URL_PATH)));
            $rel = $relDir . '/' . $name;
            $target = public_path($rel);

            if (File::exists($target) && ! $this->option('force')) {
                $this->skipped++;
                return $this->cache[$orig] = $this->cache[$u] = $rel;
            }

            try {
                $res = $this->http()->get($u);
                if ($res->successful() && str_contains((string) $res->header('Content-Type'), 'image') && strlen($res->body()) > 1000) {
                    File::put($target, $res->body());
                    $this->ok++;
                    $this->line("        ✔ {$name}");
                    return $this->cache[$orig] = $this->cache[$u] = $rel;
                }
            } catch (\Throwable $e) {
            }
        }

        $this->failed[] = basename(parse_url($orig, PHP_URL_PATH));
        $this->warn('        ✘ ' . basename(parse_url($orig, PHP_URL_PATH)));
        return '';
    }

    private function downloadFile(string $url, string $relDir): string
    {
        $name = preg_replace('/[^\w.\-]/', '_', basename(parse_url($url, PHP_URL_PATH)));
        $rel = $relDir . '/' . $name;
        $target = public_path($rel);

        File::ensureDirectoryExists(public_path($relDir));

        if (File::exists($target) && ! $this->option('force')) {
            $this->skipped++;
            return $rel;
        }

        try {
            $res = $this->http()->timeout(180)->get($url);
            if ($res->successful()) {
                File::put($target, $res->body());
                $this->ok++;
                $this->line("        ✔ PDF {$name}");
                return $rel;
            }
        } catch (\Throwable $e) {
        }

        $this->failed[] = $name;
        return '';
    }
}
