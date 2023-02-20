<?php

namespace Corals\Modules\Payment\Xendit\Message;

use Corals\Modules\Payment\Common\Message\AbstractResponse;

/**
 * Response
 */
class Response extends AbstractResponse
{
    /**
     * The overall transaction result code.
     */
    const TRANSACTION_RESULT_CODE_APPROVED = 1;
    const TRANSACTION_RESULT_CODE_DECLINED = 2;
    const TRANSACTION_RESULT_CODE_ERROR = 3;
    const TRANSACTION_RESULT_CODE_REVIEW = 4;

    public function isSuccessful()
    {
        return $this->getResultCode() === static::TRANSACTION_RESULT_CODE_APPROVED;
    }

    /**
     * Overall status of the transaction. This field is also known as "Response Code" in Authorize.NET terminology.
     *
     * @return int 1 = Approved, 2 = Declined, 3 = Error, 4 = Held for Review
     */
    public function getResultCode()
    {

        $result = isset($this->data['errors']) ? 'Error' : 'Ok';

        switch ($result) {
            case 'Ok':
                return static::TRANSACTION_RESULT_CODE_APPROVED;
            case 'Error':
                return static::TRANSACTION_RESULT_CODE_ERROR;
            default:
                return null;

        }
    }

    /**
     * Text description of the status.
     *
     * @return string|null
     */
    public function getMessage()
    {
        return data_get($this->data, 'message');
    }

    /**
     * @return string
     */
    public function getCurrentPeriodEndReference()
    {
        return \Carbon\Carbon::now()->timestamp;

    }


}
