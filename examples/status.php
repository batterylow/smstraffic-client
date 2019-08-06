<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SmsTraffic\Client;
use SmsTraffic\Exceptions\SmsTrafficException;

$testUrl = 'http://localhost:8080/test_endpoint.php';

$login = 'test_login';
$password = 'test_passport';

$smsId = '999999999999999999';

$client = new Client($login, $password, $testUrl);

try {
    $response = $client->status($smsId);
} catch (SmsTrafficException $e) {
    echo 'Status error: ' . $e->getMessage();
    exit(1);
}

echo 'Status for message ' . $response->sms_id . PHP_EOL
    . '  submition date: ' . $response->submition_date . PHP_EOL
    . '  send_date: ' . $response->send_date . PHP_EOL
    . '  last_status_change_date: ' . $response->last_status_change_date . PHP_EOL
    . '  status: ' . $response->status . PHP_EOL
    . '  error: ' . $response->error . PHP_EOL;

exit(0);
