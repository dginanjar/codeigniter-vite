# CodeigniterVite

Integrate Vite to CodeIgniter 4.

## Installation

Install with Composer:

```shell
composer require dginanjar/codeigniter-vite
```

## Usage

### Publish package

Run command to publish `.env`, `package.json`, `vite.config.js` and `app/Config/Vite.php`:

```shell
php spark vite
```

If you use Linux and Docker, you may run into privilege issue with the `package.json` file. Run the following command to fix the issue:

```shell
chown $USER package.json
```

Download Vite using NPM:

```
npm install
```

### Running Vite

Running Vite Server:

```
npm run dev
```

When it is time to deploy your app for production, simply run the vite build command.

```
npm run build
```

### Load assets

Open `app/Controllers/BaseController.php` to load vite helper.

```php
protected $helpers = ['vite'];
```

Then put `vite()` inside your views.

```html
<head>
    ...
    <?php echo vite(); ?>
</head>
```

That's it!

### Configuration

What matters here is the entry point and the output directory. Here is an example of `.env`:
```
# Specify entry point, relative to ROOTPATH
# e.g. your main.js or app.js or index.js
VITE_INPUT = 'app/Views/assets/js/app.js'

# Specify the output directory, relative to ROOTPATH
VITE_OUTDIR = 'public'
```

If you don't use .`env`, you'll have to manually write it in `app/Config/Vite.php` and `vite.config.js`.

```
# app/Config/Vite.php

$this->path = [
    'public' => getenv('VITE_INPUT'),
];
```

```
# vite.config.js
base: '/'

build: {
  manifest: true,
  rollupOptions: {
    input: 'app/Views/assets/js/app.js',
  },
  outDir: 'public',
  emptyOutDir: false,
  copyPublicDir: false,
},
```

For more details on entry point and output directory, please refer to [rollup.js Doc](https://rollupjs.org/guide/en/#input) and [Vite Doc](https://vitejs.dev/config/build-options.html#build-outdir).

#### Build public and admin pages

Maybe you want to create a public page and an admin page. To do so, copy vite config to `vite.config.admin.js` file and make same changes:
```
base: '/admin/'

build: {
  manifest: true,
  rollupOptions: {
    input: 'app/Views/admin/assets/js/app.js',
  },
  outDir: 'public/admin',
  emptyOutDir: false,
  copyPublicDir: false,
},
```

Add some scripts to `package.json`:
```
"scripts": {
  "dev": "vite --host",
  "build": "vite build",
  "dev:admin": "vite --host --config vite.config.admin.js",
  "build:admin": "vite build --config vite.config.admin.js",
},
```

Update your `app/Config/Vite.php`:

```
# app/Config/Vite.php

$this->path = [
    'public' => 'app/Views/assets/js/app.js',
    'admin' => 'app/Views/admin/assets/js/app.js',
];
```

In admin view:

```html
<head>
    ...
    <?php echo vite('admin'); ?>
</head>
```

Then you can run:

```shell
npm run dev:admin
```

or...

```
npm run build:admin
```
