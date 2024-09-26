<?php

namespace Swapnil\StarterKit;
use Exception;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Swapnil\StarterKit\Http\Middleware\StarterMiddleware;

class LoginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->make('Swapnil\StarterKit\Http\Controllers\Auth\LoginController');
        $this->app->make('Swapnil\StarterKit\Http\Controllers\HomeController');
//        $this->mergeConfigFrom(
//            __DIR__ . '/config/starterkit_swapnil.php', 'starterkit'
//        );
//        $this->loadViewsFrom(__DIR__.'/resources/views', 'views');
//        $viewsDirectory = __DIR__.'/resources/views';
//        $this->publishes([$viewsDirectory => base_path('views/vendor/swapnil-starterkit')], 'views');
//        $routeDirectory = __DIR__ . '/routes/web.php';
//        $this->publishes([$routeDirectory => base_path('routes/web.php')], 'routes');
    }

    /**
     * Bootstrap services.
     */
    public function boot(Router $router): void
    {
        // Publish config
        $configPath = __DIR__ . '/config/starterkit_swapnil.php';
        $this->publishes([
            $configPath => config_path('starterkit.php')
        ], 'config');

        // Publish views
        $viewsDirectory = __DIR__ . '/resources/views';
        $this->publishes([
            $viewsDirectory => resource_path('views/vendor/swapnil-starterkit')
        ], 'views');
        $this->loadViewsFrom($viewsDirectory, 'swapnil-starterkit');

        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        $this->replaceInFile('register', 'signup', resource_path('views/welcome.blade.php'));
        $this->replaceInFile('/register', '/signup', resource_path('views/welcome.blade.php'));
        try {
            $this->addMiddlewareAlias('starter.kit', StarterMiddleware::class);
        } catch (\Exception $e) {
            Log::error('Starter Kit Middleware alias registration failed: '. $e->getMessage());
        }
        // "Dashboard" Route...
        $this->replaceInFile('/home', '/dashboard', resource_path('views/welcome.blade.php'));
        $this->replaceInFile('Home', 'Dashboard', resource_path('views/welcome.blade.php'));
        $this->replaceInFile('/home', '/dashboard', app_path('Providers/RouteServiceProvider.php'));

        // Controllers...
//        (new Filesystem)->ensureDirectoryExists(app_path('Http/Controllers'));
//        (new Filesystem)->copyDirectory(__DIR__.'/Http/Controllers/', app_path('Http/Controllers'));

        // Views...
//        (new Filesystem)->ensureDirectoryExists(resource_path('views'));
//        (new Filesystem)->copyDirectory(__DIR__.'/resources/views', resource_path('views'));

        //copy routes to web.php
//        copy(__DIR__ . '/routes/web.php', base_path('routes/web.php'));

    }

    protected function replaceInFile($search, $replace, $path): void
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     * @throws Exception
     */
    protected function addMiddlewareAlias($alias, $class): void
    {
        $kernelPath = app_path('Http/Kernel.php');
        $kernelContent = file_get_contents($kernelPath);

        // Check if alias already exists
        if (strpos($kernelContent, "'$alias' => \\$class::class,") === false) {
            // Find the position of $middlewareAliases array
            $position = strpos($kernelContent, 'protected $middlewareAliases = [');

            if ($position !== false) {
                // Find the position of the closing bracket of the $middlewareAliases array
                $endPosition = strpos($kernelContent, '];', $position);

                if ($endPosition !== false) {

                    $newMiddlewareAlias = "        '$alias' => \\" . $class . "::class,\n";

                    $updatedContent = substr_replace(
                        $kernelContent,
                        $newMiddlewareAlias,
                        $endPosition - 1,
                        0
                    );

                    file_put_contents($kernelPath, $updatedContent);
                } else {
                    throw new \Exception("Failed to find the end of the middlewareAliases array.");
                }
            } else {
                throw new \Exception("Failed to find middlewareAliases array in Kernel.php.");
            }
        } else {
//            Log::info("Middleware alias '$alias' already exists.");
        }
    }
}
