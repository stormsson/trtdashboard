<?php

namespace Dashboard\Entities;

class Trade {

    const SIDE_BUY = "buy";
    const SIDE_SELL = "sell";
    const SIDE_ALL = false;

    public $id;
    public $fund_id;
    public $orderId;
    public $side;
    public $amount;
    public $price;
    public $date;

    /**
     * Trade constructor.
     * @param $id
     */
    public function __construct($tradeArray)
    {

        $this->setId($tradeArray['id']);
        $this->setFundId($tradeArray['fund_id']);

        $this->setAmount($tradeArray['amount']);
        $this->setPrice($tradeArray['price']);
        $this->setSide($tradeArray['side']);
        $this->setOrderId($tradeArray['order_id']);

        $this->setDate(new \DateTime($tradeArray['date']));
    }

    public function calculateFee($feeMultiplier=0.002) // 0.2%
    {
        return $this->getAmount() * $this->getPrice() * $feeMultiplier;
    }

    public function calculateSellingFee($sellingPrice, $feeMultiplier=0.002)
    {
        return $this->getAmount() * $sellingPrice * $feeMultiplier;
    }


    public function calculateSellingValue($sellingPrice, $feeMultiplier=0)
    {
        $value = $this->getAmount() * $sellingPrice;
        if($feeMultiplier) {
            $value = $value -  $this->calculateSellingFee($sellingPrice, $feeMultiplier);
        }

        return $value;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param mixed $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return mixed
     */
    public function getSide()
    {
        return $this->side;
    }

    /**
     * @param mixed $side
     */
    public function setSide($side)
    {
        $this->side = $side;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
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