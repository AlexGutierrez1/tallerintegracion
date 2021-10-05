const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js').postCss('resources/css/app.css', 'public/css', [
    require('postcss-import'),
    require('tailwindcss'),
    require('autoprefixer'),
])
.combine([
    'node_modules/datatables.net/js/jquery.dataTables.min.js',
    'node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
    'node_modules/datatables.net-responsive/js/dataTables.responsive.min.js',
], 'public/js/datatable/datatable.js')
.combine([
    'node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
    'node_modules/datatables.net-responsive-bs/css/responsive.bootstrap.css',
],'public/css/datatable/datatable.css')
.copy('node_modules/sweetalert2/dist/sweetalert2.min.js', 'public/js/sweetalert2/sweetalert2.min.js')
.copy('node_modules/sweetalert2/dist/sweetalert2.min.css', 'public/css/sweetalert2/sweetalert2.min.css')
.sourceMaps();

mix.browserSync('http://tallerintegracion.test/');
