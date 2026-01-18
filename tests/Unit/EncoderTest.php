<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Rawaby88\Muid\Support\Encoder;
use Rawaby88\Muid\Tests\TestCase;

class EncoderTest extends TestCase
{
    #[Test]
    public function it_encodes_integers_to_base62(): void
    {
        $encoder = new Encoder('base62');

        $this->assertEquals('0', $encoder->encodeInt(0));
        $this->assertEquals('1', $encoder->encodeInt(1));
        $this->assertEquals('A', $encoder->encodeInt(10));
        $this->assertEquals('a', $encoder->encodeInt(36));
        $this->assertEquals('10', $encoder->encodeInt(62));
    }

    #[Test]
    public function it_decodes_base62_to_integers(): void
    {
        $encoder = new Encoder('base62');

        $this->assertEquals(0, $encoder->decodeInt('0'));
        $this->assertEquals(1, $encoder->decodeInt('1'));
        $this->assertEquals(10, $encoder->decodeInt('A'));
        $this->assertEquals(36, $encoder->decodeInt('a'));
        $this->assertEquals(62, $encoder->decodeInt('10'));
    }

    #[Test]
    public function it_encodes_and_decodes_large_numbers(): void
    {
        $encoder = new Encoder('base62');

        $timestamp = (int) (microtime(true) * 1000);
        $encoded = $encoder->encodeInt($timestamp);
        $decoded = $encoder->decodeInt($encoded);

        $this->assertEquals($timestamp, $decoded);
    }

    #[Test]
    public function it_encodes_integers_with_padding(): void
    {
        $encoder = new Encoder('base62');

        $this->assertEquals('00000001', $encoder->encodeIntPadded(1, 8));
        $this->assertEquals('0000000A', $encoder->encodeIntPadded(10, 8));
    }

    #[Test]
    public function it_throws_on_negative_numbers(): void
    {
        $encoder = new Encoder('base62');

        $this->expectException(InvalidArgumentException::class);
        $encoder->encodeInt(-1);
    }

    #[Test]
    public function it_throws_on_overflow_with_padding(): void
    {
        $encoder = new Encoder('base62');

        // This should overflow 2 characters in base62
        $this->expectException(InvalidArgumentException::class);
        $encoder->encodeIntPadded(62 * 62, 2);
    }

    #[Test]
    public function it_generates_random_strings(): void
    {
        $encoder = new Encoder('base62');

        $random = $encoder->randomString(16);

        $this->assertEquals(16, strlen($random));
        $this->assertTrue($encoder->isValid($random));
    }

    #[Test]
    public function it_validates_encoded_strings(): void
    {
        $encoder = new Encoder('base62');

        $this->assertTrue($encoder->isValid('abc123'));
        $this->assertTrue($encoder->isValid('ABC123xyz'));
        $this->assertFalse($encoder->isValid('abc_123')); // underscore not valid
        $this->assertFalse($encoder->isValid('abc-123')); // dash not valid
    }

    #[Test]
    public function it_works_with_base36(): void
    {
        $encoder = new Encoder('base36');

        $this->assertEquals('0', $encoder->encodeInt(0));
        $this->assertEquals('a', $encoder->encodeInt(10));
        $this->assertEquals('10', $encoder->encodeInt(36));

        $this->assertEquals(0, $encoder->decodeInt('0'));
        $this->assertEquals(10, $encoder->decodeInt('a'));
        $this->assertEquals(36, $encoder->decodeInt('10'));
    }

    #[Test]
    public function it_throws_on_invalid_encoding_type(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Encoder('base64');
    }

    #[Test]
    public function it_encodes_timestamps(): void
    {
        $encoder = new Encoder('base62');

        $timestamp = 1705000000000; // Example millisecond timestamp
        $encoded = $encoder->encodeTimestamp($timestamp, 8);

        $this->assertEquals(8, strlen($encoded));

        $decoded = $encoder->decodeTimestamp($encoded);
        $this->assertEquals($timestamp, $decoded);
    }

    #[Test]
    public function it_returns_encoding_type(): void
    {
        $encoder62 = new Encoder('base62');
        $encoder36 = new Encoder('base36');

        $this->assertEquals('base62', $encoder62->getEncodingType());
        $this->assertEquals('base36', $encoder36->getEncodingType());
    }

    #[Test]
    public function it_returns_charset(): void
    {
        $encoder = new Encoder('base62');

        $this->assertEquals(Encoder::BASE62_CHARS, $encoder->getCharset());
    }

    #[Test]
    public function it_returns_base(): void
    {
        $encoder62 = new Encoder('base62');
        $encoder36 = new Encoder('base36');

        $this->assertEquals(62, $encoder62->getBase());
        $this->assertEquals(36, $encoder36->getBase());
    }

    #[Test]
    public function it_encodes_and_decodes_bytes(): void
    {
        $encoder = new Encoder('base62');

        $bytes = random_bytes(8);
        $encoded = $encoder->encodeBytes($bytes);
        $decoded = $encoder->decodeBytes($encoded);

        $this->assertEquals($bytes, $decoded);
    }
}
