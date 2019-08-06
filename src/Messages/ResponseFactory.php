<?php

namespace SmsTraffic\Messages;

use SimpleXMLElement;
use stdClass;

/**
 * ResponseFactory.
 */
abstract class ResponseFactory
{
    /**
     * Create new response for send request.
     *
     * @param string $xml Raw HTTP API send response
     *
     * @return \SmsTraffic\Messages\SendResponse Response
     */
    public static function send(string $xml)
    {
        $reply = new SimpleXMLElement($xml);

        $contents = new stdClass();
        $contents->result = (string) $reply->result;
        $contents->code = (string) $reply->code;
        $contents->description = (string) $reply->description;
        if (isset($reply->message_infos)) {
            $contents->message_infos = [];
            foreach ($reply->message_infos->message_info as $messageInfo) {
                $info = new stdClass();
                $info->phone = (string) $messageInfo->phone;
                $info->sms_id = (string) $messageInfo->sms_id;
                $contents->message_infos[] = $info;
            }
        }

        return new SendResponse($xml, $contents);
    }

    /**
     * Create new response for status request.
     *
     * @param string $xml Raw HTTP API status response
     *
     * @return \SmsTraffic\Messages\StatusResponse Response
     */
    public static function status(string $xml)
    {
        $reply = new SimpleXMLElement($xml);

        $contents = new stdClass();
        $contents->submition_date = (string) $reply->submition_date;
        $contents->send_date = (string) $reply->send_date;
        $contents->last_status_change_date = (string) $reply->last_status_change_date;
        $contents->status = (string) $reply->status;
        $contents->error = (string) $reply->error;
        $contents->sms_id = (string) $reply->sms_id;

        return new StatusResponse($xml, $contents);
    }

    /**
     * Create new response for balance request.
     *
     * @param string $xml Raw HTTP API balance response
     *
     * @return \SmsTraffic\Messages\BalanceResponse Response
     */
    public static function balance(string $xml)
    {
        $reply = new SimpleXMLElement($xml);

        $contents = new stdClass();
        $contents->account = (string) $reply->account;

        return new BalanceResponse($xml, $contents);
    }
}
