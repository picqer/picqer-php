Picqer PHP API Client
==========

This project is a PHP Library to use the Picqer API from your PHP application.

Full documentation for the API can be found on [picqer.com/en/api](https://picqer.com/en/api)

## Installation
This project can easily be installed through Composer.

```
composer require picqer/api-client
```

## Example: Get orders
```php
<?php

require __DIR__ . '/vendor/autoload.php';

$subDomain = 'jansens-webwinkels';
$apiKey = '1023ihs0edfh';

$apiClient = new Picqer\Api\Client($subDomain, $apiKey);
$apiClient->enableRetryOnRateLimitHit();
$apiClient->setUseragent('My amazing app (dev@example.org)');

$orders = $apiClient->getOrders();
var_dump($orders);
```

## Example: Results generator
If you want to loop trough all your products or orders, you can use the results generator. This will give you the results in a loop as soon as the API returns them. This will also help with memory usage.

```php
<?php

require __DIR__ . '/vendor/autoload.php';

$subDomain = 'jansens-webwinkels';
$apiKey = '1023ihs0edfh';

$apiClient = new Picqer\Api\Client($subDomain, $apiKey);
$apiClient->enableRetryOnRateLimitHit();
$apiClient->setUseragent('My amazing app (dev@example.org)');

foreach ($apiClient->getResultGenerator('order') as $order) {
    var_dump($order);
}
```

## More examples
Review the examples in the examples/ folder.

## Helpful methods
### setUseragent()
It is helpful for us if you set the user agent with the name of the application or developer that build the application. Then we can contact you if we see weird behaviour.

### sendRequest()
This is the main method of the client that sends the API request. If there are new API endpoints that are not yet implemented in this client with dedicated methods, you can create the request yourself with sendRequest().

### getResultGenerator()
This is a (generator)[http://php.net/manual/en/language.generators.syntax.php] for all listing methods like getOrders and getProducts. This will help reduce the memory usage of your application when looping through a lot of orders or products.

### enableDebugmode()
This flag gives you a lot of debug information about the request that the client send and the raw response it got as a result.

## Rate limits
Please keep in mind the rate limit of the Picqer API. In the result array you get a 'rate-limit-remaining' key with the remaining requests you can do in this minute. Try not to make any more requests if this is 0 if you can.

When you try another request, the request will fail. This client will throw you a `RateLimitException`. You can catch those and try your request again later.

We also have an option `$apiClient->enableRetryOnRateLimitHit()` you can use to enable retry's of requests when you hit a rate limit. When the client hits the rate limit, it will sleep for 20 seconds and try the same request again. 


## Support
Need support implementing the Picqer API? Feel free to [contact us](https://picqer.com/contact)
