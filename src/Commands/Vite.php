<?php

namespace Dginanjar\CodeigniterVite\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Publisher\Publisher;

class Vite extends BaseCommand
{
    protected $group       = 'CodeIgniter';
    protected $name        = 'vite';
    protected $description = 'Publish CodeIgniter Vite Package.';

    protected $source;

    public function run(array $params)
    {
        $this->source = service('autoloader')->getNamespace('Dginanjar\\CodeigniterVite')[0];

        $this->publishConfig();
        $this->publishEnv();
        $this->publishVite();
    }

    private function publishConfig()
    {
        $publisher = new Publisher($this->source, APPPATH);
        @copy(APPPATH . 'Config/Vite.php', APPPATH . 'Config/Vite.php-' . time());
        copy("{$this->source}resources/Vite.php", APPPATH . 'Config/Vite.php');

        CLI::write(CLI::color(APPPATH . "Config/Vite.php", 'green') . ' generated.');
    }

    private function publishEnv()
    {
        $appEnv = ROOTPATH . '.env';
        $pkgEnv = "{$this->source}resources/env";

        if (! is_file($appEnv)) {
            copy("{$this->source}env", ROOTPATH . '.env');

            CLI::write(CLI::color($pkgEnv, ROOTPATH . '.env', 'green') . ' generated.');

            return;
        }

        @copy($appEnv, $appEnv . '-' . time());

        $appEnvContent = parse_ini_file($appEnv);
        $pkgEnvContent = parse_ini_file($pkgEnv);

        foreach (array_diff($pkgEnvContent, $appEnvContent) as $key => $value) {
            file_put_contents($appEnv, PHP_EOL . "{$key} = {$value}", FILE_APPEND);
        }

        CLI::write(CLI::color($appEnv, 'green') . ' updated.');
    }

    private function publishVite()
    {
        $files = directory_map("{$this->source}resources/vite");

        foreach ($files as $file) {
            @copy(ROOTPATH . $file, ROOTPATH . $file . '-' . time());
        }

        $publisher = new Publisher("{$this->source}resources/vite", ROOTPATH);
        $publisher->addPaths($files)->merge(true);

        foreach ($publisher->getPublished() as $file) {
            CLI::write(CLI::color($file, 'green') . ' generated.');
        }
    }
}