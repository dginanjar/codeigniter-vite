<?php

function vite($page = 'public')
{
    $config = config('Vite');

    $entry = @file_get_contents(base_url() . ":5173/{$config->path[$page]}");
    if ($entry) {
        return sprintf('<script type="module" src="%s"></script>', base_url() . ":5173/{$config->path[$page]}");
    }

    $manifest = @file_get_contents(base_url() . ($page == 'public' ? '' : '/admin/') . '/manifest.json');
    if ($manifest) {
        foreach (json_decode($manifest) as $asset) {
            if (isset($asset->isEntry) && $asset->isEntry && isset($asset->src) && $asset->src == $config->path[$page]) {
                return
                    sprintf('<link rel="stylesheet" href="%s">', base_url(($page == 'public' ? '' : 'admin/') . $asset->css[0])) .
                    sprintf('<script src="%s" defer></script>', base_url(($page == 'public' ? '' : 'admin/') . $asset->file));
            }
        }
    }
}