<?php
namespace App;

use Carbon\Carbon;

class TransactionUnit
{


    public $date;
    public $time;
    public $type;
    public $amount;
    public $shop;
    public $balance;
    public $details;
    public $epoch;

    public function __construct(array $detailledTransaction)
    {
        $this->date    = $detailledTransaction['date-cell'];
        $this->time    = $detailledTransaction['time-cell'];
        $this->type    = $detailledTransaction['type-cell'];
        $this->amount  = $detailledTransaction['amount-cell'];
        $this->shop    = $detailledTransaction['shop-cell'];
        $this->epoch   = $detailledTransaction['epoch'];
        $this->balance = $detailledTransaction['balance-cell'];
        $this->details = $detailledTransaction['details-cell'];
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return mixed
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @return mixed
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * @return mixed
     */
    public function getEpoch()
    {
        return $this->epoch;
    }

    public function toArray()
    {
        return [
            'how-much' => $this->getAmount(),
            'where'        => $this->getShop(),
            'when'         => Carbon::createFromTimestamp($this->getEpoch())->toDateTimeString(),
            'details-cell' => $this->getDetails(),
        ];
    }


}