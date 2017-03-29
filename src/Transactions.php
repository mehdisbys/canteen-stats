<?php

namespace App;


class Transactions implements TransactionInterface
{

    /** @var  array */
    private $transactions;

    public static $TOPUP_TYPE    = 'Topup';
    public static $PURCHASE_TYPE = 'Purchase';
    public static $PRICE_CELL = 'amount-cell';
    public static $TYPE_CELL = 'type-cell';
    public static $DETAILS_CELL = 'details-cell';
    public static $EPOCH_CELL = 'epoch';

    /**
     * Transactions constructor.
     * @param array $transactions
     */
    public function __construct(array $transactions = NULL)
    {
        $this->transactions = $transactions ?? [];
    }

    private function getValues(string $field)
    {
        return array_column($this->transactions, $field);
    }


    public function getTotal() : float
    {
        $prices = $this->getValues(self::$PRICE_CELL);

        $totalCorrection = $this->getCorrections();

        return round(array_sum($prices) - $totalCorrection,2);
    }


    public function getCorrections() : float
    {
        $corrections = $this->getValues(self::$DETAILS_CELL);

        $totalCorrection = 0.0;

        foreach ($corrections as $correction) {
            foreach ($correction as $details) {
                if (isset($details['quantity']) and $details['quantity'] < 0) {
                    $totalCorrection += ($details['price'] * 2 * abs($details['quantity']));
                }
            }
        }
        return $totalCorrection;
    }

    public function getMax(): TransactionUnit
    {
        $transactionUnit = [];
        $max = 0;

        foreach ($this->transactions as $transaction){
            if($max < $transaction[self::$PRICE_CELL])
            {
                $max = $transaction[self::$PRICE_CELL];
                $transactionUnit = $transaction;
            }
        }

        return new TransactionUnit($transactionUnit);
    }

    public function getMin(): float
    {
        return min($this->getValues(self::$PRICE_CELL));
    }

    public function getAvg(): float
    {
    }

    public function getCount() : int
    {
        return count($this->transactions);
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function getLatestEpoch(): int
    {
        return max($this->getValues('epoch'));
    }

    public function getEarliestEpoch(): int
    {
        return min($this->getValues('epoch'));
    }


    public function filterHistory(string $type)
    {
        $subset = array_filter($this->transactions, function ($key) use ($type) {

            return (isset($key[self::$TYPE_CELL]) and $key[self::$TYPE_CELL] === $type);
        });

        return new Transactions($subset);
    }
}