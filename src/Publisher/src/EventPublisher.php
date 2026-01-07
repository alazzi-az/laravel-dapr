<?php

namespace AlazziAz\LaravelDaprPublisher;

use AlazziAz\LaravelDapr\Contracts\EventPublisher as EventPublisherContract;
use AlazziAz\LaravelDapr\Support\CloudEventFactory;
use AlazziAz\LaravelDapr\Support\EventPayloadSerializer;
use AlazziAz\LaravelDapr\Support\TopicResolver;
use AlazziAz\LaravelDaprPublisher\Publishing\EventContext;
use AlazziAz\LaravelDaprPublisher\Publishing\EventPipeline;
use Dapr\Client\DaprClient;
use Dapr\PubSub\Topic;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Log;

class EventPublisher implements EventPublisherContract
{
    protected DaprClient $client;

    public function __construct(
        protected TopicResolver          $topics,
        protected EventPayloadSerializer $serializer,
        protected CloudEventFactory      $cloudEvents,
        protected EventPipeline          $pipeline,
        protected Repository             $config
    )
    {
        $this->client = DaprClient::clientBuilder()->build();
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

        $metadata = $this->cloudEvents->make($event,$context->metadata());

        $contentType = $this->cloudEvents->getContentType();
        $topic = new Topic(
            $context->pubsubName(),
            $context->topic(),
            $this->client
        );

        $topic->publish(
            $context->payload(),
            $metadata,
            $contentType
        );


//        $this->client->publishEvent(
//            pubsubName: $c1ontext->pubsubName(),
//            topicName: $context->topic(),
//            data: [
//                'data' => $context->payload(),
//            ],
//            metadata: $metadata,
//            contentType: $contentType
//        );

        Log::info('Published event to Dapr.', [
            'event_class' => $event::class,
            'topic' => $context->topic(),
            'pubsub' => $context->pubsubName(),
            'metadata' => $metadata,
        ]);
    }
}
