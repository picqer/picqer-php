<?php

require('../src/Client.php');

// Start 
$apiClient = new Picqer\Api\Client('--clientsubdomain--', '--apikey--');

// Retrieve the previously inserted product
$product = $apiClient->getProductsByProductcode('DKS-092383');

// Compose the put data
// Note: if one would like to change a product field
//      This can be done with the following syntax:
//      $data = ['productfields' => [['idproductfield' => 1, 'value' => 1], ['idproductfield' => 2, 'value' => 2]]];
$data = [
    'name' => 'Apple iPod Shuffle Green'
];

var_dump($apiClient->updateProduct($product['data']['idproduct'], $data));
