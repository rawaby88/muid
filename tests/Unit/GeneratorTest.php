<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Rawaby88\Muid\Generators\IncrementalGenerator;
use Rawaby88\Muid\Generators\OrderedGenerator;
use Rawaby88\Muid\Generators\PaddedIncrementalGenerator;
use Rawaby88\Muid\Support\Encoder;
use Rawaby88\Muid\Tests\TestCase;

class GeneratorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Reset sequence caches
        IncrementalGenerator::resetSequenceCache();
        PaddedIncrementalGenerator::resetSequenceCache();
    }

    #[Test]
    public function ordered_generator_creates_valid_muids(): void
    {
        $generator = new OrderedGenerator(new Encoder);

        $muid = $generator->generate('usr');

        $this->assertStringStartsWith('usr_', $muid);
        $this->assertLessThanOrEqual(36, strlen($muid));
        $this->assertTrue($generator->validate($muid));
    }

    #[Test]
    public function ordered_generator_creates_time_sortable_muids(): void
    {
        $generator = new OrderedGenerator(new Encoder);

        $muid1 = $generator->generate('usr');
        usleep(1000); // 1ms delay
        $muid2 = $generator->generate('usr');

        // Extract bodies and compare - they should be sortable
        $body1 = substr($muid1, 4);
        $body2 = substr($muid2, 4);

        $this->assertLessThan($body2, $body1);
    }

    #[Test]
    public function ordered_generator_parses_components(): void
    {
        $generator = new OrderedGenerator(new Encoder);

        $muid = $generator->generate('usr');
        $components = $generator->parse($muid);

        $this->assertNotNull($components);
        $this->assertEquals('usr', $components->prefix);
        $this->assertEquals('ordered', $components->strategy);
        $this->assertNotNull($components->timestamp);
        $this->assertNotNull($components->random);
    }

    #[Test]
    public function ordered_generator_creates_unique_muids(): void
    {
        $generator = new OrderedGenerator(new Encoder);

        $muids = [];
        for ($i = 0; $i < 100; $i++) {
            $muids[] = $generator->generate('usr');
        }

        $this->assertCount(100, array_unique($muids));
    }

    #[Test]
    public function incremental_generator_creates_sequential_muids(): void
    {
        $generator = new IncrementalGenerator;

        IncrementalGenerator::setSequence('ord', 0);

        $muid1 = $generator->generate('ord');
        $muid2 = $generator->generate('ord');
        $muid3 = $generator->generate('ord');

        $this->assertEquals('ord_1', $muid1);
        $this->assertEquals('ord_2', $muid2);
        $this->assertEquals('ord_3', $muid3);
    }

    #[Test]
    public function incremental_generator_validates_muids(): void
    {
        $generator = new IncrementalGenerator;

        $this->assertTrue($generator->validate('ord_1'));
        $this->assertTrue($generator->validate('ord_999999'));
        $this->assertFalse($generator->validate('ord_abc'));
        $this->assertFalse($generator->validate('ord_'));
    }

    #[Test]
    public function incremental_generator_parses_components(): void
    {
        $generator = new IncrementalGenerator;

        $components = $generator->parse('ord_42');

        $this->assertNotNull($components);
        $this->assertEquals('ord', $components->prefix);
        $this->assertEquals('42', $components->body);
        $this->assertEquals('incremental', $components->strategy);
        $this->assertEquals(42, $components->sequence);
    }

    #[Test]
    public function padded_generator_creates_zero_padded_muids(): void
    {
        $generator = new PaddedIncrementalGenerator;

        PaddedIncrementalGenerator::setSequence('inv', 0);

        $muid1 = $generator->generate('inv');
        $muid2 = $generator->generate('inv');

        $this->assertEquals('inv_0000001', $muid1);
        $this->assertEquals('inv_0000002', $muid2);
    }

    #[Test]
    public function padded_generator_validates_muids(): void
    {
        $generator = new PaddedIncrementalGenerator;

        $this->assertTrue($generator->validate('inv_0000001'));
        $this->assertTrue($generator->validate('inv_0000999'));
        $this->assertFalse($generator->validate('inv_abc0001'));
        $this->assertFalse($generator->validate('inv_'));
    }

    #[Test]
    public function padded_generator_parses_components(): void
    {
        $generator = new PaddedIncrementalGenerator;

        $components = $generator->parse('inv_0000042');

        $this->assertNotNull($components);
        $this->assertEquals('inv', $components->prefix);
        $this->assertEquals('0000042', $components->body);
        $this->assertEquals('padded', $components->strategy);
        $this->assertEquals(42, $components->sequence);
    }

    #[Test]
    public function ordered_generator_respects_max_length(): void
    {
        $generator = new OrderedGenerator(new Encoder);

        $muid = $generator->generate('usr', 24);

        $this->assertLessThanOrEqual(24, strlen($muid));
    }

    #[Test]
    public function generators_throw_on_too_short_max_length(): void
    {
        $generator = new OrderedGenerator(new Encoder);

        $this->expectException(\InvalidArgumentException::class);
        $generator->generate('verylongprefix', 16);
    }

    #[Test]
    public function generators_validate_prefix(): void
    {
        $generator = new OrderedGenerator(new Encoder);

        // Valid prefixes
        $this->assertTrue($generator->validate($generator->generate('us')));
        $this->assertTrue($generator->validate($generator->generate('usr')));
        $this->assertTrue($generator->validate($generator->generate('customer')));

        // Invalid prefixes should fail validation
        $this->assertFalse($generator->validate('_test')); // no prefix
        $this->assertFalse($generator->validate('ab')); // invalid body
    }

    #[Test]
    public function generators_return_correct_strategy(): void
    {
        $this->assertEquals('ordered', (new OrderedGenerator(new Encoder))->getStrategy());
        $this->assertEquals('incremental', (new IncrementalGenerator)->getStrategy());
        $this->assertEquals('padded', (new PaddedIncrementalGenerator)->getStrategy());
    }
}
