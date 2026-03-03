<?php


namespace Skywalker\Support\Providers;

use Skywalker\Support\Exceptions\PackageException;
use Skywalker\Support\Providers\Concerns\{
    HasAssets,
    HasConfig,
    HasFactories,
    HasMigrations,
    HasTranslations,
    HasViews
};
use Skywalker\Support\Http\BladeDirectives;
use Skywalker\Support\Support\Macros\{CollectionMacros, StringMacros};
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;
use ReflectionClass;

/**
 * Class     PackageServiceProvider
 *
 * @author   Skywalker <skywalker@example.com>
 */
abstract class PackageServiceProvider extends ServiceProvider
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use HasAssets,
        HasConfig,
        HasFactories,
        HasMigrations,
        HasTranslations,
        HasViews;

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * Vendor name.
     *
     * @var string
     */
    protected $vendor = 'ermradulsharma';

    /**
     * Package name.
     *
     * @var string|null
     */
    protected $package;

    /**
     * Package base path.
     *
     * @var string
     */
    protected $basePath;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Create a new service provider instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->basePath = $this->resolveBasePath();
    }

    /**
     * Resolve the base path of the package.
     *
     * @return string
     */
    protected function resolveBasePath()
    {
        return dirname(
            (new ReflectionClass($this))->getFileName(),
            2
        );
    }

    /* -----------------------------------------------------------------
     |  Getters & Setters
     | -----------------------------------------------------------------
     */

    /**
     * Get the base path of the package.
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Get the vendor name.
     *
     * @return string
     */
    protected function getVendorName(): string
    {
        return $this->vendor;
    }

    /**
     * Get the package name.
     *
     * @return string|null
     */
    protected function getPackageName(): ?string
    {
        return $this->package;
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register the service provider.
     */
    public function register()
    {
        parent::register();

        $this->checkPackageName();
        $this->registerMacros();
        $this->registerCommands([
            \Skywalker\Support\Console\Commands\DiscoverProject::class,
            \Skywalker\Support\Console\Commands\MakeDto::class,
        ]);
    }

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $this->registerBladeDirectives();
    }

    /* -----------------------------------------------------------------
     |  Package Methods
     | -----------------------------------------------------------------
     */

    /**
     * Publish all the package files.
     */
    protected function publishAll(): void
    {
        $this->publishAssets();
        $this->publishConfig();
        $this->publishFactories();
        $this->publishMigrations();
        $this->publishTranslations();
        $this->publishViews();
    }

    /* -----------------------------------------------------------------
     |  Check Methods
     | -----------------------------------------------------------------
     */

    /**
     * Check package name.
     *
     * @throws \Skywalker\Support\Exceptions\PackageException
     */
    protected function checkPackageName(): void
    {
        if (empty($this->getVendorName()) || empty($this->getPackageName())) {
            throw PackageException::unspecifiedName();
        }
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the published tags.
     *
     * @param  string  $tag
     *
     * @return array
     */
    protected function getPublishedTags(string $tag): array
    {
        $package = $this->getPackageName();

        return array_map(function ($name) {
            return Str::slug($name);
        }, [$this->getVendorName(), $package, $tag, $package . '-' . $tag]);
    }

    /**
     * Register the package macros.
     */
    protected function registerMacros(): void
    {
        CollectionMacros::register();
        StringMacros::register();
    }

    /**
     * Register the package blade directives.
     */
    protected function registerBladeDirectives(): void
    {
        BladeDirectives::register();
    }
}
