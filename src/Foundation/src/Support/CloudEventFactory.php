<?php

namespace AlazziAz\LaravelDapr\Support;

use Dapr\PubSub\Publish;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CloudEventFactory
{
    public function __construct(
        protected Repository $config,
        protected TopicResolver $topics
    ) {
    }

    public function shouldWrap(): bool
    {
        return (bool) $this->config->get('dapr.serialization.wrap_cloudevent', true);
    }

    public function make(array $metadata = []): array
    {
        $contentType = $this->getContentType();

        if ($this->shouldWrap()) {
            return [
                ...$metadata,
                'contentType' => $contentType,
            ];
        }

        return [
            ...$metadata,
            'rawPayload' => 'true',
            'contentType' => $contentType,
        ];
    }

    protected function formatTime(mixed $value): string
    {
        if ($value instanceof Carbon) {
            return $value->toRfc3339String();
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format(\DateTimeInterface::RFC3339_EXTENDED);
        }

        if (is_numeric($value)) {
            return Carbon::createFromTimestamp($value)->toRfc3339String();
        }

        if (is_string($value)) {
            return Carbon::parse($value)->toRfc3339String();
        }

        return Carbon::now()->toRfc3339String();
    }

    public function getContentType(): string
    {
        return $this->shouldWrap()
            ? 'application/cloudevents+json'
            : 'application/json';
    }
}
