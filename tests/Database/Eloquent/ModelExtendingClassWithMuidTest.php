<?php

namespace Rawaby88\Muid\Tests\Database\Eloquent;

use Illuminate\Database\QueryException;
use Rawaby88\Muid\Tests\Models\ModelWithForeignMuidExtendsTest;
use Rawaby88\Muid\Tests\Models\ModelWithMuidExtendsTest;
use Rawaby88\Muid\Tests\Models\ModelWithPrimaryMuidExtendsTest;
use Rawaby88\Muid\Tests\TestCase;

class ModelExtendingClassWithMuidTest extends TestCase
{
    /** @test */
    public function it_generate_a_muid_when_the_id_has_not_been_set()
    {
        $model = ModelWithMuidExtendsTest::create(['name' => 'Mood']);

        $this->assertEquals(36, strlen($model->id));

        $this->assertEquals('test_', substr($model->id, 0, 5));
    }

    /** @test */
    public function it_generate_a_muid_without_collusion()
    {
        for ($i = 0; $i < 10000; $i++) {
            ModelWithMuidExtendsTest::create(['name' => 'Mood']);
        }

        $this->assertEquals(10000, ModelWithMuidExtendsTest::count());
    }

    /** @test */
    public function it_generate_a_primaryMuid_when_the_id_has_not_been_set()
    {
        $model = ModelWithPrimaryMuidExtendsTest::create(['name' => 'Mood']);

        $this->assertEquals(36, strlen($model->id));

        $this->assertEquals('primary_', substr($model->id, 0, 8));
    }

    /** @test */
    public function it_generate_a_foreignMuid_when_the_id_has_not_been_set()
    {
        $main = ModelWithMuidExtendsTest::create(['name' => 'Mood']);
        $model = ModelWithForeignMuidExtendsTest::create(['model_with_muid_test_id' => $main->id]);

        $this->assertEquals($main->id, $model->model_with_muid_test_id);
    }

    public function it_through_query_exception_if_foreign_key_does_not_exists()
    {
        $this->expectException(QueryException::class);

        ModelWithForeignMuidExtendsTest::create(['model_with_muid_test_id' => 'test']);
    }

    /** @test */
    public function it_generate_a_muidMorph_type_and_id()
    {
        $main = ModelWithMuidExtendsTest::create(['name' => 'Mood']);
        $morphModel = $main->modelWithMuidMorph()
            ->create();

        $this->assertEquals($main->id, $morphModel->testable_id);
        $this->assertEquals(ModelWithMuidExtendsTest::class, $morphModel->testable_type);
    }
}
