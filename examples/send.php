<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SmsTraffic\Client;
use SmsTraffic\Exceptions\SmsTrafficException;

$testUrl = 'http://localhost:8080/test_endpoint.php';

$login = 'test_login';
$password = 'test_passport';
$originator = 'TESTSENDER';

$phone = '79999999999';
// rand() prevent from ban while testing, see https://lk.smstraffic.ru/faq
$message = "This is a test message.\n" . rand(1000, 9999);

$client = new Client($login, $password, $testUrl);

try {
    $response = $client->send($originator, $phone, $message);
} catch (SmsTrafficException $e) {
    echo 'Send error: ' . $e->getMessage();
    exit(1);
}

if (Client::RESULT_OK === $response->result) {
    echo 'Sended.' . PHP_EOL
       . 'SMS id: ' . $response->message_infos[0]->sms_id . PHP_EOL;
}

exit(0);
