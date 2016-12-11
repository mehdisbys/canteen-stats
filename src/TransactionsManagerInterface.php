<?php

namespace App;


interface TransactionsManagerInterface
{

    public static function save(Transactions $transactions);
    public static function load(): Transactions;
}