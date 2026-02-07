<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DetectRiskyChangesToLaravel11 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scan:migrations-for-change';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warn about change() calls that may drop column modifiers in Laravel 11';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $migrationFiles = File::allFiles(database_path('migrations'));
        $risky = [];

        foreach ($migrationFiles as $file) {
            $contents = File::get($file);
            // naive match: look for `->change()` without ->default/unsigned/comment/nullable etc.
            preg_match_all('/\$table->.*?->change\(\);/s', $contents, $matches);

            foreach ($matches[0] as $match) {
                if (! preg_match('/->(default|nullable|comment|unsigned|charset|collation)/', $match)) {
                    $risky[] = [
                        'file' => $file->getRelativePathname(),
                        'line' => trim($match),
                    ];
                }
            }
        }

        if (count($risky)) {
            $this->warn("⚠️  Found possible risky `->change()` calls that may drop column attributes in Laravel 11:\n");

            foreach ($risky as $hit) {
                $this->line("  - {$hit['file']}:\n      {$hit['line']}\n");
            }

            $this->line("\n💡 Add missing modifiers like `->default()`, `->unsigned()` etc. to be safe.");
        } else {
            $this->info('✅ No risky change() calls found. You’re all good!');
        }
    }
}
