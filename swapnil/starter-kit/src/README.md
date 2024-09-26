# Swapnil Starter Kit

Swapnil Starter Kit is a Laravel package designed to provide authentication, middleware, custom routes, and more. This package allows you to easily publish controllers, views, middleware, and configuration files to extend your Laravel application.

## Requirements

- Laravel 8 or higher
- PHP 7.4 or higher

## Installation

Install the package via Composer:

```bash
composer require swapnil/starter-kit
```
# USAGE
### Key Points:
- **Publishing Configuration:** Provides a configuration file to be published and customized.
- **Publishing Views:** Allows for the easy modification of package views.
- **Publishing Controllers & Middleware:** Enables the use of pre-defined controllers and middleware.
- **Custom Routes:** Automatically loads custom routes and allows for route customization.
- **Service Provider:** Describes how everything is registered and bootstrapped.
### Publishing Assets
- The package allows you to publish several resources like controllers, views, middleware, and configuration files. Use the vendor:publish command to publish them into your Laravel app.
#### Publishing Configuration
- You can publish the configuration file to modify the default settings:

```bash
php artisan vendor:publish --tag=config
```
- This will copy the configuration file to `config/starterkit.php`.
#### Publishing Views
- You can publish the package's views into your Laravel app:
```bash
php artisan vendor:publish --tag=views
```
- This will copy the views to `resources/views/vendor/swapnil-starterkit.`
#### Publishing Controllers
- If you want to publish the controllers, use the following command:
```bash
php artisan vendor:publish --tag=controllers
```
- This will copy the controllers to `app/Http/Controllers/vendor/swapnil-starterkit.`

#### Publishing Middleware
- To publish the middleware used by the package, run:
```bash
php artisan vendor:publish --tag=middleware
```
- This will copy the middleware to `app/Http/Middleware/vendor/swapnil-starterkit.`
- The package includes a middleware class StarterMiddleware. Once the middleware is published, it can be assigned to your routes or globally registered in `app/Http/Kernel.php.`
- To register it globally, open `app/Http/Kernel.php` and add the middleware alias in the `$middlewareAliases` array:
```bash
protected $middlewareAliases = [
    // Other middleware...

    'starter.kit' => \App\Http\Middleware\vendor\swapnil-starterkit\StarterMiddleware::class,
];
```
- Alternatively, you can use it in specific `routes`:
```bash
Route::middleware('starter.kit')->group(function () {
    Route::get('/dashboard', 'DashboardController@index');
});
```
#### Custom Routes
- The package comes with predefined routes that can be loaded into your Laravel application. The routes are located in `routes/web.php` inside the package.
- To load these routes automatically, the service provider will handle that during the booting phase:
```bash
$this->loadRoutesFrom(__DIR__ . '/routes/web.php');
```
- If you'd like to modify or extend the routes, you can publish them manually by copying the file or writing custom logic in `routes/web.php`.
#### Configuration
- The configuration file allows you to customize various aspects of the package. By default, it's located in the package's `config` folder.

You can publish the configuration to your Laravel application's `config` directory:
```bash
php artisan vendor:publish --tag=config
```
- This will copy the config file to `config/starterkit.php`, where you can modify it as per your requirements.

This `README.md` should provide clear instructions on how to use and customize the package in a Laravel project.
