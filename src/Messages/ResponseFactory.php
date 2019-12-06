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
     * @param string $xml    Raw HTTP API send response
     * @param string $source Response source url
     *
     * @return \SmsTraffic\Messages\SendResponse Response
     */
    public static function send(string $xml, string $source)
    {
        $reply = new SimpleXMLElement($xml);

        $contents = new stdClass();
        $contents->result = (string) $reply->result;
        $contents->code = (string) $reply->code;
        $contents->description = (string) $reply->description;
        $contents->source = $source;
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
     * @param string $xml    Raw HTTP API status response
     * @param string $source Response source url
     *
     * @return \SmsTraffic\Messages\StatusResponse Response
     */
    public static function status(string $xml, string $source)
    {
        $reply = new SimpleXMLElement($xml);

        $contents = new stdClass();
        $contents->submition_date = (string) $reply->submition_date;
        $contents->send_date = (string) $reply->send_date;
        $contents->last_status_change_date = (string) $reply->last_status_change_date;
        $contents->status = (string) $reply->status;
        $contents->error = (string) $reply->error;
        $contents->sms_id = (string) $reply->sms_id;
        $contents->source = $source;

        return new StatusResponse($xml, $contents);
    }

    /**
     * Create new response for balance request.
     *
     * @param string $xml    Raw HTTP API balance response
     * @param string $source Response source url
     *
     * @return \SmsTraffic\Messages\BalanceResponse Response
     */
    public static function balance(string $xml, string $source)
    {
        $reply = new SimpleXMLElement($xml);

        $contents = new stdClass();
        $contents->account = (string) $reply->account;
        $contents->source = $source;

        return new BalanceResponse($xml, $contents);
    }
}
