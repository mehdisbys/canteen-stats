<?php

namespace App;


use Carbon\Carbon;

class StatsFromHistory
{


    public static function getTotalPerMonth(TransactionsManagerInterface $transactionsManager)
    {
        $transactions = $transactionsManager::load();
        $purchases    = $transactions->filterHistory(Transactions::$PURCHASE_TYPE)->getTransactions();

        $totalPermonth = [];

        $carbon = new Carbon();

        foreach ($purchases as $purchase) {
            $date = $carbon->createFromTimestamp($purchase[Transactions::$EPOCH_CELL]);

            if (!isset($totalPermonth[$date->format('F Y')]))
                $totalPermonth[$date->format('F Y')] = 0;

            $totalPermonth[$date->format('F Y')] += $purchase[Transactions::$PRICE_CELL];
        }
        return $totalPermonth;
    }
}