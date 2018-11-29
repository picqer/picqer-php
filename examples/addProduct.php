<?php

require('../src/Client.php');

// Start 
$apiClient = new Picqer\Api\Client('--clientsubdomain--', '--apikey--');
$apiClient->enableRetryOnRateLimitHit();
$apiClient->setUseragent('My amazing app (dev@example.org)');

// Retrieve VAT groups
$vatGroups = $apiClient->getVatgroups();

// Add a new product to Picqer account
$product = [
    'productcode' => 'DKS-092383',
    'productcode_supplier' => 'DKS-092383',
    'name' => 'Apple iPod Shuffle Purple',
    'price' => 59.95,
    'fixedstockprice' => 49.95,
    'weight' => 500,
    'barcode' => '9983762736271',
    'idvatgroup' => $vatGroups['data'][0]['idvatgroup'] // First VAT group in Picqer
];

$result = $apiClient->addProduct($product);
var_dump($result);
