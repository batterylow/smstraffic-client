<?php

namespace SmsTraffic\Messages;

/**
 * Send response.
 */
class SendResponse extends Response
{
    /**
     * Cast response to string.
     *
     * @return string
     */
    public function __toString()
    {
        $data = [
            'result' => $this->contents->result,
            'code' => $this->contents->code,
            'description' => $this->contents->description,
        ];
        if (isset($this->contents->message_infos)) {
            $data['message_infos'] = [];
            foreach ($this->contents->message_infos as $messageInfo) {
                $data['message_infos'][] = [
                    'phone' => $messageInfo->phone,
                    'sms_id' => $messageInfo->sms_id,
                ];
            }
        }

        return json_encode($data);
    }
}
