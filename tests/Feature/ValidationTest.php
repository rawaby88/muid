<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Tests\Feature;

use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Rawaby88\Muid\Facades\Muid;
use Rawaby88\Muid\Rules\ValidMuid;
use Rawaby88\Muid\Tests\TestCase;

class ValidationTest extends TestCase
{
    #[Test]
    public function it_validates_muid_without_prefix(): void
    {
        $validMuid = Muid::generate('usr', 'ordered');

        $validator = Validator::make(
            ['user_id' => $validMuid],
            ['user_id' => [new ValidMuid]]
        );

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function it_validates_muid_with_prefix(): void
    {
        $validMuid = Muid::generate('usr', 'ordered');

        $validator = Validator::make(
            ['user_id' => $validMuid],
            ['user_id' => [new ValidMuid('usr')]]
        );

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function it_fails_validation_for_wrong_prefix(): void
    {
        $muid = Muid::generate('cus', 'ordered');

        $validator = Validator::make(
            ['user_id' => $muid],
            ['user_id' => [new ValidMuid('usr')]]
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('user_id', $validator->errors()->toArray());
    }

    #[Test]
    public function it_fails_validation_for_invalid_muid(): void
    {
        $validator = Validator::make(
            ['user_id' => 'not-a-valid-muid'],
            ['user_id' => [new ValidMuid]]
        );

        $this->assertTrue($validator->fails());
    }

    #[Test]
    public function it_fails_validation_for_non_string(): void
    {
        $validator = Validator::make(
            ['user_id' => 12345],
            ['user_id' => [new ValidMuid]]
        );

        $this->assertTrue($validator->fails());
    }

    #[Test]
    public function it_validates_with_strategy(): void
    {
        $muid = Muid::generate('ord', 'ordered');

        $validator = Validator::make(
            ['order_id' => $muid],
            ['order_id' => [ValidMuid::withStrategy('ordered')]]
        );

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function it_validates_with_prefix_and_strategy(): void
    {
        $muid = Muid::generate('inv', 'ordered');

        $validator = Validator::make(
            ['invoice_id' => $muid],
            ['invoice_id' => [ValidMuid::for('inv', 'ordered')]]
        );

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function it_uses_static_factory_methods(): void
    {
        $muid = Muid::generate('usr', 'ordered');

        // Test make()
        $validator1 = Validator::make(
            ['id' => $muid],
            ['id' => [ValidMuid::make()]]
        );
        $this->assertFalse($validator1->fails());

        // Test withPrefix()
        $validator2 = Validator::make(
            ['id' => $muid],
            ['id' => [ValidMuid::withPrefix('usr')]]
        );
        $this->assertFalse($validator2->fails());
    }

    #[Test]
    public function it_provides_appropriate_error_messages(): void
    {
        // Test error message without prefix
        $validator1 = Validator::make(
            ['user_id' => 'invalid'],
            ['user_id' => [new ValidMuid]]
        );
        $this->assertStringContainsString('valid MUID', $validator1->errors()->first('user_id'));

        // Test error message with prefix
        $validator2 = Validator::make(
            ['user_id' => 'cus_valid123456789012'],
            ['user_id' => [new ValidMuid('usr')]]
        );
        $errors = $validator2->errors()->first('user_id');
        $this->assertStringContainsString('usr', $errors);
    }

    #[Test]
    public function it_validates_incremental_muids(): void
    {
        $validator = Validator::make(
            ['order_id' => 'ord_123'],
            ['order_id' => [ValidMuid::withPrefix('ord')]]
        );

        $this->assertFalse($validator->fails());
    }

    #[Test]
    public function it_validates_padded_muids(): void
    {
        $validator = Validator::make(
            ['invoice_id' => 'inv_0000001'],
            ['invoice_id' => [ValidMuid::withPrefix('inv')]]
        );

        $this->assertFalse($validator->fails());
    }
}
