<?php

require('../src/Client.php');

// Start 
$apiclient = new Picqer\Api\Client('--clientsubdomain--', '--apikey--');

// Retrieve all orders from Picqer account
$orders = $apiclient->getOrders();
var_dump($orders);