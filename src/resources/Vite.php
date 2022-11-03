<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Vite extends BaseConfig
{
    public function __construct()
    {
        parent::__construct();

        $this->path = [
            'public' => getenv('VITE_INPUT'),
        ];
    }
}