<?php

return [

    'request_not_contain_header' => 'The request did not contain a header named `Xendit-Signature`.',
    'signature_found_header_name' => 'The signature :name found in the header named `Xendit-Signature` is invalid. Make sure that the `services.Xendit.webhook_signing_secret` 
                                      config key is set to the value you found on the Xendit dashboard. If you are caching your config try running `php artisan clear:cache` to resolve the problem.',

    'authorize_webhook_sing_secret' => 'The Xendit webhook signing secret is not set. Make sure that the `Xendit.settings` configured as required.',
    'invalid_authorize_payload' => 'Invalid Xendit Payload. Please check WebhookCall: :arg',
    'invalid_authorize_invoice_code' => 'Invalid Xendit Invoice Code. Please check WebhookCall: :arg',
    'invalid_authorize_subscription_Reference' => 'Invalid Xendit Subscription Reference. Please check WebhookCall: :arg',
    'invalid_authorize_customer' => 'Invalid Xendit Customer. Please check WebhookCall: :arg',
    'invalid_request_exception_specify_amount' => 'Please specify amount as a string or float, with decimal places (e.g.10.00 to represent $10.00)',
];