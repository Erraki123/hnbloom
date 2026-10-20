<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

class SolutionCatalogRouteTest extends BaseTestCase
{
    protected $seed = false;

    public function test_solution_catalog_helpers_and_routes_are_registered(): void
    {
        $this->assertTrue(class_exists(\App\Support\SolutionCatalog::class));
        $this->assertNotNull(app('router')->getRoutes()->getByName('solutions.category'));
        $this->assertNotNull(app('router')->getRoutes()->getByName('solutions.product'));
    }
}
