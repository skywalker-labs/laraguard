<?php


namespace Skywalker\Support\Database;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Seeder as IlluminateSeeder;

/**
 * Class     Seeder
 *
 * @author   Skywalker <skywalker@example.com>
 */
abstract class Seeder extends IlluminateSeeder
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /**
     * Seeder collection.
     *
     * @var array
     */
    protected $seeds = [];

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Eloquent::unguard();

        foreach ($this->seeds as $seed) {
            $this->call($seed);
        }

        Eloquent::reguard();
    }
}
