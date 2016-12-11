<?php
namespace App;


interface TransactionInterface
{
    public function getTotal();
    public function getMax();
    public function getMin();
    public function getAvg();
    public function getCount();
    public function getLatestEpoch();

}