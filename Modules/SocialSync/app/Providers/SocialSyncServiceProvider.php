<?php

namespace Modules\SocialSync\Providers;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\SocialSync\app\Console\DispatchScheduledPosts;
use Modules\SocialSync\app\Console\ResetUsagePeriodCommand;
use Modules\SocialSync\app\Interfaces\ImageGeneratorInterface;
use Modules\SocialSync\app\Interfaces\TextGeneratorInterface;
use Modules\SocialSync\app\Models\Post;
use Modules\SocialSync\app\Models\SocialAccount;
use Modules\SocialSync\app\Policies\PostPolicy;
use Modules\SocialSync\app\Policies\SocialAccountPolicy;
use Modules\SocialSync\app\Services\ImageGeneratorProviders\BananaImageGenerator;
use Modules\SocialSync\app\Services\TextGeneratorProviders\GeminiTextGenerator;
use Modules\SocialSync\database\seeders\SocialSyncDatabaseSeeder;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SocialSyncServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'SocialSync';

    protected string $nameLower = 'socialsync';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
        $this->registerSeeders();
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        $this->bindings();
    }

    /**
     * Register policies.
     */
    protected function registerPolicies(): void
    {
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(SocialAccount::class, SocialAccountPolicy::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            DispatchScheduledPosts::class,
        ]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            $schedule->command('app:dispatch-scheduled-posts')->everyMinute();
        });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $config = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments = explode('.', $this->nameLower . '.' . $config_key);

                    // Remove duplicated adjacent segments
                    $normalized = [];
                    foreach ($segments as $segment) {
                        if (end($normalized) !== $segment) {
                            $normalized[] = $segment;
                        }
                    }

                    $key = ($config === 'config.php') ? $this->nameLower : implode('.', $normalized);

                    $this->publishes([$file->getPathname() => config_path($config)], 'config');
                    $this->merge_config_from($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Merge config from the given path recursively.
     */
    protected function merge_config_from(string $path, string $key): void
    {
        $existing = config($key, []);
        $module_config = require $path;

        config([$key => array_replace_recursive($existing, $module_config)]);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        Blade::componentNamespace(config('modules.namespace') . '\\' . $this->name . '\\View\\Components', $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->nameLower)) {
                $paths[] = $path . '/modules/' . $this->nameLower;
            }
        }

        return $paths;
    }

    private function bindings()
    {
        $this->app->bind(ImageGeneratorInterface::class, BananaImageGenerator::class);
        $this->app->bind(TextGeneratorInterface::class, GeminiTextGenerator::class);
        $this->app->bind(
            \Modules\SocialSync\app\Interfaces\Repositories\PostRepositoryInterface::class,
            \Modules\SocialSync\app\Repositories\Eloquent\PostRepository::class
        );
        $this->app->bind(
            \Modules\SocialSync\app\Interfaces\Repositories\SocialAccountRepositoryInterface::class,
            \Modules\SocialSync\app\Repositories\Eloquent\SocialAccountRepository::class
        );
    }

    private function registerSeeders()
    {
        DatabaseSeeder::$seeders[] = SocialSyncDatabaseSeeder::class;
    }
}
