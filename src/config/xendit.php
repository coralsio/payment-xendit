<?php

return [
    'name' => 'Xendit',
    'key' => 'payment_xendit',
    'support_subscription' => true,
    'support_ecommerce' => false,
    'manage_remote_plan' => false,
    'support_marketplace' => false,
    'manage_remote_product' => false,
    'manage_remote_sku' => false,
    'manage_remote_order' => false,
    'supports_swap' => false,
    'supports_swap_in_grace_period' => false,
    'require_invoice_creation' => false,
    'require_plan_activation' => false,
    'capture_payment_method' => false,
    'require_default_payment_set' => false,
    'can_update_payment' => false,
    'create_remote_customer' => false,
    'require_payment_token' => false,
    'default_subscription_status' => 'pending',
    'settings' => [
        'live_secret_key' => [
            'label' => 'Xendit::labels.settings.live_secret_key',
            'type' => 'text',
            'required' => false,
        ],
        'live_public_key' => [
            'label' => 'Xendit::labels.settings.live_public_key',
            'type' => 'text',
            'required' => false,
        ],

        'sandbox_mode' => [
            'label' => 'Xendit::labels.settings.sandbox_mode',
            'type' => 'boolean'
        ],
        'support_cards' => [
            'label' => 'Xendit::labels.settings.support_cards',
            'type' => 'boolean'
        ],
        'sandbox_secret_key' => [
            'label' => 'Xendit::labels.settings.sandbox_secret_key',
            'type' => 'text',
            'required' => false,
        ],
        'sandbox_public_key' => [
            'label' => 'Xendit::labels.settings.sandbox_public_key',
            'type' => 'text',
            'required' => false,
        ],
        'callback_verification_code' => [
            'label' => 'Xendit::labels.settings.callback_verification_code',
            'type' => 'text',
            'required' => false,
        ]
    ],
    'events' => [
        'invoice.paid' => \Corals\Modules\Payment\Xendit\Job\HandleInvoicePaid::class
    ],
    'webhook_handler' => \Corals\Modules\Payment\Xendit\Gateway::class,
];
