<?php

namespace Corals\Modules\Payment\Xendit\Job;


use Braintree\Exception;
use Corals\Modules\Payment\Common\Models\Invoice;
use Corals\Modules\Payment\Common\Models\WebhookCall;
use Corals\Modules\Payment\Stripe\Exception\StripeWebhookFailed;
use Corals\Modules\Subscriptions\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HandleInvoicePaid implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \Corals\Modules\Payment\Common\Models\WebhookCall
     */
    public $webhookCall;

    /**
     * HandleInvoiceCreated constructor.
     * @param WebhookCall $webhookCall
     */
    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    public function handle()
    {
        logger('Xendit Invoice Paid');

        try {
            if ($this->webhookCall->processed) {
                throw StripeWebhookFailed::processedCall($this->webhookCall);
            }

            $payload = $this->webhookCall->payload;

            $subscriptionReference = data_get($payload, 'recurring_payment_id');

            $subscription = Subscription::query()
                ->where('subscription_reference', $subscriptionReference)
                ->first();

            if (!$subscription) {
                throw  new Exception(sprintf("Couldn't find subscription with reference : %s", $subscriptionReference));
            }

            $user = $subscription->user;

            if (data_get($payload, 'status', 'PAID')) {
                $invoice = Invoice::create([
                    'code' => $payload['id'],
                    'currency' => $payload['currency'],
                    'description' => $payload['description'],
                    'sub_total' => ($payload['paid_amount'] / 100),
                    'total' => ($payload['paid_amount'] / 100),
                    'user_id' => $user->id,
                    'invoicable_id' => $subscription->id,
                    'invoicable_type' => Subscription::class,
                    'due_date' => data_get($payload, 'paid_at'),
                    'invoice_date' => now(),
                ]);

                $invoice->markAsPaid();
                $subscription->setStatus('active');
            }


            $this->webhookCall->markAsProcessed();
        } catch (\Exception $exception) {
            log_exception($exception);
            $this->webhookCall->saveException($exception);
            throw $exception;
        }
    }
}
