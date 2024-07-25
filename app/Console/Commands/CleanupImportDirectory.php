<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class CleanupImportDirectory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-import-directory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directory = '/import';

        $finder = new Finder();
        $finder->files()
            ->in($directory)
            ->name('/\.bmf/i');

        foreach($finder as $file) {

            //if the .epub file does not exist, delete the .bmf file
            if(!file_exists(str_replace('.bmf', '', $file->getRealPath()))){
                echo "Removing " . $file->getRealPath() . "\n";
                unlink($file->getRealPath());
            }

        }

        unset($finder);
        $finder = new Finder();
        $finder->directories()
            ->in($directory);

        foreach($finder as $directory) {

            $finder2 = new Finder();

            if(!$finder2->in($directory->getRealPath())->hasResults()){
                echo "Removing " . $directory->getRealPath() . "\n";
                rmdir($directory->getRealPath());
            }

            unset($finder2);
        }
    }
}
