<?php

require('../src/Client.php');

// Start 
$apiClient = new Picqer\Api\Client('--clientsubdomain--', '--apikey--');

// Retrieve all products from Picqer account
$products = $apiClient->getProducts();
var_dump($products);