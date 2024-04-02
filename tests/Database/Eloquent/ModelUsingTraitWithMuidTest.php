<?php

namespace Rawaby88\Muid\Tests\Database\Eloquent;

use Illuminate\Database\QueryException;
use Rawaby88\Muid\Tests\Models\ModelWithForeignMuidTraitTest;
use Rawaby88\Muid\Tests\Models\ModelWithMuidTraitTest;
use Rawaby88\Muid\Tests\Models\ModelWithPrimaryMuidTraitTest;
use Rawaby88\Muid\Tests\TestCase;

class ModelUsingTraitWithMuidTest extends TestCase
{
    /** @test */
    public function it_generate_a_muid_when_the_id_has_not_been_set()
    {
        $model = ModelWithMuidTraitTest::create(['name' => 'Mood']);

        $this->assertEquals(36, strlen($model->id));

        $this->assertEquals('test_', substr($model->id, 0, 5));
    }

    /** @test */
    public function it_generate_a_muid_without_collusion()
    {
        for ($i = 0; $i < 10000; $i++) {
            ModelWithMuidTraitTest::create(['name' => 'Mood']);
        }

        $this->assertEquals(10000, ModelWithMuidTraitTest::count());
    }

    /** @test */
    public function it_generate_a_primaryMuid_when_the_id_has_not_been_set()
    {
        $model = ModelWithPrimaryMuidTraitTest::create(['name' => 'Mood']);

        $this->assertEquals(36, strlen($model->id));

        $this->assertEquals('primary_', substr($model->id, 0, 8));
    }

    /** @test */
    public function it_generate_a_foreignMuid_when_the_id_has_not_been_set()
    {
        $main = ModelWithMuidTraitTest::create(['name' => 'Mood']);
        $model = ModelWithForeignMuidTraitTest::create(['model_with_muid_test_id' => $main->id]);

        $this->assertEquals($main->id, $model->model_with_muid_test_id);
    }

    public function it_through_query_exception_if_foreign_key_does_not_exists()
    {
        $this->expectException(QueryException::class);

        ModelWithForeignMuidTraitTest::create(['model_with_muid_test_id' => 'test']);
    }

    /** @test */
    public function it_generate_a_muidMorph_type_and_id()
    {
        $main = ModelWithMuidTraitTest::create(['name' => 'Mood']);
        $morphModel = $main->modelWithMuidMorph()
            ->create();

        $this->assertEquals($main->id, $morphModel->testable_id);
        $this->assertEquals(ModelWithMuidTraitTest::class, $morphModel->testable_type);
    }
}
