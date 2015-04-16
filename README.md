# enom-api
PHP Enom API


```php
use DigitalCanvas\Enom\Enom;
use GuzzleHttp\Client;

$client = new GuzzleHttp\Client();
$enom = Enom::factory('WebsiteBuilder', $uid, $pwd, $client);

$response = $enom->createAccount();
```
