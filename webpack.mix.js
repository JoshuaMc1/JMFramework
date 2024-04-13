const mix = require('laravel-mix');
const tailwindcss = require('tailwindcss');
const TerserPlugin = require('terser-webpack-plugin');

mix.setPublicPath('public');

mix.options({
    manifest: false,
});

mix.disableNotifications();

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        tailwindcss('tailwind.config.js'),
    ])
    .version()
    .webpackConfig({
        plugins: [
            new TerserPlugin({
                terserOptions: {
                    compress: {
                        drop_console: mix.inProduction(),
                    }
                },
                extractComments: false
            }),
        ],
        optimization: {
            minimize: true,
        },
    });
