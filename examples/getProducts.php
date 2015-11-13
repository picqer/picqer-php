<?php

require('../src/Client.php');

// Start 
$apiclient = new Picqer\Api\Client('--clientsubdomain--', '--apikey--');

// Retrieve all products from Picqer account
$products = $apiclient->getProducts();
var_dump($products);