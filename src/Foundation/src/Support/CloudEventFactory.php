<?php

namespace AlazziAz\LaravelDapr\Support;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use DateTimeInterface;
use Throwable;

class CloudEventFactory
{
    public function __construct(
        protected Repository    $config,
        protected TopicResolver $topics
    )
    {
    }

    public function make(object $event, array $metadata = []): array
    {
        $contentType = $this->getContentType();

        if ($this->shouldWrap()) {
            return [
                ...$this->makeCloudEventEnvelope($event, $contentType),
                ...$metadata,
            ];
        }

        return [
            ...$metadata,
            'rawPayload' => 'true',
        ];
    }

    public function getContentType(): string
    {
        return $this->config->get('dapr.publisher.serialization.content_type', 'application/json');
    }

    public function shouldWrap(): bool
    {
        return (bool)$this->config->get('dapr.publisher.serialization.wrap_cloudevent', true);
    }

    protected function makeCloudEventEnvelope(
        object $event,
        string $datacontenttype,
        array  $extensions = [],
    ): array
    {
        $spec = (string)$this->config->get('dapr.publisher.cloudevents.specversion', '1.0');

        $source = (string)$this->config->get('dapr.publisher.cloudevents.source', config('app.url', 'laravel-service'));

        $type = $this->resolveType($event);

        $id = $this->generateId();

        $time = $this->formatTime(now());


        $subject = $this->resolveSubject($event);

        // Normalize extensions: avoid overwriting core keys
        foreach (['specversion', 'id', 'source', 'type', 'subject', 'time', 'datacontenttype', 'data'] as $reserved) {
            unset($extensions[$reserved]);
        }

        return array_filter([
            'cloudevent.specversion' => $spec,
            'cloudevent.id' => $id,
            'cloudevent.source' => $source,
            'cloudevent.type' => $type,
            'cloudevent.subject' => $subject,
            'cloudevent.time' => $time,
            'cloudevent.datacontenttype' => $datacontenttype,
            ...$extensions,
        ], static fn($v) => $v !== null);
    }

    protected function resolveType(object $event): string
    {
        $strategy = (string)$this->config->get('dapr.publisher.cloudevents.type_strategy', 'class');

        return match ($strategy) {
            // Full class name (stable enough if you version namespaces)
            'class' => $event::class,

            // "alias" => event defines a stable alias, like Laravel's broadcastAs()
            'alias' => method_exists($event, 'cloudEventType')
                ? $event?->cloudEventType()
                : $event::class,

            default => $event::class,
        };
    }

    protected function generateId(): ?string
    {
        $strategy = (string)$this->config->get('dapr.publisher.cloudevents.id_strategy', 'ulid');

        return match ($strategy) {
            'uuid' => (string)Str::uuid(),
            'ulid' => (string)Str::ulid(),
            default => null,
        };
    }

    protected function formatTime(mixed $value): string
    {
        if ($value instanceof Carbon) {
            return $value->toRfc3339String();
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format(DateTimeInterface::RFC3339_EXTENDED);
        }

        if (is_numeric($value)) {
            return Carbon::createFromTimestamp($value)->toRfc3339String();
        }

        if (is_string($value)) {
            return Carbon::parse($value)->toRfc3339String();
        }

        return Carbon::now()->toRfc3339String();
    }

    protected function resolveSubject(object $event): ?string
    {
        // Optional. Two common options:
        // 1) Event provides it
        if (method_exists($event, 'cloudEventSubject')) {
            $subject = $event->cloudEventSubject();
            return $subject !== null ? (string)$subject : null;
        }

        // 2) Use topic as subject (nice for filtering/diagnostics)
        // Only if your TopicResolver can resolve from event instance/class.
        if (method_exists($this->topics, 'resolveTopic')) {
            try {
                return (string)$this->topics->resolveTopic($event);
            } catch (Throwable) {
                // ignore
            }
        }

        return null;
    }
}
