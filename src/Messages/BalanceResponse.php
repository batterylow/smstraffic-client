<?php

namespace SmsTraffic\Messages;

/**
 * Balance response.
 */
class BalanceResponse extends Response
{
    /**
     * Cast response to string.
     *
     * @return string
     */
    public function __toString()
    {
        $data = [
            'account' => $this->contents->account,
        ];

        return json_encode($data);
    }
}
