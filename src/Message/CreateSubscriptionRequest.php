<?php

namespace Corals\Modules\Payment\Xendit\Message;


use Illuminate\Support\Facades\URL;
use Xendit\{
    Recurring, Xendit
};


/**
 * Authorize Request
 *
 * @method SubscriptionResponse send()
 */
class CreateSubscriptionRequest extends AbstractRequest
{
    public function getData()
    {
        return $this->getSubscriptionData();
    }


    /**
     * Send the request with specified data
     *
     * @param mixed $data The data to send
     * @return SubscriptionResponse
     */
    public function sendData($data)
    {

        Xendit::setApiKey($this->getSecretKey());

        $subscriptionIdentifierCode = session()->get('subscription_identifier_code');

        $params = [
            'external_id' => $data['external_id'],
            'payer_email' => $data['payer_email'],
            'description' => $data['description'] ?? $data['name'],
            'amount' => $data['amount'],
            'interval' => $data['interval'],
            'interval_count' => $data['interval_count'],
            'success_redirect_url' => URL::signedRoute('subscription.successPayment', [
                $subscriptionIdentifierCode
            ]),
            'failure_redirect_url' => URL::signedRoute('subscription.failedPayment', [
                $subscriptionIdentifierCode
            ]),
//            'invoice_duration' => 60,
            'customer' => $data['customer'],
            'recharge' => true
        ];

        if (data_get($data, 'checkoutToken')) {
            $params['checkoutToken'] = data_get($data, 'checkoutToken');
            $params['charge_immediately'] = true;
        }

        $response = Recurring::create($params);

        return new SubscriptionResponse($this, $response);
    }
}
