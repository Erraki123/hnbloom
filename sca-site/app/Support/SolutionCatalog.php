<?php

namespace App\Support;

class SolutionCatalog
{
    private static ?array $solutions = null;
    private static ?array $products = null;

    /** ancien slug (index du site) => slug du scraper */
    private const ALIASES = [
        'obstacles-physiques-automatises' => 'obstacles-physiques-automatise',
        'securite-electronique'           => 'securite-electroniques',
        'detection-incendie'              => 'detection-incendi',
    ];

private static function read(string $file): array
{
    foreach ([database_path('data/' . $file), storage_path('app/' . $file)] as $path) {
        if (is_file($path)) {
            return json_decode(file_get_contents($path), true) ?: [];
        }
    }

    return [];
}

    public static function solutions(): array
    {
        return self::$solutions ??= self::read('solutions.json');
    }

    public static function allProducts(): array
    {
        return self::$products ??= self::read('products.json');
    }

    public static function solution(string $slug): ?array
    {
        $slug = self::ALIASES[$slug] ?? $slug;

        foreach (self::solutions() as $s) {
            if ($s['slug'] === $slug) {
                return $s;
            }
        }

        return null;
    }

    /** Cherche une catégorie (récursif) par slug */
    public static function category(array $nodes, string $slug): ?array
    {
        foreach ($nodes as $n) {
            if ($n['slug'] === $slug) {
                return $n;
            }
            if ($found = self::category($n['children'] ?? [], $slug)) {
                return $found;
            }
        }

        return null;
    }

    public static function product(string $slug): ?array
    {
        return self::allProducts()[$slug] ?? null;
    }

    public static function products(array $slugs): array
    {
        return array_values(array_filter(array_map(fn ($s) => self::product($s), $slugs)));
    }

    public static function count(array $node): int
    {
        $n = count($node['products'] ?? []);
        foreach ($node['children'] ?? [] as $c) {
            $n += self::count($c);
        }

        return $n;
    }

public static function related(array $p, int $limit = 4): array
{
    $list = array_values(array_filter(self::allProducts(), fn ($x) => $x['category'] === $p['category'] && $x['slug'] !== $p['slug']));
    if (!empty($p['group'])) {
        usort($list, fn ($a, $b) => (($b['group'] ?? null) === $p['group']) <=> (($a['group'] ?? null) === $p['group']));
    }
    return array_slice($list, 0, $limit);
}
}
