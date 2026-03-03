<?php

namespace Skywalker\Support\Testing\Concerns;

use Illuminate\Support\Collection;

trait InteractsWithScenarios
{
    /**
     * Create a pre-defined data scenario.
     *
     * @param  string  $name
     * @param  array   $attributes
     * @return mixed
     */
    protected function createScenario(string $name, array $attributes = [])
    {
        $method = 'scenario' . \Illuminate\Support\Str::studly($name);

        if (method_exists($this, $method)) {
            return $this->{$method}($attributes);
        }

        throw new \InvalidArgumentException("Scenario [{$name}] not defined.");
    }

    /**
     * Example Scenario: Admin User.
     *
     * @param  array  $attributes
     * @return mixed
     */
    protected function scenarioAdminUser(array $attributes = [])
    {
        // This is a placeholder. In a real app, this would use factories.
        // return User::factory()->admin()->create($attributes);
        return array_merge(['role' => 'admin'], $attributes);
    }
}
