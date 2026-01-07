<?php

namespace AlazziAz\LaravelDapr\Concerns;

use AlazziAz\LaravelDapr\Dtos\CloudEventData;

trait InteractsWithCloudData
{
    private CloudEventData $cloudData;

    public function cloudData(): CloudEventData
    {
        return $this->cloudData;
    }

    /** @internal package will call this */
    public function setCloudData(CloudEventData $cloudData): void
    {
        $this->cloudData = $cloudData;
    }

}
