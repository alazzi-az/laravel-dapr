<?php

namespace AlazziAz\LaravelDaprPublisher;

use AlazziAz\LaravelDapr\Contracts\EventPublisher as EventPublisherContract;
use AlazziAz\LaravelDapr\Support\CloudEventFactory;
use AlazziAz\LaravelDapr\Support\EventPayloadSerializer;
use AlazziAz\LaravelDapr\Support\TopicResolver;
use AlazziAz\LaravelDaprPublisher\Publishing\EventContext;
use AlazziAz\LaravelDaprPublisher\Publishing\EventPipeline;
use Dapr\Client\DaprClient;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Log;

class EventPublisher implements EventPublisherContract
{
    protected DaprClient $client;
    public function __construct(

        protected TopicResolver $topics,
        protected EventPayloadSerializer $serializer,
        protected CloudEventFactory $cloudEvents,
        protected EventPipeline $pipeline,
        protected Repository $config
    ) {
        $this->client = \Dapr\Client\DaprClient::clientBuilder()->build();
    }

    public function publish(object $event, array $metadata = []): void
    {
        $topic = $this->topics->resolve($event);
        $pubsubName = $this->config->get('dapr.pubsub.name', 'pubsub');
        $payload = $this->serializer->serialize($event);
        $middleware = $this->config->get('dapr.publisher.middleware', []);

        $context = new EventContext(
            $event,
            $topic,
            $pubsubName,
            $payload,
            $metadata
        );

        $context = $this->pipeline->send($context, $middleware);

        $body = $this->cloudEvents->shouldWrap()
            ? $this->cloudEvents->make($event, $context->payload(), $context->metadata())
            : $context->payload();

        $this->client->publishEvent(
            $context->pubsubName(),
            $context->topic(),
            $body,
            $context->metadata()
        );

        Log::info('Published event to Dapr.', [
            'event_class' => $event::class,
            'topic' => $context->topic(),
            'pubsub' => $context->pubsubName(),
            'metadata' => $context->metadata(),
        ]);
    }
}
