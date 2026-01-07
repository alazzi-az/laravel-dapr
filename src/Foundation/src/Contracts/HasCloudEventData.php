<?php

namespace AlazziAz\LaravelDapr\Contracts;

use AlazziAz\LaravelDapr\Dtos\CloudEventData;

interface  HasCloudEventData
{
    public function cloudData(): CloudEventData;
}