<?php

namespace Corals\Modules\Payment\Xendit\Exception;

use Corals\Modules\Payment\Common\Exception\WebhookFailed;
use Corals\Modules\Payment\Common\Models\WebhookCall;

class XenditWebhookFailed extends WebhookFailed
{
    public static function missingSignature()
    {
        return new static(trans('Xendit::exception.request_not_contain_header'));
    }

    public static function invalidSignature($signature)
    {
        return new static(trans('Xendit::exception.signature_found_header_name', ['name' => $signature]));
    }

    public static function signingSecretNotSet()
    {
        return new static(trans('Xendit::exception.authorize_webhook_sing_secret'));
    }

    public static function invalidXenditPayload(WebhookCall $webhookCall)
    {
        return new static(trans('Xendit::exception.invalid_authorize_payload', ['arg' => $webhookCall->id]));
    }

    public static function invalidXenditInvoice(WebhookCall $webhookCall)
    {
        return new static(trans('Xendit::exception.invalid_authorize_invoice_code', ['arg' => $webhookCall->id]));
    }

    public static function invalidXenditSubscription(WebhookCall $webhookCall)
    {
        return new static(trans('Xendit::exception.invalid_authorize_subscription_Reference', ['arg' => $webhookCall->id]));
    }

    public static function invalidXenditCustomer(WebhookCall $webhookCall)
    {
        return new static(trans('Xendit::exception.invalid_authorize_customer', ['arg' => $webhookCall->id]));
    }
}
