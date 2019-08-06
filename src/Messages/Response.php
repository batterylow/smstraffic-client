<?php

namespace SmsTraffic\Messages;

use stdClass;

/**
 * Response.
 */
abstract class Response implements ResponseInterface
{
    /**
     * XML response.
     *
     * @var string
     */
    protected $xml;
    /**
     * Response data.
     *
     * @var \stdClass
     */
    protected $contents;

    /**
     * Constructor.
     *
     * @param string    $xml      Raw HTTP API response
     * @param \stdClass $contents Response data
     */
    public function __construct(string $xml, stdClass $contents)
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
    }
}
