<?php

namespace Corals\Modules\Payment\Xendit\Providers;

use Carbon\Carbon;
use Corals\Foundation\Providers\BaseInstallModuleServiceProvider;

class InstallModuleServiceProvider extends BaseInstallModuleServiceProvider
{
    protected function providerBooted()
    {
        $supported_gateways = \Payments::getAvailableGateways();

        $supported_gateways['Xendit'] = 'Xendit';

        \Payments::setAvailableGateways($supported_gateways);

        \DB::table('settings')->insert([
            [
                'code' => 'payment_xendit_live_secret_key',
                'type' => 'TEXT',
                'category' => 'Payment',
                'label' => 'payment_xendit_live_secret_key',
                'value' => 'live_merchant_xxxxxxxxxxxxxxxxxxxxxx',
                'editable' => 1,
                'hidden' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'payment_xendit_live_public_key',
                'type' => 'TEXT',
                'category' => 'Payment',
                'label' => 'payment_xendit_live_public_key',
                'value' => 'live_merchant_xxxxxxxxxxxxxxxxxxxxxx',
                'editable' => 1,
                'hidden' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'payment_xendit_sandbox_secret_key',
                'type' => 'TEXT',
                'category' => 'Payment',
                'label' => 'payment_xendit_sandbox_secret_key',
                'value' => 'xnd_development_2i9ACnEGHyHn3csjKqe7djcmzr1D2KeQxDeNRkUpcxRAJRVtSbU1ULNU764mVrw',
                'editable' => 1,
                'hidden' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'payment_xendit_sandbox_public_key',
                'type' => 'TEXT',
                'category' => 'Payment',
                'label' => 'payment_xendit_sandbox_public_key',
                'value' => 'xnd_public_development_wsr67J7uTfRdaJxQK4JGfYdBsGvqwznC2qy75iIBbhbylAHXlDwMnR1fHIXm7A',
                'editable' => 1,
                'hidden' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'payment_xendit_sandbox_mode',
                'type' => 'TEXT',
                'category' => 'Payment',
                'label' => 'payment_xendit_sandbox_mode',
                'value' => 'true',
                'editable' => 1,
                'hidden' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'code' => 'payment_xendit_callback_verification_code',
                'type' => 'TEXT',
                'category' => 'Payment',
                'label' => 'payment_xendit_callback_verification_code',
                'value' => 'rsXO7uaf7BBsNvtaEP0gSIuu0BF26QF5mCtfhelgA47WV426',
                'editable' => 1,
                'hidden' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}
