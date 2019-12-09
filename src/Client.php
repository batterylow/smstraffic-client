<?php

namespace SmsTraffic;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;
use SmsTraffic\Exceptions\RequestException;
use SmsTraffic\Messages\ResponseFactory;
use SimpleXMLElement;

/**
 * Client.
 */
class Client
{
    /**
     * Document version.
     */
    const VERSION = '1.9.0';

    /**
     * HTTP API url.
     */
    const URL = 'https://api.smstraffic.ru/multi.php';
    /**
     * HTTP API failover url.
     */
    const FAILOVER_URL = 'https://api2.smstraffic.ru/multi.php';

    /**
     * HTTP API result is ok.
     */
    const RESULT_OK = 'OK';
    /**
     * HTTP API result is error.
     */
    const RESULT_ERROR = 'ERROR';

    /**
     * Login.
     *
     * @var string
     */
    private $login;
    /**
     * Password.
     *
     * @var string
     */
    private $password;
    /**
     * HTTP API url.
     *
     * @var string
     */
    private $url;
    /**
     * HTTP API failover url.
     *
     * @var string
     */
    private $failoverUrl;

    /**
     * Send options.
     *
     * @var array
     */
    protected $options = [
        'rus' => 0,
        'flash' => 0,
        'start_date' => '',
        'max_parts' => 255,
        'gap' => 1,
        'group' => '',
        'timeout' => 86400,
        'individual_messages' => 0,
        'delimiter' => "\n",
        'want_sms_ids' => 0,
        'with_push_id' => 0,
        'ignore_phone_format' => 0,
        'two_byte_concat' => 0,
    ];

    /**
     * Send default options.
     *
     * @var array
     */
    protected $defaults = [
        'rus' => 5,
        'want_sms_ids' => 1,
    ];

    /**
     * Constructor.
     *
     * @param string      $login       Login
     * @param string      $password    Password
     * @param string|null $url         Url
     * @param string|null $failoverUrl Failover url
     */
    public function __construct(string $login, string $password, $url = null, $failoverUrl = null)
    {
        $this->login = $login;
        $this->password = $password;
        $this->url = $url ?? self::URL;
        $this->failoverUrl = $failoverUrl ?? self::FAILOVER_URL;
    }

    /**
     * Send SMS.
     *
     * @param string $from    Sender phone or name
     * @param string $to      Receiver phone number
     * @param string $message Message
     * @param array  $options Options
     *
     * @return \SmsTraffic\Response
     */
    public function send(string $from, string $to, string $message, array $options = [])
    {
        $payload = [
            'originator' => $from,
            'phones' => $to,
            'message' => $message,
        ];
        foreach ($this->defaults as $name => $value) {
            $payload[$name] = $value;
        }
        foreach ($options as $name => $value) {
            if (array_key_exists($name, $this->options)) {
                $payload[$name] = $value;
            }
        }

        $response = $this->request($payload);

        return ResponseFactory::send($response['raw'], $response['source']);
    }

    /**
     * Get message status.
     *
     * @param string $smsId Sms id
     *
     * @return \stdClass
     */
    public function status(string $smsId)
    {
        $payload = [
            'operation' => 'status',
            'sms_id' => $smsId,
        ];

        $response = $this->request($payload);

        return ResponseFactory::status($response['raw'], $response['source']);
    }

    /**
     * Get balance.
     *
     * @return \stdClass
     */
    public function balance()
    {
        $payload = [
            'operation' => 'account',
        ];

        $response = $this->request($payload);

        return ResponseFactory::balance($response['raw'], $response['source']);
    }

    /**
     * Request HTTP API.
     *
     * @param array $payload Request body
     *
     * @return array
     */
    protected function request(array $payload)
    {
        $client = new GuzzleClient();

        $params = [
            'form_params' => array_merge([
                'login' => $this->login,
                'password' => $this->password,
            ], $payload),
        ];

        $url = $this->url;
        $response = $this->getResponse($client, $url, $params);

        if (empty($response) || !$this->checkResult($response)) {
            $url = $this->failoverUrl;
            $response = $this->getResponse($client, $url, $params, true);
        }

        return [
            'raw' => $response,
            'source' => $url,
        ];
    }

    /**
     * Get response.
     *
     * @param GuzzleClient $client HTTP client
     * @param string       $url    HTTP API url
     * @param array        $params Request params
     * @param boolean      $isLast Last request flag
     *
     * @throws RequestException Invalid request
     */
    private function getResponse(GuzzleClient $client, string $url, array $params, bool $isLast = null)
    {
        try {
            $response = $client->request('POST', $url, $params);
        } catch (GuzzleException $e) {
            if ($isLast) {
                throw new RequestException($e->getMessage());
            }

            return null;
        }

        if (200 !== $response->getStatusCode()) {
            if ($isLast) {
                throw new RequestException('Response code is ' . $response->getStatusCode());
            }

            return null;
        }

        return $response->getBody()->getContents();
    }

    /**
     * Check response result.
     *
     * @param string $xml Raw xml response
     *
     * @return boolean
     */
    private function checkResult(string $xml)
    {
        $response = new SimpleXMLElement($xml);

        if (empty($response->result)) {
            return false;
        }
        $result = (string) $response->result;

        return self::RESULT_OK === $result;
    }
}
