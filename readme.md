# A PHP wrapper for [SmsTraffic's](https://www.smstraffic.ru) HTTP API.

## Usage

See `examples/`.

## Test endpoint

You can run the test endpoint to test your application instead of using real
SmsTraffic HTTP API.

```
php -S localhost:8080 -t examples/
```

Then use `http://localhost:8080/test_endpoint.php` as SmsTraffic HTTP API url.

All `send` incoming messages will be logged to `examples/messages.log` file.
