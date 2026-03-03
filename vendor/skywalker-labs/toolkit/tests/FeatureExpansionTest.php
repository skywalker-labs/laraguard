<?php

namespace Skywalker\Support\Tests;

use Orchestra\Testbench\TestCase;
use Skywalker\Support\Database\Repository\BaseRepository;
use Skywalker\Support\Database\Concerns\HasUuid;
use Skywalker\Support\Database\Concerns\Sluggable;
use Skywalker\Support\Data\ValueObjects\Email;
use Skywalker\Support\Database\Casts\JsonCast;
use Skywalker\Support\Database\Casts\MoneyCast;
use Skywalker\Support\Support\Concerns\Enum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FeatureExpansionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('test_models', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->json('settings')->nullable();
            $table->integer('price')->nullable();
            $table->timestamps();
        });
    }

    /** @test */
    public function it_generates_uuid_and_slug()
    {
        $model = new TestModel();
        $model->title = 'Hello World';
        $model->save();

        $this->assertNotNull($model->id);
        $this->assertTrue(strlen($model->id) === 36);
        $this->assertEquals('hello-world', $model->slug);
    }

    /** @test */
    public function it_casts_json_and_money()
    {
        $model = new TestModel();
        $model->settings = ['theme' => 'dark'];
        $model->price = 10.50; // Sets as 1050
        $model->save();

        $model->refresh();

        $this->assertIsArray($model->settings);
        $this->assertEquals('dark', $model->settings['theme']);
        $this->assertEquals(10.50, $model->price);
    }

    /** @test */
    public function repo_can_create_and_find()
    {
        $repo = new TestRepository();
        $model = $repo->create(['title' => 'Repo Item']);

        $this->assertNotNull($model);
        $this->assertEquals('Repo Item', $model->title);

        $found = $repo->find($model->id);
        $this->assertEquals($model->id, $found->id);
    }

    /** @test */
    public function value_object_validates_email()
    {
        $email = new Email('test@example.com');
        $this->assertEquals('test@example.com', (string) $email);

        $this->expectException(\InvalidArgumentException::class);
        new Email('invalid-email');
    }

    /** @test */
    public function enum_trait_helpers()
    {
        if (PHP_VERSION_ID < 80100) {
            $this->markTestSkipped('PHP 8.1+ required for Enum tests');
        }

        // Dynamically define enum to avoid ParseError on PHP < 8.1
        if (! enum_exists('Skywalker\Support\Tests\TestEnum')) {
            eval('
                namespace Skywalker\Support\Tests;
                use Skywalker\Support\Support\Concerns\Enum;
                
                enum TestEnum: string {
                    use Enum;
                    case OPTION_ONE = "option_one";
                    case OPTION_TWO = "option_two";
                }
            ');
        }

        $enumClass = 'Skywalker\Support\Tests\TestEnum';

        $this->assertEquals(['option_one', 'option_two'], $enumClass::values());
        $this->assertEquals(['OPTION_ONE', 'OPTION_TWO'], $enumClass::names());
        $this->assertEquals(['option_one' => 'OPTION_ONE', 'option_two' => 'OPTION_TWO'], $enumClass::options());
    }
}

class TestModel extends Model
{
    use HasUuid, Sluggable;

    protected $table = 'test_models';
    protected $guarded = [];
    public $timestamps = false; // simplify

    protected $casts = [
        'settings' => JsonCast::class,
        'price'    => MoneyCast::class,
    ];

    public function getSlugSource(): string
    {
        return 'title';
    }
}

class TestRepository extends BaseRepository
{
    public function model(): string
    {
        return TestModel::class;
    }
}
