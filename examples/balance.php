<?php

require_once __DIR__ . '/../vendor/autoload.php';

use SmsTraffic\Client;
use SmsTraffic\Exceptions\SmsTrafficException;

$testUrl = 'http://localhost:8080/test_endpoint.php';

$login = 'test_login';
$password = 'test_passport';

$client = new Client($login, $password, $testUrl);

try {
    $response = $client->balance();
} catch (SmsTrafficException $e) {
    echo 'Status error: ' . $e->getMessage();
    exit(1);
}

echo 'Balance: ' . $response->getContents()->account . PHP_EOL;

exit(0);
