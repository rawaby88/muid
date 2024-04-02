<?php

namespace Rawaby88\Muid\Console;

use Illuminate\Foundation\Console\ModelMakeCommand;

class MuidModelCommand extends ModelMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'muid:make:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent MUID model class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'MUID Model';

    /**
     * Get the value of a command option.
     *
     * @param  string|null  $key
     * @return string|array|bool|null
     */
    public function option($key = null)
    {
        if ($key === 'pivot') {
            return false;
        }

        return parent::option($key);
    }

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return realpath(__DIR__.'/Stubs/model.stub');
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        // Remove the pivot option from the parent class.
        return array_filter(
            parent::getOptions(),
            function (array $option): bool {
                return $option[0] !== 'pivot';
            }
        );
    }
}
