<?php

namespace Dginanjar\CodeIgniterVite\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Publisher\Publisher;
use Throwable;

class Vite extends BaseCommand
{
    protected $group       = 'CodeIgniter';
    protected $name        = 'vite:publish';
    protected $description = 'Publish CodeIgniter Vite Package.';

    protected $stop, $sourcePath;


    public function run(array $params): void
    {
        $this->stop = false;
        $this->sourcePath = service('autoloader')->getNamespace('Dginanjar\\CodeIgniterVite')[0];

        $this->publishConfigs();
        $this->publishAssets();

        CLI::write('Please run ' . CLI::color('npm install', 'green') . ' and ' . CLI::color('npm run dev', 'green') . '.');
    }

    public function publishConfigs()
    {
        $publisher = new Publisher("{$this->sourcePath}/configurations", ROOTPATH);
        try {
            $publisher->addPath('.')->merge();

        } catch (Throwable $e) {
            $this->showError($e);
            return;
        }
    }

    public function publishAssets()
    {
        $publisher = new Publisher("{$this->sourcePath}/Views", APPPATH . 'Views');
        try {
            $publisher->addPath('.')->merge();

        } catch (Throwable $e) {
            $this->showError($e);
            return;
        }
    }
}