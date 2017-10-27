<?php

namespace Dashboard\Entities;

class OrderBook {

    const SIDE_BUY = "buy";
    const SIDE_SELL = "sell";

    public $fund_id;
    public $date;

    protected $asks = [];
    protected $bids = [];

    /**
     * OrderBook constructor.
     * @param $id
     */
    public function __construct($orderBookArray)
    {

        $this->setFundId($orderBookArray['fund_id']);
        $this->setDate(new \DateTime($orderBookArray['date']));

        $this->setAsks($orderBookArray['asks']);
        $this->setBids($orderBookArray['bids']);
    }

    /**
     * @return array
     */
    public function getAsks()
    {
        return $this->asks;
    }

    /**
     * @param array $asks
     */
    public function setAsks($asks)
    {
        $this->asks = $asks;
    }

    public function getTopAsk($minimumAmount=0)
    {
        foreach ($this->asks as $ask) {
            if ($ask['amount'] >= $minimumAmount) {
                return $ask['price'];
            }
        }

        return false;
    }

    /**
     * Alias
     * @return mixed
     */
    public function getTopSell($minimumAmount=0)
    {
        return $this->getTopAsk($minimumAmount);
    }

    public function getTopBid($minimumAmount=0)
    {
        foreach ($this->bids as $bid) {
            if ($bid['amount'] >= $minimumAmount) {
                return $bid['price'];
            }
        }

        return false;
    }

    /**
     * Alias
     * @return mixed
     */
    public function getTopBuy($minimumAmount=0)
    {
        return $this->getTopBid($minimumAmount);
    }

    /**
     * @return array
     */
    public function getBids()
    {
        return $this->bids;
    }

    /**
     * @param array $bids
     */
    public function setBids($bids)
    {
        $this->bids = $bids;
    }



    /**
     * @return mixed
     */
    public function getFundId()
    {
        return $this->fund_id;
    }

    /**
     * @param mixed $fund_id
     */
    public function setFundId($fund_id)
    {
        $this->fund_id = $fund_id;
    }


    /**
     * @return mixed
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }
}