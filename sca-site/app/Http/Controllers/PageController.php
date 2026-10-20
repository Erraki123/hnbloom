<?php

namespace App\Http\Controllers;

use App\Support\SolutionCatalog;

class PageController extends Controller
{
    public function about()
    {
        return view('pages.about');
    }

    public function solutions()
    {
        return view('pages.solutions');
    }

    public function solution($slug)
    {
        $f = SolutionCatalog::solution($slug);
        abort_if(! $f, 404);

        $f['lead'] = $f['lead'] ?: 'Découvrez notre gamme de produits et solutions.';

        return view('solution', compact('f'));
    }

    public function solutionCategory($solution, $category)
    {
        $f = SolutionCatalog::solution($solution);
        abort_if(! $f, 404);

        $cat = SolutionCatalog::category($f['categories'], $category);
        abort_if(! $cat, 404);

        $products = SolutionCatalog::products($cat['products'] ?? []);

        return view('solution-category', compact('f', 'cat', 'products'));
    }

    public function product($slug)
    {
        $p = SolutionCatalog::product($slug);
        abort_if(! $p, 404);

        $f = SolutionCatalog::solution($p['solution']);
        $cat = $f ? SolutionCatalog::category($f['categories'], $p['category']) : null;
        $related = SolutionCatalog::related($p);

        return view('solution-product', compact('p', 'f', 'cat', 'related'));
    }

    public function domotique()
    {
        return view('pages.domotique');
    }

    public function domotiqueMarket(string $slug)
    {
        $market = collect(config('hnbloom.domotique.markets'))->firstWhere('slug', $slug);
        abort_unless($market, 404);

        return view('pages.domotique-market', compact('market'));
    }

    public function projects()
    {
        return view('pages.projects', [
            'projects' => config('portfolio.projects'),
        ]);
    }

    public function project(string $slug)
    {
        $list = collect(config('portfolio.projects'))->values();
        $i = $list->search(fn ($p) => $p['slug'] === $slug);

        abort_if($i === false, 404);

        return view('pages.project-show', [
            'project' => $list[$i],
            'prev'    => $list->get($i - 1),
            'next'    => $list->get($i + 1),
        ]);
    }

    public function reseller()
    {
        return view('pages.reseller');
    }
}
