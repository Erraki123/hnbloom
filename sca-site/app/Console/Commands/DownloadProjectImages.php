<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class DownloadProjectImages extends Command
{
    protected $signature = 'projects:download-images
                            {--force : Retélécharger même si le fichier existe déjà}
                            {--insecure : Ignorer la vérification SSL (utile sous Windows)}';

    protected $description = 'Télécharge les images des projets HN BLOOM dans public/images/projects';

    private const SOURCE = 'https://hnbloom.com/wp-content/uploads/2024/09/';

    public function handle(): int
    {
        $dir = public_path('images/projects');
        File::ensureDirectoryExists($dir);

        $files = collect(config('portfolio.projects'))
            ->flatMap(fn ($p) => array_merge([$p['thumb'] ?? null, $p['image'] ?? null], $p['gallery'] ?? []))
            ->filter()
            ->map(fn ($u) => basename(parse_url($u, PHP_URL_PATH)))
            ->unique()
            ->values();

        $this->info("{$files->count()} images à traiter → {$dir}");

        $ok = $skipped = 0;
        $failed = [];

        foreach ($files as $file) {
            $target = $dir . DIRECTORY_SEPARATOR . $file;

            if (File::exists($target) && ! $this->option('force')) {
                $skipped++;
                continue;
            }

            try {
                $http = Http::timeout(30)->retry(2, 500);

                if ($this->option('insecure')) {
                    $http = $http->withoutVerifying();
                }

                $res = $http->get(self::SOURCE . $file);

                if ($res->successful() && $res->body() !== '') {
                    File::put($target, $res->body());
                    $ok++;
                    $this->line("  ✔ {$file}");
                } else {
                    $failed[] = $file;
                    $this->warn("  ✘ {$file} (HTTP {$res->status()})");
                }
            } catch (\Throwable $e) {
                $failed[] = $file;
                $this->warn("  ✘ {$file} ({$e->getMessage()})");
            }
        }

        $this->newLine();
        $this->info("Téléchargées : {$ok} | Déjà présentes : {$skipped} | Échecs : " . count($failed));

        if ($failed) {
            $this->warn('Fichiers en échec : ' . implode(', ', $failed));
            return self::FAILURE;
        }

        $this->comment('Terminé. Lancez : php artisan config:clear');
        return self::SUCCESS;
    }
}
