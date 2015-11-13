Picqer PHP API Client
==========

This project is a PHP Library to use the Picqer API from your PHP application.

Information about our API can be found on [picqer.com/en/api](https://picqer.com/en/api)

## Installation
This project can easily be installed through Composer.

```
composer require picqer/api-client
```

## Example: Get orders
```php
<?php

require __DIR__ . '/vendor/autoload.php';

$subdomain = 'jansens-webwinkels';
$apikey = '1023ihs0edfh';

$apiclient = new Picqer\Api\Client($subdomain, $apikey);

$orders = $apiclient->getOrders();
var_dump($orders);
```

## More examples
Review the examples in the examples/ folder.

## Support
Need support implementing the Picqer API? Feel free to [contact us](https://picqer.com/contact)
