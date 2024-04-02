<?php

namespace Rawaby88\Muid\Tests\Database\Eloquent;

use PDOException;
use Rawaby88\Muid\Tests\Models\ModelWithMuidExtendsTest;
use Rawaby88\Muid\Tests\TestCase;

class ModelExtendingClassWithoutMuidTest extends TestCase
{
    /** @test */
    public function it_does_not_generate_muid_when_no_id_has_been_set()
    {
        $this->expectException(PDOException::class);

        ModelWithMuidExtendsTest::create([]);
    }
}
