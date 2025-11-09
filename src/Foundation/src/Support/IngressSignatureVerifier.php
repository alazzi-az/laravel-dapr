<?php

namespace AlazziAz\LaravelDapr\Support;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

class IngressSignatureVerifier
{
    public function __construct(
        protected Repository $config
    ) {
    }

    public function verify(Request $request): bool
    {
        if (! $this->config->get('dapr.http.verify_signature', false)) {
            return true;
        }

        $secret = $this->config->get('dapr.http.signature_secret');
        if (! $secret) {
            return false;
        }

        $headerName = $this->config->get('dapr.http.signature_header', 'x-dapr-signature');
        $provided = $request->headers->get($headerName);

        if (! $provided) {
            return false;
        }

        $expected = hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $provided);
    }
}
