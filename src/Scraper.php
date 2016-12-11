<?php


namespace App;

use Carbon\Carbon;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class Scraper
{
    protected     $client                    = null;
    protected     $crawler                   = null;
    protected     $accountInfo               = [];
    private       $latestTransactionEpoch    = 0;
    private       $cachedTransactionHistory  = [];
    private       $hasCache;
    public static $LOGIN_URL                 = 'https://icashless.systopiacloud.com';
    public static $HISTORY_URL               = 'https://icashless.systopiacloud.com/Account/GetTransactions?';
    public static $DETAILLED_TRANSACTION_URL = 'https://icashless.systopiacloud.com/account/gettransactiondetails/?';


    public function __construct(Client $client)
    {
        $this->client                   = $client;
        $this->cachedTransactionHistory = TransactionsManager::load();
        $this->hasCache                 = $this->cachedTransactionHistory->getCount() > 0;
        if ($this->hasCache) {
            $this->latestTransactionEpoch = $this->cachedTransactionHistory->getLatestEpoch();
        }
    }



    private function login(string $email, string $password)
    {
        try {
            $page = $this->client->request('GET', self::$LOGIN_URL);

            $form = $page->selectButton('LOG IN')->form
            ([
                 'LoginUser.UserName' => htmlspecialchars($email),
                 'LoginUser.Password' => htmlspecialchars($password),
             ]);

            $account           = $this->client->submit($form);
            $this->accountInfo = $this->getAccountInfo($account);
        } catch (\Exception $e) {
            throw new \Exception("There has been an error while logging in");
        }
        return $this->accountInfo;
    }


    private function scrape()
    {
        $i       = 0;
        $history = [];

        while (true) {
            echo ".";

            $params       = ['pageOffset' => $i++];
            $cells        = $this->client->request('GET', self::$HISTORY_URL . http_build_query($params))->filter('table > tr');
            $transactions = $this->getTransactions($cells);

            if (count($transactions['result']) == 0) break;

            $history = array_merge($history, $transactions['result']);

            if ($transactions['status'] === 'done') break;

            sleep(2); // Be nice to server
        }

        $latestTransactions = array_merge($history, $this->cachedTransactionHistory->getTransactions());

        if (count($history)) {
            TransactionsManager::save(new Transactions($latestTransactions));
        }

        return new Transactions($latestTransactions);
    }


    private function getTransactions($cells): array
    {
        $rows = [];

        foreach ($cells as $i => $content) {
            $tds     = [];
            $crawler = new Crawler($content);
            foreach ($crawler->filter('td') as $i => $node) {

                $key = $node->getAttribute('class');

                if ($key === 'details-cell') {
                    try {
                        $transaction = $node->childNodes->item(1); // Button
                        if ($transaction) {
                            $params    = ['transactionId' => $transaction->getAttribute('id')];
                            $details   = $this->client->request('GET', self::$DETAILLED_TRANSACTION_URL . http_build_query($params))->filter('.transdialog')->filter('tr');
                            $tds[$key] = $this->getDetails($details);
                        }
                    } catch (\Exception $e) {
                    }
                    continue;
                }
                $tds[$key] = $this->cleanupEntries($key, trim($node->nodeValue));
            }
            $epoch = $this->getCleanDate($tds);

            if ($this->hasCache and $epoch > 0 and $epoch === $this->latestTransactionEpoch) {
                array_shift($rows);
                return ['status' => 'done', 'result' => $rows];
            }

            $tds['epoch'] = $epoch;
            $rows[]       = $tds;
        }

        // First row is empty
        array_shift($rows);
        return ['status' => 'wip', 'result' => $rows];
    }

    private function getDetails($details)
    {
        $rows     = [];
        $cellType = ['item', 'quantity', 'price', 'total'];

        foreach ($details as $i => $content) {
            $tds     = [];
            $crawler = new Crawler($content);
            foreach ($crawler->filter('td') as $n => $node) {

                $key       = $cellType[$n];
                $tds[$key] = $this->cleanupEntries($key, trim($node->nodeValue));
            }
            $rows[] = $tds;

        }
        // First row is empty
        array_shift($rows);
        // Last row is total - we already have it
        array_pop($rows);
        return $rows;
    }

    private function cleanupEntries($key, $value)
    {
        if (in_array($key, ['amount-cell', 'price', 'total', 'balance-cell']))
            return getPrice($value);

        return $value;
    }

    private function getCleanDate(array $cell)
    {
        if (isset($cell['date-cell']) == false or isset($cell['time-cell']) == false)
            return 0;

        $date = Carbon::createFromFormat('d/m/Y H:i', $cell['date-cell'] . " " . $cell['time-cell']);
        return $date->getTimestamp();
    }


    private function getAccountInfo(Crawler $crawler)
    {
        $accountHolder  = $crawler->filter('.homeInfoContainer')->text();
        $currentBalance = $crawler->filter('#accountinfobalance')->text();

        return ['accountHolder' => $accountHolder, 'balance' => $currentBalance];
    }

    public function getStats(string $email, string $password)
    {
        $this->login($email, $password);

        $transactions   = $this->scrape();
        $purchases      = $transactions->filterHistory(Transactions::$PURCHASE_TYPE);
        $topups         = $transactions->filterHistory(Transactions::$TOPUP_TYPE);
        $totalPurchases = $purchases->getTotal();
        $totalTopups    = $topups->getTotal();

        $r =
            [
                'current_balance'         => round($totalTopups - $totalPurchases, 2),
                'total_paid'              => $totalPurchases,
                'latest_transaction_date' => Carbon::createFromTimestamp($purchases->getLatestEpoch())->toDateTimeString(),
                'first_transaction_date'  => Carbon::createFromTimestamp($purchases->getEarliestEpoch())->toDateTimeString(),
                'total_topped-up'         => $totalTopups,
                'number_of_purchases'     => $purchases->getCount(),
                'average_per_transaction' => round($totalPurchases / $purchases->getCount(), 2),
                'highest_paid'            => $purchases->getMax()->toArray(),
                'lowest_paid'             => $purchases->getMin(),
            ];

        return json_encode($r, JSON_PRETTY_PRINT) . "\n";
    }

}