<?php

require('../src/Client.php');

// Start 
$apiclient = new Picqer\Api\Client('--clientsubdomain--', '--apikey--');

// Retrieve the previously inserted product
$product = $apiclient->getProductsByProductcode('DKS-092383');

// Compose the put data
// Note: if one would like to change a product field
//      This can be done with the following syntax:
//      $data = array('productfields' => array(array('idproductfields' => 1, 'value' => 1), array('idproductfields' => 2, 'value' => 2)))
$data = array(
    'name' => 'Apple iPod Shuffle Green'
);

var_dump($apiclient->updateProduct($product['data']['idproduct'], $data));
