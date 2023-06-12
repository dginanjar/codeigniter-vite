<?php

namespace Dginanjar\CodeIgniterVite\Config;

use CodeIgniter\Config\BaseConfig;

class Vite extends BaseConfig
{
    public array $entryPoints = [
        '' => 'app/Views/assets/js/main.js',
    ];
}