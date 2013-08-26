<?php

require('../src/Picqer/Api/Client.php');

// Start 
$apiclient = new Picqer\Api\Client('--clientsubdomain--', '--apikey--');

// Retrieve VAT groups
$vatgroups = $apiclient->getVatgroups();

// Add a new product to Picqer account
$product = array(
    'productcode' => 'DKS-092383',
    'productcode_supplier' => 'DKS-092383',
    'name' => 'Apple iPod Shuffle Purple',
    'price' => 59.95,
    'fixedstockprice' => 49.95,
    'weight' => 500,
    'barcode' => '9983762736271',
    'idvatgroup' => $vatgroups['data'][0]['idvatgroup'] // First VAT group in Picqer
);

$result = $apiclient->addProduct($product);
var_dump($result);
