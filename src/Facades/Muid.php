<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Facades;

use Illuminate\Support\Facades\Facade;
use Rawaby88\Muid\Contracts\MuidGenerator;
use Rawaby88\Muid\Support\Encoder;
use Rawaby88\Muid\Support\MuidComponents;
use Rawaby88\Muid\Support\MuidFactory;
use Rawaby88\Muid\Support\MuidParser;
use Rawaby88\Muid\Support\MuidValidator;

/**
 * @method static string generate(string $prefix, ?string $strategy = null, ?int $maxLength = null)
 * @method static string standard(string $prefix, ?string $strategy = null)
 * @method static string small(string $prefix, ?string $strategy = null)
 * @method static string tiny(string $prefix, ?string $strategy = null)
 * @method static bool isValid(string $muid, ?string $expectedPrefix = null, ?string $strategy = null)
 * @method static MuidComponents|null parse(string $muid, ?string $strategy = null)
 * @method static string|null extractPrefix(string $muid)
 * @method static string|null extractBody(string $muid)
 * @method static MuidGenerator getGenerator(string $strategy)
 * @method static array getAvailableStrategies()
 * @method static Encoder getEncoder()
 * @method static MuidValidator getValidator()
 * @method static MuidParser getParser()
 *
 * @see \Rawaby88\Muid\Support\MuidFactory
 */
class Muid extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MuidFactory::class;
    }
}
