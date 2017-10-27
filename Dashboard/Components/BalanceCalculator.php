<?php
namespace Dashboard\Components;

use Dashboard\Entities\Trade;

class BalanceCalculator {
    protected $trades = [];
    protected $balances = [];
    protected $finalBalance = 0;

    protected $currentPrice = 0;


    public function addTrade(Trade $t) {
        $this->trades[$t->getId()] = $t;
    }

    public function calculateInstantProfit(Trade $t, $currentPrice, $fee=0.002)
    {
        $buyingFee = $t->getAmount() * $t->getPrice() * $fee ;
        $sellingFee = $t->getAmount() * $currentPrice * $fee ;

        $buyingPrice = $t->getAmount() * $t->getPrice();
        $sellingPrice = $t->getAmount() * $currentPrice;

        $profit =  $sellingPrice - $buyingPrice - $buyingFee - $sellingFee;

        return $profit;
    }

    protected function calculateBalance() {

        /* @var $trade Trade */
        foreach ($this->trades as $id => $trade) {
            if($trade->getSide() === Trade::SIDE_BUY) {
                $tradeDifference = $trade->getAmount() * $this->currentPrice - $trade->getAmount() * $trade->getPrice();
                $this->balances[$trade->getId()] = $tradeDifference;
                $this->finalBalance += $tradeDifference;
            }
        }
    }

    public function setCurrentPrice($price)
    {
        $this->currentPrice = $price;
        $this->calculateBalance();
    }

    /**
     * @return int
     */
    public function getCurrentPrice()
    {
        return $this->currentPrice;
    }

    /**
     * @return int
     */
    public function getFinalBalance()
    {
        return $this->finalBalance;
    }

    /**
     * @return array
     */
    public function getBalances()
    {
        return $this->balances;
    }



}