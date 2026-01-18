<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Database\Schema;

use Illuminate\Database\Schema\Blueprint;

/**
 * Helper class for MUID Blueprint macros.
 *
 * This class is not used directly - the macros are registered in MuidServiceProvider.
 * This file serves as documentation and for IDE autocompletion support.
 *
 * @see \Rawaby88\Muid\MuidServiceProvider::registerBlueprintMacros()
 *
 * @mixin Blueprint
 *
 * Standard MUID macros (36 chars):
 * @method \Illuminate\Database\Schema\ColumnDefinition muid(string $column = 'id')
 * @method \Illuminate\Database\Schema\ColumnDefinition primaryMuid(string $column = 'id')
 * @method \Illuminate\Database\Schema\ColumnDefinition foreignMuid(string $column)
 * @method \Illuminate\Database\Schema\ColumnDefinition nullableMuid(string $column)
 * @method void muidMorphs(string $name, ?string $indexName = null)
 * @method void nullableMuidMorphs(string $name, ?string $indexName = null)
 *
 * Small MUID macros (24 chars):
 * @method \Illuminate\Database\Schema\ColumnDefinition smallMuid(string $column = 'id')
 * @method \Illuminate\Database\Schema\ColumnDefinition primarySmallMuid(string $column = 'id')
 * @method \Illuminate\Database\Schema\ColumnDefinition foreignSmallMuid(string $column)
 * @method \Illuminate\Database\Schema\ColumnDefinition nullableSmallMuid(string $column)
 * @method void smallMuidMorphs(string $name, ?string $indexName = null)
 * @method void nullableSmallMuidMorphs(string $name, ?string $indexName = null)
 *
 * Tiny MUID macros (16 chars):
 * @method \Illuminate\Database\Schema\ColumnDefinition tinyMuid(string $column = 'id')
 * @method \Illuminate\Database\Schema\ColumnDefinition primaryTinyMuid(string $column = 'id')
 * @method \Illuminate\Database\Schema\ColumnDefinition foreignTinyMuid(string $column)
 * @method \Illuminate\Database\Schema\ColumnDefinition nullableTinyMuid(string $column)
 * @method void tinyMuidMorphs(string $name, ?string $indexName = null)
 * @method void nullableTinyMuidMorphs(string $name, ?string $indexName = null)
 */
class MuidBlueprint
{
    // This class is intentionally empty.
    // It exists only to provide IDE documentation for Blueprint macros.
}
