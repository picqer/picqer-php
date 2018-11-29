<?php

require('../src/Client.php');

// Start 
$apiClient = new Picqer\Api\Client('--clientsubdomain--', '--apikey--');
$apiClient->enableRetryOnRateLimitHit();
$apiClient->setUseragent('My amazing app (dev@example.org)');

// Retrieve all orders from Picqer account
$orders = $apiClient->getOrders();
var_dump($orders);

// Alternative when there are a lot of orders
foreach ($apiClient->getResultGenerator('order') as $order) {
    var_dump($order);
}
