<?php

namespace Rawaby88\Muid\Tests\Database\Eloquent;

use PDOException;
use Rawaby88\Muid\Tests\Models\ModelWithMuidTraitTest;
use Rawaby88\Muid\Tests\TestCase;

class ModelUsingTraitWithoutMuidTest extends TestCase
{
    /** @test */
    public function it_does_not_generate_muid_when_no_id_has_been_set()
    {
        $this->expectException(PDOException::class);

        ModelWithMuidTraitTest::create([]);
    }
}
