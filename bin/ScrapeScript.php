<?php

namespace App;

use Dotenv\Dotenv;

require "vendor/autoload.php";

$client       = new \Goutte\Client();
$guzzleClient = new \GuzzleHttp\Client(['curl' => [CURLOPT_TIMEOUT => 60]]);
$client->setClient($guzzleClient);

$dotenv       = new Dotenv(__DIR__ . '/../');
$dotenv->load();
$email    = getenv('EMAIL');
$password = getenv('PASSWORD');

$scraper  = new Scraper($client);

$r = $scraper->getStats($email, $password);

exec("osascript -e 'display notification \"£{$r['current_balance']}\" with title \"Canteen Balance\"'");

echo json_encode($r, JSON_PRETTY_PRINT) . "\n";


