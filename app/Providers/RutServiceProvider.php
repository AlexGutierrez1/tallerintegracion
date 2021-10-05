<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class RutServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Create Blade directive to convert number to RUT
        Blade::directive('rut', function ($string) {
            return "<?php echo \Freshwork\ChileanBundle\Rut::parse($string)->quiet()->format() ?>";
        });
    }
}
