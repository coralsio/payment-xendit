<?php

namespace Corals\Modules\Payment\Xendit\Providers;

use Corals\Foundation\Providers\BaseUpdateModuleServiceProvider;

class UpdateModuleServiceProvider extends BaseUpdateModuleServiceProvider
{
    protected $module_code = 'corals-payment-xendit';
    protected $batches_path = __DIR__ . '/../update-batches/*.php';
}
