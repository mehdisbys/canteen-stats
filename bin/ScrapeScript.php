<?php

namespace App;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

require "vendor/autoload.php";

$client       = new \Goutte\Client();
$guzzleClient = new \GuzzleHttp\Client(['curl' => [CURLOPT_TIMEOUT => 60]]);
$client->setClient($guzzleClient);

$dotenv = new Dotenv(__DIR__ . '/../');

try {
    $dotenv->load();
} catch (InvalidPathException $e) {

    echo "Please create a .env file (look at .env.example)";
    exit(1);
}

$email    = getenv('EMAIL');
$password = getenv('PASSWORD');

$stats   = new StatsFromHistory();
$totalPerMonth = $stats::getTotalPerMonth(new TransactionsManager());

$scraper = new Scraper($client);
$r       = $scraper->getStats($email, $password);

exec("osascript -e 'display notification \"£{$r['current_balance']}\" with title \"Canteen Balance\"'");


echo json_encode([$r, $totalPerMonth], JSON_PRETTY_PRINT) . "\n";


