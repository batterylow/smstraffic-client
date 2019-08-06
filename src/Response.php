<?php

namespace SmsTraffic;

use SimpleXMLElement;
use stdClass;

/**
 * Client.
 */
class Response
{
    /**
     * XML response.
     *
     * @var string
     */
    private $xml;
    /**
     * Response data.
     *
     * @var \stdClass
     */
    private $contents;

    /**
     * Constructor.
     *
     * @param string    $xml      Raw HTTP API response
     * @param \stdClass $contents Response data
     */
    private function __construct(string $xml, stdClass $contents)
    {
        $this->xml = $xml;
        $this->contents = $contents;
    }

    /**
     * Get response data.
     *
     * @return array
     */
    public function getContents()
    {
        return $this->contents;

        if (null === $this->contents) {
            $reply = new SimpleXMLElement($this->xml);
            $this->contents = json_decode(json_encode($reply), true);

            if (isset($reply->message_infos)) {
                $this->contents->message_infos = [];
                foreach ($reply->message_infos->message_info as $messageInfo) {
                    $info = new stdClass();
                    $info->phone = (string) $messageInfo->phone;
                    $info->sms_id = (string) $messageInfo->sms_id;
                    $this->contents->message_infos[] = $info;
                }
            }
        }

        return $this->contents;
    }

    /**
     * Create new response for send request.
     *
     * @param string $xml Raw HTTP API send response
     *
     * @return \SmsTraffic\Response Response
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

        return new self($xml, $contents);
    }

    /**
     * Create new response for status request.
     *
     * @param string $xml Raw HTTP API status response
     *
     * @return \SmsTraffic\Response Response
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

        return new self($xml, $contents);
    }

    /**
     * Create new response for balance request.
     *
     * @param string $xml Raw HTTP API balance response
     *
     * @return \SmsTraffic\Response Response
     */
    public static function balance(string $xml)
    {
        $reply = new SimpleXMLElement($xml);

        $contents = new stdClass();
        $contents->account = (string) $reply->account;

        return new self($xml, $contents);
    }
}
