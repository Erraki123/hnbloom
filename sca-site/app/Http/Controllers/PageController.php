<?php

namespace App\Http\Controllers;

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

    public function solution(string $slug)
    {
        $family = config("hnbloom.families.$slug");
        abort_unless($family, 404);

        return view('pages.solution', ['slug' => $slug, 'f' => $family]);
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
