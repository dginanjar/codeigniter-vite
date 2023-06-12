<?php

function vite_url(string $entry = ''): ?string
{
    $config = config('Vite');
    if (empty($config->entryPoints)) return null;

    $main_url = function ($url) {
        $parts = parse_url($url);

        return "{$parts['scheme']}://{$parts['host']}";
    };

    $entryPoint = $main_url(base_url()) . ":5173/{$config->entryPoints[$entry]}";
    if (@file_get_contents($entryPoint)) {
        if (count(explode('/', $entry)) == 1) {
            return sprintf(
                '<script type="module" src="%s"></script><script type="module" src="%s"></script>',
                $main_url(base_url()) . ':5173/@vite/client',
                $entryPoint
            );
        } else {
            return sprintf('<script type="module" src="%s"></script>', $entryPoint);
        }
    }

    $mainEntry = explode('/', $entry)[0];
    $manifest = json_decode(@file_get_contents(base_url("{$mainEntry}/manifest.json")), true);
    if (empty($manifest)) return null;

    $styles = '';
    $entryPoint = $manifest[$config->entryPoints[$entry]];
    if (!empty($entryPoint['css'])) {
        foreach ($entryPoint['css'] as $css) {
            $styles .= sprintf('<link rel="stylesheet" href="%s">', base_url("{$mainEntry}/{$css}"));
        }
    }

    $scripts = sprintf('<script type="module" src="%s"></script>', base_url("{$mainEntry}/{$entryPoint['file']}"));

    $collectImports = function ($record, $imports) use ($manifest, &$collectImports)
    {
        if (isset($record['dynamicImports']) || isset($record['imports'])) {
            if (isset($record['dynamicImports'])) {
                foreach ($record['dynamicImports'] as $dynamicImport) {
                    $imports[] = $manifest[$dynamicImport]['file'];
                    $imports = $collectImports($manifest[$dynamicImport], $imports);
                }
            }

            if (isset($record['imports'])) {
                foreach ($record['imports'] as $import) {
                    $imports[] = $manifest[$import]['file'];
                    $imports = $collectImports($manifest[$import], $imports);
                }
            }

            return $imports;
        } else {
            return $imports;
        }
    };

    $imports = $collectImports($entryPoint, []);

    if (count(explode('/', $entry)) == 1) {
        $mainEntryPoint = $manifest[$config->entryPoints[$mainEntry]];
        $mainImports = $collectImports($mainEntryPoint, []);
        $imports = array_diff($imports, $mainImports);
    }

    foreach (array_reverse($imports) as $import) {
        $scripts = sprintf('<script type="module" src="%s"></script>', $import) . $scripts;
    }

    return "{$styles}\n{$scripts}";
}