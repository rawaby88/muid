<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Rawaby88\Muid\Facades\Muid;
use Rawaby88\Muid\Tests\TestCase;

class ValidatorTest extends TestCase
{
    #[Test]
    public function it_validates_ordered_muids(): void
    {
        $muid = Muid::generate('usr', 'ordered');

        $this->assertTrue(Muid::isValid($muid));
        $this->assertTrue(Muid::isValid($muid, 'usr'));
        $this->assertFalse(Muid::isValid($muid, 'cus'));
    }

    #[Test]
    public function it_validates_incremental_muids(): void
    {
        $this->assertTrue(Muid::isValid('ord_1'));
        $this->assertTrue(Muid::isValid('ord_1', 'ord'));
        $this->assertFalse(Muid::isValid('ord_1', 'usr'));
    }

    #[Test]
    public function it_validates_padded_muids(): void
    {
        $this->assertTrue(Muid::isValid('inv_0000001'));
        $this->assertTrue(Muid::isValid('inv_0000001', 'inv'));
        $this->assertFalse(Muid::isValid('inv_0000001', 'usr'));
    }

    #[Test]
    public function it_rejects_invalid_formats(): void
    {
        $this->assertFalse(Muid::isValid('')); // empty
        $this->assertFalse(Muid::isValid('usr')); // no separator
        $this->assertFalse(Muid::isValid('_body')); // no prefix
        $this->assertFalse(Muid::isValid('usr_')); // no body
        $this->assertFalse(Muid::isValid('us__body')); // double separator
        $this->assertFalse(Muid::isValid('a_body')); // prefix too short
    }

    #[Test]
    public function it_rejects_invalid_prefix_patterns(): void
    {
        $this->assertFalse(Muid::isValid('1usr_body')); // starts with number
        $this->assertFalse(Muid::isValid('usr-test_body')); // contains hyphen
        $this->assertFalse(Muid::isValid('usr.test_body')); // contains dot
    }

    #[Test]
    public function it_rejects_invalid_body_patterns(): void
    {
        // For incremental strategy (numeric body)
        $this->assertFalse(Muid::isValid('ord_abc', null, 'incremental'));

        // For ordered strategy (base62 body)
        $this->assertFalse(Muid::isValid('usr_!!!invalid', null, 'ordered'));
    }

    #[Test]
    public function it_validates_with_specific_strategy(): void
    {
        // Incremental MUID should fail ordered validation (body too short)
        $this->assertFalse(Muid::isValid('ord_1', null, 'ordered'));

        // But should pass incremental validation
        $this->assertTrue(Muid::isValid('ord_1', null, 'incremental'));
    }

    #[Test]
    public function it_validates_many_muids(): void
    {
        $muids = [
            Muid::generate('usr', 'ordered'),
            Muid::generate('cus', 'ordered'),
            'invalid_muid',
        ];

        $validator = Muid::getValidator();
        $results = $validator->validateMany($muids);

        $this->assertCount(3, $results);
        $this->assertTrue($results[$muids[0]]);
        $this->assertTrue($results[$muids[1]]);
        $this->assertFalse($results['invalid_muid']);
    }

    #[Test]
    public function it_filters_valid_muids(): void
    {
        $validMuid1 = Muid::generate('usr', 'ordered');
        $validMuid2 = Muid::generate('cus', 'ordered');

        $muids = [$validMuid1, 'invalid', $validMuid2];

        $validator = Muid::getValidator();
        $filtered = $validator->filterValid($muids);

        $this->assertCount(2, $filtered);
        $this->assertContains($validMuid1, $filtered);
        $this->assertContains($validMuid2, $filtered);
        $this->assertNotContains('invalid', $filtered);
    }

    #[Test]
    public function it_detects_strategy_from_body(): void
    {
        $validator = Muid::getValidator();

        // Numeric body without leading zeros -> incremental
        $this->assertEquals('incremental', $validator->detectStrategy('123'));

        // Numeric body with leading zeros -> padded
        $this->assertEquals('padded', $validator->detectStrategy('0000123'));

        // Alphanumeric body -> ordered
        $this->assertEquals('ordered', $validator->detectStrategy('abc123XYZ456'));
    }
}
