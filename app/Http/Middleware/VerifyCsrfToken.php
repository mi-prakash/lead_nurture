<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/home/webhook_new_appointment/*',
        '/home/webhook_rescheduled',
        '/home/webhook_canceled',
        '/home/webhook_changed',
        '/home/webhook_complete',
        '/home/messages/pre_event',
        '/home/messages/get_sms/*',
        '/home/clickfunnel_webhook',
        '/home/funnel_webhooks/test/get_lead/*',
    ];
}
