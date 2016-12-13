<?php
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{

    public function testReturnsCorrectTotal()
    {
        $transactions = \App\TransactionsManager::load('./tests/transactionsTest.json');
        $purchases  = $transactions->filterHistory(\App\Transactions::$PURCHASE_TYPE);
        $topups     = $transactions->filterHistory(\App\Transactions::$TOPUP_TYPE);
        $totalCount = $transactions->getCount();
        $purchasesCount = $purchases->getCount();

        PHPUnit_Framework_Assert::assertEquals(107.7, $transactions->getTotal());
        PHPUnit_Framework_Assert::assertEquals(50.7, $purchases->getTotal());
        PHPUnit_Framework_Assert::assertEquals($totalCount - $purchasesCount, $topups->getCount());
        PHPUnit_Framework_Assert::assertEquals($transactions->getTotal() - $purchases->getTotal(), $topups->getTotal());
    }

    /**
     * When the cashier does a mistake and corrects it by re-scanning the product a second time to deduct the price
     * (quantity of items is negative instead of the price being negative)
     */
    public function testTransactionWithCorrections()
    {
        $transactions = \App\TransactionsManager::load('./tests/transactionsWithCorrectionsTest.json');

        PHPUnit_Framework_Assert::assertEquals(52.32, $transactions->getTotal());
    }

    public function testGetLatestTransactionDate()
    {
        $transactions = \App\TransactionsManager::load('./tests/transactionsTest.json');

        PHPUnit_Framework_Assert::assertEquals(1469799840, $transactions->getLatestEpoch());
    }

}