<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Rawaby88\Muid\Tests\TestCase;

class MigrationTest extends TestCase
{
    #[Test]
    public function it_creates_standard_muid_column(): void
    {
        Schema::create('test_muid_standard', function (Blueprint $table) {
            $table->muid('id');
        });

        $columns = Schema::getColumns('test_muid_standard');
        $idColumn = collect($columns)->firstWhere('name', 'id');

        $this->assertNotNull($idColumn);
        $this->assertStringContainsString('varchar', strtolower($idColumn['type']));

        Schema::dropIfExists('test_muid_standard');
    }

    #[Test]
    public function it_creates_primary_muid(): void
    {
        Schema::create('test_muid_primary', function (Blueprint $table) {
            $table->primaryMuid();
        });

        $columns = Schema::getColumns('test_muid_primary');
        $idColumn = collect($columns)->firstWhere('name', 'id');

        $this->assertNotNull($idColumn);

        Schema::dropIfExists('test_muid_primary');
    }

    #[Test]
    public function it_creates_foreign_muid(): void
    {
        Schema::create('test_muid_foreign', function (Blueprint $table) {
            $table->id();
            $table->foreignMuid('user_id');
        });

        $columns = Schema::getColumns('test_muid_foreign');
        $userIdColumn = collect($columns)->firstWhere('name', 'user_id');

        $this->assertNotNull($userIdColumn);

        Schema::dropIfExists('test_muid_foreign');
    }

    #[Test]
    public function it_creates_nullable_muid(): void
    {
        Schema::create('test_muid_nullable', function (Blueprint $table) {
            $table->id();
            $table->nullableMuid('optional_id');
        });

        $columns = Schema::getColumns('test_muid_nullable');
        $optionalColumn = collect($columns)->firstWhere('name', 'optional_id');

        $this->assertNotNull($optionalColumn);
        $this->assertTrue($optionalColumn['nullable']);

        Schema::dropIfExists('test_muid_nullable');
    }

    #[Test]
    public function it_creates_muid_morphs(): void
    {
        Schema::create('test_muid_morphs', function (Blueprint $table) {
            $table->id();
            $table->muidMorphs('taggable');
        });

        $columns = Schema::getColumns('test_muid_morphs');

        $typeColumn = collect($columns)->firstWhere('name', 'taggable_type');
        $idColumn = collect($columns)->firstWhere('name', 'taggable_id');

        $this->assertNotNull($typeColumn);
        $this->assertNotNull($idColumn);

        Schema::dropIfExists('test_muid_morphs');
    }

    #[Test]
    public function it_creates_nullable_muid_morphs(): void
    {
        Schema::create('test_muid_nullable_morphs', function (Blueprint $table) {
            $table->id();
            $table->nullableMuidMorphs('commentable');
        });

        $columns = Schema::getColumns('test_muid_nullable_morphs');

        $typeColumn = collect($columns)->firstWhere('name', 'commentable_type');
        $idColumn = collect($columns)->firstWhere('name', 'commentable_id');

        $this->assertNotNull($typeColumn);
        $this->assertNotNull($idColumn);
        $this->assertTrue($typeColumn['nullable']);
        $this->assertTrue($idColumn['nullable']);

        Schema::dropIfExists('test_muid_nullable_morphs');
    }

    #[Test]
    public function it_creates_small_muid_column(): void
    {
        Schema::create('test_small_muid', function (Blueprint $table) {
            $table->smallMuid('id');
        });

        $columns = Schema::getColumns('test_small_muid');
        $idColumn = collect($columns)->firstWhere('name', 'id');

        $this->assertNotNull($idColumn);

        Schema::dropIfExists('test_small_muid');
    }

    #[Test]
    public function it_creates_primary_small_muid(): void
    {
        Schema::create('test_small_muid_primary', function (Blueprint $table) {
            $table->primarySmallMuid();
        });

        $columns = Schema::getColumns('test_small_muid_primary');
        $idColumn = collect($columns)->firstWhere('name', 'id');

        $this->assertNotNull($idColumn);

        Schema::dropIfExists('test_small_muid_primary');
    }

    #[Test]
    public function it_creates_tiny_muid_column(): void
    {
        Schema::create('test_tiny_muid', function (Blueprint $table) {
            $table->tinyMuid('id');
        });

        $columns = Schema::getColumns('test_tiny_muid');
        $idColumn = collect($columns)->firstWhere('name', 'id');

        $this->assertNotNull($idColumn);

        Schema::dropIfExists('test_tiny_muid');
    }

    #[Test]
    public function it_creates_primary_tiny_muid(): void
    {
        Schema::create('test_tiny_muid_primary', function (Blueprint $table) {
            $table->primaryTinyMuid();
        });

        $columns = Schema::getColumns('test_tiny_muid_primary');
        $idColumn = collect($columns)->firstWhere('name', 'id');

        $this->assertNotNull($idColumn);

        Schema::dropIfExists('test_tiny_muid_primary');
    }

    #[Test]
    public function it_creates_small_muid_morphs(): void
    {
        Schema::create('test_small_muid_morphs', function (Blueprint $table) {
            $table->id();
            $table->muidSmallMorphs('likeable');
        });

        $columns = Schema::getColumns('test_small_muid_morphs');

        $typeColumn = collect($columns)->firstWhere('name', 'likeable_type');
        $idColumn = collect($columns)->firstWhere('name', 'likeable_id');

        $this->assertNotNull($typeColumn);
        $this->assertNotNull($idColumn);

        Schema::dropIfExists('test_small_muid_morphs');
    }

    #[Test]
    public function it_creates_tiny_muid_morphs(): void
    {
        Schema::create('test_tiny_muid_morphs', function (Blueprint $table) {
            $table->id();
            $table->muidTinyMorphs('viewable');
        });

        $columns = Schema::getColumns('test_tiny_muid_morphs');

        $typeColumn = collect($columns)->firstWhere('name', 'viewable_type');
        $idColumn = collect($columns)->firstWhere('name', 'viewable_id');

        $this->assertNotNull($typeColumn);
        $this->assertNotNull($idColumn);

        Schema::dropIfExists('test_tiny_muid_morphs');
    }
}
