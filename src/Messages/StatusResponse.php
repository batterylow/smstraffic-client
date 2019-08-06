<?php

namespace SmsTraffic\Messages;

/**
 * Status response.
 */
class StatusResponse extends Response
{
    /**
     * Cast response to string.
     *
     * @return string
     */
    public function __toString()
    {
        $data = [
            'submition_date' => $this->contents->submition_date,
            'send_date' => $this->contents->send_date,
            'last_status_change_date' => $this->contents->last_status_change_date,
            'status' => $this->contents->status,
            'error' => $this->contents->error,
            'sms_id' => $this->contents->sms_id,
        ];

        return json_encode($data);
    }
}
