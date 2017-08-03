<?php
require __DIR__ . '/../autoload.php';

use JPush\Client as JPush;

$app_key = getenv('af59e43d6324a2a9d995f72f');
$master_secret = getenv('44c54839bd576b9a2a476275');
$registration_id = getenv('registration_id');

$client = new JPush($app_key, $master_secret);
