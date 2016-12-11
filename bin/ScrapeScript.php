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

echo $scraper->getStats($email, $password);

