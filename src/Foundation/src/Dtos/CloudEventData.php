<?php

namespace AlazziAz\LaravelDapr\Dtos;

use Illuminate\Support\Arr;

final class CloudEventData
{
    public function __construct(
        public readonly ?string $specversion,
        public readonly ?string $id,
        public readonly ?string $source,
        public readonly ?string $type,
        public readonly ?string $subject,
        public readonly ?string $time,
        public readonly ?string $datacontenttype,
        public readonly mixed $data,
        public readonly array $extensions = [],
        public readonly array $raw = [],
    ) {}

    public function extension(string $key, mixed $default = null): mixed
    {
        return $this->extensions[$key] ?? $default;
    }

    public function toArray(): array
    {
        return array_filter([
            'specversion'      => $this->specversion,
            'id'               => $this->id,
            'source'           => $this->source,
            'type'             => $this->type,
            'subject'          => $this->subject,
            'time'             => $this->time,
            'datacontenttype'  => $this->datacontenttype,
            'data'             => $this->data,
            ...$this->extensions,
        ], static fn ($value) => $value !== null);
    }


    public function raw(): array
    {
        return $this->raw;
    }

    /**
     * Get CloudEvent extension attributes only.
     */
    public function extensions(): array
    {
        return $this->extensions;
    }

    /**
     * Get standard CloudEvent attributes only.
     */
    public function attributes(): array
    {
        return Arr::only($this->toArray(), [
            'specversion',
            'id',
            'source',
            'type',
            'subject',
            'time',
            'datacontenttype',
        ]);
    }

    /**
     * Check if CloudEvent matches a specific type.
     */
    public function isType(string $type): bool
    {
        return $this->type === $type;
    }
}
