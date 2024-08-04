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
    protected $signature = 'app:cleanup-import-directory {--removeAdditionalExtensions=} {--removeDefaultExtensions=}';

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
            ->in($directory);

        $extensions = [];

        $removeExtensions = explode(",", $this->option('removeAdditionalExtensions'));

        if($this->option('removeDefaultExtensions') == "true"){
            $removeExtensions = array_merge($removeExtensions, explode(",", "pyo,exe,pyd,dll,zip,xml,ico,manifest,recipe,png,bat,sh,py,pickle,js,qrc,sql,types,json,jpg,gif,css,xhtml,html,otf,ttf,mo,xsl,tmpl,db,opf,nfo,doc,txt,original_epub,par2,nzb,lnk,epu,ini,par,EPU,rar,url,reg"));
        }
        foreach($finder as $file) {

            //Count the number of files for each extension
            if(!isset($extensions[$file->getExtension()])){
                $extensions[$file->getExtension()] = 1;
            } else {
                $extensions[$file->getExtension()]++;
            }

            //if the .epub file does not exist, delete the .bmf file
            if($file->getExtension() == 'bmf'){
                if(!file_exists(str_replace('.bmf', '', $file->getRealPath()))){
                    echo "Removing " . $file->getRealPath() . "\n";
                    unlink($file->getRealPath());
                }
            }

            if(in_array($file->getExtension(), $removeExtensions)){
                echo "Removing " . $file->getRealPath() . "\n";
                unlink($file->getRealPath());
            }

        }

        print_r($extensions);

        unset($finder);
        $finder = new Finder();
        $finder->directories()
            ->in($directory);

        $removeDirectories = [];
        foreach($finder as $directory) {

            $finder2 = new Finder();

            if(!$finder2->in($directory->getRealPath())->hasResults()){
                $removeDirectories[] = $directory->getRealPath();
            }

            unset($finder2);
        }

        foreach($removeDirectories as $directory) {
            echo "Removing " . $directory . "\n";
            try {
                rmdir($directory);
            } catch (\Exception $e) {
                echo "Could not remove " . $directory . "\n";
            }
        }
    }
}
