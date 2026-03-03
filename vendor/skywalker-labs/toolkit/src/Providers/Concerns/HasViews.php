<?php


namespace Skywalker\Support\Providers\Concerns;

/**
 * Trait     HasViews
 *
 * @author   Skywalker <skywalker@example.com>
 */
trait HasViews
{
    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the base views path.
     *
     * @return string
     */
    protected function getViewsPath(): string
    {
        return $this->getBasePath() . DIRECTORY_SEPARATOR . 'views';
    }

    /**
     * Get the destination views path.
     *
     * @return string
     */
    protected function getViewsDestinationPath(): string
    {
        return $this->app['config']['view.paths'][0] . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . $this->getPackageName();
    }

    /**
     * Publish the views.
     *
     * @param  string|null  $path
     */
    protected function publishViews(?string $path = null): void
    {
        $this->publishes([
            $this->getViewsPath() => $path ?: $this->getViewsDestinationPath(),
        ], $this->getPublishedTags('views'));
    }

    /**
     * Load the views files.
     */
    protected function loadViews(): void
    {
        $this->loadViewsFrom($this->getViewsPath(), $this->getPackageName());
    }
}
