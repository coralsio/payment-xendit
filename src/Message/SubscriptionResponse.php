<?php

namespace Corals\Modules\Payment\Xendit\Message;

/**
 * SubscriptionResponse
 */
class SubscriptionResponse extends Response
{
    /**
     * @return string
     */
    public function getRedirectPaymentURL(): string
    {
        return data_get($this->data, 'last_created_invoice_url');
    }

    /**
     * @return string
     */
    public function getSubscriptionReference()
    {
        if ($this->isSuccessful()) {
            $subscriptionId = $this->getSubscriptionId();

            if (!empty($subscriptionId)) {
                return $subscriptionId;
            }
        }

        return false;
    }

    public function getSubscriptionId()
    {
        if ($this->isSuccessful()) {
            return data_get($this->data, 'id');
        }

        return null;
    }


}
