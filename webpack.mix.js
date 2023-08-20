const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');

mix.setPublicPath('public');

mix.options({
    manifest: false,
    notifications: false,
});

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        tailwindcss('tailwind.config.js'),
    ]);