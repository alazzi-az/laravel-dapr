<?php

namespace AlazziAz\LaravelDapr\Http\Controllers;


use AlazziAz\LaravelDapr\Contracts\DaprHealthCheckerInterface;
use AlazziAz\LaravelDapr\Support\HealthCheckerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
class HealthController
{
    public function __invoke(Request $request): JsonResponse|Response
    {
        // Optional custom checker class
        $checker = config('dapr.health.checker',HealthCheckerInterface::class);
        $ok = true;

        if ( class_exists($checker) && is_subclass_of($checker, DaprHealthCheckerInterface::class)) {
            $service = app($checker);
            $ok = (bool) $service->isHealthy();
        }

        if (config('dapr.health.response', 'empty') === 'json') {
            return response()->json(['status' => $ok ? 'ok' : 'fail'], $ok ? 200 : 500);
        }

        return response('', $ok ? 200 : 500);
    }
}