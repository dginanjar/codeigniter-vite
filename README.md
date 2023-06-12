# Codeigniter Vite

Integrate Vite to CodeIgniter 4.

## Installation

Install with Composer:

```shell
composer require dginanjar/codeigniter-vite
```

Run command to publish `package.json`, `postcss.config.js`, `tailwind.config.js` and `vite.config.js`.

```shell
php spark vite:publish
```

Download Vite and all required packages.

```
npm install
```

### Usage

Running Vite Server:

```
npm run dev
```

When it is time to deploy your app for production, simply run the vite build command.

```
npm run build
```

Put `vite_url()` inside your views.

```html
<head>
    ...
    <?php echo vite_url(); ?>
</head>
```

`vite_url()` is a function added to URL Helper and by default this helper is loaded automatically by CodeIgniter.

If the Vite server is running then `vite_url()` will generate:

```html
<script type="module" src="http://localhost:5173/@vite/client"></script>
<script type="module" src="http://localhost:5173/app/Views/js/main.js"></script>
```

Meanwhile, if we execute `npm run build`, the `manifest.json` file will appear in `public` folder and the generated code will be:

```html
<link rel="stylesheet" href="http://localhost/assets/css/xxxx.css">
<script type="module" src="http://localhost/assets/js/xxx.js"></script>
```

That's it!

## Configuration

Here is the default `vite.config.js`.

```js
import { resolve } from 'path'
import { defineConfig, loadEnv } from 'vite'

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')

  return {
    css: { devSourcemap: true },
    server: {
      host: '0.0.0.0'
    },
    build: {
      outDir: resolve(__dirname, 'public'),
      emptyOutDir: false,
      rollupOptions: {
        input: resolve(__dirname, 'app/Views/assets/js/main.js')
      },
      cssCodeSplit: true,
      manifest: true
    }
  }
})
```

In most cases, you only need to adjust the `build.outDir` and `build.rollupOptions.input`. Then create `App/Config/Vite.php` and adjust its contents based on the `build.rollupOptions.input`.

```php
<?php

namespace Dginanjar\CodeIgniterVite\Config;

use CodeIgniter\Config\BaseConfig;

class Vite extends BaseConfig
{
    public array $entryPoints = [
        '' => 'app/Views/assets/js/main.js',
    ];
}
```

### Build multiple type of pages

Maybe you want to create a public and an admin page. The Vite server will serve assets for both public and admin pages using the `npm run dev` command. So we only need to add one script to build admin page assets.

But first, we have to create an entry point for admin pages. For that just copy `app/Views/assets` folder and paste as `app/Views/admin/assets`.

Previously we had `vite.config.js` and now we have to create a similar file for the build assets required for admin pages, `vite.config.admin.js`.

```js
import { resolve } from 'path'
import { defineConfig, loadEnv } from 'vite'

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')

  return {
    css: { devSourcemap: true },
    server: {
      host: '0.0.0.0'
    },
    build: {
      outDir: resolve(__dirname, 'public/admin'),
      emptyOutDir: false,
      rollupOptions: {
        input: resolve(__dirname, 'app/Views/admin/assets/js/main.js')
      },
      cssCodeSplit: true,
      manifest: true
    }
  }
})
```

By default Vite will try to find and read `vite.config.js` but we can also specify the configuration file Vite will use with the `--config` option. Open `package.json` and add a script.

```json
"scripts": {
  "dev": "vite",
  "build": "vite build",
  "build-admin": "vite build --config vite.config.admin.js"
},
```

Now we have to deal with `app/Config/Vite.php`. Here we have to write two items to the `$paths` property according to `vite.config.js` and `vite.config.admin.js`.

```php
<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Vite extends BaseConfig
{
    public $paths = [
        '' => 'app/Views/assets/js/main.js',
        'admin' => 'app/Views/admin/assets/js/main.js',
    ];
}
```

Finally, for the admin page, in the view we write `vite_url('admin')`. That's it!