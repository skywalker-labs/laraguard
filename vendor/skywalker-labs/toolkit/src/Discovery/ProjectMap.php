<?php

namespace Skywalker\Support\Discovery;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;
use ReflectionClass;

class ProjectMap
{
    /**
     * Generate the project map.
     *
     * @return array
     */
    public function generate(): array
    {
        return [
            'routes'  => $this->getRoutes(),
            'models'  => $this->getModels(),
            'actions' => $this->getActions(),
            'config'  => $this->getImportantConfigs(),
        ];
    }

    /**
     * Get all registered routes.
     *
     * @return array
     */
    protected function getRoutes(): array
    {
        return collect(Route::getRoutes())->map(function ($route) {
            return [
                'uri'     => $route->uri(),
                'methods' => $route->methods(),
                'name'    => $route->getName(),
                'action'  => $route->getActionName(),
            ];
        })->toArray();
    }

    /**
     * Discover models and their schemas.
     *
     * @return array
     */
    protected function getModels(): array
    {
        $modelPath = app_path('Models');
        if (! File::isDirectory($modelPath)) {
            return [];
        }

        return collect(File::allFiles($modelPath))
            ->map(function ($file) {
                $class = $this->getClassFromFile($file);
                if (! $class || ! is_subclass_of($class, 'Illuminate\Database\Eloquent\Model')) {
                    return null;
                }

                $instance = new $class;
                $table = $instance->getTable();

                return [
                    'class'   => $class,
                    'table'   => $table,
                    'columns' => Schema::getColumnListing($table),
                ];
            })
            ->filter()
            ->toArray();
    }

    /**
     * Discover Action classes.
     *
     * @return array
     */
    protected function getActions(): array
    {
        $actionPath = app_path('Actions');
        if (! File::isDirectory($actionPath)) {
            return [];
        }

        return collect(File::allFiles($actionPath))
            ->map(function ($file) {
                return $this->getClassFromFile($file);
            })
            ->filter()
            ->toArray();
    }

    /**
     * Get important configuration keys.
     *
     * @return array
     */
    protected function getImportantConfigs(): array
    {
        return [
            'app_name' => config('app.name'),
            'env'      => config('app.env'),
            'debug'    => config('app.debug'),
            'timezone' => config('app.timezone'),
        ];
    }

    /**
     * Get class name from file path.
     *
     * @param  \Symfony\Component\Finder\SplFileInfo  $file
     * @return string|null
     */
    protected function getClassFromFile($file): ?string
    {
        $contents = file_get_contents($file->getRealPath());
        if (preg_match('/namespace\s+(.+?);/', $contents, $matches)) {
            $namespace = $matches[1];
            return $namespace . '\\' . str_replace('.php', '', $file->getFilename());
        }

        return null;
    }
}
