<?php

namespace SmsTraffic\Messages;

interface ResponseInterface
{
    /**
     * Get response data.
     *
     * @return array
     */
    public function getContents();

    /**
     * Cast response to string.
     *
     * @return string
     */
    public function __toString();
}
