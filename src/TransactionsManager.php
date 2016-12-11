<?php
namespace App;

class TransactionsManager implements TransactionsManagerInterface
{
    public static $TRANSACTION_HISTORY = 'transactions.json';


    public static function save(Transactions $transactions)
    {
        $file = fopen(self::$TRANSACTION_HISTORY, "w+");
        fwrite($file, json_encode($transactions->getTransactions(), JSON_PRETTY_PRINT));
        fclose($file);
    }

    public static function load(): Transactions
    {
        if (file_exists(self::$TRANSACTION_HISTORY)) {
            return new Transactions(json_decode(file_get_contents(self::$TRANSACTION_HISTORY), true));
        }
        return new Transactions([]);
    }

}