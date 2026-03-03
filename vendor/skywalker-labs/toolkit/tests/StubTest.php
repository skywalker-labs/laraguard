<?php


namespace Skywalker\Support\Tests;

use Skywalker\Support\Stub;
use Illuminate\Support\Str;

/**
 * Class     StubTest
 *
 * @author   Skywalker <skywalker@example.com>
 */
class StubTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Skywalker\Support\Stub */
    private $stub;

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    public function setUp(): void
    {
        parent::setUp();

        //
    }

    public function tearDown(): void
    {
        unset($this->stub);

        parent::tearDown();
    }

    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_be_instantiated(): void
    {
        $this->stub = new Stub(
            $file = $this->getFixturesPath('stubs/composer.stub')
        );

        static::assertInstanceOf(\Skywalker\Support\Stub::class, $this->stub);

        $fileContent = file_get_contents($file);

        static::assertEquals($fileContent, $this->stub->render());
        static::assertEquals($fileContent, (string) $this->stub);
    }

    /** @test */
    public function it_can_create(): void
    {
        Stub::setBasePath(
            $basePath = $this->getFixturesPath('stubs')
        );

        $this->stub = Stub::create('composer.stub');

        $this->stub->replaces([
            'VENDOR'            => 'skywalker',
            'PACKAGE'           => 'package',
            'AUTHOR_NAME'       => 'Skywalker',
            'AUTHOR_EMAIL'      => 'skywalker@example.com',
            'MODULE_NAMESPACE'  => Str::studly('skywalker'),
            'STUDLY_NAME'       => Str::studly('package'),
        ]);

        $this->stub->save('composer.json');

        $fixture = $this->getFixturesPath('stubs/composer.json');

        static::assertEquals(file_get_contents($fixture), $this->stub->render());

        $this->stub->saveTo($basePath, 'composer.json');

        static::assertEquals(file_get_contents($fixture), $this->stub->render());
    }

    /** @test */
    public function it_can_set_and_get_base_path(): void
    {
        Stub::setBasePath(
            $basePath = $this->getFixturesPath('stubs')
        );

        static::assertEquals($basePath, Stub::getBasePath());
    }

    /** @test */
    public function it_can_create_from_path(): void
    {
        $this->stub = Stub::createFromPath(
            $path = $this->getFixturesPath('stubs') . '/composer.stub'
        );

        static::assertEmpty($this->stub->getBasePath());
        static::assertEquals($path, $this->stub->getPath());
        static::assertEmpty($this->stub->getReplaces());
    }
}
