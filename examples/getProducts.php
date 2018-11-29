<?php

require('../src/Client.php');

// Start 
$apiClient = new Picqer\Api\Client('--clientsubdomain--', '--apikey--');
$apiClient->enableRetryOnRateLimitHit();
$apiClient->setUseragent('My amazing app (dev@example.org)');

// Retrieve all products from Picqer account
$products = $apiClient->getProducts();
var_dump($products);