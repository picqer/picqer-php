<?php

require('../src/PicqerWebhook.php');

$webhook = Picqer\Api\PicqerWebhook::retrieve();

echo 'Hook received: ' . $webhook->getName() . ' that was triggered at ' . $webhook->getEventTriggeredAt() . PHP_EOL;
echo var_dump($webhook->getData());
