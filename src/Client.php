<?php

namespace SmsTraffic;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;
use SmsTraffic\Exceptions\RequestException;
use SmsTraffic\Messages\ResponseFactory;

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

        return ResponseFactory::send($this->request($payload));
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

        return ResponseFactory::status($this->request($payload));
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

        return ResponseFactory::balance($this->request($payload));
    }

    /**
     * Request HTTP API.
     *
     * @param array $payload Request body
     *
     * @return string
     *
     * @throws \SmsTraffic\RequestException Invalid request
     */
    protected function request(array $payload)
    {
        $client = new GuzzleClient();

        try {
            $response = $client->request('POST', $this->url, [
                'form_params' => array_merge([
                    'login' => $this->login,
                    'password' => $this->password,
                ], $payload),
            ]);
        } catch (GuzzleException $e) {
            throw new RequestException($e->getMessage());
        }

        if (200 !== $response->getStatusCode()) {
            throw new RequestException('Response code is ' . $response->getStatusCode());
        }

        return $response->getBody()->getContents();
    }
}
