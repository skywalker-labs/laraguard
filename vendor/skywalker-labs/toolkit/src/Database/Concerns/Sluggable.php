<?php

namespace Skywalker\Support\Database\Concerns;

use Illuminate\Support\Str;

trait Sluggable
{
    /**
     * Boot the trait.
     */
    protected static function bootSluggable()
    {
        static::saving(function ($model) {
            $source = $model->getSlugSource();
            $slugField = $model->getSlugField();

            if (! empty($model->{$source}) && empty($model->{$slugField})) {
                $model->{$slugField} = Str::slug($model->{$source});
            }
        });
    }

    /**
     * Get the column to generate slug from.
     *
     * @return string
     */
    public function getSlugSource(): string
    {
        return 'title';
    }

    /**
     * Get the column to save slug to.
     *
     * @return string
     */
    public function getSlugField(): string
    {
        return 'slug';
    }
}
