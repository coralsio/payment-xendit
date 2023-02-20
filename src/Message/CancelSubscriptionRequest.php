<?php

namespace Corals\Modules\Payment\Xendit\Message;


use Xendit\{
    Recurring, Xendit
};


/**
 * Authorize Request
 *
 * @method SubscriptionResponse send()
 */
class CancelSubscriptionRequest extends AbstractRequest
{
    public function getData()
    {
        return $this->getSubscriptionCancellationData();
    }


    /**
     * @param mixed $data
     * @return SubscriptionResponse
     * @throws \Xendit\Exceptions\ApiException
     */
    public function sendData($data)
    {
        Xendit::setApiKey($this->getSecretKey());

        $response = Recurring::stop($data['subscriptionId']);

        return new SubscriptionResponse($this, $response);
    }
}
