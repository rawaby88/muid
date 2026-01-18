<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Rawaby88\Muid\Concerns\HasIntegerMuid;
use Rawaby88\Muid\Concerns\HasMuid;
use Rawaby88\Muid\Facades\Muid;
use Rawaby88\Muid\Tests\TestCase;

class MuidTraitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('test_users', function (Blueprint $table) {
            $table->primaryMuid();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('test_orders', function (Blueprint $table) {
            $table->primarySmallMuid();
            $table->foreignMuid('user_id');
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });

        Schema::create('test_invoices', function (Blueprint $table) {
            $table->primaryIntegerMuid();
            $table->string('number');
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('test_invoices');
        Schema::dropIfExists('test_orders');
        Schema::dropIfExists('test_users');

        parent::tearDown();
    }

    #[Test]
    public function it_generates_muid_on_create(): void
    {
        $user = TestUser::create(['name' => 'John Doe']);

        $this->assertNotNull($user->id);
        $this->assertStringStartsWith('testuser_', $user->id);
        $this->assertTrue(Muid::isValid($user->id));
    }

    #[Test]
    public function it_preserves_existing_muid(): void
    {
        $customId = Muid::generate('testuser', 'ordered');
        $user = TestUser::create(['id' => $customId, 'name' => 'Jane Doe']);

        $this->assertEquals($customId, $user->id);
    }

    #[Test]
    public function it_uses_custom_prefix(): void
    {
        $order = TestOrder::create(['user_id' => 'usr_test', 'total' => 99.99]);

        $this->assertStringStartsWith('order_', $order->id);
    }

    #[Test]
    public function it_sets_key_type_to_string(): void
    {
        $user = new TestUser;

        $this->assertEquals('string', $user->getKeyType());
    }

    #[Test]
    public function it_disables_incrementing(): void
    {
        $user = new TestUser;

        $this->assertFalse($user->getIncrementing());
    }

    #[Test]
    public function it_generates_new_muid(): void
    {
        $user = new TestUser;

        $muid = $user->newMuid();

        $this->assertStringStartsWith('testuser_', $muid);
        $this->assertTrue(Muid::isValid($muid));
    }

    #[Test]
    public function it_validates_muid_for_model(): void
    {
        $user = new TestUser;
        $validMuid = Muid::generate('testuser', 'ordered');
        $invalidMuid = Muid::generate('other', 'ordered');

        $this->assertTrue($user->isValidMuid($validMuid));
        $this->assertFalse($user->isValidMuid($invalidMuid));
    }

    #[Test]
    public function it_supports_relationships(): void
    {
        $user = TestUser::create(['name' => 'Alice']);
        $order = TestOrder::create(['user_id' => $user->id, 'total' => 150.00]);

        $this->assertEquals($user->id, $order->user_id);
    }

    // Tests for HasIntegerMuid trait

    #[Test]
    public function integer_muid_generates_auto_increment(): void
    {
        $invoice1 = TestInvoice::create(['number' => 'INV-001']);
        $invoice2 = TestInvoice::create(['number' => 'INV-002']);

        $this->assertEquals(1, $invoice1->getRawId());
        $this->assertEquals(2, $invoice2->getRawId());
    }

    #[Test]
    public function integer_muid_formats_with_prefix(): void
    {
        $invoice = TestInvoice::create(['number' => 'INV-001']);

        $this->assertEquals('inv_0000001', $invoice->muid);
    }

    #[Test]
    public function integer_muid_parses_muid_string(): void
    {
        $invoice = new TestInvoice;

        $this->assertEquals(42, $invoice->parseMuid('inv_42'));
        $this->assertEquals(42, $invoice->parseMuid('inv_0000042'));
        $this->assertNull($invoice->parseMuid('wrong_42'));
        $this->assertNull($invoice->parseMuid('inv_abc'));
    }

    #[Test]
    public function integer_muid_validates_muid_string(): void
    {
        $invoice = new TestInvoice;

        $this->assertTrue($invoice->isValidMuid('inv_42'));
        $this->assertTrue($invoice->isValidMuid('inv_0000042'));
        $this->assertFalse($invoice->isValidMuid('wrong_42'));
        $this->assertFalse($invoice->isValidMuid('inv_abc'));
    }

    #[Test]
    public function integer_muid_find_by_muid(): void
    {
        $invoice = TestInvoice::create(['number' => 'INV-001']);

        $found = TestInvoice::findByMuid('inv_1');
        $this->assertNotNull($found);
        $this->assertEquals($invoice->id, $found->id);

        $found2 = TestInvoice::findByMuid('inv_0000001');
        $this->assertNotNull($found2);
        $this->assertEquals($invoice->id, $found2->id);

        $notFound = TestInvoice::findByMuid('inv_999');
        $this->assertNull($notFound);
    }

    #[Test]
    public function integer_muid_includes_muid_in_array(): void
    {
        $invoice = TestInvoice::create(['number' => 'INV-001']);
        $array = $invoice->toArray();

        $this->assertArrayHasKey('muid', $array);
        $this->assertEquals('inv_0000001', $array['muid']);
    }
}

/**
 * Test model for MUID trait tests.
 *
 * @property string $id
 * @property string $name
 */
class TestUser extends Model
{
    use HasMuid;

    protected $table = 'test_users';

    protected $fillable = ['id', 'name'];
}

/**
 * Test model with custom MUID configuration.
 *
 * @property string $id
 * @property string $user_id
 * @property float $total
 */
class TestOrder extends Model
{
    use HasMuid;

    protected $table = 'test_orders';

    protected $fillable = ['id', 'user_id', 'total'];

    public function muidPrefix(): string
    {
        return 'order';
    }

    public function muidMaxLength(): int
    {
        return 24; // Small MUID
    }
}

/**
 * Test model with integer-based MUID.
 *
 * @property int $id
 * @property string $number
 */
class TestInvoice extends Model
{
    use HasIntegerMuid;

    protected $table = 'test_invoices';

    protected $fillable = ['number'];

    public function muidPrefix(): string
    {
        return 'inv';
    }

    public function muidStrategy(): string
    {
        return 'padded';
    }
}
