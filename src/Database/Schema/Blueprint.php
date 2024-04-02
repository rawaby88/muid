<?php

namespace Rawaby88\Muid\Database\Schema;

use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use Illuminate\Database\Schema\ForeignIdColumnDefinition;
use Illuminate\Support\Fluent;

class Blueprint extends BaseBlueprint
{
    /**
     * Create a new muid column as the primary key(s) for the table.
     *
     * @param  string|array  $columns
     * @param  string|null  $name
     * @param  string|null  $algorithm
     * @return Fluent
     */
    public function primaryMuid($column = 'muid', $name = null, $algorithm = null)
    {
        $length = config('muid.muid_length');
        $this->addColumn('string', $column, compact('length'));

        return $this->primary($column, $name, $algorithm);
    }

    /**
     * Create a new muid column on the table with a foreign key constraint.
     *
     * @param  string  $column
     * @return ForeignIdColumnDefinition
     */
    public function foreignMuid($column = 'muid')
    {
        return $this->addColumnDefinition(new ForeignIdColumnDefinition($this, [
            'type' => 'string',
            'name' => $column,
            'length' => config('muid.muid_length'),
        ]));
    }

    /**
     * Add the proper columns for a polymorphic table using MUIDs.
     *
     * @param  string  $name
     * @param  string|null  $indexName
     * @return void
     */
    public function muidMorphs($name, $indexName = null)
    {
        $this->string("{$name}_type");

        $this->muid("{$name}_id");

        $this->index([
            "{$name}_type",
            "{$name}_id",
        ], $indexName);
    }

    /**
     * Create a new muid column on the table.
     *
     * @param  string  $column
     * @return ColumnDefinition
     */
    public function muid($column = 'muid')
    {
        $length = config('muid.muid_length');

        return $this->addColumn('string', $column, compact('length'));
    }

    /**
     * Add nullable columns for a polymorphic table using MUIDs.
     *
     * @param  string  $name
     * @param  string|null  $indexName
     * @return void
     */
    public function nullableMuidMorphs($name, $indexName = null)
    {
        $this->string("{$name}_type")
            ->nullable();

        $this->muid("{$name}_id")
            ->nullable();

        $this->index([
            "{$name}_type",
            "{$name}_id",
        ], $indexName);
    }
}
