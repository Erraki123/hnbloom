<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Teste en local toutes les routes GET publiques (sans paramètre)
 * et affiche celles qui ne fonctionnent pas (404 / 500 / vue introuvable...).
 *
 * Usage :
 *   php artisan routes:check          -> routes publiques seulement
 *   php artisan routes:check --all    -> inclut aussi auth/admin
 */
class CheckPublicRoutes extends Command
{
    protected $signature = 'routes:check {--all : inclure aussi les routes protégées (auth/admin)}';

    protected $description = 'Teste toutes les routes GET publiques et liste celles qui ne fonctionnent pas';

    public function handle(): int
    {
        $kernel  = $this->laravel->make(Kernel::class);
        $broken  = [];
        $params  = [];
        $okCount = 0;

        foreach (Route::getRoutes() as $route) {
            if (! in_array('GET', $route->methods(), true)) {
                continue;
            }

            $uri = '/' . ltrim($route->uri(), '/');

            // routes techniques à ignorer
            if (preg_match('#^/(_|up$|sanctum|storage|livewire|telescope|horizon)#', $uri)) {
                continue;
            }

            // routes protégées
            $protected = collect($route->gatherMiddleware())->contains(
                fn ($m) => is_string($m) && preg_match('/^(auth|admin|verified|role|can|password\.confirm)/', $m)
            );
            if ($protected && ! $this->option('all')) {
                continue;
            }

            // routes avec paramètres : à tester à la main
            if (str_contains($uri, '{')) {
                $params[] = [$uri, $route->getName() ?: '-', $route->getActionName()];
                continue;
            }

            try {
                $response = $kernel->handle(Request::create($uri, 'GET'));
                $code     = $response->getStatusCode();
                $hint     = '';

                if ($code >= 300 && $code < 400) {
                    $hint = '→ ' . $response->headers->get('Location');
                } elseif ($code >= 400 && isset($response->exception) && $response->exception) {
                    $hint = mb_substr($response->exception->getMessage(), 0, 90);
                }
            } catch (\Throwable $e) {
                $code = 500;
                $hint = mb_substr($e->getMessage(), 0, 90);
            }

            if ($code >= 200 && $code < 300) {
                $okCount++;
            } elseif ($code >= 300 && $code < 400) {
                $okCount++; // simple redirection, pas une erreur
            } else {
                $broken[] = [$code, $uri, $route->getName() ?: '-', $route->getActionName(), $hint];
            }
        }

        $this->newLine();
        $this->info("Routes OK : {$okCount}");

        if ($broken) {
            $this->error('Routes en erreur : ' . count($broken));
            $this->table(['Code', 'URL', 'Nom', 'Action', 'Détail'], $broken);
        } else {
            $this->info('Aucune route publique sans paramètre n\'est en erreur.');
        }

        if ($params) {
            $this->newLine();
            $this->warn('Routes avec paramètres (à tester à la main, ex. /solutions/{slug}) : ' . count($params));
            $this->table(['URL', 'Nom', 'Action'], $params);
        }

        return $broken ? self::FAILURE : self::SUCCESS;
    }
}
