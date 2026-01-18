<?php

declare(strict_types=1);

namespace Rawaby88\Muid\Support;

use DateTimeImmutable;

class MuidComponents
{
    public function __construct(
        public readonly string $prefix,
        public readonly string $body,
        public readonly string $strategy,
        public readonly ?int $timestamp = null,
        public readonly ?string $random = null,
        public readonly ?string $signature = null,
        public readonly ?int $sequence = null,
    ) {}

    /**
     * Get the full MUID string.
     */
    public function toString(): string
    {
        return $this->prefix.'_'.$this->body;
    }

    /**
     * Get the timestamp as a DateTimeImmutable.
     */
    public function getDateTime(): ?DateTimeImmutable
    {
        if ($this->timestamp === null) {
            return null;
        }

        return DateTimeImmutable::createFromFormat(
            'U.u',
            sprintf('%d.%03d', intdiv($this->timestamp, 1000), $this->timestamp % 1000)
        ) ?: null;
    }

    /**
     * Check if the MUID has a signature.
     */
    public function hasSignature(): bool
    {
        return $this->signature !== null;
    }

    /**
     * Check if the MUID is time-sortable.
     */
    public function isTimeSortable(): bool
    {
        return $this->timestamp !== null;
    }

    /**
     * Check if the MUID is incremental.
     */
    public function isIncremental(): bool
    {
        return $this->sequence !== null;
    }

    /**
     * Convert to array representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'prefix' => $this->prefix,
            'body' => $this->body,
            'strategy' => $this->strategy,
            'timestamp' => $this->timestamp,
            'random' => $this->random,
            'signature' => $this->signature,
            'sequence' => $this->sequence,
        ];
    }
}
