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

foreach ($apiClient->getResultGenerator('order') as $order) {
    var_dump($order);
}
```

## More examples
Review the examples in the examples/ folder.

## Support
Need support implementing the Picqer API? Feel free to [contact us](https://picqer.com/contact)
