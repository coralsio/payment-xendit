<?php

namespace Corals\Modules\Payment\Xendit;


use Corals\Modules\Payment\Xendit\Exception\XenditWebhookFailed;
use Corals\Modules\Payment\Common\AbstractGateway;
use Corals\Modules\Payment\Common\Models\WebhookCall;
use Corals\Modules\Payment\Payment;
use Corals\Modules\Subscriptions\Models\Plan;
use Corals\Modules\Subscriptions\Models\Subscription;
use Corals\Settings\Facades\Settings;
use Corals\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Authorize.Net AIM Class
 */
class Gateway extends AbstractGateway
{
    public function getName()
    {
        return 'Xendit';
    }

    public function getDefaultParameters()
    {
        return [
            'secretKey' => '',
            'publicKey' => ''
        ];
    }

    public function setAuthentication()
    {

        $sandbox = \Settings::get('payment_xendit_sandbox_mode', 'true');

        if ($sandbox == 'true') {
            $secretKey = \Settings::get('payment_xendit_sandbox_secret_key');
            $publicKey = \Settings::get('payment_xendit_sandbox_public_key');
        } else {
            $secretKey = \Settings::get('payment_xendit_live_secret_key');
            $publicKey = \Settings::get('payment_xendit_live_public_key');

        }

        $this->setSecretKey($secretKey);
        $this->setPublicKey($publicKey);

    }

    public function getPaymentViewName($type = null)
    {
        if ($type == "subscription") {
            return "Xendit::subscription-checkout";
        }
    }


    public function setHashSecret($value)
    {
        return $this->setParameter('hashSecret', $value);
    }


    public function setSecretKey($key)
    {
        return $this->setParameter('secretKey', $key);
    }

    public function getSecretKey()
    {
        return $this->getParameter('secretKey');
    }

    public function setPublicKey($key)
    {
        return $this->setParameter('publicKey', $key);
    }

    public function getPublicKey()
    {
        return $this->getParameter('publicKey');
    }

    public function getHashSecret()
    {
        return $this->getParameter('hashSecret');
    }

    /**
     * @param array $parameters
     * @return Message\SubscriptionResponse
     */
    public function createSubscription(array $parameters = array())
    {
        return $this->createRequest('\Corals\Modules\Payment\Xendit\Message\CreateSubscriptionRequest', $parameters);
    }

    /**
     * @param array $parameters
     * @return Message\Response
     */
    public function cancelSubscription(array $parameters = array())
    {
        return $this->createRequest('\Corals\Modules\Payment\Xendit\Message\CancelSubscriptionRequest', $parameters);
    }


    function userRequirePayment(User $user)
    {
        if (is_null($user->integration_id)) {
            return true;

        }
        return false;
    }

    public function supportCards(): bool
    {
        return Settings::get('payment_xendit_support_cards') == 'true';
    }

    /**
     * @param Plan $plan
     * @param User $user
     * @param Subscription|null $subscription
     * @param null $subscription_data
     * @return array
     */
    public function prepareSubscriptionParameters(Plan $plan, User $user, Subscription $subscription = null, $subscription_data = null)
    {

        $parameters['subscriptionData']['name'] = $plan->name;
        $parameters['subscriptionData']['amount'] = \Currency::getAmountISOCurrency(\Currency::convert($plan->price, 'USD', 'IDR', false), 'IDR');
        $parameters['subscriptionData']['external_id'] = sprintf('user_%s', $user->id);
        $parameters['subscriptionData']['payer_email'] = $user->email;


        session()->put('subscription_identifier_code', Str::uuid()->toString());

        $parameters['subscriptionData']['checkoutToken'] = session()->get('checkoutToken');
        $parameters['subscriptionData']['description'] = data_get($subscription_data, 'notes');

        switch ($plan->bill_cycle) {
            case 'day':
                $recurring_unit = 'day';
                $intervalCount = $plan->bill_frequency;
                break;

            case 'month':
                $recurring_unit = 'month';
                $intervalCount = $plan->bill_frequency;
                break;

            case 'year':
                $recurring_unit = 'month';
                $intervalCount = $plan->bill_frequency * 12;
                break;

            default:
                $recurring_unit = $plan->bill_cycle;
                $intervalCount = $plan->bill_frequency;
        }


        $parameters['subscriptionData']['interval'] = strtoupper($recurring_unit);
        $parameters['subscriptionData']['interval_count'] = $intervalCount;

        $parameters['subscriptionData']['customer'] = [
            'given_names' => $user->full_name,
            'address' => data_get($subscription_data, 'billing_address', data_get($subscription, 'billing_address')),
            'email' => $user->email
        ];

        session()->forget('checkoutToken');

        return $parameters;
    }

    /**
     * @param User $user
     * @param Subscription $current_subscription
     * @return array
     */
    public function prepareSubscriptionCancellationParameters(User $user, Subscription $current_subscription)
    {
        $parameters['SubscriptionCancellationData'] = [
            'subscriptionId' => $current_subscription->subscription_reference,
        ];

        return $parameters;
    }

    public static function webhookHandler(Request $request)
    {
        try {
            $webhookCall = null;


            $eventPayload = $request->getContent();

            if (!static::validate($request->header('x-callback-token'))) {
                throw XenditWebhookFailed::invalidSignature($request->header('x-callback-token'));
            }

            $eventPayload = json_decode($eventPayload, true);

            $event = data_get($eventPayload, 'event');

            if (!$event) {
                $event = 'invoice.paid';
            }

            $data = [
                'event_name' => 'xendit.' . $event,
                'payload' => $eventPayload,
                'gateway' => 'Xendit'
            ];
            $webhookCall = WebhookCall::create($data);

            $webhookCall->process();
            die();
        } catch (\Exception $exception) {
            if ($webhookCall) {
                $webhookCall->saveException($exception);
            }
            log_exception($exception, 'Webhooks', 'xendit');
        }
    }

    /**
     * @param string $callbackVerificationToken
     * @return bool
     */
    public static function validate(string $callbackVerificationToken): bool
    {
        return \Settings::get('payment_xendit_callback_verification_code') == $callbackVerificationToken;
    }

}
